<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CollectCouponRequest;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Services\Coupons\CouponPublicCatalogService;
use App\Services\Coupons\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Voucher / coupon công khai cho trang khuyến mãi (user).
 */
class CouponApiController extends Controller
{
    public function __construct(
        private readonly CouponService $coupons,
        private readonly CouponPublicCatalogService $couponCatalog,
    ) {}

    public function index(): JsonResponse
    {
        $rows = $this->couponCatalog->activePublicCouponsQuery()->get();

        return response()->json([
            'data' => $this->couponCatalog->mapRowsToPublicDtos($rows),
        ]);
    }

    /** User thu thập coupon (mỗi coupon 1 lần / user). */
    public function collect(CollectCouponRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $code = $this->coupons->normalizeCode((string) $request->validated('code'));
        $coupon = $this->coupons->findByNormalizedCode($code);
        if (! $coupon) {
            return response()->json(['message' => 'Voucher không tồn tại.'], 404);
        }
        if (! $this->coupons->isActiveNow($coupon)) {
            return response()->json(['message' => 'Voucher đã hết hạn hoặc không còn hiệu lực.'], 422);
        }
        if (! $this->coupons->hasGlobalUsesRemaining($coupon)) {
            return response()->json(['message' => 'Voucher đã hết lượt sử dụng.'], 422);
        }

        $uc = DB::transaction(function () use ($user, $coupon) {
            $existing = UserCoupon::query()
                ->where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->first();
            if ($existing) {
                return $existing;
            }

            return UserCoupon::query()->create([
                'user_id' => $user->id,
                'coupon_id' => $coupon->id,
                'is_used' => false,
                'used_at' => null,
            ]);
        });

        // Đã thu thập trước đó (đặc biệt: đã dùng) thì không cho "thu thập lại".
        if ($uc && (bool) $uc->wasRecentlyCreated === false) {
            if ((bool) $uc->is_used) {
                return response()->json(['message' => 'Voucher này đã được sử dụng, không thể thu thập lại.'], 422);
            }

            return response()->json(['message' => 'Voucher này đã được thu thập trước đó.'], 422);
        }

        return response()->json([
            'data' => [
                'ok' => true,
                'code' => (string) ($coupon->code ?? ''),
                'is_used' => (bool) $uc->is_used,
            ],
        ], 201);
    }

    /** Danh sách coupon user đã thu thập. */
    public function my(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $rows = UserCoupon::query()
            ->where('user_id', $user->id)
            ->with('coupon')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $rows->map(function (UserCoupon $uc) {
                $c = $uc->coupon;

                return [
                    'code' => (string) ($c?->code ?? ''),
                    'is_used' => (bool) $uc->is_used,
                    'used_at' => $uc->used_at?->toISOString(),
                ];
            })->values(),
        ]);
    }
}
