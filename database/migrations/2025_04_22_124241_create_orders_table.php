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
            $table->id(); // معرف الطلب
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // معرف المستخدم (إذا كان هناك تسجيل دخول)
            $table->string('name');
            $table->string('email');
            $table->string('phone_number');
            $table->decimal('total_price', 10, 2); // إجمالي المبلغ
            $table->decimal('original_price', 10, 2); // إجمالي المبلغ
            $table->string('status')->default('pending'); // حالة الطلب (مثل pending, completed, cancelled)
            $table->text('shipping_address'); // عنوان الشحن
            $table->text('village'); // عنوان الشحن
            $table->text('city'); // عنوان الشحن
            $table->text('governorate'); // عنوان الشحن
            $table->text('billing_address')->nullable(); // عنوان الفاتورة
            $table->string('payment_method'); // طريقة الدفع
            $table->timestamp('ordered_at')->useCurrent(); // تاريخ ووقت الطلب
            $table->timestamps(); // timestamps لإنشاء الحقول created_at و updated_at
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