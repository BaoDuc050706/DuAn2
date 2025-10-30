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
            $table->unsignedBigInteger('category_id')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedTinyInteger('discount')->default(0);
            $table->string('connection')->nullable(); // nếu dùng
            $table->boolean('rgb')->default(false);   // nếu dùng
            $table->string('image')->nullable();      // fallback ảnh chính
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('category_id');
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
