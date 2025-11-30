<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido_detalles', function (Blueprint $table) {
            if (!Schema::hasColumn('pedido_detalles', 'talla')) {
                $table->string('talla', 20)->nullable()->after('precio');
            }
            if (!Schema::hasColumn('pedido_detalles', 'entregado')) {
                $table->boolean('entregado')->default(false)->after('talla');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedido_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('pedido_detalles', 'entregado')) {
                $table->dropColumn('entregado');
            }
            if (Schema::hasColumn('pedido_detalles', 'talla')) {
                $table->dropColumn('talla');
            }
        });
    }
};
