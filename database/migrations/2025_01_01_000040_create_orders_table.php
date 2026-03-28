<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('adhoc'); // adhoc, subscription
            $table->string('status')->default('pending'); // pending, confirmed, picked_up, delivered, cancelled
            $table->string('pickup_link');
            $table->timestamp('pickup_time')->nullable();
            $table->string('delivery_address');
            $table->string('delivery_city')->nullable();
            $table->string('delivery_state', 2)->nullable()->default('AL');
            $table->string('delivery_zip', 10)->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->unsignedInteger('amount_cents')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
