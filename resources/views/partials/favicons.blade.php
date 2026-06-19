{{-- Single source of truth for favicons / meta icons across the public site AND
     the Filament admin panel (injected there via PanelsRenderHook::HEAD_END), so
     every surface shows the same Win Win branding. The ?v= cache-buster is the
     icon file's modified time, so it changes automatically whenever an icon is
     replaced — no manual bumping, and browsers re-fetch instead of showing a
     stale (e.g. old Filament default) favicon. --}}
@php
    // Cache-buster = newest mtime across all icon files, so replacing ANY icon
    // (not just the SVG) forces browsers to re-fetch instead of showing a stale one.
    $iconV = collect(['winwin-favicon.svg', 'favicon.ico', 'winwin-apple-touch-icon.png', 'winwin-favicon-32x32.png'])
        ->map(fn ($f) => @filemtime(public_path($f)) ?: 0)
        ->max() ?: '1';
@endphp
<meta name="theme-color" content="#C8413D" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0C0C0E" media="(prefers-color-scheme: dark)">
<link rel="icon" href="{{ asset('winwin-favicon.svg') }}?v={{ $iconV }}" type="image/svg+xml">
<link rel="icon" href="{{ asset('winwin-favicon.svg') }}?v={{ $iconV }}" sizes="any">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ $iconV }}">
<link rel="apple-touch-icon" href="{{ asset('winwin-apple-touch-icon.png') }}?v={{ $iconV }}">
<link rel="mask-icon" href="{{ asset('winwin-favicon.svg') }}?v={{ $iconV }}" color="#C8413D">
<link rel="manifest" href="{{ asset('site.webmanifest') }}?v={{ $iconV }}">
