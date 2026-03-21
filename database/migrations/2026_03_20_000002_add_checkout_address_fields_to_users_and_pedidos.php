<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pais')) {
                $table->string('pais', 100)->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('users', 'departamento')) {
                $table->string('departamento', 120)->nullable()->after('pais');
            }
            if (!Schema::hasColumn('users', 'direccion')) {
                $table->text('direccion')->nullable()->after('departamento');
            }
        });

        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'pais')) {
                $table->string('pais', 100)->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('pedidos', 'departamento')) {
                $table->string('departamento', 120)->nullable()->after('pais');
            }
            if (!Schema::hasColumn('pedidos', 'ciudad')) {
                $table->string('ciudad', 120)->nullable()->after('departamento');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'ciudad')) {
                $table->dropColumn('ciudad');
            }
            if (Schema::hasColumn('pedidos', 'departamento')) {
                $table->dropColumn('departamento');
            }
            if (Schema::hasColumn('pedidos', 'pais')) {
                $table->dropColumn('pais');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'direccion')) {
                $table->dropColumn('direccion');
            }
            if (Schema::hasColumn('users', 'departamento')) {
                $table->dropColumn('departamento');
            }
            if (Schema::hasColumn('users', 'pais')) {
                $table->dropColumn('pais');
            }
        });
    }
};
