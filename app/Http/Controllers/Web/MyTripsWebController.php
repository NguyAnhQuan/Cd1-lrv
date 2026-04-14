<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\BookingCheckoutController;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyTripsWebController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem chuyến của bạn.');
        }

        $request->setUserResolver(fn () => Auth::user());
        $json = app(BookingCheckoutController::class)->myBookings($request);
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json->getContent(), true) ?? [];
        /** @var list<array<string, mixed>> $bookings */
        $bookings = $decoded['data'] ?? [];

        return view('web.my-trips.index', ['bookings' => $bookings]);
    }
}
