<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Consulta de comprobante · {{ $empresa->comercial ?? $empresa->razon_social ?? 'Comprobante' }}</title>
<link rel="icon" href="{{ asset('logos/logo.svg') }}">

<style>
:root{
    --primary:#3b82f6;
    --primary-600:#2563eb;
    --primary-700:#1d4ed8;

    --bg:#f9fafb;
    --card:#ffffff;
    --text:#111827;
    --text-2:#6b7280;
    --text-3:#9ca3af;
    --border:#e5e7eb;
    --input-bg:#ffffff;

    --success-bg:#dcfce7; --success-fg:#166534;
    --danger-bg:#fee2e2;  --danger-fg:#991b1b;
    --warning-bg:#fef3c7; --warning-fg:#92400e;
}
@media (prefers-color-scheme:dark){
    :root{
        --bg:#0f172a;
        --card:#1f2937;
        --text:#f9fafb;
        --text-2:#9ca3af;
        --text-3:#6b7280;
        --border:rgba(255,255,255,.1);
        --input-bg:rgba(255,255,255,.05);

        --success-bg:rgba(34,197,94,.15); --success-fg:#4ade80;
        --danger-bg:rgba(239,68,68,.15);  --danger-fg:#f87171;
        --warning-bg:rgba(245,158,11,.15);--warning-fg:#fbbf24;
    }
}

*{box-sizing:border-box;margin:0;padding:0}
html{-webkit-text-size-adjust:100%}

body{
    font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
    background:var(--bg);
    color:var(--text);
    min-height:100dvh;
    display:grid;
    place-items:center;
    padding:16px;
    line-height:1.5;
}

.card{
    width:100%;max-width:420px;
    background:var(--card);
    border:1px solid var(--border);
    border-radius:12px;
    padding:24px;
    box-shadow:0 1px 3px rgba(0,0,0,.06), 0 8px 24px -12px rgba(0,0,0,.12);
}

