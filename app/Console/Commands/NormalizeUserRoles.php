<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class NormalizeUserRoles extends Command
{
    protected $signature = 'usuarios:normalizar-roles {--dry-run : Solo mostrar cambios} {--force : Ejecutar sin confirmación}';

    protected $description = 'Normaliza usuarios para que tengan un solo rol (prioridad: admin > vendedor > cliente)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $usuarios = User::with('roles')->get();
        $conMultiples = $usuarios->filter(fn (User $user) => $user->roles->count() > 1);

        if ($conMultiples->isEmpty()) {
            $this->info('No hay usuarios con múltiples roles.');
            return self::SUCCESS;
        }

        $this->warn('Usuarios con múltiples roles detectados: ' . $conMultiples->count());

        if ($dryRun) {
            $this->comment('Modo simulación activo. No se aplicarán cambios.');
        } elseif (!$this->option('force') && !$this->confirm('¿Deseas normalizar roles ahora?')) {
            $this->info('Operación cancelada.');
            return self::SUCCESS;
        }

        $updated = 0;

        foreach ($conMultiples as $user) {
            $currentRoles = $user->roles->pluck('name')->values();
            $targetRole = $this->resolveTargetRole($currentRoles->all());

            if (!$targetRole) {
                $this->line("- Usuario {$user->id}: sin rol objetivo claro, se omite.");
                continue;
            }

            if ($dryRun) {
                $this->line("[DRY-RUN] Usuario {$user->id} ({$user->email}) roles: " . $currentRoles->implode(', ') . " => {$targetRole}");
                $updated++;
                continue;
            }

            $user->syncRoles([$targetRole]);
            $updated++;
            $this->line("Usuario {$user->id} normalizado a rol único: {$targetRole}");
        }

        $this->newLine();
        $this->info('Resumen:');
        $this->line('- Usuarios evaluados con múltiples roles: ' . $conMultiples->count());
        $this->line('- Usuarios normalizados: ' . $updated);

        return self::SUCCESS;
    }

    private function resolveTargetRole(array $roles): ?string
    {
        $priority = ['admin', 'vendedor', 'cliente'];

        foreach ($priority as $role) {
            if (in_array($role, $roles, true)) {
                return $role;
            }
        }

        return $roles[0] ?? null;
    }
}
