<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consulta de comprobante</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;background:#f1f5f9;color:#0f172a;
             min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{width:100%;max-width:520px;background:#fff;border-radius:16px;padding:28px;
              box-shadow:0 10px 30px rgba(0,0,0,.08)}
        h1{font-size:1.4rem;margin-bottom:6px}
        .sub{color:#64748b;font-size:.9rem;margin-bottom:22px}
        label{display:block;font-size:.82rem;font-weight:600;color:#475569;margin:14px 0 5px}
        input,select{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:.92rem;background:#fff}
        input:focus,select:focus{outline:none;border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.15)}
        .row{display:flex;gap:12px}
        .row>div{flex:1}
        button{width:100%;margin-top:20px;padding:12px;border:0;border-radius:10px;background:#3b82f6;color:#fff;
               font-weight:600;font-size:.95rem;cursor:pointer}
        button:hover{background:#2563eb}
        .alerta{margin-top:16px;padding:11px 14px;border-radius:10px;font-size:.86rem;
                background:#fef2f2;border:1px solid #fecaca;color:#b91c1c}
        .campo-error{color:#b91c1c;font-size:.78rem;margin-top:4px}
        .res{margin-top:24px;padding:18px;border-radius:12px;border:1px solid #bfdbfe;background:#eff6ff}
        .res h2{font-size:1.05rem;margin-bottom:12px}
        .fila{display:flex;justify-content:space-between;gap:12px;padding:6px 0;font-size:.88rem}
        .fila span:first-child{color:#64748b}
        .fila span:last-child{font-weight:600;text-align:right}
        .badge{display:inline-block;padding:3px 9px;border-radius:999px;font-size:.75rem;font-weight:700}
        .ok{background:#dcfce7;color:#166534}
        .warn{background:#fef9c3;color:#854d0e}
        .bad{background:#fee2e2;color:#991b1b}
        .btn-pdf{display:block;text-align:center;margin-top:16px;padding:11px;border-radius:10px;
                 background:#0f172a;color:#fff;text-decoration:none;font-weight:600;font-size:.9rem}
        .nota{margin-top:12px;font-size:.75rem;color:#64748b;text-align:center}
    </style>
</head>
<body>
<div class="card">
    <h1>Consulta tu comprobante</h1>
    <p class="sub">Ingresá los datos de tu documento para verificarlo y descargarlo.</p>

    @if (session('error'))
        <div class="alerta">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('consulta.buscar') }}">
        @csrf

        <label for="tipo">Tipo de documento</label>
        <select id="tipo" name="tipo" required>
            <option value="venta" @selected(old('tipo') === 'venta')>Factura / Boleta</option>
            <option value="nota"  @selected(old('tipo') === 'nota')>Nota de crédito / débito</option>
            <option value="guia"  @selected(old('tipo') === 'guia')>Guía de remisión</option>
        </select>

        <div class="row">
            <div>
                <label for="serie">Serie</label>
                <input id="serie" name="serie" value="{{ old('serie') }}" placeholder="B001" maxlength="4" required>
                @error('serie') <div class="campo-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="numero">Número</label>
                <input id="numero" name="numero" value="{{ old('numero') }}" placeholder="605" inputmode="numeric" required>
                @error('numero') <div class="campo-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <label for="documento">Tu DNI o RUC</label>
        <input id="documento" name="documento" value="{{ old('documento') }}" placeholder="77425200" maxlength="15" required>
        @error('documento') <div class="campo-error">{{ $message }}</div> @enderror

        <button type="submit">Consultar</button>
    </form>

    @isset($resultado)
        <div class="res">
            <h2>{{ $resultado['titulo'] }} — {{ $resultado['documento'] }}</h2>

            <div class="fila"><span>Fecha de emisión</span><span>{{ $resultado['fecha'] }}</span></div>
            <div class="fila"><span>Cliente</span><span>{{ $resultado['cliente'] }}</span></div>

            @if (! is_null($resultado['total']))
                <div class="fila"><span>Total</span><span>S/ {{ number_format($resultado['total'], 2) }}</span></div>
            @endif

            <div class="fila">
                <span>Estado</span>
                <span>
                    @if ($resultado['anulado'])
                        <span class="badge bad">Anulado</span>
                    @elseif ($resultado['estado_sunat'] === 'aceptado')
                        <span class="badge ok">Aceptado por SUNAT</span>
                    @elseif ($resultado['estado_sunat'] === 'rechazado')
                        <span class="badge bad">Rechazado por SUNAT</span>
                    @else
                        <span class="badge warn">Pendiente de envío</span>
                    @endif
                </span>
            </div>

            <a class="btn-pdf" href="{{ $resultado['pdf_url'] }}" target="_blank" rel="noopener">Ver / descargar PDF</a>
            <p class="nota">El enlace del PDF vence en 30 minutos.</p>
        </div>
    @endisset
</div>
</body>
</html>
