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
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre_cliente' => 'required|string|max:255',
            'email_cliente' => 'required|email',
            'telefono_cliente' => 'required|string',
            'descripcion' => 'required|string',
            'documento' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $documentoPath = null;
        if ($request->hasFile('documento')) {
            $documentoPath = $request->file('documento')->store('solicitudes-mayorista', 'public');
        }

        SolicitudCompraMayorista::create([
            'user_id' => auth()->id(),
            'empresa_id' => $request->input('empresa_id'),
            'nombre_cliente' => $request->input('nombre_cliente'),
            'email_cliente' => $request->input('email_cliente'),
            'telefono_cliente' => $request->input('telefono_cliente'),
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
        // Verificar que el usuario es vendedor y que tiene empresa
        if (!auth()->user()->empresa_id) {
            return redirect()->route('dashboard')->with('error', 'No tienes una empresa asignada.');
        }

        $solicitudes = SolicitudCompraMayorista::where('empresa_id', auth()->user()->empresa_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('mayorista.solicitudes', compact('solicitudes'));
    }

    /**
     * Mostrar detalle de una solicitud
     */
    public function show($id)
    {
        $solicitud = SolicitudCompraMayorista::findOrFail($id);
        
        // Verificar que la solicitud pertenece a la empresa del usuario
        if ($solicitud->empresa_id != auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para ver esta solicitud.');
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
        $solicitud = SolicitudCompraMayorista::findOrFail($id);
        
        // Verificar que la solicitud pertenece a la empresa del usuario
        if ($solicitud->empresa_id != auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para modificar esta solicitud.');
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
        $solicitud = SolicitudCompraMayorista::findOrFail($id);
        
        // Verificar que la solicitud pertenece a la empresa del usuario
        if ($solicitud->empresa_id != auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para acceder a esta solicitud.');
        }

        if (!$solicitud->visto_en) {
            $solicitud->update(['visto_en' => now()]);
        }

        return redirect()->back();
    }
}
