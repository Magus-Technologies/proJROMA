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
            </div>
        </div>
    </div>

    <style>
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
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
