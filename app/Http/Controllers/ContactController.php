<?php

namespace App\Http\Controllers;

use App\Data\ContactPageData;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact', [
            'consultationOptions' => ContactPageData::consultationOptions(),
        ]);
    }
}
