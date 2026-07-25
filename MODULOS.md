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

## Módulo Finanzas (En desarrollo)

Grupo en sidebar: **Finanzas**

### 1. Estado de Resultados (P&L) ✅
Página implementada en `Finanzas > Estado de Resultados`.
- Ventas Netas, Costo de Ventas, Utilidad Bruta, Gastos Operativos, Utilidad Neta
- KPIs: Margen Bruto %, Margen Neto %
- Filtros: Este Mes, Mes Anterior, 3M, 6M, Este Año
- Gráfico evolución 6 meses (Ventas vs Costos vs Utilidad)
- Tabla P&L estilo contable

### 2. Análisis de Márgenes ✅
Página implementada en `Finanzas > Análisis de Márgenes`.
- Margen general del período
- Top 15 productos por margen
- Margen por vendedor
- Alertas: productos con margen bajo (&lt; 10%) y margen negativo
- Datos del mes actual con desglose venta/costo/margen

### 3. Costeo y Rentabilidad de Productos (Pendiente)
- Costo promedio ponderado / FIFO
- Rentabilidad por SKU
- Alertas de productos con margen bajo o negativo

### 4. Presupuestos (Pendiente)
- Presupuesto de ventas vs real
- Presupuesto de gastos vs real
- Proyecciones

### 5. Flujo de Caja (Cash Flow) ✅
Página implementada en `Finanzas > Flujo de Caja`.
- Saldo actual en caja
- Cuentas por Cobrar (por vencer / vencido)
- Cuentas por Pagar (por vencer / vencido)
- Proyección a 30, 60 y 90 días
- Flujo neto proyectado

### 6. Cuentas por Cobrar / Pagar (Pendiente)
- Antigüedad de saldos (aging)
- Clientes / proveedores con mayor exposición

### 7. Indicadores Financieros (KPIs) ✅
Página implementada en `Finanzas > Indicadores Financieros`.
- Ventas del Mes, Utilidad Bruta, Utilidad Neta, EBITDA
- Margen Bruto %, Margen Neto %, ROI %
- Liquidez Corriente, Rotación de Inventario
- Punto de Equilibrio, Costo de Inventario
- Tabla resumen con interpretación automática (✅/⚠️/❌)

### 8. Reportes Comparativos (Pendiente)
- Año vs año, mes vs mes
- Real vs presupuestado
- Por sucursal / almacén

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
- **Estado de Resultados** ✅ — Ya implementado en Finanzas
- **Balance General** — Pendiente
