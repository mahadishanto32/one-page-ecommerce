<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_attribute_option_images', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('product_id');

            $table->unsignedBigInteger('product_attribute_option_id');

            $table->string('image_path');

            $table->timestamps();

            $table->foreign('product_id', 'paoi_product_id_fk')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('product_attribute_option_id', 'paoi_option_id_fk')
                ->references('id')
                ->on('product_attribute_options')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_attribute_option_images');
    }
};
