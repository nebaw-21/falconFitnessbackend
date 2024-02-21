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
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable()->default(NULL);
            $table->unsignedBigInteger('product_color_id')->nullable()->default(NULL);
            $table->string('size');
            $table->boolean('published')->default(1);

            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_color_id')->references('id')->on('product_colors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};