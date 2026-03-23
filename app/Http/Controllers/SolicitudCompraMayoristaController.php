<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudCompraMayorista;
use App\Models\Empresa;

class SolicitudCompraMayoristaController extends Controller
{
    /**
     * Guardar una nueva solicitud de compra mayorista
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para enviar una solicitud mayorista.');
        }

        $request->merge([
            'nombre_cliente' => auth()->user()->name,
            'email_cliente' => auth()->user()->email,
            'telefono_cliente' => (string) (auth()->user()->telefono ?? ''),
        ]);

        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre_cliente' => 'required|string|max:255',
            'email_cliente' => 'required|email',
            'telefono_cliente' => ['required', 'regex:/^[0-9]+$/'],
            'descripcion' => 'required|string',
            'documento' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ], [
            'telefono_cliente.required' => 'Debes tener un teléfono registrado en tu cuenta para solicitar compra mayorista.',
            'telefono_cliente.regex' => 'El teléfono de tu cuenta solo puede contener números.',
        ]);

        $documentoPath = null;
        if ($request->hasFile('documento')) {
            $documentoPath = $request->file('documento')->store('solicitudes-mayorista', 'public');
        }

        SolicitudCompraMayorista::create([
            'user_id' => auth()->id(),
            'empresa_id' => $request->input('empresa_id'),
            'nombre_cliente' => $validated['nombre_cliente'],
            'email_cliente' => $validated['email_cliente'],
            'telefono_cliente' => $validated['telefono_cliente'],
            'descripcion' => $request->input('descripcion'),
            'documento' => $documentoPath,
            'estado' => 'pendiente',
        ]);

        return redirect()->back()->with('mensaje', 'Solicitud de compra mayorista enviada correctamente. Nos pondremos en contacto con ustedes.');
    }

    /**
     * Mostrar solicitudes recibidas por una empresa
     */
    public function indexEmpresa()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $solicitudes = SolicitudCompraMayorista::with(['empresa', 'usuario'])
                ->orderBy('created_at', 'desc')
                ->paginate(8);

            return view('mayorista.solicitudes', compact('solicitudes'));
        }

        $empresaId = $this->resolveEmpresaIdForUser($user);
        if (!$empresaId) {
            return redirect()->route('dashboard')->with('error', 'No tienes una empresa asignada.');
        }

        $solicitudes = SolicitudCompraMayorista::with(['empresa', 'usuario'])
            ->where('empresa_id', $empresaId)
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('mayorista.solicitudes', compact('solicitudes'));
    }

    /**
     * Mostrar detalle de una solicitud
     */
    public function show($id)
    {
        $user = auth()->user();
        $solicitud = SolicitudCompraMayorista::findOrFail($id);
        
        if (!$user->hasRole('admin')) {
            $empresaId = $this->resolveEmpresaIdForUser($user);
            if (!$empresaId || (int) $solicitud->empresa_id !== (int) $empresaId) {
                abort(403, 'No tienes permiso para ver esta solicitud.');
            }
        }

        // Marcar como visto
        if (!$solicitud->visto_en) {
            $solicitud->update(['visto_en' => now()]);
        }

        return view('mayorista.solicitud-detalle', compact('solicitud'));
    }

    /**
     * Actualizar estado de solicitud
     */
    public function updateEstado(Request $request, $id)
    {
        $user = auth()->user();
        $solicitud = SolicitudCompraMayorista::findOrFail($id);
        
        if (!$user->hasRole('admin')) {
            $empresaId = $this->resolveEmpresaIdForUser($user);
            if (!$empresaId || (int) $solicitud->empresa_id !== (int) $empresaId) {
                abort(403, 'No tienes permiso para modificar esta solicitud.');
            }
        }

        $validated = $request->validate([
            'estado' => 'required|in:contactado,rechazado,completado',
        ]);

        $solicitud->update([
            'estado' => $request->input('estado'),
            'respondido_en' => now(),
        ]);

        return redirect()->back()->with('mensaje', 'Estado actualizado correctamente.');
    }

    /**
     * Marcar como visto
     */
    public function marcarVisto($id)
    {
        $user = auth()->user();
        $solicitud = SolicitudCompraMayorista::findOrFail($id);
        
        if (!$user->hasRole('admin')) {
            $empresaId = $this->resolveEmpresaIdForUser($user);
            if (!$empresaId || (int) $solicitud->empresa_id !== (int) $empresaId) {
                abort(403, 'No tienes permiso para acceder a esta solicitud.');
            }
        }

        if (!$solicitud->visto_en) {
            $solicitud->update(['visto_en' => now()]);
        }

        return redirect()->back();
    }

    /**
     * Eliminar solicitud mayorista
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $solicitud = SolicitudCompraMayorista::findOrFail($id);

        if (!$user->hasRole('admin')) {
            $empresaId = $this->resolveEmpresaIdForUser($user);
            if (!$empresaId || (int) $solicitud->empresa_id !== (int) $empresaId) {
                abort(403, 'No tienes permiso para eliminar esta solicitud.');
            }
        }

        if (!empty($solicitud->documento)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($solicitud->documento);
        }

        $solicitud->delete();

        return redirect()->route('mayorista.solicitudes.index')->with('mensaje', 'Solicitud eliminada correctamente.');
    }

    private function resolveEmpresaIdForUser($user): ?int
    {
        if (!$user) {
            return null;
        }

        if ($user->empresa_id) {
            return (int) $user->empresa_id;
        }

        $empresa = Empresa::where('user_id', $user->id)
            ->whereIn('estado', ['activo', 'aprobada'])
            ->orderByDesc('id')
            ->first();

        if (!$empresa) {
            return null;
        }

        $user->empresa_id = $empresa->id;
        $user->save();

        return (int) $empresa->id;
    }
}
