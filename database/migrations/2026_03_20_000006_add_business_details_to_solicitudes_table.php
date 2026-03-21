<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->string('nombre_emprendimiento')->nullable()->after('detalle');
            $table->string('tipo_negocio', 100)->nullable()->after('nombre_emprendimiento');
            $table->string('categoria_negocio', 100)->nullable()->after('tipo_negocio');
            $table->text('productos_servicios')->nullable()->after('categoria_negocio');
            $table->string('publico_objetivo', 255)->nullable()->after('productos_servicios');
            $table->text('diferenciador')->nullable()->after('publico_objetivo');
            $table->string('pais', 100)->nullable()->after('diferenciador');
            $table->string('ciudad', 100)->nullable()->after('pais');
            $table->string('direccion')->nullable()->after('ciudad');
            $table->string('redes_sociales_web')->nullable()->after('direccion');
            $table->string('empresa_registrada_legalmente', 2)->nullable()->after('redes_sociales_web');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn([
                'nombre_emprendimiento',
                'tipo_negocio',
                'categoria_negocio',
                'productos_servicios',
                'publico_objetivo',
                'diferenciador',
                'pais',
                'ciudad',
                'direccion',
                'redes_sociales_web',
                'empresa_registrada_legalmente',
            ]);
        });
    }
};
