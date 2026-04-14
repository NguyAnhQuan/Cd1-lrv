<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Api\Admin\AdminReviewController;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewWebController extends Controller
{
    public function index(): View
    {
        $json = app(AdminReviewController::class)->index();
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json->getContent(), true) ?? [];
        /** @var list<array<string, mixed>> $reviews */
        $reviews = $decoded['data'] ?? [];

        return view('admin.reviews.index', ['reviews' => $reviews]);
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $json = app(AdminReviewController::class)->update($request, $review);
        if ($json->getStatusCode() >= 400) {
            return back()->with('error', 'Cập nhật thất bại.');
        }

        return redirect()->route('admin.reviews.index')->with('success', 'Đã cập nhật đánh giá.');
    }
}
