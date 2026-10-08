<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->longText('description')->nullable();

            $table->decimal('price', 12, 2);
            $table->unsignedInteger('quantity')->default(0);
            $table->enum('stock_status', ['In Stock', 'Out of Stock', 'Pre-Order'])
                  ->default('In Stock');
            $table->decimal('previous_price', 12, 2)->nullable();

            $table->string('sku')->nullable();
            $table->unsignedInteger('min_order')->nullable();
            $table->unsignedInteger('max_order')->nullable();

            $table->string('featured_image');
            $table->json('gallery_images')->nullable(); // multiple files stored as JSON array
            $table->string('video')->nullable();

            $table->foreignId('category_id')
                  ->constrained('product_categories')
                  ->cascadeOnDelete();

            $table->string('product_unit')->nullable(); // kg, gm, pcs

            $table->json('specification')->nullable();
            $table->json('product_tags')->nullable();

            $table->boolean('is_featured')->default(1);
            $table->boolean('is_show_frontend')->default(1);

            $table->json('size_chart')->nullable();

            $table->string('status')->default('Active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};