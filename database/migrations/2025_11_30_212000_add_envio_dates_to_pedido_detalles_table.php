<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido_detalles', function (Blueprint $table) {
            if (!Schema::hasColumn('pedido_detalles', 'fecha_envio')) {
                $table->timestamp('fecha_envio')->nullable()->after('envio_estado');
            }
            if (!Schema::hasColumn('pedido_detalles', 'fecha_recibido')) {
                $table->timestamp('fecha_recibido')->nullable()->after('fecha_envio');
            }
            if (!Schema::hasColumn('pedido_detalles', 'entregado_por')) {
                $table->unsignedBigInteger('entregado_por')->nullable()->after('fecha_recibido');
                $table->foreign('entregado_por')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedido_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('pedido_detalles', 'entregado_por')) {
                $table->dropForeign(['entregado_por']);
                $table->dropColumn('entregado_por');
            }
            if (Schema::hasColumn('pedido_detalles', 'fecha_recibido')) {
                $table->dropColumn('fecha_recibido');
            }
            if (Schema::hasColumn('pedido_detalles', 'fecha_envio')) {
                $table->dropColumn('fecha_envio');
            }
        });
    }
};
