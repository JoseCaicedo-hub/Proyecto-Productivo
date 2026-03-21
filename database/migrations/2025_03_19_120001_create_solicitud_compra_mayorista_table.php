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
        Schema::create('solicitud_compra_mayorista', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('empresa_id');
            $table->string('nombre_cliente');
            $table->string('email_cliente');
            $table->string('telefono_cliente')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('documento')->nullable();
            $table->enum('estado', ['pendiente', 'contactado', 'rechazado', 'completado'])->default('pendiente');
            $table->timestamp('visto_en')->nullable();
            $table->timestamp('respondido_en')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->index('empresa_id');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_compra_mayorista');
    }
};
