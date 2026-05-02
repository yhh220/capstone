<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found — Win Win Car Audio</title>
    <link rel="icon" href="{{ asset('winwin-favicon.svg') }}?v=20260428" type="image/svg+xml">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=20260428">
    <link rel="icon" href="{{ asset('winwin-favicon-32x32.png') }}?v=20260428" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('winwin-apple-touch-icon.png') }}?v=20260428">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=20260428">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #09090b;
            --surface: #18181b;
            --border: #27272a;
            --rose: #f43f5e;
            --rose-dark: #be123c;
            --rose-glow: rgba(244, 63, 94, 0.15);
            --text: #fafafa;
            --muted: #a1a1aa;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Background glow blobs */
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

        /* Grid pattern overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
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

        /* Logo */
        .logo {
            margin-bottom: 2.5rem;
        }
        .logo img {
            height: 36px;
            opacity: 0.9;
        }

        /* Shield icon */
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

        /* Code badge */
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

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin-bottom: 2rem;
        }

        /* Buttons */
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
        .btn svg {
            width: 16px; height: 16px;
        }
    </style>
</head>
<body>
    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>

    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/logo/logo-light.svg') }}" alt="Win Win Car Audio">
        </div>

        <div class="icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <div class="code-badge">404 Not Found</div>

        <h1>Page Not Found</h1>
        <p>
            The page you are looking for doesn't exist or has been moved.
        </p>

        <hr class="divider">

        <div class="btn-group">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Go to Homepage
            </a>
        </div>
    </div>
</body>
</html>
