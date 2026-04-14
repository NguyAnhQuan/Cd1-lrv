<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminTourController;
use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTourWebController extends Controller
{
    public function index(): View
    {
        $json = app(AdminTourController::class)->index();
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json->getContent(), true) ?? [];
        /** @var list<array<string, mixed>> $tours */
        $tours = $decoded['data'] ?? [];

        return view('admin.tours.index', ['tours' => $tours]);
    }

    public function create(): View
    {
        $catJson = app(AdminCategoryController::class)->index();
        $decoded = json_decode($catJson->getContent(), true) ?? [];
        $categories = $decoded['data'] ?? [];

        return view('admin.tours.form', [
            'tour' => null,
            'categories' => $categories,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $json = app(AdminTourController::class)->store($request);
        if ($json->getStatusCode() >= 400) {
            $body = json_decode($json->getContent(), true);

            return back()->withErrors(['message' => $body['message'] ?? 'Lỗi lưu tour'])->withInput();
        }

        return redirect()->route('admin.tours.index')->with('success', 'Đã tạo tour.');
    }

    public function edit(Tour $tour): View
    {
        $catJson = app(AdminCategoryController::class)->index();
        $decoded = json_decode($catJson->getContent(), true) ?? [];
        $categories = $decoded['data'] ?? [];

        return view('admin.tours.form', [
            'tour' => $tour,
            'categories' => $categories,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Tour $tour): RedirectResponse
    {
        $json = app(AdminTourController::class)->update($request, $tour);
        if ($json->getStatusCode() >= 400) {
            $body = json_decode($json->getContent(), true);

            return back()->withErrors(['message' => $body['message'] ?? 'Lỗi cập nhật'])->withInput();
        }

        return redirect()->route('admin.tours.index')->with('success', 'Đã cập nhật tour.');
    }

    public function destroy(Request $request, Tour $tour): RedirectResponse
    {
        $json = app(AdminTourController::class)->destroy($tour);
        if ($json->getStatusCode() >= 400) {
            return back()->with('error', 'Không xóa được tour.');
        }

        return redirect()->route('admin.tours.index')->with('success', 'Đã xóa tour.');
    }
}
