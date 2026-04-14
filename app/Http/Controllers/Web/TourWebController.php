<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\TourApiController;
use App\Http\Controllers\Controller;
use App\Support\Web\TourCatalogFilters;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TourWebController extends Controller
{
    private const PER_PAGE = 10;

    public function catalogDomestic(Request $request): View
    {
        return $this->catalog($request, 'domestic', 'Du lịch trong nước', 1);
    }

    public function catalogInternational(Request $request): View
    {
        return $this->catalog($request, 'international', 'Du lịch nước ngoài', 2);
    }

    private function catalog(Request $request, string $scope, string $title, int $menuIndex): View
    {
        $apiReq = Request::create('/tours', 'GET', [
            'scope' => $scope,
            'departure_date' => $request->query('departure_date'),
        ]);
        $json = app(TourApiController::class)->index($apiReq);
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json->getContent(), true) ?? [];
        /** @var list<array<string, mixed>> $tours */
        $tours = $decoded['data'] ?? [];

        $filterQuery = [
            'q' => $request->query('q', ''),
            'departure_date' => $request->query('departure_date', ''),
            'price_0' => $request->query('price_0'),
            'price_1' => $request->query('price_1'),
            'price_2' => $request->query('price_2'),
            'duration' => $request->query('duration'),
            'sort' => $request->query('sort', 0),
        ];
        $filtered = TourCatalogFilters::apply($tours, $filterQuery);
        $page = max(1, (int) $request->query('page', 1));
        $paginated = TourCatalogFilters::paginate($filtered, $page, self::PER_PAGE);

        return view('web.tours.catalog', [
            'scope' => $scope,
            'title' => $title,
            'menuIndex' => $menuIndex,
            'tours' => $paginated['items'],
            'pagination' => $paginated,
            'filterQuery' => $filterQuery + ['page' => $page],
        ]);
    }

    public function show(string $slug): View|Response
    {
        $response = app(TourApiController::class)->show($slug);
        if ($response->getStatusCode() === 404) {
            abort(404);
        }
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($response->getContent(), true) ?? [];
        $tour = $decoded['data'] ?? null;
        if (! is_array($tour)) {
            abort(404);
        }

        return view('web.tours.show', ['tour' => $tour]);
    }
}
