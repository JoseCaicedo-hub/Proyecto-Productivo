<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido_detalles', function (Blueprint $table) {
            if (!Schema::hasColumn('pedido_detalles', 'envio_estado')) {
                $table->string('envio_estado', 30)->default('pendiente')->after('talla');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedido_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('pedido_detalles', 'envio_estado')) {
                $table->dropColumn('envio_estado');
            }
        });
    }
};
