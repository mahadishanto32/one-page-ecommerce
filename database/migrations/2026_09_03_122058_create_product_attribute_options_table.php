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
        Schema::create('product_attribute_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('attribute_id')
                  ->constrained('product_attributes')
                  ->onDelete('cascade');
            $table->string('color_code')->nullable();
            $table->string('sku')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('image')->nullable();
            $table->decimal('added_price', 10, 2)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('attribute_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_options');
    }
};