<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\SolicitudEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        if (!$user->can('empresa-list')) {
            abort(403, 'No autorizado.');
        }

        $empresas = Empresa::where('user_id', $user->id)->latest()->get();
        $solicitudes = SolicitudEmpresa::where('user_id', $user->id)->latest()->get();

        return view('empresa.index', compact('empresas', 'solicitudes', 'user'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->can('empresa-create')) {
            abort(403, 'No autorizado.');
        }

        return view('empresa.action');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $esVendedor = $user->hasRole('vendedor') && !$user->hasRole('admin');

        if (!$user->can('empresa-create')) {
            abort(403, 'No autorizado.');
        }

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'documento_pdf' => [$esVendedor ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = strtolower(Str::random(8)) . '-' . $logo->getClientOriginalName();
            $logo->move(public_path('uploads/empresas'), $logoName);
            $logoPath = 'uploads/empresas/' . $logoName;
        }

        $documentoPdfPath = null;
        if ($request->hasFile('documento_pdf')) {
            $pdf = $request->file('documento_pdf');
            $pdfName = strtolower(Str::random(8)) . '-' . $pdf->getClientOriginalName();
            $pdf->move(public_path('uploads/empresas/solicitudes'), $pdfName);
            $documentoPdfPath = 'uploads/empresas/solicitudes/' . $pdfName;
        }

        if ($esVendedor) {
            SolicitudEmpresa::create([
                'user_id' => $user->id,
                'nombre' => $data['nombre'],
                'logo' => $logoPath,
                'descripcion' => $data['descripcion'] ?? null,
                'contacto' => $data['contacto'] ?? null,
                'documento_pdf' => $documentoPdfPath,
                'estado' => 'pendiente',
            ]);

            return redirect()->route('empresas.index')->with('mensaje', 'Solicitud de empresa enviada. Un administrador debe aprobarla.');
        }

        Empresa::create([
            'user_id' => $user->id,
            'nombre' => $data['nombre'],
            'logo' => $logoPath,
            'descripcion' => $data['descripcion'] ?? null,
            'contacto' => $data['contacto'] ?? null,
            'estado' => 'aprobada',
        ]);

        return redirect()->route('empresas.index')->with('mensaje', 'Empresa creada correctamente.');
    }

    public function edit($id)
    {
        $user = auth()->user();
        $empresa = Empresa::findOrFail($id);

        if (!$user->can('empresa-edit')) {
            abort(403, 'No autorizado.');
        }

        if ($empresa->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403, 'No autorizado.');
        }

        return view('empresa.action', compact('empresa'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $empresa = Empresa::findOrFail($id);

        if (!$user->can('empresa-edit')) {
            abort(403, 'No autorizado.');
        }

        if ($empresa->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403, 'No autorizado.');
        }

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'contacto' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = strtolower(Str::random(8)) . '-' . $logo->getClientOriginalName();
            $logo->move(public_path('uploads/empresas'), $logoName);

            if (!empty($empresa->logo)) {
                $oldPath = public_path($empresa->logo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $empresa->logo = 'uploads/empresas/' . $logoName;
        }

        $empresa->nombre = $data['nombre'];
        $empresa->descripcion = $data['descripcion'] ?? null;
        $empresa->contacto = $data['contacto'] ?? null;
        $empresa->save();

        return redirect()->route('empresas.index')->with('mensaje', 'Empresa actualizada correctamente.');
    }
}
