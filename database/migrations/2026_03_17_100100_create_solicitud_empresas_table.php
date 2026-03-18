<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->string('logo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('contacto', 255)->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo_rechazo')->nullable();
            $table->timestamp('revisado_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_empresas');
    }
};
