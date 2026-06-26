@extends('layouts.app')
@section('title','Administrar Empresas')
@section('page-title','Administrar Empresas')
@section('breadcrumb','Admin / Empresas')

@section('content')

<x-alert type="success" />
<x-alert type="error" />

<div class="mb-4 flex flex-wrap gap-2">
    <button id="btnNuevaEmpresa" onclick="abrirModalNuevo()"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed px-4 py-2 text-xs font-semibold text-white shadow-sm transition">
        <i class="ti ti-plus"></i> Nueva Empresa
    </button>
    <span id="btnNuevaEmpresaHint" class="hidden items-center text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
        <i class="ti ti-info-circle mr-1"></i> Solo puede existir una empresa registrada.
    </span>
</div>

<x-table id="tblEmpresas" title="Lista de Empresas">
    <x-slot:thead>
        <x-th>RUC</x-th>
        <x-th>Razón Social</x-th>
        <x-th>Comercial</x-th>
        <x-th>Distrito</x-th>
        <x-th>Estado</x-th>
        <x-th align="center">Acciones</x-th>
    </x-slot:thead>
</x-table>

{{-- Modal Empresa --}}
<x-modal id="mdEmpresa" title="Nueva Empresa" titleId="mdTitle" maxWidth="max-w-3xl">
    <form id="frmEmpresa" class="space-y-5">
        <input type="hidden" id="f_id">

        {{-- Datos Principales --}}
        <fieldset>
            <legend class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Datos Principales</legend>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="col-span-2 sm:col-span-1">
                    <x-label for="f_ruc" required>RUC</x-label>
                    <div class="flex gap-2">
                        <x-input id="f_ruc" type="text" maxlength="11" placeholder="20123456789" required class="flex-1" />
                        <button type="button" onclick="buscarRuc()" class="rounded-lg bg-blue-50 hover:bg-blue-100 px-3 text-blue-600 transition" title="Consultar RUC">
                            <i class="ti ti-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-span-2 sm:col-span-2">
                    <x-label for="f_razon" required>Razón Social</x-label>
                    <x-input id="f_razon" type="text" maxlength="245" placeholder="Razón social de la empresa" required class="w-full" />
                </div>
                <div class="col-span-2 sm:col-span-3">
                    <x-label for="f_comercial">Nombre Comercial</x-label>
                    <x-input id="f_comercial" type="text" maxlength="245" placeholder="Nombre comercial (opcional)" class="w-full" />
                </div>
            </div>
        </fieldset>

        {{-- Ubicación --}}
        <fieldset>
            <legend class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Ubicación</legend>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="col-span-2">
                    <x-label for="f_direccion">Dirección</x-label>
                    <x-input id="f_direccion" type="text" maxlength="245" placeholder="Av. Principal 123" class="w-full" />
                </div>
                <div>
                    <x-label for="f_distrito">Distrito</x-label>
                    <x-input id="f_distrito" type="text" maxlength="45" placeholder="Lima" class="w-full" />
                </div>
                <div>
                    <x-label for="f_provincia">Provincia</x-label>
                    <x-input id="f_provincia" type="text" maxlength="45" placeholder="Lima" class="w-full" />
                </div>
                <div>
                    <x-label for="f_departamento">Departamento</x-label>
                    <x-input id="f_departamento" type="text" maxlength="45" placeholder="Lima" class="w-full" />
                </div>
                <div>
                    <x-label for="f_ubigeo">Ubigeo</x-label>
                    <x-input id="f_ubigeo" type="text" maxlength="6" placeholder="150101" class="w-full" />
                </div>
                <div>
                    <x-label for="f_sucursal">Cod. Sucursal</x-label>
                    <x-input id="f_sucursal" type="text" maxlength="4" placeholder="0001" class="w-full" />
                </div>
            </div>
        </fieldset>

        {{-- Contacto --}}
        <fieldset>
            <legend class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Contacto</legend>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="col-span-2 sm:col-span-1">
                    <x-label for="f_email">Email</x-label>
                    <x-input id="f_email" type="email" maxlength="145" placeholder="correo@empresa.com" class="w-full" />
                </div>
                <div>
                    <x-label for="f_tel1">Teléfono 1</x-label>
                    <x-input id="f_tel1" type="text" maxlength="30" placeholder="014567890" class="w-full" />
                </div>
                <div>
                    <x-label for="f_tel2">Teléfono 2</x-label>
                    <x-input id="f_tel2" type="text" maxlength="30" placeholder="999888777" class="w-full" />
                </div>
                <div>
                    <x-label for="f_tel3">Teléfono 3</x-label>
                    <x-input id="f_tel3" type="text" maxlength="30" placeholder="999888777" class="w-full" />
                </div>
            </div>
        </fieldset>

        {{-- Credenciales SUNAT --}}
        <fieldset>
            <legend class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Credenciales SUNAT</legend>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="f_user_sol">Usuario SOL</x-label>
                    <x-input id="f_user_sol" type="text" maxlength="45" placeholder=" Usuario SOL" class="w-full" />
                </div>
                <div>
                    <x-label for="f_clave_sol">Clave SOL</x-label>
                    <x-input id="f_clave_sol" type="text" maxlength="45" placeholder=" Clave SOL" class="w-full" />
                </div>
            </div>
        </fieldset>

        {{-- Certificado Digital (solo al editar) --}}
        <fieldset id="fsCertificado" class="hidden">
            <legend class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Certificado Digital (.pem)</legend>
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4 space-y-3">
                <p class="text-xs text-gray-500">Subí el certificado digital en formato <strong>.pem</strong> para habilitar la firma electrónica de comprobantes SUNAT.</p>
                <div class="flex items-center gap-3">
                    <input type="file" id="f_certificado" accept=".pem,.pfx,.p12"
                           class="flex-1 text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <button type="button" onclick="subirCertificado()"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-2 text-xs font-semibold text-white transition">
                        <i class="ti ti-upload"></i> Subir
                    </button>
                </div>
                <p id="certEstado" class="text-xs text-gray-400 hidden"></p>
            </div>
        </fieldset>

        {{-- Configuración --}}
        <fieldset>
            <legend class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Configuración</legend>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 items-end">
                <div>
                    <x-label for="f_igv">IGV (%)</x-label>
                    <x-input id="f_igv" type="number" step="0.01" min="0" max="1" value="0.18" class="w-full" />
                </div>
                <div>
                    <x-label for="f_tipo_impresion">Tipo Impresión</x-label>
                    <x-select id="f_tipo_impresion" class="w-full">
                        <option value="1">A4</option>
                        <option value="2">8cm (Voucher)</option>
                    </x-select>
                </div>
                <div>
                    <x-label for="f_modo">Modo</x-label>
                    <x-input id="f_modo" type="text" maxlength="50" placeholder="producción" class="w-full" />
                </div>
                <div>
                    <x-label>Estado</x-label>
                    <div class="pt-1">
                        <x-switch id="f_estado" checked />
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <x-label for="f_propaganda">Propaganda / Lema</x-label>
                <x-input id="f_propaganda" type="text" maxlength="250" placeholder="Texto que aparece en comprobantes (opcional)" class="w-full" />
            </div>
        </fieldset>
    </form>

    <x-slot:footer>
        <button onclick="cerrarModal('mdEmpresa')"
                class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 transition">
            Cancelar
        </button>
        <button onclick="guardar()"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition">
            <i class="ti ti-device-floppy"></i> Guardar
        </button>
    </x-slot:footer>
