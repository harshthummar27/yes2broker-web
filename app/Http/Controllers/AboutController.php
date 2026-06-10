<?php

namespace App\Http\Controllers;

use App\Data\AboutPageData;
use App\Data\HomePageData;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        return view('pages.about', [
            'usps' => HomePageData::usps(),
            'highlights' => AboutPageData::highlights(),
            'services' => AboutPageData::services(),
            'howItWorks' => AboutPageData::howItWorks(),
            'team' => AboutPageData::team(),
            'partners' => HomePageData::partners(),
            'locations' => HomePageData::locations(),
        ]);
    }
}
