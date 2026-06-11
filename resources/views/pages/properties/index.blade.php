@extends('layouts.app')

@section('title', 'All Properties')

@section('content')
<x-page-banner title="All Properties" />

<section class="py-12 md:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        {{-- Search (always visible) --}}
        <div class="mb-10">
            <x-property-search
                :property-types="$propertyTypes"
                :budgets="$budgets"
                :selected="$filters" />
        </div>

        {{-- Results header --}}
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                @if(count($filters) > 0)
                    <p class="text-sm text-gray-500">Search results</p>
                @else
                    <p class="text-sm text-gray-500">Browse all listings</p>
                @endif
                <p class="font-semibold text-y2b-primary text-lg">
                    {{ $totalCount }} {{ Str::plural('property', $totalCount) }}
                    @if(count($filters) > 0)
                        found
                    @endif
                </p>
            </div>
            @if(count($filters) > 0)
                <a href="{{ route('properties.index') }}"
                   class="text-sm font-semibold text-y2b-primary hover:text-y2b-accent transition">
                    Clear filters
                </a>
            @endif
        </div>

        {{-- Property grid from database --}}
        <div id="property-grid"
             class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @include('partials.property-grid-items', ['properties' => $properties])
        </div>

        @if($totalCount === 0)
            <div class="text-center py-16 bg-white rounded-xl shadow-sm">
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
