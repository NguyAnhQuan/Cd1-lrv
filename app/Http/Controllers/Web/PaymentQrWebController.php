<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\BookingCheckoutController;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentQrWebController extends Controller
{
    public function vietqr(Request $request, Payment $payment): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }
        $request->setUserResolver(fn () => Auth::user());
        $json = app(BookingCheckoutController::class)->vietqr($request, $payment);
        $body = json_decode($json->getContent(), true);
        if ($json->getStatusCode() >= 400) {
            return redirect()->route('my-trips')->with('error', $body['message'] ?? 'Không lấy được mã QR.');
        }
        $data = $body['data'] ?? [];

        return view('web.booking.vietqr', ['data' => is_array($data) ? $data : []]);
    }
}
