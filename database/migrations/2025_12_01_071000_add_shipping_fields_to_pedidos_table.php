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
        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'direccion')) {
                $table->text('direccion')->nullable()->after('total');
            }
            if (!Schema::hasColumn('pedidos', 'tipo_documento')) {
                $table->string('tipo_documento', 30)->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('pedidos', 'numero_documento')) {
                $table->string('numero_documento', 50)->nullable()->after('tipo_documento');
            }
            if (!Schema::hasColumn('pedidos', 'metodo_pago')) {
                $table->string('metodo_pago', 50)->nullable()->after('numero_documento');
            }
            if (!Schema::hasColumn('pedidos', 'telefono')) {
                $table->string('telefono', 30)->nullable()->after('metodo_pago');
            }
            if (!Schema::hasColumn('pedidos', 'referencia')) {
                $table->string('referencia')->nullable()->after('telefono');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'referencia')) {
                $table->dropColumn('referencia');
            }
            if (Schema::hasColumn('pedidos', 'telefono')) {
                $table->dropColumn('telefono');
            }
            if (Schema::hasColumn('pedidos', 'metodo_pago')) {
                $table->dropColumn('metodo_pago');
            }
            if (Schema::hasColumn('pedidos', 'numero_documento')) {
                $table->dropColumn('numero_documento');
            }
            if (Schema::hasColumn('pedidos', 'tipo_documento')) {
                $table->dropColumn('tipo_documento');
            }
            if (Schema::hasColumn('pedidos', 'direccion')) {
                $table->dropColumn('direccion');
            }
        });
    }
};
