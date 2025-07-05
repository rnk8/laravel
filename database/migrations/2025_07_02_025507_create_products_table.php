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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('manage_stock')->default(true);
            $table->boolean('in_stock')->default(true);
            $table->string('status', 20)->default('active'); // active, inactive, discontinued
            $table->json('images')->nullable(); // Array de URLs de imágenes
            $table->json('attributes')->nullable(); // Características del producto
            $table->string('material')->nullable(); // Material principal (acero, aluminio, etc.)
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->decimal('weight', 8, 2)->nullable(); // en gramos
            $table->string('dimensions')->nullable(); // dimensiones del producto
            $table->string('warranty')->nullable(); // garantía
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->decimal('rating', 2, 1)->default(0); // rating promedio
            $table->integer('reviews_count')->default(0);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['status', 'is_featured']);
            $table->index(['category_id', 'status']);
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
