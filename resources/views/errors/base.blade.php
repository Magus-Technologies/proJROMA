<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — {{ config('app.name', 'Sistema') }}</title>
    <style>
        :root {
            --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --muted: #64748b;
            --border: #e2e8f0; --accent: #3b82f6; --accent-soft: #eff6ff;
            --detail-bg: #f1f5f9;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0f172a; --card: #1e293b; --text: #e2e8f0; --muted: #94a3b8;
                --border: #334155; --accent: #60a5fa; --accent-soft: #1e3a5f;
                --detail-bg: #0f172a;
            }
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg); color: var(--text);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }
        .card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 20px; padding: 40px; max-width: 560px; width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,.08); text-align: center;
        }
        .icon {
            width: 72px; height: 72px; margin: 0 auto 20px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; background: var(--accent-soft); font-size: 34px;
        }
        .code { font-size: 13px; font-weight: 700; letter-spacing: 2px; color: var(--accent); text-transform: uppercase; }
        h1 { font-size: 24px; font-weight: 700; margin: 8px 0 12px; }
        p.msg { font-size: 15px; color: var(--muted); line-height: 1.6; margin-bottom: 24px; }
        .actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 11px 20px; border-radius: 12px; font-size: 14px; font-weight: 600;
            text-decoration: none; cursor: pointer; border: 1px solid transparent; transition: opacity .15s;
        }
        .btn:hover { opacity: .88; }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-ghost { background: transparent; color: var(--muted); border-color: var(--border); }
        .detail {
            margin-top: 24px; text-align: left; border-top: 1px solid var(--border); padding-top: 20px;
        }
        .detail summary {
            cursor: pointer; font-size: 13px; font-weight: 600; color: var(--muted);
            list-style: none; user-select: none;
        }
        .detail summary::-webkit-details-marker { display: none; }
        .detail summary::before { content: '▸ '; }
        .detail[open] summary::before { content: '▾ '; }
        .detail pre {
            margin-top: 12px; background: var(--detail-bg); border: 1px solid var(--border);
            border-radius: 10px; padding: 14px; font-size: 12.5px; line-height: 1.5;
            color: var(--text); overflow-x: auto; white-space: pre-wrap; word-break: break-word;
            font-family: 'Consolas', 'Monaco', monospace;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">@yield('emoji', '⚠️')</div>
        <div class="code">Error @yield('code')</div>
        <h1>@yield('title')</h1>
        <p class="msg">@yield('message')</p>

        <div class="actions">
            <a href="{{ url('/panel') }}" class="btn btn-primary">Ir al inicio</a>
            <a href="javascript:history.back()" class="btn btn-ghost">← Volver atrás</a>
        </div>

        @hasSection('detail')
            <details class="detail">
                <summary>Detalle técnico (para soporte)</summary>
                <pre>@yield('detail')</pre>
            </details>
        @endif
    </div>
</body>
</html>
