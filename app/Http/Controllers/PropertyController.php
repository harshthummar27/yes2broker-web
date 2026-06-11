<?php

namespace App\Http\Controllers;

use App\Data\HomePageData;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct(
        private readonly PropertyService $properties
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);
        $result = $this->properties->paginate($filters, 1);

        return view('pages.properties.index', [
            'properties' => $result['properties'],
            'filters' => $filters,
            'currentPage' => $result['currentPage'],
            'totalPages' => $result['totalPages'],
            'totalCount' => $result['totalCount'],
            'propertyTypes' => HomePageData::propertyTypes(),
            'budgets' => HomePageData::budgets(),
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
        $result = $this->properties->paginate($filters, $page);

        return response()->json([
            'html' => view('partials.property-grid-items', ['properties' => $result['properties']])->render(),
            'page' => $result['currentPage'],
            'hasMore' => $result['hasMore'],
        ]);
    }

    private function filtersFromRequest(Request $request): array
    {
        return array_filter([
            'city' => $request->query('city'),
            'area' => $request->query('area'),
            'type' => $request->query('type'),
            'budget' => $request->query('budget'),
        ], fn ($value) => $value !== null && $value !== '');
    }
}
