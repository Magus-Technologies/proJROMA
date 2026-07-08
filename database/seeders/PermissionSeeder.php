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
            'Inventario' => [
                'almacen_recepcion.ver'   => 'Recepción - ver',
                'almacen_recepcion.crear' => 'Recepción - crear',
                'almacen_existencias.ver' => 'Existencias - ver',
                'almacen_ajustes.ver'     => 'Ajustes - ver',
                'almacen_ajustes.crear'   => 'Ajustes - crear',
                'almacen_traslados.ver'   => 'Traslados - ver',
                'almacen_traslados.crear' => 'Traslados - crear',
                'almacen_prestamos.ver'   => 'Préstamos - ver',
                'almacen_prestamos.crear' => 'Préstamos - crear',
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
            'TMS (Transporte)' => [
                'tms_mercados.ver'    => 'Mercados - ver',
                'tms_mercados.crear'  => 'Mercados - crear',
                'tms_mercados.editar' => 'Mercados - editar',
                'tms_vehiculos.ver'   => 'Vehículos - ver',
                'tms_vehiculos.crear' => 'Vehículos - crear',
                'tms_vehiculos.editar'=> 'Vehículos - editar',
                'tms_conductores.ver'   => 'Conductores - ver',
                'tms_conductores.crear' => 'Conductores - crear',
                'tms_conductores.editar'=> 'Conductores - editar',
                'tms_rutas.ver'    => 'Rutas - ver',
                'tms_rutas.crear'  => 'Rutas - crear',
                'tms_rutas.editar' => 'Rutas - editar',
                'tms_despachos.ver'    => 'Despachos - ver',
                'tms_despachos.crear'  => 'Despachos - crear',
                'tms_despachos.editar' => 'Despachos - editar',
                'tms_despachos.pdf'    => 'Despachos - PDF',
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
            'Administración' => [
                'auditoria.ver'          => 'Ver auditoría',
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
