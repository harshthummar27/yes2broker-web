<?php

namespace App\Http\Controllers;

use App\Data\HomePageData;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('pages.home', [
            'usps' => HomePageData::usps(),
            'trendingProperties' => HomePageData::trendingProperties(),
            'partners' => HomePageData::partners(),
            'locations' => HomePageData::locations(),
            'localities' => HomePageData::localities(),
            'featuredCarousel' => HomePageData::featuredCarousel(),
            'propertyTypes' => HomePageData::propertyTypes(),
            'budgets' => HomePageData::budgets(),
            'consultationOptions' => HomePageData::consultationOptions(),
        ]);
    }
}
