<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Stripe identifiers
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_charge_id')->nullable()->unique();
            $table->string('stripe_payment_method_id')->nullable();

            // Payment details
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('usd');
            $table->enum('status', [
                'pending',
                'processing',
                'succeeded',
                'failed',
                'refunded',
            ])->default('pending');
            $table->enum('payment_method_type', [
                'stripe',
                'payhere',
                'bank_transfer',
            ])->default('stripe');

            // Metadata
            $table->json('metadata')->nullable();
            $table->string('idempotency_key')->nullable()->unique();

            // Timestamps
            $table->dateTime('failed_at')->nullable();
            $table->dateTime('succeeded_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->text('error_message')->nullable();

            // Indexes for performance
            $table->timestamps();
            $table->index('user_id');
            $table->index('order_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
