<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Consulta de comprobante · {{ $empresa->comercial ?? $empresa->razon_social ?? 'Comprobante electrónico' }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
:root{
    --ink:#14202e;
    --ink-2:#3f5064;
    --ink-3:#7c8a99;
    --paper:#f4f0e7;
    --card:#fffdf8;
    --seal:#8c2f39;
    --sage:#3d6b52;
    --ochre:#93641c;
    --rule:rgba(20,32,46,.13);
    --rule-hard:rgba(20,32,46,.28);

    --guilloche:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='32' viewBox='0 0 64 32'%3E%3Cg fill='none' stroke='%2314202e' stroke-width='.45' opacity='.16'%3E%3Cpath d='M0 16 Q16 2 32 16 T64 16'/%3E%3Cpath d='M0 24 Q16 10 32 24 T64 24'/%3E%3Cpath d='M0 8 Q16 -6 32 8 T64 8'/%3E%3C/g%3E%3C/svg%3E");
    --grain:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='180' height='180' filter='url(%23n)' opacity='.5'/%3E%3C/svg%3E");
}

*{box-sizing:border-box;margin:0;padding:0}
html{-webkit-text-size-adjust:100%}

body{
    font-family:"IBM Plex Sans",ui-sans-serif,system-ui,sans-serif;
    background:var(--paper);
    color:var(--ink);
    min-height:100dvh;
    padding:clamp(20px,5vw,64px) 20px;
    display:flex;flex-direction:column;align-items:center;
    position:relative;
}
/* Grano de papel sobre todo el fondo */
body::after{
    content:"";position:fixed;inset:0;pointer-events:none;z-index:0;
    background-image:var(--grain);
    opacity:.05;mix-blend-mode:multiply;
}
.wrap{width:100%;max-width:620px;position:relative;z-index:1}

/* ── Cabecera del emisor ─────────────────────────────── */
.head{margin-bottom:clamp(24px,4vw,40px)}
.eyebrow{
    font-family:"IBM Plex Mono",monospace;
    font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;
    color:var(--seal);font-weight:600;
    display:flex;align-items:center;gap:12px;
}
.eyebrow::after{content:"";flex:1;height:1px;background:var(--rule-hard)}

.head h1{
    font-family:"Fraunces","Georgia",serif;
    font-optical-sizing:auto;
    font-weight:600;
    font-size:clamp(2rem,6.5vw,2.9rem);
    line-height:1.02;
    letter-spacing:-.02em;
    margin:16px 0 12px;
}
.emisor{
    font-size:.82rem;color:var(--ink-2);line-height:1.6;
}
.emisor strong{color:var(--ink);font-weight:600}
.ruc{font-family:"IBM Plex Mono",monospace;font-size:.78rem;color:var(--ink-3)}

/* ── Tarjeta de papel ────────────────────────────────── */
.sheet{
    background:var(--card);
    border:1px solid var(--rule-hard);
    border-radius:2px;
    box-shadow:0 1px 0 rgba(20,32,46,.06), 0 18px 40px -28px rgba(20,32,46,.55);
    position:relative;
    overflow:hidden;
}
/* Banda de seguridad guilloché */
.sheet::before{
    content:"";position:absolute;inset:0 0 auto 0;height:8px;
    background-image:var(--guilloche);
    border-bottom:1px solid var(--rule);
}
.sheet-body{padding:clamp(22px,4.5vw,34px)}

/* ── Documento encontrado (recibo) ───────────────────── */
.doc{margin-bottom:26px;position:relative}
.doc-kicker{
    font-family:"IBM Plex Mono",monospace;
    font-size:.66rem;letter-spacing:.18em;text-transform:uppercase;color:var(--ink-3);
}
.doc-id{
    font-family:"IBM Plex Mono",monospace;
    font-size:clamp(1.5rem,5.5vw,2rem);
    font-weight:600;letter-spacing:-.01em;
    margin:6px 0 4px;
}
.doc-tipo{
    font-family:"Fraunces",serif;font-size:1rem;color:var(--ink-2);font-weight:400;
}

.datos{margin-top:22px;border-top:1px solid var(--rule)}
.dato{
    display:flex;justify-content:space-between;align-items:baseline;gap:16px;
    padding:11px 0;border-bottom:1px dotted var(--rule-hard);
}
.dato dt{font-size:.78rem;color:var(--ink-3);letter-spacing:.02em;white-space:nowrap}
.dato dd{
    font-family:"IBM Plex Mono",monospace;font-size:.86rem;font-weight:500;
    text-align:right;word-break:break-word;
}
.dato dd.monto{font-size:1.05rem;font-weight:600}

/* Sello de goma */
.sello{
    position:absolute;top:-6px;right:-6px;
    transform:rotate(-9deg);
    border:2.5px solid currentColor;border-radius:4px;
    padding:7px 13px 6px;
    font-family:"Fraunces",serif;font-weight:700;
    font-size:.7rem;letter-spacing:.14em;text-transform:uppercase;
    line-height:1.15;text-align:center;
    opacity:.88;mix-blend-mode:multiply;
    box-shadow:inset 0 0 0 1px currentColor;
    animation:sellar .55s cubic-bezier(.2,1.35,.4,1) both;
}
.sello small{display:block;font-family:"IBM Plex Mono",monospace;font-weight:500;font-size:.56rem;letter-spacing:.1em;opacity:.75;margin-top:2px}
.sello.ok{color:var(--sage)}
.sello.bad{color:var(--seal)}
.sello.pend{color:var(--ochre)}

@keyframes sellar{
    0%{opacity:0;transform:rotate(-9deg) scale(1.9)}
    55%{opacity:.95}
    100%{opacity:.88;transform:rotate(-9deg) scale(1)}
}

/* Línea de corte perforada */
.corte{
    margin:28px 0;border:0;height:1px;
    background:repeating-linear-gradient(90deg,var(--rule-hard) 0 5px,transparent 5px 11px);
    position:relative;
}
.corte::before,.corte::after{
    content:"";position:absolute;top:-7px;width:14px;height:14px;border-radius:50%;
    background:var(--paper);border:1px solid var(--rule-hard);
}
.corte::before{left:-24px}
.corte::after{right:-24px}

/* ── Descarga ────────────────────────────────────────── */
.descargar{
    display:flex;align-items:center;justify-content:center;gap:10px;
    margin-top:22px;padding:14px;
    background:var(--ink);color:var(--card);
    text-decoration:none;border-radius:2px;
    font-weight:600;font-size:.9rem;letter-spacing:.01em;
    transition:transform .18s ease, box-shadow .18s ease, background .18s ease;
}
.descargar:hover{background:#0a1520;transform:translateY(-1px);box-shadow:0 10px 22px -14px rgba(20,32,46,.9)}
.descargar:active{transform:translateY(0)}
.vence{
    margin-top:9px;text-align:center;
    font-family:"IBM Plex Mono",monospace;font-size:.68rem;color:var(--ink-3);letter-spacing:.04em;
}

/* ── Formulario ──────────────────────────────────────── */
.form-titulo{
    font-family:"Fraunces",serif;font-size:1.15rem;font-weight:600;margin-bottom:4px;
}
.form-sub{font-size:.83rem;color:var(--ink-2);margin-bottom:22px;line-height:1.55}

.campo{margin-bottom:16px;animation:subir .5s ease both}
.campo:nth-child(1){animation-delay:.04s}
.campo:nth-child(2){animation-delay:.09s}
.campo:nth-child(3){animation-delay:.14s}
@keyframes subir{from{opacity:0;transform:translateY(7px)}to{opacity:1;transform:none}}

label{
    display:block;
    font-family:"IBM Plex Mono",monospace;
    font-size:.66rem;letter-spacing:.15em;text-transform:uppercase;
    color:var(--ink-2);font-weight:600;margin-bottom:7px;
}
input,select{
    width:100%;padding:12px 13px;
    border:1px solid var(--rule-hard);border-radius:2px;
    background:#fff;color:var(--ink);
    font-family:"IBM Plex Mono",monospace;font-size:.92rem;
    transition:border-color .15s ease, box-shadow .15s ease;
}
select{font-family:"IBM Plex Sans",sans-serif;cursor:pointer}
input::placeholder{color:#b9c2ca}
input:focus,select:focus{
    outline:none;border-color:var(--ink);
    box-shadow:0 0 0 3px rgba(20,32,46,.09);
}
.par{display:grid;grid-template-columns:1fr 1.3fr;gap:14px}

.enviar{
    width:100%;margin-top:8px;padding:14px;
    border:1px solid var(--ink);border-radius:2px;
    background:var(--ink);color:var(--card);
    font-family:"IBM Plex Sans",sans-serif;
    font-weight:600;font-size:.9rem;letter-spacing:.02em;cursor:pointer;
    transition:transform .18s ease, box-shadow .18s ease;
}
.enviar:hover{transform:translateY(-1px);box-shadow:0 10px 22px -14px rgba(20,32,46,.9)}
.enviar:active{transform:translateY(0)}

/* ── Alertas ─────────────────────────────────────────── */
.aviso{
    display:flex;gap:11px;align-items:flex-start;
    margin-bottom:22px;padding:13px 15px;
    border:1px solid var(--seal);border-left-width:3px;border-radius:2px;
    background:rgba(140,47,57,.05);
    font-size:.84rem;line-height:1.5;color:#6d242c;
}
.aviso strong{display:block;font-weight:600;margin-bottom:2px;color:var(--seal)}
.err{
    font-family:"IBM Plex Mono",monospace;
    font-size:.7rem;color:var(--seal);margin-top:5px;letter-spacing:.02em;
}

.pie{
    margin-top:26px;text-align:center;
    font-size:.74rem;color:var(--ink-3);line-height:1.6;
}
.pie a{color:var(--ink-2)}

@media (max-width:480px){
    .par{grid-template-columns:1fr}
    .sello{position:static;display:inline-block;transform:rotate(-3deg);margin-top:14px}
    .corte::before,.corte::after{display:none}
}
@media (prefers-reduced-motion:reduce){
    *{animation:none!important;transition:none!important}
}
</style>
</head>
<body>
<div class="wrap">

    <header class="head">
        <p class="eyebrow">Comprobante electrónico</p>
        <h1>Consulta tu documento</h1>
        @if ($empresa)
            <p class="emisor">
                <strong>{{ $empresa->razon_social }}</strong><br>
                <span class="ruc">RUC {{ $empresa->ruc }}</span>
            </p>
        @endif
    </header>

    <div class="sheet">
        <div class="sheet-body">

            @isset($resultado)
                <section class="doc">
                    @if ($resultado['anulado'])
                        <div class="sello bad">Anulado<small>sin efecto</small></div>
                    @elseif ($resultado['estado_sunat'] === 'aceptado')
                        <div class="sello ok">Aceptado<small>por SUNAT</small></div>
                    @elseif ($resultado['estado_sunat'] === 'rechazado')
                        <div class="sello bad">Rechazado<small>por SUNAT</small></div>
                    @else
                        <div class="sello pend">Pendiente<small>de envío</small></div>
                    @endif

                    <p class="doc-kicker">Documento verificado</p>
                    <p class="doc-id">{{ $resultado['documento'] }}</p>
                    <p class="doc-tipo">{{ $resultado['titulo'] }}</p>

                    <dl class="datos">
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

                    <a class="descargar" href="{{ $resultado['pdf_url'] }}" target="_blank" rel="noopener">
                        Ver o descargar el PDF
                    </a>
                    <p class="vence">El enlace vence en 30 minutos</p>
                </section>

                <hr class="corte">
            @endisset

            @if (session('error'))
                <div class="aviso" role="alert">
                    <div>
                        <strong>No encontramos ese documento</strong>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <h2 class="form-titulo">
                {{ isset($resultado) ? 'Consultar otro documento' : 'Verificá tu comprobante' }}
            </h2>
            <p class="form-sub">
                Ingresá los datos que figuran en tu documento. Pedimos tu DNI o RUC para
                proteger la información de los demás clientes.
            </p>

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

                <button class="enviar" type="submit">Consultar documento</button>
            </form>
        </div>
    </div>

    <p class="pie">
        Representación impresa del comprobante electrónico.<br>
        Podés validarlo también en el portal de SUNAT.
    </p>
</div>
</body>
</html>
