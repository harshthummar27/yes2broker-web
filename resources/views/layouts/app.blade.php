<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('site.name')) — {{ config('site.tagline') }}</title>
    <meta name="description" content="@yield('meta_description', 'Yes2Broker — India\'s first broking house offering ₹1,00,000 cashback on purchasing property on a woman\'s name. Find properties in Ahmedabad & Gandhinagar.')">
    <link rel="icon" href="{{ config('site.media_url') }}/2025/07/cropped-cropped-Y2B-Final-LOGO_page-0001-Photoroom-768x184-1-1-32x32.webp">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="antialiased text-gray-800 bg-white">
    @include('partials.flash-messages') 
    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <x-consultation-modal />

    @stack('scripts')
</body>
</html>
