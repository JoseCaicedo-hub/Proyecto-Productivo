<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'empresa_id')) {
                $table->foreignId('empresa_id')->nullable()->after('user_id')->constrained('empresas')->nullOnDelete();
            }
        });

        $ownerIds = DB::table('productos')
            ->whereNull('empresa_id')
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        foreach ($ownerIds as $ownerId) {
            $empresaId = DB::table('empresas')
                ->where('user_id', $ownerId)
                ->where('estado', 'aprobada')
                ->value('id');

            if (!$empresaId) {
                $empresaId = DB::table('empresas')->insertGetId([
                    'user_id' => $ownerId,
                    'nombre' => 'Empresa principal #' . $ownerId,
                    'estado' => 'aprobada',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('productos')
                ->where('user_id', $ownerId)
                ->whereNull('empresa_id')
                ->update(['empresa_id' => $empresaId]);
        }
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'empresa_id')) {
                $table->dropForeign(['empresa_id']);
                $table->dropColumn('empresa_id');
            }
        });
    }
};
