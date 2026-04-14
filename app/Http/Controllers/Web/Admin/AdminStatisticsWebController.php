<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Api\Admin\AdminReportController;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminStatisticsWebController extends Controller
{
    public function index(): View
    {
        $json = app(AdminReportController::class)->statistics();
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json->getContent(), true) ?? [];

        return view('admin.statistics', [
            'payload' => $decoded['data'] ?? [],
        ]);
    }
}
