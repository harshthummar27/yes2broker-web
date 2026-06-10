<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChannelPartnerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeLoanController;
use App\Http\Controllers\ListPropertyController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Yes2Broker Public Routes
|--------------------------------------------------------------------------
| Mirrors WordPress URL structure from yes2broker.in
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [AboutController::class, 'index'])->name('about');
Route::get('/all-properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/all-properties/load-more', [PropertyController::class, 'loadMore'])->name('properties.load-more');
Route::get('/property/{slug}', fn (string $slug) => view('pages.properties.show', compact('slug')))->name('properties.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/list-your-property', [ListPropertyController::class, 'index'])->name('list-property');
Route::get('/become-channel-partner', [ChannelPartnerController::class, 'index'])->name('channel-partner');
Route::get('/home-loan', [HomeLoanController::class, 'index'])->name('home-loan');
