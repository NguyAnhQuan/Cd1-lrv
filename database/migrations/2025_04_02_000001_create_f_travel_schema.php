<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('avatar')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('slug', 100)->nullable()->index();
        });

        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->index();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->string('start_location')->nullable();
            $table->unsignedInteger('max_people')->nullable();
            $table->text('thumbnail')->nullable();
            $table->string('status', 50)->nullable();
            $table->decimal('rating', 3, 1)->default(4.8);
            $table->string('badge_label', 100)->nullable();
            $table->string('badge_variant', 50)->nullable();
            $table->string('meta_text1', 100)->nullable();
            $table->string('meta_text2', 100)->nullable();
            $table->string('meta_icon1', 50)->nullable();
            $table->string('meta_icon2', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('tour_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->text('image_url')->nullable();
        });

        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->integer('day_number')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tour_id')->nullable()->constrained('tours')->nullOnDelete();
            $table->string('booking_code', 100)->nullable()->unique();
            $table->decimal('total_price', 12, 2)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('payment_status', 50)->nullable();
            $table->dateTime('booking_date')->nullable();
            $table->dateTime('travel_date')->nullable();
            $table->integer('number_of_people')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->string('customer_email')->nullable();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('payment_method', 100)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('transaction_code')->nullable();
            $table->dateTime('paid_at')->nullable();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->nullable()->unique();
            $table->string('discount_type', 50)->nullable();
            $table->decimal('discount_value', 12, 2)->nullable();
            $table->decimal('min_order_value', 12, 2)->nullable();
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('status', 50)->nullable();
        });

        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->boolean('is_used')->default(false);
            $table->dateTime('used_at')->nullable();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->integer('rating')->nullable();
            $table->text('comment')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->text('image')->nullable();
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->string('placement', 50)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('user_coupons');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('booking_details');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('itineraries');
        Schema::dropIfExists('tour_images');
        Schema::dropIfExists('tours');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
    }
};
