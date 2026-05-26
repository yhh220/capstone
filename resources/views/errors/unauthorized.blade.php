<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Access Denied') }} - {{ config('services.store.name') }}</title>
    <link rel="icon" href="{{ asset('winwin-favicon.svg') }}?v=20260428" type="image/svg+xml">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=20260428">
    <link rel="icon" href="{{ asset('winwin-favicon-32x32.png') }}?v=20260428" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('winwin-apple-touch-icon.png') }}?v=20260428">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=20260428">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            var dark = t === 'dark' || (!t || t === 'system') && window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.dataset.theme = dark ? 'dark' : 'light';
        })();
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #f4f4f5;
            --surface: #ffffff;
            --border: #e4e4e7;
            --rose: #f43f5e;
            --rose-dark: #be123c;
            --rose-glow: rgba(244, 63, 94, 0.15);
            --text: #18181b;
            --muted: #52525b;
            color-scheme: light;
        }

        html[data-theme="dark"] {
            --bg: #09090b;
            --surface: #18181b;
            --border: #27272a;
            --text: #fafafa;
            --muted: #a1a1aa;
            color-scheme: dark;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            padding: 1rem;
        }

        .bg-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            z-index: 0;
        }
        .bg-blob-1 {
            width: 500px; height: 500px;
            background: rgba(244, 63, 94, 0.08);
            top: -100px; left: -100px;
            animation: float1 8s ease-in-out infinite;
        }
        .bg-blob-2 {
            width: 400px; height: 400px;
            background: rgba(244, 63, 94, 0.05);
            bottom: -80px; right: -80px;
            animation: float2 10s ease-in-out infinite;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, 20px); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-20px, -30px); }
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(24,24,27,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(24,24,27,.04) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
        }
        html[data-theme="dark"] body::before {
            background-image:
                linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px);
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            max-width: 520px;
            width: 100%;
            animation: fadeUp 0.6s cubic-bezier(.16,1,.3,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo {
            margin-bottom: 2.5rem;
        }
        .logo img {
            height: 36px;
            opacity: 0.9;
            margin: 0 auto;
        }
        .logo-light {
            display: none;
        }
        html[data-theme="dark"] .logo-dark {
            display: none;
        }
        html[data-theme="dark"] .logo-light {
            display: block;
        }

        .icon-wrap {
            width: 80px; height: 80px;
            background: var(--rose-glow);
            border: 1px solid rgba(244, 63, 94, 0.25);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: pulse 2.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(244,63,94,0.2); }
            50%       { box-shadow: 0 0 0 12px rgba(244,63,94,0); }
        }
        .icon-wrap svg {
            width: 36px; height: 36px;
            color: var(--rose);
        }

        .code-badge {
            display: inline-block;
            background: var(--rose-glow);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: var(--rose);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            margin-bottom: 1rem;
        }
        html:not([lang^="en"]) .code-badge {
            letter-spacing: 0;
            text-transform: none;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
            line-height: 1.2;
        }

        p {
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        p strong {
            color: var(--text);
            font-weight: 500;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin-bottom: 2rem;
        }

        /* User info card */
        .user-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            margin-bottom: 1.5rem;
            text-align: left;
            min-width: 0;
            flex-wrap: wrap;
        }
        .user-avatar {
            width: 40px; height: 40px;
            background: var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--muted);
            flex-shrink: 0;
        }
        .user-info small {
            display: block;
            font-size: 0.75rem;
            color: var(--muted);
        }
        .user-info span {
            font-size: 0.875rem;
            font-weight: 500;
            overflow-wrap: anywhere;
        }
        .user-role {
            margin-left: auto;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--muted);
            background: var(--border);
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
        }

        .btn-group {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            justify-content: center;
            white-space: normal;
            overflow-wrap: anywhere;
        }
        .btn-primary {
            background: var(--rose);
            color: white;
        }
        .btn-primary:hover {
            background: var(--rose-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(244, 63, 94, 0.3);
        }
        .btn-secondary {
            background: var(--surface);
            color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover {
            background: var(--border);
            transform: translateY(-1px);
        }
        .btn svg {
            width: 16px; height: 16px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>

    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/logo/logo-dark.svg') }}" alt="{{ config('services.store.name') }}" class="logo-dark">
            <img src="{{ asset('images/logo/logo-light.svg') }}" alt="{{ config('services.store.name') }}" class="logo-light">
        </div>

        <div class="icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        </div>

        <div class="code-badge">403 {{ __('Unauthorized') }}</div>

        <h1>{{ __('Access Denied') }}</h1>
        <p>
            {{ __('Your account does not have permission to access the admin panel.') }}
            {{ __('Only') }} <strong>{{ __('staff, admin, and owner') }}</strong> {{ __('accounts are allowed.') }}
        </p>

        <hr class="divider">

        @auth
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <small>{{ __('Signed in as') }}</small>
                <span>{{ auth()->user()->name }}</span>
            </div>
            <div class="user-role">{{ ucfirst(auth()->user()->role ?? 'client') }}</div>
        </div>
        @endauth

        <div class="btn-group">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                {{ __('Go to Homepage') }}
            </a>
            @auth
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                    </svg>
                    {{ __('Sign Out') }}
                </button>
            </form>
            @endauth
        </div>
    </div>
</body>
</html>
