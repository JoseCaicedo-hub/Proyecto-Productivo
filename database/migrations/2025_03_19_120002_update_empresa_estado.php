<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cambiar el estado de empresas 'aprobada' a 'activo'
        \Illuminate\Support\Facades\DB::table('empresas')
            ->where('estado', 'aprobada')
            ->update(['estado' => 'activo']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir: cambiar de 'activo' a 'aprobada' (solo aquellas que fueron actualizadas)
        // Nota: Esta reversión es destructiva y no es 100% precisa, úsala solo si necesitas revert
        // \Illuminate\Support\Facades\DB::table('empresas')
        //     ->where('estado', 'activo')
        //     ->update(['estado' => 'aprobada']);
    }
};
