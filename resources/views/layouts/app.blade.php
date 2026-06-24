<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'ProjRoma') — Facturación</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    colors: {
                        brand: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#1d4ed8',700:'#1e40af',800:'#1e3a8a',900:'#1c2e6e' },
                        navy:  { 800:'#0f1f3d', 900:'#0a1628' },
                    }
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">

    <style>
        [x-cloak]{display:none!important}
        ::-webkit-scrollbar{width:5px;height:5px}
        ::-webkit-scrollbar-track{background:#f1f5f9}
        ::-webkit-scrollbar-thumb{background:#94a3b8;border-radius:3px}
        ::-webkit-scrollbar-thumb:hover{background:#1d4ed8}
        #sidebar{transition:transform .25s cubic-bezier(.4,0,.2,1)}
        .card-hover{transition:box-shadow .2s,transform .2s}
        .card-hover:hover{box-shadow:0 10px 30px rgba(0,0,0,.12);transform:translateY(-2px)}
        tbody tr{transition:background .15s}
        tbody tr:hover{background:#eff6ff}
        @keyframes spin{to{transform:rotate(360deg)}}
        .spin{animation:spin 1s linear infinite}
        @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .fade-in{animation:fadeIn .3s ease-out}
    </style>

    @stack('styles')
</head>

<body class="h-full bg-slate-50 font-sans text-gray-800 antialiased"
      x-data="{ sidebar: false }">

{{-- Overlay móvil --}}
<div x-show="sidebar" x-cloak @click="sidebar=false"
     class="fixed inset-0 z-20 bg-black/50 lg:hidden"></div>

<div class="flex h-full">

    {{-- ══ SIDEBAR ══════════════════════════════════════════════════════════ --}}
    <aside id="sidebar"
           :class="{ '-translate-x-full': !sidebar, 'translate-x-0': sidebar }"
           class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col
                  bg-gradient-to-b from-[#0a1628] via-[#0f1f3d] to-[#1e3a8a]
                  shadow-2xl lg:relative lg:translate-x-0">

        {{-- Logo --}}
        <div class="flex h-16 shrink-0 items-center gap-3 border-b border-white/10 px-5">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 shadow-lg">
                <i class="ti ti-ship text-xl text-white"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold text-white">ProjRoma</div>
                <div class="text-[10px] text-blue-300 truncate">{{ session('nombre_empresa') }}</div>
            </div>
            <button @click="sidebar=false" class="lg:hidden text-white/60 hover:text-white">
                <i class="ti ti-x"></i>
            </button>
        </div>

        {{-- Usuario --}}
        <div class="flex items-center gap-3 border-b border-white/10 px-5 py-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-500 text-xs font-bold text-white">
                {{ strtoupper(substr(auth()->user()->nombres ?? 'U', 0, 2)) }}
            </div>
            <div class="min-w-0">
                <div class="truncate text-xs font-semibold text-white">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</div>
                <div class="text-[10px] text-blue-300">{{ auth()->user()->rol?->nombre ?? 'Usuario' }} · Suc.{{ session('sucursal') }}</div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5 text-sm">
            @php $rol = auth()->user()->id_rol ?? 2; @endphp

            <x-nav-link href="{{ route('dashboard') }}"           icon="ti-home"            label="Dashboard" />

            <x-nav-section label="Facturación" />
            <x-nav-link href="{{ route('ventas.index') }}"        icon="ti-receipt"         label="Ventas" />
            <x-nav-link href="{{ route('guias.index') }}"         icon="ti-truck-delivery"  label="Guías de Remisión" />
            <x-nav-link href="{{ route('nota.electronica.lista') }}" icon="ti-file-invoice" label="Notas Electrónicas" />

            <x-nav-section label="Pedidos" />
            <x-nav-link href="{{ route('cotizaciones.index') }}"  icon="ti-clipboard-list"  label="Pedidos / Cotizaciones" />

            <x-nav-section label="Cobranzas" />
            <x-nav-link href="{{ route('cobranzas.index') }}"     icon="ti-credit-card"     label="Cuentas por Cobrar" />
            <x-nav-link href="{{ route('cobranzas.deudas') }}"    icon="ti-report-money"    label="Reporte Deudas" />
            @if(in_array($rol,[3,4]))
                <x-nav-link href="{{ route('cobranzas.miscobros') }}" icon="ti-wallet"      label="Mis Cobros" />
            @endif

            <x-nav-section label="Pagos" />
            <x-nav-link href="{{ route('pagos.index') }}"         icon="ti-building-bank"   label="Cuentas por Pagar" />
            <x-nav-link href="{{ route('devoluciones.index') }}"  icon="ti-arrow-back-up"   label="Devoluciones" />

            <x-nav-section label="Cajas" />
            <x-nav-link href="{{ route('caja.registros') }}"      icon="ti-cash-register"   label="Registro de Caja" />
            <x-nav-link href="{{ route('caja.flujo') }}"          icon="ti-coins"           label="Caja Chica" />
            <x-nav-link href="{{ route('caja.arqueo') }}"         icon="ti-calculator"      label="Arqueo Diario" />
            @if($rol == 3)
                <x-nav-link href="{{ route('caja.micaja') }}"     icon="ti-wallet"          label="Mi Caja" />
            @endif

            <x-nav-section label="Compras" />
            <x-nav-link href="{{ route('compras.index') }}"       icon="ti-shopping-cart"   label="Compras" />

            <x-nav-section label="Almacén" />
            <x-nav-link href="{{ route('almacen.index') }}"       icon="ti-packages"        label="Kardex / Productos" />
            <x-nav-link href="{{ route('almacen.intercambio') }}" icon="ti-arrows-exchange" label="Intercambio" />

            <x-nav-section label="Maestros" />
            <x-nav-link href="{{ route('clientes.index') }}"      icon="ti-users"           label="Clientes" />
            <x-nav-link href="{{ route('proveedores.index') }}"   icon="ti-building-store"  label="Proveedores" />

            @if($rol == 1)
                <x-nav-section label="Administración" />
                <x-nav-link href="{{ route('usuarios.index') }}"  icon="ti-user-cog"        label="Usuarios" />
                <x-nav-link href="{{ route('admin.empresas') }}"  icon="ti-building"        label="Empresas" />
            @endif
        </nav>

        {{-- Logout --}}
        <div class="shrink-0 border-t border-white/10 p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-xs text-blue-200 hover:bg-white/10 hover:text-white transition-colors">
                    <i class="ti ti-logout text-sm"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- ══ CONTENIDO ════════════════════════════════════════════════════════ --}}
    <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="sticky top-0 z-10 flex h-14 shrink-0 items-center gap-3 border-b border-gray-200 bg-white px-4 shadow-sm">
            <button @click="sidebar=!sidebar" class="lg:hidden text-gray-500 hover:text-gray-800">
                <i class="ti ti-menu-2 text-xl"></i>
            </button>

            <div class="flex-1 min-w-0">
                <h1 class="text-sm font-semibold text-gray-700 truncate">@yield('page-title','Dashboard')</h1>
                @hasSection('breadcrumb')
                    <p class="text-[10px] text-gray-400">@yield('breadcrumb')</p>
                @endif
            </div>

            <div class="hidden sm:flex items-center gap-3 shrink-0">
                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-brand-700">
                    <i class="ti ti-building text-[10px]"></i> Suc. {{ session('sucursal') }}
                </span>
                <span class="text-xs text-gray-400">{{ now()->format('d/m/Y') }}</span>
            </div>
        </header>

        {{-- Main --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">

            @if(session('success'))
                <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4500)"
                     class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 fade-in">
                    <i class="ti ti-circle-check text-emerald-500"></i>
                    {{ session('success') }}
                    <button @click="show=false" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="ti ti-x"></i></button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 fade-in">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Scripts globales --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
    // CSRF global para jQuery AJAX
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    // Toasts
    const Toast = Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3500, timerProgressBar:true });
    const toastOk   = msg => Toast.fire({ icon:'success', title: msg });
    const toastErr  = msg => Toast.fire({ icon:'error',   title: msg });
    const toastWarn = msg => Toast.fire({ icon:'warning', title: msg });

    // Helpers fetch
    const _csrf = () => document.querySelector('meta[name=csrf-token]').content;

    async function apiPost(url, data = {}) {
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': _csrf(), 'Accept':'application/json' },
            body: JSON.stringify(data),
        });
        return r.json();
    }

    async function apiGet(url, params = {}) {
        const qs = new URLSearchParams(params).toString();
        const r = await fetch(qs ? `${url}?${qs}` : url, {
            headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': _csrf() }
        });
        return r.json();
    }

    // Formatear moneda
    const sol = n => 'S/ ' + parseFloat(n || 0).toFixed(2);
</script>

@stack('scripts')
</body>
</html>
