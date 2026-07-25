# Módulos de ProJROMA

## Módulos Existentes

### Facturación
- **Ventas** — Registro de ventas, facturas, boletas (con SUNAT)
- **Notas Electrónicas** — Notas de crédito/débito electrónicas
- **Guías de Remisión** — Guías de remisión electrónicas

### Cotizaciones
- **Cotizaciones / Pedidos** — Registro y seguimiento de cotizaciones

### Cobranzas
- **Cuentas por Cobrar** — Gestión de cobranzas y saldos pendientes
- **Mis Cobros** — Cobros asignados al usuario

### Pagos
- **Cuentas por Pagar** — Gestión de pagos a proveedores

### Caja
- **Mi Caja** — Apertura/cierre de caja chica
- **Cajas Principales** — Gestión de cajas generales
- **Gestión de Cajas** — Administración de cajas
- **Movimientos de Caja** — Registro de ingresos/egresos
- **Cierres de Caja** — Cuadre y cierre de caja
- **Métodos de Pago** — Bancos, cuentas, tarjetas, billeteras

### Inventario
- **Compras** — Registro de compras y recepción
- **Productos** — Catálogo de productos, marcas, categorías, presentaciones
- **Recepción** — Recepción de mercadería
- **Almacén Stock** — Stock por almacén
- **Kardex** — Kardex de inventarios
- **Ajustes** — Cuadres y ajustes de stock
- **Traslados** — Traslado de stock entre almacenes
- **Préstamos** — Préstamos de productos

### Transporte (TMS)
- **Mercados** — Gestión de mercados
- **Vehículos** — Flota de vehículos
- **Conductores** — Registro de conductores
- **Rutas** — Rutas de despacho
- **Despachos** — Gestión de despachos

### Maestros
- **Clientes** — Registro de clientes
- **Proveedores** — Registro de proveedores
- **Motivos de Movimiento** — Tipos de movimiento

### Administración
- **Usuarios** — Gestión de usuarios del sistema
- **Empresas** — Configuración multi-empresa
- **Sucursales** — Gestión de sucursales
- **Roles** — Roles y permisos
- **Permisos** — Permisos del sistema
- **Auditoría** — Log de cambios
- **Correlativos** — Configuración de series/numeración

### Dashboard (Tablero de KPIs)
- **Ventas del Mes** — Total ventas del período
- **Compras del Mes** — Total compras del período
- **Clientes** — Total de clientes registrados
- **Pedidos Pendientes** — Cotizaciones sin convertir
- **Ventas últimos 30 días** — Gráfico de línea diaria
- **Productos más vendidos** — Top productos del mes
- **Ventas vs Compras** — Comparativa 6 meses (barras)
- **Ventas por Categoría** — Distribución por categoría
- **CxC Antigüedad** — Cuentas por cobrar por rango de vencimiento
- **Top Deudores** — Clientes con mayor deuda
- **Top Clientes** — Clientes que más compran
- **Evolución de Clientes** — Crecimiento de clientes en el tiempo
- **Stock Bajo** — Productos con stock mínimo
- **Últimas Ventas** — Tabla de últimas transacciones

---

## Módulo de Contabilidad Simple (Propuesto)

No existe actualmente. Propuesta mínima:

### 1. Plan de Cuentas (PCGE)
Catálogo básico de cuentas contables (1-2-3-4-5-6-7-8-9).

### 2. Asientos Contables Automáticos
Generar asientos automáticos desde:
- Ventas → cuenta 70 (Ventas) y 12 (Clientes) / 10 (Efectivo)
- Compras → cuenta 60 (Compras) y 42 (Proveedores)
- Cobranzas → cuenta 10 (Efectivo) y 12 (Clientes)
- Pagos → cuenta 42 (Proveedores) y 10 (Efectivo)

### 3. Libro Diario
Vista de todos los asientos contables ordenados por fecha.

### 4. Balance de Comprobación
Sumas y saldos de todas las cuentas (deudor/acreedor).

### 5. Estados Financieros Básicos
- **Balance General** — Activo, Pasivo y Patrimonio
- **Estado de Resultados** — Ingresos, Gastos y Utilidad Neta

### 6. Reporte de Rentabilidad
- Margen bruto (Ventas - Costo de Ventas)
- Margen neto (Utilidad Neta / Ventas)
- Variación vs mes anterior

---

## Dashboard Ejecutivo (Propuesto)

Indicadores clave para saber "cómo va la empresa":

| Indicador | Fuente |
|-----------|--------|
| Utilidad Bruta del Mes | Ventas - Costo de Ventas |
| Utilidad Neta del Mes | Ingresos - Gastos |
| Margen de Ganancia % | (Utilidad Bruta / Ventas) × 100 |
| Ratio de Liquidez | Efectivo / Pasivo Corriente |
| CxC Vencido % | (CxC vencido / Total CxC) × 100 |
| Rotación de Inventario | Costo Ventas / Inventario Promedio |
| Gastos Operativos | Suma de gastos del período |
| Variación vs Mes Anterior | % de cambio en ventas/utilidad |
| Proyección de Ventas | Tendencia basada en datos históricos |
| EBITDA Estimado | Utilidad Operativa + Depreciación |
