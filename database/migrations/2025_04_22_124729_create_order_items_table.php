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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // معرف العنصر
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // معرف الطلب (علاقة مع جدول orders)
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // معرف المنتج (علاقة مع جدول المنتجات)
            $table->integer('quantity'); // كمية المنتج في الطلب
            $table->json('image')->nullable();
            $table->decimal('price', 10, 2); // سعر المنتج
            $table->decimal('total_price', 10, 2); // إجمالي سعر العنصر (الكمية × السعر)
            $table->timestamps(); // إضافة حقل created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};