<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('tip_cents')->nullable()->after('amount_cents');
            $table->string('tip_stripe_payment_intent_id')->nullable()->after('tip_cents');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tip_cents', 'tip_stripe_payment_intent_id']);
        });
    }
};
