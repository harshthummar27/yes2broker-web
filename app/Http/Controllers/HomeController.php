<?php

namespace App\Http\Controllers;

use App\Data\HomePageData;
use App\Services\PropertyService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly PropertyService $properties
    ) {}

    public function index(): View
    {
        return view('pages.home', [
            'usps' => HomePageData::usps(),
            'trendingProperties' => $this->properties->trending(12),
            'partners' => HomePageData::partners(),
            'locations' => HomePageData::locations(),
            'localities' => $this->properties->localitiesByArea(),
            'featuredCarousel' => $this->properties->featuredCarousel(3),
            'propertyTypes' => HomePageData::propertyTypes(),
            'budgets' => HomePageData::budgets(),
            'consultationOptions' => HomePageData::consultationOptions(),
        ]);
    }
}
