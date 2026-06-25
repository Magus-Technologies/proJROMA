@extends('layouts.app')
@section('title','Registro de Productos')
@section('page-title','Registro de Productos')
@section('breadcrumb','Inventario / Registro de Productos')

@section('content')
@php
    $tabs = [
        'productos'     => 'Productos',
        'categorias'    => 'Categorías',
        'subcategorias' => 'Subcategorías',
        'marcas'        => 'Marcas',
        'submarcas'     => 'Submarcas',
    ];
    // tipos taxonomía con su etiqueta de "padre" (null = sin padre)
    $taxs = [
        'categorias'    => ['label' => 'Categoría',    'parent' => null],
        'subcategorias' => ['label' => 'Subcategoría', 'parent' => 'Categoría'],
        'marcas'        => ['label' => 'Marca',        'parent' => null],
        'submarcas'     => ['label' => 'Submarca',     'parent' => 'Marca'],
    ];
@endphp

<div x-data="{ tab: 'productos' }">

    {{-- ══ Tabs ══ --}}
    <div class="mb-4 flex flex-wrap gap-1 border-b border-gray-200">
        @foreach($tabs as $key => $label)
            <button @click="tab='{{ $key }}'; window.taxTab && taxTab('{{ $key }}')"
                    :class="tab==='{{ $key }}' ? 'border-brand-600 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="-mb-px border-b-2 px-4 py-2 text-xs font-semibold transition-colors">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ══ Tab: Productos ══ --}}
    <div x-show="tab==='productos'">
        <div class="mb-4 flex flex-wrap gap-2">
            <button onclick="abrirModalNuevo()" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition">
                <i class="ti ti-plus"></i> Nuevo Producto
            </button>
            <button onclick="exportarExcel()" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2 text-xs font-medium text-gray-700 transition">
                <i class="ti ti-file-type-xls text-emerald-500"></i> Excel
            </button>
        </div>

        <x-table id="tblProductos" title="Productos">
            <x-slot:filters>
                <select id="filtroAlmacen" onchange="cambiarAlmacen()" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs">
                    <option value="1">Almacén 1</option>
                    <option value="2">Almacén 2</option>
                    <option value="3">Almacén 3</option>
                </select>
            </x-slot:filters>
            <x-slot:thead>
                <x-th>Código</x-th>
                <x-th>Descripción</x-th>
                <x-th>Categoría</x-th>
                <x-th>Marca</x-th>
                <x-th align="right">Precio</x-th>
                <x-th align="center">Stock</x-th>
                <x-th align="center">Acciones</x-th>
            </x-slot:thead>
        </x-table>
    </div>

    {{-- ══ Tabs: Categorías / Subcategorías / Marcas / Submarcas ══ --}}
    @foreach($taxs as $tipo => $c)
        <div x-show="tab==='{{ $tipo }}'" x-cloak>
            <x-table id="tbl-{{ $tipo }}" title="{{ $tabs[$tipo] }}">
                <x-slot:filters>
                    <x-btn color="primary" icon="ti ti-plus" onclick="abrirTaxModal('{{ $tipo }}')">Agregar</x-btn>
                </x-slot:filters>
                <x-slot:thead>
                    <x-th>Nombre</x-th>
                    <x-th>Descripción</x-th>
                    @if($c['parent'])<x-th>{{ $c['parent'] }}</x-th>@endif
                    <x-th align="center">Estado</x-th>
                    <x-th align="center">Acciones</x-th>
                </x-slot:thead>
            </x-table>
        </div>
    @endforeach
</div>

