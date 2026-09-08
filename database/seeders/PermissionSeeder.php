<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public static function groups(): array
    {
        return [
            'Ventas' => [
                'ventas.ver'    => 'Ver listado de ventas',
                'ventas.crear'  => 'Crear ventas',
                'ventas.anular' => 'Anular ventas',
                'ventas.pdf'    => 'Generar PDF / comprobante',
                'ventas.sunat'  => 'Enviar a SUNAT, regenerar XML y descargar CDR',
            ],
            'Compras' => [
                'compras.ver'    => 'Ver listado de compras',
                'compras.crear'  => 'Crear compras',
                'compras.editar' => 'Editar compras',
                'compras.pdf'    => 'Generar PDF',
            ],
            'Cotizaciones' => [
                'cotizaciones.ver'    => 'Ver listado',
                'cotizaciones.crear'  => 'Crear cotizaciones',
                'cotizaciones.editar' => 'Editar cotizaciones',
                'cotizaciones.pdf'    => 'Generar PDF',
                'cotizaciones.cuotas' => 'Gestionar cuotas',
                'cotizaciones.anular' => 'Anular cotizaciones',
            ],
            'Notas Electrónicas' => [
                'notas.ver'    => 'Ver listado',
                'notas.crear'  => 'Crear notas',
                'notas.pdf'    => 'Generar PDF',
                'notas.sunat'  => 'Enviar a SUNAT, regenerar XML y descargar CDR',
                'notas.anular' => 'Anular notas electrónicas',
            ],
            'Guías Remisión' => [
                'guias.ver'    => 'Ver listado',
                'guias.crear'  => 'Crear guías',
                'guias.pdf'    => 'Generar PDF',
                'guias.sunat'  => 'Enviar a SUNAT, regenerar XML y descargar CDR',
                'guias.anular' => 'Anular guías de remisión',
            ],
            'Clientes' => [
                'clientes.ver'     => 'Ver listado',
                'clientes.crear'   => 'Crear clientes',
                'clientes.editar'  => 'Editar clientes',
                'clientes.borrar'  => 'Eliminar clientes',
                'clientes.exportar'=> 'Exportar Excel',
                'clientes.importar'=> 'Importar clientes desde Excel',
            ],
            'Proveedores' => [
                'proveedores.ver'     => 'Ver listado',
                'proveedores.crear'   => 'Crear proveedores',
                'proveedores.editar'  => 'Editar proveedores',
                'proveedores.exportar'=> 'Exportar Excel',
            ],
            'Productos' => [
                'productos.ver'     => 'Ver listado',
                'productos.crear'   => 'Crear productos',
                'productos.editar'  => 'Editar productos',
                'productos.kardex'  => 'Ver kardex',
                'productos.importar'=> 'Importar productos desde Excel',
            ],
            'Recepción' => [
                'almacen_recepcion.ver'   => 'Ver recepciones',
                'almacen_recepcion.crear' => 'Registrar recepción',
            ],
            'Existencias' => [
                'almacen_existencias.ver' => 'Ver existencias por almacén',
            ],
            'Ajustes / Cuadres' => [
                'almacen_ajustes.ver'   => 'Ver ajustes',
                'almacen_ajustes.crear' => 'Crear ajustes',
                'almacen_ajustes.anular' => 'Anular ajustes',
            ],
            'Traslados' => [
                'almacen_traslados.ver'   => 'Ver traslados',
                'almacen_traslados.crear' => 'Crear traslados',
                'almacen_traslados.anular' => 'Anular traslados',
            ],
            'Préstamos' => [
                'almacen_prestamos.ver'   => 'Ver préstamos',
                'almacen_prestamos.crear' => 'Registrar préstamos',
            ],
            // Un grupo por pantalla del menú de Caja, con el mismo nombre.
            // Cada uno lleva su permiso de ver y sus propias acciones: así se
            // puede dar Movimientos sin dar Mi Caja, o Cierres sin Métodos de Pago.
            'Gestión de Cajas' => [
                'caja.gestionar'              => 'Ver y crear cajas',
                'caja.gestionar_estado'       => 'Activar o desactivar una caja',
                'caja.gestionar_instrumentos' => 'Asignar métodos de pago a una caja',
            ],
            'Movimientos' => [
                'caja.movimientos_ver'       => 'Ver movimientos de todas las cajas',
                'caja.movimientos_registrar' => 'Registrar ingresos y egresos desde esta pantalla',
                'caja.movimiento_anular'     => 'Anular un movimiento',
            ],
            'Cierres y Cuadre' => [
                'caja.cierres'            => 'Ver cierres y cuadres',
                'caja.cierres_consolidado' => 'Generar el cuadre consolidado',
                'caja.cierre_aprobar'     => 'Aprobar o rechazar un cierre',
                'caja.cancelar_deuda'     => 'Cancelar (perdonar) una deuda de cierre',
            ],
            'Asignaciones de Fondo' => [
                'caja.transferencias'             => 'Ver asignaciones de fondo',
                'caja.transferencias_asignar'     => 'Asignar fondos a una caja',
                'caja.transferencias_reasignar'   => 'Reasignar una asignación',
                'caja.transferencias_anular'      => 'Anular una asignación',
                'caja.transferencias_rechazar'    => 'Rechazar una asignación',
                'caja.transferencias_discrepancia' => 'Resolver una discrepancia',
            ],
            'Mi Caja' => [
                'caja.ver'                  => 'Ver mi caja y sus movimientos',
                'caja.aperturar'            => 'Aperturar la caja',
                'caja.cerrar'               => 'Cerrar la caja',
                'caja.movimiento_registrar' => 'Registrar ingresos y egresos',
                'caja.apertura_ver'         => 'Ver detalle de la apertura',
                'caja.apertura_editar'      => 'Editar la apertura del día',
            ],
            'Métodos de Pago' => [
                'caja.metodos_pago'        => 'Ver bancos, cuentas, tarjetas y billeteras',
                'caja.metodos_pago_crear'  => 'Crear métodos de pago',
                'caja.metodos_pago_editar' => 'Editar métodos de pago',
                'caja.metodos_pago_estado' => 'Activar o desactivar un método de pago',
            ],
            'Códigos QR' => [
                'qr.ver' => 'Ver códigos QR de cobro',
            ],
            'Cuentas por Cobrar' => [
                'cobranzas.ver'       => 'Ver cuentas por cobrar',
                'cobranzas.registrar' => 'Registrar abonos / cobros',
                'cobranzas.editar'    => 'Editar abonos',
                'cobranzas.anular'    => 'Anular abonos',
            ],
            'Mis Cobros' => [
                'cobranzas_miscobros.ver' => 'Ver mis cobros',
            ],
            'Pagos' => [
                'pagos.ver'      => 'Ver pagos',
                'pagos.registrar'=> 'Registrar pagos',
            ],
            'Mercados' => [
                'tms_mercados.ver'    => 'Ver mercados',
                'tms_mercados.crear'  => 'Crear mercados',
                'tms_mercados.editar' => 'Editar mercados',
            ],
            'Vehículos' => [
                'tms_vehiculos.ver'    => 'Ver vehículos',
                'tms_vehiculos.crear'  => 'Crear vehículos',
                'tms_vehiculos.editar' => 'Editar vehículos',
            ],
            'Conductores' => [
                'tms_conductores.ver'    => 'Ver conductores',
                'tms_conductores.crear'  => 'Crear conductores',
                'tms_conductores.editar' => 'Editar conductores',
            ],
            'Rutas' => [
                'tms_rutas.ver'    => 'Ver rutas',
                'tms_rutas.crear'  => 'Crear rutas',
                'tms_rutas.editar' => 'Editar rutas',
            ],
            'Despachos' => [
                'tms_despachos.ver'    => 'Ver despachos',
                'tms_despachos.crear'  => 'Armar despachos',
                'tms_despachos.editar' => 'Editar despachos',
                'tms_despachos.pdf'    => 'Hoja de carga / guías PDF',
                'tms_despachos.cerrar' => 'Cerrar despachos',
                'tms_despachos.anular' => 'Anular despachos',
            ],

            'Finanzas' => [
                'finanzas.utilidades'        => 'Ver utilidades',
                'finanzas.flujo_caja'        => 'Ver flujo de caja',
                'finanzas.estado_resultados' => 'Ver estado de resultados',
                'finanzas.indicadores'       => 'Ver indicadores financieros',
                'finanzas.margenes'          => 'Ver análisis de márgenes',
                'finanzas.costeo'            => 'Ver costeo y rentabilidad',
            ],
            'Reportes' => [
                'reportes.exportar'     => 'Exportar reportes a Excel',
                'reportes_ventas.pdf'   => 'Reporte de ventas PDF',
                'reportes_clientes.pdf' => 'Reporte de cliente PDF',
            ],
            'Usuarios' => [
                'usuarios.ver'    => 'Ver listado',
                'usuarios.crear'  => 'Crear usuarios',
                'usuarios.editar' => 'Editar usuarios',
                'usuarios.borrar' => 'Eliminar usuarios',
            ],
            'Empresas' => [
                'empresas.ver'    => 'Ver empresas',
                'empresas.crear'  => 'Crear empresas',
                'empresas.editar' => 'Editar empresas',
            ],
            'Sucursales' => [
                'sucursales.ver'    => 'Ver sucursales',
                'sucursales.crear'  => 'Crear sucursales',
                'sucursales.editar' => 'Editar sucursales',
            ],
            'Roles' => [
                'roles.ver'    => 'Ver listado',
                'roles.crear'  => 'Crear roles',
                'roles.editar' => 'Editar roles',
                'roles.borrar' => 'Eliminar roles',
            ],
            'Permisos' => [
                // El catálogo de permisos es de solo lectura por diseño: los
                // permisos los define el código, no la interfaz.
                'permisos.ver' => 'Ver catálogo de permisos',
            ],
            'Auditoría' => [
                'auditoria.ver' => 'Ver registro de auditoría',
            ],
            'Correlativos' => [
                'correlativos.gestionar' => 'Configurar correlativos',
            ],
        ];
    }

    public function run(): void
    {
        foreach (static::groups() as $group => $permissions) {
            foreach ($permissions as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    ['description' => $description]
                );
            }
        }
    }
}
