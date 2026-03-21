<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migración para estandarizar precios a INTEGER (COP sin decimales)
     * 
     * Todos los precios se guardarán como enteros:
     * - 25000 representa $25.000 COP
     * - Sin decimales
     * - Conversión: DECIMAL(10,2) → INTEGER
     */
    public function up(): void
    {
        // Convertir tabla productos
        if (Schema::hasTable('productos')) {
            Schema::table('productos', function (Blueprint $table) {
                // Cambiar precio de DECIMAL(10,2) a INTEGER
                $table->integer('precio')->change();
            });
        }

        // Convertir tabla pedidos
        if (Schema::hasTable('pedidos')) {
            Schema::table('pedidos', function (Blueprint $table) {
                // Cambiar total de DECIMAL(10,2) a INTEGER
                $table->integer('total')->change();
            });
        }

        // Convertir tabla pedido_detalles
        if (Schema::hasTable('pedido_detalles')) {
            Schema::table('pedido_detalles', function (Blueprint $table) {
                // Cambiar precio de DECIMAL(12,2) a INTEGER
                $table->integer('precio')->change();
            });
        }
    }

    public function down(): void
    {
        // Revertir tabla productos
        if (Schema::hasTable('productos')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->decimal('precio', 10, 2)->change();
            });
        }

        // Revertir tabla pedidos
        if (Schema::hasTable('pedidos')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->decimal('total', 10, 2)->change();
            });
        }

        // Revertir tabla pedido_detalles
        if (Schema::hasTable('pedido_detalles')) {
            Schema::table('pedido_detalles', function (Blueprint $table) {
                $table->decimal('precio', 12, 2)->change();
            });
        }
    }
};
