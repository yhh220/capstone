{{-- Single source of truth for favicons / meta icons across the public site AND
     the Filament admin panel (injected there via PanelsRenderHook::HEAD_END), so
     every surface shows the same Win Win branding. Bump ?v= to flush caches. --}}
<meta name="theme-color" content="#C8413D" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0C0C0E" media="(prefers-color-scheme: dark)">
<link rel="icon" href="{{ asset('winwin-favicon.svg') }}?v=20260618" type="image/svg+xml">
<link rel="icon" href="{{ asset('winwin-favicon.svg') }}?v=20260618" sizes="any">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=20260618">
<link rel="apple-touch-icon" href="{{ asset('winwin-apple-touch-icon.png') }}?v=20260618">
<link rel="mask-icon" href="{{ asset('winwin-favicon.svg') }}?v=20260618" color="#C8413D">
<link rel="manifest" href="{{ asset('site.webmanifest') }}?v=20260618">
