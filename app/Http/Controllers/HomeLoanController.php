<?php

namespace App\Http\Controllers;

use App\Data\HomeLoanPageData;
use Illuminate\View\View;

class HomeLoanController extends Controller
{
    public function index(): View
    {
        return view('pages.home-loan', [
            'howItWorks' => HomeLoanPageData::howItWorks(),
            'bankPartners' => HomeLoanPageData::bankPartners(),
        ]);
    }
}
