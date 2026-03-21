<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'telefono')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE users SET telefono = NULL WHERE telefono IS NOT NULL AND (telefono = '' OR telefono REGEXP '[^0-9]' OR CHAR_LENGTH(telefono) > 19)");
            DB::statement("ALTER TABLE users MODIFY telefono BIGINT UNSIGNED NULL");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("UPDATE users SET telefono = NULL WHERE telefono IS NOT NULL AND (telefono = '' OR telefono ~ '[^0-9]' OR LENGTH(telefono) > 19)");
            DB::statement("ALTER TABLE users ALTER COLUMN telefono TYPE BIGINT USING telefono::BIGINT");
            DB::statement("ALTER TABLE users ALTER COLUMN telefono DROP NOT NULL");
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'telefono')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY telefono VARCHAR(50) NULL");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE users ALTER COLUMN telefono TYPE VARCHAR(50)");
            DB::statement("ALTER TABLE users ALTER COLUMN telefono DROP NOT NULL");
        }
    }
};
