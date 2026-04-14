<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingCheckoutRequest;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Tour;
use App\Models\User;
use App\Models\UserCoupon;
use App\Services\Bookings\BookingCodeGenerator;
use App\Services\Bookings\CheckoutPricingService;
use App\Services\Coupons\CouponService;
use App\Services\VietQrService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingCheckoutController extends Controller
{
    public function __construct(
        private readonly VietQrService $vietQr,
        private readonly CouponService $coupons,
        private readonly CheckoutPricingService $checkoutPricing,
        private readonly BookingCodeGenerator $bookingCodes,
    ) {}

    public function store(StoreBookingCheckoutRequest $request): JsonResponse
    {
        $data = $request->validated();

        $tour = Tour::query()->findOrFail((int) $data['tour_id']);
        /** @var \App\Models\User $user */
        $user = $request->user();

        $coupon = null;
        $couponDiscountVnd = 0;
        $userCoupon = null;
        if (($data['coupon_code'] ?? null) !== null && trim((string) $data['coupon_code']) !== '') {
            $code = $this->coupons->normalizeCode((string) $data['coupon_code']);
            $coupon = $this->coupons->findByNormalizedCode($code);
            if (! $coupon) {
                return response()->json(['message' => 'Voucher không tồn tại.'], 422);
            }
            if (! $this->coupons->isActiveNow($coupon)) {
                return response()->json(['message' => 'Voucher đã hết hạn hoặc không còn hiệu lực.'], 422);
            }
            if (! $this->coupons->hasGlobalUsesRemaining($coupon)) {
                return response()->json(['message' => 'Voucher đã hết lượt sử dụng.'], 422);
            }
            $uc = UserCoupon::query()
                ->where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->first();
            if (! $uc) {
                return response()->json(['message' => 'Bạn chưa thu thập voucher này.'], 422);
            }
            if ((bool) $uc->is_used) {
                return response()->json(['message' => 'Voucher này đã được sử dụng.'], 422);
            }
            $userCoupon = $uc;
            $couponDiscountVnd = $this->coupons->computeDiscountVndForTour($coupon, $tour);
        }

        $amountVnd = $this->checkoutPricing->computeTotalVnd($tour, $couponDiscountVnd);
        if ($amountVnd <= 0) {
            return response()->json([
                'message' => 'Không xác định được số tiền tour. Vui lòng liên hệ hỗ trợ.',
            ], 422);
        }

        $people = (int) ($data['number_of_people'] ?? 1);
        $travelDate = isset($data['travel_date']) ? Carbon::parse($data['travel_date'])->startOfDay() : null;

        $payload = DB::transaction(function () use ($data, $tour, $amountVnd, $people, $travelDate, $user, $coupon, $couponDiscountVnd, $userCoupon) {
            $bookingCode = $this->bookingCodes->generate();

            // Voucher: khóa ngay khi tạo đơn để tránh user tạo nhiều đơn dùng cùng 1 voucher.
            // Nếu user hủy giao dịch pending, sẽ hoàn lại (xem cancelPayment).
            if ($coupon && $userCoupon && ! $userCoupon->is_used) {
                $userCoupon->update([
                    'is_used' => true,
                    'used_at' => now(),
                ]);
                Coupon::query()->where('id', (int) $coupon->id)->increment('used_count');
            }

            $booking = Booking::query()->create([
                'user_id' => $user->id,
                'tour_id' => $tour->id,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon ? (string) ($coupon->code ?? '') : null,
                'coupon_discount_vnd' => $couponDiscountVnd,
                'booking_code' => $bookingCode,
                'total_price' => $amountVnd,
                'status' => 'pending',
                'payment_status' => 'pending',
                'booking_date' => now(),
                'travel_date' => $travelDate,
                'number_of_people' => $people,
                'created_at' => now(),
            ]);

            BookingDetail::query()->create([
                'booking_id' => $booking->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'],
            ]);

            $payment = Payment::query()->create([
                'booking_id' => $booking->id,
                'payment_method' => $data['payment_method'],
                'amount' => $amountVnd,
                'status' => 'pending',
                'transaction_code' => null,
                'paid_at' => null,
            ]);

            $addInfo = sprintf('FT-P%d-T%d', $payment->id, $tour->id);
            $payment->update(['transaction_code' => $addInfo]);

            return [
                'booking' => $booking->fresh(),
                'payment' => $payment->fresh(),
                'add_info' => $addInfo,
                'amount_vnd' => $amountVnd,
            ];
        });

        $vietqrUrl = null;
        if ($data['payment_method'] === 'bank_transfer') {
            $vietqrUrl = $this->vietQr->buildImageUrl($payload['amount_vnd'], $payload['add_info']);
        }

        /** @var Booking $b */
        $b = $payload['booking'];
        /** @var Payment $p */
        $p = $payload['payment'];

        return response()->json([
            'data' => [
                'booking_id' => $b->id,
                'booking_code' => (string) ($b->booking_code ?? ''),
                'payment_id' => $p->id,
                'tour_id' => $tour->id,
                'amount_vnd' => $payload['amount_vnd'],
                'add_info' => $payload['add_info'],
                'vietqr_image_url' => $vietqrUrl,
            ],
        ], 201);
    }

    /** Danh sách đơn của user đang đăng nhập. */
    public function myBookings(Request $request): JsonResponse
    {
        $rows = Booking::query()
            ->where('user_id', $request->user()->id)
            ->with(['tour', 'payments', 'detail'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $rows->map(function (Booking $b) {
                $t = $b->tour;
                $pay = $b->payments->first();

                return [
                    'id' => $b->id,
                    'booking_code' => (string) ($b->booking_code ?? ''),
                    'status' => (string) ($b->status ?? ''),
                    'payment_status' => (string) ($b->payment_status ?? ''),
                    'travel_date' => $b->travel_date?->format('Y-m-d'),
                    'total_price' => (float) ($b->total_price ?? 0),
                    'tour_id' => $t?->id ?? $b->tour_id,
                    'tour_slug' => $t?->slug,
                    'tour_name' => $t?->name ?? '',
                    'tour_thumbnail' => $t?->thumbnail ?? '',
                    'payment_id' => $pay?->id,
                    'payment_method' => $pay?->payment_method,
                    'payment_row_status' => $pay?->status,
                    'coupon_code' => (string) ($b->coupon_code ?? ''),
                ];
            }),
        ]);
    }

    /** Người dùng xác nhận đã chuyển khoản — ghi nhận tour thuộc khách, cập nhật DB. */
    public function confirmPayment(Request $request, Payment $payment): JsonResponse
    {
        $booking = $payment->booking;
        if (! $booking || (int) $booking->user_id !== (int) $request->user()->id) {
            return response()->json([
                'message' => 'Không có quyền thực hiện.',
            ], 403);
        }

        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Thanh toán không còn ở trạng thái chờ xử lý.',
            ], 422);
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            $booking = $payment->booking;
            if ($booking) {
                $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                ]);

                // Voucher đã bị "khóa" ở store() khi tạo đơn. Ở confirm chỉ cần giữ nguyên.

                $owner = User::query()->find($booking->user_id);
                if ($owner) {
                    $total = (float) ($booking->total_price ?? 0);
                    if ($total > 0) {
                        // 1 điểm / 10.000đ thanh toán, tối thiểu 1 điểm mỗi giao dịch thành công
                        $earned = max(1, (int) floor($total / 10000));
                        $owner->increment('loyalty_points', $earned);
                    }
                }
            }
        });

        return response()->json([
            'data' => ['ok' => true, 'payment_id' => $payment->id],
        ]);
    }

    /** Lấy lại link VietQR cho thanh toán pending (để thanh toán lại từ Quản lý chuyến đi). */
    public function vietqr(Request $request, Payment $payment): JsonResponse
    {
        $booking = $payment->booking;
        if (! $booking || (int) $booking->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Không có quyền thực hiện.'], 403);
        }
        if ($payment->status !== 'pending') {
            return response()->json(['message' => 'Thanh toán không còn ở trạng thái chờ xử lý.'], 422);
        }
        if ($payment->payment_method !== 'bank_transfer') {
            return response()->json(['message' => 'Giao dịch này không phải chuyển khoản.'], 422);
        }
        $tourId = (int) ($booking->tour_id ?? 0);
        $addInfo = (string) ($payment->transaction_code ?? '');
        if ($addInfo === '') {
            $addInfo = sprintf('FT-P%d-T%d', $payment->id, $tourId);
            $payment->update(['transaction_code' => $addInfo]);
        }
        $amountVnd = (int) round((float) ($payment->amount ?? 0));
        if ($amountVnd <= 0) {
            $amountVnd = (int) round((float) ($booking->total_price ?? 0));
        }
        $vietqrUrl = $this->vietQr->buildImageUrl($amountVnd, $addInfo);

        return response()->json([
            'data' => [
                'booking_id' => $booking->id,
                'booking_code' => (string) ($booking->booking_code ?? ''),
                'payment_id' => $payment->id,
                'tour_id' => $tourId,
                'amount_vnd' => $amountVnd,
                'add_info' => $addInfo,
                'vietqr_image_url' => $vietqrUrl,
            ],
        ]);
    }

    /** Người dùng hủy — không chấp nhận thanh toán, đơn / giao dịch chuyển sang hủy. */
    public function cancelPayment(Request $request, Payment $payment): JsonResponse
    {
        $booking = $payment->booking;
        if (! $booking || (int) $booking->user_id !== (int) $request->user()->id) {
            return response()->json([
                'message' => 'Không có quyền thực hiện.',
            ], 403);
        }

        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Không thể hủy giao dịch này.',
            ], 422);
        }

        DB::transaction(function () use ($payment, $request) {
            $payment->update([
                'status' => 'cancelled',
                'paid_at' => null,
            ]);
            $booking = $payment->booking;
            if ($booking) {
                $booking->update([
                    'status' => 'cancelled',
                    'payment_status' => 'cancelled',
                ]);

                // Hoàn lại voucher nếu đơn bị hủy ở trạng thái pending (chưa trả tiền).
                if ($booking->coupon_id !== null) {
                    $uc = UserCoupon::query()
                        ->where('user_id', $request->user()->id)
                        ->where('coupon_id', (int) $booking->coupon_id)
                        ->first();
                    if ($uc && $uc->is_used) {
                        $uc->update([
                            'is_used' => false,
                            'used_at' => null,
                        ]);
                        // Giảm used_count nhưng không âm.
                        $c = Coupon::query()->find((int) $booking->coupon_id);
                        if ($c) {
                            $cur = (int) ($c->used_count ?? 0);
                            if ($cur > 0) {
                                $c->decrement('used_count');
                            }
                        }
                    }
                }
            }
        });

        return response()->json([
            'data' => ['ok' => true, 'payment_id' => $payment->id],
        ]);
    }
}
