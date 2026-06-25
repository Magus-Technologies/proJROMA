# Lógica Bancos y Billeteras Digitales — Para aplicar en Caja

## Estructura jerárquica

```
Banco
├── Cuentas bancarias (CC, CA, CTS, etc.)
├── Tarjetas (Crédito / Débito)
└── Billeteras digitales
    ├── Plin
    ├── Yape
    ├── Tunki
    ├── Agora
    └── Otras (BIM, etc.)
```

---

## Entidades necesarias

### 1. Bancos (`bancos`)
| Campo | Tipo |
|---|---|
| id_banco | PK |
| nombre | varchar(100) |
| codigo_sunat | varchar(10) |
| estado | tinyint |

### 2. Cuentas Bancarias (`cuentas_bancarias`)
| Campo | Tipo |
|---|---|
| id_cuenta | PK |
| id_empresa | FK |
| id_banco | FK |
| tipo_cuenta | enum: 'CC', 'CA', 'CTS', 'AHORRO' |
| numero_cuenta | varchar(30) |
| cci | varchar(30) |
| moneda | enum: 'PEN', 'USD' |
| titular | varchar(200) |
| estado | tinyint |

### 3. Tarjetas (`tarjetas`)
| Campo | Tipo |
|---|---|
| id_tarjeta | PK |
| id_empresa | FK |
| id_banco | FK |
| id_cuenta_bancaria | FK (nullable, si la tarjeta está ligada a una cuenta) |
| tipo | enum: 'CREDITO', 'DEBITO' |
| marca | enum: 'VISA', 'MASTERCARD', 'AMEX', 'DINERS' |
| ultimos_4 | char(4) |
| titular | varchar(200) |
| fecha_vencimiento | date |
| estado | tinyint |

### 4. Billeteras Digitales (`billeteras_digitales`)
| Campo | Tipo |
|---|---|
| id_billetera | PK |
| id_empresa | FK |
| tipo | enum: 'PLIN', 'YAPE', 'TUNKI', 'AGORA', 'BIM', 'OTRO' |
| telefono | varchar(15) |
| titular | varchar(200) |
| estado | tinyint |

---

## Flujo en Caja (Ingresos / Egresos)

Al registrar un **Ingreso** o **Egreso**, el usuario elige:

1. **Tipo de origen/destino**: Efectivo / Banco / Tarjeta / Billetera Digital
2. Si elige **Banco** → selecciona Cuenta Bancaria
3. Si elige **Tarjeta** → selecciona Tarjeta (y opcionalmente la Cuenta vinculada)
4. Si elige **Billetera Digital** → selecciona Billetera (Plin, Yape, etc.)

Esto permite conciliar el arqueo diario separando:
- Efectivo físico
- Depósitos/transferencias por cuenta bancaria
- Pagos con tarjeta (crédito/débito)
- Pagos con billetera digital (Plin, Yape, etc.)

---

## Aplicación en Arqueo Diario

El arqueo debería mostrar columnas separadas por cada "instrumento":

| Vendedor | Efectivo | Cuenta BCP CC | Cuenta Interbank CA | Tarjeta Visa *1234 | Plin | Yape | Total |
|---|---|---|---|---|---|---|---|

Los totales de "Bancos" en el arqueo actual se desglosan en cuentas/tarjetas/billeteras.

---

## Notas de implementación

- Las entidades son **por empresa** (`id_empresa`) y opcionalmente por `sucursal` si una sucursal maneja sus propias cuentas
- Se requiere CRUD para cada entidad (Banco, Cuenta, Tarjeta, Billetera)
- En `ingreso_egreso` / `caja_empresa` agregar campos:
  - `instrumento_tipo` enum: 'EFECTIVO', 'CUENTA_BANCARIA', 'TARJETA', 'BILLETERA_DIGITAL'
  - `instrumento_id` FK polimórfico o separado por tipo
- En `arqueos_diarios` agregar columnas de desglose por instrumento o crear tabla `arqueo_detalle` por instrumento