{{-- ══ Modales: Agregar Categoría / Subcategoría / Marca / Submarca ══ --}}
@foreach($taxs as $tipo => $c)
    <x-modal id="md-{{ $tipo }}" title="{{ $c['label'] }}" size="max-w-md">
        <input type="hidden" id="md-{{ $tipo }}-id">
        <div class="space-y-4">
            @if($c['parent'])
                <x-input-group label="{{ $c['parent'] }}" :required="true">
                    <x-select id="md-{{ $tipo }}-parent" />
                </x-input-group>
            @endif
            <x-input-group label="Nombre" :required="true">
                <x-input id="md-{{ $tipo }}-nombre" maxlength="150" placeholder="Nombre de {{ $c['label'] }}"
                         onkeydown="if(event.key==='Enter')taxGuardar('{{ $tipo }}')" />
            </x-input-group>
            <x-input-group label="Descripción">
                <x-input id="md-{{ $tipo }}-desc" maxlength="255" placeholder="Descripción (opcional)" />
            </x-input-group>
        </div>
        <x-slot:footer>
            <x-btn color="ghost" onclick="cerrarModal('md-{{ $tipo }}')">Cancelar</x-btn>
            <x-btn color="primary" icon="ti ti-device-floppy" onclick="taxGuardar('{{ $tipo }}')">Guardar</x-btn>
        </x-slot:footer>
    </x-modal>
@endforeach

{{-- ══ Modal Producto ══ --}}
<div id="mdProducto" class="fixed inset-0 z-50 hidden items-start justify-center pt-10 px-4">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalProd()"></div>
    <div class="relative z-10 w-full max-w-2xl rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-4">
            <h4 class="text-sm font-semibold text-gray-700" id="mdTitulo">Nuevo Producto</h4>
            <button onclick="cerrarModalProd()" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
        </div>
        <div class="p-5 grid grid-cols-2 gap-4 max-h-[70vh] overflow-y-auto">
            <input type="hidden" id="pid">
            <div class="col-span-2">
                <x-label :required="true">Descripción</x-label>
                <input id="pdesc" type="text" maxlength="245" placeholder="Nombre del producto" class="field">
            </div>

            {{-- Clasificación --}}
            <div>
                <x-label :optional="true">Categoría</x-label>
                <select id="p_categoria" onchange="onProdCatChange()" class="field bg-white"></select>
            </div>
            <div>
                <x-label :optional="true">Subcategoría</x-label>
                <select id="p_subcategoria" class="field bg-white"></select>
            </div>
            <div>
                <x-label :optional="true">Marca</x-label>
                <select id="p_marca" onchange="onProdMarcaChange()" class="field bg-white"></select>
            </div>
            <div>
                <x-label :optional="true">Submarca</x-label>
                <select id="p_submarca" class="field bg-white"></select>
            </div>

            <div>
                <x-label :optional="true">Código</x-label>
                <input id="pcod" type="text" maxlength="50" class="field">
            </div>
            <div>
                <x-label :optional="true">Código de Barra</x-label>
                <input id="pbarra" type="text" maxlength="100" class="field">
            </div>
            <div>
                <x-label :required="true">Precio Venta</x-label>
                <input id="pprecio" type="number" step="0.01" min="0" placeholder="0.00" class="field">
            </div>
            <div>
                <x-label :optional="true">Costo</x-label>
                <input id="pcosto" type="number" step="0.01" min="0" placeholder="0.00" class="field">
            </div>
            <div>
                <x-label :optional="true">Precio 2</x-label>
                <input id="pprecio2" type="number" step="0.01" min="0" placeholder="0.00" class="field">
            </div>
            <div>
                <x-label :optional="true">Precio 3</x-label>
                <input id="pprecio3" type="number" step="0.01" min="0" placeholder="0.00" class="field">
            </div>
            <div>
                <x-label :optional="true">Stock</x-label>
                <input id="pcantidad" type="number" step="1" min="0" placeholder="0" class="field">
            </div>
            <div>
                <x-label :optional="true">Almacén</x-label>
                <select id="palmacen" class="field bg-white">
                    <option value="1">Almacén 1</option>
                    <option value="2">Almacén 2</option>
                    <option value="3">Almacén 3</option>
                </select>
            </div>
            <div>
                <x-label :optional="true">Código SUNAT</x-label>
                <input id="psunat" type="text" maxlength="20" placeholder="ZZ" class="field">
            </div>
            <div>
                <x-label :optional="true">Afectación IGV</x-label>
                <select id="piscbp" class="field bg-white">
                    <option value="0">Gravado</option>
                    <option value="1">Exonerado</option>
                    <option value="2">Inafecto</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-3">
            <button onclick="cerrarModalProd()" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">Cancelar</button>
            <button onclick="guardarProducto()" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-semibold text-white">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
let tabla;
const g = id => document.getElementById(id);

