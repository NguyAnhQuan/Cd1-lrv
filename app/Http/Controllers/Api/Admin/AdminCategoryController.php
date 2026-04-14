<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class AdminCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = Category::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'status']);

        return response()->json(['data' => $rows]);
    }
}
