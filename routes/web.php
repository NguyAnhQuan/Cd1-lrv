<?php

use App\Http\Controllers\Web\Admin\AdminCouponWebController;
use App\Http\Controllers\Web\Admin\AdminDashboardWebController;
use App\Http\Controllers\Web\Admin\AdminReviewWebController;
use App\Http\Controllers\Web\Admin\AdminRoleWebController;
use App\Http\Controllers\Web\Admin\AdminStatisticsWebController;
use App\Http\Controllers\Web\Admin\AdminTourWebController;
use App\Http\Controllers\Web\Admin\AdminUserWebController;
use App\Http\Controllers\Api\Admin\AdminReportController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\BookingWebController;
use App\Http\Controllers\Web\HomeWebController;
use App\Http\Controllers\Web\MyTripsWebController;
use App\Http\Controllers\Web\PaymentQrWebController;
use App\Http\Controllers\Web\ProfileWebController;
use App\Http\Controllers\Web\TourWebController;
use App\Http\Controllers\Web\VoucherWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeWebController::class, 'index'])->name('home');

Route::get('/tours/domestic', [TourWebController::class, 'catalogDomestic'])->name('tours.domestic');
Route::get('/tours/international', [TourWebController::class, 'catalogInternational'])->name('tours.international');
Route::get('/tours/detail/{slug}', [TourWebController::class, 'show'])->name('tours.show');

Route::get('/vouchers', [VoucherWebController::class, 'index'])->name('vouchers');
Route::post('/vouchers/collect', [VoucherWebController::class, 'collect'])->middleware('auth')->name('vouchers.collect');

Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.store');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthWebController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/booking/checkout', [BookingWebController::class, 'create'])->name('booking.create');
    Route::post('/booking/checkout', [BookingWebController::class, 'store'])->name('booking.store');
    Route::get('/booking/result', [BookingWebController::class, 'result'])->name('booking.result');

    Route::get('/my-trips', [MyTripsWebController::class, 'index'])->name('my-trips');

    Route::get('/profile', [ProfileWebController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileWebController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileWebController::class, 'destroy'])->name('profile.destroy');

    Route::post('/payments/{payment}/confirm', [BookingWebController::class, 'confirmPayment'])->name('payments.confirm');
    Route::post('/payments/{payment}/cancel', [BookingWebController::class, 'cancelPayment'])->name('payments.cancel');
    Route::get('/payments/{payment}/vietqr', [PaymentQrWebController::class, 'vietqr'])->name('payments.vietqr');
});

Route::middleware(['auth', 'admin.web'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardWebController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [AdminStatisticsWebController::class, 'index'])->name('statistics');

    Route::get('/reports/export/bookings-csv', [AdminReportController::class, 'exportBookingsCsv'])->name('reports.bookings-csv');
    Route::get('/reports/export/revenue-pdf', [AdminReportController::class, 'exportRevenuePdf'])->name('reports.revenue-pdf');

    Route::get('/tours', [AdminTourWebController::class, 'index'])->name('tours.index');
    Route::get('/tours/create', [AdminTourWebController::class, 'create'])->name('tours.create');
    Route::post('/tours', [AdminTourWebController::class, 'store'])->name('tours.store');
    Route::get('/tours/{tour}/edit', [AdminTourWebController::class, 'edit'])->name('tours.edit');
    Route::put('/tours/{tour}', [AdminTourWebController::class, 'update'])->name('tours.update');
    Route::delete('/tours/{tour}', [AdminTourWebController::class, 'destroy'])->name('tours.destroy');

    Route::get('/users', [AdminUserWebController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [AdminUserWebController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserWebController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserWebController::class, 'destroy'])->name('users.destroy');

    Route::get('/coupons', [AdminCouponWebController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [AdminCouponWebController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [AdminCouponWebController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit', [AdminCouponWebController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{coupon}', [AdminCouponWebController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [AdminCouponWebController::class, 'destroy'])->name('coupons.destroy');

    Route::get('/reviews', [AdminReviewWebController::class, 'index'])->name('reviews.index');
    Route::put('/reviews/{review}', [AdminReviewWebController::class, 'update'])->name('reviews.update');

    Route::get('/roles', [AdminRoleWebController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [AdminRoleWebController::class, 'create'])->name('roles.create');
    Route::post('/roles', [AdminRoleWebController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [AdminRoleWebController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [AdminRoleWebController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [AdminRoleWebController::class, 'destroy'])->name('roles.destroy');
});
