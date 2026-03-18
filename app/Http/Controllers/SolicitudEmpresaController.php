<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\SolicitudEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudEmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->user()->can('empresa-solicitud-list')) {
            abort(403, 'No autorizado.');
        }

        $solicitudes = SolicitudEmpresa::with('user')
            ->where('estado', 'pendiente')
            ->latest()
            ->paginate(15);

        return view('empresa.solicitudes', compact('solicitudes'));
    }

    public function historial()
    {
        if (!auth()->user()->can('empresa-solicitud-history')) {
            abort(403, 'No autorizado.');
        }

        $solicitudes = SolicitudEmpresa::with(['user', 'admin'])
            ->whereIn('estado', ['aprobada', 'rechazada'])
            ->latest('revisado_en')
            ->paginate(20);

        return view('empresa.solicitudes_historial', compact('solicitudes'));
    }

    public function aprobar($id)
    {
        if (!auth()->user()->can('empresa-solicitud-aprobar')) {
            abort(403, 'No autorizado.');
        }

        $solicitud = SolicitudEmpresa::where('estado', 'pendiente')->findOrFail($id);

        DB::transaction(function () use ($solicitud) {
            Empresa::create([
                'user_id' => $solicitud->user_id,
                'nombre' => $solicitud->nombre,
                'logo' => $solicitud->logo,
                'descripcion' => $solicitud->descripcion,
                'contacto' => $solicitud->contacto,
                'estado' => 'aprobada',
            ]);

            $solicitud->estado = 'aprobada';
            $solicitud->admin_id = auth()->id();
            $solicitud->revisado_en = now();
            $solicitud->save();
        });

        return redirect()->route('admin.empresas.solicitudes.index')->with('mensaje', 'Solicitud aprobada correctamente.');
    }

    public function rechazar(Request $request, $id)
    {
        if (!auth()->user()->can('empresa-solicitud-rechazar')) {
            abort(403, 'No autorizado.');
        }

        $data = $request->validate([
            'motivo_rechazo' => ['nullable', 'string', 'max:2000'],
        ]);

        $solicitud = SolicitudEmpresa::where('estado', 'pendiente')->findOrFail($id);
        $solicitud->estado = 'rechazada';
        $solicitud->admin_id = auth()->id();
        $solicitud->motivo_rechazo = $data['motivo_rechazo'] ?? null;
        $solicitud->revisado_en = now();
        $solicitud->save();

        return redirect()->route('admin.empresas.solicitudes.index')->with('mensaje', 'Solicitud rechazada.');
    }

    public function descargarDocumento($id)
    {
        if (!auth()->user()->can('empresa-solicitud-list') && !auth()->user()->can('empresa-solicitud-history')) {
            abort(403, 'No autorizado.');
        }

        $solicitud = SolicitudEmpresa::findOrFail($id);

        if (empty($solicitud->documento_pdf)) {
            return redirect()->back()->with('mensaje', 'La solicitud no tiene documento PDF adjunto.');
        }

        $filePath = public_path($solicitud->documento_pdf);
        if (!file_exists($filePath)) {
            return redirect()->back()->with('mensaje', 'No se encontró el archivo PDF en el servidor.');
        }

        return response()->download($filePath, 'solicitud-empresa-' . $solicitud->id . '.pdf');
    }
}
