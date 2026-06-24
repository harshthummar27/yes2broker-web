<?php

namespace App\Http\Controllers;

use App\Data\HomePageData;
use App\Services\HomePromoService;
use App\Services\LookupOptionService;
use App\Services\PropertyService;
use App\Support\PossessionFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct(
        private readonly PropertyService $properties,
        private readonly HomePromoService $homePromos,
        private readonly LookupOptionService $lookupOptions,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);
        $result = $this->properties->paginate($filters, 1);
        $promoItems = $this->homePromos->forPropertiesList()->all();

        return view('pages.properties.index', [
            'properties' => $result['properties'],
            'filters' => $filters,
            'currentPage' => $result['currentPage'],
            'totalPages' => $result['totalPages'],
            'totalCount' => $result['totalCount'],
            'cities' => $this->lookupOptions->citiesForSearch(),
            'defaultCity' => $this->lookupOptions->defaultCitySlug(),
            'propertyTypes' => $this->lookupOptions->propertyTypesForSearch(),
            'budgets' => HomePageData::budgets(),
            'sortOptions' => HomePageData::sortOptions(),
            'possessionFilterOptions' => PossessionFilter::options(),
            'promoItems' => $promoItems,
            'hasFormPromoBanner' => $this->homePromos->hasFormBanner($promoItems),
        ]);
    }

    public function show(string $slug): View
    {
        $record = $this->properties->findActiveBySlug($slug);
        abort_if($record === null, 404);

        return view('pages.properties.show', [
            'property' => $record->toDetailArray(),
            'relatedProperties' => $this->properties->related($record),
        ]);
    }

    public function loadMore(Request $request): JsonResponse
    {
        $filters = $this->filtersFromRequest($request);
        $page = max(1, (int) $request->query('page', 2));
        $startIndex = max(0, (int) $request->query('start_index', 0));
        $result = $this->properties->paginate($filters, $page);

        $promoItems = $this->homePromos->forPropertiesList()->all();

        return response()->json([
            'html' => view('partials.property-list-items', [
                'properties' => $result['properties'],
                'promos' => $promoItems,
                'startIndex' => $startIndex,
            ])->render(),
            'page' => $result['currentPage'],
            'hasMore' => $result['hasMore'],
            'nextStartIndex' => $startIndex + count($result['properties']),
        ]);
    }

    private function filtersFromRequest(Request $request): array
    {
        return array_filter([
            'city' => $request->query('city'),
            'area' => $request->query('area'),
            'type' => $request->query('type'),
            'budget' => $request->query('budget'),
            'possession_filter' => $request->query('possession_filter'),
            'sort' => $request->query('sort'),
        ], fn ($value) => $value !== null && $value !== '' && ! in_array($value, ['relevance', 'all'], true));
    }
}
