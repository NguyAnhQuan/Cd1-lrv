<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\BookingCheckoutController;
use App\Http\Controllers\Api\TourApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingCheckoutRequest;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingWebController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt tour.');
        }

        $tour = null;
        $tourId = $request->query('tour_id');
        $slug = $request->query('slug');
        if ($slug) {
            $res = app(TourApiController::class)->show((string) $slug);
            if ($res->getStatusCode() === 200) {
                $decoded = json_decode($res->getContent(), true);
                $tour = $decoded['data'] ?? null;
            }
        } elseif ($tourId) {
            $res = app(TourApiController::class)->show((string) (int) $tourId);
            if ($res->getStatusCode() === 200) {
                $decoded = json_decode($res->getContent(), true);
                $tour = $decoded['data'] ?? null;
            }
        }

        $preferredDate = $request->query('travel_date');

        return view('web.booking.checkout', [
            'tour' => is_array($tour) ? $tour : null,
            'preferredTravelDate' => $preferredDate,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $sub = StoreBookingCheckoutRequest::createFrom($request);
        $sub->setUserResolver(fn () => Auth::user());
        $sub->setContainer(app())->setRedirector(app('redirect'));
        $sub->validateResolved();

        $json = app(BookingCheckoutController::class)->store($sub);
        $status = $json->getStatusCode();
        $body = json_decode($json->getContent(), true);
        if ($status >= 400) {
            return back()
                ->withErrors(['booking' => $body['message'] ?? 'Không tạo được đơn.'])
                ->withInput();
        }

        $data = $body['data'] ?? [];
        if (! is_array($data)) {
            return back()->withErrors(['booking' => 'Phản hồi không hợp lệ.'])->withInput();
        }

        return redirect()
            ->route('booking.result')
            ->with('checkout', $data);
    }

    public function result(Request $request): View|RedirectResponse
    {
        $checkout = $request->session()->get('checkout');
        if (! is_array($checkout)) {
            return redirect()->route('booking.create');
        }

        return view('web.booking.result', ['checkout' => $checkout]);
    }

    public function confirmPayment(Request $request, Payment $payment): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }
        $request->setUserResolver(fn () => Auth::user());
        $json = app(BookingCheckoutController::class)->confirmPayment($request, $payment);
        $body = json_decode($json->getContent(), true);
        if ($json->getStatusCode() >= 400) {
            return back()->with('error', $body['message'] ?? 'Không xác nhận được thanh toán.');
        }

        return redirect()->route('my-trips')->with('success', 'Đã xác nhận thanh toán.');
    }

    public function cancelPayment(Request $request, Payment $payment): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }
        $request->setUserResolver(fn () => Auth::user());
        $json = app(BookingCheckoutController::class)->cancelPayment($request, $payment);
        $body = json_decode($json->getContent(), true);
        if ($json->getStatusCode() >= 400) {
            return back()->with('error', $body['message'] ?? 'Không hủy được giao dịch.');
        }

        return redirect()->route('my-trips')->with('success', 'Đã hủy giao dịch chờ thanh toán.');
    }
}
