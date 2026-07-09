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
            ],
            'Notas Electrónicas' => [
                'notas.ver'    => 'Ver listado',
                'notas.crear'  => 'Crear notas',
                'notas.pdf'    => 'Generar PDF',
            ],
            'Guías Remisión' => [
                'guias.ver'    => 'Ver listado',
                'guias.crear'  => 'Crear guías',
                'guias.pdf'    => 'Generar PDF',
            ],
            'Clientes' => [
                'clientes.ver'     => 'Ver listado',
                'clientes.crear'   => 'Crear clientes',
                'clientes.editar'  => 'Editar clientes',
                'clientes.borrar'  => 'Eliminar clientes',
                'clientes.exportar'=> 'Exportar Excel',
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
                'productos.exportar'=> 'Exportar Excel',
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
            ],
            'Traslados' => [
                'almacen_traslados.ver'   => 'Ver traslados',
                'almacen_traslados.crear' => 'Crear traslados',
            ],
            'Préstamos' => [
                'almacen_prestamos.ver'   => 'Ver préstamos',
                'almacen_prestamos.crear' => 'Registrar préstamos',
            ],
            'Caja' => [
                'caja.ver'       => 'Ver movimientos de caja',
                'caja.gestionar' => 'Aperturar / cerrar caja',
                'caja.arqueo'    => 'Arqueo diario',
            ],
            'Cobranzas' => [
                'cobranzas.ver'      => 'Ver cobranzas',
                'cobranzas.registrar'=> 'Registrar cobros',
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
            ],
            'Reportes' => [
                'reportes.ver'      => 'Ver reportes',
                'reportes.exportar' => 'Exportar Excel',
                'reportes_ventas.pdf'   => 'Reporte de ventas PDF',
                'reportes_compras.pdf'  => 'Reporte de compras PDF',
                'reportes_clientes.pdf' => 'Reporte de clientes PDF',
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
                'permisos.ver'    => 'Ver listado',
                'permisos.crear'  => 'Crear permisos',
                'permisos.editar' => 'Editar permisos',
                'permisos.borrar' => 'Eliminar permisos',
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
