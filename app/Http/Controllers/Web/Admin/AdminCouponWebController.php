<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Api\Admin\AdminCouponController;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCouponWebController extends Controller
{
    public function index(): View
    {
        $json = app(AdminCouponController::class)->index();
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json->getContent(), true) ?? [];
        /** @var list<array<string, mixed>> $coupons */
        $coupons = $decoded['data'] ?? [];

        return view('admin.coupons.index', ['coupons' => $coupons]);
    }

    public function create(): View
    {
        return view('admin.coupons.form', ['coupon' => null, 'mode' => 'create']);
    }

    public function store(Request $request): RedirectResponse
    {
        $json = app(AdminCouponController::class)->store($request);
        if ($json->getStatusCode() >= 400) {
            return back()->withErrors(['message' => 'Dữ liệu không hợp lệ'])->withInput();
        }

        return redirect()->route('admin.coupons.index')->with('success', 'Đã tạo voucher.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.form', ['coupon' => $coupon, 'mode' => 'edit']);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $json = app(AdminCouponController::class)->update($request, $coupon);
        if ($json->getStatusCode() >= 400) {
            return back()->withErrors(['message' => 'Cập nhật thất bại'])->withInput();
        }

        return redirect()->route('admin.coupons.index')->with('success', 'Đã cập nhật voucher.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        app(AdminCouponController::class)->destroy($coupon);

        return redirect()->route('admin.coupons.index')->with('success', 'Đã xóa voucher.');
    }
}
