@php
    $livewire ??= null;
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <div style="
        min-height: 100vh;
        display: flex;
        background: linear-gradient(135deg, var(--brand-from,#0a1628) 0%, var(--brand-mid,#0f1f3d) 45%, var(--brand-dark,#1e3a8a) 75%, var(--brand-light,#1d4ed8) 100%);
        position: relative;
        overflow: hidden;
    ">
        {{-- Orbs decorativos --}}
        <div style="position:fixed;width:500px;height:500px;border-radius:50%;background:rgba(59,130,246,.18);top:-180px;right:-120px;pointer-events:none;filter:blur(80px);z-index:0;"></div>
        <div style="position:fixed;width:320px;height:320px;border-radius:50%;background:rgba(29,78,216,.22);bottom:-80px;left:-80px;pointer-events:none;filter:blur(80px);z-index:0;"></div>

        {{-- Panel izquierdo — solo desktop --}}
        <div class="stc-left" style="
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 50%;
            padding: 4rem;
            position: relative;
            z-index: 1;
        ">
            <div style="margin-bottom:2.5rem;animation:float 6s ease-in-out infinite;">
                <div style="display:flex;align-items:center;justify-content:center;border-radius:1.5rem;background:rgba(255,255,255,.95);box-shadow:0 25px 50px -12px rgba(0,0,0,.4);padding:1rem 1.5rem;">
                    <img src="{{ asset('logos/logo.svg') }}" alt="{{ filament()->getBrandName() }}" style="height:5rem;width:auto;">
                </div>
            </div>
            <p style="color:#bfdbfe;font-size:.875rem;text-align:center;max-width:18rem;margin:0 auto 2.5rem;line-height:1.7;">
                Sistema integrado de ventas, facturación electrónica SUNAT y gestión de almacén.
            </p>
            <div style="display:flex;flex-direction:column;gap:.625rem;width:100%;max-width:18rem;">
                @foreach([
                    ['ti-receipt-2',   'Facturación electrónica SUNAT'],
                    ['ti-chart-bar',   'Dashboard con KPIs en tiempo real'],
                    ['ti-users',       'Multi-empresa · Multi-sucursal'],
                    ['ti-shield-lock', 'Roles y acceso por perfiles'],
                ] as [$ico, $txt])
                <div style="display:flex;align-items:center;gap:.75rem;border-radius:.75rem;padding:.625rem 1rem;background:rgba(255,255,255,.07);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.13);">
                    <i class="ti {{ $ico }}" style="color:#93c5fd;font-size:.875rem;flex-shrink:0;"></i>
                    <span style="font-size:.75rem;color:#bfdbfe;">{{ $txt }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Panel derecho — formulario --}}
        <div style="display:flex;flex:1;align-items:center;justify-content:center;padding:1.5rem;position:relative;z-index:1;">
            <div style="width:100%;max-width:28rem;">

                {{-- Logo móvil --}}
                <div class="stc-mobile-logo" style="display:flex;justify-content:center;margin-bottom:2rem;">
                    <div style="display:flex;align-items:center;justify-content:center;border-radius:1.25rem;background:rgba(255,255,255,.9);padding:.75rem 1.25rem;box-shadow:0 20px 40px rgba(0,0,0,.3);animation:float 6s ease-in-out infinite;">
                        <img src="{{ asset('logos/logo.svg') }}" alt="{{ filament()->getBrandName() }}" style="height:3rem;width:auto;">
                    </div>
                </div>

                {{-- Slot = contenido del view de Login --}}
                {{ $slot }}

                <p style="margin-top:1.25rem;text-align:center;font-size:.625rem;color:rgba(147,197,253,.4);">
                    &copy; {{ date('Y') }} {{ filament()->getBrandName() }} &middot; Panel Administrativo
                </p>

                {{-- Créditos del desarrollador --}}
                <a href="https://magustechnologies.com/" target="_blank" rel="noopener noreferrer" class="stc-magus">
                    <img src="{{ asset('logos/magus.png') }}" alt="Magus Technologies" class="stc-magus-logo">
                    <span class="stc-magus-sep"></span>
                    <span class="stc-magus-text">
                        <strong>Desarrollado por Magus Technologies</strong>
                        <span class="stc-magus-link">
                            magustechnologies.com
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
                        </span>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .stc-magus {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1rem;
            padding: .875rem 1.25rem;
            border-radius: 1rem;
            background: #0a121f;
            border: 1px solid #22d3ee;
            box-shadow: 0 0 0 1px rgba(34,211,238,.15), 0 8px 24px rgba(0,0,0,.35);
            text-decoration: none;
            transition: box-shadow .15s ease, transform .15s ease;
        }
        .stc-magus:hover {
            box-shadow: 0 0 0 1px rgba(34,211,238,.4), 0 0 20px rgba(34,211,238,.25), 0 8px 24px rgba(0,0,0,.35);
            transform: translateY(-1px);
        }
        .stc-magus-logo { height: 2.5rem; width: auto; flex-shrink: 0; }
        .stc-magus-sep  { align-self: stretch; width: 1px; background: rgba(255,255,255,.18); flex-shrink: 0; }
        .stc-magus-text { display: flex; flex-direction: column; gap: .125rem; min-width: 0; }
        .stc-magus-text strong { color: #fff; font-size: .8125rem; font-weight: 700; }
        .stc-magus-link {
            display: inline-flex; align-items: center; gap: .375rem;
            color: #22d3ee; font-size: .75rem;
        }
        @media (min-width: 1024px) {
            .stc-left        { display: flex !important; }
            .stc-mobile-logo { display: none !important; }
        }
        /* Override Filament simple layout resets so nuestro contenedor tome el 100% */
        .fi-simple-layout,
        .fi-simple-main-ctn,
        .fi-simple-main {
            all: unset !important;
            display: contents !important;
        }
    </style>
</x-filament-panels::layout.base>
