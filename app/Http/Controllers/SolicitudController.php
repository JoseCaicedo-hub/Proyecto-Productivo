<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SolicitudRecibida;
use App\Notifications\NuevaSolicitudNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SolicitudController extends Controller
{
    // Guardar solicitud enviada desde el formulario público
    public function store(Request $request)
    {
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            return redirect()->back()->with('error', 'Tu solicitud ya fue aprobada y ya cuentas con rol de vendedor.');
        }

        $paisesPermitidos = ['Colombia','Argentina','Brasil','Chile','Ecuador','Perú','Venezuela','México','Costa Rica','Panamá','Uruguay','Paraguay','Bolivia','Guatemala','Honduras'];

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'nombre_emprendimiento' => 'required|string|max:191',
            'tipo_negocio' => 'nullable|string|max:100',
            'categoria_negocio' => 'nullable|string|max:100',
            'productos_servicios' => 'required|string',
            'publico_objetivo' => 'nullable|string|max:255',
            'diferenciador' => 'nullable|string',
            'pais' => ['required', 'string', Rule::in($paisesPermitidos)],
            'departamento' => ['required', 'string', 'max:120', 'regex:/^[\pL\s\-\'.]+$/u'],
            'ciudad' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-\'.]+$/u'],
            'direccion' => 'nullable|string|max:255',
            'telefono' => ['required', 'regex:/^[0-9]+$/'],
            'redes_sociales_web' => 'nullable|string|max:255',
            'empresa_registrada_legalmente' => 'nullable|in:si,no',
            'producto_img' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'carta' => 'required|mimes:pdf,doc,docx|max:5120',
        ], [
            'nombre_emprendimiento.required' => 'El nombre del emprendimiento es obligatorio.',
            'productos_servicios.required' => 'La descripción de productos o servicios es obligatoria.',
            'pais.required' => 'El país es obligatorio.',
            'pais.in' => 'El país seleccionado no es válido.',
            'departamento.required' => 'El departamento/estado es obligatorio.',
            'departamento.regex' => 'El departamento/estado solo puede contener letras y espacios.',
            'ciudad.required' => 'La ciudad es obligatoria.',
            'ciudad.regex' => 'La ciudad solo puede contener letras y espacios.',
            'telefono.required' => 'El teléfono del emprendimiento es obligatorio.',
            'telefono.regex' => 'El teléfono solo debe contener números.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $pais = mb_strtolower(trim((string) $request->input('pais', '')));
            $telefono = preg_replace('/\D+/', '', (string) $request->input('telefono', ''));
            $len = strlen($telefono);

            $reglasTelefonoPorPais = [
                'colombia' => ['exact' => 10],
            ];

            $regla = $reglasTelefonoPorPais[$pais] ?? ['min' => 10, 'max' => 15];

            if (isset($regla['exact'])) {
                if ($len !== (int) $regla['exact']) {
                    $validator->errors()->add('telefono', 'El teléfono para ' . ucfirst($pais) . ' debe tener exactamente ' . $regla['exact'] . ' dígitos.');
                }
                return;
            }

            $min = (int) ($regla['min'] ?? 10);
            $max = (int) ($regla['max'] ?? 15);

            if ($len < $min || $len > $max) {
                $validator->errors()->add('telefono', 'El teléfono debe tener entre ' . $min . ' y ' . $max . ' dígitos.');
            }
        });

        $data = $validator->validate();

        $pendingQuery = Solicitud::where('estado', 'pendiente');
        if (auth()->check()) {
            $pendingQuery->where('user_id', auth()->id());
        } else {
            $pendingQuery->where('email', $data['email']);
        }

        if ($pendingQuery->exists()) {
            return redirect()->back()->withInput()->with('error', 'Ya tienes una solicitud pendiente. Debes esperar la respuesta del administrador antes de enviar otra.');
        }

        $data['titulo'] = $data['nombre_emprendimiento'];
        $data['idea'] = $data['productos_servicios'];
        $data['detalle'] = trim(implode("\n", array_filter([
            !empty($data['publico_objetivo']) ? 'Público objetivo: ' . $data['publico_objetivo'] : null,
            !empty($data['diferenciador']) ? 'Diferenciador: ' . $data['diferenciador'] : null,
        ])));

        // Guardar archivos en disco (disk 'public')
        if ($request->hasFile('producto_img')) {
            $data['producto_img'] = $request->file('producto_img')->store('solicitudes/images', 'public');
        }
        if ($request->hasFile('carta')) {
            $data['carta'] = $request->file('carta')->store('solicitudes/cartas', 'public');
        }

        $data['user_id'] = auth()->id();

        $sol = Solicitud::create($data);

        // Enviar correo al solicitante confirmando recepción
        try {
            Mail::to($sol->email)->send(new SolicitudRecibida($sol));
        } catch (\Exception $e) {
            // no bloquear si falla el envío
        }

        // Notificar a todos los administradores (usuarios con role 'admin')
        $admins = User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NuevaSolicitudNotification($sol));
        }

        return redirect()->back()->with('mensaje', 'Solicitud enviada correctamente. Pronto recibirás noticias.');
    }

    // Vista para admins: listar solicitudes
    public function adminIndex()
    {
        // Sólo administradores
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'No autorizado');
        }

        $solicitudes = Solicitud::orderBy('created_at', 'desc')->paginate(20);
        return view('solicitudes.index', compact('solicitudes'));
    }

    // Aceptar solicitud y dar rol de admin al usuario si existe
    public function accept(Request $request, $id)
    {
        // Comprobación de rol admin
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'No autorizado');
        }
        $sol = Solicitud::findOrFail($id);
        $sol->estado = 'aceptada';
        $sol->admin_id = auth()->id();
        $sol->respuesta = $request->input('respuesta');
        $sol->save();

        // Intentar asignar rol "vendedor" al usuario (si existe)
        $user = $sol->user ?: User::where('email', $sol->email)->first();
        if ($user) {
            try {
                $user->assignRole('vendedor');
            } catch (\Exception $e) {
                // Si falla la asignación de rol no bloqueamos el flujo
            }
        }

        // Notificar al solicitante por correo
        try {
            Mail::to($sol->email)->send(new \App\Mail\SolicitudAceptada($sol, $user ?? null));
        } catch (\Exception $e) {}

        return redirect()->back()->with('mensaje', 'Solicitud aceptada.');
    }

    // Rechazar solicitud
    public function reject(Request $request, $id)
    {
        // Comprobación de rol admin
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'No autorizado');
        }
        $sol = Solicitud::findOrFail($id);
        $sol->estado = 'rechazada';
        $sol->admin_id = auth()->id();
        $sol->respuesta = $request->input('respuesta');
        $sol->save();

        // Notificar al solicitante por correo
        try {
            Mail::to($sol->email)->send(new \App\Mail\SolicitudRechazada($sol));
        } catch (\Exception $e) {}

        return redirect()->back()->with('mensaje', 'Solicitud rechazada.');
    }
}
