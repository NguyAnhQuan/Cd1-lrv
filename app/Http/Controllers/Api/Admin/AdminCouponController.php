<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Coupon::query()->orderByDesc('id')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:100'],
            'title' => ['nullable', 'string', 'max:255'],
            'scope' => ['nullable', 'string', 'max:32'],
            'discount_type' => ['required', 'string', 'max:50'],
            'discount_value' => ['required', 'numeric'],
            'min_order_value' => ['nullable', 'numeric'],
            'max_discount' => ['nullable', 'numeric'],
            'quantity' => ['nullable', 'integer'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $coupon = Coupon::query()->create($data + ['used_count' => 0]);

        return response()->json(['data' => $coupon], 201);
    }

    public function update(Request $request, Coupon $coupon): JsonResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'scope' => ['nullable', 'string', 'max:32'],
            'discount_type' => ['nullable', 'string', 'max:50'],
            'discount_value' => ['nullable', 'numeric'],
            'min_order_value' => ['nullable', 'numeric'],
            'max_discount' => ['nullable', 'numeric'],
            'quantity' => ['nullable', 'integer'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $coupon->fill($data)->save();

        return response()->json(['data' => $coupon->fresh()]);
    }

    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();

        return response()->json(['ok' => true]);
    }
}

