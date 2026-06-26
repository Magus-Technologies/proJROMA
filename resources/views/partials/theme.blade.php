{{--
  ═══════════════════════════════════════════════════════════════════════════
  SISTEMA DE DISEÑO  ·  ProjRoma
  ───────────────────────────────────────────────────────────────────────────
  Fuente única de colores y estilos. Todos los componentes reutilizables
  (resources/views/components/*) heredan de aquí.

  ▸ Para cambiar un color de TODO el sistema, edítalo SOLO en este archivo.
  ▸ Paleta:  brand (azul corporativo) · navy (sidebar) + grises/estados Tailwind
  ═══════════════════════════════════════════════════════════════════════════
--}}

{{-- ── 1. PALETA Y FUENTES (config de Tailwind) ───────────────────────────── --}}
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Plus Jakarta Sans', 'sans-serif'],
                    mono: ['JetBrains Mono', 'monospace'],
                },
                colors: {
                    // Azul corporativo — botones, enlaces, foco, acentos
                    brand: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#1d4ed8',700:'#1e40af',800:'#1e3a8a',900:'#1c2e6e' },
                    // Azul profundo — fondo del sidebar
                    navy:  { 800:'#0f1f3d', 900:'#0a1628' },
                },
            },
        },
    };
</script>

{{-- ── 2. ESTILOS DE COMPONENTES (se procesan con la paleta de arriba) ─────── --}}
<style type="text/tailwindcss">
    @layer components {

        /* ---- Botones (x-btn, x-action-btn) ---------------------------------- */
        .btn          { @apply inline-flex items-center justify-center gap-1.5 rounded-xl font-semibold shadow-sm transition disabled:opacity-50 disabled:pointer-events-none; }
        .btn-xs       { @apply px-3 py-1.5 text-[11px]; }
        .btn-sm       { @apply px-4 py-2 text-xs; }
        .btn-md       { @apply px-5 py-2.5 text-sm; }
        .btn-primary  { @apply bg-brand-500 text-white hover:bg-brand-600; }
        .btn-danger   { @apply bg-red-600 text-white hover:bg-red-700; }
        .btn-emerald  { @apply bg-emerald-600 text-white hover:bg-emerald-700; }
        .btn-outline  { @apply border border-gray-200 bg-white text-gray-700 hover:bg-gray-50; }
        .btn-ghost    { @apply bg-transparent text-gray-600 shadow-none hover:bg-gray-100; }

        /* ---- Tarjetas (x-card, x-card-header) ------------------------------- */
        .card               { @apply rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden; }
        .card-body          { @apply p-4 sm:p-5; }
        .card-header        { @apply flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 px-4 py-3 sm:px-5 sm:py-4; }
        .card-header__title { @apply text-sm font-semibold text-gray-700; margin:0; }

        /* ---- Campos de formulario (x-input, x-select) ----------------------- */
        .field { @apply w-full rounded-lg border border-gray-200 px-3 py-2 text-sm transition focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-400/40; }

        /* ---- Buscador (x-search) — estilo único para todo el sistema -------- */
        .search-input { @apply w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-3 text-sm text-gray-700 transition placeholder:text-gray-400 focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-400/40; }
    }
</style>

{{-- ── 3. UTILIDADES GLOBALES (CSS puro: animaciones, scrollbar, etc.) ─────── --}}
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

    /* Indicadores de campo: obligatorio (asterisco pulsante) / opcional (badge) */
    .req-star{color:#ef4444;margin-left:2px;font-weight:700;display:inline-block;animation:reqPulse 1.6s ease-in-out infinite}
    @keyframes reqPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.35;transform:scale(1.3)}}
    .opt-badge{margin-left:6px;font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:#94a3b8;background:#f1f5f9;padding:1px 6px;border-radius:9999px;vertical-align:middle}

    /* ═══ TABLAS (DataTables) ═══════════════════════════════════════════════ */

    /* La paginación va justo debajo de las filas (sin bloque blanco extra). */
    .dataTables_wrapper>.dt-foot{margin-top:.75rem;padding-top:.75rem;border-top:1px solid #f1f5f9}

    /* ---- Encabezado y filas (fondo de columnas + filas alternas) ---------- */
    table.dataTable{border-collapse:separate!important;border-spacing:0}
    table.dataTable thead th{
        background:#dbeafe;color:#1e40af;font-weight:700;font-size:11px;
        text-transform:uppercase;letter-spacing:.03em;
        padding:.7rem .75rem;border-bottom:1px solid #bfdbfe;white-space:nowrap;
    }
    table.dataTable thead th:first-child{border-top-left-radius:.6rem}
    table.dataTable thead th:last-child{border-top-right-radius:.6rem}
    table.dataTable tbody td{padding:.6rem .75rem;border-bottom:1px solid #f1f5f9;color:#334155}
    table.dataTable tbody tr:nth-child(even){background:#f8fafc}
    table.dataTable tbody tr:hover{background:#eff6ff}

    /* ---- Buscador (arriba) ----------------------------------------------- */
    .dataTables_wrapper .dataTables_filter{margin:0}
    .dataTables_wrapper .dataTables_filter label{font-size:0;color:transparent}
    .dataTables_wrapper .dataTables_filter input{
        margin:0;border:1px solid #e5e7eb;border-radius:.6rem;
        padding:.45rem .8rem;font-size:12px;min-width:220px;background:#fff;
        color:#374151!important;-webkit-text-fill-color:#374151!important;
    }
    .dataTables_wrapper .dataTables_filter input::placeholder{color:#9ca3af}
    .dataTables_wrapper .dataTables_filter input:focus{
        outline:none;border-color:#60a5fa;box-shadow:0 0 0 3px rgba(96,165,250,.25)
    }

    /* ---- Selector "Show 25" e info (abajo) ------------------------------- */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info{font-size:12px;color:#6b7280;padding:0;margin:0}
    .dataTables_wrapper .dataTables_length select{
        border:1px solid #e5e7eb;border-radius:.5rem;padding:.3rem 1.6rem .3rem .6rem;
        font-size:12px;background:#fff;margin:0 .35rem;cursor:pointer;
    }
    .dataTables_wrapper .dataTables_length select:focus{outline:none;border-color:#60a5fa}

    /* ---- Paginación (abajo, bien hecha) ---------------------------------- */
    .dataTables_wrapper .dataTables_paginate{display:flex;flex-wrap:wrap;gap:.3rem;padding:0}
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        display:inline-flex;align-items:center;justify-content:center;
        min-width:34px;height:34px;padding:0 .65rem;margin:0!important;
        border:1px solid #e5e7eb!important;border-radius:.6rem!important;
        background:#fff!important;color:#374151!important;font-size:12px;
        cursor:pointer;transition:background .15s,color .15s,border-color .15s;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover{
        background:#f3f4f6!important;color:#1d4ed8!important;border-color:#cbd5e1!important
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
        background:#1d4ed8!important;border-color:#1d4ed8!important;color:#fff!important;
        box-shadow:0 2px 6px rgba(29,78,216,.35)
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover{
        opacity:.4;cursor:not-allowed;background:#fff!important;color:#9ca3af!important;box-shadow:none
    }
    .dataTables_wrapper .dataTables_paginate .ellipsis{padding:0 .15rem;color:#9ca3af;align-self:center}

    /* ---- Botón de columnas: SOLO un ícono (sin caja ni borde) ------------- */
    .dt-button.dt-icon-btn{
        display:inline-flex;align-items:center;justify-content:center;
        height:34px;width:34px;padding:0;margin:0;font-size:19px;line-height:1;
        border:none!important;background:transparent!important;color:#64748b;
        box-shadow:none!important;cursor:pointer;transition:color .15s;
    }
    .dt-button.dt-icon-btn:hover,
    .dt-button.dt-icon-btn.dt-button-active{color:#1d4ed8;background:transparent!important}

    /* Panel desplegable de columnas */
    .dt-button-collection{
        border:1px solid #f1f5f9!important;border-radius:.9rem!important;
        box-shadow:0 12px 30px rgba(0,0,0,.12)!important;padding:.4rem!important;background:#fff;
    }
    .dt-button-collection .dt-button{
        border-radius:.5rem!important;font-size:12px!important;color:#374151!important;
        background:#fff!important;margin:1px 0!important;text-align:left;
    }
    .dt-button-collection .dt-button:hover{background:#f3f4f6!important}
    .dt-button-collection .dt-button.dt-button-active{background:#eff6ff!important;color:#1d4ed8!important}

    /* ColReorder: el encabezado se puede arrastrar */
    table.dataTable thead th{cursor:move}
    .DTCR-pointer{background:#1d4ed8!important}

    /* Control responsive (+/–) */
    table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before{
        background-color:#1d4ed8;border:none;box-shadow:none;color:#fff
    }
    table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td.dtr-control:before{background-color:#dc2626}

    /* ═══ Vista TARJETAS en móvil ═══════════════════════════════════════════ */
    @media (max-width:640px){
        table.dataTable>tbody>tr.parent{background:#fff;box-shadow:inset 0 0 0 1px #e5e7eb;border-radius:.6rem}
        table.dataTable>tbody>tr.child{background:#f8fafc}
        ul.dtr-details{display:block;width:100%;padding:.25rem 0}
        ul.dtr-details>li{display:flex;justify-content:space-between;gap:1rem;border-bottom:1px solid #f1f5f9;padding:.45rem .25rem}
        ul.dtr-details>li:last-child{border-bottom:none}
        .dtr-details .dtr-title{font-weight:600;color:#6b7280;min-width:90px}
        .dtr-details .dtr-data{text-align:right;color:#111827}
        .dataTables_wrapper .dataTables_filter input{min-width:0;width:100%}
        .dataTables_wrapper .dataTables_paginate .paginate_button{min-width:30px;height:30px;padding:0 .4rem}
    }
</style>
