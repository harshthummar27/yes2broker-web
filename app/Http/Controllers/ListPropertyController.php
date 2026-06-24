<?php

namespace App\Http\Controllers;

use App\Data\ListPropertyPageData;
use App\Services\LookupOptionService;
use Illuminate\View\View;

class ListPropertyController extends Controller
{
    public function __construct(
        private readonly LookupOptionService $lookupOptions,
    ) {}

    public function index(): View
    {
        return view('pages.list-property', [
            'propertyTypes' => $this->lookupOptions->propertyTypeNames(),
            'listingStatuses' => ListPropertyPageData::listingStatuses(),
            'ownerTypes' => ListPropertyPageData::ownerTypes(),
            'listableTypes' => ListPropertyPageData::listableTypes(),
        ]);
    }
}
