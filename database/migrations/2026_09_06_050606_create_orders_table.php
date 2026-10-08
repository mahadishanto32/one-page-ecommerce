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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->text('note')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('status', [
                'pending',
                'processing',
                'delivered',
                'partial_delivered',
                'cancelled',
                'returned',
                'refunded',
                'incomplete',
            ])->default('pending');
            $table->string('courier_name')->nullable();
            $table->string('courier_tracking_id')->nullable();
            $table->string('consignment_id')->nullable();
            $table->decimal('total', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};