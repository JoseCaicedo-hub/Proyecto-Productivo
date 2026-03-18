<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitud_empresas', function (Blueprint $table) {
            if (!Schema::hasColumn('solicitud_empresas', 'documento_pdf')) {
                $table->string('documento_pdf')->nullable()->after('contacto');
            }
        });
    }

    public function down(): void
    {
        Schema::table('solicitud_empresas', function (Blueprint $table) {
            if (Schema::hasColumn('solicitud_empresas', 'documento_pdf')) {
                $table->dropColumn('documento_pdf');
            }
        });
    }
};
