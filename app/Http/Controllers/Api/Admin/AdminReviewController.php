<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = Review::query()
            ->with(['user:id,name,email', 'tour:id,name,slug'])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return response()->json(['data' => $rows]);
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        $data = $request->validate([
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $review->fill($data)->save();

        return response()->json(['data' => $review->fresh(['user', 'tour'])]);
    }
}

