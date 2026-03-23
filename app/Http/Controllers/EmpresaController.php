<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Solicitud;
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

        if ($user->hasRole('admin')) {
            $solicitudesAceptadas = Solicitud::query()
                ->where('estado', 'aceptada')
                ->orderByDesc('updated_at')
                ->get();

            foreach ($solicitudesAceptadas as $solicitudAceptada) {
                $owner = $solicitudAceptada->user;
                if (!$owner && !empty($solicitudAceptada->email)) {
                    $owner = \App\Models\User::where('email', $solicitudAceptada->email)->first();
                }

                if (!$owner) {
                    continue;
                }

                if (!$solicitudAceptada->user_id || (int) $solicitudAceptada->user_id !== (int) $owner->id) {
                    $solicitudAceptada->user_id = $owner->id;
                    $solicitudAceptada->save();
                }

                $ownerId = (int) $owner->id;
                if ($ownerId <= 0) {
                    continue;
                }

                $empresa = Empresa::where('user_id', $ownerId)
                    ->whereIn('estado', ['activo', 'aprobada'])
                    ->orderByDesc('id')
                    ->first();

                if (!$empresa) {
                    $desiredEmpresaId = (int) $solicitudAceptada->id;
                    $empresaBySolicitudId = Empresa::find($desiredEmpresaId);

                    if ($empresaBySolicitudId && (int) $empresaBySolicitudId->user_id === $ownerId) {
                        $empresa = $empresaBySolicitudId;
                    } elseif (!$empresaBySolicitudId) {
                        $empresa = new Empresa([
                            'user_id' => $ownerId,
                            'nombre' => $solicitudAceptada->nombre_emprendimiento ?: ($solicitudAceptada->titulo ?: ('Empresa #' . $ownerId)),
                            'logo' => null,
                            'descripcion' => $solicitudAceptada->productos_servicios ?: $solicitudAceptada->idea,
                            'contacto' => $solicitudAceptada->telefono ?: $solicitudAceptada->email,
                            'estado' => 'activo',
                        ]);
                        $empresa->id = $desiredEmpresaId;
                        $empresa->save();
                    } else {
                        $empresa = Empresa::create([
                            'user_id' => $ownerId,
                            'nombre' => $solicitudAceptada->nombre_emprendimiento ?: ($solicitudAceptada->titulo ?: ('Empresa #' . $ownerId)),
                            'logo' => null,
                            'descripcion' => $solicitudAceptada->productos_servicios ?: $solicitudAceptada->idea,
                            'contacto' => $solicitudAceptada->telefono ?: $solicitudAceptada->email,
                            'estado' => 'activo',
                        ]);
                    }
                }

                if ($owner && (!$owner->empresa_id || (int) $owner->empresa_id !== (int) $empresa->id)) {
                    $owner->empresa_id = $empresa->id;
                    $owner->save();
                }
            }

            $empresas = Empresa::whereIn('estado', ['activo', 'aprobada'])->latest()->get();
            $solicitudes = SolicitudEmpresa::latest()->get();

            return view('empresa.index', compact('empresas', 'solicitudes', 'user'));
        }

        $empresas = Empresa::where('user_id', $user->id)->latest()->get();

        if ($empresas->isEmpty() && $user->hasRole('vendedor')) {
            $solicitudAceptada = Solicitud::query()
                ->where('estado', 'aceptada')
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('email', $user->email);
                })
                ->latest('updated_at')
                ->first();

            if ($solicitudAceptada) {
                $desiredEmpresaId = (int) $solicitudAceptada->id;
                $empresaBySolicitudId = Empresa::find($desiredEmpresaId);

                if ($empresaBySolicitudId && (int) $empresaBySolicitudId->user_id === (int) $user->id) {
                    $empresa = $empresaBySolicitudId;
                } elseif (!$empresaBySolicitudId) {
                    $empresa = new Empresa([
                        'user_id' => $user->id,
                        'nombre' => $solicitudAceptada->nombre_emprendimiento ?: ($solicitudAceptada->titulo ?: ('Empresa de ' . $user->name)),
                        'logo' => null,
                        'descripcion' => $solicitudAceptada->productos_servicios ?: $solicitudAceptada->idea,
                        'contacto' => $solicitudAceptada->telefono ?: $solicitudAceptada->email,
                        'estado' => 'activo',
                    ]);
                    $empresa->id = $desiredEmpresaId;
                    $empresa->save();
                } else {
                    $empresa = Empresa::create([
                        'user_id' => $user->id,
                        'nombre' => $solicitudAceptada->nombre_emprendimiento ?: ($solicitudAceptada->titulo ?: ('Empresa de ' . $user->name)),
                        'logo' => null,
                        'descripcion' => $solicitudAceptada->productos_servicios ?: $solicitudAceptada->idea,
                        'contacto' => $solicitudAceptada->telefono ?: $solicitudAceptada->email,
                        'estado' => 'activo',
                    ]);
                }

                if (!$user->empresa_id || (int) $user->empresa_id !== (int) $empresa->id) {
                    $user->empresa_id = $empresa->id;
                    $user->save();
                }

                $empresas = Empresa::where('user_id', $user->id)->latest()->get();
            }
        }

        $solicitudes = SolicitudEmpresa::where('user_id', $user->id)->latest()->get();

        return view('empresa.index', compact('empresas', 'solicitudes', 'user'));
    }

    public function create()
    {
        $user = auth()->user();
        $esVendedor = $user->hasRole('vendedor') && !$user->hasRole('admin');

        if (!$user->can('empresa-create')) {
            abort(403, 'No autorizado.');
        }

        if ($esVendedor) {
            $yaTieneEmpresa = Empresa::where('user_id', $user->id)
                ->whereIn('estado', ['activo', 'aprobada'])
                ->exists();

            if ($yaTieneEmpresa || $user->empresa_id) {
                return redirect()->route('dashboard')->with('error', 'Ya tienes una empresa aprobada. No puedes crear otra.');
            }
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

        if ($esVendedor) {
            $yaTieneEmpresa = Empresa::where('user_id', $user->id)
                ->whereIn('estado', ['activo', 'aprobada'])
                ->exists();

            if ($yaTieneEmpresa || $user->empresa_id) {
                return redirect()->route('dashboard')->with('error', 'Ya tienes una empresa aprobada. No puedes crear otra.');
            }
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
            'estado' => 'activo',
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
