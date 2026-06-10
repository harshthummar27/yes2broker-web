@extends('layouts.app')

@section('title', ucwords(str_replace('-', ' ', $slug)))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-16">
    <h1 class="text-3xl font-bold mb-4">{{ ucwords(str_replace('-', ' ', $slug)) }}</h1>
    <p class="text-gray-600">Property detail page — slug: <code>{{ $slug }}</code></p>
    <p class="mt-8 text-amber-600">🚧 Property data will load from database in Phase 3</p>
</div>
@endsection