/* ════════ TABLA PRODUCTOS ════════ */
$(function () { cargarTabla(1); });

function cargarTabla(almacen) {
    if (tabla) { tabla.destroy(); $('#tblProductos tbody').empty(); }
    tabla = initDataTable('#tblProductos', {
        processing: true, serverSide: true,
        ajax: {
            url: BASE + '/api/productos/serverside',
            data: d => { d.almacenId = almacen; },
            headers: { 'Accept':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
            beforeSend: () => $('#tblProductos-loading').removeClass('hidden'),
            complete:   () => $('#tblProductos-loading').addClass('hidden'),
        },
        columns: [
            { data:'codigo', defaultContent:'-' },
            { data:'descripcion', responsivePriority:1 },
            { data:'categoria_nombre', defaultContent:'-', orderable:false, searchable:false },
            { data:'marca_nombre', defaultContent:'-', orderable:false, searchable:false },
            { data:'precio', className:'text-right', render: v => 'S/ ' + parseFloat(v||0).toFixed(2) },
            { data:'cantidad', className:'text-center font-bold',
              render: v => `<span class="${parseInt(v)<=5?'text-red-600':'text-emerald-600'}">${v}</span>` },
            { data:'id_producto', orderable:false, responsivePriority:2, className:'text-center no-colvis',
              render: id => `<div class="flex justify-center gap-1">
                <button onclick="editarProducto(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-pencil text-sm"></i></button>
                <button onclick="eliminarProducto(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-sm"></i></button>
              </div>` },
        ],
        order:[[1,'asc']],
    });
}
function cambiarAlmacen() { cargarTabla(g('filtroAlmacen').value); }

/* ════════ MODAL PRODUCTO ════════ */
function abrirModalProd()  { g('mdProducto').classList.replace('hidden','flex'); }
function cerrarModalProd() { g('mdProducto').classList.replace('flex','hidden'); }

function fillSel(id, rows, pk, lbl) {
    g(id).innerHTML = `<option value="">— ${lbl} —</option>` +
        rows.map(r => `<option value="${r[pk]}">${r.nombre}</option>`).join('');
}

async function loadProdClasif() {
    const [cats, marcas] = await Promise.all([
        apiGet(`${BASE}/api/catalogo/categorias`, { activos: 1 }),
        apiGet(`${BASE}/api/catalogo/marcas`, { activos: 1 }),
    ]);
    fillSel('p_categoria', cats, 'id_categoria', 'Categoría');
    fillSel('p_marca', marcas, 'id_marca', 'Marca');
    fillSel('p_subcategoria', [], 'id_subcategoria', 'Subcategoría');
    fillSel('p_submarca', [], 'id_submarca', 'Submarca');
}
async function onProdCatChange(cid = null) {
    cid = cid ?? g('p_categoria').value;
    const subs = cid ? await apiGet(`${BASE}/api/catalogo/subcategorias`, { parent: cid, activos: 1 }) : [];
    fillSel('p_subcategoria', subs, 'id_subcategoria', 'Subcategoría');
}
async function onProdMarcaChange(mid = null) {
    mid = mid ?? g('p_marca').value;
    const subs = mid ? await apiGet(`${BASE}/api/catalogo/submarcas`, { parent: mid, activos: 1 }) : [];
    fillSel('p_submarca', subs, 'id_submarca', 'Submarca');
}

async function abrirModalNuevo() {
    g('mdTitulo').textContent = 'Nuevo Producto';
    ['pid','pdesc','pcod','pbarra','pprecio','pcosto','pprecio2','pprecio3','pcantidad','psunat'].forEach(id => g(id).value = '');
    g('palmacen').value = '1'; g('piscbp').value = '0';
    await loadProdClasif();
    abrirModalProd();
}

async function editarProducto(id) {
    const d = await apiPost(BASE + '/api/productos/get-one', { id_producto: id });
    g('mdTitulo').textContent = 'Editar Producto';
    g('pid').value = d.id_producto; g('pdesc').value = d.descripcion || ''; g('pcod').value = d.codigo || '';
    g('pbarra').value = d.cod_barra || ''; g('pprecio').value = d.precio || 0; g('pcosto').value = d.costo || 0;
    g('pprecio2').value = d.precio2 || 0; g('pprecio3').value = d.precio3 || 0; g('pcantidad').value = d.cantidad || 0;
    g('palmacen').value = d.almacen || '1'; g('psunat').value = d.codsunat || ''; g('piscbp').value = d.iscbp || 0;

    await loadProdClasif();
    g('p_categoria').value = d.id_categoria || '';
    g('p_marca').value = d.id_marca || '';
    await Promise.all([ onProdCatChange(d.id_categoria), onProdMarcaChange(d.id_marca) ]);
    g('p_subcategoria').value = d.id_subcategoria || '';
    g('p_submarca').value = d.id_submarca || '';

    abrirModalProd();
}

async function guardarProducto() {
    const id = g('pid').value, desc = g('pdesc').value.trim(), prec = g('pprecio').value;
    if (!desc || !prec) { toastWarn('Descripción y precio son obligatorios.'); return; }
    const payload = {
        descripcion: desc, precio: parseFloat(prec),
        costo: parseFloat(g('pcosto').value||0), precio2: parseFloat(g('pprecio2').value||0),
        precio3: parseFloat(g('pprecio3').value||0), cantidad: parseInt(g('pcantidad').value||0),
        codigo: g('pcod').value, cod_barra: g('pbarra').value,
        almacen: g('palmacen').value, codsunat: g('psunat').value, iscbp: parseInt(g('piscbp').value),
        id_categoria:    g('p_categoria').value    || null,
        id_subcategoria: g('p_subcategoria').value || null,
        id_marca:        g('p_marca').value        || null,
        id_submarca:     g('p_submarca').value     || null,
    };
    if (id) payload.id_producto = parseInt(id);
    const url  = id ? BASE + '/api/productos/editar' : BASE + '/api/productos/add';
    const data = await apiPost(url, payload);
    if (data.res) { toastOk(id ? 'Producto actualizado.' : 'Producto registrado.'); cerrarModalProd(); tabla.ajax.reload(null,false); }
    else toastErr(data.msg || 'Error al guardar.');
}

async function eliminarProducto(id) {
    const { isConfirmed } = await Swal.fire({ title:'¿Dar de baja?', text:'Se marcará como inactivo.', icon:'warning', showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonText:'Cancelar', confirmButtonText:'Sí, dar de baja' });
    if (!isConfirmed) return;
    const d = await apiPost(BASE + '/api/productos/borrar', { id_producto: id });
    if (d.res) { toastOk('Producto dado de baja.'); tabla.ajax.reload(null,false); }
    else Swal.fire({ icon:'warning', title:'No se puede eliminar', text: d.msg || 'Ocurrió un error.', confirmButtonColor:'#1d4ed8' });
}

function exportarExcel() { window.open(BASE + '/reporte/producto/excel', '_blank'); }

/* ════════ TAXONOMÍAS (Categorías / Subcategorías / Marcas / Submarcas) ════════ */
const TAX = {
    categorias:    { pk:'id_categoria',    parent:null },
    subcategorias: { pk:'id_subcategoria', parent:{ tipo:'categorias', col:'id_categoria', lbl:'Categoría' } },
    marcas:        { pk:'id_marca',        parent:null },
    submarcas:     { pk:'id_submarca',     parent:{ tipo:'marcas', col:'id_marca', lbl:'Marca' } },
};

const tablasTax = {};

// Se llama al cambiar de tab: inicializa la tabla (1ra vez) o la refresca
window.taxTab = function (tipo) {
    if (!TAX[tipo]) return;            // tab Productos
    if (tablasTax[tipo]) { taxReload(tipo); return; }

    const cfg  = TAX[tipo];
    const cols = [
        { data: 'nombre', responsivePriority: 1 },
        { data: 'descripcion', defaultContent: '-', orderable: false },
    ];
    if (cfg.parent) cols.push({ data: 'parent_nombre', defaultContent: '-', orderable: false, searchable: false });
    cols.push({
        data: 'estado', orderable: false, searchable: false, responsivePriority: 3, className: 'text-center',
        render: v => v === '1'
            ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activo</span>'
            : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactivo</span>',
    });
    cols.push({
        data: cfg.pk, orderable: false, searchable: false, responsivePriority: 2, className: 'text-center no-colvis',
        render: (id, t, row) => `<div class="flex justify-center gap-1">
            <button onclick="taxEditOpen('${tipo}',${id})" title="Editar" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-pencil text-sm"></i></button>
            ${row.estado === '1'
                ? `<button onclick="taxToggle('${tipo}',${id})" title="Desactivar" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-ban text-sm"></i></button>`
                : `<button onclick="taxToggle('${tipo}',${id})" title="Activar" class="h-7 w-7 flex items-center justify-center rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600"><i class="ti ti-circle-check text-sm"></i></button>`}
        </div>`,
    });

    tablasTax[tipo] = initDataTable('#tbl-' + tipo, {
        ajax: {
            url: `${BASE}/api/catalogo/${tipo}`, dataSrc: '',
            beforeSend: () => $(`#tbl-${tipo}-loading`).removeClass('hidden'),
            complete:   () => $(`#tbl-${tipo}-loading`).addClass('hidden'),
        },
        columns: cols,
        order: [[0, 'asc']],
    });
    // Ajusta anchos/responsive (la tabla estaba oculta al inicializar)
    setTimeout(() => tablasTax[tipo].columns.adjust().responsive.recalc(), 60);
};

