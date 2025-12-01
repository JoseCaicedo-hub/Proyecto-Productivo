<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SolicitudRecibida;
use App\Notifications\NuevaSolicitudNotification;
use Illuminate\Support\Facades\Notification;

class SolicitudController extends Controller
{
    // Guardar solicitud enviada desde el formulario público
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'telefono' => 'nullable|string|max:50',
            'titulo' => 'nullable|string|max:255',
            'idea' => 'required|string',
            'detalle' => 'nullable|string',
        ]);

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
