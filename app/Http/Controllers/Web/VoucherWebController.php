<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\CouponApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CollectCouponRequest;
use App\Models\UserCoupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VoucherWebController extends Controller
{
    public function index(): View
    {
        $response = app(CouponApiController::class)->index();
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($response->getContent(), true) ?? [];
        /** @var list<array<string, mixed>> $coupons */
        $coupons = $decoded['data'] ?? [];

        if (Auth::check()) {
            $codes = UserCoupon::query()
                ->where('user_id', Auth::id())
                ->pluck('coupon_id');
            // Đánh dấu collected theo mã — API public luôn false; đồng bộ với Flutter.
            $couponIds = $codes->all();
            if ($couponIds !== []) {
                $idToCode = \App\Models\Coupon::query()
                    ->whereIn('id', $couponIds)
                    ->pluck('code', 'id');
                foreach ($coupons as &$c) {
                    $code = strtoupper(trim((string) ($c['code'] ?? '')));
                    foreach ($idToCode as $cid => $dbCode) {
                        if (strtoupper(trim((string) $dbCode)) === $code) {
                            $c['collected'] = true;
                            break;
                        }
                    }
                }
                unset($c);
            }
        }

        return view('web.vouchers.index', ['coupons' => $coupons]);
    }

    public function collect(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thu thập voucher.');
        }

        $request->merge($request->validate([
            'code' => ['required', 'string', 'max:100'],
        ]));
        $fr = CollectCouponRequest::createFrom($request);
        $fr->setUserResolver(fn () => Auth::user());
        $fr->setContainer(app())->setRedirector(app('redirect'));
        $fr->validateResolved();

        $json = app(CouponApiController::class)->collect($fr);
        $body = json_decode($json->getContent(), true);
        if ($json->getStatusCode() >= 400) {
            return back()->with('error', $body['message'] ?? 'Không thu thập được voucher.');
        }

        return back()->with('success', 'Đã thu thập voucher thành công.');
    }
}