</x-modal>

@endsection

@push('scripts')
<script>
const BASE = BASE_URL;
let t;

$(function () {
    t = initDataTable('#tblEmpresas', {
        processing: true,
        serverSide: true,
        ajax: {
            url: BASE + '/api/empresas',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            dataSrc: function(json) {
                const btn  = document.getElementById('btnNuevaEmpresa');
                const hint = document.getElementById('btnNuevaEmpresaHint');
                if (json.recordsTotal >= 1) {
                    btn.disabled = true;
                    hint.classList.remove('hidden');
                    hint.classList.add('inline-flex');
                } else {
                    btn.disabled = false;
                    hint.classList.add('hidden');
                    hint.classList.remove('inline-flex');
                }
                return json.data;
            },
        },
        columns: [
            { data: 'ruc' },
            { data: 'razon_social' },
            { data: 'comercial', defaultContent: '-' },
            { data: 'distrito', defaultContent: '-' },
            { data: 'estado_html', orderable: false, searchable: false, className: 'text-center' },
            { data: 'acciones', orderable: false, searchable: false, className: 'text-center no-colvis' },
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
    });
});

function abrirModalNuevo() {
    document.getElementById('mdTitle').textContent = 'Nueva Empresa';
    ['f_id','f_ruc','f_razon','f_comercial','f_direccion','f_distrito','f_provincia',
     'f_departamento','f_ubigeo','f_sucursal','f_email','f_tel1','f_tel2','f_tel3',
     'f_user_sol','f_clave_sol','f_igv','f_modo','f_propaganda','f_logo'
    ].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('f_igv').value = '0.18';
    document.getElementById('f_tipo_impresion').value = '1';
    const estado = document.getElementById('f_estado');
    if (estado) estado.checked = true;
    document.getElementById('fsCertificado').classList.add('hidden');
    abrirModal('mdEmpresa');
}

async function buscarRuc() {
    const ruc = document.getElementById('f_ruc').value.trim();
    if (ruc.length < 11) { toastWarn('El RUC debe tener 11 dígitos.'); return; }
    try {
        const d = await apiPost(BASE + '/api/empresas/buscar-ruc', { ruc });
        if (d.res && d.empresa) {
            document.getElementById('f_razon').value = d.empresa.razon_social || '';
            document.getElementById('f_comercial').value = d.empresa.comercial || '';
            document.getElementById('f_direccion').value = d.empresa.direccion || '';
            document.getElementById('f_distrito').value = d.empresa.distrito || '';
            document.getElementById('f_provincia').value = d.empresa.provincia || '';
            document.getElementById('f_departamento').value = d.empresa.departamento || '';
            document.getElementById('f_ubigeo').value = d.empresa.ubigeo || '';
            toastOk('Datos de empresa encontrados.');
        } else {
            toastWarn('No se encontró empresa con ese RUC.');
        }
    } catch { toastWarn('Error al consultar RUC.'); }
}

async function guardar() {
    const id     = document.getElementById('f_id').value;
    const ruc    = document.getElementById('f_ruc').value.trim();
    const razon  = document.getElementById('f_razon').value.trim();
    if (!ruc || !razon) { toastWarn('RUC y Razón Social son obligatorios.'); return; }

    const payload = {
        ruc,
        razon_social:  razon,
        comercial:     document.getElementById('f_comercial').value.trim(),
        cod_sucursal:  document.getElementById('f_sucursal').value.trim(),
        direccion:     document.getElementById('f_direccion').value.trim(),
        email:         document.getElementById('f_email').value.trim(),
        telefono:      document.getElementById('f_tel1').value.trim(),
        telefono2:     document.getElementById('f_tel2').value.trim(),
        telefono3:     document.getElementById('f_tel3').value.trim(),
        user_sol:      document.getElementById('f_user_sol').value.trim(),
        clave_sol:     document.getElementById('f_clave_sol').value.trim(),
        distrito:      document.getElementById('f_distrito').value.trim(),
        provincia:     document.getElementById('f_provincia').value.trim(),
        departamento:  document.getElementById('f_departamento').value.trim(),
        ubigeo:        document.getElementById('f_ubigeo').value.trim(),
        tipo_impresion: document.getElementById('f_tipo_impresion').value,
        modo:          document.getElementById('f_modo').value.trim(),
        igv:           parseFloat(document.getElementById('f_igv').value) || 0.18,
        estado:        document.getElementById('f_estado').checked ? '1' : '0',
        propaganda:    document.getElementById('f_propaganda').value.trim(),
    };

    const url = id ? BASE + '/api/empresas/editar' : BASE + '/api/empresas/add';
    if (id) payload.id_empresa = parseInt(id);

    const d = await apiPost(url, payload);
    if (d.res) {
        toastOk(id ? 'Empresa actualizada.' : 'Empresa registrada.');
        cerrarModal('mdEmpresa');
        t.ajax.reload(null, false);
    } else {
        toastErr(d.msg || 'Error al guardar.');
    }
}

async function editar(id) {
    const d = await apiPost(BASE + '/api/empresas/get-one', { id_empresa: id });
    document.getElementById('mdTitle').textContent = 'Editar Empresa';
    document.getElementById('f_id').value    = d.id_empresa;
    document.getElementById('f_ruc').value   = d.ruc || '';
    document.getElementById('f_razon').value = d.razon_social || '';
    document.getElementById('f_comercial').value = d.comercial || '';
    document.getElementById('f_direccion').value = d.direccion || '';
    document.getElementById('f_distrito').value  = d.distrito || '';
    document.getElementById('f_provincia').value = d.provincia || '';
    document.getElementById('f_departamento').value = d.departamento || '';
    document.getElementById('f_ubigeo').value  = d.ubigeo || '';
    document.getElementById('f_sucursal').value = d.cod_sucursal || '';
    document.getElementById('f_email').value   = d.email || '';
    document.getElementById('f_tel1').value    = d.telefono || '';
    document.getElementById('f_tel2').value    = d.telefono2 || '';
    document.getElementById('f_tel3').value    = d.telefono3 || '';
    document.getElementById('f_user_sol').value = d.user_sol || '';
    document.getElementById('f_clave_sol').value = d.clave_sol || '';
    document.getElementById('f_igv').value    = d.igv || '0.18';
    document.getElementById('f_tipo_impresion').value = d.tipo_impresion || '1';
    document.getElementById('f_modo').value   = d.modo || '';
    document.getElementById('f_propaganda').value = d.propaganda || '';
    const estado = document.getElementById('f_estado');
    if (estado) estado.checked = d.estado === '1';
    document.getElementById('fsCertificado').classList.remove('hidden');
    document.getElementById('f_certificado').value = '';
    document.getElementById('certEstado').classList.add('hidden');
    abrirModal('mdEmpresa');
}

async function toggle(id) {
    const d = await apiPost(BASE + '/api/empresas/toggle', { id_empresa: id });
    if (d.res) {
        toastOk(d.estado === '1' ? 'Empresa activada.' : 'Empresa desactivada.');
        t.ajax.reload(null, false);
    } else { toastErr(d.msg || 'Error.'); }
}

async function subirCertificado() {
    const id   = document.getElementById('f_id').value;
    const file = document.getElementById('f_certificado').files[0];
    const statusEl = document.getElementById('certEstado');

    if (!id)   { toastWarn('Guardá la empresa primero.'); return; }
    if (!file) { toastWarn('Seleccioná un archivo .pem.'); return; }

    statusEl.textContent = 'Subiendo certificado...';
    statusEl.className   = 'text-xs text-blue-500';
    statusEl.classList.remove('hidden');

    const form = new FormData();
    form.append('id_empresa',  id);
    form.append('certificado', file);
    form.append('_token',      '{{ csrf_token() }}');

    try {
        const resp = await fetch(BASE + '/api/empresas/subir-certificado', { method: 'POST', body: form });
        const d    = await resp.json();
        if (d.res) {
            statusEl.textContent = '✓ ' + d.msg;
            statusEl.className   = 'text-xs text-emerald-600';
            document.getElementById('f_certificado').value = '';
        } else {
            statusEl.textContent = '✗ ' + (d.msg || 'Error al subir.');
            statusEl.className   = 'text-xs text-red-500';
        }
    } catch {
        statusEl.textContent = '✗ Error de conexión.';
        statusEl.className   = 'text-xs text-red-500';
    }
}

async function eliminar(id) {
    const { isConfirmed } = await Swal.fire({
        title: '¿Eliminar esta empresa?',
        text: 'Esta acción no se puede deshacer. Los usuarios no podrán acceder.',
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626',
        cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar',
    });
    if (!isConfirmed) return;
    const d = await apiPost(BASE + '/api/empresas/eliminar', { id_empresa: id });
    if (d.res) { toastOk('Empresa eliminada.'); t.ajax.reload(null, false); }
    else toastErr(d.msg || 'No se pudo eliminar.');
}
</script>
@endpush
