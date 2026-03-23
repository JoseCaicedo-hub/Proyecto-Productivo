<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairLegacySellerCompanies extends Command
{
    protected $signature = 'vendedores:reparar-empresas
                            {--user_id= : Reparar un vendedor específico por ID}
                            {--dry-run : Solo mostrar cambios sin guardar}
                            {--force : Ejecutar sin pedir confirmación}';

    protected $description = 'Repara vendedores antiguos sin empresa vinculada creando/vinculando empresa desde su solicitud aceptada';

    public function handle(): int
    {
        $userId = $this->option('user_id');
        $dryRun = (bool) $this->option('dry-run');

        $query = User::query()->orderBy('id');

        if (!empty($userId)) {
            $query->where('id', (int) $userId);
        } else {
            $acceptedUserIds = Solicitud::query()
                ->where('estado', 'aceptada')
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id');

            $acceptedEmails = Solicitud::query()
                ->where('estado', 'aceptada')
                ->whereNull('user_id')
                ->whereNotNull('email')
                ->distinct()
                ->pluck('email');

            $query->where(function ($innerQuery) use ($acceptedUserIds, $acceptedEmails) {
                $innerQuery->whereIn('id', $acceptedUserIds)
                    ->orWhereIn('email', $acceptedEmails)
                    ->orWhereHas('roles', function ($roleQuery) {
                        $roleQuery->where('name', 'vendedor');
                    });
            });
        }

        $vendedores = $query->get();

        if ($vendedores->isEmpty()) {
            $this->warn('No se encontraron vendedores para reparar.');
            return self::SUCCESS;
        }

        $this->info('Vendedores a evaluar: ' . $vendedores->count());

        if ($dryRun) {
            $this->comment('Modo simulación activo (dry-run). No se aplicarán cambios.');
        } elseif (!$this->option('force')) {
            if (!$this->confirm('¿Deseas ejecutar la reparación de empresas para los vendedores encontrados?')) {
                $this->warn('Operación cancelada.');
                return self::SUCCESS;
            }
        }

        $createdCompanies = 0;
        $linkedUsers = 0;
        $withoutAcceptedSolicitud = 0;
        $withoutData = 0;

        foreach ($vendedores as $user) {
            $acceptedWithoutUserByEmail = Solicitud::query()
                ->where('estado', 'aceptada')
                ->whereNull('user_id')
                ->where('email', $user->email)
                ->count();

            if ($acceptedWithoutUserByEmail > 0) {
                if ($dryRun) {
                    $this->line("[DRY-RUN] Corregiría {$acceptedWithoutUserByEmail} solicitudes aceptadas sin user_id para {$user->email}");
                } else {
                    Solicitud::query()
                        ->where('estado', 'aceptada')
                        ->whereNull('user_id')
                        ->where('email', $user->email)
                        ->update(['user_id' => $user->id]);
                }
            }

            $solicitudAceptada = Solicitud::query()
                ->where('estado', 'aceptada')
                ->where(function ($innerQuery) use ($user) {
                    $innerQuery->where('user_id', $user->id)
                        ->orWhere('email', $user->email);
                })
                ->latest('updated_at')
                ->first();

            if ($solicitudAceptada && (!$solicitudAceptada->user_id || (int) $solicitudAceptada->user_id !== (int) $user->id)) {
                if ($dryRun) {
                    $this->line("[DRY-RUN] Corregiría solicitud {$solicitudAceptada->id} para user_id={$user->id}");
                } else {
                    $solicitudAceptada->user_id = $user->id;
                    $solicitudAceptada->save();
                }
            }

            $empresa = Empresa::where('user_id', $user->id)
                ->whereIn('estado', ['activo', 'aprobada'])
                ->orderByDesc('id')
                ->first();

            if (!$empresa) {
                if (!$solicitudAceptada) {
                    $withoutAcceptedSolicitud++;
                    $this->line("- Usuario {$user->id} ({$user->email}): sin solicitud aceptada, no se crea empresa.");
                    continue;
                }

                $nombreEmpresa = $solicitudAceptada->nombre_emprendimiento ?: ($solicitudAceptada->titulo ?: null);
                $descripcion = $solicitudAceptada->productos_servicios ?: $solicitudAceptada->idea;
                $contacto = $solicitudAceptada->telefono ?: $solicitudAceptada->email;

                if (empty($nombreEmpresa)) {
                    $withoutData++;
                    $this->line("- Usuario {$user->id} ({$user->email}): solicitud aceptada sin nombre de empresa.");
                    continue;
                }

                if ($dryRun) {
                    $this->info("[DRY-RUN] Crearía empresa #{$solicitudAceptada->id} para usuario {$user->id}: {$nombreEmpresa}");
                    $createdCompanies++;
                    $empresa = new Empresa(['id' => 0, 'user_id' => $user->id]);
                } else {
                    $desiredEmpresaId = (int) $solicitudAceptada->id;
                    $empresa = DB::transaction(function () use ($user, $nombreEmpresa, $descripcion, $contacto, $desiredEmpresaId) {
                        $empresaBySolicitudId = Empresa::find($desiredEmpresaId);

                        if ($empresaBySolicitudId && (int) $empresaBySolicitudId->user_id === (int) $user->id) {
                            return $empresaBySolicitudId;
                        }

                        if (!$empresaBySolicitudId) {
                            $empresaNueva = new Empresa([
                                'user_id' => $user->id,
                                'nombre' => $nombreEmpresa,
                                'logo' => null,
                                'descripcion' => $descripcion,
                                'contacto' => $contacto,
                                'estado' => 'activo',
                            ]);
                            $empresaNueva->id = $desiredEmpresaId;
                            $empresaNueva->save();
                            return $empresaNueva;
                        }

                        return Empresa::create([
                            'user_id' => $user->id,
                            'nombre' => $nombreEmpresa,
                            'logo' => null,
                            'descripcion' => $descripcion,
                            'contacto' => $contacto,
                            'estado' => 'activo',
                        ]);
                    });

                    $createdCompanies++;
                    $this->info("Empresa creada para usuario {$user->id}: {$empresa->nombre}");
                }
            }

            $targetEmpresaId = (int) ($empresa->id ?? 0);
            $currentEmpresaId = (int) ($user->empresa_id ?? 0);

            if ($targetEmpresaId > 0 && $currentEmpresaId !== $targetEmpresaId) {
                if ($dryRun) {
                    $this->line("[DRY-RUN] Vincularía usuario {$user->id} -> empresa {$targetEmpresaId}");
                    $linkedUsers++;
                } else {
                    $user->empresa_id = $targetEmpresaId;
                    $user->save();
                    $linkedUsers++;
                    $this->line("Usuario {$user->id} vinculado a empresa {$targetEmpresaId}");
                }
            }
        }

        $this->newLine();
        $this->info('Resumen de reparación:');
        $this->line("- Empresas creadas: {$createdCompanies}");
        $this->line("- Usuarios vinculados: {$linkedUsers}");
        $this->line("- Sin solicitud aceptada: {$withoutAcceptedSolicitud}");
        $this->line("- Solicitud sin datos mínimos: {$withoutData}");

        return self::SUCCESS;
    }
}
