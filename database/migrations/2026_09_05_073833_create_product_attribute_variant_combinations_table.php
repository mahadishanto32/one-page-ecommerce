<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attribute_variant_combinations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products', 'id', 'pavc_product_fk')
                ->cascadeOnDelete();

            $table->foreignId('product_attribute_one_id')
                ->constrained('product_attributes', 'id', 'pavc_attr_one_fk')
                ->cascadeOnDelete();

            $table->foreignId('product_attribute_two_id')
                ->constrained('product_attributes', 'id', 'pavc_attr_two_fk')
                ->cascadeOnDelete();

            $table->foreignId('product_option_attribute_one_id')
                ->constrained('product_attribute_options', 'id', 'pavc_opt_one_fk')
                ->cascadeOnDelete();

            $table->foreignId('product_attribute_option_two_id')
                ->constrained('product_attribute_options', 'id', 'pavc_opt_two_fk')
                ->cascadeOnDelete();

            $table->string('image')->nullable();
            $table->string('sku')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('additional_price', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_variant_combinations');
    }
};
