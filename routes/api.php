<?php

use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TourApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AdminCouponController;
use App\Http\Controllers\Api\Admin\AdminReviewController;
use App\Http\Controllers\Api\Admin\AdminTourController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\AdminRoleController;
use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminReportController;
use App\Http\Controllers\Api\CouponApiController;
use App\Http\Controllers\Api\BookingCheckoutController;

Route::get('/home', [HomeApiController::class, 'index']);
Route::get('/coupons', [CouponApiController::class, 'index']);
Route::get('/tours', [TourApiController::class, 'index']);
Route::get('/tours/{slug}', [TourApiController::class, 'show']);

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::delete('/auth/account', [AuthController::class, 'destroyAccount']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::post('/coupons/collect', [CouponApiController::class, 'collect']);
    Route::get('/coupons/my', [CouponApiController::class, 'my']);

    Route::post('/bookings/checkout', [BookingCheckoutController::class, 'store']);
    Route::get('/bookings/my', [BookingCheckoutController::class, 'myBookings']);
    Route::post('/bookings/payments/{payment}/confirm', [BookingCheckoutController::class, 'confirmPayment']);
    Route::post('/bookings/payments/{payment}/cancel', [BookingCheckoutController::class, 'cancelPayment']);
    Route::get('/bookings/payments/{payment}/vietqr', [BookingCheckoutController::class, 'vietqr']);

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/categories', [AdminCategoryController::class, 'index']);
        Route::get('/tours', [AdminTourController::class, 'index']);
        Route::post('/tours', [AdminTourController::class, 'store']);
        Route::put('/tours/{tour}', [AdminTourController::class, 'update']);
        Route::delete('/tours/{tour}', [AdminTourController::class, 'destroy']);

        Route::get('/users', [AdminUserController::class, 'index']);
        Route::put('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);

        Route::get('/coupons', [AdminCouponController::class, 'index']);
        Route::post('/coupons', [AdminCouponController::class, 'store']);
        Route::put('/coupons/{coupon}', [AdminCouponController::class, 'update']);
        Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy']);

        Route::get('/reviews', [AdminReviewController::class, 'index']);
        Route::put('/reviews/{review}', [AdminReviewController::class, 'update']);

        Route::get('/roles', [AdminRoleController::class, 'index']);
        Route::post('/roles', [AdminRoleController::class, 'store']);
        Route::put('/roles/{role}', [AdminRoleController::class, 'update']);
        Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy']);

        Route::get('/reports/dashboard', [AdminReportController::class, 'dashboard']);
        Route::get('/reports/statistics', [AdminReportController::class, 'statistics']);
        Route::get('/reports/export/bookings-csv', [AdminReportController::class, 'exportBookingsCsv']);
        Route::get('/reports/export/revenue-pdf', [AdminReportController::class, 'exportRevenuePdf']);
    });
});
