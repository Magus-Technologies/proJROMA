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

    {{-- Sistema de diseño: paleta de colores + estilos de componentes --}}
    @include('partials.theme')

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.dataTables.min.css">
    {{-- Extensiones DataTables: ColReorder · Buttons(colVis) · Responsive --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    @stack('styles')
</head>

<body class="h-full bg-slate-50 font-sans text-gray-800 antialiased"
      x-data="{
        sidebar: false,
        collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggleCollapsed() {
          this.collapsed = !this.collapsed;
          localStorage.setItem('sidebarCollapsed', this.collapsed);
        }
      }">

{{-- Overlay móvil --}}
<div x-show="sidebar" x-cloak @click="sidebar=false"
     class="fixed inset-0 z-20 bg-black/50 lg:hidden"></div>

<div class="flex h-full">

    {{-- ══ SIDEBAR ══════════════════════════════════════════════════════════ --}}
    <aside id="sidebar"
           :class="{
             'w-16': collapsed,
             'w-64': !collapsed,
             '-translate-x-full': !sidebar,
             'translate-x-0': sidebar
           }"
           class="fixed inset-y-0 left-0 z-30 flex flex-col
                  bg-gradient-to-b from-[#0a1628] via-[#0f1f3d] to-[#1e3a8a]
                  shadow-2xl lg:relative lg:translate-x-0 transition-all duration-300">

        {{-- Logo --}}
        <div class="flex h-16 shrink-0 items-center gap-3 border-b border-white/10 px-4"
             :class="collapsed ? 'justify-center px-0' : 'px-4'">
            <button @click="toggleCollapsed()"
                    class="hidden lg:flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 shadow-lg hover:bg-white/20 transition-colors shrink-0">
                <i class="ti ti-ship text-xl text-white"></i>
            </button>
            <button @click="sidebar=false" class="lg:hidden text-white/60 hover:text-white shrink-0">
                <i class="ti ti-x"></i>
            </button>
            <div x-show="!collapsed" class="flex-1 min-w-0">
                <div class="text-sm font-bold text-white">ProjRoma</div>
                <div class="text-[10px] text-blue-300 truncate">{{ session('nombre_empresa') }}</div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5 text-sm"
             :class="collapsed ? 'px-2' : 'px-3'">
            @php $rol = auth()->user()->id_rol ?? 2; @endphp

            <x-nav-link href="{{ route('dashboard') }}" icon="ti ti-home" label="Dashboard" />

            <x-nav-group icon="ti ti-receipt" label="Facturación"
                         :active="request()->routeIs('ventas.*','guias.*','nota.electronica.*')">
                <x-nav-link href="{{ route('ventas.index') }}"           icon="ti ti-receipt"         label="Ventas" />
                <x-nav-link href="{{ route('guias.index') }}"            icon="ti ti-truck-delivery"  label="Guías de Remisión" />
                <x-nav-link href="{{ route('nota.electronica.lista') }}" icon="ti ti-file-invoice"    label="Notas Electrónicas" />
            </x-nav-group>

            <x-nav-group icon="ti ti-clipboard-list" label="Pedidos"
                         :active="request()->routeIs('cotizaciones.*')">
                <x-nav-link href="{{ route('cotizaciones.index') }}" icon="ti ti-clipboard-list" label="Pedidos / Cotizaciones" />
            </x-nav-group>

            <x-nav-group icon="ti ti-credit-card" label="Cobranzas"
                         :active="request()->routeIs('cobranzas.*')">
                <x-nav-link href="{{ route('cobranzas.index') }}"  icon="ti ti-credit-card"  label="Cuentas por Cobrar" />
                <x-nav-link href="{{ route('cobranzas.deudas') }}" icon="ti ti-report-money" label="Reporte Deudas" />
                @if(in_array($rol,[3,4]))
                    <x-nav-link href="{{ route('cobranzas.miscobros') }}" icon="ti ti-wallet" label="Mis Cobros" />
                @endif
            </x-nav-group>

            <x-nav-group icon="ti ti-building-bank" label="Pagos"
                         :active="request()->routeIs('pagos.*','devoluciones.*')">
                <x-nav-link href="{{ route('pagos.index') }}"        icon="ti ti-building-bank" label="Cuentas por Pagar" />
                <x-nav-link href="{{ route('devoluciones.index') }}" icon="ti ti-arrow-back-up" label="Devoluciones" />
            </x-nav-group>

            <x-nav-group icon="ti ti-cash" label="Cajas"
                         :active="request()->routeIs('caja.*','pago.*')">
                <x-nav-link href="{{ route('caja.registros') }}" icon="ti ti-cash" label="Registro de Caja" />
                <x-nav-link href="{{ route('caja.flujo') }}"     icon="ti ti-coins"         label="Caja Chica" />
                <x-nav-link href="{{ route('caja.arqueo') }}"    icon="ti ti-calculator"    label="Arqueo Diario" />
                <x-nav-link href="{{ route('caja.micaja') }}" icon="ti ti-wallet" label="Mi Caja" />
                <x-nav-link href="{{ route('pago.instrumentos') }}" icon="ti ti-credit-card" label="Métodos de Pago" />
            </x-nav-group>

            <x-nav-group icon="ti ti-packages" label="Inventario"
                         :active="request()->routeIs('almacen.*','compras.*')">
                <x-nav-link href="{{ route('almacen.index') }}"     icon="ti ti-box"              label="Registro de Productos" />
                <x-nav-link href="{{ route('compras.index') }}"     icon="ti ti-shopping-cart"   label="Compras" />
                <x-nav-link href="{{ route('almacen.recepcion') }}" icon="ti ti-package-import"  label="Recepción" />
                <x-nav-link href="{{ route('almacen.almacen') }}"   icon="ti ti-archive"         label="Almacén" />
                <x-nav-link href="{{ route('almacen.kardex') }}"    icon="ti ti-history"         label="Kardex" />
                <x-nav-link href="{{ route('almacen.ajustes') }}"   icon="ti ti-adjustments"     label="Ajustes / Cuadres" />
                <x-nav-link href="{{ route('almacen.traslado') }}"  icon="ti ti-arrows-exchange" label="Traslado de Stock" />
                <x-nav-link href="{{ route('almacen.prestamos') }}" icon="ti ti-arrows-left-right" label="Préstamos de Productos" />
            </x-nav-group>

            <x-nav-group icon="ti ti-users" label="Maestros"
                         :active="request()->routeIs('clientes.*','proveedores.*')">
                <x-nav-link href="{{ route('clientes.index') }}"    icon="ti ti-users"          label="Clientes" />
                <x-nav-link href="{{ route('proveedores.index') }}" icon="ti ti-building-store" label="Proveedores" />
            </x-nav-group>

            @if($rol == 1)
                <x-nav-group icon="ti ti-settings" label="Administración"
                             :active="request()->routeIs('usuarios.*','admin.*')">
                    <x-nav-link href="{{ route('usuarios.index') }}" icon="ti ti-user-cog" label="Usuarios" />
                    <x-nav-link href="{{ route('admin.sucursales') }}" icon="ti ti-building-store" label="Sucursales" />
                    <x-nav-link href="{{ route('admin.empresas') }}" icon="ti ti-building" label="Empresas" />
                </x-nav-group>
            @endif
        </nav>

        {{-- Toggle collapse --}}
        <div class="hidden lg:flex shrink-0 border-t border-white/10 p-3" :class="collapsed ? 'justify-center p-2' : ''">
            <button @click="toggleCollapsed()"
                    class="flex items-center justify-center w-full gap-2 rounded-lg px-3 py-2 text-blue-300/60 hover:text-white hover:bg-white/10 transition-all text-xs"
                    :class="collapsed ? 'w-9 h-9 p-0' : ''">
                <i class="ti ti-menu-deep text-sm"
                   :class="collapsed ? 'rotate-180' : ''"
                   style="transition:transform .3s"></i>
                <span x-show="!collapsed" class="text-xs">Colapsar</span>
            </button>
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

            <span class="hidden md:block text-xs text-gray-400 shrink-0">{{ now()->format('d/m/Y') }}</span>

            {{-- Menú de usuario --}}
            <div class="relative shrink-0" x-data="{ open: false }">
                <button @click="open=!open"
                        class="flex items-center gap-2.5 rounded-xl px-2 py-1.5 hover:bg-gray-100 transition-colors">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->nombres ?? 'U', 0, 2)) }}
                    </div>
                    <div class="hidden sm:block text-left leading-tight">
                        <div class="truncate max-w-[140px] text-xs font-semibold text-gray-700">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</div>
                        <div class="text-[10px] text-gray-400">{{ auth()->user()->rol?->nombre ?? 'Usuario' }} · Suc.{{ session('sucursal') }}</div>
                    </div>
                    <i class="ti ti-chevron-down text-sm text-gray-400 hidden sm:block" :class="open && 'rotate-180'" style="transition:transform .2s"></i>
                </button>

                <div x-show="open" x-cloak @click.outside="open=false"
                     x-transition.origin.top.right
                     class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl z-50">
                    <div class="border-b border-gray-100 px-4 py-3 sm:hidden">
                        <div class="truncate text-xs font-semibold text-gray-700">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</div>
                        <div class="text-[10px] text-gray-400">{{ auth()->user()->rol?->nombre ?? 'Usuario' }} · Suc.{{ session('sucursal') }}</div>
                    </div>
                    <div class="flex items-center gap-1 px-4 py-2 text-[10px] font-semibold text-brand-700">
                        <i class="ti ti-building text-[10px]"></i> Sucursal {{ session('sucursal') }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center gap-2 px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 transition-colors">
                            <i class="ti ti-logout text-sm"></i> Cerrar sesión
                        </button>
                    </form>
                </div>
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
{{-- Extensiones DataTables --}}
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

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

    /**
     * Inicializa una tabla DataTable con las capacidades del sistema:
     *  • Responsive  → en móvil cada fila se vuelve una tarjeta (toca el + para ver todo)
     *  • ColReorder  → arrastra el encabezado para mover columnas de lugar
     *  • colVis      → un solo ícono arriba para mostrar/ocultar columnas
     *  • stateSave   → recuerda orden/visibilidad de columnas por tabla
     *  • min-height fijo (vía CSS .dataTables_wrapper) aunque no haya datos
     *
     * @param {string} selector  ej. '#tblProductos'
     * @param {object} options   opciones propias (ajax, columns, order, ...) — se fusionan
     */
    window.initDataTable = function (selector, options = {}) {
        const base = {
            responsive: true,
            colReorder: true,
            stateSave: true,
            stateDuration: 60 * 60 * 24 * 30,   // 30 días
            deferRender: true,
            autoWidth: false,
            pageLength: 25,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
            // El buscador es el componente x-search. Aquí solo: tabla(t) + Show(l)+info(i)+paginación(p)
            dom: 'rt<"dt-foot flex flex-wrap items-center justify-between gap-3 mt-4"<"flex items-center gap-3"li>p>',
        };
        const dt = $(selector).DataTable($.extend(true, {}, base, options));

        // Ícono ÚNICO de mostrar/ocultar columnas, ubicado en el header de la tabla
        const id    = selector.replace(/^#/, '');
        const tools = document.getElementById(id + '-tools');
        if (tools) {
            tools.innerHTML = '';   // evita duplicados al re-inicializar
            new $.fn.dataTable.Buttons(dt, {
                buttons: [{
                    extend: 'colvis',
                    text: '<i class="ti ti-columns-3"></i>',
                    titleAttr: 'Mostrar / ocultar columnas',
                    className: 'dt-icon-btn',
                    columns: ':not(.no-colvis)',   // .no-colvis = columnas que no se pueden ocultar
                }],
            });
            dt.buttons().container().appendTo(tools);
        }
        return dt;
    };

    // Conecta cualquier campo x-search (atributo data-dt-search) con su DataTable
    $(document).on('input', 'input[data-dt-search]', function () {
        const t = document.getElementById(this.dataset.dtSearch);
        if (t && $.fn.dataTable.isDataTable(t)) $(t).DataTable().search(this.value).draw();
    });
</script>

@stack('scripts')
</body>
</html>
