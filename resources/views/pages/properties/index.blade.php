@extends('layouts.app')

@section('title', 'All Properties')

@section('content')
<x-page-banner title="All Properties" />

<section class="py-12 md:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        @if(count($filters) > 0)
            <div class="mb-8 bg-white rounded-xl shadow-sm p-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500">Search results</p>
                    <p class="font-semibold text-y2b-primary">
                        {{ $totalCount }} {{ Str::plural('property', $totalCount) }} found
                    </p>
                </div>
                <a href="{{ route('properties.index') }}"
                   class="text-sm font-semibold text-y2b-primary hover:text-y2b-accent transition">
                    Clear filters
                </a>
            </div>

            <div class="mb-10">
                <x-property-search :property-types="$propertyTypes" :budgets="$budgets" />
            </div>
        @endif

        <div id="property-grid"
             class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @include('partials.property-grid-items', ['properties' => $properties])
        </div>

        @if($totalCount === 0)
            <div class="text-center py-16">
                <p class="text-gray-500 text-lg">No properties match your search criteria.</p>
                <a href="{{ route('properties.index') }}"
                   class="inline-block mt-4 text-y2b-primary font-semibold hover:text-y2b-accent transition">
                    View all properties
                </a>
            </div>
        @elseif($currentPage < $totalPages)
            <div class="load-more-wrap text-center mt-12">
                <button type="button"
                        id="load-more-properties"
                        data-page="{{ $currentPage }}"
                        data-url="{{ route('properties.load-more') }}"
                        class="inline-flex items-center justify-center gap-2 bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-8 py-3.5 rounded transition disabled:opacity-60">
                    <span class="load-more-text">Load More Properties</span>
                </button>
            </div>
        @endif
    </div>
</section>
@endsection
