<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade');
            
            // Shipment details
            $table->enum('carrier', ['standard', 'express', 'overnight', 'pickup'])
                  ->default('standard');
            $table->string('tracking_number')->unique();
            $table->enum('status', [
                'pending',
                'picked_up',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'failed',
                'returned',
            ])->default('pending');
            
            // Dates
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('estimated_delivery_at')->nullable();
            
            // Location tracking
            $table->string('last_location')->nullable();
            $table->timestamp('last_update_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('reason_for_failure')->nullable();
            
            $table->timestamps();
            $table->index('tracking_number');
            $table->index('status');
        });

        // Add shipping columns to orders if they don't exist
        if (!Schema::hasColumn('orders', 'shipping_method')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->enum('shipping_method', ['standard', 'express', 'overnight', 'pickup'])
                      ->default('standard')
                      ->after('payment_method');
                $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipping_method');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
        
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_method')) {
                $table->dropColumn('shipping_method');
            }
            if (Schema::hasColumn('orders', 'shipping_cost')) {
                $table->dropColumn('shipping_cost');
            }
        });
    }
};
