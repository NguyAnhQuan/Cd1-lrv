<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminTourController extends Controller
{
    public function index(): JsonResponse
    {
        $tours = Tour::query()->with('category')->orderByDesc('id')->get();

        return response()->json(['data' => $tours]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric'],
            'discount_price' => ['nullable', 'numeric'],
            'duration' => ['nullable', 'integer'],
            'departure_date' => ['nullable', 'date'],
            'start_location' => ['nullable', 'string', 'max:255'],
            'max_people' => ['nullable', 'integer'],
            'thumbnail' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'rating' => ['nullable', 'numeric'],
            'badge_label' => ['nullable', 'string', 'max:100'],
            'badge_variant' => ['nullable', 'string', 'max:50'],
            'meta_icon1' => ['nullable', 'string', 'max:50'],
            'meta_text1' => ['nullable', 'string', 'max:100'],
            'meta_icon2' => ['nullable', 'string', 'max:50'],
            'meta_text2' => ['nullable', 'string', 'max:100'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $tour = Tour::query()->create($data);

        return response()->json(['data' => $tour], 201);
    }

    public function update(Request $request, Tour $tour): JsonResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric'],
            'discount_price' => ['nullable', 'numeric'],
            'duration' => ['nullable', 'integer'],
            'departure_date' => ['nullable', 'date'],
            'start_location' => ['nullable', 'string', 'max:255'],
            'max_people' => ['nullable', 'integer'],
            'thumbnail' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'rating' => ['nullable', 'numeric'],
            'badge_label' => ['nullable', 'string', 'max:100'],
            'badge_variant' => ['nullable', 'string', 'max:50'],
            'meta_icon1' => ['nullable', 'string', 'max:50'],
            'meta_text1' => ['nullable', 'string', 'max:100'],
            'meta_icon2' => ['nullable', 'string', 'max:50'],
            'meta_text2' => ['nullable', 'string', 'max:100'],
        ]);

        $tour->fill($data)->save();

        return response()->json(['data' => $tour->fresh()]);
    }

    public function destroy(Tour $tour): JsonResponse
    {
        $tour->delete();

        return response()->json(['ok' => true]);
    }
}

