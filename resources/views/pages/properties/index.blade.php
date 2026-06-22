@extends('layouts.app')

@section('title', 'All Properties')

@section('content')
{{-- Filter bar --}}
<section class="bg-y2b-primary py-6 md:py-8">
    <div class="max-w-7xl mx-auto px-4">
        <x-property-search
            :property-types="$propertyTypes"
            :budgets="$budgets"
            :selected="$filters" />
    </div>
</section>

<section class="py-8 md:py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-12 gap-8">
            {{-- Main list --}}
            <div class="lg:col-span-8 xl:col-span-9 min-w-0">
                {{-- Header --}}
                <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-y2b-primary">All Properties</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Showing <span class="font-semibold text-gray-700">{{ count($properties) }}</span>
                            of <span class="font-semibold text-gray-700">{{ $totalCount }}</span> total results
                        </p>
                    </div>

                    <form method="GET" action="{{ route('properties.index') }}" class="flex flex-wrap items-center gap-3 shrink-0">
                        @foreach($filters as $key => $value)
                            @if(! in_array($key, ['sort', 'possession_filter'], true))
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <div class="flex items-center gap-2">
                            <label for="possession_filter" class="text-sm text-gray-500 whitespace-nowrap">Filter by:</label>
                            <select id="possession_filter" name="possession_filter" onchange="this.form.submit()"
                                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                                @foreach($possessionFilterOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['possession_filter'] ?? 'all') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label for="sort" class="text-sm text-gray-500 whitespace-nowrap">Sort By:</label>
                            <select id="sort" name="sort" onchange="this.form.submit()"
                                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white outline-none focus:border-y2b-primary focus:ring-1 focus:ring-y2b-primary">
                                @foreach($sortOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['sort'] ?? 'relevance') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                @if(count($filters) > 0)
                    <div class="mb-5 flex flex-wrap items-center gap-2">
                        <span class="text-xs text-gray-500">Active filters applied</span>
                        <a href="{{ route('properties.index') }}"
                           class="text-xs font-semibold text-y2b-primary hover:text-y2b-accent transition">
                            Clear all filters
                        </a>
                    </div>
                @endif

                {{-- Property list --}}
                <div id="property-list" class="space-y-5">
                    @if($totalCount > 0)
                        @include('partials.property-list-items', [
                            'properties' => $properties,
                            'banners' => $promoBanners,
                            'startIndex' => 0,
                        ])
                    @endif
                </div>

                @if($totalCount === 0)
                    <div class="text-center py-16 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-gray-500 text-lg">No properties match your search criteria.</p>
                        <a href="{{ route('properties.index') }}"
                           class="inline-block mt-4 text-y2b-primary font-semibold hover:text-y2b-accent transition">
                            View all properties
                        </a>
                    </div>
                @elseif($currentPage < $totalPages)
                    <div class="load-more-wrap text-center mt-10">
                        <button type="button"
                                id="load-more-properties"
                                data-page="{{ $currentPage }}"
                                data-start-index="{{ count($properties) }}"
                                data-url="{{ route('properties.load-more') }}"
                                class="inline-flex items-center justify-center gap-2 bg-y2b-primary hover:bg-y2b-primary-dark text-white font-semibold px-8 py-3.5 rounded-lg transition disabled:opacity-60">
                            <span class="load-more-text">Load More Properties</span>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Sidebar — sticky while left list scrolls --}}
            <aside class="lg:col-span-4 xl:col-span-3 hidden lg:block">
                <div class="property-list-sidebar-sticky">
                    <x-property-list-sidebar />
                </div>
            </aside>
        </div>

        {{-- Mobile sidebar form --}}
        <div class="lg:hidden mt-10">
            <x-property-list-sidebar />
        </div>
    </div>
</section>
@endsection
