<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('producto_imagenes', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('producto_id')->index();
			$table->string('ruta');
			$table->string('alt')->nullable();
			$table->integer('orden')->default(0);
			$table->timestamps();

			$table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('producto_imagenes');
	}
};

