<?php

namespace App\Http\Controllers;

use App\Data\HomePageData;
use App\Data\PropertiesPageData;
use App\Data\PropertyDetailData;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    private const PER_PAGE = 30;

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);

        if ($this->usesDatabase()) {
            $query = Property::query()->active()->filtered($filters)->orderBy('title');
            $totalCount = (clone $query)->count();
            $properties = $query->limit(self::PER_PAGE)->get()->map->toCardArray()->all();
            $totalPages = max(1, (int) ceil($totalCount / self::PER_PAGE));
        } else {
            $properties = PropertiesPageData::paginate(1, $filters);
            $totalCount = PropertiesPageData::totalCount($filters);
            $totalPages = PropertiesPageData::totalPages($filters);
        }

        return view('pages.properties.index', [
            'properties' => $properties,
            'filters' => $filters,
            'currentPage' => 1,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'propertyTypes' => HomePageData::propertyTypes(),
            'budgets' => HomePageData::budgets(),
        ]);
    }

    public function show(string $slug): View
    {
        if ($this->usesDatabase()) {
            $record = Property::query()->active()->where('slug', $slug)->first();
            abort_if($record === null, 404);

            $property = $record->toDetailArray();
            $relatedProperties = $this->relatedFromDatabase($record);
        } else {
            $property = PropertyDetailData::findBySlug($slug);
            abort_if($property === null, 404);
            $relatedProperties = PropertyDetailData::related($slug);
        }

        return view('pages.properties.show', [
            'property' => $property,
            'relatedProperties' => $relatedProperties,
        ]);
    }

    public function loadMore(Request $request): JsonResponse
    {
        $filters = $this->filtersFromRequest($request);
        $page = max(1, (int) $request->query('page', 2));

        if ($this->usesDatabase()) {
            $query = Property::query()->active()->filtered($filters)->orderBy('title');
            $totalPages = max(1, (int) ceil((clone $query)->count() / self::PER_PAGE));
            $properties = $query
                ->offset(($page - 1) * self::PER_PAGE)
                ->limit(self::PER_PAGE)
                ->get()
                ->map->toCardArray()
                ->all();
            $hasMore = $page < $totalPages;
        } else {
            $properties = PropertiesPageData::paginate($page, $filters);
            $hasMore = $page < PropertiesPageData::totalPages($filters);
        }

        return response()->json([
            'html' => view('partials.property-grid-items', compact('properties'))->render(),
            'page' => $page,
            'hasMore' => $hasMore,
        ]);
    }

    private function usesDatabase(): bool
    {
        return Property::query()->exists();
    }

    private function relatedFromDatabase(Property $property): array
    {
        $locationNeedle = strtolower(explode(',', $property->location)[0]);

        $related = Property::query()
            ->active()
            ->where('id', '!=', $property->id)
            ->whereRaw('LOWER(location) LIKE ?', ["%{$locationNeedle}%"])
            ->limit(3)
            ->get();

        if ($related->count() < 3) {
            $existingIds = $related->pluck('id')->push($property->id)->all();
            $more = Property::query()
                ->active()
                ->whereNotIn('id', $existingIds)
                ->limit(3 - $related->count())
                ->get();

            $related = $related->concat($more);
        }

        return $related->map->toCardArray()->all();
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
