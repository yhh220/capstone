<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Not Found - {{ config('services.store.seo_name') }}</title>
    <link rel="icon" href="{{ asset('winwin-favicon.svg') }}?v=20260613" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        (function () {
            var t = localStorage.getItem('site-theme');
            var dark = t === 'dark' || (!t || t === 'system') && window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.dataset.theme = dark ? 'dark' : 'light';
        })();
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg:#f4f4f5; --surface:#ffffff; --border:#e4e4e7; --rose:#f43f5e; --text:#18181b; --muted:#52525b; color-scheme: light; }
        html[data-theme="dark"] { --bg:#09090b; --surface:#18181b; --border:#27272a; --text:#fafafa; --muted:#a1a1aa; color-scheme: dark; }
        body { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem; }
        .card { background:var(--surface); border:1px solid var(--border); border-radius:1.25rem; padding:2.5rem 2rem; max-width:460px; width:100%; text-align:center; box-shadow:0 10px 40px rgba(0,0,0,.06); }
        .icon { width:56px; height:56px; margin:0 auto 1.25rem; border-radius:50%; background:rgba(244,63,94,.12); color:var(--rose); display:flex; align-items:center; justify-content:center; }
        h1 { font-size:1.35rem; font-weight:700; margin-bottom:.5rem; }
        p { color:var(--muted); font-size:.925rem; line-height:1.6; margin-bottom:1.75rem; }
        .actions { display:flex; flex-direction:column; gap:.6rem; }
        @media (min-width:480px) { .actions { flex-direction:row; justify-content:center; } }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; padding:.7rem 1.4rem; border-radius:.75rem; font-size:.875rem; font-weight:700; text-decoration:none; transition:transform .15s ease; }
        .btn:active { transform:scale(.96); }
        .btn-primary { background:#C8413D; color:#fff; }
        .btn-secondary { border:1px solid var(--border); color:var(--text); }
    </style>
</head>
<body>
    {{-- Reached when an admin follows a link to a record that no longer exists —
         most commonly the "View order" button in an alert email after the order
         was deleted or the database was reset. The email simply outlived the
         record; explain that instead of a bare 404. --}}
    <div class="card">
        <div class="icon">
            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M8 11h6"/></svg>
        </div>
        <h1>Record Not Found</h1>
        <p>This record no longer exists — it may have been deleted, or the link came from an email older than the current data. Nothing is broken; the link has simply outlived the record.</p>
        <div class="actions">
            <a class="btn btn-primary" href="{{ url('/admin/orders') }}">Go to Orders</a>
            <a class="btn btn-secondary" href="{{ url('/admin') }}">Dashboard</a>
        </div>
    </div>
</body>
</html>
