<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'telefono')) {
                $table->string('telefono', 50)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'ciudad')) {
                $table->string('ciudad', 100)->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('users', 'municipio')) {
                $table->string('municipio', 100)->nullable()->after('ciudad');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'municipio')) {
                $table->dropColumn('municipio');
            }
            if (Schema::hasColumn('users', 'ciudad')) {
                $table->dropColumn('ciudad');
            }
            if (Schema::hasColumn('users', 'telefono')) {
                $table->dropColumn('telefono');
            }
        });
    }
};
