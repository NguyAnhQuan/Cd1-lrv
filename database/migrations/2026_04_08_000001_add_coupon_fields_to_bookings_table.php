<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'coupon_id')) {
                $table->foreignId('coupon_id')->nullable()->after('tour_id')->constrained('coupons')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'coupon_code')) {
                $table->string('coupon_code', 100)->nullable()->after('coupon_id');
            }
            if (! Schema::hasColumn('bookings', 'coupon_discount_vnd')) {
                $table->unsignedInteger('coupon_discount_vnd')->default(0)->after('coupon_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'coupon_id')) {
                $table->dropConstrainedForeignId('coupon_id');
            }
            if (Schema::hasColumn('bookings', 'coupon_code')) {
                $table->dropColumn('coupon_code');
            }
            if (Schema::hasColumn('bookings', 'coupon_discount_vnd')) {
                $table->dropColumn('coupon_discount_vnd');
            }
        });
    }
};

