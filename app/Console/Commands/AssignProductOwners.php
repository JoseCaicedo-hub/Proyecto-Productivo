<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Models\User;

class AssignProductOwners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'producto:assign-owners {user_id : ID del usuario que recibirá los productos} {--preview : Mostrar solo resumen sin ejecutar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignar todos los productos sin owner (user_id NULL) a un usuario específico';

    public function handle()
    {
        $userId = (int) $this->argument('user_id');

        $user = User::find($userId);
        if (! $user) {
            $this->error("Usuario con id {$userId} no encontrado.");
            return 1;
        }

        $count = Producto::whereNull('user_id')->count();
        $this->info("Se encontraron {$count} productos sin owner.");

        if ($count === 0) {
            $this->info('Nada que asignar.');
            return 0;
        }

        $sample = Producto::whereNull('user_id')->take(10)->get(['id','nombre']);
        $this->info('Ejemplo de productos que serán asignados (hasta 10):');
        foreach ($sample as $p) {
            $this->line(" - [{$p->id}] {$p->nombre}");
        }

        if ($this->option('preview')) {
            $this->info('Modo preview activo. Ningún cambio será aplicado.');
            return 0;
        }

        if (! $this->confirm("¿Deseas asignar estos {$count} productos al usuario {$user->name} (id={$userId})?")) {
            $this->info('Operación cancelada.');
            return 0;
        }

        $updated = Producto::whereNull('user_id')->update(['user_id' => $userId]);

        $this->info("Asignados {$updated} productos al usuario id={$userId} ({$user->name}).");
        return 0;
    }
}