function taxReload(tipo) {
    if (tablasTax[tipo]) tablasTax[tipo].ajax.reload(null, false);
}

// Abre el modal para AGREGAR (row=null) o EDITAR (row con datos)
async function abrirTaxModal(tipo, row = null) {
    const cfg = TAX[tipo];
    g(`md-${tipo}-id`).value     = row ? row[cfg.pk] : '';
    g(`md-${tipo}-nombre`).value = row ? (row.nombre || '') : '';
    g(`md-${tipo}-desc`).value   = row ? (row.descripcion || '') : '';
    if (cfg.parent) {
        const opts = await apiGet(`${BASE}/api/catalogo/${cfg.parent.tipo}`, { activos: 1 });
        fillSel(`md-${tipo}-parent`, opts, TAX[cfg.parent.tipo].pk, cfg.parent.lbl);
        g(`md-${tipo}-parent`).value = row ? (row[cfg.parent.col] || '') : '';
    }
    abrirModal('md-' + tipo);
    setTimeout(() => g(`md-${tipo}-nombre`).focus(), 100);
}

// Busca la fila en la tabla y abre el modal en modo edición
function taxEditOpen(tipo, id) {
    const cfg = TAX[tipo];
    const row = tablasTax[tipo].rows().data().toArray().find(r => String(r[cfg.pk]) === String(id));
    abrirTaxModal(tipo, row);
}

