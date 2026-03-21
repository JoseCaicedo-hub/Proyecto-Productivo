<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'payment_provider')) {
                $table->string('payment_provider', 50)->nullable()->after('metodo_pago');
            }
            if (!Schema::hasColumn('pedidos', 'account_number')) {
                $table->string('account_number', 30)->nullable()->after('payment_provider');
            }
            if (!Schema::hasColumn('pedidos', 'card_last4')) {
                $table->string('card_last4', 4)->nullable()->after('account_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'card_last4')) {
                $table->dropColumn('card_last4');
            }
            if (Schema::hasColumn('pedidos', 'account_number')) {
                $table->dropColumn('account_number');
            }
            if (Schema::hasColumn('pedidos', 'payment_provider')) {
                $table->dropColumn('payment_provider');
            }
        });
    }
};
