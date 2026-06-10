<?php

namespace App\Http\Controllers;

use App\Data\HomePageData;
use App\Data\PropertiesPageData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);
        $properties = PropertiesPageData::paginate(1, $filters);

        return view('pages.properties.index', [
            'properties' => $properties,
            'filters' => $filters,
            'currentPage' => 1,
            'totalPages' => PropertiesPageData::totalPages($filters),
            'totalCount' => PropertiesPageData::totalCount($filters),
            'propertyTypes' => HomePageData::propertyTypes(),
            'budgets' => HomePageData::budgets(),
        ]);
    }

    public function loadMore(Request $request): JsonResponse
    {
        $filters = $this->filtersFromRequest($request);
        $page = max(1, (int) $request->query('page', 2));
        $properties = PropertiesPageData::paginate($page, $filters);

        return response()->json([
            'html' => view('partials.property-grid-items', compact('properties'))->render(),
            'page' => $page,
            'hasMore' => $page < PropertiesPageData::totalPages($filters),
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
