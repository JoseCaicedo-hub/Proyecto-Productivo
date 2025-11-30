<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Intentar detectar y borrar índices únicos existentes que cubran user_id+producto_id
        try {
            $rows = DB::select("SHOW INDEX FROM `carritos` WHERE Non_unique = 0");
            $groups = [];
            foreach ($rows as $r) {
                $groups[$r->Key_name][] = $r->Column_name;
            }
            foreach ($groups as $key => $cols) {
                $cols = array_values($cols);
                sort($cols);
                if ($cols === ['producto_id', 'user_id']) {
                    DB::statement("ALTER TABLE `carritos` DROP INDEX `{$key}`");
                    break;
                }
            }
        } catch (\Exception $e) {
            // ignorar errores de inspección de índices
        }

        Schema::table('carritos', function (Blueprint $table) {
            // Añadir columna talla (nullable)
            if (! Schema::hasColumn('carritos', 'talla')) {
                $table->string('talla')->nullable()->after('cantidad');
            }

            // Crear nuevo índice único que incluya talla
            try {
                $table->unique(['user_id', 'producto_id', 'talla']);
            } catch (\Exception $e) {
                // ignorar errores al crear índice
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carritos', function (Blueprint $table) {
            try {
                $table->dropUnique('carritos_user_id_producto_id_talla_unique');
            } catch (\Exception $e) {
                try {
                    $table->dropUnique(['user_id', 'producto_id', 'talla']);
                } catch (\Exception $e) {
                }
            }
            if (Schema::hasColumn('carritos', 'talla')) {
                $table->dropColumn('talla');
            }
            try {
                $table->unique(['user_id', 'producto_id']);
            } catch (\Exception $e) {
            }
        });
    }
};
