<?php

namespace App\Http\Controllers;

use App\Data\HomePageData;
use App\Services\HomePromoService;
use App\Services\LookupOptionService;
use App\Services\PropertyService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly PropertyService $properties,
        private readonly HomePromoService $homePromos,
        private readonly LookupOptionService $lookupOptions,
    ) {}

    public function index(): View
    {
        $dreamPromoItems = $this->homePromos->forHome()->all();

        return view('pages.home', [
            'usps' => HomePageData::usps(),
            'trendingProperties' => $this->properties->trending(12),
            'dreamPromoItems' => $dreamPromoItems,
            'hasFormPromoBanner' => $this->homePromos->hasFormBanner($dreamPromoItems),
            'partners' => HomePageData::partners(),
            'locations' => HomePageData::locations(),
            'localities' => $this->properties->localitiesByArea(),
            'featuredCarousel' => $this->properties->featuredCarousel(3),
            'cities' => $this->lookupOptions->citiesForSearch(),
            'defaultCity' => $this->lookupOptions->defaultCitySlug(),
            'propertyTypes' => $this->lookupOptions->propertyTypesForSearch(),
            'budgets' => HomePageData::budgets(),
            'consultationOptions' => HomePageData::consultationOptions(),
        ]);
    }
}
