<?php

namespace App\Http\Controllers;

use App\Data\ListPropertyPageData;
use Illuminate\View\View;

class ListPropertyController extends Controller
{
    public function index(): View
    {
        return view('pages.list-property', [
            'propertyTypes' => ListPropertyPageData::propertyTypes(),
            'listingStatuses' => ListPropertyPageData::listingStatuses(),
            'ownerTypes' => ListPropertyPageData::ownerTypes(),
            'listableTypes' => ListPropertyPageData::listableTypes(),
        ]);
    }
}
