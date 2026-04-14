<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeWebController extends Controller
{
    public function index(): View
    {
        $response = app(HomeApiController::class)->index();
        /** @var array<string, mixed> $payload */
        $payload = json_decode($response->getContent(), true) ?? [];

        return view('web.home', [
            'home' => $payload,
        ]);
    }
}
