<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cambia la precisión de la columna precio para aceptar valores mayores
        DB::statement("ALTER TABLE `pedido_detalles` MODIFY `precio` DECIMAL(12,2) NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Vuelve a la definición anterior (si existe)
        DB::statement("ALTER TABLE `pedido_detalles` MODIFY `precio` DECIMAL(6,2) NOT NULL DEFAULT 0");
    }
};