/* ── Marca ─────────────────────────────────────── */
.marca{display:flex;align-items:center;gap:10px;margin-bottom:18px}
.marca img{height:28px;width:auto}
.marca div{min-width:0}
.marca .nombre{font-size:.8rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.marca .ruc{font-size:.72rem;color:var(--text-3)}

h1{font-size:1.15rem;font-weight:600;letter-spacing:-.01em}
.sub{font-size:.82rem;color:var(--text-2);margin:4px 0 18px}

/* ── Formulario ────────────────────────────────── */
label{display:block;font-size:.78rem;font-weight:500;color:var(--text);margin-bottom:5px}
input,select{
    width:100%;padding:8px 11px;
    border:1px solid var(--border);border-radius:8px;
    background:var(--input-bg);color:var(--text);
    font:inherit;font-size:.875rem;
    transition:border-color .15s, box-shadow .15s;
}
input::placeholder{color:var(--text-3)}
input:focus,select:focus{
    outline:none;border-color:var(--primary);
    box-shadow:0 0 0 3px rgba(59,130,246,.2);
}
.campo{margin-bottom:12px}
.par{display:grid;grid-template-columns:1fr 1.2fr;gap:10px}

.btn{
    display:flex;align-items:center;justify-content:center;gap:7px;
    width:100%;padding:9px 14px;
    border:0;border-radius:8px;cursor:pointer;
    font:inherit;font-size:.875rem;font-weight:600;
    background:var(--primary-600);color:#fff;
    text-decoration:none;
    transition:background .15s;
}
.btn:hover{background:var(--primary-700)}
.btn.mt{margin-top:6px}
.btn-sec{
    background:transparent;color:var(--text-2);
    border:1px solid var(--border);margin-top:8px;
}
.btn-sec:hover{background:var(--bg);color:var(--text)}

/* ── Resultado ─────────────────────────────────── */
.cabecera{
    display:flex;align-items:flex-start;justify-content:space-between;gap:12px;
    padding-bottom:14px;margin-bottom:4px;border-bottom:1px solid var(--border);
}
.doc-num{font-size:1.15rem;font-weight:700;letter-spacing:-.01em;font-variant-numeric:tabular-nums}
.doc-tipo{font-size:.78rem;color:var(--text-2);margin-top:2px}

.badge{
    flex-shrink:0;display:inline-flex;align-items:center;gap:5px;
    padding:4px 9px;border-radius:6px;
    font-size:.7rem;font-weight:600;white-space:nowrap;
}
.badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.badge.ok{background:var(--success-bg);color:var(--success-fg)}
.badge.bad{background:var(--danger-bg);color:var(--danger-fg)}
.badge.warn{background:var(--warning-bg);color:var(--warning-fg)}

.dato{
    display:flex;justify-content:space-between;align-items:baseline;gap:14px;
    padding:9px 0;border-bottom:1px solid var(--border);
}
.dato:last-of-type{border-bottom:0}
.dato dt{font-size:.78rem;color:var(--text-2);white-space:nowrap}
.dato dd{font-size:.82rem;font-weight:500;text-align:right;word-break:break-word}
.dato dd.monto{font-size:1rem;font-weight:700;font-variant-numeric:tabular-nums}

.vence{margin-top:8px;text-align:center;font-size:.7rem;color:var(--text-3)}

/* ── Alerta ────────────────────────────────────── */
.aviso{
    display:flex;gap:9px;margin-bottom:16px;padding:10px 12px;
    background:var(--danger-bg);border-radius:8px;
    font-size:.8rem;line-height:1.45;color:var(--danger-fg);
}
.err{font-size:.72rem;color:var(--danger-fg);margin-top:4px}

.pie{margin-top:14px;text-align:center;font-size:.7rem;color:var(--text-3)}

@media (max-width:400px){ .par{grid-template-columns:1fr} }
</style>
</head>
<body>
<main class="card">

    @if ($empresa)
        <div class="marca">
            <img src="{{ asset('logos/logo.svg') }}" alt="">
            <div>
                <div class="nombre">{{ $empresa->comercial ?: $empresa->razon_social }}</div>
                <div class="ruc">RUC {{ $empresa->ruc }}</div>
            </div>
        </div>
    @endif

    @isset($resultado)
        {{-- ── Estado: documento encontrado ── --}}
        <div class="cabecera">
            <div>
                <div class="doc-num">{{ $resultado['documento'] }}</div>
                <div class="doc-tipo">{{ $resultado['titulo'] }}</div>
            </div>

            @if ($resultado['anulado'])
                <span class="badge bad">Anulado</span>
            @elseif ($resultado['estado_sunat'] === 'aceptado')
                <span class="badge ok">Aceptado</span>
            @elseif ($resultado['estado_sunat'] === 'rechazado')
                <span class="badge bad">Rechazado</span>
            @else
                <span class="badge warn">Pendiente</span>
            @endif
        </div>

        <dl>
            <div class="dato">
                <dt>Fecha de emisión</dt>
                <dd>{{ $resultado['fecha'] }}</dd>
            </div>
            <div class="dato">
                <dt>Cliente</dt>
                <dd>{{ $resultado['cliente'] }}</dd>
            </div>
            @if (! is_null($resultado['total']))
                <div class="dato">
                    <dt>Importe total</dt>
                    <dd class="monto">S/ {{ number_format($resultado['total'], 2) }}</dd>
                </div>
            @endif
        </dl>

        <a class="btn mt" href="{{ $resultado['pdf_url'] }}" target="_blank" rel="noopener">
            Ver o descargar el PDF
        </a>
        <p class="vence">El enlace vence en 30 minutos</p>

        <a class="btn btn-sec" href="{{ route('consulta.index') }}">Consultar otro documento</a>

    @else
        {{-- ── Estado: formulario ── --}}
        <h1>Consulta tu comprobante</h1>
        <p class="sub">Ingresá los datos de tu documento para verificarlo y descargarlo.</p>

        @if (session('error'))
            <div class="aviso" role="alert">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('consulta.buscar') }}" novalidate>
            @csrf

            <div class="campo">
                <label for="tipo">Tipo de documento</label>
                <select id="tipo" name="tipo" required>
                    <option value="venta" @selected(old('tipo', 'venta') === 'venta')>Factura o Boleta</option>
                    <option value="nota"  @selected(old('tipo') === 'nota')>Nota de crédito o débito</option>
                    <option value="guia"  @selected(old('tipo') === 'guia')>Guía de remisión</option>
                </select>
            </div>

            <div class="campo par">
                <div>
                    <label for="serie">Serie</label>
                    <input id="serie" name="serie" value="{{ old('serie') }}" placeholder="B001"
                           maxlength="4" autocomplete="off" spellcheck="false" required
                           style="text-transform:uppercase">
                    @error('serie') <p class="err">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="numero">Número</label>
                    <input id="numero" name="numero" value="{{ old('numero') }}" placeholder="605"
                           inputmode="numeric" autocomplete="off" required>
                    @error('numero') <p class="err">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="campo">
                <label for="documento">Tu DNI o RUC</label>
                <input id="documento" name="documento" value="{{ old('documento') }}" placeholder="77425200"
                       inputmode="numeric" maxlength="15" autocomplete="off" required>
                @error('documento') <p class="err">{{ $message }}</p> @enderror
            </div>

            <button class="btn mt" type="submit">Consultar</button>
        </form>
    @endisset

    <p class="pie">Representación impresa del comprobante electrónico</p>
</main>
</body>
</html>
