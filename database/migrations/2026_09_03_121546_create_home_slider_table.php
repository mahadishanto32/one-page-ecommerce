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
        Schema::create('home_slider', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('slider_type', ['image', 'video'])->default('image');
            $table->string('file_path');
            $table->integer('serial')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            // Optional: Add indexes for better performance
            $table->index('status');
            $table->index('serial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_slider');
    }
};