async function taxGuardar(tipo) {
    const cfg = TAX[tipo];
    const id  = g(`md-${tipo}-id`).value;
    const nombre = g(`md-${tipo}-nombre`).value.trim();
    if (!nombre) { toastWarn('Escribe un nombre.'); return; }
    const payload = { nombre, descripcion: g(`md-${tipo}-desc`).value.trim() };
    if (cfg.parent) {
        const pid = g(`md-${tipo}-parent`).value;
        if (!pid) { toastWarn(`Selecciona ${cfg.parent.lbl}.`); return; }
        payload[cfg.parent.col] = pid;
    }
    if (id) payload.id = id;
    const url = id ? `${BASE}/api/catalogo/${tipo}/editar` : `${BASE}/api/catalogo/${tipo}`;
    const d = await apiPost(url, payload);
    if (d.res) { toastOk(id ? 'Actualizado.' : 'Guardado.'); cerrarModal('md-' + tipo); taxReload(tipo); }
    else toastErr(d.msg || 'Error al guardar.');
}

// Activa / desactiva (con validación de integridad al desactivar)
async function taxToggle(tipo, id) {
    const d = await apiPost(`${BASE}/api/catalogo/${tipo}/toggle`, { id });
    if (d.res) { toastOk(d.estado === '1' ? 'Activado.' : 'Desactivado.'); taxReload(tipo); }
    else Swal.fire({ icon:'warning', title:'No se puede desactivar', text: d.msg || 'Ocurrió un error.', confirmButtonColor:'#1d4ed8' });
}
</script>
@endpush
