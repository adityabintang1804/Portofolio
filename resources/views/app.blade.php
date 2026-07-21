<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php
            $seoSettings = \Illuminate\Support\Facades\Schema::hasTable('site_settings')
                ? \App\Models\SiteSetting::query()->pluck('value', 'key')
                : collect();
            $routeProject = request()->route('project');
            $projectSlug = $routeProject instanceof \App\Models\Project ? $routeProject->slug : $routeProject;
            $seoProject = request()->routeIs('projects.show')
                ? \App\Models\Project::query()->published()->where('slug', $projectSlug)->first()
                : null;
            $seoTitle = $seoProject?->seo_title ?: $seoProject?->title ?: $seoSettings->get('default_meta_title', config('app.name'));
            $seoDescription = $seoProject?->seo_description ?: $seoProject?->short_description ?: $seoSettings->get('default_meta_description', $seoSettings->get('site_description', ''));
            $seoImagePath = $seoProject?->og_image ?: $seoProject?->thumbnail ?: $seoSettings->get('default_og_image');
            $seoImage = $seoImagePath ? url(\Illuminate\Support\Facades\Storage::url($seoImagePath)) : null;
        @endphp
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <meta name="color-scheme" content="dark light">
        @unless(request()->routeIs('admin.*'))
            <meta name="description" content="{{ $seoDescription }}">
            <link rel="canonical" href="{{ url()->current() }}">
            <meta name="robots" content="index, follow">
            <meta property="og:type" content="{{ $seoProject ? 'article' : 'website' }}">
            <meta property="og:title" content="{{ $seoTitle }}">
            <meta property="og:description" content="{{ $seoDescription }}">
            <meta property="og:url" content="{{ url()->current() }}">
            <meta property="og:site_name" content="{{ $seoSettings->get('site_name', config('app.name')) }}">
            @if($seoImage)<meta property="og:image" content="{{ $seoImage }}">@endif
            <meta name="twitter:card" content="{{ $seoImage ? 'summary_large_image' : 'summary' }}">
            <meta name="twitter:title" content="{{ $seoTitle }}">
            <meta name="twitter:description" content="{{ $seoDescription }}">
            @if($seoImage)<meta name="twitter:image" content="{{ $seoImage }}">@endif
        @endunless

        <script>
            (() => {
                const storedTheme = localStorage.getItem('portfolio-theme') ?? 'system';
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.add(
                    storedTheme === 'dark' || (storedTheme === 'system' && prefersDark) ? 'dark' : 'light'
                );
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|manrope:500,600,700,800|jetbrains-mono:400,500&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.tsx', "resources/js/Pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <body class="bg-background font-sans text-foreground antialiased">
        @inertia
    </body>
</html>
