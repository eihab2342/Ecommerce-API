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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود الكوبون
            $table->enum('type', ['fixed', 'percent', 'free_shipping'])->default('fixed'); // نوع الخصم
            $table->decimal('value', 8, 2)->nullable(); // قيمة الخصم
            $table->decimal('min_order_amount', 8, 2)->nullable(); // أقل قيمة للسلة
            $table->unsignedBigInteger('user_id')->nullable(); // لو الكوبون مخصص لمستخدم معين
            $table->boolean('is_active')->default(true); // مفعّل أو لا
            $table->integer('usage_limit')->nullable(); // عدد مرات الاستخدام المسموح
            $table->integer('used_count')->default(0); // عدد المرات اللي اتستخدم فيها الكوبون
            $table->timestamp('starts_at')->nullable(); // بداية الصلاحية
            $table->timestamp('expires_at')->nullable(); // نهاية الصلاحية
            $table->timestamps();

            // لو عاوز تربط الكوبون بمستخدم معين
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};