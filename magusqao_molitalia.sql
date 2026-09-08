-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 08-09-2026 a las 16:53:19
-- Versión del servidor: 11.4.12-MariaDB-cll-lve
-- Versión de PHP: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `magusqao_molitalia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacenes`
--

CREATE TABLE `almacenes` (
  `id_almacen` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `almacenes`
--

INSERT INTO `almacenes` (`id_almacen`, `nombre`, `codigo`, `descripcion`, `id_sucursal`, `id_empresa`, `estado`) VALUES
(1, 'Almacén 1', '1', NULL, NULL, 12, '1'),
(2, 'Almacén 2', '2', NULL, NULL, 12, '1'),
(3, 'Almacén 3', '3', NULL, NULL, 12, '1'),
(4, 'almacen4', '31564165', 'Mi prueba', NULL, 12, '1'),
(5, 'almacne-pruba2', 'aedawdawd', 'ad ada da d', NULL, 12, '1'),
(6, 'ALMACEN1', '80105', 'AAAAAAAA', NULL, 0, '1'),
(7, 'ALMACEN1', 'aa', 'aa', NULL, 0, '1'),
(8, 'ALMACEN2', 'AL2', 'norte', NULL, 0, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arqueos_diarios`
--

CREATE TABLE `arqueos_diarios` (
  `arqueo_id` int(11) NOT NULL,
  `id_caja` int(10) UNSIGNED DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `sucursal` int(11) NOT NULL,
  `fecha_arqueo` date NOT NULL,
  `vendedor` varchar(100) DEFAULT NULL,
  `vendedor_id` int(11) DEFAULT NULL,
  `cobros_efectivo` decimal(10,2) DEFAULT 0.00,
  `cobros_bancos` decimal(10,2) DEFAULT 0.00,
  `ingresos_efectivo` decimal(10,2) DEFAULT 0.00,
  `ingresos_bancos` decimal(10,2) DEFAULT 0.00,
  `egresos_efectivo` decimal(10,2) DEFAULT 0.00,
  `egresos_bancos` decimal(10,2) DEFAULT 0.00,
  `diferencia_efectivo` decimal(10,2) DEFAULT 0.00,
  `diferencia_bancos` decimal(10,2) DEFAULT 0.00,
  `cuadra_efectivo` tinyint(1) DEFAULT 0,
  `cuadra_bancos` tinyint(1) DEFAULT 0,
  `usuario_registro` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `arqueos_diarios`
--

INSERT INTO `arqueos_diarios` (`arqueo_id`, `id_caja`, `id_empresa`, `sucursal`, `fecha_arqueo`, `vendedor`, `vendedor_id`, `cobros_efectivo`, `cobros_bancos`, `ingresos_efectivo`, `ingresos_bancos`, `egresos_efectivo`, `egresos_bancos`, `diferencia_efectivo`, `diferencia_bancos`, `cuadra_efectivo`, `cuadra_bancos`, `usuario_registro`, `fecha_creacion`) VALUES
(702, NULL, 12, 1, '2026-05-05', 'LINDA', 91, 787.80, 82.50, 787.80, 1543.70, 30.00, 0.00, 0.00, 1461.20, 1, 1, 40, '2026-05-06 09:57:10'),
(703, NULL, 12, 1, '2026-05-05', 'ZENON', 61, 1234.60, 285.20, 1234.60, 2716.40, 24.50, 0.00, 0.00, 2431.20, 1, 1, 40, '2026-05-06 09:57:41'),
(704, NULL, 12, 1, '2026-05-05', 'MARIANELA', 80, 1346.50, 1231.00, 1346.50, 1231.00, 488.00, 0.00, 0.00, 0.00, 1, 1, 40, '2026-05-06 09:59:01'),
(705, NULL, 12, 1, '2026-05-05', 'Arly ', 66, 7600.00, 4158.20, 7600.00, 4158.20, 504.30, 0.00, 0.00, 0.00, 1, 1, 40, '2026-05-06 10:00:16'),
(706, NULL, 12, 1, '2026-05-05', 'GAMARRA', 94, 7777.60, 3170.10, 7777.60, 3170.10, 58.00, 0.00, 0.00, 0.00, 1, 1, 40, '2026-05-06 10:00:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arqueo_detalle`
--

CREATE TABLE `arqueo_detalle` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_arqueo` int(10) UNSIGNED NOT NULL,
  `instrumento_tipo` varchar(30) NOT NULL,
  `instrumento_id` int(10) UNSIGNED DEFAULT NULL,
  `monto_sistema` decimal(12,2) NOT NULL,
  `monto_contado` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arqueo_efectivo_detalle`
--

CREATE TABLE `arqueo_efectivo_detalle` (
  `detalle_id` int(11) NOT NULL,
  `arqueo_id` int(11) NOT NULL,
  `billetes` decimal(10,2) DEFAULT 0.00,
  `monedas` decimal(10,2) DEFAULT 0.00,
  `pasaje` decimal(10,2) DEFAULT 0.00,
  `combustible` decimal(10,2) DEFAULT 0.00,
  `gastos` decimal(10,2) DEFAULT 0.00,
  `menu` decimal(10,2) DEFAULT 0.00,
  `otro` decimal(10,2) DEFAULT 0.00,
  `otro_descripcion` varchar(255) DEFAULT NULL,
  `total_ingresos` decimal(10,2) DEFAULT 0.00,
  `total_gastos` decimal(10,2) DEFAULT 0.00,
  `total_efectivo_real` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `arqueo_efectivo_detalle`
--

INSERT INTO `arqueo_efectivo_detalle` (`detalle_id`, `arqueo_id`, `billetes`, `monedas`, `pasaje`, `combustible`, `gastos`, `menu`, `otro`, `otro_descripcion`, `total_ingresos`, `total_gastos`, `total_efectivo_real`) VALUES
(691, 702, 750.00, 7.80, 0.00, 0.00, 30.00, 0.00, 0.00, '', 757.80, 30.00, 787.80),
(692, 703, 1200.00, 10.10, 0.00, 0.00, 24.50, 0.00, 0.00, '', 1210.10, 24.50, 1234.60),
(693, 704, 840.00, 18.50, 0.00, 0.00, 28.00, 0.00, 460.00, 'semana telefono feriado', 858.50, 488.00, 1346.50),
(694, 705, 7070.00, 25.70, 0.00, 0.00, 28.00, 30.00, 446.30, 'petrolio', 7095.70, 504.30, 7600.00),
(695, 706, 7700.00, 19.60, 0.00, 0.00, 28.00, 30.00, 0.00, '', 7719.60, 58.00, 7777.60);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arqueo_pagos_digitales`
--

CREATE TABLE `arqueo_pagos_digitales` (
  `pago_digital_id` int(11) NOT NULL,
  `arqueo_id` int(11) NOT NULL,
  `cliente_nombre` varchar(255) NOT NULL,
  `tipo_pago` enum('Yape','Plin','Transferencia') NOT NULL,
  `numero_operacion` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `arqueo_pagos_digitales`
--

INSERT INTO `arqueo_pagos_digitales` (`pago_digital_id`, `arqueo_id`, `cliente_nombre`, `tipo_pago`, `numero_operacion`, `monto`, `fecha_registro`) VALUES
(4759, 702, 'C- GERALDINE DOMINGUEZ - 2 DE JULIO', 'Yape', '', 82.50, '2026-05-06 09:57:10'),
(4760, 702, 'ROSA RIVERA (UNION STA ROSA)', 'Yape', '', 216.70, '2026-05-06 09:57:10'),
(4761, 702, 'REST. LA NORTEÑA', 'Yape', '', 189.50, '2026-05-06 09:57:10'),
(4762, 702, 'C-CHICHARRONERIA ELENITA (UNION STA ROSA)', 'Yape', '', 66.00, '2026-05-06 09:57:10'),
(4763, 702, 'F-LUZ SUDARIO (LAS FLORES)', 'Yape', '', 424.00, '2026-05-06 09:57:10'),
(4764, 702, 'F-YANETH SANDOVAL (LAS FLORES)', 'Yape', '', 399.60, '2026-05-06 09:57:10'),
(4765, 702, 'F-LUZ SUDARIO (LAS FLORES)', 'Yape', '', 65.00, '2026-05-06 09:57:10'),
(4766, 702, 'F-YANETH SANDOVAL (LAS FLORES)', 'Yape', '', 100.40, '2026-05-06 09:57:10'),
(4767, 703, 'C-IVAN URBANO (PROVEEDORES)', 'Yape', '', 94.00, '2026-05-06 09:57:41'),
(4768, 703, 'B-CARLOS ALBERTO (PROGRESO)', 'Yape', '', 132.00, '2026-05-06 09:57:41'),
(4769, 703, 'C-ELAR SANCHEZ (PROVEEDORES)', 'Yape', '', 528.50, '2026-05-06 09:57:41'),
(4770, 703, 'B-LUISA GALINDO (PROGRESO)', 'Yape', '', 25.00, '2026-05-06 09:57:41'),
(4771, 703, 'D-GISELA NAVEROS (GAMBETA)', 'Yape', '', 128.20, '2026-05-06 09:57:41'),
(4772, 703, 'H-LEONARDA QUISPE CARBAJAL (V.CARMEN)', 'Yape', '', 41.40, '2026-05-06 09:57:41'),
(4773, 703, 'VICTORIA NUÑEZ ABREGU', 'Yape', '', 92.10, '2026-05-06 09:57:41'),
(4774, 703, 'A-MOISES GUIZADO (RESTAURACION)', 'Yape', '', 1000.00, '2026-05-06 09:57:41'),
(4775, 703, 'B-ROSA HUAMANI CORDOVA (NOXILIA)', 'Yape', '', 120.00, '2026-05-06 09:57:41'),
(4776, 703, 'I-FIDELIA HUAMAN VARGAS(FLORES DE BREÑA', 'Yape', '', 164.00, '2026-05-06 09:57:41'),
(4777, 703, 'G-YOLANDA CHUQUIMAJO(CARHUAZ )', 'Yape', '', 106.80, '2026-05-06 09:57:41'),
(4778, 703, 'F-HUGO VILLANO (JR LORETO)', 'Yape', '', 168.00, '2026-05-06 09:57:41'),
(4779, 703, 'G-GLADIS GOMEZ (CHACRA COLORADA)', 'Yape', '', 116.40, '2026-05-06 09:57:41'),
(4780, 704, 'B-ALEXIS (V.MARIA)', 'Plin', '', 500.00, '2026-05-06 09:59:01'),
(4781, 704, 'MARYORI CHáVEZ LOPEZ', 'Yape', '', 306.60, '2026-05-06 09:59:01'),
(4782, 704, 'E-JESSENIA LAURA HUAMAN (MILAGROS)', 'Plin', '', 324.40, '2026-05-06 09:59:01'),
(4783, 704, 'C-JESSENIA ROJAS LLANIO (AMAUTA)', 'Plin', '', 100.00, '2026-05-06 09:59:01'),
(4784, 705, 'G-PAULINA ISHUIZA (CTO REY)', 'Yape', '', 69.00, '2026-05-06 10:00:16'),
(4785, 705, 'F- ISABEL C CASTRO (CTO REY)', 'Yape', '', 200.00, '2026-05-06 10:00:16'),
(4786, 705, 'ROSA RIVERA (UNION STA ROSA)', 'Yape', '', 216.70, '2026-05-06 10:00:16'),
(4787, 705, 'REST. LA NORTEÑA', 'Yape', '', 189.50, '2026-05-06 10:00:16'),
(4788, 705, 'A-YORDY (CANTO CHICO)', 'Yape', '', 100.00, '2026-05-06 10:00:16'),
(4789, 705, 'A-HAIDE ALANYA (CTO CHICO)', 'Yape', '', 275.00, '2026-05-06 10:00:16'),
(4790, 705, 'A-ALEX ALVITES (C. CHICO)', 'Yape', '', 80.00, '2026-05-06 10:00:16'),
(4791, 705, 'C-CHICHARRONERIA ELENITA (UNION STA ROSA)', 'Yape', '', 66.00, '2026-05-06 10:00:16'),
(4792, 705, 'B-MARY RONDAN (2 DE MAYO)', 'Yape', '', 100.00, '2026-05-06 10:00:16'),
(4793, 705, 'ANA MARIA LEANDRO ILDEFONSO', 'Yape', '', 550.00, '2026-05-06 10:00:16'),
(4794, 705, 'C-PAOLA HUAMANI (M.P.B)', 'Yape', '', 400.00, '2026-05-06 10:00:16'),
(4795, 705, 'NELLY AURORA ROJAS MARTINEZ', 'Yape', '', 100.00, '2026-05-06 10:00:16'),
(4796, 705, 'F-LUZ SUDARIO (LAS FLORES)', 'Yape', '', 424.00, '2026-05-06 10:00:16'),
(4797, 705, 'F-YANETH SANDOVAL (LAS FLORES)', 'Yape', '', 399.60, '2026-05-06 10:00:16'),
(4798, 705, 'E-HELEN (CTO REY) (BODEGA)', 'Yape', '', 300.00, '2026-05-06 10:00:16'),
(4799, 705, 'F- ISABEL C CASTRO (CTO REY)', 'Plin', '', 473.00, '2026-05-06 10:00:16'),
(4800, 705, 'D-MARIA ROSARIO ZAVALA RETAMOZO', 'Yape', '', 50.00, '2026-05-06 10:00:16'),
(4801, 705, 'F-LUZ SUDARIO (LAS FLORES)', 'Yape', '', 65.00, '2026-05-06 10:00:16'),
(4802, 705, 'F-YANETH SANDOVAL (LAS FLORES)', 'Yape', '', 100.40, '2026-05-06 10:00:16'),
(4803, 706, 'C-IVAN URBANO (PROVEEDORES)', 'Yape', '', 94.00, '2026-05-06 10:00:40'),
(4804, 706, 'F-CAROLINA CAMBA (MODELO #2)', 'Yape', '', 108.90, '2026-05-06 10:00:40'),
(4805, 706, 'C-ELAR SANCHEZ (PROVEEDORES)', 'Yape', '', 528.50, '2026-05-06 10:00:40'),
(4806, 706, 'H-BERNA VERA REATEGUI (P.PACOCHA)', 'Yape', '', 203.50, '2026-05-06 10:00:40'),
(4807, 706, 'F-FIDENCIA TAPIA (MODELO # 02 )', 'Yape', '', 283.00, '2026-05-06 10:00:40'),
(4808, 706, 'A-CLORINDA MORMONTOY (BOLIVAR )', 'Yape', '', 72.00, '2026-05-06 10:00:40'),
(4809, 706, 'C-SHEILA QUINTO (FAP)', 'Yape', '', 71.50, '2026-05-06 10:00:40'),
(4810, 706, 'H-LEONARDA QUISPE CARBAJAL (V.CARMEN)', 'Yape', '', 41.40, '2026-05-06 10:00:40'),
(4811, 706, 'VICTORIA NUÑEZ ABREGU', 'Yape', '', 92.10, '2026-05-06 10:00:40'),
(4812, 706, 'A-MOISES GUIZADO (RESTAURACION)', 'Yape', '', 1000.00, '2026-05-06 10:00:40'),
(4813, 706, 'B-ROSA HUAMANI CORDOVA (NOXILIA)', 'Yape', '', 120.00, '2026-05-06 10:00:40'),
(4814, 706, 'I-FIDELIA HUAMAN VARGAS(FLORES DE BREÑA', 'Yape', '', 164.00, '2026-05-06 10:00:40'),
(4815, 706, 'G-YOLANDA CHUQUIMAJO(CARHUAZ )', 'Yape', '', 106.80, '2026-05-06 10:00:40'),
(4816, 706, 'F-HUGO VILLANO (JR LORETO)', 'Yape', '', 168.00, '2026-05-06 10:00:40'),
(4817, 706, 'G-GLADIS GOMEZ (CHACRA COLORADA)', 'Yape', '', 116.40, '2026-05-06 10:00:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asientos_contables`
--

CREATE TABLE `asientos_contables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero` varchar(20) NOT NULL,
  `fecha` date NOT NULL,
  `glosa` varchar(500) NOT NULL,
  `tipo` enum('apertura','operaciones','ajuste','cierre') NOT NULL DEFAULT 'operaciones',
  `estado` enum('provisional','definitivo','anulado') NOT NULL DEFAULT 'provisional',
  `total_debe` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_haber` decimal(14,2) NOT NULL DEFAULT 0.00,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asientos_detalle`
--

CREATE TABLE `asientos_detalle` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asiento_id` bigint(20) UNSIGNED NOT NULL,
  `plan_cuenta_id` bigint(20) UNSIGNED NOT NULL,
  `debe` decimal(14,2) NOT NULL DEFAULT 0.00,
  `haber` decimal(14,2) NOT NULL DEFAULT 0.00,
  `glosa` varchar(300) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audits`
--

CREATE TABLE `audits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_rol` varchar(255) DEFAULT NULL,
  `empresa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` varchar(255) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `audits`
--

INSERT INTO `audits` (`id`, `user_id`, `user_name`, `user_rol`, `empresa_id`, `event`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `method`, `created_at`, `updated_at`) VALUES
(1, 107, 'admin', '1', 12, 'deleted', 'App\\Models\\User', '109', '{\"usuario_id\":109,\"id_empresa\":12,\"id_rol\":5,\"num_doc\":\"77425200\",\"usuario\":\"admin\",\"email\":\"rodrigoyarleque7@gmail.com\",\"nombres\":\"EMER RODRIGO\",\"apellidos\":\"YARLEQUE ZAPATA\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"993321920\",\"foto\":\"usuarios\\/fotos\\/01KX65WPGKDS4DGE755BETPEMR.jpg\",\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-07-10\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"updated_at\":null,\"created_at\":null}', NULL, '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 18:17:06', '2026-07-10 18:17:06'),
(2, 107, 'admin', '1', 12, 'created', 'App\\Models\\User', '110', NULL, '{\"num_doc\":\"77425200\",\"nombres\":\"EMER RODRIGO\",\"apellidos\":\"YARLEQUE ZAPATA\",\"telefono\":\"993321920\",\"usuario\":\"conta\",\"email\":\"adan2025zapata@gmail.com\",\"id_rol\":5,\"foto\":\"usuarios\\/fotos\\/01KX668MCG9RKRK7REXJDMF9Y2.jpg\",\"estado\":\"1\",\"available_status\":true,\"id_empresa\":12,\"sucursal\":1,\"fecha_inicio\":\"2026-07-10\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"usuario_id\":110}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 18:17:51', '2026-07-10 18:17:51'),
(3, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":null,\"usuario\":\"admin\",\"clave\":\"$2y$12$5Fd.8bGpyECeP5.igqefzOGm8EyBFO5VclMgWsDrfH3AeI.y2JbJO\",\"email\":\"admin@admin.com\",\"nombres\":\"Admin\",\"apellidos\":\"Sistema\",\"rubro\":null,\"sucursal\":1,\"telefono\":null,\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"zp0NQ77TYhDjVvshD3Hjt2p5N81kz3a0Y3T8F2LKcC2Hqz0DMyQD3zE8zIgo\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"remember_token\":\"7zXCCBKx3uFespo4UIwCB8eHLMIzfjCPF217VZdACVNGIaCvWYDoOIybvoOO\"}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/panel/logout', 'POST', '2026-07-10 18:39:06', '2026-07-10 18:39:06'),
(4, 107, 'admin', '1', 12, 'created', 'App\\Models\\Producto', '419', NULL, '{\"cod_barra\":\"23135485\",\"codigo\":\"31564165\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"peso_bruto\":12,\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":12,\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"activo\":true,\"imagen\":null,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"id_producto\":419}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 18:42:11', '2026-07-10 18:42:11'),
(5, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '419', '{\"id_producto\":419,\"codigo\":\"31564165\",\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\",\"peso_bruto\":12,\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"precio\":120,\"costo\":80,\"cantidad\":null,\"activo\":1,\"imagen\":null,\"id_empresa\":12}', '{\"activo\":true,\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\"}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 18:42:38', '2026-07-10 18:42:38'),
(6, 107, 'admin', '1', 12, 'created', 'App\\Models\\Caja', '4', NULL, '{\"nombre\":\"caja admin\",\"id_usuario_responsable\":107,\"estado\":\"ACTIVA\",\"id_empresa\":12,\"sucursal\":1,\"saldo_actual\":0,\"moneda\":\"PEN\",\"id_caja_padre\":null,\"id\":4}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 18:54:28', '2026-07-10 18:54:28'),
(7, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '419', '{\"id_producto\":419,\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"cantidad\":null,\"iscbp\":null,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":null,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":1,\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\"}', '{\"cantidad\":10}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 18:56:09', '2026-07-10 18:56:09'),
(8, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '14', NULL, '{\"id_empresa\":12,\"almacen\":31564165,\"id_producto\":419,\"tipo\":\"I\",\"id_motivo\":1,\"cantidad\":10,\"stock_anterior\":0,\"stock_nuevo\":10,\"costo\":80,\"observacion\":\"adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10T14:56:09.545698Z\",\"id_movimiento\":14}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 18:56:09', '2026-07-10 18:56:09'),
(9, 107, 'admin', '1', 12, 'created', 'App\\Models\\Producto', '420', NULL, '{\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"cantidad\":0,\"iscbp\":null,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":31564165,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":1,\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\",\"id_producto\":420}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:04:23', '2026-07-10 19:04:23'),
(10, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '420', '{\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"cantidad\":0,\"iscbp\":null,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":31564165,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":1,\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\",\"id_producto\":420}', '{\"costo\":10,\"cantidad\":10}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:04:23', '2026-07-10 19:04:23'),
(11, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '15', NULL, '{\"id_empresa\":12,\"almacen\":31564165,\"id_producto\":420,\"tipo\":\"I\",\"id_motivo\":1,\"cantidad\":10,\"stock_anterior\":0,\"stock_nuevo\":10,\"costo\":10,\"observacion\":\"adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10T15:04:23.875717Z\",\"id_movimiento\":15}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:04:23', '2026-07-10 19:04:23'),
(12, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '420', '{\"id_producto\":420,\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":10,\"cantidad\":10,\"iscbp\":null,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"31564165\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":1,\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\"}', '{\"cantidad\":5}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:04:51', '2026-07-10 19:04:51'),
(13, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '16', NULL, '{\"id_empresa\":12,\"almacen\":31564165,\"id_producto\":420,\"tipo\":\"S\",\"id_motivo\":8,\"cantidad\":5,\"stock_anterior\":10,\"stock_nuevo\":5,\"costo\":null,\"observacion\":\"adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10T15:04:51.707599Z\",\"id_movimiento\":16}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:04:51', '2026-07-10 19:04:51'),
(14, 107, 'admin', '1', 12, 'created', 'App\\Models\\Caja', '5', NULL, '{\"nombre\":\"caja admin\",\"id_usuario_responsable\":107,\"id_caja_padre\":4,\"estado\":\"ACTIVA\",\"id_empresa\":12,\"sucursal\":1,\"saldo_actual\":0,\"moneda\":\"PEN\",\"id\":5}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:05:44', '2026-07-10 19:05:44'),
(15, 107, 'admin', '1', 12, 'created', 'App\\Models\\Compra', '162', NULL, '{\"id_proveedor\":181,\"id_tido\":2,\"id_tipo_pago\":1,\"instrumento_tipo\":\"EFECTIVO\",\"instrumento_id\":null,\"fecha_emision\":\"2026-07-10T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-10T00:00:00.000000Z\",\"direccion\":\"Convertido de cotizaci\\u00f3n N\\u00b0 2953\",\"serie\":\"F001\",\"numero\":\"665412\",\"total\":2800,\"id_empresa\":12,\"sucursal\":1,\"moneda\":\"S\",\"recepcionado\":0,\"id_compra\":162}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:07:51', '2026-07-10 19:07:51'),
(16, 107, 'admin', '1', 12, 'created', 'App\\Models\\Producto', '421', NULL, '{\"cod_barra\":\"\",\"descripcion\":\"ARROZ EXTRA SACO 10KG (TEST)\",\"precio\":42,\"costo\":35,\"cantidad\":80,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":10.5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":31564165,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-001\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null,\"id_producto\":421}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:27:17', '2026-07-10 19:27:17'),
(17, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '17', NULL, '{\"id_empresa\":12,\"almacen\":31564165,\"id_producto\":421,\"tipo\":\"I\",\"id_motivo\":2,\"cantidad\":80,\"stock_anterior\":0,\"stock_nuevo\":80,\"costo\":\"35\",\"observacion\":\"Recepci\\u00f3n #3 (compra #162)\",\"id_usuario\":107,\"fecha\":\"2026-07-10T15:27:17.132879Z\",\"id_movimiento\":17}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:27:17', '2026-07-10 19:27:17'),
(18, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Compra', '162', '{\"id_compra\":162,\"id_tido\":2,\"id_tipo_pago\":1,\"instrumento_tipo\":\"EFECTIVO\",\"instrumento_id\":null,\"id_proveedor\":181,\"fecha_emision\":\"2026-07-10T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-10T00:00:00.000000Z\",\"dias_pagos\":null,\"direccion\":\"Convertido de cotizaci\\u00f3n N\\u00b0 2953\",\"serie\":\"F001\",\"numero\":\"665412\",\"total\":2800,\"recepcionado\":0,\"id_empresa\":12,\"moneda\":\"S\",\"sucursal\":1}', '{\"recepcionado\":1}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:27:17', '2026-07-10 19:27:17'),
(19, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '416', '{\"id_producto\":416,\"cod_barra\":\"\",\"descripcion\":\"ACEITE VEGETAL CAJA 12X1L (TEST)\",\"precio\":96,\"costo\":78,\"cantidad\":990,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12.8,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-003\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null}', '{\"cantidad\":90}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:27:50', '2026-07-10 19:27:50'),
(20, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '18', NULL, '{\"id_empresa\":12,\"almacen\":1,\"id_producto\":416,\"tipo\":\"S\",\"id_motivo\":9,\"cantidad\":900,\"stock_anterior\":990,\"stock_nuevo\":90,\"costo\":78,\"observacion\":\"Traslado a almacen4. adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10T15:27:50.356057Z\",\"id_movimiento\":18}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:27:50', '2026-07-10 19:27:50'),
(21, 107, 'admin', '1', 12, 'created', 'App\\Models\\Producto', '422', NULL, '{\"cod_barra\":\"\",\"descripcion\":\"ACEITE VEGETAL CAJA 12X1L (TEST)\",\"precio\":96,\"costo\":78,\"cantidad\":900,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12.8,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":31564165,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-003\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null,\"id_producto\":422}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:27:50', '2026-07-10 19:27:50'),
(22, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '19', NULL, '{\"id_empresa\":12,\"almacen\":31564165,\"id_producto\":422,\"tipo\":\"I\",\"id_motivo\":5,\"cantidad\":900,\"stock_anterior\":0,\"stock_nuevo\":900,\"costo\":78,\"observacion\":\"Traslado desde Almac\\u00e9n 1. adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10T15:27:50.370547Z\",\"id_movimiento\":19}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:27:50', '2026-07-10 19:27:50'),
(23, 107, 'admin', '1', 12, 'created', 'App\\Models\\Prestamo', '1', NULL, '{\"id_empresa\":12,\"tipo\":\"R\",\"tercero\":\"ard\\u00e1is Mario\",\"almacen\":31564165,\"estado\":\"P\",\"observacion\":\"adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10T15:28:52.601891Z\",\"id_producto\":409,\"cantidad\":100,\"id_prestamo\":1}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:28:52', '2026-07-10 19:28:52'),
(24, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '409', '{\"id_producto\":409,\"cod_barra\":null,\"descripcion\":\"JAB\\u00d3N DE ROPA BELTRA*175 GMS\",\"precio\":45,\"costo\":43,\"cantidad\":1,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"Jabel0001\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":1,\"razon_social\":\"CORPORACION BELTRAN ESPINOZA E.I.R.L. - COBELES E.I.R.L.\",\"ruc\":\"20602096808\",\"estado\":\"1\",\"almacen\":\"2\",\"precio2\":46,\"precio3\":47,\"precio4\":44,\"precio_unidad\":44,\"codigo\":\"Jabel0001\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Cajas\",\"presentaciones\":\"2\",\"cnt_presenta\":\"1,2,3\"}', '{\"cantidad\":101}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:28:52', '2026-07-10 19:28:52'),
(25, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '20', NULL, '{\"id_empresa\":12,\"almacen\":\"31564165\",\"id_producto\":409,\"tipo\":\"I\",\"id_motivo\":12,\"cantidad\":100,\"stock_anterior\":1,\"stock_nuevo\":101,\"costo\":43,\"observacion\":\"Pr\\u00e9stamo de ard\\u00e1is Mario\",\"id_usuario\":107,\"fecha\":\"2026-07-10T15:28:52.626124Z\",\"id_movimiento\":20}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:28:52', '2026-07-10 19:28:52'),
(26, 107, 'admin', '1', 12, 'created', 'App\\Models\\Prestamo', '2', NULL, '{\"id_empresa\":12,\"tipo\":\"R\",\"tercero\":\"adadadad\",\"almacen\":31564165,\"estado\":\"P\",\"observacion\":\"aada dawda wd\",\"id_usuario\":107,\"fecha\":\"2026-07-10T15:33:45.965671Z\",\"id_producto\":418,\"cantidad\":900,\"id_prestamo\":2}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:33:45', '2026-07-10 19:33:45'),
(27, 107, 'admin', '1', 12, 'created', 'App\\Models\\Producto', '423', NULL, '{\"cod_barra\":\"\",\"descripcion\":\"LECHE EVAPORADA PACK 48UND (TEST)\",\"precio\":168,\"costo\":140,\"cantidad\":900,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":20.4,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"31564165\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-005\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null,\"id_producto\":423}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:33:45', '2026-07-10 19:33:45'),
(28, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '21', NULL, '{\"id_empresa\":12,\"almacen\":\"31564165\",\"id_producto\":423,\"tipo\":\"I\",\"id_motivo\":12,\"cantidad\":900,\"stock_anterior\":0,\"stock_nuevo\":900,\"costo\":140,\"observacion\":\"Pr\\u00e9stamo de adadadad\",\"id_usuario\":107,\"fecha\":\"2026-07-10T15:33:45.985014Z\",\"id_movimiento\":21}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:33:45', '2026-07-10 19:33:45'),
(29, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '409', '{\"id_producto\":409,\"cod_barra\":null,\"descripcion\":\"JAB\\u00d3N DE ROPA BELTRA*175 GMS\",\"precio\":45,\"costo\":43,\"cantidad\":101,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"Jabel0001\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":1,\"razon_social\":\"CORPORACION BELTRAN ESPINOZA E.I.R.L. - COBELES E.I.R.L.\",\"ruc\":\"20602096808\",\"estado\":\"1\",\"almacen\":\"2\",\"precio2\":46,\"precio3\":47,\"precio4\":44,\"precio_unidad\":44,\"codigo\":\"Jabel0001\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Cajas\",\"presentaciones\":\"2\",\"cnt_presenta\":\"1,2,3\"}', '{\"cantidad\":91}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:34:43', '2026-07-10 19:34:43'),
(30, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '22', NULL, '{\"id_empresa\":12,\"almacen\":\"31564165\",\"id_producto\":409,\"tipo\":\"S\",\"id_motivo\":11,\"cantidad\":10,\"stock_anterior\":101,\"stock_nuevo\":91,\"costo\":43,\"observacion\":\"Devoluci\\u00f3n a ard\\u00e1is Mario\",\"id_usuario\":107,\"fecha\":\"2026-07-10T15:34:43.788959Z\",\"id_movimiento\":22}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:34:43', '2026-07-10 19:34:43'),
(31, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Prestamo', '1', '{\"id_prestamo\":1,\"id_empresa\":12,\"tipo\":\"R\",\"tercero\":\"ard\\u00e1is Mario\",\"id_producto\":409,\"almacen\":\"31564165\",\"cantidad\":100,\"estado\":\"P\",\"observacion\":\"adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10 15:28:52\",\"fecha_devolucion\":null}', '{\"estado\":\"X\"}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 19:34:43', '2026-07-10 19:34:43'),
(32, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '423', '{\"id_producto\":423,\"cod_barra\":\"\",\"descripcion\":\"LECHE EVAPORADA PACK 48UND (TEST)\",\"precio\":168,\"costo\":140,\"cantidad\":900,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":20.4,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"31564165\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-005\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null}', '{\"cantidad\":899}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 20:15:29', '2026-07-10 20:15:29'),
(33, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '23', NULL, '{\"id_empresa\":12,\"almacen\":\"31564165\",\"id_producto\":423,\"tipo\":\"S\",\"id_motivo\":11,\"cantidad\":1,\"stock_anterior\":900,\"stock_nuevo\":899,\"costo\":140,\"observacion\":\"Devoluci\\u00f3n a adadadad\",\"id_usuario\":107,\"fecha\":\"2026-07-10T16:15:29.624370Z\",\"id_movimiento\":23}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 20:15:29', '2026-07-10 20:15:29'),
(34, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Prestamo', '2', '{\"id_prestamo\":2,\"id_empresa\":12,\"tipo\":\"R\",\"tercero\":\"adadadad\",\"id_producto\":418,\"almacen\":\"31564165\",\"cantidad\":900,\"estado\":\"P\",\"observacion\":\"aada dawda wd\",\"id_usuario\":107,\"fecha\":\"2026-07-10 15:33:45\",\"fecha_devolucion\":null}', '{\"estado\":\"X\"}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 20:15:29', '2026-07-10 20:15:29'),
(35, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '423', '{\"id_producto\":423,\"cod_barra\":\"\",\"descripcion\":\"LECHE EVAPORADA PACK 48UND (TEST)\",\"precio\":168,\"costo\":140,\"cantidad\":899,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":20.4,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"31564165\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-005\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null}', '{\"cantidad\":898}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 20:18:53', '2026-07-10 20:18:53'),
(36, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '24', NULL, '{\"id_empresa\":12,\"almacen\":\"31564165\",\"id_producto\":423,\"tipo\":\"S\",\"id_motivo\":11,\"cantidad\":1,\"stock_anterior\":899,\"stock_nuevo\":898,\"costo\":140,\"observacion\":\"Devoluci\\u00f3n a adadadad\",\"id_usuario\":107,\"fecha\":\"2026-07-10T16:18:53.681237Z\",\"id_movimiento\":24}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 20:18:53', '2026-07-10 20:18:53'),
(37, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '423', '{\"id_producto\":423,\"cod_barra\":\"\",\"descripcion\":\"LECHE EVAPORADA PACK 48UND (TEST)\",\"precio\":168,\"costo\":140,\"cantidad\":898,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":20.4,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"31564165\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-005\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null}', '{\"cantidad\":0}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 20:20:13', '2026-07-10 20:20:13'),
(38, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '25', NULL, '{\"id_empresa\":12,\"almacen\":\"31564165\",\"id_producto\":423,\"tipo\":\"S\",\"id_motivo\":11,\"cantidad\":898,\"stock_anterior\":898,\"stock_nuevo\":0,\"costo\":140,\"observacion\":\"Devoluci\\u00f3n a adadadad\",\"id_usuario\":107,\"fecha\":\"2026-07-10T16:20:13.826324Z\",\"id_movimiento\":25}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 20:20:13', '2026-07-10 20:20:13'),
(39, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Prestamo', '2', '{\"id_prestamo\":2,\"id_empresa\":12,\"tipo\":\"R\",\"tercero\":\"adadadad\",\"id_producto\":418,\"almacen\":\"31564165\",\"cantidad\":900,\"estado\":\"X\",\"observacion\":\"aada dawda wd\",\"id_usuario\":107,\"fecha\":\"2026-07-10 15:33:45\",\"fecha_devolucion\":null}', '{\"estado\":\"D\",\"fecha_devolucion\":\"2026-07-10T16:20:13.855551Z\"}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 20:20:13', '2026-07-10 20:20:13'),
(40, 107, 'admin', '1', 12, 'created', 'App\\Models\\Traslado', '1', NULL, '{\"id_empresa\":12,\"almacen_origen\":1,\"almacen_destino\":31564165,\"fecha\":\"2026-07-10T17:03:49.000000Z\",\"observacion\":\"adadadadaw\",\"id_usuario\":107,\"estado\":\"1\",\"id_traslado\":1}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:03:49', '2026-07-10 21:03:49'),
(41, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '412', '{\"id_producto\":412,\"cod_barra\":\"\",\"descripcion\":\"LENTEJA  BB VERDE *SACO VERDE\",\"precio\":135,\"costo\":120,\"cantidad\":451,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"100198\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":45.36,\"razon_social\":\"INTERCOMPANY Y SR HUANCA                                    \",\"ruc\":\"20468985757\",\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":136,\"precio3\":137,\"precio4\":135,\"precio_unidad\":133,\"codigo\":\"100198\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":\"4\",\"cnt_presenta\":\"1,2,3,4,5,6\"}', '{\"cantidad\":51}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:03:49', '2026-07-10 21:03:49'),
(42, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '26', NULL, '{\"id_empresa\":12,\"almacen\":1,\"id_producto\":412,\"tipo\":\"S\",\"id_motivo\":9,\"cantidad\":400,\"stock_anterior\":451,\"stock_nuevo\":51,\"costo\":120,\"observacion\":\"Traslado TS-00000001 a almacen4. adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10T17:03:49.502875Z\",\"id_movimiento\":26}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:03:49', '2026-07-10 21:03:49'),
(43, 107, 'admin', '1', 12, 'created', 'App\\Models\\Producto', '424', NULL, '{\"cod_barra\":\"\",\"descripcion\":\"LENTEJA  BB VERDE *SACO VERDE\",\"precio\":135,\"costo\":120,\"cantidad\":400,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"100198\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":45.36,\"razon_social\":\"INTERCOMPANY Y SR HUANCA                                    \",\"ruc\":\"20468985757\",\"estado\":\"1\",\"almacen\":31564165,\"precio2\":136,\"precio3\":137,\"precio4\":135,\"precio_unidad\":133,\"codigo\":\"100198\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":\"4\",\"cnt_presenta\":\"1,2,3,4,5,6\",\"id_producto\":424}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:03:49', '2026-07-10 21:03:49'),
(44, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '27', NULL, '{\"id_empresa\":12,\"almacen\":31564165,\"id_producto\":424,\"tipo\":\"I\",\"id_motivo\":5,\"cantidad\":400,\"stock_anterior\":0,\"stock_nuevo\":400,\"costo\":120,\"observacion\":\"Traslado TS-00000001 desde Almac\\u00e9n 1. adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10T17:03:49.502875Z\",\"id_movimiento\":27}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:03:49', '2026-07-10 21:03:49'),
(45, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '415', '{\"id_producto\":415,\"cod_barra\":\"\",\"descripcion\":\"AZUCAR RUBIA BOLSA 5KG (TEST)\",\"precio\":24.5,\"costo\":19,\"cantidad\":1000,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5.2,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-002\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null}', '{\"cantidad\":500}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:03:49', '2026-07-10 21:03:49'),
(46, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '28', NULL, '{\"id_empresa\":12,\"almacen\":1,\"id_producto\":415,\"tipo\":\"S\",\"id_motivo\":9,\"cantidad\":500,\"stock_anterior\":1000,\"stock_nuevo\":500,\"costo\":19,\"observacion\":\"Traslado TS-00000001 a almacen4. adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10T17:03:49.502875Z\",\"id_movimiento\":28}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:03:49', '2026-07-10 21:03:49'),
(47, 107, 'admin', '1', 12, 'created', 'App\\Models\\Producto', '425', NULL, '{\"cod_barra\":\"\",\"descripcion\":\"AZUCAR RUBIA BOLSA 5KG (TEST)\",\"precio\":24.5,\"costo\":19,\"cantidad\":500,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5.2,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":31564165,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-002\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null,\"id_producto\":425}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:03:49', '2026-07-10 21:03:49'),
(48, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '29', NULL, '{\"id_empresa\":12,\"almacen\":31564165,\"id_producto\":425,\"tipo\":\"I\",\"id_motivo\":5,\"cantidad\":500,\"stock_anterior\":0,\"stock_nuevo\":500,\"costo\":19,\"observacion\":\"Traslado TS-00000001 desde Almac\\u00e9n 1. adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-10T17:03:49.502875Z\",\"id_movimiento\":29}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:03:49', '2026-07-10 21:03:49'),
(49, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '424', '{\"id_producto\":424,\"cod_barra\":\"\",\"descripcion\":\"LENTEJA  BB VERDE *SACO VERDE\",\"precio\":135,\"costo\":120,\"cantidad\":400,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"100198\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":45.36,\"razon_social\":\"INTERCOMPANY Y SR HUANCA                                    \",\"ruc\":\"20468985757\",\"estado\":\"1\",\"almacen\":\"31564165\",\"precio2\":136,\"precio3\":137,\"precio4\":135,\"precio_unidad\":133,\"codigo\":\"100198\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":\"4\",\"cnt_presenta\":\"1,2,3,4,5,6\"}', '{\"cantidad\":300}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:06:26', '2026-07-10 21:06:26'),
(50, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '30', NULL, '{\"id_empresa\":12,\"almacen\":\"31564165\",\"id_producto\":424,\"tipo\":\"S\",\"id_motivo\":15,\"cantidad\":100,\"stock_anterior\":400,\"stock_nuevo\":300,\"costo\":\"120.0000\",\"observacion\":\"Ajuste TS-00000001: \\\"LENTEJA  BB VERDE *SACO VERDE\\\" devuelve a Almac\\u00e9n 1\",\"id_usuario\":107,\"fecha\":\"2026-07-10T17:06:26.101814Z\",\"id_movimiento\":30}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:06:26', '2026-07-10 21:06:26'),
(51, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '412', '{\"id_producto\":412,\"cod_barra\":\"\",\"descripcion\":\"LENTEJA  BB VERDE *SACO VERDE\",\"precio\":135,\"costo\":120,\"cantidad\":51,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"100198\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":45.36,\"razon_social\":\"INTERCOMPANY Y SR HUANCA                                    \",\"ruc\":\"20468985757\",\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":136,\"precio3\":137,\"precio4\":135,\"precio_unidad\":133,\"codigo\":\"100198\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":\"4\",\"cnt_presenta\":\"1,2,3,4,5,6\"}', '{\"cantidad\":151}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:06:26', '2026-07-10 21:06:26'),
(52, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '31', NULL, '{\"id_empresa\":12,\"almacen\":\"1\",\"id_producto\":412,\"tipo\":\"I\",\"id_motivo\":14,\"cantidad\":100,\"stock_anterior\":51,\"stock_nuevo\":151,\"costo\":\"120.0000\",\"observacion\":\"Ajuste TS-00000001: \\\"LENTEJA  BB VERDE *SACO VERDE\\\" regresa desde almacen4\",\"id_usuario\":107,\"fecha\":\"2026-07-10T17:06:26.112595Z\",\"id_movimiento\":31}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:06:26', '2026-07-10 21:06:26'),
(53, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '425', '{\"id_producto\":425,\"cod_barra\":\"\",\"descripcion\":\"AZUCAR RUBIA BOLSA 5KG (TEST)\",\"precio\":24.5,\"costo\":19,\"cantidad\":500,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5.2,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"31564165\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-002\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null}', '{\"cantidad\":250}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:06:27', '2026-07-10 21:06:27'),
(54, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '32', NULL, '{\"id_empresa\":12,\"almacen\":\"31564165\",\"id_producto\":425,\"tipo\":\"S\",\"id_motivo\":15,\"cantidad\":250,\"stock_anterior\":500,\"stock_nuevo\":250,\"costo\":\"19.0000\",\"observacion\":\"Ajuste TS-00000001: \\\"AZUCAR RUBIA BOLSA 5KG (TEST)\\\" devuelve a Almac\\u00e9n 1\",\"id_usuario\":107,\"fecha\":\"2026-07-10T17:06:27.739418Z\",\"id_movimiento\":32}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:06:27', '2026-07-10 21:06:27'),
(55, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '415', '{\"id_producto\":415,\"cod_barra\":\"\",\"descripcion\":\"AZUCAR RUBIA BOLSA 5KG (TEST)\",\"precio\":24.5,\"costo\":19,\"cantidad\":500,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5.2,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-002\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null}', '{\"cantidad\":750}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:06:27', '2026-07-10 21:06:27'),
(56, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '33', NULL, '{\"id_empresa\":12,\"almacen\":\"1\",\"id_producto\":415,\"tipo\":\"I\",\"id_motivo\":14,\"cantidad\":250,\"stock_anterior\":500,\"stock_nuevo\":750,\"costo\":\"19.0000\",\"observacion\":\"Ajuste TS-00000001: \\\"AZUCAR RUBIA BOLSA 5KG (TEST)\\\" regresa desde almacen4\",\"id_usuario\":107,\"fecha\":\"2026-07-10T17:06:27.753661Z\",\"id_movimiento\":33}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:06:27', '2026-07-10 21:06:27'),
(57, 107, 'admin', '1', 12, 'created', 'App\\Models\\Compra', '163', NULL, '{\"id_proveedor\":182,\"id_tido\":2,\"id_tipo_pago\":1,\"instrumento_tipo\":\"EFECTIVO\",\"instrumento_id\":null,\"fecha_emision\":\"2026-07-10T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-10T00:00:00.000000Z\",\"direccion\":\"Convertido de cotizaci\\u00f3n N\\u00b0 2953\",\"serie\":\"F001\",\"numero\":\"8875541\",\"total\":136800,\"id_empresa\":12,\"sucursal\":1,\"moneda\":\"S\",\"recepcionado\":0,\"id_compra\":163}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:08:16', '2026-07-10 21:08:16'),
(58, 107, 'admin', '1', 12, 'created', 'App\\Models\\Producto', '426', NULL, '{\"cod_barra\":null,\"descripcion\":\"Az\\u00facar Cartavio Blaco\",\"precio\":157,\"costo\":152,\"cantidad\":900,\"iscbp\":0,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"13422\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":1,\"razon_social\":\"MANUEL QUISPE                                               \",\"ruc\":\"10099666922\",\"estado\":\"1\",\"almacen\":31564165,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"13422\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":1,\"medida\":\"Sacos\",\"presentaciones\":\"4\",\"cnt_presenta\":\"1,2,3,4,5\",\"id_producto\":426}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:08:27', '2026-07-10 21:08:27'),
(59, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '34', NULL, '{\"id_empresa\":12,\"almacen\":31564165,\"id_producto\":426,\"tipo\":\"I\",\"id_motivo\":2,\"cantidad\":900,\"stock_anterior\":0,\"stock_nuevo\":900,\"costo\":\"152\",\"observacion\":\"Recepci\\u00f3n #4 (compra #163)\",\"id_usuario\":107,\"fecha\":\"2026-07-10T17:08:27.876785Z\",\"id_movimiento\":34}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:08:27', '2026-07-10 21:08:27'),
(60, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Compra', '163', '{\"id_compra\":163,\"id_tido\":2,\"id_tipo_pago\":1,\"instrumento_tipo\":\"EFECTIVO\",\"instrumento_id\":null,\"id_proveedor\":182,\"fecha_emision\":\"2026-07-10T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-10T00:00:00.000000Z\",\"dias_pagos\":null,\"direccion\":\"Convertido de cotizaci\\u00f3n N\\u00b0 2953\",\"serie\":\"F001\",\"numero\":\"8875541\",\"total\":136800,\"recepcionado\":0,\"id_empresa\":12,\"moneda\":\"S\",\"sucursal\":1}', '{\"recepcionado\":1}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:08:27', '2026-07-10 21:08:27'),
(61, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Caja', '4', '{\"id\":4,\"id_empresa\":12,\"sucursal\":1,\"nombre\":\"caja admin\",\"id_usuario_responsable\":107,\"id_caja_padre\":null,\"saldo_actual\":1700,\"moneda\":\"PEN\",\"estado\":\"ACTIVA\"}', '{\"nombre\":\"caja admin principal\"}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-10 21:11:05', '2026-07-10 21:11:05');
INSERT INTO `audits` (`id`, `user_id`, `user_name`, `user_rol`, `empresa_id`, `event`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `method`, `created_at`, `updated_at`) VALUES
(62, 107, 'admin', '1', 12, 'created', 'App\\Models\\GuiaRemision', '424', NULL, '{\"id_venta\":263,\"motivo_traslado\":\"01\",\"fecha_emision\":\"2026-07-10T00:00:00.000000Z\",\"fecha_traslado\":\"2026-07-10T00:00:00.000000Z\",\"dir_llegada\":\"sdcsdcsd\",\"ubigeo\":\"010601\",\"dir_partida\":\"SAN MARTIN DE PORRES\",\"ubigeo_partida\":\"150135\",\"tipo_transporte\":1,\"ruc_transporte\":null,\"razon_transporte\":null,\"transportista_nro_mtc\":null,\"vehiculo\":\"fgnhbnhgfnhg\",\"conductor_documento\":\"77425200\",\"conductor_nombres\":\"EMER RODRIGO\",\"conductor_apellidos\":\"YARLEQUE ZAPATA\",\"conductor_licencia\":\"sdfvdsav2\",\"peso\":1,\"und_peso_total\":\"KGM\",\"nro_bultos\":1,\"serie\":\"T001\",\"numero\":1032,\"estado\":\"1\",\"enviado_sunat\":\"0\",\"estado_gre\":\"pendiente\",\"id_empresa\":12,\"sucursal\":1,\"id_guia_remision\":424}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-11 00:53:36', '2026-07-11 00:53:36'),
(63, 107, 'admin', '1', 12, 'updated', 'App\\Models\\GuiaRemision', '424', '{\"id_venta\":263,\"motivo_traslado\":\"01\",\"fecha_emision\":\"2026-07-10T00:00:00.000000Z\",\"fecha_traslado\":\"2026-07-10T00:00:00.000000Z\",\"dir_llegada\":\"sdcsdcsd\",\"ubigeo\":\"010601\",\"dir_partida\":\"SAN MARTIN DE PORRES\",\"ubigeo_partida\":\"150135\",\"tipo_transporte\":1,\"ruc_transporte\":null,\"razon_transporte\":null,\"transportista_nro_mtc\":null,\"vehiculo\":\"fgnhbnhgfnhg\",\"conductor_documento\":\"77425200\",\"conductor_nombres\":\"EMER RODRIGO\",\"conductor_apellidos\":\"YARLEQUE ZAPATA\",\"conductor_licencia\":\"sdfvdsav2\",\"peso\":1,\"und_peso_total\":\"KGM\",\"nro_bultos\":1,\"serie\":\"T001\",\"numero\":1032,\"estado\":\"1\",\"enviado_sunat\":\"0\",\"estado_gre\":\"pendiente\",\"id_empresa\":12,\"sucursal\":1,\"id_guia_remision\":424}', '{\"nombre_xml\":\"20000000001-09-T001-1032\",\"hash\":\"Jwin\\/IyftiifJoOd3P4edHET1l0=\",\"xml_ruta\":\"sunat\\/xml\\/20000000001\\/20000000001-09-T001-1032.xml\",\"mensaje_sunat\":\"XML generado, pendiente de env\\u00edo.\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-11 00:53:36', '2026-07-11 00:53:36'),
(64, 107, 'admin', '1', 12, 'created', 'App\\Models\\Compra', '164', NULL, '{\"id_proveedor\":181,\"id_tido\":2,\"id_tipo_pago\":1,\"instrumento_tipo\":\"BILLETERA_DIGITAL\",\"instrumento_id\":2,\"fecha_emision\":\"2026-07-19T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-19T00:00:00.000000Z\",\"direccion\":\"aadadadawd\",\"serie\":\"F001\",\"numero\":\"3322\",\"total\":35,\"id_empresa\":12,\"sucursal\":1,\"moneda\":\"S\",\"recepcionado\":0,\"id_compra\":164}', '38.252.222.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://magus-qa.com/molitalia/livewire-a17b28a7/update', 'POST', '2026-07-19 09:54:54', '2026-07-19 09:54:54'),
(65, 107, 'admin', '1', 12, 'created', 'App\\Models\\Cotizacion', '51496', NULL, '{\"numero\":2977,\"id_tido\":6,\"id_tipo_pago\":1,\"fecha\":\"2026-07-21T00:00:00.000000Z\",\"direccion\":\"sdcsdcsd\",\"id_cliente\":2516,\"total\":45,\"estado\":\"1\",\"id_empresa\":12,\"sucursal\":1,\"usar_precio\":1,\"moneda\":1,\"id_usuario\":107,\"observacion\":\"sdfcvsfvc\",\"fecha_registro\":\"2026-07-21T13:58:03.171598Z\",\"cotizacion_id\":51496}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-21 17:58:03', '2026-07-21 17:58:03'),
(66, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '110', '{\"usuario_id\":110,\"id_empresa\":\"12\",\"id_rol\":\"5\",\"num_doc\":\"77425200\",\"usuario\":\"conta\",\"clave\":\"$2y$12$uYeCOT6driqWFkWWQJNjJ.yfrHPdyiqa7ET6nBBwiQ7awqclCBObu\",\"email\":\"adan2025zapata@gmail.com\",\"nombres\":\"EMER RODRIGO\",\"apellidos\":\"YARLEQUE ZAPATA\",\"rubro\":null,\"sucursal\":\"1\",\"telefono\":\"993321920\",\"foto\":\"usuarios\\/fotos\\/01KX668MCG9RKRK7REXJDMF9Y2.jpg\",\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-07-10\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":null,\"updated_at\":null,\"created_at\":null}', '{\"clave\":\"$2y$12$atNOstr8CGjhrS2ImtRkzODxFd3sPTIIMk8tPxwb5RNChbwQ46eLC\",\"foto\":null}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 18:57:36', '2026-07-22 18:57:36'),
(67, 107, 'admin', '1', 12, 'created', 'App\\Models\\GuiaRemision', '425', NULL, '{\"id_venta\":263,\"motivo_traslado\":\"01\",\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_traslado\":\"2026-07-22T00:00:00.000000Z\",\"dir_llegada\":\"sdcsdcsd\",\"ubigeo\":\"030202\",\"dir_partida\":\"SAN MARTIN DE PORRES\",\"ubigeo_partida\":\"150135\",\"tipo_transporte\":1,\"ruc_transporte\":null,\"razon_transporte\":null,\"transportista_nro_mtc\":null,\"vehiculo\":\"efrvdsf345345\",\"conductor_documento\":\"77425200\",\"conductor_nombres\":\"EMER RODRIGO\",\"conductor_apellidos\":\"YARLEQUE ZAPATA\",\"conductor_licencia\":\"cfdssdfvds\",\"peso\":1,\"und_peso_total\":\"KGM\",\"nro_bultos\":1,\"serie\":\"T001\",\"numero\":1033,\"estado\":\"1\",\"enviado_sunat\":\"0\",\"estado_gre\":\"pendiente\",\"id_empresa\":12,\"sucursal\":1,\"id_guia_remision\":425}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:00:51', '2026-07-22 19:00:51'),
(68, 107, 'admin', '1', 12, 'updated', 'App\\Models\\GuiaRemision', '425', '{\"id_venta\":263,\"motivo_traslado\":\"01\",\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_traslado\":\"2026-07-22T00:00:00.000000Z\",\"dir_llegada\":\"sdcsdcsd\",\"ubigeo\":\"030202\",\"dir_partida\":\"SAN MARTIN DE PORRES\",\"ubigeo_partida\":\"150135\",\"tipo_transporte\":1,\"ruc_transporte\":null,\"razon_transporte\":null,\"transportista_nro_mtc\":null,\"vehiculo\":\"efrvdsf345345\",\"conductor_documento\":\"77425200\",\"conductor_nombres\":\"EMER RODRIGO\",\"conductor_apellidos\":\"YARLEQUE ZAPATA\",\"conductor_licencia\":\"cfdssdfvds\",\"peso\":1,\"und_peso_total\":\"KGM\",\"nro_bultos\":1,\"serie\":\"T001\",\"numero\":1033,\"estado\":\"1\",\"enviado_sunat\":\"0\",\"estado_gre\":\"pendiente\",\"id_empresa\":12,\"sucursal\":1,\"id_guia_remision\":425}', '{\"nombre_xml\":\"20000000001-09-T001-1033\",\"hash\":\"wE2vpuqCsGdo8GDCIHDDwFvxGpI=\",\"xml_ruta\":\"sunat\\/xml\\/20000000001\\/20000000001-09-T001-1033.xml\",\"mensaje_sunat\":\"XML generado, pendiente de env\\u00edo.\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:00:52', '2026-07-22 19:00:52'),
(69, 107, 'admin', '1', 12, 'created', 'App\\Models\\Proveedor', '187', NULL, '{\"ruc\":\"76165962\",\"razon_social\":\"VICTOR RAUL CANCHARI RIQUI\",\"nombre_comercial\":\"victor 2\",\"direccion\":\"aaaaaaaaaaaaaaaa sad adwd \",\"telefono\":\"92670321\",\"email\":\"vcanchari38@gmail.com\",\"id_empresa\":12,\"fecha_create\":\"2026-07-22T15:04:37.571191Z\",\"estado\":1,\"direccion2\":\"\",\"telefono2\":\"\",\"proveedor_id\":187}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:04:37', '2026-07-22 19:04:37'),
(70, 107, 'admin', '1', 12, 'created', 'App\\Models\\Cliente', '2539', NULL, '{\"documento\":\"44925551\",\"datos\":\"JANETT NILDA RIQUI PI\\u00d1AS\",\"direccion\":\"PSJ.INCA ROCA MZ. 131 LT.33\",\"ubigeo\":\"150112\",\"distrito\":\"INDEPENDENCIA\",\"mercado\":null,\"telefono\":\"92670321\",\"email\":\"vcanchari38@gmail.com\",\"id_empresa\":12,\"id_cliente\":2539}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:05:42', '2026-07-22 19:05:42'),
(71, 107, 'admin', '1', 12, 'created', 'App\\Models\\User', '111', NULL, '{\"num_doc\":\"76165962\",\"nombres\":\"Victor\",\"apellidos\":\"Canchari\",\"telefono\":\"92670321\",\"usuario\":\"victor12\",\"email\":\"vcanchari@gmail.com\",\"id_rol\":7,\"foto\":null,\"estado\":\"1\",\"available_status\":true,\"id_empresa\":12,\"sucursal\":1,\"fecha_inicio\":\"2026-07-22\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"usuario_id\":111}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:08:43', '2026-07-22 19:08:43'),
(72, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":\"12\",\"id_rol\":\"1\",\"num_doc\":null,\"usuario\":\"admin\",\"clave\":\"$2y$12$5Fd.8bGpyECeP5.igqefzOGm8EyBFO5VclMgWsDrfH3AeI.y2JbJO\",\"email\":\"admin@admin.com\",\"nombres\":\"Admin\",\"apellidos\":\"Sistema\",\"rubro\":null,\"sucursal\":\"1\",\"telefono\":null,\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"7zXCCBKx3uFespo4UIwCB8eHLMIzfjCPF217VZdACVNGIaCvWYDoOIybvoOO\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"remember_token\":\"nDqRVG12FpthzlgGN2188zy42gglZlnpO54prrP02quxICIL4YTMLo2GjZEF\"}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/panel/logout', 'POST', '2026-07-22 19:08:54', '2026-07-22 19:08:54'),
(73, 111, 'victor12', '7', NULL, 'updated', 'App\\Models\\User', '111', '{\"usuario_id\":111,\"id_empresa\":\"12\",\"id_rol\":\"7\",\"num_doc\":\"76165962\",\"usuario\":\"victor12\",\"clave\":\"$2y$12$8Hxmxh4C\\/foJXUz6FhqBVODz2meYQGi2haI1yggCrexTTl91qVxs6\",\"email\":\"vcanchari@gmail.com\",\"nombres\":\"Victor\",\"apellidos\":\"Canchari\",\"rubro\":null,\"sucursal\":\"1\",\"telefono\":\"92670321\",\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-07-22\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":null,\"updated_at\":null,\"created_at\":null}', '{\"remember_token\":\"sWf247cqU8U7dNnxHBhnpKmcXoIliOLGFVaTGC9iGYsFKyJpqZbPJr09lu8A\"}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:08:59', '2026-07-22 19:08:59'),
(74, 111, 'victor12', '7', 12, 'created', 'App\\Models\\Compra', '165', NULL, '{\"id_proveedor\":181,\"id_tido\":2,\"id_tipo_pago\":2,\"instrumento_tipo\":null,\"instrumento_id\":null,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-22T00:00:00.000000Z\",\"direccion\":\"adadawd\",\"serie\":\"F001\",\"numero\":\"22541\",\"total\":3500,\"id_empresa\":12,\"sucursal\":1,\"moneda\":\"S\",\"recepcionado\":0,\"id_compra\":165}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:20:25', '2026-07-22 19:20:25'),
(75, 111, 'victor12', '7', 12, 'updated', 'App\\Models\\Producto', '414', '{\"id_producto\":414,\"cod_barra\":\"\",\"descripcion\":\"ARROZ EXTRA SACO 10KG (TEST)\",\"precio\":42,\"costo\":35,\"cantidad\":993,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":10.5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-001\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null}', '{\"cantidad\":1043}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:21:02', '2026-07-22 19:21:02'),
(76, 111, 'victor12', '7', 12, 'created', 'App\\Models\\InventarioMovimiento', '35', NULL, '{\"id_empresa\":12,\"almacen\":1,\"id_producto\":414,\"tipo\":\"I\",\"id_motivo\":2,\"cantidad\":50,\"stock_anterior\":993,\"stock_nuevo\":1043,\"costo\":\"35\",\"observacion\":\"Recepci\\u00f3n #5 (compra #165)\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:21:02.411121Z\",\"id_movimiento\":35}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:21:02', '2026-07-22 19:21:02'),
(77, 111, 'victor12', '7', 12, 'updated', 'App\\Models\\Compra', '165', '{\"id_compra\":165,\"id_tido\":\"2\",\"id_tipo_pago\":\"2\",\"instrumento_tipo\":null,\"instrumento_id\":null,\"id_proveedor\":\"181\",\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-22T00:00:00.000000Z\",\"dias_pagos\":null,\"direccion\":\"adadawd\",\"serie\":\"F001\",\"numero\":\"22541\",\"total\":3500,\"recepcionado\":\"0\",\"id_empresa\":\"12\",\"moneda\":\"S\",\"sucursal\":\"1\"}', '{\"recepcionado\":2}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:21:02', '2026-07-22 19:21:02'),
(78, 111, 'victor12', '7', 12, 'created', 'App\\Models\\Traslado', '2', NULL, '{\"id_empresa\":12,\"almacen_origen\":1,\"almacen_destino\":2,\"fecha\":\"2026-07-22T15:24:07.000000Z\",\"observacion\":\"hbsssfj s fjshfjsf jsfsfs\",\"id_usuario\":111,\"estado\":\"1\",\"id_traslado\":2}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:24:07', '2026-07-22 19:24:07'),
(79, 111, 'victor12', '7', 12, 'updated', 'App\\Models\\Producto', '416', '{\"id_producto\":416,\"cod_barra\":\"\",\"descripcion\":\"ACEITE VEGETAL CAJA 12X1L (TEST)\",\"precio\":96,\"costo\":78,\"cantidad\":90,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12.8,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-003\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null}', '{\"cantidad\":40}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:24:07', '2026-07-22 19:24:07'),
(80, 111, 'victor12', '7', 12, 'created', 'App\\Models\\InventarioMovimiento', '36', NULL, '{\"id_empresa\":12,\"almacen\":1,\"id_producto\":416,\"tipo\":\"S\",\"id_motivo\":9,\"cantidad\":50,\"stock_anterior\":90,\"stock_nuevo\":40,\"costo\":78,\"observacion\":\"Traslado TS-00000002 a Almac\\u00e9n 2. hbsssfj s fjshfjsf jsfsfs\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:24:07.370219Z\",\"id_movimiento\":36}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:24:07', '2026-07-22 19:24:07'),
(81, 111, 'victor12', '7', 12, 'created', 'App\\Models\\Producto', '427', NULL, '{\"cod_barra\":\"\",\"descripcion\":\"ACEITE VEGETAL CAJA 12X1L (TEST)\",\"precio\":96,\"costo\":78,\"cantidad\":50,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12.8,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":2,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-003\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null,\"id_producto\":427}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:24:07', '2026-07-22 19:24:07'),
(82, 111, 'victor12', '7', 12, 'created', 'App\\Models\\InventarioMovimiento', '37', NULL, '{\"id_empresa\":12,\"almacen\":2,\"id_producto\":427,\"tipo\":\"I\",\"id_motivo\":5,\"cantidad\":50,\"stock_anterior\":0,\"stock_nuevo\":50,\"costo\":78,\"observacion\":\"Traslado TS-00000002 desde Almac\\u00e9n 1. hbsssfj s fjshfjsf jsfsfs\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:24:07.370219Z\",\"id_movimiento\":37}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:24:07', '2026-07-22 19:24:07'),
(83, 111, 'victor12', '7', 12, 'updated', 'App\\Models\\Producto', '415', '{\"id_producto\":415,\"cod_barra\":\"\",\"descripcion\":\"AZUCAR RUBIA BOLSA 5KG (TEST)\",\"precio\":24.5,\"costo\":19,\"cantidad\":750,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5.2,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-002\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null}', '{\"cantidad\":700}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:24:07', '2026-07-22 19:24:07'),
(84, 111, 'victor12', '7', 12, 'created', 'App\\Models\\InventarioMovimiento', '38', NULL, '{\"id_empresa\":12,\"almacen\":1,\"id_producto\":415,\"tipo\":\"S\",\"id_motivo\":9,\"cantidad\":50,\"stock_anterior\":750,\"stock_nuevo\":700,\"costo\":19,\"observacion\":\"Traslado TS-00000002 a Almac\\u00e9n 2. hbsssfj s fjshfjsf jsfsfs\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:24:07.370219Z\",\"id_movimiento\":38}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:24:07', '2026-07-22 19:24:07'),
(85, 111, 'victor12', '7', 12, 'created', 'App\\Models\\Producto', '428', NULL, '{\"cod_barra\":\"\",\"descripcion\":\"AZUCAR RUBIA BOLSA 5KG (TEST)\",\"precio\":24.5,\"costo\":19,\"cantidad\":50,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5.2,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":2,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-002\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null,\"id_producto\":428}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:24:07', '2026-07-22 19:24:07'),
(86, 111, 'victor12', '7', 12, 'created', 'App\\Models\\InventarioMovimiento', '39', NULL, '{\"id_empresa\":12,\"almacen\":2,\"id_producto\":428,\"tipo\":\"I\",\"id_motivo\":5,\"cantidad\":50,\"stock_anterior\":0,\"stock_nuevo\":50,\"costo\":19,\"observacion\":\"Traslado TS-00000002 desde Almac\\u00e9n 1. hbsssfj s fjshfjsf jsfsfs\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:24:07.370219Z\",\"id_movimiento\":39}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:24:07', '2026-07-22 19:24:07'),
(87, 111, 'victor12', '7', 12, 'created', 'App\\Models\\Producto', '429', NULL, '{\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"cantidad\":0,\"iscbp\":null,\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":1,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":\"4\",\"id_subcategoria\":\"9\",\"id_marca\":\"4\",\"id_submarca\":\"6\",\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":\"1\",\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\",\"id_producto\":429}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:25:15', '2026-07-22 19:25:15'),
(88, 111, 'victor12', '7', 12, 'updated', 'App\\Models\\Producto', '429', '{\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"cantidad\":0,\"iscbp\":null,\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":1,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":\"4\",\"id_subcategoria\":\"9\",\"id_marca\":\"4\",\"id_submarca\":\"6\",\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":\"1\",\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\",\"id_producto\":429}', '{\"cantidad\":5}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:25:15', '2026-07-22 19:25:15'),
(89, 111, 'victor12', '7', 12, 'created', 'App\\Models\\InventarioMovimiento', '40', NULL, '{\"id_empresa\":12,\"almacen\":1,\"id_producto\":429,\"tipo\":\"I\",\"id_motivo\":8,\"cantidad\":5,\"stock_anterior\":0,\"stock_nuevo\":5,\"observacion\":\"b nbes f\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:25:15.991918Z\",\"id_movimiento\":40}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:25:15', '2026-07-22 19:25:15'),
(90, 111, 'victor12', '7', 12, 'created', 'App\\Models\\Producto', '430', NULL, '{\"cod_barra\":\"\",\"descripcion\":\"ACEITE VEGETAL CAJA 12X1L (TEST)\",\"precio\":96,\"costo\":78,\"cantidad\":0,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12.8,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":1,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-003\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null,\"id_producto\":430}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:25:15', '2026-07-22 19:25:15'),
(91, 111, 'victor12', '7', 12, 'updated', 'App\\Models\\Producto', '430', '{\"cod_barra\":\"\",\"descripcion\":\"ACEITE VEGETAL CAJA 12X1L (TEST)\",\"precio\":96,\"costo\":78,\"cantidad\":0,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12.8,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":1,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"DESP-003\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Unidad\",\"presentaciones\":null,\"cnt_presenta\":null,\"id_producto\":430}', '{\"cantidad\":5}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:25:15', '2026-07-22 19:25:15'),
(92, 111, 'victor12', '7', 12, 'created', 'App\\Models\\InventarioMovimiento', '41', NULL, '{\"id_empresa\":12,\"almacen\":1,\"id_producto\":430,\"tipo\":\"I\",\"id_motivo\":8,\"cantidad\":5,\"stock_anterior\":0,\"stock_nuevo\":5,\"observacion\":\"b nbes f\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:25:15.993223Z\",\"id_movimiento\":41}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:25:15', '2026-07-22 19:25:15'),
(93, 107, 'admin', '1', 12, 'created', 'App\\Models\\Venta', '264', NULL, '{\"id_tido\":1,\"id_tipo_pago\":1,\"metodo_pago\":\"EFECTIVO\",\"pago_referencia\":null,\"pago_voucher\":null,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-22T00:00:00.000000Z\",\"direccion\":\"sdcsdcsd\",\"serie\":\"B001\",\"numero\":622,\"id_cliente\":2516,\"total\":157,\"subtotal\":133.05,\"igv\":23.95,\"apli_igv\":\"1\",\"tipo_igv\":\"gravado\",\"estado\":\"1\",\"enviado_sunat\":\"0\",\"id_empresa\":12,\"sucursal\":1,\"id_vendedor\":107,\"observacion\":null,\"id_coti\":null,\"id_venta\":264}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:26:12', '2026-07-22 19:26:12'),
(94, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '411', '{\"id_producto\":411,\"cod_barra\":null,\"descripcion\":\"Az\\u00facar Cartavio Blaco\",\"precio\":157,\"costo\":152,\"cantidad\":20,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"13422\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":1,\"razon_social\":\"MANUEL QUISPE                                               \",\"ruc\":\"10099666922\",\"estado\":\"1\",\"almacen\":\"2\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"13422\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Sacos\",\"presentaciones\":\"4\",\"cnt_presenta\":\"1,2,3,4,5\"}', '{\"cantidad\":19}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:26:12', '2026-07-22 19:26:12'),
(95, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '42', NULL, '{\"id_empresa\":12,\"almacen\":\"2\",\"id_producto\":411,\"tipo\":\"S\",\"id_motivo\":6,\"cantidad\":1,\"stock_anterior\":20,\"stock_nuevo\":19,\"costo\":152,\"observacion\":\"Venta B001-00000622\",\"id_usuario\":107,\"fecha\":\"2026-07-22T15:26:12.635536Z\",\"id_movimiento\":42}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:26:12', '2026-07-22 19:26:12'),
(96, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Venta', '264', '{\"id_tido\":1,\"id_tipo_pago\":1,\"metodo_pago\":\"EFECTIVO\",\"pago_referencia\":null,\"pago_voucher\":null,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-22T00:00:00.000000Z\",\"direccion\":\"sdcsdcsd\",\"serie\":\"B001\",\"numero\":622,\"id_cliente\":2516,\"total\":157,\"subtotal\":133.05,\"igv\":23.95,\"apli_igv\":\"1\",\"tipo_igv\":\"gravado\",\"estado\":\"1\",\"enviado_sunat\":\"0\",\"id_empresa\":12,\"sucursal\":1,\"id_vendedor\":107,\"observacion\":null,\"id_coti\":null,\"id_venta\":264}', '{\"xml_ruta\":\"sunat\\/xml\\/20000000001\\/20000000001-03-B001-622.xml\",\"hash_cpe\":\"8MO6wqrGPIz5JxjvB7vp5UlHjF4=\",\"sunat_estado\":\"pendiente\",\"sunat_mensaje\":\"XML generado, pendiente de env\\u00edo.\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:26:13', '2026-07-22 19:26:13'),
(98, 111, 'victor12', '7', 12, 'created', 'App\\Models\\Prestamo', '4', NULL, '{\"id_empresa\":12,\"tipo\":\"P\",\"tercero\":\"ard\\u00e1is Mario\",\"almacen\":1,\"estado\":\"P\",\"observacion\":\"adawdawd\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:26:54.332340Z\",\"id_producto\":419,\"cantidad\":5,\"id_prestamo\":4}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:26:54', '2026-07-22 19:26:54'),
(99, 111, 'victor12', '7', 12, 'updated', 'App\\Models\\Producto', '419', '{\"id_producto\":419,\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"cantidad\":10,\"iscbp\":null,\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":null,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":\"4\",\"id_subcategoria\":\"9\",\"id_marca\":\"4\",\"id_submarca\":\"6\",\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":\"1\",\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\"}', '{\"cantidad\":5}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:26:54', '2026-07-22 19:26:54'),
(100, 111, 'victor12', '7', 12, 'created', 'App\\Models\\InventarioMovimiento', '43', NULL, '{\"id_empresa\":12,\"almacen\":\"1\",\"id_producto\":419,\"tipo\":\"S\",\"id_motivo\":11,\"cantidad\":5,\"stock_anterior\":10,\"stock_nuevo\":5,\"costo\":80,\"observacion\":\"Pr\\u00e9stamo a ard\\u00e1is Mario\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:26:54.334727Z\",\"id_movimiento\":43}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:26:54', '2026-07-22 19:26:54'),
(101, 111, 'victor12', '7', 12, 'updated', 'App\\Models\\Producto', '429', '{\"id_producto\":429,\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"cantidad\":5,\"iscbp\":null,\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":\"4\",\"id_subcategoria\":\"9\",\"id_marca\":\"4\",\"id_submarca\":\"6\",\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":\"1\",\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\"}', '{\"cantidad\":6}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:27:25', '2026-07-22 19:27:25'),
(102, 111, 'victor12', '7', 12, 'created', 'App\\Models\\InventarioMovimiento', '44', NULL, '{\"id_empresa\":12,\"almacen\":\"1\",\"id_producto\":429,\"tipo\":\"I\",\"id_motivo\":12,\"cantidad\":1,\"stock_anterior\":5,\"stock_nuevo\":6,\"costo\":80,\"observacion\":\"Devoluci\\u00f3n de ard\\u00e1is Mario\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:27:25.172267Z\",\"id_movimiento\":44}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:27:25', '2026-07-22 19:27:25'),
(103, 111, 'victor12', '7', 12, 'updated', 'App\\Models\\Prestamo', '4', '{\"id_prestamo\":4,\"id_empresa\":\"12\",\"tipo\":\"P\",\"tercero\":\"ard\\u00e1is Mario\",\"id_producto\":\"419\",\"almacen\":\"1\",\"cantidad\":\"5\",\"estado\":\"P\",\"observacion\":\"adawdawd\",\"id_usuario\":\"111\",\"fecha\":\"2026-07-22 15:26:54\",\"fecha_devolucion\":null}', '{\"estado\":\"X\"}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:27:25', '2026-07-22 19:27:25'),
(104, 111, 'victor12', '7', 12, 'updated', 'App\\Models\\Producto', '429', '{\"id_producto\":429,\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"cantidad\":6,\"iscbp\":null,\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":\"4\",\"id_subcategoria\":\"9\",\"id_marca\":\"4\",\"id_submarca\":\"6\",\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":\"1\",\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\"}', '{\"cantidad\":5}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:27:51', '2026-07-22 19:27:51'),
(105, 111, 'victor12', '7', 12, 'created', 'App\\Models\\InventarioMovimiento', '45', NULL, '{\"id_empresa\":12,\"almacen\":\"1\",\"id_producto\":429,\"tipo\":\"S\",\"id_motivo\":11,\"cantidad\":1,\"stock_anterior\":6,\"stock_nuevo\":5,\"costo\":80,\"observacion\":\"Anulaci\\u00f3n devoluci\\u00f3n #5 (ard\\u00e1is Mario)\",\"id_usuario\":111,\"fecha\":\"2026-07-22T15:27:51.296998Z\",\"id_movimiento\":45}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 19:27:51', '2026-07-22 19:27:51'),
(106, 107, 'admin', '1', 12, 'created', 'App\\Models\\Cotizacion', '51497', NULL, '{\"numero\":2978,\"id_tido\":6,\"id_tipo_pago\":1,\"fecha\":\"2026-07-22T00:00:00.000000Z\",\"direccion\":\"sdcsdcsd\",\"id_cliente\":2516,\"total\":45,\"estado\":\"1\",\"id_empresa\":12,\"sucursal\":1,\"usar_precio\":1,\"moneda\":1,\"id_usuario\":107,\"observacion\":null,\"fecha_registro\":\"2026-07-22T16:03:29.956261Z\",\"cotizacion_id\":51497}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:03:29', '2026-07-22 20:03:29'),
(107, 107, 'admin', '1', 12, 'created', 'App\\Models\\Venta', '265', NULL, '{\"id_tido\":6,\"id_tipo_pago\":2,\"metodo_pago\":null,\"pago_referencia\":null,\"pago_voucher\":null,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-24T00:00:00.000000Z\",\"direccion\":\"sdcsdcsd\",\"serie\":\"NV01\",\"numero\":2979,\"id_cliente\":2516,\"total\":45,\"subtotal\":38.14,\"igv\":6.86,\"apli_igv\":\"1\",\"tipo_igv\":\"gravado\",\"estado\":\"1\",\"enviado_sunat\":\"0\",\"id_empresa\":12,\"sucursal\":1,\"id_vendedor\":107,\"observacion\":\"Convertido de cotizaci\\u00f3n N\\u00b0 2978\",\"id_coti\":51497,\"id_venta\":265}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:05:59', '2026-07-22 20:05:59'),
(108, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '409', '{\"id_producto\":409,\"cod_barra\":null,\"descripcion\":\"JAB\\u00d3N DE ROPA BELTRA*175 GMS\",\"precio\":45,\"costo\":43,\"cantidad\":91,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"Jabel0001\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":1,\"razon_social\":\"CORPORACION BELTRAN ESPINOZA E.I.R.L. - COBELES E.I.R.L.\",\"ruc\":\"20602096808\",\"estado\":\"1\",\"almacen\":\"2\",\"precio2\":46,\"precio3\":47,\"precio4\":44,\"precio_unidad\":44,\"codigo\":\"Jabel0001\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Cajas\",\"presentaciones\":\"2\",\"cnt_presenta\":\"1,2,3\"}', '{\"cantidad\":90}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:05:59', '2026-07-22 20:05:59'),
(109, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '46', NULL, '{\"id_empresa\":12,\"almacen\":\"2\",\"id_producto\":409,\"tipo\":\"S\",\"id_motivo\":6,\"cantidad\":1,\"stock_anterior\":91,\"stock_nuevo\":90,\"costo\":43,\"observacion\":\"Venta NV01-00002979\",\"id_usuario\":107,\"fecha\":\"2026-07-22T16:05:59.231927Z\",\"id_movimiento\":46}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:05:59', '2026-07-22 20:05:59'),
(110, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Venta', '264', '{\"id_venta\":264,\"id_tido\":\"1\",\"id_tipo_pago\":\"1\",\"metodo_pago\":\"EFECTIVO\",\"pago_referencia\":null,\"pago_voucher\":null,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-22T00:00:00.000000Z\",\"dias_pagos\":null,\"direccion\":\"sdcsdcsd\",\"serie\":\"B001\",\"numero\":\"622\",\"id_cliente\":\"2516\",\"total\":157,\"subtotal\":133.05,\"estado\":\"1\",\"enviado_sunat\":\"0\",\"sunat_estado\":\"pendiente\",\"sunat_mensaje\":\"XML generado, pendiente de env\\u00edo.\",\"hash_cpe\":\"8MO6wqrGPIz5JxjvB7vp5UlHjF4=\",\"xml_ruta\":\"sunat\\/xml\\/20000000001\\/20000000001-03-B001-622.xml\",\"cdr_ruta\":null,\"id_empresa\":\"12\",\"sucursal\":\"1\",\"apli_igv\":\"1\",\"tipo_igv\":\"gravado\",\"observacion\":null,\"igv\":23.95,\"medoto_pago_id\":null,\"pagado\":null,\"is_segun_pago\":null,\"medoto_pago2_id\":null,\"pagado2\":null,\"moneda\":\"1\",\"cm_tc\":null,\"id_coti\":null,\"id_vendedor\":\"107\",\"despacho_info\":null}', '{\"enviado_sunat\":\"1\",\"sunat_estado\":\"aceptado\",\"sunat_mensaje\":\"Aceptado por SUNAT.\",\"cdr_ruta\":\"sunat\\/cdr\\/20000000001\\/R-20000000001-03-B001-622.zip\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:08:59', '2026-07-22 20:08:59'),
(111, 107, 'admin', '1', 12, 'created', 'App\\Models\\NotaElectronica', '3', NULL, '{\"id_venta\":264,\"tipo\":\"credito\",\"cod_motivo\":\"01\",\"motivo\":1,\"motivo_desc\":\"Anulaci\\u00f3n de la operaci\\u00f3n\",\"id_empresa\":12,\"sucursal\":1,\"serie\":\"BC01\",\"numero\":2,\"total\":157,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"estado\":\"1\",\"enviado_sunat\":\"0\",\"nota_id\":3}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:15:50', '2026-07-22 20:15:50'),
(112, 107, 'admin', '1', 12, 'updated', 'App\\Models\\NotaElectronica', '3', '{\"id_venta\":264,\"tipo\":\"credito\",\"cod_motivo\":\"01\",\"motivo\":1,\"motivo_desc\":\"Anulaci\\u00f3n de la operaci\\u00f3n\",\"id_empresa\":12,\"sucursal\":1,\"serie\":\"BC01\",\"numero\":2,\"total\":157,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"estado\":\"1\",\"enviado_sunat\":\"0\",\"nota_id\":3}', '{\"nombre_xml\":\"20000000001-07-BC01-2\",\"hash\":\"e6so6Os5ae0Jg9nIq\\/t2L\\/TorGY=\",\"xml_ruta\":\"sunat\\/xml\\/20000000001\\/20000000001-07-BC01-2.xml\",\"sunat_estado\":\"pendiente\",\"sunat_mensaje\":\"XML generado, pendiente de env\\u00edo.\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:15:51', '2026-07-22 20:15:51'),
(113, 107, 'admin', '1', 12, 'updated', 'App\\Models\\NotaElectronica', '3', '{\"nota_id\":3,\"id_venta\":\"264\",\"tipo\":\"credito\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"tido\":null,\"fecha\":null,\"serie\":\"BC01\",\"numero\":\"2\",\"total\":\"157.00\",\"motivo\":\"1\",\"motivo_desc\":\"Anulaci\\u00f3n de la operaci\\u00f3n\",\"cod_motivo\":\"01\",\"monto\":null,\"productos\":null,\"estado_sunat\":\"0\",\"estado\":\"1\",\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"hash\":\"e6so6Os5ae0Jg9nIq\\/t2L\\/TorGY=\",\"nombre_xml\":\"20000000001-07-BC01-2\",\"enviado_sunat\":\"0\",\"sunat_estado\":\"pendiente\",\"sunat_mensaje\":\"XML generado, pendiente de env\\u00edo.\",\"xml_ruta\":\"sunat\\/xml\\/20000000001\\/20000000001-07-BC01-2.xml\",\"cdr_ruta\":null}', '{\"enviado_sunat\":\"1\",\"sunat_estado\":\"aceptado\",\"sunat_mensaje\":\"Aceptada por SUNAT.\",\"cdr_ruta\":\"sunat\\/cdr\\/20000000001\\/R-20000000001-07-BC01-2.zip\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:16:06', '2026-07-22 20:16:06'),
(114, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '411', '{\"id_producto\":411,\"cod_barra\":null,\"descripcion\":\"Az\\u00facar Cartavio Blaco\",\"precio\":157,\"costo\":152,\"cantidad\":19,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"13422\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":1,\"razon_social\":\"MANUEL QUISPE                                               \",\"ruc\":\"10099666922\",\"estado\":\"1\",\"almacen\":\"2\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"13422\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Sacos\",\"presentaciones\":\"4\",\"cnt_presenta\":\"1,2,3,4,5\"}', '{\"cantidad\":20}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:16:06', '2026-07-22 20:16:06'),
(115, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '47', NULL, '{\"id_empresa\":12,\"almacen\":\"2\",\"id_producto\":411,\"tipo\":\"I\",\"id_motivo\":6,\"cantidad\":1,\"stock_anterior\":19,\"stock_nuevo\":20,\"costo\":152,\"observacion\":\"Anulaci\\u00f3n por nota de cr\\u00e9dito BC01-00000002\",\"id_usuario\":107,\"fecha\":\"2026-07-22T16:16:06.436408Z\",\"id_movimiento\":47}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:16:06', '2026-07-22 20:16:06'),
(116, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Venta', '264', '{\"id_venta\":264,\"id_tido\":\"1\",\"id_tipo_pago\":\"1\",\"metodo_pago\":\"EFECTIVO\",\"pago_referencia\":null,\"pago_voucher\":null,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-22T00:00:00.000000Z\",\"dias_pagos\":null,\"direccion\":\"sdcsdcsd\",\"serie\":\"B001\",\"numero\":\"622\",\"id_cliente\":\"2516\",\"total\":157,\"subtotal\":133.05,\"estado\":\"1\",\"enviado_sunat\":\"1\",\"sunat_estado\":\"aceptado\",\"sunat_mensaje\":\"Aceptado por SUNAT.\",\"hash_cpe\":\"8MO6wqrGPIz5JxjvB7vp5UlHjF4=\",\"xml_ruta\":\"sunat\\/xml\\/20000000001\\/20000000001-03-B001-622.xml\",\"cdr_ruta\":\"sunat\\/cdr\\/20000000001\\/R-20000000001-03-B001-622.zip\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"apli_igv\":\"1\",\"tipo_igv\":\"gravado\",\"observacion\":null,\"igv\":23.95,\"medoto_pago_id\":null,\"pagado\":null,\"is_segun_pago\":null,\"medoto_pago2_id\":null,\"pagado2\":null,\"moneda\":\"1\",\"cm_tc\":null,\"id_coti\":null,\"id_vendedor\":\"107\"}', '{\"estado\":\"0\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:16:06', '2026-07-22 20:16:06'),
(117, 107, 'admin', '1', 12, 'created', 'App\\Models\\GuiaRemision', '426', NULL, '{\"id_venta\":265,\"motivo_traslado\":\"01\",\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_traslado\":\"2026-07-22T00:00:00.000000Z\",\"dir_llegada\":\"sdcsdcsd\",\"ubigeo\":\"030302\",\"dir_partida\":\"SAN MARTIN DE PORRES\",\"ubigeo_partida\":\"150135\",\"tipo_transporte\":1,\"ruc_transporte\":null,\"razon_transporte\":null,\"transportista_nro_mtc\":null,\"vehiculo\":\"efrvdsf34534523\",\"conductor_documento\":\"77325200\",\"conductor_nombres\":\"YEMIMA ADALI\",\"conductor_apellidos\":\"VIERA CIENFUEGOS\",\"conductor_licencia\":\"cfdssdfvdssdc\",\"peso\":21,\"und_peso_total\":\"KGM\",\"nro_bultos\":1,\"serie\":\"T001\",\"numero\":1034,\"estado\":\"1\",\"enviado_sunat\":\"0\",\"estado_gre\":\"pendiente\",\"id_empresa\":12,\"sucursal\":1,\"id_guia_remision\":426}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:17:33', '2026-07-22 20:17:33');
INSERT INTO `audits` (`id`, `user_id`, `user_name`, `user_rol`, `empresa_id`, `event`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `method`, `created_at`, `updated_at`) VALUES
(118, 107, 'admin', '1', 12, 'updated', 'App\\Models\\GuiaRemision', '426', '{\"id_venta\":265,\"motivo_traslado\":\"01\",\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_traslado\":\"2026-07-22T00:00:00.000000Z\",\"dir_llegada\":\"sdcsdcsd\",\"ubigeo\":\"030302\",\"dir_partida\":\"SAN MARTIN DE PORRES\",\"ubigeo_partida\":\"150135\",\"tipo_transporte\":1,\"ruc_transporte\":null,\"razon_transporte\":null,\"transportista_nro_mtc\":null,\"vehiculo\":\"efrvdsf34534523\",\"conductor_documento\":\"77325200\",\"conductor_nombres\":\"YEMIMA ADALI\",\"conductor_apellidos\":\"VIERA CIENFUEGOS\",\"conductor_licencia\":\"cfdssdfvdssdc\",\"peso\":21,\"und_peso_total\":\"KGM\",\"nro_bultos\":1,\"serie\":\"T001\",\"numero\":1034,\"estado\":\"1\",\"enviado_sunat\":\"0\",\"estado_gre\":\"pendiente\",\"id_empresa\":12,\"sucursal\":1,\"id_guia_remision\":426}', '{\"nombre_xml\":\"20000000001-09-T001-1034\",\"hash\":\"Xi9AVSeB4JFTnoP5aM\\/U89wgYV8=\",\"xml_ruta\":\"sunat\\/xml\\/20000000001\\/20000000001-09-T001-1034.xml\",\"mensaje_sunat\":\"XML generado, pendiente de env\\u00edo.\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:17:33', '2026-07-22 20:17:33'),
(119, 107, 'admin', '1', 12, 'created', 'App\\Models\\Venta', '266', NULL, '{\"id_tido\":6,\"id_tipo_pago\":1,\"metodo_pago\":\"EFECTIVO\",\"pago_referencia\":null,\"pago_voucher\":null,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-22T00:00:00.000000Z\",\"direccion\":\"sdcsdcsd\",\"serie\":\"NV01\",\"numero\":2980,\"id_cliente\":2516,\"total\":45,\"subtotal\":38.14,\"igv\":6.86,\"apli_igv\":\"1\",\"tipo_igv\":\"gravado\",\"estado\":\"1\",\"enviado_sunat\":\"0\",\"id_empresa\":12,\"sucursal\":1,\"id_vendedor\":107,\"observacion\":\"Convertido de cotizaci\\u00f3n N\\u00b0 2977\",\"id_coti\":51496,\"id_venta\":266}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:19:16', '2026-07-22 20:19:16'),
(120, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '409', '{\"id_producto\":409,\"cod_barra\":null,\"descripcion\":\"JAB\\u00d3N DE ROPA BELTRA*175 GMS\",\"precio\":45,\"costo\":43,\"cantidad\":90,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"Jabel0001\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":1,\"razon_social\":\"CORPORACION BELTRAN ESPINOZA E.I.R.L. - COBELES E.I.R.L.\",\"ruc\":\"20602096808\",\"estado\":\"1\",\"almacen\":\"2\",\"precio2\":46,\"precio3\":47,\"precio4\":44,\"precio_unidad\":44,\"codigo\":\"Jabel0001\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Cajas\",\"presentaciones\":\"2\",\"cnt_presenta\":\"1,2,3\"}', '{\"cantidad\":89}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:19:16', '2026-07-22 20:19:16'),
(121, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '48', NULL, '{\"id_empresa\":12,\"almacen\":\"2\",\"id_producto\":409,\"tipo\":\"S\",\"id_motivo\":6,\"cantidad\":1,\"stock_anterior\":90,\"stock_nuevo\":89,\"costo\":43,\"observacion\":\"Venta NV01-00002980\",\"id_usuario\":107,\"fecha\":\"2026-07-22T16:19:16.559709Z\",\"id_movimiento\":48}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:19:16', '2026-07-22 20:19:16'),
(122, 107, 'admin', '1', 12, 'created', 'App\\Models\\Venta', '267', NULL, '{\"id_tido\":1,\"id_tipo_pago\":1,\"metodo_pago\":\"EFECTIVO\",\"pago_referencia\":null,\"pago_voucher\":null,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-22T00:00:00.000000Z\",\"direccion\":\"sdcsdcsd\",\"serie\":\"B001\",\"numero\":623,\"id_cliente\":2516,\"total\":45,\"subtotal\":38.14,\"igv\":6.86,\"apli_igv\":\"1\",\"tipo_igv\":\"gravado\",\"estado\":\"1\",\"enviado_sunat\":\"0\",\"id_empresa\":12,\"sucursal\":1,\"id_vendedor\":107,\"observacion\":null,\"id_coti\":null,\"id_venta\":267}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:20:01', '2026-07-22 20:20:01'),
(123, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '409', '{\"id_producto\":409,\"cod_barra\":null,\"descripcion\":\"JAB\\u00d3N DE ROPA BELTRA*175 GMS\",\"precio\":45,\"costo\":43,\"cantidad\":89,\"iscbp\":\"0\",\"id_empresa\":\"12\",\"sucursal\":\"1\",\"ultima_salida\":\"1000-01-01T00:00:00.000000Z\",\"codsunat\":\"Jabel0001\",\"usar_barra\":\"0\",\"precio_mayor\":1,\"precio_menor\":1,\"peso_bruto\":1,\"razon_social\":\"CORPORACION BELTRAN ESPINOZA E.I.R.L. - COBELES E.I.R.L.\",\"ruc\":\"20602096808\",\"estado\":\"1\",\"almacen\":\"2\",\"precio2\":46,\"precio3\":47,\"precio4\":44,\"precio_unidad\":44,\"codigo\":\"Jabel0001\",\"id_categoria\":null,\"id_subcategoria\":null,\"id_marca\":null,\"id_submarca\":null,\"imagen\":null,\"activo\":\"1\",\"medida\":\"Cajas\",\"presentaciones\":\"2\",\"cnt_presenta\":\"1,2,3\"}', '{\"cantidad\":88}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:20:01', '2026-07-22 20:20:01'),
(124, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '49', NULL, '{\"id_empresa\":12,\"almacen\":\"2\",\"id_producto\":409,\"tipo\":\"S\",\"id_motivo\":6,\"cantidad\":1,\"stock_anterior\":89,\"stock_nuevo\":88,\"costo\":43,\"observacion\":\"Venta B001-00000623\",\"id_usuario\":107,\"fecha\":\"2026-07-22T16:20:01.843947Z\",\"id_movimiento\":49}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:20:01', '2026-07-22 20:20:01'),
(125, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Venta', '267', '{\"id_tido\":1,\"id_tipo_pago\":1,\"metodo_pago\":\"EFECTIVO\",\"pago_referencia\":null,\"pago_voucher\":null,\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-22T00:00:00.000000Z\",\"direccion\":\"sdcsdcsd\",\"serie\":\"B001\",\"numero\":623,\"id_cliente\":2516,\"total\":45,\"subtotal\":38.14,\"igv\":6.86,\"apli_igv\":\"1\",\"tipo_igv\":\"gravado\",\"estado\":\"1\",\"enviado_sunat\":\"0\",\"id_empresa\":12,\"sucursal\":1,\"id_vendedor\":107,\"observacion\":null,\"id_coti\":null,\"id_venta\":267}', '{\"xml_ruta\":\"sunat\\/xml\\/20000000001\\/20000000001-03-B001-623.xml\",\"hash_cpe\":\"CjqTBSKJe0SxBiPYc51JpEke+jg=\",\"sunat_estado\":\"pendiente\",\"sunat_mensaje\":\"XML generado, pendiente de env\\u00edo.\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:20:02', '2026-07-22 20:20:02'),
(126, 107, 'admin', '1', 12, 'created', 'App\\Models\\GuiaRemision', '427', NULL, '{\"id_venta\":267,\"motivo_traslado\":\"01\",\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_traslado\":\"2026-07-22T00:00:00.000000Z\",\"dir_llegada\":\"sdcsdcsd\",\"ubigeo\":\"010304\",\"dir_partida\":\"SAN MARTIN DE PORRES\",\"ubigeo_partida\":\"150135\",\"tipo_transporte\":1,\"ruc_transporte\":null,\"razon_transporte\":null,\"transportista_nro_mtc\":null,\"vehiculo\":\"efrvdsf345345z\",\"conductor_documento\":\"77425200\",\"conductor_nombres\":\"EMER RODRIGO\",\"conductor_apellidos\":\"YARLEQUE ZAPATA\",\"conductor_licencia\":\"savcdasvdfsvdsf\",\"peso\":1,\"und_peso_total\":\"KGM\",\"nro_bultos\":1,\"serie\":\"T001\",\"numero\":1035,\"estado\":\"1\",\"enviado_sunat\":\"0\",\"estado_gre\":\"pendiente\",\"id_empresa\":12,\"sucursal\":1,\"id_guia_remision\":427}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:22:29', '2026-07-22 20:22:29'),
(127, 107, 'admin', '1', 12, 'updated', 'App\\Models\\GuiaRemision', '427', '{\"id_venta\":267,\"motivo_traslado\":\"01\",\"fecha_emision\":\"2026-07-22T00:00:00.000000Z\",\"fecha_traslado\":\"2026-07-22T00:00:00.000000Z\",\"dir_llegada\":\"sdcsdcsd\",\"ubigeo\":\"010304\",\"dir_partida\":\"SAN MARTIN DE PORRES\",\"ubigeo_partida\":\"150135\",\"tipo_transporte\":1,\"ruc_transporte\":null,\"razon_transporte\":null,\"transportista_nro_mtc\":null,\"vehiculo\":\"efrvdsf345345z\",\"conductor_documento\":\"77425200\",\"conductor_nombres\":\"EMER RODRIGO\",\"conductor_apellidos\":\"YARLEQUE ZAPATA\",\"conductor_licencia\":\"savcdasvdfsvdsf\",\"peso\":1,\"und_peso_total\":\"KGM\",\"nro_bultos\":1,\"serie\":\"T001\",\"numero\":1035,\"estado\":\"1\",\"enviado_sunat\":\"0\",\"estado_gre\":\"pendiente\",\"id_empresa\":12,\"sucursal\":1,\"id_guia_remision\":427}', '{\"nombre_xml\":\"20000000001-09-T001-1035\",\"hash\":\"aS6Apv4RF\\/dly1vuVg4AE6A+KKo=\",\"xml_ruta\":\"sunat\\/xml\\/20000000001\\/20000000001-09-T001-1035.xml\",\"mensaje_sunat\":\"XML generado, pendiente de env\\u00edo.\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-22 20:22:30', '2026-07-22 20:22:30'),
(128, 111, 'victor12', '7', NULL, 'updated', 'App\\Models\\User', '111', '{\"usuario_id\":111,\"id_empresa\":12,\"id_rol\":7,\"num_doc\":\"76165962\",\"usuario\":\"victor12\",\"clave\":\"$2y$12$8Hxmxh4C\\/foJXUz6FhqBVODz2meYQGi2haI1yggCrexTTl91qVxs6\",\"email\":\"vcanchari@gmail.com\",\"nombres\":\"Victor\",\"apellidos\":\"Canchari\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"92670321\",\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-07-22\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"sWf247cqU8U7dNnxHBhnpKmcXoIliOLGFVaTGC9iGYsFKyJpqZbPJr09lu8A\",\"updated_at\":null,\"created_at\":null}', '{\"remember_token\":\"gJKafJcHUuQaf0ueuly5YAo68wFDibmBWIi9lwBHvgQepAZemf0GGhV7W2aS\"}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/panel/logout', 'POST', '2026-07-24 19:27:49', '2026-07-24 19:27:49'),
(129, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":null,\"usuario\":\"admin\",\"clave\":\"$2y$12$5Fd.8bGpyECeP5.igqefzOGm8EyBFO5VclMgWsDrfH3AeI.y2JbJO\",\"email\":\"admin@admin.com\",\"nombres\":\"Admin\",\"apellidos\":\"Sistema\",\"rubro\":null,\"sucursal\":1,\"telefono\":null,\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"nDqRVG12FpthzlgGN2188zy42gglZlnpO54prrP02quxICIL4YTMLo2GjZEF\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"remember_token\":\"8Yav2RII62lf1ekjEGnmHTfz3GNEcBRft7pwdzOnA7JV7fLK32QbbKCkx19i\"}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/panel/logout', 'POST', '2026-07-25 07:52:11', '2026-07-25 07:52:11'),
(130, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '429', '{\"id_producto\":429,\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"cantidad\":5,\"iscbp\":null,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":1,\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\"}', '{\"cantidad\":6}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-27 23:40:32', '2026-07-27 23:40:32'),
(131, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '50', NULL, '{\"id_empresa\":12,\"almacen\":\"1\",\"id_producto\":429,\"tipo\":\"I\",\"id_motivo\":12,\"cantidad\":1,\"stock_anterior\":5,\"stock_nuevo\":6,\"costo\":80,\"observacion\":\"Devoluci\\u00f3n de ard\\u00e1is Mario\",\"id_usuario\":107,\"fecha\":\"2026-07-27T19:40:32.262623Z\",\"id_movimiento\":50}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-27 23:40:32', '2026-07-27 23:40:32'),
(132, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Prestamo', '4', '{\"id_prestamo\":4,\"id_empresa\":12,\"tipo\":\"P\",\"tercero\":\"ard\\u00e1is Mario\",\"id_producto\":419,\"almacen\":\"1\",\"cantidad\":5,\"estado\":\"P\",\"observacion\":\"adawdawd\",\"id_usuario\":111,\"fecha\":\"2026-07-22 15:26:54\",\"fecha_devolucion\":null}', '{\"estado\":\"X\"}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-27 23:40:32', '2026-07-27 23:40:32'),
(133, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '429', '{\"id_producto\":429,\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":80,\"cantidad\":6,\"iscbp\":null,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"1\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":1,\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\"}', '{\"cantidad\":7}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-27 23:42:35', '2026-07-27 23:42:35'),
(134, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '51', NULL, '{\"id_empresa\":12,\"almacen\":1,\"id_producto\":429,\"tipo\":\"I\",\"id_motivo\":7,\"cantidad\":1,\"stock_anterior\":6,\"stock_nuevo\":7,\"observacion\":\"adadadadaw\",\"id_usuario\":107,\"fecha\":\"2026-07-27T19:42:35.880894Z\",\"id_movimiento\":51}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-27 23:42:35', '2026-07-27 23:42:35'),
(135, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '419', '{\"id_producto\":419,\"codigo\":\"31564165\",\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\",\"peso_bruto\":12,\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"precio\":120,\"costo\":80,\"cantidad\":5,\"stock_minimo\":5,\"stock_maximo\":null,\"activo\":1,\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"id_empresa\":12}', '{\"stock_minimo\":1,\"stock_maximo\":10,\"activo\":true,\"imagen\":null}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-28 03:08:40', '2026-07-28 03:08:40'),
(136, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '419', '{\"id_producto\":419,\"codigo\":\"31564165\",\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\",\"peso_bruto\":12,\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"precio\":120,\"costo\":80,\"cantidad\":5,\"stock_minimo\":1,\"stock_maximo\":10,\"activo\":1,\"imagen\":null,\"id_empresa\":12}', '{\"stock_minimo\":8,\"activo\":true}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-28 03:09:18', '2026-07-28 03:09:18'),
(137, 107, 'admin', '1', 12, 'created', 'App\\Models\\Compra', '166', NULL, '{\"id_proveedor\":181,\"id_tido\":2,\"id_tipo_pago\":1,\"instrumento_tipo\":\"EFECTIVO\",\"instrumento_id\":null,\"fecha_emision\":\"2026-07-27T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-27T00:00:00.000000Z\",\"direccion\":\"Convertido de cotizaci\\u00f3n N\\u00b0 2953\",\"serie\":\"F001\",\"numero\":\"33651\",\"total\":100,\"id_empresa\":12,\"sucursal\":1,\"moneda\":\"S\",\"recepcionado\":0,\"id_compra\":166}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-28 03:10:28', '2026-07-28 03:10:28'),
(138, 107, 'admin', '1', 12, 'created', 'App\\Models\\Producto', '431', NULL, '{\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":10,\"cantidad\":10,\"stock_minimo\":5,\"stock_maximo\":null,\"iscbp\":null,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"aedawdawd\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":1,\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\",\"id_producto\":431}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-28 03:10:38', '2026-07-28 03:10:38'),
(139, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '52', NULL, '{\"id_empresa\":12,\"almacen\":\"aedawdawd\",\"id_producto\":431,\"tipo\":\"I\",\"id_motivo\":2,\"cantidad\":10,\"stock_anterior\":0,\"stock_nuevo\":10,\"costo\":\"10\",\"observacion\":\"Recepci\\u00f3n #6 (compra #166)\",\"id_usuario\":107,\"fecha\":\"2026-07-27T23:10:38.507206Z\",\"id_movimiento\":52}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-28 03:10:38', '2026-07-28 03:10:38'),
(140, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Compra', '166', '{\"id_compra\":166,\"id_tido\":2,\"id_tipo_pago\":1,\"instrumento_tipo\":\"EFECTIVO\",\"instrumento_id\":null,\"id_proveedor\":181,\"fecha_emision\":\"2026-07-27T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-07-27T00:00:00.000000Z\",\"dias_pagos\":null,\"direccion\":\"Convertido de cotizaci\\u00f3n N\\u00b0 2953\",\"serie\":\"F001\",\"numero\":\"33651\",\"total\":100,\"recepcionado\":0,\"id_empresa\":12,\"moneda\":\"S\",\"sucursal\":1}', '{\"recepcionado\":1}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-28 03:10:38', '2026-07-28 03:10:38'),
(141, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Producto', '431', '{\"id_producto\":431,\"cod_barra\":\"23135485\",\"descripcion\":\"ACEITE RICOSOL 1 L                           \",\"precio\":120,\"costo\":10,\"cantidad\":10,\"stock_minimo\":5,\"stock_maximo\":null,\"iscbp\":null,\"id_empresa\":12,\"sucursal\":1,\"ultima_salida\":\"2026-07-10T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":12,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"aedawdawd\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"31564165\",\"id_categoria\":4,\"id_subcategoria\":9,\"id_marca\":4,\"id_submarca\":6,\"imagen\":\"productos\\/01KX67P0ZFQ58A3J3JNP30PANM.jpg\",\"activo\":1,\"medida\":\"Litro\",\"presentaciones\":\"Caja\",\"cnt_presenta\":\"12\"}', '{\"cantidad\":5}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-28 03:11:42', '2026-07-28 03:11:42'),
(142, 107, 'admin', '1', 12, 'created', 'App\\Models\\InventarioMovimiento', '53', NULL, '{\"id_empresa\":12,\"almacen\":\"aedawdawd\",\"id_producto\":431,\"tipo\":\"S\",\"id_motivo\":8,\"cantidad\":5,\"stock_anterior\":10,\"stock_nuevo\":5,\"costo\":null,\"observacion\":\"ad ada adadw\",\"id_usuario\":107,\"fecha\":\"2026-07-27T23:11:42.662121Z\",\"id_movimiento\":53}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-28 03:11:42', '2026-07-28 03:11:42'),
(143, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Caja', '3', '{\"id\":3,\"id_empresa\":12,\"sucursal\":1,\"nombre\":\"CAJA VICTOR (PRUEBA)\",\"id_usuario_responsable\":108,\"id_caja_padre\":1,\"saldo_actual\":1660,\"moneda\":\"PEN\",\"estado\":\"ACTIVA\"}', '{\"id_usuario_responsable\":111}', '38.252.222.52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-07-28 03:45:03', '2026-07-28 03:45:03'),
(144, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":null,\"usuario\":\"admin\",\"clave\":\"$2y$12$5Fd.8bGpyECeP5.igqefzOGm8EyBFO5VclMgWsDrfH3AeI.y2JbJO\",\"email\":\"admin@admin.com\",\"nombres\":\"Admin\",\"apellidos\":\"Sistema\",\"rubro\":null,\"sucursal\":1,\"telefono\":null,\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"8Yav2RII62lf1ekjEGnmHTfz3GNEcBRft7pwdzOnA7JV7fLK32QbbKCkx19i\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"remember_token\":\"duJnKb4rJHGJLSVxohQJs2Ru15GtAAyjRXnD8vzy24Fg1Vdt5rK4zWnAzIFb\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/panel/logout', 'POST', '2026-08-19 07:35:23', '2026-08-19 07:35:23'),
(145, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":null,\"usuario\":\"admin\",\"clave\":\"$2y$12$5Fd.8bGpyECeP5.igqefzOGm8EyBFO5VclMgWsDrfH3AeI.y2JbJO\",\"email\":\"admin@admin.com\",\"nombres\":\"Admin\",\"apellidos\":\"Sistema\",\"rubro\":null,\"sucursal\":1,\"telefono\":null,\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"duJnKb4rJHGJLSVxohQJs2Ru15GtAAyjRXnD8vzy24Fg1Vdt5rK4zWnAzIFb\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"remember_token\":\"25cQSEgWDq4Bz1fiGVE0o7ClzbvV1Cr11DBtbFIh26BhqTn4GgRLoEHR8csv\"}', '38.255.107.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/panel/logout', 'POST', '2026-09-01 00:07:09', '2026-09-01 00:07:09'),
(146, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":null,\"usuario\":\"admin\",\"clave\":\"$2y$12$5Fd.8bGpyECeP5.igqefzOGm8EyBFO5VclMgWsDrfH3AeI.y2JbJO\",\"email\":\"admin@admin.com\",\"nombres\":\"Admin\",\"apellidos\":\"Sistema\",\"rubro\":null,\"sucursal\":1,\"telefono\":null,\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"25cQSEgWDq4Bz1fiGVE0o7ClzbvV1Cr11DBtbFIh26BhqTn4GgRLoEHR8csv\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"remember_token\":\"iHfIGoLoY2ynSdemKTsxQ0ACDzp7dPsXY9FcgrkuHxgQQYhFd1PFeopaqb9y\"}', '185.227.218.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/panel/logout', 'POST', '2026-09-08 05:22:19', '2026-09-08 05:22:19'),
(147, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '108', '{\"usuario_id\":108,\"id_empresa\":12,\"id_rol\":3,\"num_doc\":\"99887766\",\"usuario\":\"VICTOR\",\"clave\":\"$2y$12$WWCTij4EjyQfIqdMbhSm1Odw7LjNE5l22u2GEN6f\\/KxTWBY2mYjwC\",\"email\":\"vcanchari38@gmail.com\",\"nombres\":\"VICTOR RAUL\",\"apellidos\":\"CANCHARI RIQUI\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"92670321\",\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-07-10\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":null,\"updated_at\":null,\"created_at\":null}', '{\"id_rol\":8}', '185.227.218.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 07:51:39', '2026-09-08 07:51:39'),
(148, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '108', '{\"usuario_id\":108,\"id_empresa\":12,\"id_rol\":8,\"num_doc\":\"99887766\",\"usuario\":\"VICTOR\",\"clave\":\"$2y$12$WWCTij4EjyQfIqdMbhSm1Odw7LjNE5l22u2GEN6f\\/KxTWBY2mYjwC\",\"email\":\"vcanchari38@gmail.com\",\"nombres\":\"VICTOR RAUL\",\"apellidos\":\"CANCHARI RIQUI\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"92670321\",\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-07-10\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":null,\"updated_at\":null,\"created_at\":null}', '{\"clave\":\"$2y$12$mFVHHlIUaXwSqFMXdC.Db.E.2evF7WhCZTor7kZR0Y.Y2k8hcPIHW\"}', '185.227.218.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 07:51:52', '2026-09-08 07:51:52'),
(149, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":null,\"usuario\":\"admin\",\"clave\":\"$2y$12$5Fd.8bGpyECeP5.igqefzOGm8EyBFO5VclMgWsDrfH3AeI.y2JbJO\",\"email\":\"admin@admin.com\",\"nombres\":\"Admin\",\"apellidos\":\"Sistema\",\"rubro\":null,\"sucursal\":1,\"telefono\":null,\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"iHfIGoLoY2ynSdemKTsxQ0ACDzp7dPsXY9FcgrkuHxgQQYhFd1PFeopaqb9y\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"remember_token\":\"3ydN8sCg83M10BEWZYDzfgOuvjMH5qCnq0uic3PQ5ED7slbV1pglTAI5FjgL\"}', '185.227.218.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/panel/logout', 'POST', '2026-09-08 08:01:57', '2026-09-08 08:01:57'),
(150, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":null,\"usuario\":\"admin\",\"clave\":\"$2y$12$5Fd.8bGpyECeP5.igqefzOGm8EyBFO5VclMgWsDrfH3AeI.y2JbJO\",\"email\":\"admin@admin.com\",\"nombres\":\"Admin\",\"apellidos\":\"Sistema\",\"rubro\":null,\"sucursal\":1,\"telefono\":null,\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"3ydN8sCg83M10BEWZYDzfgOuvjMH5qCnq0uic3PQ5ED7slbV1pglTAI5FjgL\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"remember_token\":\"ezkIL5ZXnXo2LBjXHJiP9K1jRoOce3n2GFdHoluz1nk1Kb5mgHekyvNHc98I\"}', '185.227.218.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/panel/logout', 'POST', '2026-09-08 08:45:31', '2026-09-08 08:45:31'),
(151, 107, 'admin', '1', 12, 'created', 'App\\Models\\Caja', '6', NULL, '{\"nombre\":\"caja de prueba\",\"id_usuario_responsable\":111,\"estado\":\"ACTIVA\",\"id_empresa\":12,\"sucursal\":1,\"saldo_actual\":0,\"moneda\":\"PEN\",\"id_caja_padre\":null,\"id\":6}', '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 19:18:55', '2026-09-08 19:18:55'),
(152, 107, 'admin', '1', 12, 'created', 'App\\Models\\Caja', '7', NULL, '{\"nombre\":\"Caja victor\",\"id_usuario_responsable\":111,\"id_caja_padre\":6,\"estado\":\"ACTIVA\",\"id_empresa\":12,\"sucursal\":1,\"saldo_actual\":0,\"moneda\":\"PEN\",\"id\":7}', '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 19:19:35', '2026-09-08 19:19:35'),
(153, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Caja', '7', '{\"id\":7,\"id_empresa\":12,\"sucursal\":1,\"nombre\":\"Caja victor\",\"id_usuario_responsable\":111,\"id_caja_padre\":6,\"saldo_actual\":0,\"moneda\":\"PEN\",\"estado\":\"ACTIVA\"}', '{\"id_usuario_responsable\":108}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 19:21:19', '2026-09-08 19:21:19'),
(154, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Caja', '6', '{\"id\":6,\"id_empresa\":12,\"sucursal\":1,\"nombre\":\"caja de prueba\",\"id_usuario_responsable\":111,\"id_caja_padre\":null,\"saldo_actual\":0,\"moneda\":\"PEN\",\"estado\":\"ACTIVA\"}', '{\"id_usuario_responsable\":108}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 19:21:34', '2026-09-08 19:21:34'),
(155, 107, 'admin', '1', 12, 'updated', 'App\\Models\\Caja', '3', '{\"id\":3,\"id_empresa\":12,\"sucursal\":1,\"nombre\":\"CAJA VICTOR (PRUEBA)\",\"id_usuario_responsable\":111,\"id_caja_padre\":1,\"saldo_actual\":1160,\"moneda\":\"PEN\",\"estado\":\"ACTIVA\"}', '{\"id_usuario_responsable\":108}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 19:29:20', '2026-09-08 19:29:20'),
(156, 107, 'admin', '1', 12, 'created', 'App\\Models\\User', '112', NULL, '{\"num_doc\":\"71012821\",\"nombres\":\"MIGUEL ANGEL\",\"apellidos\":\"CHOQUE VALLEJOS\",\"telefono\":\"932348127\",\"foto\":null,\"usuario\":\"71012821\",\"email\":\"choquevallejosmiguelangel@gmail.com\",\"id_rol\":9,\"estado\":\"1\",\"available_status\":true,\"id_empresa\":12,\"sucursal\":1,\"fecha_inicio\":\"2026-09-08\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"usuario_id\":112}', '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 19:36:36', '2026-09-08 19:36:36'),
(157, 107, 'admin', '1', 12, 'created', 'App\\Models\\User', '113', NULL, '{\"num_doc\":\"45006566\",\"nombres\":\"ELOHA MILAGROS\",\"apellidos\":\"SOTO AYALA\",\"telefono\":\"979941429\",\"foto\":\"usuarios\\/fotos\\/01M20Y9SFGCBENN2ZRH89ZMA3E.jpg\",\"usuario\":\"45006566\",\"email\":\"milagroselohas.a@gmail.com\",\"id_rol\":3,\"estado\":\"1\",\"available_status\":true,\"id_empresa\":12,\"sucursal\":1,\"fecha_inicio\":\"2026-09-08\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"usuario_id\":113}', '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 19:40:57', '2026-09-08 19:40:57'),
(158, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":null,\"usuario\":\"admin\",\"clave\":\"$2y$12$5Fd.8bGpyECeP5.igqefzOGm8EyBFO5VclMgWsDrfH3AeI.y2JbJO\",\"email\":\"admin@admin.com\",\"nombres\":\"Admin\",\"apellidos\":\"Sistema\",\"rubro\":null,\"sucursal\":1,\"telefono\":null,\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"ezkIL5ZXnXo2LBjXHJiP9K1jRoOce3n2GFdHoluz1nk1Kb5mgHekyvNHc98I\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"num_doc\":\"32961853\",\"nombres\":\"ZOILA PATRICIA\",\"apellidos\":\"GUTIERREZ VELA\",\"telefono\":\"946423341\",\"foto\":\"usuarios\\/fotos\\/01M20ZRH4F7GGEEVQAMSPW67PG.jpeg\"}', '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:06:28', '2026-09-08 20:06:28'),
(159, 107, 'admin', '1', 12, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":\"32961853\",\"usuario\":\"admin\",\"clave\":\"$2y$12$5Fd.8bGpyECeP5.igqefzOGm8EyBFO5VclMgWsDrfH3AeI.y2JbJO\",\"email\":\"admin@admin.com\",\"nombres\":\"ZOILA PATRICIA\",\"apellidos\":\"GUTIERREZ VELA\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"946423341\",\"foto\":\"usuarios\\/fotos\\/01M20ZRH4F7GGEEVQAMSPW67PG.jpeg\",\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"ezkIL5ZXnXo2LBjXHJiP9K1jRoOce3n2GFdHoluz1nk1Kb5mgHekyvNHc98I\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"usuario\":\"32961853\",\"clave\":\"$2y$12$iCZy\\/WDQGT28q3tPYsLgmetPbN3WHxS2osKTdl5ukQKMb4CA0wZ6C\",\"email\":\"zoegv2602@gmail.com\"}', '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:08:37', '2026-09-08 20:08:37'),
(160, 107, '32961853', '1', 12, 'created', 'App\\Models\\User', '114', NULL, '{\"num_doc\":\"32970115\",\"nombres\":\"SILVIA DOROTHY\",\"apellidos\":\"MU\\u00d1OZ REGIS\",\"telefono\":\"32961853\",\"foto\":null,\"usuario\":\"32970115\",\"email\":\"dorothymunoz24@gmail.com\",\"id_rol\":1,\"estado\":\"1\",\"available_status\":true,\"id_empresa\":12,\"sucursal\":1,\"fecha_inicio\":\"2026-09-08\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"usuario_id\":114}', '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:10:16', '2026-09-08 20:10:16'),
(161, 107, '32961853', '1', 12, 'created', 'App\\Models\\User', '115', NULL, '{\"num_doc\":\"76165962\",\"nombres\":\"VICTOR RAUL\",\"apellidos\":\"CANCHARI RICKY\",\"telefono\":\"32970115\",\"foto\":null,\"usuario\":\"VICTORAUL\",\"email\":null,\"id_rol\":1,\"estado\":\"1\",\"available_status\":true,\"id_empresa\":12,\"sucursal\":1,\"fecha_inicio\":\"2026-09-08\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"usuario_id\":115}', '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:12:34', '2026-09-08 20:12:34'),
(162, 114, '32970115', '1', NULL, 'updated', 'App\\Models\\User', '114', '{\"usuario_id\":114,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":\"32970115\",\"usuario\":\"32970115\",\"clave\":\"$2y$12$oMJOg4Fn6W2Ot6CLZfYESOiTLGKvWQ88wixsV5pOTdo2WOGKZ9AZG\",\"email\":\"dorothymunoz24@gmail.com\",\"nombres\":\"SILVIA DOROTHY\",\"apellidos\":\"MU\\u00d1OZ REGIS\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"32961853\",\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-09-08\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":null,\"updated_at\":null,\"created_at\":null}', '{\"remember_token\":\"Zqt8FRnFbT97rDt2Caojj2o4xVXPwPoAFq8anq1TTB5SrVa7Jtcc0kgePdvM\"}', '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:15:11', '2026-09-08 20:15:11'),
(163, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\User', '115', '{\"usuario_id\":115,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":\"76165962\",\"usuario\":\"VICTORAUL\",\"clave\":\"$2y$12$WXf4dmMkTU91m8Teeg72FOp7MFORg7vcPUtk1aHQgvZUZGB.eG5la\",\"email\":null,\"nombres\":\"VICTOR RAUL\",\"apellidos\":\"CANCHARI RICKY\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"32970115\",\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-09-08\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":null,\"updated_at\":null,\"created_at\":null}', '{\"remember_token\":\"kGT6CeHMv56tnpNPChuohPNJrvnMu56blarYe3NR8IOqPdmCR3EGrWi6TBip\"}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:18:22', '2026-09-08 20:18:22'),
(164, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\User', '115', '{\"usuario_id\":115,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":\"76165962\",\"usuario\":\"VICTORAUL\",\"clave\":\"$2y$12$WXf4dmMkTU91m8Teeg72FOp7MFORg7vcPUtk1aHQgvZUZGB.eG5la\",\"email\":null,\"nombres\":\"VICTOR RAUL\",\"apellidos\":\"CANCHARI RICKY\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"32970115\",\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-09-08\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"kGT6CeHMv56tnpNPChuohPNJrvnMu56blarYe3NR8IOqPdmCR3EGrWi6TBip\",\"updated_at\":null,\"created_at\":null}', '{\"remember_token\":\"uRORMSOLPHzxeFXcuGzaTwdrkbMjUH7b6IUi4F1e9k8GC4VpbfwAJbqq8W8M\"}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/reporte/productos/plantilla', 'GET', '2026-09-08 20:18:28', '2026-09-08 20:18:28'),
(165, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\Producto', '432', NULL, '{\"cod_barra\":null,\"codigo\":\"80105\",\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"stock_minimo\":50,\"stock_maximo\":100,\"peso_bruto\":5,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":20,\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"activo\":true,\"imagen\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"id_producto\":432}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:29:46', '2026-09-08 20:29:46'),
(166, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\Producto', '433', NULL, '{\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":0,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":80105,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\",\"id_producto\":433}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:31:20', '2026-09-08 20:31:20'),
(167, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\Producto', '433', '{\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":0,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":80105,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\",\"id_producto\":433}', '{\"cantidad\":10}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:31:20', '2026-09-08 20:31:20'),
(168, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\InventarioMovimiento', '54', NULL, '{\"id_empresa\":0,\"almacen\":80105,\"id_producto\":433,\"tipo\":\"I\",\"id_motivo\":16,\"cantidad\":10,\"stock_anterior\":0,\"stock_nuevo\":10,\"observacion\":\"RELLEA\",\"id_usuario\":115,\"fecha\":\"2026-09-08T17:31:20.610814Z\",\"id_movimiento\":54}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:31:20', '2026-09-08 20:31:20'),
(169, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\Producto', '433', '{\"id_producto\":433,\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":10,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"80105\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\"}', '{\"cantidad\":0}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:32:25', '2026-09-08 20:32:25'),
(170, 115, 'VICTORAUL', '1', NULL, 'deleted', 'App\\Models\\InventarioMovimiento', '54', '{\"id_movimiento\":54,\"id_empresa\":0,\"almacen\":\"80105\",\"id_producto\":433,\"tipo\":\"I\",\"id_motivo\":16,\"cantidad\":10,\"stock_anterior\":0,\"stock_nuevo\":10,\"costo\":null,\"id_proveedor\":null,\"observacion\":\"RELLEA\",\"id_usuario\":115,\"fecha\":\"2026-09-08 17:31:20\",\"producto\":{\"id_producto\":433,\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":10,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"80105\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\"},\"motivo\":{\"id_motivo\":16,\"nombre\":\"CARGA INICAL\",\"tipo\":\"I\",\"es_sistema\":0,\"id_empresa\":0,\"estado\":\"1\"},\"usuario\":{\"usuario_id\":115,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":\"76165962\",\"usuario\":\"VICTORAUL\",\"email\":null,\"nombres\":\"VICTOR RAUL\",\"apellidos\":\"CANCHARI RICKY\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"32970115\",\"foto\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-09-08\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"updated_at\":null,\"created_at\":null}}', NULL, '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:32:25', '2026-09-08 20:32:25'),
(171, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\Proveedor', '188', NULL, '{\"ruc\":\"20100035121\",\"razon_social\":\"MOLITALIA S.A\",\"nombre_comercial\":\"MOLITLIA\",\"direccion\":\"AV. VENEZUELA NRO. 2850 URB. ELIO LIMA LIMA LIMA, LIMA, LIMA, LIMA\",\"telefono\":\"923654727\",\"email\":\"JCASACHAGUA@MOLITALIA.COM.PE\",\"id_empresa\":0,\"fecha_create\":\"2026-09-08T17:35:19.743815Z\",\"estado\":1,\"direccion2\":\"\",\"telefono2\":\"\",\"proveedor_id\":188}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:35:19', '2026-09-08 20:35:19'),
(172, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\Compra', '167', NULL, '{\"id_proveedor\":188,\"id_tido\":2,\"id_tipo_pago\":1,\"instrumento_tipo\":\"TRANSFERENCIA\",\"instrumento_id\":5,\"fecha_emision\":\"2026-09-08T00:00:00.000000Z\",\"fecha_vencimiento\":\"2026-09-08T00:00:00.000000Z\",\"direccion\":\"sfsefsfse\",\"serie\":\"F001\",\"numero\":\"01115591\",\"total\":25.6099999999999994315658113919198513031005859375,\"id_empresa\":0,\"sucursal\":0,\"moneda\":\"S\",\"recepcionado\":0,\"id_compra\":167}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:41:54', '2026-09-08 20:41:54');
INSERT INTO `audits` (`id`, `user_id`, `user_name`, `user_rol`, `empresa_id`, `event`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `method`, `created_at`, `updated_at`) VALUES
(173, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\Producto', '434', NULL, '{\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":0,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":80105,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\",\"id_producto\":434}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:46:35', '2026-09-08 20:46:35'),
(174, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\Producto', '434', '{\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":0,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":80105,\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\",\"id_producto\":434}', '{\"cantidad\":50}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:46:35', '2026-09-08 20:46:35'),
(175, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\InventarioMovimiento', '55', NULL, '{\"id_empresa\":0,\"almacen\":80105,\"id_producto\":434,\"tipo\":\"I\",\"id_motivo\":16,\"cantidad\":50,\"stock_anterior\":0,\"stock_nuevo\":50,\"observacion\":\"aaaaaaaaa\",\"id_usuario\":115,\"fecha\":\"2026-09-08T17:46:35.798154Z\",\"id_movimiento\":55}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:46:35', '2026-09-08 20:46:35'),
(176, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\Traslado', '3', NULL, '{\"id_empresa\":0,\"almacen_origen\":80105,\"almacen_destino\":\"AL2\",\"fecha\":\"2026-09-08T17:49:53.000000Z\",\"observacion\":\"aaaaaaaaaa\",\"id_usuario\":115,\"estado\":\"1\",\"id_traslado\":3}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:49:53', '2026-09-08 20:49:53'),
(177, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\Producto', '434', '{\"id_producto\":434,\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":50,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"80105\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\"}', '{\"cantidad\":45}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:49:53', '2026-09-08 20:49:53'),
(178, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\InventarioMovimiento', '56', NULL, '{\"id_empresa\":0,\"almacen\":80105,\"id_producto\":434,\"tipo\":\"S\",\"id_motivo\":null,\"cantidad\":5,\"stock_anterior\":50,\"stock_nuevo\":45,\"costo\":25.6099999999999994315658113919198513031005859375,\"observacion\":\"Traslado TS-00000003 a ALMACEN2. aaaaaaaaaa\",\"id_usuario\":115,\"fecha\":\"2026-09-08T17:49:53.789253Z\",\"id_movimiento\":56}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:49:53', '2026-09-08 20:49:53'),
(179, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\Producto', '435', NULL, '{\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":5,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"AL2\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\",\"id_producto\":435}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:49:53', '2026-09-08 20:49:53'),
(180, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\InventarioMovimiento', '57', NULL, '{\"id_empresa\":0,\"almacen\":\"AL2\",\"id_producto\":435,\"tipo\":\"I\",\"id_motivo\":null,\"cantidad\":5,\"stock_anterior\":0,\"stock_nuevo\":5,\"costo\":25.6099999999999994315658113919198513031005859375,\"observacion\":\"Traslado TS-00000003 desde ALMACEN1. aaaaaaaaaa\",\"id_usuario\":115,\"fecha\":\"2026-09-08T17:49:53.789253Z\",\"id_movimiento\":57}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:49:53', '2026-09-08 20:49:53'),
(181, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\Prestamo', '5', NULL, '{\"id_empresa\":0,\"tipo\":\"P\",\"tercero\":\"wuygfjfbse\",\"almacen\":\"AL2\",\"estado\":\"P\",\"observacion\":\"sefsfe\",\"id_usuario\":115,\"fecha\":\"2026-09-08T17:50:46.728318Z\",\"id_producto\":434,\"cantidad\":1,\"id_prestamo\":5}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:50:46', '2026-09-08 20:50:46'),
(182, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\Producto', '434', '{\"id_producto\":434,\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":45,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"80105\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\"}', '{\"cantidad\":44}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:50:46', '2026-09-08 20:50:46'),
(183, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\InventarioMovimiento', '58', NULL, '{\"id_empresa\":0,\"almacen\":\"AL2\",\"id_producto\":434,\"tipo\":\"S\",\"id_motivo\":null,\"cantidad\":1,\"stock_anterior\":45,\"stock_nuevo\":44,\"costo\":25.6099999999999994315658113919198513031005859375,\"observacion\":\"Pr\\u00e9stamo a wuygfjfbse\",\"id_usuario\":115,\"fecha\":\"2026-09-08T17:50:46.730339Z\",\"id_movimiento\":58}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:50:46', '2026-09-08 20:50:46'),
(184, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\Producto', '435', '{\"id_producto\":435,\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":5,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"AL2\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\"}', '{\"cantidad\":6}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:51:02', '2026-09-08 20:51:02'),
(185, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\InventarioMovimiento', '59', NULL, '{\"id_empresa\":0,\"almacen\":\"AL2\",\"id_producto\":435,\"tipo\":\"I\",\"id_motivo\":null,\"cantidad\":1,\"stock_anterior\":5,\"stock_nuevo\":6,\"costo\":25.6099999999999994315658113919198513031005859375,\"observacion\":\"Devoluci\\u00f3n de wuygfjfbse\",\"id_usuario\":115,\"fecha\":\"2026-09-08T17:51:02.723344Z\",\"id_movimiento\":59}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:51:02', '2026-09-08 20:51:02'),
(186, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\Prestamo', '5', '{\"id_prestamo\":5,\"id_empresa\":0,\"tipo\":\"P\",\"tercero\":\"wuygfjfbse\",\"id_producto\":434,\"almacen\":\"AL2\",\"cantidad\":1,\"estado\":\"P\",\"observacion\":\"sefsfe\",\"id_usuario\":115,\"fecha\":\"2026-09-08 17:50:46\",\"fecha_devolucion\":null}', '{\"estado\":\"D\",\"fecha_devolucion\":\"2026-09-08T17:51:02.724307Z\"}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:51:02', '2026-09-08 20:51:02'),
(187, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\Producto', '435', '{\"id_producto\":435,\"cod_barra\":null,\"descripcion\":\"MOLITATIA TORNILLO 76 20x250 GR\",\"precio\":30,\"costo\":25.6099999999999994315658113919198513031005859375,\"cantidad\":6,\"stock_minimo\":50,\"stock_maximo\":100,\"iscbp\":null,\"id_empresa\":0,\"sucursal\":1,\"ultima_salida\":\"2026-09-08T00:00:00.000000Z\",\"codsunat\":\"-\",\"usar_barra\":\"0\",\"precio_mayor\":null,\"precio_menor\":null,\"peso_bruto\":5,\"razon_social\":null,\"ruc\":null,\"estado\":\"1\",\"almacen\":\"AL2\",\"precio2\":0,\"precio3\":0,\"precio4\":0,\"precio_unidad\":null,\"codigo\":\"80105\",\"id_categoria\":10,\"id_subcategoria\":24,\"id_marca\":10,\"id_submarca\":19,\"imagen\":null,\"activo\":1,\"medida\":\"CAJA POMAROLA \",\"presentaciones\":\"BOLSA\",\"cnt_presenta\":\"20\"}', '{\"cantidad\":4}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:52:32', '2026-09-08 20:52:32'),
(188, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\InventarioMovimiento', '60', NULL, '{\"id_empresa\":0,\"almacen\":\"AL2\",\"id_producto\":435,\"tipo\":\"S\",\"id_motivo\":17,\"cantidad\":2,\"stock_anterior\":6,\"stock_nuevo\":4,\"observacion\":\"sdfefsfs\",\"id_usuario\":115,\"fecha\":\"2026-09-08T17:52:32.011740Z\",\"id_movimiento\":60}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 20:52:32', '2026-09-08 20:52:32'),
(189, 115, 'VICTORAUL', '1', NULL, 'created', 'App\\Models\\Cliente', '2540', NULL, '{\"documento\":\"20612637441\",\"datos\":\"INVERSIONES EL JHORCH S.A.C.\",\"direccion\":\"AV. METROPOLITANA 2450 INT NRO. 566 URB. SANTA LUZMILA LIMA LIMA COMAS, COMAS, LIMA, LIMA\",\"ubigeo\":\"150110\",\"distrito\":\"COMAS\",\"mercado\":25,\"telefono\":\"925467771\",\"email\":null,\"id_empresa\":0,\"id_cliente\":2540}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/livewire-cccef930/update', 'POST', '2026-09-08 21:00:28', '2026-09-08 21:00:28'),
(190, 115, 'VICTORAUL', '1', NULL, 'updated', 'App\\Models\\User', '115', '{\"usuario_id\":115,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":\"76165962\",\"usuario\":\"VICTORAUL\",\"clave\":\"$2y$12$WXf4dmMkTU91m8Teeg72FOp7MFORg7vcPUtk1aHQgvZUZGB.eG5la\",\"email\":null,\"nombres\":\"VICTOR RAUL\",\"apellidos\":\"CANCHARI RICKY\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"32970115\",\"foto\":null,\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2026-09-08\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"uRORMSOLPHzxeFXcuGzaTwdrkbMjUH7b6IUi4F1e9k8GC4VpbfwAJbqq8W8M\",\"updated_at\":null,\"created_at\":null}', '{\"remember_token\":\"O2wnO2bjOKDIDCzW6wrKHA6F0mjMIulflAkAfTgzsFOdMtJCjMkhCK55UPv5\"}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/panel/logout', 'POST', '2026-09-08 21:01:34', '2026-09-08 21:01:34'),
(191, 107, '32961853', '1', NULL, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":\"32961853\",\"usuario\":\"32961853\",\"clave\":\"$2y$12$iCZy\\/WDQGT28q3tPYsLgmetPbN3WHxS2osKTdl5ukQKMb4CA0wZ6C\",\"email\":\"zoegv2602@gmail.com\",\"nombres\":\"ZOILA PATRICIA\",\"apellidos\":\"GUTIERREZ VELA\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"946423341\",\"foto\":\"usuarios\\/fotos\\/01M20ZRH4F7GGEEVQAMSPW67PG.jpeg\",\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"ezkIL5ZXnXo2LBjXHJiP9K1jRoOce3n2GFdHoluz1nk1Kb5mgHekyvNHc98I\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"remember_token\":\"f1gjvIIAzR24Cltm2FmqzaquKOUP4yfrWIFeLLYpSf65t3oRMZtIy99lfmdu\"}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/panel/logout', 'POST', '2026-09-08 21:03:45', '2026-09-08 21:03:45'),
(192, 107, '32961853', '1', NULL, 'updated', 'App\\Models\\User', '107', '{\"usuario_id\":107,\"id_empresa\":12,\"id_rol\":1,\"num_doc\":\"32961853\",\"usuario\":\"32961853\",\"clave\":\"$2y$12$iCZy\\/WDQGT28q3tPYsLgmetPbN3WHxS2osKTdl5ukQKMb4CA0wZ6C\",\"email\":\"zoegv2602@gmail.com\",\"nombres\":\"ZOILA PATRICIA\",\"apellidos\":\"GUTIERREZ VELA\",\"rubro\":null,\"sucursal\":1,\"telefono\":\"946423341\",\"foto\":\"usuarios\\/fotos\\/01M20ZRH4F7GGEEVQAMSPW67PG.jpeg\",\"token_reset\":null,\"estado\":\"1\",\"mensaje\":null,\"rotativo\":false,\"fecha_inicio\":\"2024-01-01\",\"fecha_salida\":\"2030-12-31\",\"funciones\":\"\",\"id_ruta\":null,\"available_status\":true,\"remember_token\":\"f1gjvIIAzR24Cltm2FmqzaquKOUP4yfrWIFeLLYpSf65t3oRMZtIy99lfmdu\",\"updated_at\":null,\"created_at\":\"2026-06-26 14:10:27\"}', '{\"remember_token\":\"Tbi0FQ0vHrxZNXXeqk4GL27TjeVhDQSQLQvKi18WAad8VzEs4fn5NHJrMZP4\"}', '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'https://corporacionromaerp.com/reporte/compras/pdf/167', 'GET', '2026-09-08 21:24:56', '2026-09-08 21:24:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos`
--

CREATE TABLE `bancos` (
  `id_banco` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo_sunat` varchar(10) DEFAULT NULL,
  `estado` varchar(2) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `bancos`
--

INSERT INTO `bancos` (`id_banco`, `id_empresa`, `nombre`, `codigo_sunat`, `estado`, `created_at`, `updated_at`) VALUES
(1, 12, 'BCP', '02', '1', '2026-07-10 05:58:03', '2026-07-10 06:08:18'),
(2, 12, 'BBVA', '11', '1', '2026-07-10 05:58:03', '2026-07-10 05:58:03'),
(3, 12, 'interbanck', 'c3', '1', '2026-07-22 19:28:39', '2026-07-22 19:28:39'),
(4, 0, 'bcp', 'AA', '1', '2026-09-08 20:37:06', '2026-09-08 20:37:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `billeteras_digitales`
--

CREATE TABLE `billeteras_digitales` (
  `id_billetera` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `id_cuenta_bancaria` int(10) UNSIGNED DEFAULT NULL,
  `id_billetera_tipo` tinyint(3) UNSIGNED NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `titular` varchar(200) NOT NULL,
  `qr` varchar(255) DEFAULT NULL,
  `estado` varchar(2) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `billeteras_digitales`
--

INSERT INTO `billeteras_digitales` (`id_billetera`, `id_empresa`, `id_cuenta_bancaria`, `id_billetera_tipo`, `telefono`, `titular`, `qr`, `estado`, `created_at`, `updated_at`) VALUES
(1, 12, 1, 7, '987654321', 'victor raul canchari', 'qrs/01M1ZRS36MQPNC8FE4RW1W93YZ.jpg', '1', '2026-07-10 05:58:03', '2026-09-08 08:45:12'),
(2, 12, 3, 8, '92670321', 'victor raul canchari', NULL, '1', '2026-07-10 05:58:03', '2026-07-10 05:58:03'),
(3, 12, 4, 8, '92670321', 'victor', NULL, '1', '2026-07-22 19:31:25', '2026-07-22 19:31:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `billetera_tipos`
--

CREATE TABLE `billetera_tipos` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `estado` varchar(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `billetera_tipos`
--

INSERT INTO `billetera_tipos` (`id`, `id_empresa`, `nombre`, `estado`) VALUES
(1, 1, 'Yape', '1'),
(2, 1, 'Plin', '1'),
(3, 1, 'Tunki', '1'),
(4, 1, 'Agora', '1'),
(5, 1, 'BIM', '1'),
(6, 1, 'Otro', '1'),
(7, 12, 'Yape', '1'),
(8, 12, 'Plin', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `sucursal` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_usuario_responsable` int(10) UNSIGNED DEFAULT NULL,
  `id_caja_padre` int(10) UNSIGNED DEFAULT NULL,
  `saldo_actual` decimal(14,2) NOT NULL DEFAULT 0.00,
  `moneda` varchar(3) NOT NULL DEFAULT 'PEN',
  `estado` varchar(10) NOT NULL DEFAULT 'ACTIVA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `cajas`
--

INSERT INTO `cajas` (`id`, `id_empresa`, `sucursal`, `nombre`, `id_usuario_responsable`, `id_caja_padre`, `saldo_actual`, `moneda`, `estado`) VALUES
(1, 12, 1, 'Caja Principal', NULL, NULL, 4300.00, 'PEN', 'ACTIVA'),
(2, 12, 1, 'Caja Chica', NULL, 1, 0.00, 'PEN', 'INACTIVA'),
(3, 12, 1, 'CAJA VICTOR (PRUEBA)', 108, 1, 1160.00, 'PEN', 'ACTIVA'),
(4, 12, 1, 'caja admin principal', 107, NULL, 1698.00, 'PEN', 'ACTIVA'),
(5, 12, 1, 'caja admin', 107, 4, 66769.82, 'PEN', 'ACTIVA'),
(6, 12, 1, 'caja de prueba', 108, NULL, 0.00, 'PEN', 'ACTIVA'),
(7, 12, 1, 'Caja victor', 108, 6, 0.00, 'PEN', 'ACTIVA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_aperturas`
--

CREATE TABLE `caja_aperturas` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_caja` int(10) UNSIGNED NOT NULL,
  `id_transferencia` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha` date NOT NULL,
  `monto_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `estado` varchar(20) NOT NULL DEFAULT 'ABIERTA' COMMENT 'ABIERTA|CERRADA',
  `id_usuario_apertura` int(10) UNSIGNED NOT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `caja_aperturas`
--

INSERT INTO `caja_aperturas` (`id`, `id_caja`, `id_transferencia`, `fecha`, `monto_total`, `estado`, `id_usuario_apertura`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 3, NULL, '2026-07-10', 600.00, 'CERRADA', 108, 'adaada [Monto fijo ingresado]', '2026-07-10 08:36:34', '2026-07-28 03:45:15'),
(2, 5, NULL, '2026-07-10', 3000.00, 'CERRADA', 107, 'ada dadawd [Monto fijo ingresado]', '2026-07-10 19:06:38', '2026-07-10 21:11:54'),
(3, 5, NULL, '2026-07-27', 133.53, 'CERRADA', 107, 'adadawd [Monto fijo ingresado]', '2026-07-28 00:28:01', '2026-07-28 03:25:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_apertura_detalles`
--

CREATE TABLE `caja_apertura_detalles` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_apertura` int(10) UNSIGNED NOT NULL,
  `denominacion` decimal(10,2) NOT NULL,
  `tipo` varchar(10) NOT NULL COMMENT 'BILLETE|MONEDA',
  `cantidad` int(10) UNSIGNED NOT NULL,
  `subtotal` decimal(14,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `caja_apertura_detalles`
--

INSERT INTO `caja_apertura_detalles` (`id`, `id_apertura`, `denominacion`, `tipo`, `cantidad`, `subtotal`) VALUES
(1, 1, 200.00, 'BILLETE', 1, 200.00),
(2, 1, 100.00, 'BILLETE', 1, 100.00),
(3, 1, 50.00, 'BILLETE', 6, 300.00),
(4, 2, 200.00, 'BILLETE', 10, 2000.00),
(5, 2, 100.00, 'BILLETE', 10, 1000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_chica`
--

CREATE TABLE `caja_chica` (
  `caja_chica_id` int(11) NOT NULL,
  `id_caja_empresa` int(11) DEFAULT NULL,
  `hora` varchar(50) DEFAULT NULL,
  `detalle` varchar(220) DEFAULT NULL,
  `tipo` char(1) DEFAULT 'f',
  `entrada` double(15,2) DEFAULT NULL,
  `salida` double(15,2) DEFAULT NULL,
  `metodo` char(1) DEFAULT NULL COMMENT '1 = EFECTIVO 2 =TARJETAS 3 =TRANSFERENCIAS'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `caja_chica`
--

INSERT INTO `caja_chica` (`caja_chica_id`, `id_caja_empresa`, `hora`, `detalle`, `tipo`, `entrada`, `salida`, `metodo`) VALUES
(1, 1, '08:49 PM', 'Apertura de caja', 'a', 500.00, 0.00, '1'),
(2, 1, '08:49 PM', 'Alex', 'f', 0.00, 500.00, '1'),
(3, 2, '11:00 AM', 'Apertura de caja', 'a', 100.00, 0.00, '1'),
(4, 2, '11:05 AM', 'favrt124', 'f', 0.00, 1000.00, '1'),
(5, 3, '05:40 PM', 'Apertura de caja', 'a', 4500.00, 0.00, '1'),
(6, 3, '05:40 PM', '10 gasolina', 'f', 0.00, 70.00, '1'),
(7, 3, '05:41 PM', 'efectivo 300', 'f', 300.00, 0.00, '1'),
(8, 4, '09:57 AM', 'Apertura de caja', 'a', 0.00, 0.00, '1'),
(9, 5, '05:19 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1'),
(10, 5, '06:04 PM', 'Cobro Cotización #35946 - ANY VARGAS (VIRGEN DEL ROSARIO)', 'f', 0.10, 0.00, '1'),
(11, 6, '06:55 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1'),
(12, 6, '06:55 PM', 'Cobro Cotización #36247 - A-ELENA BLAS (ASOCIACION D COMERCIANTES)', 'f', 470.00, 0.00, '1'),
(13, 6, '06:57 PM', 'Cobro Cotización #36245 - A-ELENA CORNEJO (MACCHUPICHU)', 'f', 50.00, 0.00, '1'),
(14, 6, '06:57 PM', 'Cobro Cotización #36272 - B-HILDA PINEDA (BUEN JESUS)', 'f', 0.40, 0.00, '1'),
(15, 7, '07:13 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1'),
(16, 7, '07:14 PM', 'Cobro Cotización #34378 - G-LALO MALLQUI (JR EMILIO SANDOVAL)', 'f', 102.00, 0.00, '1'),
(17, 8, '07:15 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1'),
(18, 8, '07:16 PM', 'Cobro Cotización #35613 - E-BEATRIZ ROMANI CARRION (CASTILLA)', 'f', 58.50, 0.00, '3'),
(19, 8, '07:16 PM', 'Cobro Cotización #26053 - F-RAFAEL CONDORI (SAN MIGUEL )', 'f', 207.60, 0.00, '1'),
(20, 8, '07:16 PM', 'Cobro Cotización #26053 - F-RAFAEL CONDORI (SAN MIGUEL )', 'f', 500.00, 0.00, '3'),
(21, 8, '07:17 PM', 'Cobro Cotización #36283 - D-MARIO ROSALES (LA MARINA )', 'f', 30.00, 0.00, '1'),
(22, 7, '07:17 PM', 'Cobro Cotización #34370 - G-MANUEL DE LA CRUZ (PARADITA #3)', 'f', 23.60, 0.00, '1'),
(23, 8, '07:17 PM', 'Cobro Cotización #36284 - D-LEONOR ESCOBERO (MARINA)', 'f', 565.50, 0.00, '1'),
(24, 8, '07:17 PM', 'Cobro Cotización #36282 - D-VICTORIA (MARINA)', 'f', 142.60, 0.00, '1'),
(25, 8, '07:18 PM', 'Cobro Cotización #36359 - F-DELIA MARQUEZ (SAN MIGUEL)', 'f', 149.50, 0.00, '1'),
(26, 8, '07:21 PM', 'Cobro Cotización #36257 - AMELIA CARHUARICRA HUARI', 'f', 158.20, 0.00, '1'),
(27, 8, '07:22 PM', 'Cobro Cotización #36292 - D-MARIO PILCO (MARINA)', 'f', 293.00, 0.00, '1'),
(28, 8, '07:23 PM', 'Cobro Cotización #36297 - D-FABIANA PEREZ BARCAS (LA MARINA )', 'f', 39.90, 0.00, '1'),
(29, 8, '07:23 PM', 'Cobro Cotización #36301 - F-RUTH PORRAS (SAN  MIGUEL) (AFUERA)', 'f', 242.00, 0.00, '3'),
(30, 8, '07:23 PM', 'Cobro Cotización #36293 - D-ESTELA JULIAN (LA MARINA)', 'f', 480.40, 0.00, '3'),
(31, 8, '07:33 PM', 'Cobro Cotización #36259 - C-NELLY VASQUEZ (LA PERLA)', 'f', 240.00, 0.00, '1'),
(32, 8, '07:44 PM', 'Cobro Cotización #35648 - E-OSCAR (METRITO)', 'f', 1771.50, 0.00, '3'),
(33, 8, '07:44 PM', 'Cobro Cotización #34951 - E-OSCAR (METRITO)', 'f', 1341.50, 0.00, '3'),
(34, 8, '07:45 PM', 'Cobro Cotización #36278 - E-COMERCIAL ANGELITOS (M.CASTILLA)', 'f', 263.60, 0.00, '3'),
(35, 8, '07:45 PM', 'Cobro Cotización #36277 - E-ERIKA MENESES (CASTILLA)', 'f', 72.40, 0.00, '1'),
(36, 4, '08:19 PM', 'ADMIN', 'f', 0.00, 0.00, '1'),
(37, 9, '08:20 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1'),
(38, 9, '08:22 PM', 'Cobro Cotización #20356 - B-CEVICHERIA JACKY (CHIRA)', 'f', 500.00, 0.00, '1'),
(39, 9, '08:22 PM', 'Cobro Cotización #20356 - B-CEVICHERIA JACKY (CHIRA)', 'f', 660.10, 0.00, '3'),
(40, 10, '09:02 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1'),
(41, 11, '07:01 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1'),
(42, 12, '10:48 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1'),
(43, 12, '10:51 PM', 'Cobro Cotización #35739 - B-LORENZA CHIPANA (MATEO PUMACAHUA )', 'f', 266.80, 0.00, '3'),
(44, 12, '10:51 PM', 'Cobro Cotización #34400 - B-LUZ SEGIL (MATEO PUMACAHUA)', 'f', 100.00, 0.00, '3'),
(45, 12, '10:52 PM', 'Cobro Cotización #34400 - B-LUZ SEGIL (MATEO PUMACAHUA)', 'f', 103.70, 0.00, '1'),
(46, 12, '10:52 PM', 'Cobro Cotización #35293 - E-DEL CARPIO (TNE JIMENEZ)', 'f', 250.00, 0.00, '3'),
(47, 12, '10:54 PM', 'Cobro Cotización #33687 - E-MARIA GAMARRA (TNTE JIMENEZ)', 'f', 276.90, 0.00, '3'),
(48, 12, '10:55 PM', 'Cobro Cotización #35321 - G-CASTILLO ALCALA (PROCERES)', 'f', 50.10, 0.00, '3'),
(49, 12, '10:55 PM', 'Cobro Cotización #35748 - G-RENE VALDERRAMA MENDOZA(PROCERES)', 'f', 100.00, 0.00, '1'),
(50, 13, '07:09 AM', 'Apertura de caja', 'a', 0.00, 0.00, '1'),
(51, 13, '07:10 AM', 'Cobro Cotización #35240 - H-EUGENIA DUEÑAS (V.CARMEN)', 'f', 45.00, 0.00, '3'),
(52, 13, '07:12 AM', 'Cobro Cotización #35765 - H-EUGENIA DUEÑAS (V.CARMEN)', 'f', 200.00, 0.00, '3'),
(53, 13, '09:29 AM', 'Cobro Cotización #35769 - H-EDUARDO QUISPE (V.CARMEN)', 'f', 42.80, 0.00, '1'),
(54, 13, '09:54 AM', 'Cobro Cotización #35251 - H-FABIOLA (V. CARMEN)', 'f', 162.90, 0.00, '1'),
(55, 13, '09:55 AM', 'Cobro Cotización #35766 - H-FABIOLA (V. CARMEN)', 'f', 100.00, 0.00, '1'),
(56, 13, '10:07 AM', 'Cobro Cotización #35255 - H-NOEMY SIERRA (V. DEL CARMEN)', 'f', 150.00, 0.00, '1'),
(57, 13, '10:33 AM', 'Cobro Cotización #35777 - H-CARMEN ROSA (V.CARMEN)', 'f', 247.00, 0.00, '3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_cierre_deudas`
--

CREATE TABLE `caja_cierre_deudas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_cierre` int(10) UNSIGNED NOT NULL,
  `id_caja` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `estado` varchar(15) NOT NULL DEFAULT 'PENDIENTE',
  `observaciones` varchar(255) DEFAULT NULL,
  `id_usuario_registra` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `caja_cierre_deudas`
--

INSERT INTO `caja_cierre_deudas` (`id`, `id_cierre`, `id_caja`, `id_usuario`, `monto`, `estado`, `observaciones`, `id_usuario_registra`, `created_at`, `updated_at`) VALUES
(1, 3, 3, 111, 500.00, 'PENDIENTE', 'Faltante en cierre de caja del 2026-07-27', 107, '2026-07-28 03:45:55', '2026-07-28 03:45:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_empresa`
--

CREATE TABLE `caja_empresa` (
  `caja_id` int(11) NOT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `sucursal` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `detalle` varchar(200) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `entrada` varchar(200) DEFAULT NULL,
  `salida` varchar(200) DEFAULT NULL,
  `estado` char(1) DEFAULT '1',
  `instrumento_tipo` varchar(30) DEFAULT NULL,
  `instrumento_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `caja_empresa`
--

INSERT INTO `caja_empresa` (`caja_id`, `id_empresa`, `sucursal`, `id_usuario`, `detalle`, `fecha`, `entrada`, `salida`, `estado`, `instrumento_tipo`, `instrumento_id`) VALUES
(1, 12, 1, NULL, 'Luz', '2024-10-14 00:00:00', '', '', '1', NULL, NULL),
(2, 12, 1, NULL, 'wilmer', '2025-03-17 00:00:00', '', '', '1', NULL, NULL),
(3, 12, 1, NULL, 'MIERCOLES 16 JULIO', '2025-07-16 00:00:00', '4800', '70', '0', NULL, NULL),
(4, 12, 1, NULL, '09 diciembre', '2026-01-09 00:00:00', '', '', '1', NULL, NULL),
(5, 12, 1, 60, 'Prueba', '2026-01-09 17:20:04', '0', '0', '0', NULL, NULL),
(6, 12, 1, 94, 'gamarra', '2026-01-09 18:55:23', '0', '0', '0', NULL, NULL),
(7, 12, 1, 61, 'Paz', '2026-01-09 19:13:49', '0', '0', '0', NULL, NULL),
(8, 12, 1, 92, 'YORCHS', '2026-01-09 19:15:39', '0', '0', '0', NULL, NULL),
(9, 12, 1, 40, 'admin', '2026-01-09 20:20:45', '0', '0', '0', NULL, NULL),
(10, 12, 1, 61, 'Paz', '2026-01-09 21:02:38', '0', '0', '1', NULL, NULL),
(11, 12, 1, 62, 'hum', '2026-01-09 21:42:00', '0', '0', '0', NULL, NULL),
(12, 12, 1, 80, 'Marianela', '2026-01-09 22:48:23', '0', '0', '0', NULL, NULL),
(13, 12, 1, 61, 'Paz', '2026-01-10 07:09:25', '0', '0', '1', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_instrumentos`
--

CREATE TABLE `caja_instrumentos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_caja` int(10) UNSIGNED NOT NULL,
  `instrumento_tipo` varchar(30) NOT NULL,
  `instrumento_id` int(10) UNSIGNED DEFAULT NULL,
  `estado` varchar(10) NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `caja_instrumentos`
--

INSERT INTO `caja_instrumentos` (`id`, `id_caja`, `instrumento_tipo`, `instrumento_id`, `estado`) VALUES
(1, 5, 'TRANSFERENCIA', 1, 'ACTIVO'),
(2, 5, 'TRANSFERENCIA', 2, 'ACTIVO'),
(3, 5, 'TRANSFERENCIA', 3, 'ACTIVO'),
(4, 5, 'BILLETERA_DIGITAL', 1, 'ACTIVO'),
(5, 5, 'BILLETERA_DIGITAL', 2, 'ACTIVO'),
(6, 3, 'BILLETERA_DIGITAL', 3, 'ACTIVO'),
(7, 3, 'BILLETERA_DIGITAL', 2, 'ACTIVO'),
(8, 3, 'BILLETERA_DIGITAL', 1, 'ACTIVO'),
(9, 3, 'TRANSFERENCIA', 4, 'ACTIVO'),
(10, 3, 'TRANSFERENCIA', 3, 'ACTIVO'),
(11, 3, 'TRANSFERENCIA', 2, 'ACTIVO'),
(12, 3, 'EFECTIVO', NULL, 'ACTIVO'),
(13, 3, 'TRANSFERENCIA', 1, 'ACTIVO'),
(14, 7, 'BILLETERA_DIGITAL', 1, 'ACTIVO'),
(15, 7, 'EFECTIVO', NULL, 'ACTIVO'),
(16, 7, 'TRANSFERENCIA', 1, 'ACTIVO'),
(17, 7, 'TRANSFERENCIA', 2, 'ACTIVO'),
(18, 7, 'TRANSFERENCIA', 3, 'ACTIVO'),
(19, 7, 'TRANSFERENCIA', 4, 'ACTIVO'),
(20, 7, 'BILLETERA_DIGITAL', 2, 'ACTIVO'),
(21, 7, 'BILLETERA_DIGITAL', 3, 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_movimientos`
--

CREATE TABLE `caja_movimientos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_caja` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `tipo` varchar(10) NOT NULL COMMENT 'INGRESO|EGRESO',
  `categoria` varchar(30) NOT NULL COMMENT 'VENTA|COMPRA|GASTO_OP|REPOSICION|RENDICION|AJUSTE|APERTURA|MANUAL',
  `descripcion` varchar(245) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL,
  `instrumento_tipo` varchar(30) DEFAULT NULL,
  `instrumento_id` int(10) UNSIGNED DEFAULT NULL,
  `referencia` varchar(60) DEFAULT NULL,
  `saldo_anterior` decimal(14,2) NOT NULL DEFAULT 0.00,
  `saldo_posterior` decimal(14,2) NOT NULL DEFAULT 0.00,
  `origen_tipo` varchar(50) DEFAULT NULL,
  `origen_id` int(10) UNSIGNED DEFAULT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `estado` varchar(15) NOT NULL DEFAULT 'CONFIRMADO' COMMENT 'CONFIRMADO|ANULADO',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `caja_movimientos`
--

INSERT INTO `caja_movimientos` (`id`, `id_caja`, `fecha`, `tipo`, `categoria`, `descripcion`, `monto`, `instrumento_tipo`, `instrumento_id`, `referencia`, `saldo_anterior`, `saldo_posterior`, `origen_tipo`, `origen_id`, `id_usuario`, `estado`, `created_at`) VALUES
(1, 1, '2025-07-16', 'INGRESO', 'MANUAL', 'MIERCOLES 16 JULIO', 4800.00, 'EFECTIVO', NULL, NULL, 0.00, 4800.00, NULL, NULL, 1, 'CONFIRMADO', '2026-07-09 23:59:36'),
(2, 3, '2026-07-10', 'INGRESO', 'APERTURA', 'Apertura de caja (fondo inicial)', 600.00, 'EFECTIVO', NULL, NULL, 0.00, 600.00, 'APERTURA', 1, 108, 'CONFIRMADO', '2026-07-10 08:36:34'),
(3, 3, '2026-07-10', 'INGRESO', 'VENTA', 'Cobro venta NV01-00002976', 960.00, 'TRANSFERENCIA', 3, 'adadadadawdaw', 600.00, 1560.00, 'Venta', 263, 108, 'CONFIRMADO', '2026-07-10 09:12:24'),
(4, 3, '2026-07-10', 'INGRESO', 'COBRO', 'Cobro B001-00000620', 100.00, 'EFECTIVO', NULL, NULL, 1560.00, 1660.00, 'DiasVenta', 414, 108, 'CONFIRMADO', '2026-07-10 09:48:06'),
(5, 4, '2026-07-10', 'INGRESO', 'COBRO', 'Cobro B001-00000620', 800.00, 'EFECTIVO', NULL, NULL, 0.00, 800.00, 'DiasVenta', 414, 107, 'CONFIRMADO', '2026-07-10 18:56:20'),
(6, 4, '2026-07-10', 'INGRESO', 'COBRO', 'Cobro B001-00000620', 900.00, 'EFECTIVO', NULL, NULL, 800.00, 1700.00, 'DiasVenta', 415, 107, 'CONFIRMADO', '2026-07-10 18:56:40'),
(7, 5, '2026-07-10', 'INGRESO', 'APERTURA', 'Apertura de caja (fondo inicial)', 3000.00, 'EFECTIVO', NULL, NULL, 0.00, 3000.00, 'APERTURA', 2, 107, 'CONFIRMADO', '2026-07-10 19:06:38'),
(8, 5, '2026-07-10', 'EGRESO', 'COMPRA', 'Pago compra F001-8875541', 136800.00, 'EFECTIVO', NULL, 'Compra #163', 3000.00, -133800.00, 'Compra', 163, 107, 'CONFIRMADO', '2026-07-10 21:08:16'),
(10, 4, '2026-07-14', 'EGRESO', 'COMPRA', 'Pago compra 01-352', 1.00, 'EFECTIVO', NULL, NULL, 1700.00, 1699.00, NULL, NULL, 107, 'CONFIRMADO', '2026-07-15 01:39:30'),
(11, 4, '2026-07-15', 'EGRESO', 'COMPRA', 'Pago compra 01-352', 1.00, 'EFECTIVO', NULL, NULL, 1699.00, 1698.00, NULL, NULL, 107, 'CONFIRMADO', '2026-07-15 23:29:16'),
(12, 5, '2026-07-22', 'INGRESO', 'VENTA', 'Cobro venta B001-00000622', 157.00, 'EFECTIVO', NULL, NULL, -133800.00, -133643.00, 'Venta', 264, 107, 'CONFIRMADO', '2026-07-22 19:26:12'),
(13, 5, '2026-07-22', 'INGRESO', 'COBRO', 'Cobro NV01-00002979', 22.50, 'EFECTIVO', NULL, NULL, -133643.00, -133620.50, 'DiasVenta', 416, 107, 'CONFIRMADO', '2026-07-22 20:06:55'),
(14, 5, '2026-07-22', 'INGRESO', 'VENTA', 'Cobro venta NV01-00002980', 45.00, 'EFECTIVO', NULL, NULL, -133620.50, -133575.50, 'Venta', 266, 107, 'CONFIRMADO', '2026-07-22 20:19:16'),
(15, 5, '2026-07-22', 'INGRESO', 'VENTA', 'Cobro venta B001-00000623', 45.00, 'EFECTIVO', NULL, NULL, -133575.50, -133530.50, 'Venta', 267, 107, 'CONFIRMADO', '2026-07-22 20:20:01'),
(16, 5, '2026-07-27', 'INGRESO', 'APERTURA', 'Apertura de caja (fondo inicial)', 133.53, 'EFECTIVO', NULL, NULL, -133530.50, -133396.97, 'APERTURA', 3, 107, 'CONFIRMADO', '2026-07-28 00:28:01'),
(17, 5, '2026-07-27', 'INGRESO', 'MANUAL', 'adadawd', 133.53, 'EFECTIVO', NULL, NULL, -133396.97, -133263.44, NULL, NULL, 107, 'CONFIRMADO', '2026-07-28 00:28:19'),
(18, 5, '2026-07-27', 'INGRESO', 'MANUAL', '133,263.44', 133.26, 'EFECTIVO', NULL, NULL, -133263.44, -133130.18, NULL, NULL, 107, 'CONFIRMADO', '2026-07-28 00:28:54'),
(19, 5, '2026-07-27', 'INGRESO', 'MANUAL', 'ad ada dadw', 200000.00, 'EFECTIVO', NULL, NULL, -133130.18, 66869.82, NULL, NULL, 107, 'CONFIRMADO', '2026-07-28 00:29:17'),
(20, 5, '2026-07-27', 'EGRESO', 'COMPRA', 'Pago compra F001-33651', 100.00, 'EFECTIVO', NULL, 'Compra #166', 66869.82, 66769.82, 'Compra', 166, 107, 'CONFIRMADO', '2026-07-28 03:10:28'),
(21, 3, '2026-07-27', 'EGRESO', 'AJUSTE', 'Ajuste por faltante en cierre #3', 500.00, NULL, NULL, NULL, 1660.00, 1160.00, 'CIERRE', 3, 107, 'CONFIRMADO', '2026-07-28 03:45:55'),
(22, 1, '2026-07-27', 'EGRESO', 'TRANSFERENCIA', 'Asignación de fondo a CAJA VICTOR (PRUEBA) (asignación #1)', 500.00, 'EFECTIVO', NULL, NULL, 4800.00, 4300.00, 'TRANSFERENCIA_FONDO', 1, 107, 'CONFIRMADO', '2026-07-28 03:46:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`, `id_empresa`, `estado`) VALUES
(1, 'mmm', NULL, 12, '1'),
(2, 'Fideos y Pastas', 'Pastas largas, cortas, instantáneas y especiales', 12, '1'),
(3, 'Salsas y Conservas', 'Salsas de tomate, conservas vegetales y de pescado', 12, '1'),
(4, 'Aceites y Margarinas', 'Aceites comestibles y margarinas para cocina', 12, '1'),
(5, 'Harinas y Panificación', 'Harinas de trigo y pre-mezclas para panadería', 12, '1'),
(6, 'Galletas y Snacks', 'Galletas dulces, saladas y snacks variados', 12, '1'),
(7, 'Bebidas y Jugos', 'Néctares, jugos en polvo y bebidas hidratantes', 12, '1'),
(8, 'Limpieza e Higiene', 'Jabones, detergentes y productos de limpieza', 12, '1'),
(9, 'Arroz y Abarrotes', 'Arroz, azúcar, legumbres y abarrotes en general', 12, '1'),
(10, 'FIDEOS', 'AAAAAAAAAA', 0, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierre_caja`
--

CREATE TABLE `cierre_caja` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_caja` int(10) UNSIGNED NOT NULL,
  `id_apertura` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha` date NOT NULL,
  `saldo_declarado` decimal(14,2) NOT NULL,
  `saldo_sistema` decimal(14,2) NOT NULL,
  `desglose_instrumentos` text DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'PENDIENTE',
  `id_usuario_cierra` int(10) UNSIGNED NOT NULL,
  `id_usuario_aprueba` int(10) UNSIGNED DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `cierre_caja`
--

INSERT INTO `cierre_caja` (`id`, `id_caja`, `id_apertura`, `fecha`, `saldo_declarado`, `saldo_sistema`, `desglose_instrumentos`, `estado`, `id_usuario_cierra`, `id_usuario_aprueba`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 5, 2, '2026-07-10', 0.00, -133800.00, '[]', 'PENDIENTE', 107, NULL, NULL, '2026-07-10 21:11:54', '2026-07-27 23:24:07'),
(2, 5, 3, '2026-07-27', 40000.00, 200300.32, '[{\"tipo\":\"BILLETE\",\"denominacion\":200,\"cantidad\":200,\"subtotal\":40000}]', 'PENDIENTE', 107, NULL, NULL, '2026-07-28 03:25:46', '2026-07-28 03:25:46'),
(3, 3, 1, '2026-07-27', 1160.00, 1660.00, '[{\"tipo\":\"BILLETE\",\"denominacion\":200,\"cantidad\":1,\"subtotal\":200}]', 'APROBADO', 111, 107, NULL, '2026-07-28 03:45:15', '2026-07-28 03:45:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `documento` varchar(11) DEFAULT NULL,
  `datos` varchar(245) DEFAULT NULL,
  `direccion` varchar(245) DEFAULT NULL,
  `distrito` varchar(220) DEFAULT NULL,
  `ubigeo` char(6) DEFAULT NULL,
  `telefono` varchar(200) DEFAULT NULL,
  `dias_visitas` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `ultima_venta` date DEFAULT NULL,
  `total_venta` double(8,2) DEFAULT NULL,
  `id_ruta` int(11) DEFAULT NULL,
  `mercado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `documento`, `datos`, `direccion`, `distrito`, `ubigeo`, `telefono`, `dias_visitas`, `email`, `id_empresa`, `ultima_venta`, `total_venta`, `id_ruta`, `mercado`) VALUES
(2512, '06745308', 'VICTORIA NUÑEZ ABREGU', 'MDO V. CARMEN#26 COMIDAS', 'Breña', NULL, '936480104', 'Martes', '', 12, '1000-01-01', 0.00, 3, 4),
(2513, '07134105', 'MAITE ELIZABETH (MERCADO 7 DE ABRIL) PUESTO 33 PIñATERíA', 'MERCADO 7 DE ABRIL PUESTO 33 (PIñATERíA)', '', NULL, '945160416', 'Jueves', '', 12, '1000-01-01', 0.00, 1, 0),
(2514, 'Miguel Ánge', 'D-MIGUEL ANGEL SILVA ROQUE(GAMBETA BAJA)', 'MDO GAMBETA BAJA PTO 57 CEVICHERIA', NULL, NULL, NULL, NULL, NULL, 12, NULL, NULL, NULL, NULL),
(2515, 'Cardena', 'D-MARIA CARDENAS (GAMBETA)', 'MDO GAMBETA PTO 135', NULL, NULL, NULL, NULL, NULL, 12, NULL, NULL, NULL, NULL),
(2516, '77425200', 'EMER RODRIGO YARLEQUE ZAPATA', 'sdcsdcsd', 'SAN MARTIN DE PORRES', NULL, '92670321', NULL, 'adadadaw@gmail.com', 12, '2026-07-22', 4376.00, NULL, 24),
(2517, '76165962', 'VICTOR RAUL CANCHARI RIQUI', 'PSJ.INCA ROCA MZ. 131 LT.33', 'indepencia', NULL, '92670321', NULL, 'vcanchari38@gmail.com', 12, '2026-07-09', 125.00, NULL, 4),
(2518, '99550001', 'CLIENTE TEST DESPACHO 01', 'PUESTO 01 - MERCADO TEST', 'dfgbbd', NULL, '999000001', NULL, 'adan2025zapata@gmail.com', 12, NULL, NULL, NULL, 23),
(2519, '99550002', 'CLIENTE TEST DESPACHO 02', 'PUESTO 02 - MERCADO TEST', 'dfgbbd', NULL, '999000002', NULL, 'adan2025zapata@gmail.com', 12, NULL, NULL, NULL, 23),
(2520, '99550003', 'CLIENTE TEST DESPACHO 03', 'PUESTO 03 - MERCADO TEST', NULL, NULL, '999000003', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2521, '99550004', 'CLIENTE TEST DESPACHO 04', 'PUESTO 04 - MERCADO TEST', NULL, NULL, '999000004', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2522, '99550005', 'CLIENTE TEST DESPACHO 05', 'PUESTO 05 - MERCADO TEST', NULL, NULL, '999000005', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2523, '99550006', 'CLIENTE TEST DESPACHO 06', 'PUESTO 06 - MERCADO TEST', NULL, NULL, '999000006', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2524, '99550007', 'CLIENTE TEST DESPACHO 07', 'PUESTO 07 - MERCADO TEST', NULL, NULL, '999000007', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2525, '99550008', 'CLIENTE TEST DESPACHO 08', 'PUESTO 08 - MERCADO TEST', NULL, NULL, '999000008', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2526, '99550009', 'CLIENTE TEST DESPACHO 09', 'PUESTO 09 - MERCADO TEST', NULL, NULL, '999000009', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2527, '99550010', 'CLIENTE TEST DESPACHO 10', 'PUESTO 10 - MERCADO TEST', NULL, NULL, '999000010', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2528, '99550011', 'CLIENTE TEST DESPACHO 11', 'PUESTO 11 - MERCADO TEST', NULL, NULL, '999000011', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2529, '99550012', 'CLIENTE TEST DESPACHO 12', 'PUESTO 12 - MERCADO TEST', NULL, NULL, '999000012', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2530, '99550013', 'CLIENTE TEST DESPACHO 13', 'PUESTO 13 - MERCADO TEST', NULL, NULL, '999000013', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2531, '99550014', 'CLIENTE TEST DESPACHO 14', 'PUESTO 14 - MERCADO TEST', NULL, NULL, '999000014', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2532, '99550015', 'CLIENTE TEST DESPACHO 15', 'PUESTO 15 - MERCADO TEST', NULL, NULL, '999000015', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2533, '99550016', 'CLIENTE TEST DESPACHO 16', 'PUESTO 16 - MERCADO TEST', NULL, NULL, '999000016', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2534, '99550017', 'CLIENTE TEST DESPACHO 17', 'PUESTO 17 - MERCADO TEST', NULL, NULL, '999000017', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2535, '99550018', 'CLIENTE TEST DESPACHO 18', 'PUESTO 18 - MERCADO TEST', NULL, NULL, '999000018', NULL, NULL, 12, NULL, NULL, NULL, 23),
(2536, '99550019', 'CLIENTE TEST DESPACHO 19', 'PUESTO 19 - MERCADO TEST', NULL, NULL, '999000019', NULL, NULL, 12, '2026-07-10', 13.00, NULL, 23),
(2537, '99550020', 'CLIENTE TEST DESPACHO 20', 'PUESTO 20 - MERCADO TEST', NULL, NULL, '999000020', NULL, NULL, 12, '2026-07-10', 756.00, NULL, 23),
(2538, '76165963', 'YORCHS BRAULIO CANCHARI RIQUI', 'PSJ.INCA ROCA MZ. 131 LT.33', 'indepencia', NULL, '92670321', NULL, 'FABIO@gmail.com', 12, '2026-07-10', 1292.00, NULL, 24),
(2539, '44925551', 'JANETT NILDA RIQUI PIÑAS', 'PSJ.INCA ROCA MZ. 131 LT.33', 'INDEPENDENCIA', '150112', '92670321', NULL, 'vcanchari38@gmail.com', 12, NULL, NULL, NULL, NULL),
(2540, '20612637441', 'INVERSIONES EL JHORCH S.A.C.', 'AV. METROPOLITANA 2450 INT NRO. 566 URB. SANTA LUZMILA LIMA LIMA COMAS, COMAS, LIMA, LIMA', 'COMAS', '150110', '925467771', NULL, NULL, 0, NULL, NULL, NULL, 25);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_venta`
--

CREATE TABLE `cliente_venta` (
  `id_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL,
  `id_tido` int(11) DEFAULT NULL,
  `id_tipo_pago` int(11) DEFAULT NULL,
  `instrumento_tipo` varchar(30) DEFAULT NULL COMMENT 'EFECTIVO | CUENTA_BANCARIA | TARJETA | BILLETERA_DIGITAL',
  `instrumento_id` int(10) UNSIGNED DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `fecha_emision` varchar(50) DEFAULT NULL,
  `fecha_vencimiento` varchar(50) DEFAULT NULL,
  `dias_pagos` varchar(100) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `serie` varchar(50) DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `total` varchar(50) DEFAULT NULL,
  `recepcionado` tinyint(4) NOT NULL DEFAULT 0,
  `id_empresa` int(11) DEFAULT NULL,
  `moneda` char(1) DEFAULT NULL,
  `sucursal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id_compra`, `id_tido`, `id_tipo_pago`, `instrumento_tipo`, `instrumento_id`, `id_proveedor`, `fecha_emision`, `fecha_vencimiento`, `dias_pagos`, `direccion`, `serie`, `numero`, `total`, `recepcionado`, `id_empresa`, `moneda`, `sucursal`) VALUES
(111, 12, 1, NULL, NULL, 181, '2026-02-18', '2026-02-18', NULL, '-', '01', '352', '6400', 1, 12, '1', 1),
(118, 2, 1, NULL, NULL, 181, '2026-03-23', '2026-03-23', NULL, '-', '01', '2352', '9820', 1, 12, '1', 1),
(134, 12, 1, NULL, NULL, 182, '2026-04-21', '2026-04-21', NULL, '-', '01', '5352', '8250', 1, 12, '1', 1),
(142, 12, 2, NULL, NULL, 181, '2026-04-27', '2026-04-28', NULL, '-', '01', '352', '3100', 1, 12, '1', 1),
(155, 12, 2, NULL, NULL, 183, '2026-04-01', '2026-04-02', NULL, '-', '01', '352', '54740', 1, 12, '1', 1),
(157, 2, 1, NULL, NULL, 184, '2026-04-27', '2026-04-30', NULL, '-', 'fa01', '194', '31000', 1, 12, '1', 1),
(162, 2, 1, 'EFECTIVO', NULL, 181, '2026-07-10 00:00:00', '2026-07-10 00:00:00', NULL, 'Convertido de cotización N° 2953', 'F001', '665412', '2800', 1, 12, 'S', 1),
(163, 2, 1, 'EFECTIVO', NULL, 182, '2026-07-10 00:00:00', '2026-07-10 00:00:00', NULL, 'Convertido de cotización N° 2953', 'F001', '8875541', '136800', 1, 12, 'S', 1),
(164, 2, 1, 'BILLETERA_DIGITAL', 2, 181, '2026-07-19 00:00:00', '2026-07-19 00:00:00', NULL, 'aadadadawd', 'F001', '3322', '35', 0, 12, 'S', 1),
(165, 2, 2, NULL, NULL, 181, '2026-07-22 00:00:00', '2026-07-22 00:00:00', NULL, 'adadawd', 'F001', '22541', '3500', 2, 12, 'S', 1),
(166, 2, 1, 'EFECTIVO', NULL, 181, '2026-07-27 00:00:00', '2026-07-27 00:00:00', NULL, 'Convertido de cotización N° 2953', 'F001', '33651', '100', 1, 12, 'S', 1),
(167, 2, 1, 'TRANSFERENCIA', 5, 188, '2026-09-08 00:00:00', '2026-09-08 00:00:00', NULL, 'sfsefsfse', 'F001', '01115591', '25.61', 0, 0, 'S', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones`
--

CREATE TABLE `cotizaciones` (
  `cotizacion_id` int(11) NOT NULL,
  `numero` int(11) DEFAULT NULL,
  `id_tido` int(11) NOT NULL,
  `id_tipo_pago` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `dias_pagos` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `direccion` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `id_cliente` int(11) NOT NULL,
  `total` double(10,2) DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `sucursal` int(11) DEFAULT NULL,
  `usar_precio` int(11) DEFAULT NULL,
  `moneda` int(11) DEFAULT 1,
  `cm_tc` varchar(100) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `observacion` varchar(225) DEFAULT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `cotizaciones`
--

INSERT INTO `cotizaciones` (`cotizacion_id`, `numero`, `id_tido`, `id_tipo_pago`, `fecha`, `dias_pagos`, `direccion`, `id_cliente`, `total`, `estado`, `id_empresa`, `sucursal`, `usar_precio`, `moneda`, `cm_tc`, `id_usuario`, `observacion`, `id_venta`, `fecha_registro`) VALUES
(6193, 4479, 6, 2, '2025-03-28', '', '1', 1526, 66.50, '0', 12, 1, 5, 1, NULL, 63, '', NULL, '2025-03-28 12:28:47'),
(6194, 4480, 6, 2, '2025-03-28', '', '1', 2129, 14.00, '0', 12, 1, 5, 1, NULL, 63, '', NULL, '2025-03-28 12:30:51'),
(6195, 4481, 6, 2, '2025-03-28', '', '1', 2114, 464.00, '0', 12, 1, 1, 1, NULL, 62, '', NULL, '2025-03-28 12:40:56'),
(6196, 4482, 6, 2, '2025-03-28', '', '1', 1521, 196.00, '0', 12, 1, 1, 1, NULL, 63, '', NULL, '2025-03-28 12:42:33'),
(6197, 4483, 6, 2, '2025-03-28', '', '1', 2113, 124.20, '0', 12, 1, 1, 1, NULL, 62, '', NULL, '2025-03-28 12:48:25'),
(51470, 2947, 1, 1, '2026-07-08', NULL, NULL, 2516, 125.00, '3', 12, 1, 1, 1, NULL, 107, NULL, 239, '2026-07-08 12:31:57'),
(51471, 2948, 6, 1, '2026-07-09', NULL, '1adadawda', 2517, 125.00, '3', 12, 1, 1, 1, NULL, 107, 'Convertido de cotización N° 2953', 238, '2026-07-09 14:53:10'),
(51472, 2950, 6, 1, '2026-07-10', NULL, '1', 2518, 84.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 242, '2026-07-10 01:43:05'),
(51473, 2951, 6, 1, '2026-07-10', NULL, '1', 2519, 649.50, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 243, '2026-07-10 01:43:05'),
(51474, 2952, 6, 1, '2026-07-10', NULL, '1', 2520, 2109.50, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 244, '2026-07-10 01:43:05'),
(51475, 2953, 6, 1, '2026-07-10', NULL, '1', 2521, 32.50, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 245, '2026-07-10 01:43:05'),
(51476, 2954, 6, 1, '2026-07-10', NULL, '1', 2522, 1386.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 246, '2026-07-10 01:43:05'),
(51477, 2955, 6, 1, '2026-07-10', NULL, '1', 2523, 923.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 247, '2026-07-10 01:43:05'),
(51478, 2956, 6, 1, '2026-07-10', NULL, '1', 2524, 196.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 248, '2026-07-10 01:43:05'),
(51479, 2957, 6, 1, '2026-07-10', NULL, '1', 2525, 883.50, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 249, '2026-07-10 01:43:05'),
(51480, 2958, 6, 1, '2026-07-10', NULL, '1', 2526, 1031.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 250, '2026-07-10 01:43:05'),
(51481, 2959, 6, 1, '2026-07-10', NULL, '1', 2527, 336.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 251, '2026-07-10 01:43:05'),
(51482, 2960, 6, 1, '2026-07-10', NULL, '1', 2528, 273.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 252, '2026-07-10 01:43:05'),
(51483, 2961, 6, 1, '2026-07-10', NULL, '1', 2529, 835.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 253, '2026-07-10 01:43:05'),
(51484, 2962, 6, 1, '2026-07-10', NULL, '1', 2530, 480.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 254, '2026-07-10 01:43:05'),
(51485, 2963, 6, 1, '2026-07-10', NULL, '1', 2531, 1551.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 255, '2026-07-10 01:43:05'),
(51486, 2964, 6, 1, '2026-07-10', NULL, '1', 2532, 1694.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 256, '2026-07-10 01:43:05'),
(51487, 2965, 6, 1, '2026-07-10', NULL, '1', 2533, 336.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 257, '2026-07-10 01:43:05'),
(51488, 2966, 6, 1, '2026-07-10', NULL, '1', 2534, 508.50, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 258, '2026-07-10 01:43:05'),
(51489, 2967, 6, 1, '2026-07-10', NULL, '1', 2535, 2162.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 259, '2026-07-10 01:43:05'),
(51490, 2968, 6, 1, '2026-07-10', NULL, '1', 2536, 13.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 241, '2026-07-10 01:43:05'),
(51491, 2969, 6, 1, '2026-07-10', NULL, '1', 2537, 756.00, '3', 12, 1, 1, 1, NULL, 108, 'PEDIDO TEST DESPACHO', 240, '2026-07-10 01:43:05'),
(51492, 2972, 6, 1, '2026-07-10', NULL, 'PSJ.INCA ROCA MZ. 131 LT.33', 2538, 1292.00, '3', 12, 1, 1, 1, NULL, 108, NULL, 260, '2026-07-10 04:31:37'),
(51493, 2973, 6, 2, '2026-07-10', NULL, 'sdcsdcsd', 2516, 1800.00, '3', 12, 1, 1, 1, NULL, 108, 'aaaaa', 261, '2026-07-10 04:53:10'),
(51494, 2974, 6, 1, '2026-07-10', NULL, 'sdcsdcsd', 2516, 1250.00, '3', 12, 1, 1, 1, NULL, 108, 'adadadadawd', 262, '2026-07-10 05:02:34'),
(51495, 2975, 6, 1, '2026-07-10', NULL, 'sdcsdcsd', 2516, 960.00, '3', 12, 1, 1, 1, NULL, 108, NULL, 263, '2026-07-10 05:11:27'),
(51496, 2977, 6, 1, '2026-07-21', NULL, 'sdcsdcsd', 2516, 45.00, '3', 12, 1, 1, 1, NULL, 107, 'sdfcvsfvc', 266, '2026-07-21 13:58:03'),
(51497, 2978, 6, 1, '2026-07-22', NULL, 'sdcsdcsd', 2516, 45.00, '3', 12, 1, 1, 1, NULL, 107, NULL, 265, '2026-07-22 16:03:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_bancarias`
--

CREATE TABLE `cuentas_bancarias` (
  `id_cuenta` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `id_banco` int(10) UNSIGNED NOT NULL,
  `tipo_cuenta` enum('CC','CA','CTS','AHORRO') NOT NULL DEFAULT 'CC',
  `numero_cuenta` varchar(30) DEFAULT NULL,
  `cci` varchar(30) DEFAULT NULL,
  `moneda` enum('PEN','USD') NOT NULL DEFAULT 'PEN',
  `saldo_inicial` decimal(14,2) NOT NULL DEFAULT 0.00,
  `fecha_corte` date DEFAULT NULL,
  `titular` varchar(200) NOT NULL,
  `estado` varchar(2) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `cuentas_bancarias`
--

INSERT INTO `cuentas_bancarias` (`id_cuenta`, `id_empresa`, `id_banco`, `tipo_cuenta`, `numero_cuenta`, `cci`, `moneda`, `saldo_inicial`, `fecha_corte`, `titular`, `estado`, `created_at`, `updated_at`) VALUES
(1, 12, 1, 'AHORRO', '66541223151', '00219300665412231512', 'PEN', 0.00, NULL, 'Víctor Raúl Canchari', '1', '2026-07-10 05:58:03', '2026-07-31 05:33:12'),
(2, 12, 1, 'CC', '193-2255887-0-11', '00219300225588701128', 'PEN', 0.00, NULL, 'ROMA DISTRIBUCIONES & SERVICIOS GENERALES S.A.C.', '1', '2026-07-10 05:58:03', '2026-07-10 05:58:03'),
(3, 12, 2, 'CC', '0011-0057-0200334455', '01105700020033445529', 'PEN', 0.00, NULL, 'ROMA DISTRIBUCIONES & SERVICIOS GENERALES S.A.C.', '1', '2026-07-10 05:58:03', '2026-07-10 05:58:03'),
(4, 12, 3, 'CC', '44565552214411', 'adad adadad', 'PEN', 0.00, NULL, 'victor', '1', '2026-07-22 19:29:21', '2026-07-22 19:29:51'),
(5, 0, 4, 'CC', '12345891212313', '12312314335353', 'PEN', 0.00, '2027-01-01', 'roma', '1', '2026-09-08 20:40:32', '2026-09-08 20:40:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuotas_cotizacion`
--

CREATE TABLE `cuotas_cotizacion` (
  `cuota_coti_id` int(11) NOT NULL,
  `id_coti` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_caja_empresa` int(11) DEFAULT NULL,
  `monto` double(10,3) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` char(1) DEFAULT '0',
  `tipo_pago` varchar(200) DEFAULT NULL,
  `fecha_pago_real` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `cuotas_cotizacion`
--

INSERT INTO `cuotas_cotizacion` (`cuota_coti_id`, `id_coti`, `id_usuario`, `id_caja_empresa`, `monto`, `fecha`, `estado`, `tipo_pago`, `fecha_pago_real`) VALUES
(26878, 6193, NULL, NULL, 66.500, '2025-04-03', '1', 'Efectivo', '2025-04-03 12:00:00'),
(236734, 51493, 108, NULL, 900.000, '2026-08-09', '0', 'EFECTIVO', NULL),
(236735, 51493, 108, NULL, 900.000, '2026-09-08', '0', 'EFECTIVO', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cxc_abonos`
--

CREATE TABLE `cxc_abonos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_dias_venta` int(10) UNSIGNED NOT NULL,
  `id_venta` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `metodo_pago` varchar(20) NOT NULL DEFAULT 'EFECTIVO',
  `referencia` varchar(60) DEFAULT NULL,
  `id_movimiento_caja` int(10) UNSIGNED DEFAULT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `estado` varchar(10) NOT NULL DEFAULT 'ACTIVO',
  `motivo_anulacion` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `cxc_abonos`
--

INSERT INTO `cxc_abonos` (`id`, `id_dias_venta`, `id_venta`, `fecha`, `monto`, `metodo_pago`, `referencia`, `id_movimiento_caja`, `id_usuario`, `estado`, `motivo_anulacion`, `created_at`, `updated_at`) VALUES
(1, 410, 229, '2026-03-28', 750.00, 'PLIN', NULL, NULL, 40, 'ACTIVO', NULL, '2026-07-10 09:34:11', '2026-07-10 09:34:11'),
(2, 411, 229, '2026-03-28', 750.00, 'YAPE', NULL, NULL, 40, 'ACTIVO', NULL, '2026-07-10 09:34:11', '2026-07-10 09:34:11'),
(3, 413, 231, '2026-04-16', 340.00, 'EFECTIVO', NULL, NULL, 40, 'ACTIVO', NULL, '2026-07-10 09:34:11', '2026-07-10 09:34:11'),
(4, 414, 261, '2026-07-10', 100.00, 'EFECTIVO', NULL, 4, 108, 'ACTIVO', NULL, '2026-07-10 09:48:06', '2026-07-10 09:48:06'),
(5, 414, 261, '2026-07-10', 800.00, 'EFECTIVO', NULL, 5, 107, 'ACTIVO', NULL, '2026-07-10 18:56:20', '2026-07-10 18:56:20'),
(6, 415, 261, '2026-07-10', 900.00, 'EFECTIVO', NULL, 6, 107, 'ACTIVO', NULL, '2026-07-10 18:56:40', '2026-07-10 18:56:40'),
(7, 416, 265, '2026-07-22', 22.50, 'EFECTIVO', NULL, 13, 107, 'ACTIVO', NULL, '2026-07-22 20:06:55', '2026-07-22 20:06:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devoluciones_nv`
--

CREATE TABLE `devoluciones_nv` (
  `id_devolucion` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `cantidad` double(6,2) NOT NULL,
  `presenta` varchar(100) DEFAULT NULL,
  `presenta_cnt` int(11) DEFAULT NULL,
  `signo` char(1) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `devoluciones_nv`
--

INSERT INTO `devoluciones_nv` (`id_devolucion`, `id_venta`, `id_producto`, `id_usuario`, `cantidad`, `presenta`, `presenta_cnt`, `signo`, `fecha`) VALUES
(3, 229, 412, 40, 10.00, '4', 1, '+', '2026-04-08 00:26:39'),
(158, 231, 367, 40, 10.00, '1', 10, '+', '2026-04-17 00:42:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dias_compras`
--

CREATE TABLE `dias_compras` (
  `dias_compra_id` int(11) NOT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `monto` double(10,3) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  `id_caja` int(11) DEFAULT NULL,
  `instrumento_tipo` varchar(30) DEFAULT NULL,
  `instrumento_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `dias_compras`
--

INSERT INTO `dias_compras` (`dias_compra_id`, `id_compra`, `monto`, `fecha`, `estado`, `id_caja`, `instrumento_tipo`, `instrumento_id`) VALUES
(75, 142, 3100.000, '2026-04-28', '0', NULL, NULL, NULL),
(90, 155, 25507.400, '2026-04-02', '1', NULL, NULL, NULL),
(91, 155, 10000.000, '2026-04-02', '1', NULL, NULL, NULL),
(92, 155, 19232.600, '2026-04-02', '0', NULL, NULL, NULL),
(93, 155, 1.000, '2026-07-14', '1', 4, 'EFECTIVO', NULL),
(94, 142, 1.000, '2026-07-15', '1', 4, 'EFECTIVO', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dias_ventas`
--

CREATE TABLE `dias_ventas` (
  `dias_venta_id` int(11) NOT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_caja_empresa` int(11) DEFAULT NULL,
  `monto` double(10,3) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` char(1) DEFAULT '0',
  `tipo_pago` varchar(200) DEFAULT NULL,
  `referencia` varchar(60) DEFAULT NULL,
  `voucher` varchar(255) DEFAULT NULL,
  `fecha_pago_real` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `dias_ventas`
--

INSERT INTO `dias_ventas` (`dias_venta_id`, `id_venta`, `id_usuario`, `id_caja_empresa`, `monto`, `fecha`, `estado`, `tipo_pago`, `referencia`, `voucher`, `fecha_pago_real`) VALUES
(410, 229, NULL, NULL, 750.000, '2026-03-28', '1', 'PLIN', NULL, NULL, NULL),
(411, 229, NULL, NULL, 750.000, '2026-03-28', '1', 'YAPE', NULL, NULL, NULL),
(413, 231, NULL, NULL, 340.000, '2026-04-16', '1', 'EFECTIVO', NULL, NULL, NULL),
(414, 261, 107, NULL, 900.000, '2026-08-09', '1', 'EFECTIVO', NULL, NULL, '2026-07-10 00:00:00'),
(415, 261, 107, NULL, 900.000, '2026-09-08', '1', 'EFECTIVO', NULL, NULL, '2026-07-10 00:00:00'),
(416, 265, 107, NULL, 22.500, '2026-08-21', '1', 'EFECTIVO', NULL, NULL, '2026-07-22 00:00:00'),
(417, 265, 107, NULL, 22.500, '2026-09-20', '0', 'BILLETERA|3', '456456654', 'vouchers/01KY5967QT0APNK5MTS3TBX4FF.jpg', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_empresas`
--

CREATE TABLE `documentos_empresas` (
  `id_empresa` int(11) NOT NULL,
  `id_tido` int(11) NOT NULL,
  `sucursal` int(11) DEFAULT NULL,
  `serie` varchar(4) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `documentos_empresas`
--

INSERT INTO `documentos_empresas` (`id_empresa`, `id_tido`, `sucursal`, `serie`, `numero`) VALUES
(12, 1, 1, 'B001', 623),
(12, 2, 1, 'F001', 2358),
(12, 3, 1, 'F001', 7),
(12, 4, 1, 'F001', 1),
(12, 6, 1, 'NV01', 2980),
(12, 11, 1, 'T001', 1031),
(12, 1, 2, 'B002', 605),
(12, 2, 2, 'F002', 2359),
(12, 3, 2, 'F002', 6),
(12, 4, 2, 'F002', 1),
(12, 6, 2, 'NV02', 2960),
(12, 11, 2, 'T002', 1025);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_sunat`
--

CREATE TABLE `documentos_sunat` (
  `id_tido` int(11) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  `cod_sunat` varchar(2) DEFAULT NULL,
  `abreviatura` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `documentos_sunat`
--

INSERT INTO `documentos_sunat` (`id_tido`, `nombre`, `cod_sunat`, `abreviatura`) VALUES
(1, 'BOLETA DE VENTA', '03', 'BT'),
(2, 'FACTURA', '01', 'FT'),
(3, 'NOTA DE CREDITO', '07', 'NC'),
(4, 'NOTA DE DEBITO', '08', 'ND'),
(5, 'NOTA DE RECEPCION', '09', 'GR'),
(6, 'NOTA DE VENTA', '00', 'NV'),
(7, 'NOTA DE SEPARACION', '00', 'NS'),
(8, 'NOTA DE TRASLADO', '00', 'NT'),
(9, 'NOTA DE INVENTARIO', '00', 'NIV'),
(10, 'NOTA DE INGRESO', '00', 'NIG'),
(11, 'GUIA DE REMISION', '09', 'GR'),
(12, 'NOTA DE COMPRA', '00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id_empresa` int(11) NOT NULL,
  `ruc` varchar(11) DEFAULT NULL,
  `razon_social` varchar(245) DEFAULT NULL,
  `comercial` varchar(245) NOT NULL,
  `cod_sucursal` varchar(4) DEFAULT NULL,
  `direccion` varchar(245) DEFAULT NULL,
  `email` varchar(145) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `user_sol` varchar(45) DEFAULT NULL,
  `clave_sol` varchar(45) DEFAULT NULL,
  `gre_client_id` varchar(255) DEFAULT NULL,
  `gre_client_secret` varchar(255) DEFAULT NULL,
  `logo` varchar(200) DEFAULT NULL,
  `ubigeo` varchar(6) DEFAULT NULL,
  `distrito` varchar(45) DEFAULT NULL,
  `provincia` varchar(45) DEFAULT NULL,
  `departamento` varchar(45) DEFAULT NULL,
  `tipo_impresion` char(1) DEFAULT NULL,
  `modo` varchar(50) DEFAULT NULL,
  `igv` double(10,2) DEFAULT 0.18,
  `propaganda` varchar(250) DEFAULT NULL,
  `telefono2` varchar(30) DEFAULT NULL,
  `telefono3` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `ruc`, `razon_social`, `comercial`, `cod_sucursal`, `direccion`, `email`, `telefono`, `estado`, `password`, `user_sol`, `clave_sol`, `gre_client_id`, `gre_client_secret`, `logo`, `ubigeo`, `distrito`, `provincia`, `departamento`, `tipo_impresion`, `modo`, `igv`, `propaganda`, `telefono2`, `telefono3`) VALUES
(12, '20614669447', 'ROMA DISTRIBUCIONES & SERVICIOS GENERALES S.A.C.', 'ROMA D&SG SAC', '554a', 'AV. CANTA CALLAO MZ C LT . 5 MZ C URB. SAN JUAN SALINAS ', 'zoegv1607@gmail.com', '961710639', '0', NULL, 'STERPTUM', 'veriusion', 'e8a55644-f5aa-4f6a-b5c7-abc18b9d629e', 'DvWl96Rx2ZZACjSVbGDcYA==', 'logos/01M20W1PH25JTH0QKX1RFGFK2W.jpg', '150135', 'SAN MARTIN DE PORRES', 'LIMA', 'LIMA', '1', 'beta', 0.18, 'holaaaaaaa dfbvfdsbv', '946423341', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia_detalles`
--

CREATE TABLE `guia_detalles` (
  `guia_detalle_id` int(11) NOT NULL,
  `id_guia` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `detalles` varchar(200) DEFAULT NULL,
  `unidad` varchar(10) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio` double(20,5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `guia_detalles`
--

INSERT INTO `guia_detalles` (`guia_detalle_id`, `id_guia`, `id_producto`, `detalles`, `unidad`, `cantidad`, `precio`) VALUES
(5588, 419, 352, '4585 | SAL JJ D MAR 1K*25                                ', 'NIU', 1, 20.00000),
(5589, 420, 174, '4585 | F CINTA ROSCA NAPOLI*5K                           ', 'NIU', 1, 24.00000),
(5590, 420, 178, '4585 | F ROSCA FINA NAPOLI*5K                            ', 'NIU', 1, 24.00000),
(5591, 420, 179, '4585 | F ROSCA GRUESO*5K                                 ', 'NIU', 1, 24.00000),
(5592, 421, 174, '4585 | F CINTA ROSCA NAPOLI*5K                           ', 'NIU', 1, 24.00000),
(5593, 421, 178, '4585 | F ROSCA FINA NAPOLI*5K                            ', 'NIU', 1, 24.00000),
(5594, 421, 179, '4585 | F ROSCA GRUESO*5K                                 ', 'NIU', 1, 24.00000),
(5595, 422, 15, '4585 | ACEITE PATRONA *1LT                               ', 'NIU', 1, 80.00000),
(5596, 422, 120, '4585 |  CAMANEJO X1KG                                     ', 'NIU', 1, 6.20000),
(5597, 423, 414, 'ARROZ EXTRA SACO 10KG (TEST)', 'NIU', 1, 42.00000),
(5598, 423, 410, 'AZUCAR BLANCA IMPORTADA*50K', 'NIU', 10, 125.00000),
(5599, 424, 416, 'ACEITE VEGETAL CAJA 12X1L (TEST)', 'NIU', 10, 96.00000),
(5600, 425, 416, 'ACEITE VEGETAL CAJA 12X1L (TEST)', 'NIU', 10, 96.00000),
(5601, 426, 409, 'JABÓN DE ROPA BELTRA*175 GMS', 'NIU', 1, 45.00000),
(5602, 427, 409, 'JABÓN DE ROPA BELTRA*175 GMS', 'NIU', 1, 45.00000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia_detalle_transporte`
--

CREATE TABLE `guia_detalle_transporte` (
  `id` int(11) NOT NULL,
  `id_guia` int(11) DEFAULT NULL,
  `bien_normalizado` varchar(255) DEFAULT NULL,
  `codigo_bien` varchar(255) DEFAULT NULL,
  `codigo_sunat` varchar(255) DEFAULT NULL,
  `partida_arancelaria` varchar(255) DEFAULT NULL,
  `codigo_gtin` varchar(255) DEFAULT NULL,
  `descripcion_detallada` varchar(255) DEFAULT NULL,
  `unidad_medida` varchar(255) DEFAULT NULL,
  `cantidad` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia_remision`
--

CREATE TABLE `guia_remision` (
  `id_guia_remision` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `motivo_traslado` varchar(2) NOT NULL DEFAULT '01',
  `descripcion_motivo` varchar(255) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `fecha_traslado` date DEFAULT NULL,
  `dir_llegada` varchar(245) DEFAULT NULL,
  `ubigeo` varchar(6) DEFAULT NULL,
  `tipo_transporte` char(1) DEFAULT NULL,
  `ruc_transporte` varchar(45) DEFAULT NULL,
  `razon_transporte` varchar(245) DEFAULT NULL,
  `transportista_nro_mtc` varchar(30) DEFAULT NULL,
  `vehiculo` varchar(45) DEFAULT NULL,
  `chofer_brevete` varchar(45) DEFAULT NULL,
  `conductor_tipo_doc` varchar(1) DEFAULT NULL,
  `conductor_documento` varchar(15) DEFAULT NULL,
  `conductor_nombres` varchar(150) DEFAULT NULL,
  `conductor_apellidos` varchar(150) DEFAULT NULL,
  `conductor_licencia` varchar(30) DEFAULT NULL,
  `enviado_sunat` char(1) DEFAULT NULL,
  `estado_gre` varchar(20) NOT NULL DEFAULT 'pendiente',
  `ticket_sunat` varchar(100) DEFAULT NULL,
  `codigo_sunat` varchar(20) DEFAULT NULL,
  `mensaje_sunat` text DEFAULT NULL,
  `cdr_url` varchar(255) DEFAULT NULL,
  `hash` varchar(45) DEFAULT NULL,
  `nombre_xml` varchar(245) DEFAULT NULL,
  `xml_ruta` varchar(255) DEFAULT NULL,
  `cdr_ruta` varchar(255) DEFAULT NULL,
  `serie` varchar(4) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `peso` double(8,2) DEFAULT NULL,
  `und_peso_total` varchar(5) NOT NULL DEFAULT 'KGM',
  `ubigeo_partida` varchar(6) DEFAULT NULL,
  `dir_partida` varchar(255) DEFAULT NULL,
  `nro_bultos` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `sucursal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `guia_remision`
--

INSERT INTO `guia_remision` (`id_guia_remision`, `id_venta`, `motivo_traslado`, `descripcion_motivo`, `fecha_emision`, `fecha_traslado`, `dir_llegada`, `ubigeo`, `tipo_transporte`, `ruc_transporte`, `razon_transporte`, `transportista_nro_mtc`, `vehiculo`, `chofer_brevete`, `conductor_tipo_doc`, `conductor_documento`, `conductor_nombres`, `conductor_apellidos`, `conductor_licencia`, `enviado_sunat`, `estado_gre`, `ticket_sunat`, `codigo_sunat`, `mensaje_sunat`, `cdr_url`, `hash`, `nombre_xml`, `xml_ruta`, `cdr_ruta`, `serie`, `numero`, `peso`, `und_peso_total`, `ubigeo_partida`, `dir_partida`, `nro_bultos`, `estado`, `id_empresa`, `sucursal`) VALUES
(418, 0, '01', NULL, '2025-03-11', NULL, '', '010202', '1', '', '', NULL, '', '', NULL, NULL, NULL, NULL, NULL, '0', 'pendiente', NULL, NULL, NULL, NULL, '', '', NULL, NULL, 'T001', 1026, 1.00, 'KGM', NULL, NULL, 1, '1', 12, 1),
(419, 204, '01', NULL, '2025-08-19', NULL, 'MDO V ROSSY', '010202', '1', '', '', NULL, '', '', NULL, NULL, NULL, NULL, NULL, '0', 'pendiente', NULL, NULL, NULL, NULL, '', '', NULL, NULL, 'T001', 1027, 1.00, 'KGM', NULL, NULL, 1, '1', 12, 1),
(420, 220, '01', NULL, '2025-08-19', NULL, 'MDO V ROSSY', '010202', '1', '', '', NULL, '', '', NULL, NULL, NULL, NULL, NULL, '0', 'pendiente', NULL, NULL, NULL, NULL, '', '', NULL, NULL, 'T001', 1028, 1.00, 'KGM', NULL, NULL, 1, '1', 12, 1),
(421, 221, '01', NULL, '2025-08-19', NULL, 'MDO V ROSSY', '010202', '1', '', '', NULL, '', '', NULL, NULL, NULL, NULL, NULL, '0', 'pendiente', NULL, NULL, NULL, NULL, '', '', NULL, NULL, 'T001', 1029, 1.00, 'KGM', NULL, NULL, 1, '1', 12, 1),
(422, 224, '01', NULL, '2025-08-19', NULL, 'MDO V ROSSY', '010202', '1', '', '', NULL, '', '', NULL, NULL, NULL, NULL, NULL, '0', 'pendiente', NULL, NULL, NULL, NULL, '', '', NULL, NULL, 'T001', 1030, 1.00, 'KGM', NULL, NULL, 1, '1', 12, 1),
(423, 260, '01', NULL, '2026-07-10', '2026-07-10', 'PSJ.INCA ROCA MZ. 131 LT.33', '150135', '1', NULL, NULL, NULL, 'XX122', NULL, NULL, '11223344', 'Alejandro', 'Torres Vega', 'T11223344', '0', 'pendiente', NULL, NULL, 'XML generado, pendiente de envío.', NULL, 'IKN0LS2X95F0aCQDdwxDvhCtYMg=', '20000000001-09-T001-1031', 'sunat/xml/20000000001/20000000001-09-T001-1031.xml', NULL, 'T001', 1031, 1.00, 'KGM', '150135', 'SAN MARTIN DE PORRES', 1, '1', 12, 1),
(424, 263, '01', NULL, '2026-07-10', '2026-07-10', 'sdcsdcsd', '010601', '1', NULL, NULL, NULL, 'fgnhbnhgfnhg', NULL, NULL, '77425200', 'EMER RODRIGO', 'YARLEQUE ZAPATA', 'sdfvdsav2', '0', 'pendiente', NULL, NULL, 'XML generado, pendiente de envío.', NULL, 'Jwin/IyftiifJoOd3P4edHET1l0=', '20000000001-09-T001-1032', 'sunat/xml/20000000001/20000000001-09-T001-1032.xml', NULL, 'T001', 1032, 1.00, 'KGM', '150135', 'SAN MARTIN DE PORRES', 1, '1', 12, 1),
(425, 263, '01', NULL, '2026-07-22', '2026-07-22', 'sdcsdcsd', '030202', '1', NULL, NULL, NULL, 'efrvdsf345345', NULL, NULL, '77425200', 'EMER RODRIGO', 'YARLEQUE ZAPATA', 'cfdssdfvds', '0', 'pendiente', NULL, NULL, 'XML generado, pendiente de envío.', NULL, 'wE2vpuqCsGdo8GDCIHDDwFvxGpI=', '20000000001-09-T001-1033', 'sunat/xml/20000000001/20000000001-09-T001-1033.xml', NULL, 'T001', 1033, 1.00, 'KGM', '150135', 'SAN MARTIN DE PORRES', 1, '1', 12, 1),
(426, 265, '01', NULL, '2026-07-22', '2026-07-22', 'sdcsdcsd', '030302', '1', NULL, NULL, NULL, 'efrvdsf34534523', NULL, NULL, '77325200', 'YEMIMA ADALI', 'VIERA CIENFUEGOS', 'cfdssdfvdssdc', '0', 'pendiente', NULL, NULL, 'XML generado, pendiente de envío.', NULL, 'Xi9AVSeB4JFTnoP5aM/U89wgYV8=', '20000000001-09-T001-1034', 'sunat/xml/20000000001/20000000001-09-T001-1034.xml', NULL, 'T001', 1034, 21.00, 'KGM', '150135', 'SAN MARTIN DE PORRES', 1, '1', 12, 1),
(427, 267, '01', NULL, '2026-07-22', '2026-07-22', 'sdcsdcsd', '010304', '1', NULL, NULL, NULL, 'efrvdsf345345z', NULL, NULL, '77425200', 'EMER RODRIGO', 'YARLEQUE ZAPATA', 'savcdasvdfsvdsf', '0', 'pendiente', NULL, NULL, 'XML generado, pendiente de envío.', NULL, 'aS6Apv4RF/dly1vuVg4AE6A+KKo=', '20000000001-09-T001-1035', 'sunat/xml/20000000001/20000000001-09-T001-1035.xml', NULL, 'T001', 1035, 1.00, 'KGM', '150135', 'SAN MARTIN DE PORRES', 1, '1', 12, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia_sunat`
--

CREATE TABLE `guia_sunat` (
  `id_guia` int(11) NOT NULL,
  `hash` varchar(200) DEFAULT NULL,
  `nombre_xml` varchar(200) DEFAULT NULL,
  `qr_data` varchar(220) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia_transporte`
--

CREATE TABLE `guia_transporte` (
  `id_guia_remision` int(11) NOT NULL,
  `id_venta` varchar(20) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `dir_llegada` varchar(245) DEFAULT NULL,
  `ubigeo` varchar(6) DEFAULT NULL,
  `tipo_transporte` char(1) DEFAULT NULL,
  `ruc_transporte` varchar(45) DEFAULT NULL,
  `razon_transporte` varchar(245) DEFAULT NULL,
  `vehiculo` varchar(45) DEFAULT NULL,
  `chofer_brevete` varchar(45) DEFAULT NULL,
  `enviado_sunat` char(1) DEFAULT NULL,
  `hash` varchar(45) DEFAULT NULL,
  `nombre_xml` varchar(245) DEFAULT NULL,
  `serie` varchar(4) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `peso` double(8,2) DEFAULT NULL,
  `nro_bultos` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `sucursal` int(11) DEFAULT NULL,
  `transbordo` varchar(255) DEFAULT NULL,
  `retorno` varchar(255) DEFAULT NULL,
  `subcontratado` varchar(255) DEFAULT NULL,
  `envases` varchar(255) DEFAULT NULL,
  `pagador` varchar(255) DEFAULT NULL,
  `subcontratador` varchar(255) DEFAULT NULL,
  `flete` varchar(255) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `nom_cli` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `guia_transporte`
--

INSERT INTO `guia_transporte` (`id_guia_remision`, `id_venta`, `fecha_emision`, `dir_llegada`, `ubigeo`, `tipo_transporte`, `ruc_transporte`, `razon_transporte`, `vehiculo`, `chofer_brevete`, `enviado_sunat`, `hash`, `nombre_xml`, `serie`, `numero`, `peso`, `nro_bultos`, `estado`, `id_empresa`, `sucursal`, `transbordo`, `retorno`, `subcontratado`, `envases`, `pagador`, `subcontratador`, `flete`, `observacion`, `nom_cli`) VALUES
(1, '20554454276', '2024-06-10', 'AV. LOS MAESTROS NRO 206 FND SAN JOSÉ INT 101 - ICA  ICA - ICA', '150101', '1', '20554454276', 'LABORATORIOS CLINICOS MULTIPLES S.A.C.', 'MGF-322', '72314107', '0', '', '', 'T001', 626, 0.00, 0, '1', 12, 2, 'si', 'no', 'si', 'no', 'Subcontratador', 'EXACTA OPERADOR LOGISTICO SOCIEDAD ANONIMA CERRADA - REGISTRO ÚNICO DE CONTRIBUYENTES N° 20517650871', 'EXACTA OPERADOR LOGISTICO SOCIEDAD ANONIMA CERRADA - REGISTRO ÚNICO DE CONTRIBUYENTES N° 20517650871', 'SEGUN  GUÍA DE REMISIÓN ELECTRÓNICA  TRANSPORTISTA  N° EG03 - 00026899', 'TIENDAS POR DEPARTAMENTO RIPLEY S.A.C. - REGISTRO ÚNICO DE CONTRIBUYENTES ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_egreso`
--

CREATE TABLE `ingreso_egreso` (
  `intercambio_id` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `almacen_ingreso` char(1) DEFAULT NULL,
  `almacen_egreso` char(1) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT '2' COMMENT '2 = solo ingreso',
  `instrumento_tipo` varchar(30) DEFAULT NULL,
  `instrumento_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `ingreso_egreso`
--

INSERT INTO `ingreso_egreso` (`intercambio_id`, `id_producto`, `tipo`, `cantidad`, `almacen_ingreso`, `almacen_egreso`, `id_usuario`, `estado`, `instrumento_tipo`, `instrumento_id`) VALUES
(4, 1, 'e', 1, '1', '1', 40, '1', NULL, NULL),
(5, 0, 'i', 1, '1', NULL, 40, '2', NULL, NULL),
(6, 207, 'e', 2, '2', '1', 40, '1', NULL, NULL),
(7, 165, 'i', 3, '2', NULL, 40, '2', NULL, NULL),
(8, 163, 'e', 3, '2', '1', 40, '1', NULL, NULL),
(9, 209, 'e', 2, '2', '1', 40, '1', NULL, NULL),
(10, 996, 'e', 4, '2', '1', 40, '0', NULL, NULL),
(11, 2160, 'e', 4, '2', '1', 40, '0', NULL, NULL),
(12, 121, 'i', 1500, '1', NULL, 40, '2', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_movimientos`
--

CREATE TABLE `inventario_movimientos` (
  `id_movimiento` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `almacen` varchar(50) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `tipo` char(1) NOT NULL,
  `id_motivo` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `stock_anterior` int(11) NOT NULL DEFAULT 0,
  `stock_nuevo` int(11) NOT NULL DEFAULT 0,
  `costo` decimal(12,4) DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `inventario_movimientos`
--

INSERT INTO `inventario_movimientos` (`id_movimiento`, `id_empresa`, `almacen`, `id_producto`, `tipo`, `id_motivo`, `cantidad`, `stock_anterior`, `stock_nuevo`, `costo`, `id_proveedor`, `observacion`, `id_usuario`, `fecha`) VALUES
(1, 12, '1', 7, 'S', 6, 1, 10000, 9999, 70.0000, NULL, 'Venta B001-00000598', 40, '2026-06-26 17:56:24'),
(2, 12, '1', 410, 'S', 6, 1, 20, 19, 120.0000, NULL, 'Venta B001-00000599', 107, '2026-07-09 14:35:42'),
(3, 12, '1', 410, 'I', 6, 1, 19, 20, 120.0000, NULL, 'Anulación por nota de crédito BC01-00000001', 107, '2026-07-09 14:36:23'),
(4, 12, '1', 410, 'S', 6, 1, 20, 19, 120.0000, NULL, 'Venta NV01-00002949', 107, '2026-07-09 14:53:21'),
(5, 12, '1', 410, 'S', 6, 1, 19, 18, 120.0000, NULL, 'Venta B001-00000600', 107, '2026-07-09 14:54:28'),
(6, 12, '1', 418, 'S', 6, 3, 1000, 997, 140.0000, NULL, 'Venta NV01-00002970', 107, '2026-07-10 02:11:13'),
(7, 12, '1', 414, 'S', 6, 6, 1000, 994, 35.0000, NULL, 'Venta NV01-00002970', 107, '2026-07-10 02:11:13'),
(8, 12, '1', 417, 'S', 6, 2, 1000, 998, 4.8000, NULL, 'Venta NV01-00002971', 107, '2026-07-10 02:11:51'),
(9, 12, '1', 414, 'S', 6, 1, 994, 993, 35.0000, NULL, 'Venta B001-00000619', 108, '2026-07-10 04:33:07'),
(10, 12, '1', 410, 'S', 6, 10, 18, 8, 120.0000, NULL, 'Venta B001-00000619', 108, '2026-07-10 04:33:07'),
(11, 12, '2', 413, 'S', 6, 10, 100, 90, 1.0000, NULL, 'Venta B001-00000620', 108, '2026-07-10 04:55:10'),
(12, 12, '1', 410, 'S', 6, 8, 8, 0, 120.0000, NULL, 'Venta B001-00000621', 108, '2026-07-10 05:04:26'),
(13, 12, '1', 416, 'S', 6, 10, 1000, 990, 78.0000, NULL, 'Venta NV01-00002976', 108, '2026-07-10 05:12:24'),
(14, 12, '31564165', 419, 'I', 1, 10, 0, 10, 80.0000, NULL, 'adadadadaw', 107, '2026-07-10 14:56:09'),
(15, 12, '31564165', 420, 'I', 1, 10, 0, 10, 10.0000, NULL, 'adadadadaw', 107, '2026-07-10 15:04:23'),
(16, 12, '31564165', 420, 'S', 8, 5, 10, 5, NULL, NULL, 'adadadadaw', 107, '2026-07-10 15:04:51'),
(17, 12, '31564165', 421, 'I', 2, 80, 0, 80, 35.0000, NULL, 'Recepción #3 (compra #162)', 107, '2026-07-10 15:27:17'),
(18, 12, '1', 416, 'S', 9, 900, 990, 90, 78.0000, NULL, 'Traslado a almacen4. adadadadaw', 107, '2026-07-10 15:27:50'),
(19, 12, '31564165', 422, 'I', 5, 900, 0, 900, 78.0000, NULL, 'Traslado desde Almacén 1. adadadadaw', 107, '2026-07-10 15:27:50'),
(20, 12, '31564165', 409, 'I', 12, 100, 1, 101, 43.0000, NULL, 'Préstamo de ardáis Mario', 107, '2026-07-10 15:28:52'),
(21, 12, '31564165', 423, 'I', 12, 900, 0, 900, 140.0000, NULL, 'Préstamo de adadadad', 107, '2026-07-10 15:33:45'),
(22, 12, '31564165', 409, 'S', 11, 10, 101, 91, 43.0000, NULL, 'Devolución a ardáis Mario', 107, '2026-07-10 15:34:43'),
(23, 12, '31564165', 423, 'S', 11, 1, 900, 899, 140.0000, NULL, 'Devolución a adadadad', 107, '2026-07-10 16:15:29'),
(24, 12, '31564165', 423, 'S', 11, 1, 899, 898, 140.0000, NULL, 'Devolución a adadadad', 107, '2026-07-10 16:18:53'),
(25, 12, '31564165', 423, 'S', 11, 898, 898, 0, 140.0000, NULL, 'Devolución a adadadad', 107, '2026-07-10 16:20:13'),
(26, 12, '1', 412, 'S', 9, 400, 451, 51, 120.0000, NULL, 'Traslado TS-00000001 a almacen4. adadadadaw', 107, '2026-07-10 17:03:49'),
(27, 12, '31564165', 424, 'I', 5, 400, 0, 400, 120.0000, NULL, 'Traslado TS-00000001 desde Almacén 1. adadadadaw', 107, '2026-07-10 17:03:49'),
(28, 12, '1', 415, 'S', 9, 500, 1000, 500, 19.0000, NULL, 'Traslado TS-00000001 a almacen4. adadadadaw', 107, '2026-07-10 17:03:49'),
(29, 12, '31564165', 425, 'I', 5, 500, 0, 500, 19.0000, NULL, 'Traslado TS-00000001 desde Almacén 1. adadadadaw', 107, '2026-07-10 17:03:49'),
(30, 12, '31564165', 424, 'S', 15, 100, 400, 300, 120.0000, NULL, 'Ajuste TS-00000001: \"LENTEJA  BB VERDE *SACO VERDE\" devuelve a Almacén 1', 107, '2026-07-10 17:06:26'),
(31, 12, '1', 412, 'I', 14, 100, 51, 151, 120.0000, NULL, 'Ajuste TS-00000001: \"LENTEJA  BB VERDE *SACO VERDE\" regresa desde almacen4', 107, '2026-07-10 17:06:26'),
(32, 12, '31564165', 425, 'S', 15, 250, 500, 250, 19.0000, NULL, 'Ajuste TS-00000001: \"AZUCAR RUBIA BOLSA 5KG (TEST)\" devuelve a Almacén 1', 107, '2026-07-10 17:06:27'),
(33, 12, '1', 415, 'I', 14, 250, 500, 750, 19.0000, NULL, 'Ajuste TS-00000001: \"AZUCAR RUBIA BOLSA 5KG (TEST)\" regresa desde almacen4', 107, '2026-07-10 17:06:27'),
(34, 12, '31564165', 426, 'I', 2, 900, 0, 900, 152.0000, NULL, 'Recepción #4 (compra #163)', 107, '2026-07-10 17:08:27'),
(35, 12, '1', 414, 'I', 2, 50, 993, 1043, 35.0000, NULL, 'Recepción #5 (compra #165)', 111, '2026-07-22 15:21:02'),
(36, 12, '1', 416, 'S', 9, 50, 90, 40, 78.0000, NULL, 'Traslado TS-00000002 a Almacén 2. hbsssfj s fjshfjsf jsfsfs', 111, '2026-07-22 15:24:07'),
(37, 12, '2', 427, 'I', 5, 50, 0, 50, 78.0000, NULL, 'Traslado TS-00000002 desde Almacén 1. hbsssfj s fjshfjsf jsfsfs', 111, '2026-07-22 15:24:07'),
(38, 12, '1', 415, 'S', 9, 50, 750, 700, 19.0000, NULL, 'Traslado TS-00000002 a Almacén 2. hbsssfj s fjshfjsf jsfsfs', 111, '2026-07-22 15:24:07'),
(39, 12, '2', 428, 'I', 5, 50, 0, 50, 19.0000, NULL, 'Traslado TS-00000002 desde Almacén 1. hbsssfj s fjshfjsf jsfsfs', 111, '2026-07-22 15:24:07'),
(40, 12, '1', 429, 'I', 8, 5, 0, 5, NULL, NULL, 'b nbes f', 111, '2026-07-22 15:25:15'),
(41, 12, '1', 430, 'I', 8, 5, 0, 5, NULL, NULL, 'b nbes f', 111, '2026-07-22 15:25:15'),
(42, 12, '2', 411, 'S', 6, 1, 20, 19, 152.0000, NULL, 'Venta B001-00000622', 107, '2026-07-22 15:26:12'),
(43, 12, '1', 419, 'S', 11, 5, 10, 5, 80.0000, NULL, 'Préstamo a ardáis Mario', 111, '2026-07-22 15:26:54'),
(44, 12, '1', 429, 'I', 12, 1, 5, 6, 80.0000, NULL, 'Devolución de ardáis Mario', 111, '2026-07-22 15:27:25'),
(45, 12, '1', 429, 'S', 11, 1, 6, 5, 80.0000, NULL, 'Anulación devolución #5 (ardáis Mario)', 111, '2026-07-22 15:27:51'),
(46, 12, '2', 409, 'S', 6, 1, 91, 90, 43.0000, NULL, 'Venta NV01-00002979', 107, '2026-07-22 16:05:59'),
(47, 12, '2', 411, 'I', 6, 1, 19, 20, 152.0000, NULL, 'Anulación por nota de crédito BC01-00000002', 107, '2026-07-22 16:16:06'),
(48, 12, '2', 409, 'S', 6, 1, 90, 89, 43.0000, NULL, 'Venta NV01-00002980', 107, '2026-07-22 16:19:16'),
(49, 12, '2', 409, 'S', 6, 1, 89, 88, 43.0000, NULL, 'Venta B001-00000623', 107, '2026-07-22 16:20:01'),
(50, 12, '1', 429, 'I', 12, 1, 5, 6, 80.0000, NULL, 'Devolución de ardáis Mario', 107, '2026-07-27 19:40:32'),
(51, 12, '1', 429, 'I', 7, 1, 6, 7, NULL, NULL, 'adadadadaw', 107, '2026-07-27 19:42:35'),
(52, 12, 'aedawdawd', 431, 'I', 2, 10, 0, 10, 10.0000, NULL, 'Recepción #6 (compra #166)', 107, '2026-07-27 23:10:38'),
(53, 12, 'aedawdawd', 431, 'S', 8, 5, 10, 5, NULL, NULL, 'ad ada adadw', 107, '2026-07-27 23:11:42'),
(55, 0, '80105', 434, 'I', 16, 50, 0, 50, NULL, NULL, 'aaaaaaaaa', 115, '2026-09-08 17:46:35'),
(56, 0, '80105', 434, 'S', NULL, 5, 50, 45, 25.6100, NULL, 'Traslado TS-00000003 a ALMACEN2. aaaaaaaaaa', 115, '2026-09-08 17:49:53'),
(57, 0, 'AL2', 435, 'I', NULL, 5, 0, 5, 25.6100, NULL, 'Traslado TS-00000003 desde ALMACEN1. aaaaaaaaaa', 115, '2026-09-08 17:49:53'),
(58, 0, 'AL2', 434, 'S', NULL, 1, 45, 44, 25.6100, NULL, 'Préstamo a wuygfjfbse', 115, '2026-09-08 17:50:46'),
(59, 0, 'AL2', 435, 'I', NULL, 1, 5, 6, 25.6100, NULL, 'Devolución de wuygfjfbse', 115, '2026-09-08 17:51:02'),
(60, 0, 'AL2', 435, 'S', 17, 2, 6, 4, NULL, NULL, 'sdfefsfs', 115, '2026-09-08 17:52:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id_marca` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id_marca`, `nombre`, `descripcion`, `id_empresa`, `estado`) VALUES
(1, 'mm', NULL, 12, '1'),
(2, 'Molitalia', 'Marca principal de pastas y harinas', 12, '1'),
(3, 'Don Vittorio', 'Pastas premium', 12, '1'),
(4, 'Primor', 'Aceites comestibles', 12, '1'),
(5, 'Sello de Oro', 'Margarinas y aceites', 12, '1'),
(6, 'Nutri-V', 'Galletas y snacks nutritivos', 12, '1'),
(7, 'Opal', 'Jabones y detergentes', 12, '1'),
(8, 'Patito', 'Galletas tradicionales', 12, '1'),
(9, 'Activ', 'Bebidas hidratantes y jugos', 12, '1'),
(10, 'MOLITALIA', 'AAAA', 0, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mes`
--

CREATE TABLE `mes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(12) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `mes`
--

INSERT INTO `mes` (`id`, `nombre`) VALUES
(1, 'Ene'),
(2, 'Feb'),
(3, 'Mar'),
(4, 'Abr'),
(5, 'May'),
(6, 'Jun'),
(7, 'Jul'),
(8, 'Ago'),
(9, 'Set'),
(10, 'Oct'),
(11, 'Nov'),
(12, 'Dic');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id_metodo_pago` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `estado` char(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `metodo_pago`
--

INSERT INTO `metodo_pago` (`id_metodo_pago`, `nombre`, `estado`) VALUES
(1, 'TRANSFERENCIA BANCO BCP', '1'),
(2, 'TRANSFERENCIA BANCO NACION', '1'),
(3, 'TRANSFERENCIA BANCO INTERBANK', '1'),
(4, 'TRANSFERENCIA BANCO BBVA', '1'),
(5, 'YAPE', '1'),
(6, 'PLIN', '1'),
(7, 'TARJETA DE CREDITO VISA', '0'),
(8, 'TARJETA DE CREDITO MASTERCARD', '0'),
(9, 'TARJETA DE CREDITO DINNERS CLUB', '0'),
(10, 'POS ', '1'),
(11, 'TRANSFERENCIA BANCO SCOTIABANK', '1'),
(12, 'EFECTIVO', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_01_01_000000_create_usuarios_table', 1),
(2, '2024_01_01_000001_create_sessions_table', 1),
(3, '2026_05_07_164349_create_permission_tables', 1),
(4, '2026_06_25_163621_fix_caja_empresa_and_ingreso_egreso_schema', 1),
(5, '2024_01_01_000002_add_security_columns', 2),
(6, '2026_06_24_000000_create_catalogo_tables', 3),
(7, '2026_06_24_000001_add_descripcion_to_catalogo', 3),
(8, '2026_06_24_000002_create_almacenes_table', 3),
(9, '2026_06_24_000003_create_inventario_movimientos', 3),
(10, '2026_06_24_000004_add_recepcionado_to_compras', 3),
(11, '2026_06_24_000005_create_prestamos', 3),
(12, '2026_06_24_000006_add_nombre_to_sucursales', 3),
(13, '2026_06_25_134257_create_prestamo_detalle_table', 3),
(14, '2026_06_25_140000_create_traslados_tables', 3),
(15, '2026_06_25_150000_add_snapshots_to_traslado_detalle', 3),
(16, '2026_06_25_160000_create_prestamo_devoluciones', 3),
(17, '2026_06_25_160846_create_bancos_table', 3),
(18, '2026_06_25_160851_create_cuentas_bancarias_table', 3),
(19, '2026_06_25_160856_create_tarjetas_table', 3),
(20, '2026_06_25_160900_create_billeteras_digitales_table', 3),
(21, '2026_06_25_160904_add_instrumento_pago_to_compras_table', 3),
(22, '2026_06_25_162145_add_instrumento_pago_to_caja_empresa_table', 3),
(23, '2026_06_25_162753_add_instrumento_pago_to_ingreso_egreso_table', 3),
(24, '2026_06_25_164838_create_billetera_tipos_and_update_billeteras_digitales', 3),
(25, '2026_06_25_170000_create_compra_recepciones', 3),
(26, '2026_06_25_180000_create_recepciones', 3),
(27, '2026_06_25_174733_create_caja_movimientos_table', 4),
(28, '2026_06_25_174733_create_cajas_table', 4),
(29, '2026_06_25_174734_create_caja_instrumentos_table', 4),
(30, '2026_06_25_174735_create_arqueo_detalle_table', 4),
(31, '2026_06_25_174735_create_caja_chica_rendiciones_table', 4),
(32, '2026_06_25_174736_alter_arqueos_diarios_add_id_caja', 4),
(33, '2026_06_25_175035_seed_cajas_from_old_tables', 5),
(34, '2026_06_25_190000_alter_cajas_drop_tipo', 5),
(35, '2026_06_25_190100_create_cierre_caja_table', 5),
(36, '2026_06_25_191000_convert_caja_instrumentos_to_metodos_pago', 5),
(37, '2026_06_25_191500_widen_productos_almacen', 5),
(38, '2026_06_25_231544_add_id_venta_to_cotizaciones', 6),
(39, '2026_06_25_191600_add_instrumento_pago_to_dias_compras', 7),
(40, '2026_06_26_000001_create_guia_remision_table', 8),
(41, '2026_06_26_000002_create_guia_detalles_table', 8),
(42, '2026_06_26_000003_create_notas_electronicas_table', 9),
(43, '2026_06_26_000004_add_missing_cols_notas_electronicas', 9),
(44, '2026_06_26_000005_add_subtotal_to_ventas', 10),
(45, '2026_06_26_000006_add_cols_to_productos_ventas', 11),
(46, '2026_06_26_155218_create_caja_aperturas_table', 12),
(47, '2026_06_27_175416_add_remember_token_to_usuarios', 12),
(48, '2026_06_29_000001_create_tms_tables', 12),
(49, '2026_06_29_000002_seed_tms_mercados_from_clientes', 12),
(50, '2026_06_29_000003_create_tms_despacho_costos_table', 12),
(51, '2026_06_30_000001_create_catalogos_producto_tables', 12),
(52, '2026_07_01_000001_add_foto_to_usuarios_table', 12),
(53, '2026_07_03_000001_add_ubigeo_to_tms_mercados', 12),
(54, '2026_07_03_000001_create_tms_tipos_vehiculo_table', 12),
(55, '2026_07_03_000002_add_fecha_registro_to_productos_cotis', 12),
(56, '2026_07_06_224043_add_description_to_permissions_table', 12),
(57, '2026_07_07_012626_create_audits_table', 12),
(58, '2026_07_07_020000_create_audits_table_pgsql', 13),
(59, '2026_07_08_000001_add_gre_fields_to_empresas_and_guias', 14),
(60, '2026_07_08_000002_add_sunat_fields_to_ventas', 14),
(61, '2026_07_08_000003_add_tipo_igv_to_ventas', 14),
(62, '2026_07_09_000001_add_motivo_desc_to_notas_electronicas', 14),
(63, '2026_07_09_000002_fix_notas_electronicas_autoincrement', 14),
(64, '2026_07_09_000003_add_sunat_fields_to_notas_electronicas', 14),
(65, '2026_07_09_000004_add_pago_referencia_to_ventas', 15),
(66, '2026_07_09_000005_add_created_at_to_caja_movimientos', 15),
(67, '2026_07_09_000006_add_xml_ruta_to_guia_remision', 15),
(68, '2026_07_09_000006_create_caja_cierre_deudas', 15),
(69, '2026_07_09_000007_add_referencia_to_caja_movimientos', 15),
(70, '2026_07_10_000001_create_cxc_abonos', 16),
(71, '2026_07_10_000002_backfill_cxc_abonos', 16),
(72, '2026_07_10_000003_migrar_audits_pgsql_a_mysql', 17),
(73, '2026_07_10_000000_add_costo_to_traslado_detalle', 18),
(74, '2026_07_10_000001_add_estado_to_traslado_detalle', 18),
(75, '2026_07_10_000001_add_ubigeo_to_clientes', 18),
(76, '2026_07_09_000003_create_dias_ventas_table', 19),
(77, '2026_07_25_004224_add_qr_to_billeteras_digitales_table', 20),
(78, '2026_07_25_023449_create_plan_cuentas_table', 20),
(79, '2026_07_25_023506_create_asientos_contables_table', 20),
(80, '2026_07_25_023507_create_asientos_detalle_table', 20),
(81, '2026_07_11_000001_create_venta_pagos_table', 21),
(82, '2026_07_27_000001_crear_permisos_contabilidad', 21),
(83, '2026_07_27_000002_limpiar_marcas_cierre_en_caja_movimientos', 22),
(84, '2026_07_27_000003_agregar_id_apertura_a_cierre_caja', 22),
(85, '2026_07_27_000004_recalcular_cierres_pendientes_por_turno', 22),
(86, '2026_07_27_000005_crear_transferencias_fondo', 22),
(87, '2026_07_27_000006_agregar_stock_minimo_maximo_a_productos', 23),
(88, '2026_07_27_000007_crear_tabla_notifications', 23),
(89, '2026_07_28_000001_agregar_saldo_inicial_a_cuentas_bancarias', 24),
(90, '2026_07_28_000002_crear_permisos_finanzas', 25),
(91, '2026_07_28_000003_eliminar_permisos_contabilidad', 25),
(92, '2026_07_28_000004_limpiar_permisos_sin_uso', 25),
(93, '2026_07_28_000005_limpiar_permisos_sin_pantalla', 25),
(94, '2026_07_28_000006_desglosar_permisos_caja', 26),
(95, '2026_07_28_000007_permisos_acciones_sin_control', 27),
(96, '2026_07_28_000008_permisos_caja_por_pantalla', 28),
(97, '2026_07_28_000009_caja_un_permiso_por_accion', 28);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` int(11) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motivos_movimiento`
--

CREATE TABLE `motivos_movimiento` (
  `id_motivo` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `tipo` char(1) NOT NULL,
  `es_sistema` tinyint(4) NOT NULL DEFAULT 0,
  `id_empresa` int(11) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `motivos_movimiento`
--

INSERT INTO `motivos_movimiento` (`id_motivo`, `nombre`, `tipo`, `es_sistema`, `id_empresa`, `estado`) VALUES
(1, 'Carga inicial', 'I', 0, 12, '1'),
(2, 'Compra', 'I', 1, 12, '1'),
(3, 'Ajuste positivo', 'I', 0, 12, '1'),
(4, 'Devolución de cliente', 'I', 0, 12, '1'),
(5, 'Traslado entrada', 'I', 1, 12, '1'),
(6, 'Venta', 'S', 1, 12, '1'),
(7, 'Ajuste negativo', 'S', 0, 12, '1'),
(8, 'Merma / pérdida', 'S', 0, 12, '1'),
(9, 'Traslado salida', 'S', 1, 12, '1'),
(10, 'Consumo interno', 'S', 0, 12, '1'),
(11, 'Préstamo entregado', 'S', 1, 12, '1'),
(12, 'Préstamo recibido', 'I', 1, 12, '1'),
(13, 'ingreso prueba', 'I', 0, 12, '1'),
(14, 'Anulación traslado', 'I', 1, 12, '1'),
(15, 'Anulación traslado', 'S', 1, 12, '1'),
(16, 'CARGA INICAL', 'I', 0, 0, '1'),
(17, 'merma', 'S', 1, 0, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motivo_documento`
--

CREATE TABLE `motivo_documento` (
  `id_motivo` int(11) NOT NULL,
  `codigo` varchar(10) DEFAULT NULL,
  `nombre` varchar(145) DEFAULT NULL,
  `id_tido` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `motivo_documento`
--

INSERT INTO `motivo_documento` (`id_motivo`, `codigo`, `nombre`, `id_tido`) VALUES
(1, '01', 'Anulación de la operacion', 3),
(2, '02', 'Anulación por error en el RUC', 3),
(3, '03', 'Corrección por error en la descripción', 3),
(4, '10', 'Otros Conceptos', 3),
(5, '01', 'Intereses por mora', 4),
(6, '02', 'Aumento en el valor', 4),
(7, '03', 'Penalidades/ otros conceptos', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas_electronicas`
--

CREATE TABLE `notas_electronicas` (
  `nota_id` int(11) NOT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `tipo` varchar(10) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `sucursal` int(11) DEFAULT NULL,
  `tido` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `serie` varchar(20) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT 0.00,
  `motivo` int(11) DEFAULT NULL,
  `motivo_desc` varchar(255) DEFAULT NULL,
  `cod_motivo` varchar(5) NOT NULL DEFAULT '01',
  `monto` double(15,2) DEFAULT NULL,
  `productos` longtext DEFAULT NULL,
  `estado_sunat` char(1) DEFAULT '0',
  `estado` char(1) DEFAULT '1',
  `fecha_emision` date DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `nombre_xml` varchar(255) DEFAULT NULL,
  `enviado_sunat` varchar(2) DEFAULT '0',
  `sunat_estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `sunat_mensaje` text DEFAULT NULL,
  `xml_ruta` varchar(255) DEFAULT NULL,
  `cdr_ruta` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `notas_electronicas`
--

INSERT INTO `notas_electronicas` (`nota_id`, `id_venta`, `tipo`, `id_empresa`, `sucursal`, `tido`, `fecha`, `serie`, `numero`, `total`, `motivo`, `motivo_desc`, `cod_motivo`, `monto`, `productos`, `estado_sunat`, `estado`, `fecha_emision`, `hash`, `nombre_xml`, `enviado_sunat`, `sunat_estado`, `sunat_mensaje`, `xml_ruta`, `cdr_ruta`) VALUES
(1, 8, NULL, 12, 1, 3, '2024-08-03', 'F001', 6, 0.00, 1, NULL, '01', 192.00, '[{\"productoid\":\"\",\"descripcion\":\"kuatitos\",\"cantidad\":\"1\",\"precio\":\"192\",\"codigo\":\"\",\"costo\":\"\"}]', '0', '1', NULL, NULL, NULL, '0', 'pendiente', NULL, NULL, NULL),
(2, 237, 'credito', 12, 1, NULL, NULL, 'BC01', 1, 125.00, 1, 'Anulación de la operación', '01', NULL, NULL, '0', '1', '2026-07-09', '+nF/QtQRHm1s6XYfguLOc8cHzxE=', '20000000001-07-BC01-1', '1', 'aceptado', 'Aceptada por SUNAT.', 'sunat/xml/20000000001/20000000001-07-BC01-1.xml', 'sunat/cdr/20000000001/R-20000000001-07-BC01-1.zip'),
(3, 264, 'credito', 12, 1, NULL, NULL, 'BC01', 2, 157.00, 1, 'Anulación de la operación', '01', NULL, NULL, '0', '1', '2026-07-22', 'e6so6Os5ae0Jg9nIq/t2L/TorGY=', '20000000001-07-BC01-2', '1', 'aceptado', 'Aceptada por SUNAT.', 'sunat/xml/20000000001/20000000001-07-BC01-2.xml', 'sunat/cdr/20000000001/R-20000000001-07-BC01-2.zip');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas_electronicas_sunat`
--

CREATE TABLE `notas_electronicas_sunat` (
  `id_notas_electronicas` int(11) NOT NULL,
  `hash` varchar(200) DEFAULT NULL,
  `nombre_xml` varchar(200) DEFAULT NULL,
  `qr_data` varchar(220) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `notas_electronicas_sunat`
--

INSERT INTO `notas_electronicas_sunat` (`id_notas_electronicas`, `hash`, `nombre_xml`, `qr_data`) VALUES
(0, 'CWk0mb9Jh88O1xTUKF6lZrWUjbo=', '20603319274-07-F001-6', '20603319274|07|F001-6|29.29|29.29|2024-08-03|0|00000000');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('6b88cfe2-67bb-446b-ac2d-fd27c843f754', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 111, '{\"actions\":[],\"body\":\"S\\/ 500.00 desde \\\"Caja Principal\\\". Cuenta el efectivo recibido y apertura tu caja para aplicarlo.\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-banknotes\",\"iconColor\":\"info\",\"status\":\"info\",\"title\":\"Fondo asignado a tu caja \\\"CAJA VICTOR (PRUEBA)\\\"\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-07-28 03:46:13', '2026-07-28 03:46:13'),
('b770becc-a1e5-4301-8542-c816dbb16e66', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 107, '{\"actions\":[],\"body\":\"Quedan 5 unidades (m\\u00ednimo 5) en almacne-pruba2.\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-exclamation-triangle\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Bajo stock: ACEITE RICOSOL 1 L                           \",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-07-28 03:11:42', '2026-07-28 03:11:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ventas.ver', 'web', NULL, '2026-05-07 10:38:08', NULL),
(2, 'ventas.crear', 'web', NULL, '2026-05-07 10:38:08', NULL),
(3, 'ventas.anular', 'web', NULL, '2026-05-07 10:38:08', NULL),
(4, 'compras.ver', 'web', NULL, '2026-05-07 10:38:08', NULL),
(5, 'compras.crear', 'web', NULL, '2026-05-07 10:38:08', NULL),
(6, 'clientes.ver', 'web', NULL, '2026-05-07 10:38:08', NULL),
(7, 'clientes.editar', 'web', NULL, '2026-05-07 10:38:08', NULL),
(8, 'clientes.borrar', 'web', NULL, '2026-05-07 10:38:08', NULL),
(9, 'productos.ver', 'web', NULL, '2026-05-07 10:38:08', NULL),
(10, 'productos.editar', 'web', NULL, '2026-05-07 10:38:08', NULL),
(12, 'reportes.exportar', 'web', NULL, '2026-05-07 10:38:08', NULL),
(15, 'caja.ver', 'web', NULL, '2026-05-07 10:38:08', NULL),
(16, 'caja.gestionar', 'web', NULL, '2026-05-07 10:38:08', NULL),
(17, 'cotizaciones.ver', 'web', NULL, '2026-05-07 10:38:08', NULL),
(18, 'cotizaciones.crear', 'web', NULL, '2026-05-07 10:38:08', NULL),
(19, 'ventas.pdf', 'web', 'Generar PDF / comprobante', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(20, 'compras.editar', 'web', 'Editar compras', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(21, 'compras.pdf', 'web', 'Generar PDF', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(22, 'cotizaciones.editar', 'web', 'Editar cotizaciones', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(23, 'cotizaciones.pdf', 'web', 'Generar PDF', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(24, 'cotizaciones.cuotas', 'web', 'Gestionar cuotas', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(25, 'notas.ver', 'web', 'Ver listado', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(26, 'notas.crear', 'web', 'Crear notas', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(27, 'notas.pdf', 'web', 'Generar PDF', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(28, 'guias.ver', 'web', 'Ver listado', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(29, 'guias.crear', 'web', 'Crear guías', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(30, 'guias.pdf', 'web', 'Generar PDF', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(31, 'clientes.crear', 'web', 'Crear clientes', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(32, 'clientes.exportar', 'web', 'Exportar Excel', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(33, 'proveedores.ver', 'web', 'Ver listado', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(34, 'proveedores.crear', 'web', 'Crear proveedores', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(35, 'proveedores.editar', 'web', 'Editar proveedores', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(36, 'proveedores.exportar', 'web', 'Exportar Excel', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(37, 'productos.crear', 'web', 'Crear productos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(38, 'productos.kardex', 'web', 'Ver kardex', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(40, 'almacen_recepcion.ver', 'web', 'Ver recepciones', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(41, 'almacen_recepcion.crear', 'web', 'Registrar recepción', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(42, 'almacen_existencias.ver', 'web', 'Ver existencias por almacén', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(43, 'almacen_ajustes.ver', 'web', 'Ver ajustes', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(44, 'almacen_ajustes.crear', 'web', 'Crear ajustes', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(45, 'almacen_traslados.ver', 'web', 'Ver traslados', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(46, 'almacen_traslados.crear', 'web', 'Crear traslados', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(47, 'almacen_prestamos.ver', 'web', 'Ver préstamos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(48, 'almacen_prestamos.crear', 'web', 'Registrar préstamos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(50, 'cobranzas.ver', 'web', 'Ver cobranzas', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(51, 'cobranzas.registrar', 'web', 'Registrar cobros', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(52, 'pagos.ver', 'web', 'Ver pagos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(53, 'pagos.registrar', 'web', 'Registrar pagos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(54, 'tms_mercados.ver', 'web', 'Ver mercados', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(55, 'tms_mercados.crear', 'web', 'Crear mercados', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(56, 'tms_mercados.editar', 'web', 'Editar mercados', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(57, 'tms_vehiculos.ver', 'web', 'Ver vehículos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(58, 'tms_vehiculos.crear', 'web', 'Crear vehículos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(59, 'tms_vehiculos.editar', 'web', 'Editar vehículos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(60, 'tms_conductores.ver', 'web', 'Ver conductores', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(61, 'tms_conductores.crear', 'web', 'Crear conductores', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(62, 'tms_conductores.editar', 'web', 'Editar conductores', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(63, 'tms_rutas.ver', 'web', 'Ver rutas', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(64, 'tms_rutas.crear', 'web', 'Crear rutas', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(65, 'tms_rutas.editar', 'web', 'Editar rutas', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(66, 'tms_despachos.ver', 'web', 'Ver despachos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(67, 'tms_despachos.crear', 'web', 'Armar despachos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(68, 'tms_despachos.editar', 'web', 'Editar despachos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(69, 'tms_despachos.pdf', 'web', 'Hoja de carga / guías PDF', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(70, 'reportes_ventas.pdf', 'web', 'Reporte de ventas PDF', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(72, 'reportes_clientes.pdf', 'web', 'Reporte de clientes PDF', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(73, 'usuarios.ver', 'web', 'Ver listado', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(74, 'usuarios.crear', 'web', 'Crear usuarios', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(75, 'usuarios.editar', 'web', 'Editar usuarios', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(76, 'usuarios.borrar', 'web', 'Eliminar usuarios', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(77, 'empresas.ver', 'web', 'Ver empresas', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(78, 'empresas.crear', 'web', 'Crear empresas', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(79, 'empresas.editar', 'web', 'Editar empresas', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(80, 'sucursales.ver', 'web', 'Ver sucursales', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(81, 'sucursales.crear', 'web', 'Crear sucursales', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(82, 'sucursales.editar', 'web', 'Editar sucursales', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(83, 'roles.ver', 'web', 'Ver listado', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(84, 'roles.crear', 'web', 'Crear roles', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(85, 'roles.editar', 'web', 'Editar roles', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(86, 'roles.borrar', 'web', 'Eliminar roles', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(87, 'permisos.ver', 'web', 'Ver listado', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(91, 'auditoria.ver', 'web', 'Ver registro de auditoría', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(92, 'correlativos.gestionar', 'web', 'Configurar correlativos', '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(93, 'caja.apertura_ver', 'web', 'Ver detalle de la apertura', '2026-07-10 05:03:36', '2026-07-10 05:03:36'),
(94, 'caja.apertura_editar', 'web', 'Editar la apertura del día', '2026-07-10 05:03:36', '2026-07-10 05:03:36'),
(95, 'cobranzas.editar', 'web', 'Editar abonos', '2026-07-10 09:44:49', '2026-07-10 09:44:49'),
(96, 'cobranzas.anular', 'web', 'Anular abonos', '2026-07-10 09:44:49', '2026-07-10 09:44:49'),
(98, 'cobranzas_miscobros.ver', 'web', 'Ver mis cobros', '2026-07-10 09:44:49', '2026-07-10 09:44:49'),
(101, 'finanzas.utilidades', 'web', 'Ver utilidades', '2026-09-08 06:21:37', '2026-09-08 06:21:37'),
(102, 'finanzas.flujo_caja', 'web', 'Ver flujo de caja', '2026-09-08 06:21:37', '2026-09-08 06:21:37'),
(103, 'finanzas.estado_resultados', 'web', 'Ver estado de resultados', '2026-09-08 06:21:37', '2026-09-08 06:21:37'),
(104, 'finanzas.indicadores', 'web', 'Ver indicadores financieros', '2026-09-08 06:21:37', '2026-09-08 06:21:37'),
(105, 'finanzas.margenes', 'web', 'Ver análisis de márgenes', '2026-09-08 06:21:37', '2026-09-08 06:21:37'),
(106, 'finanzas.costeo', 'web', 'Ver costeo y rentabilidad', '2026-09-08 06:21:37', '2026-09-08 06:21:37'),
(107, 'caja.principales', 'web', 'Ver cajas principales', '2026-09-08 08:00:11', '2026-09-08 08:00:11'),
(108, 'caja.cierres', 'web', 'Ver cierres de caja', '2026-09-08 08:00:11', '2026-09-08 08:00:11'),
(109, 'caja.transferencias', 'web', 'Ver transferencias de fondos', '2026-09-08 08:00:11', '2026-09-08 08:00:11'),
(110, 'caja.metodos_pago', 'web', 'Gestionar bancos, cuentas, tarjetas y billeteras', '2026-09-08 08:00:11', '2026-09-08 08:00:11'),
(111, 'caja.aperturar', 'web', 'Aperturar la caja', '2026-09-08 08:00:11', '2026-09-08 08:00:11'),
(112, 'caja.cerrar', 'web', 'Cerrar la caja', '2026-09-08 08:00:11', '2026-09-08 08:00:11'),
(113, 'caja.movimiento_registrar', 'web', 'Registrar ingresos y egresos manuales', '2026-09-08 08:00:11', '2026-09-08 08:00:11'),
(114, 'caja.movimiento_anular', 'web', 'Anular un movimiento de caja', '2026-09-08 08:00:11', '2026-09-08 08:00:11'),
(115, 'caja.cierre_aprobar', 'web', 'Aprobar o rechazar un cierre', '2026-09-08 08:00:11', '2026-09-08 08:00:11'),
(116, 'ventas.sunat', 'web', 'Enviar a SUNAT, regenerar XML y descargar CDR', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(117, 'guias.sunat', 'web', 'Enviar a SUNAT, regenerar XML y descargar CDR', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(118, 'guias.anular', 'web', 'Anular guías de remisión', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(119, 'notas.sunat', 'web', 'Enviar a SUNAT, regenerar XML y descargar CDR', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(120, 'notas.anular', 'web', 'Anular notas electrónicas', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(121, 'cotizaciones.anular', 'web', 'Anular cotizaciones', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(122, 'almacen_ajustes.anular', 'web', 'Anular ajustes', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(123, 'almacen_traslados.anular', 'web', 'Anular traslados', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(124, 'tms_despachos.cerrar', 'web', 'Cerrar despachos', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(125, 'tms_despachos.anular', 'web', 'Anular despachos', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(126, 'clientes.importar', 'web', 'Importar clientes desde Excel', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(127, 'productos.importar', 'web', 'Importar productos desde Excel', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(128, 'caja.cancelar_deuda', 'web', 'Cancelar (perdonar) una deuda de cierre', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(129, 'qr.ver', 'web', 'Ver códigos QR de cobro', '2026-09-08 08:16:05', '2026-09-08 08:16:05'),
(130, 'caja.movimientos_ver', 'web', 'Ver movimientos de todas las cajas', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(131, 'caja.gestionar_estado', 'web', 'Activar o desactivar una caja', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(132, 'caja.gestionar_instrumentos', 'web', 'Asignar métodos de pago a una caja', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(134, 'caja.principales_editar', 'web', 'Editar y crear cajas hijas', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(135, 'caja.principales_estado', 'web', 'Activar o desactivar una caja principal', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(137, 'caja.movimientos_registrar', 'web', 'Registrar ingresos y egresos desde Movimientos', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(138, 'caja.cierres_consolidado', 'web', 'Generar el cuadre consolidado', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(139, 'caja.transferencias_asignar', 'web', 'Asignar fondos a una caja', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(140, 'caja.transferencias_reasignar', 'web', 'Reasignar una asignación', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(141, 'caja.transferencias_anular', 'web', 'Anular una asignación', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(142, 'caja.transferencias_rechazar', 'web', 'Rechazar una asignación', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(143, 'caja.transferencias_discrepancia', 'web', 'Resolver una discrepancia', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(144, 'caja.metodos_pago_crear', 'web', 'Crear métodos de pago', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(145, 'caja.metodos_pago_editar', 'web', 'Editar métodos de pago', '2026-09-08 08:55:13', '2026-09-08 08:55:13'),
(146, 'caja.metodos_pago_estado', 'web', 'Activar o desactivar un método de pago', '2026-09-08 08:55:13', '2026-09-08 08:55:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_cuentas`
--

CREATE TABLE `plan_cuentas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `tipo` enum('activo','pasivo','patrimonio','ingreso','costo','gasto') NOT NULL,
  `nivel` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `padre_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `plan_cuentas`
--

INSERT INTO `plan_cuentas` (`id`, `codigo`, `nombre`, `tipo`, `nivel`, `padre_id`, `estado`, `created_at`, `updated_at`) VALUES
(1, '1', 'ACTIVO', 'activo', 1, NULL, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(2, '10', 'Efectivo y Equivalentes de Efectivo', 'activo', 2, 1, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(3, '101', 'Caja', 'activo', 3, 2, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(4, '102', 'Bancos', 'activo', 3, 2, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(5, '103', 'Caja Chica', 'activo', 3, 2, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(6, '12', 'Cuentas por Cobrar Comerciales', 'activo', 2, 1, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(7, '121', 'Facturas por Cobrar', 'activo', 3, 6, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(8, '122', 'Letras por Cobrar', 'activo', 3, 6, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(9, '14', 'Cuentas por Cobrar Diversas', 'activo', 2, 1, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(10, '16', 'Existencias (Inventarios)', 'activo', 2, 1, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(11, '161', 'Mercaderías', 'activo', 3, 10, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(12, '162', 'Materias Primas', 'activo', 3, 10, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(13, '163', 'Envases y Embalajes', 'activo', 3, 10, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(14, '18', 'Servicios y Otros Contratados Anticipadamente', 'activo', 2, 1, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(15, '19', 'Estimación de Cuentas de Cobranza Dudosa', 'activo', 2, 1, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(16, '2', 'PASIVO', 'pasivo', 1, NULL, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(17, '20', 'Cuentas por Pagar Comerciales', 'pasivo', 2, 16, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(18, '201', 'Facturas por Pagar', 'pasivo', 3, 17, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(19, '202', 'Letras por Pagar', 'pasivo', 3, 17, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(20, '21', 'Cuentas por Pagar Diversas', 'pasivo', 2, 16, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(21, '23', 'Remuneraciones por Pagar', 'pasivo', 2, 16, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(22, '24', 'Tributos por Pagar', 'pasivo', 2, 16, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(23, '241', 'IGV por Pagar', 'pasivo', 3, 22, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(24, '242', 'IR por Pagar', 'pasivo', 3, 22, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(25, '3', 'PATRIMONIO', 'patrimonio', 1, NULL, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(26, '30', 'Capital', 'patrimonio', 2, 25, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(27, '301', 'Capital Social', 'patrimonio', 3, 26, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(28, '31', 'Resultados Acumulados', 'patrimonio', 2, 25, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(29, '32', 'Resultado del Ejercicio', 'patrimonio', 2, 25, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(30, '4', 'INGRESOS', 'ingreso', 1, NULL, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(31, '40', 'Ventas', 'ingreso', 2, 30, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(32, '401', 'Ventas Netas', 'ingreso', 3, 31, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(33, '402', 'Devoluciones sobre Ventas', 'ingreso', 3, 31, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(34, '41', 'Otros Ingresos', 'ingreso', 2, 30, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(35, '5', 'COSTOS', 'costo', 1, NULL, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(36, '50', 'Costo de Ventas', 'costo', 2, 35, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(37, '501', 'Costo de Mercaderías Vendidas', 'costo', 3, 36, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(38, '6', 'GASTOS', 'gasto', 1, NULL, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(39, '60', 'Gastos de Personal', 'gasto', 2, 38, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(40, '601', 'Sueldos y Salarios', 'gasto', 3, 39, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(41, '602', 'Beneficios Sociales', 'gasto', 3, 39, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(42, '61', 'Gastos de Servicios Públicos', 'gasto', 2, 38, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(43, '611', 'Electricidad', 'gasto', 3, 42, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(44, '612', 'Agua', 'gasto', 3, 42, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(45, '613', 'Teléfono e Internet', 'gasto', 3, 42, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(46, '62', 'Gastos de Alquiler', 'gasto', 2, 38, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(47, '63', 'Gastos de Transporte', 'gasto', 2, 38, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(48, '64', 'Gastos de Ventas', 'gasto', 2, 38, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(49, '65', 'Gastos Administrativos', 'gasto', 2, 38, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(50, '66', 'Gastos Financieros', 'gasto', 2, 38, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(51, '68', 'Depreciación y Amortización', 'gasto', 2, 38, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22'),
(52, '69', 'Costo de Ventas (Gasto)', 'gasto', 2, 38, 1, '2026-07-25 07:49:22', '2026-07-25 07:49:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones`
--

CREATE TABLE `presentaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `presentaciones`
--

INSERT INTO `presentaciones` (`id`, `id_empresa`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
(1, 12, '2', 1, '2026-07-08 06:53:14', '2026-07-08 06:53:14'),
(2, 12, '4', 1, '2026-07-08 06:53:14', '2026-07-08 06:53:14'),
(3, 12, 'Bolsa', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(4, 12, 'Botella', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(5, 12, 'Caja', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(6, 12, 'Frasco', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(7, 12, 'Lata', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(8, 12, 'Paquete', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(9, 12, 'Saco', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(10, 12, 'Sachet', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(11, 12, 'Sobre', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(12, 12, 'Tarro', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(13, 12, 'Dispensador', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(14, 12, 'Envase', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(15, 0, 'CAJA', 1, '2026-09-08 20:20:22', '2026-09-08 20:20:22'),
(16, 0, 'BOLSA', 1, '2026-09-08 20:20:26', '2026-09-08 20:20:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id_prestamo` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `tipo` char(1) NOT NULL,
  `tercero` varchar(150) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `almacen` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT 'P',
  `observacion` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `fecha_devolucion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id_prestamo`, `id_empresa`, `tipo`, `tercero`, `id_producto`, `almacen`, `cantidad`, `estado`, `observacion`, `id_usuario`, `fecha`, `fecha_devolucion`) VALUES
(1, 12, 'R', 'ardáis Mario', 409, '31564165', 100, 'X', 'adadadadaw', 107, '2026-07-10 15:28:52', NULL),
(2, 12, 'R', 'adadadad', 418, '31564165', 900, 'D', 'aada dawda wd', 107, '2026-07-10 15:33:45', '2026-07-10 16:20:13'),
(4, 12, 'P', 'ardáis Mario', 419, '1', 5, 'X', 'adawdawd', 111, '2026-07-22 15:26:54', NULL),
(5, 0, 'P', 'wuygfjfbse', 434, 'AL2', 1, 'D', 'sefsfe', 115, '2026-09-08 17:50:46', '2026-09-08 17:51:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamo_detalle`
--

CREATE TABLE `prestamo_detalle` (
  `id_detalle` int(10) UNSIGNED NOT NULL,
  `id_prestamo` int(10) UNSIGNED NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `prestamo_detalle`
--

INSERT INTO `prestamo_detalle` (`id_detalle`, `id_prestamo`, `id_producto`, `cantidad`, `observacion`, `created_at`, `updated_at`) VALUES
(1, 1, 409, 100, NULL, '2026-07-10 19:28:52', '2026-07-10 19:28:52'),
(2, 2, 418, 900, NULL, '2026-07-10 19:33:45', '2026-07-10 19:33:45'),
(3, 4, 419, 5, NULL, '2026-07-22 19:26:54', '2026-07-22 19:26:54'),
(4, 5, 434, 1, NULL, '2026-09-08 20:50:46', '2026-09-08 20:50:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamo_devoluciones`
--

CREATE TABLE `prestamo_devoluciones` (
  `id_devolucion` int(10) UNSIGNED NOT NULL,
  `id_prestamo` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `prestamo_devoluciones`
--

INSERT INTO `prestamo_devoluciones` (`id_devolucion`, `id_prestamo`, `id_producto`, `cantidad`, `fecha`, `id_usuario`) VALUES
(1, 1, 409, 10, '2026-07-10 15:34:43', 107),
(2, 2, 418, 1, '2026-07-10 16:15:29', 107),
(3, 2, 418, 1, '2026-07-10 16:18:53', 107),
(4, 2, 418, 898, '2026-07-10 16:20:13', 107),
(6, 4, 419, 1, '2026-07-27 19:40:32', 107),
(7, 5, 434, 1, '2026-09-08 17:51:02', 115);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `cod_barra` varchar(100) DEFAULT NULL,
  `descripcion` varchar(245) DEFAULT NULL,
  `precio` double(10,4) DEFAULT NULL,
  `costo` double(10,4) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `stock_minimo` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `stock_maximo` int(10) UNSIGNED DEFAULT NULL,
  `iscbp` int(11) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `sucursal` int(11) DEFAULT NULL,
  `ultima_salida` date NOT NULL,
  `codsunat` varchar(20) NOT NULL,
  `usar_barra` char(1) DEFAULT '0',
  `precio_mayor` double(10,4) DEFAULT NULL,
  `precio_menor` double(10,4) DEFAULT NULL,
  `peso_bruto` decimal(10,2) DEFAULT 0.00,
  `razon_social` varchar(250) DEFAULT NULL,
  `ruc` varchar(11) DEFAULT NULL,
  `estado` char(1) DEFAULT '1',
  `almacen` varchar(50) DEFAULT NULL,
  `precio2` double(10,4) DEFAULT 0.0000,
  `precio3` double(10,4) DEFAULT 0.0000,
  `precio4` double(10,4) DEFAULT 0.0000,
  `precio_unidad` double(10,4) DEFAULT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `id_subcategoria` int(11) DEFAULT NULL,
  `id_marca` int(11) DEFAULT NULL,
  `id_submarca` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `medida` varchar(100) DEFAULT 'Unidad',
  `presentaciones` varchar(100) DEFAULT NULL,
  `cnt_presenta` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `cod_barra`, `descripcion`, `precio`, `costo`, `cantidad`, `stock_minimo`, `stock_maximo`, `iscbp`, `id_empresa`, `sucursal`, `ultima_salida`, `codsunat`, `usar_barra`, `precio_mayor`, `precio_menor`, `peso_bruto`, `razon_social`, `ruc`, `estado`, `almacen`, `precio2`, `precio3`, `precio4`, `precio_unidad`, `codigo`, `id_categoria`, `id_subcategoria`, `id_marca`, `id_submarca`, `imagen`, `activo`, `medida`, `presentaciones`, `cnt_presenta`) VALUES
(409, NULL, 'JABÓN DE ROPA BELTRA*175 GMS', 45.0000, 43.0000, 88, 5, NULL, 0, 12, 1, '1000-01-01', 'Jabel0001', '0', 1.0000, 1.0000, 1.00, 'CORPORACION BELTRAN ESPINOZA E.I.R.L. - COBELES E.I.R.L.', '20602096808', '1', '2', 46.0000, 47.0000, 44.0000, 44.0000, 'Jabel0001', NULL, NULL, NULL, NULL, NULL, 1, 'Cajas', '2', '1,2,3'),
(410, '', 'AZUCAR BLANCA IMPORTADA*50K', 125.0000, 120.0000, 0, 5, NULL, 0, 12, 1, '1000-01-01', '13422', '0', 1.0000, 1.0000, 50.00, 'MANUEL QUISPE                                               ', '10099666922', '1', '1', 140.0000, 141.0000, 138.0000, 138.0000, '13422', NULL, NULL, NULL, NULL, NULL, 0, 'Unidad', '4', '1,2,3,4,5'),
(411, NULL, 'Azúcar Cartavio Blaco', 157.0000, 152.0000, 20, 5, NULL, 0, 12, 1, '1000-01-01', '13422', '0', 1.0000, 1.0000, 1.00, 'MANUEL QUISPE                                               ', '10099666922', '1', '2', 0.0000, 0.0000, 0.0000, NULL, '13422', NULL, NULL, NULL, NULL, NULL, 1, 'Sacos', '4', '1,2,3,4,5'),
(412, '', 'LENTEJA  BB VERDE *SACO VERDE', 135.0000, 120.0000, 151, 5, NULL, 0, 12, 1, '1000-01-01', '100198', '0', 1.0000, 1.0000, 45.36, 'INTERCOMPANY Y SR HUANCA                                    ', '20468985757', '1', '1', 136.0000, 137.0000, 135.0000, 133.0000, '100198', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', '4', '1,2,3,4,5,6'),
(413, NULL, 'LENTEJA ESTON USA BB VERDE *45.36K', 180.0000, 1.0000, 90, 5, NULL, 0, 12, 1, '1000-01-01', '100198', '0', 1.0000, 1.0000, 1.00, 'INTERCOMPANY Y SR HUANCA                                    ', '20468985757', '1', '2', 185.0000, 185.0000, 177.0000, 175.0000, '100198', NULL, NULL, NULL, NULL, NULL, 1, 'Sacos', '4', '1,2,3,4,5,6'),
(414, '', 'ARROZ EXTRA SACO 10KG (TEST)', 42.0000, 35.0000, 1043, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 10.50, NULL, NULL, '1', '1', 0.0000, 0.0000, 0.0000, NULL, 'DESP-001', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(415, '', 'AZUCAR RUBIA BOLSA 5KG (TEST)', 24.5000, 19.0000, 700, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 5.20, NULL, NULL, '1', '1', 0.0000, 0.0000, 0.0000, NULL, 'DESP-002', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(416, '', 'ACEITE VEGETAL CAJA 12X1L (TEST)', 96.0000, 78.0000, 40, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 12.80, NULL, NULL, '1', '1', 0.0000, 0.0000, 0.0000, NULL, 'DESP-003', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(417, '', 'HARINA PREPARADA BOLSA 1KG (TEST)', 6.5000, 4.8000, 998, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 1.10, NULL, NULL, '1', '1', 0.0000, 0.0000, 0.0000, NULL, 'DESP-004', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(418, '', 'LECHE EVAPORADA PACK 48UND (TEST)', 168.0000, 140.0000, 997, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 20.40, NULL, NULL, '1', '1', 0.0000, 0.0000, 0.0000, NULL, 'DESP-005', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(419, '23135485', 'ACEITE RICOSOL 1 L                           ', 120.0000, 80.0000, 5, 8, 10, NULL, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 12.00, NULL, NULL, '1', NULL, 0.0000, 0.0000, 0.0000, NULL, '31564165', 4, 9, 4, 6, NULL, 1, 'Litro', 'Caja', '12'),
(420, '23135485', 'ACEITE RICOSOL 1 L                           ', 120.0000, 10.0000, 5, 5, NULL, NULL, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 12.00, NULL, NULL, '1', '31564165', 0.0000, 0.0000, 0.0000, NULL, '31564165', 4, 9, 4, 6, 'productos/01KX67P0ZFQ58A3J3JNP30PANM.jpg', 1, 'Litro', 'Caja', '12'),
(421, '', 'ARROZ EXTRA SACO 10KG (TEST)', 42.0000, 35.0000, 80, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 10.50, NULL, NULL, '1', '31564165', 0.0000, 0.0000, 0.0000, NULL, 'DESP-001', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(422, '', 'ACEITE VEGETAL CAJA 12X1L (TEST)', 96.0000, 78.0000, 900, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 12.80, NULL, NULL, '1', '31564165', 0.0000, 0.0000, 0.0000, NULL, 'DESP-003', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(423, '', 'LECHE EVAPORADA PACK 48UND (TEST)', 168.0000, 140.0000, 0, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 20.40, NULL, NULL, '1', '31564165', 0.0000, 0.0000, 0.0000, NULL, 'DESP-005', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(424, '', 'LENTEJA  BB VERDE *SACO VERDE', 135.0000, 120.0000, 300, 5, NULL, 0, 12, 1, '1000-01-01', '100198', '0', 1.0000, 1.0000, 45.36, 'INTERCOMPANY Y SR HUANCA                                    ', '20468985757', '1', '31564165', 136.0000, 137.0000, 135.0000, 133.0000, '100198', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', '4', '1,2,3,4,5,6'),
(425, '', 'AZUCAR RUBIA BOLSA 5KG (TEST)', 24.5000, 19.0000, 250, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 5.20, NULL, NULL, '1', '31564165', 0.0000, 0.0000, 0.0000, NULL, 'DESP-002', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(426, NULL, 'Azúcar Cartavio Blaco', 157.0000, 152.0000, 900, 5, NULL, 0, 12, 1, '1000-01-01', '13422', '0', 1.0000, 1.0000, 1.00, 'MANUEL QUISPE                                               ', '10099666922', '1', '31564165', 0.0000, 0.0000, 0.0000, NULL, '13422', NULL, NULL, NULL, NULL, NULL, 1, 'Sacos', '4', '1,2,3,4,5'),
(427, '', 'ACEITE VEGETAL CAJA 12X1L (TEST)', 96.0000, 78.0000, 50, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 12.80, NULL, NULL, '1', '2', 0.0000, 0.0000, 0.0000, NULL, 'DESP-003', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(428, '', 'AZUCAR RUBIA BOLSA 5KG (TEST)', 24.5000, 19.0000, 50, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 5.20, NULL, NULL, '1', '2', 0.0000, 0.0000, 0.0000, NULL, 'DESP-002', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(429, '23135485', 'ACEITE RICOSOL 1 L                           ', 120.0000, 80.0000, 7, 5, NULL, NULL, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 12.00, NULL, NULL, '1', '1', 0.0000, 0.0000, 0.0000, NULL, '31564165', 4, 9, 4, 6, 'productos/01KX67P0ZFQ58A3J3JNP30PANM.jpg', 1, 'Litro', 'Caja', '12'),
(430, '', 'ACEITE VEGETAL CAJA 12X1L (TEST)', 96.0000, 78.0000, 5, 5, NULL, 0, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 12.80, NULL, NULL, '1', '1', 0.0000, 0.0000, 0.0000, NULL, 'DESP-003', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', NULL, NULL),
(431, '23135485', 'ACEITE RICOSOL 1 L                           ', 120.0000, 10.0000, 5, 5, NULL, NULL, 12, 1, '2026-07-10', '-', '0', NULL, NULL, 12.00, NULL, NULL, '1', 'aedawdawd', 0.0000, 0.0000, 0.0000, NULL, '31564165', 4, 9, 4, 6, 'productos/01KX67P0ZFQ58A3J3JNP30PANM.jpg', 1, 'Litro', 'Caja', '12'),
(432, NULL, 'MOLITATIA TORNILLO 76 20x250 GR', 30.0000, 25.6100, NULL, 50, 100, NULL, 0, 1, '2026-09-08', '-', '0', NULL, NULL, 5.00, NULL, NULL, '1', NULL, 0.0000, 0.0000, 0.0000, NULL, '80105', 10, 24, 10, 19, NULL, 1, 'CAJA POMAROLA ', 'BOLSA', '20'),
(433, NULL, 'MOLITATIA TORNILLO 76 20x250 GR', 30.0000, 25.6100, 0, 50, 100, NULL, 0, 1, '2026-09-08', '-', '0', NULL, NULL, 5.00, NULL, NULL, '1', '80105', 0.0000, 0.0000, 0.0000, NULL, '80105', 10, 24, 10, 19, NULL, 1, 'CAJA POMAROLA ', 'BOLSA', '20'),
(434, NULL, 'MOLITATIA TORNILLO 76 20x250 GR', 30.0000, 25.6100, 44, 50, 100, NULL, 0, 1, '2026-09-08', '-', '0', NULL, NULL, 5.00, NULL, NULL, '1', '80105', 0.0000, 0.0000, 0.0000, NULL, '80105', 10, 24, 10, 19, NULL, 1, 'CAJA POMAROLA ', 'BOLSA', '20'),
(435, NULL, 'MOLITATIA TORNILLO 76 20x250 GR', 30.0000, 25.6100, 4, 50, 100, NULL, 0, 1, '2026-09-08', '-', '0', NULL, NULL, 5.00, NULL, NULL, '1', 'AL2', 0.0000, 0.0000, 0.0000, NULL, '80105', 10, 24, 10, 19, NULL, 1, 'CAJA POMAROLA ', 'BOLSA', '20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_compras`
--

CREATE TABLE `productos_compras` (
  `id_producto_venta` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `cantidad` varchar(50) DEFAULT NULL,
  `precio` double(10,3) DEFAULT NULL,
  `costo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `productos_compras`
--

INSERT INTO `productos_compras` (`id_producto_venta`, `id_producto`, `id_compra`, `cantidad`, `precio`, `costo`) VALUES
(242, 319, 111, '1000', 4.900, NULL),
(243, 129, 111, '500', 3.000, NULL),
(254, 129, 118, '500', 3.100, NULL),
(255, 319, 118, '1000', 5.300, NULL),
(256, 222, 118, '550', 5.400, NULL),
(302, 236, 134, '1000', 2.500, NULL),
(303, 238, 134, '1000', 3.800, NULL),
(304, 335, 134, '500', 3.900, NULL),
(327, 129, 142, '1000', 3.100, NULL),
(383, 85, 155, '340', 161.000, NULL),
(386, 9, 157, '500', 62.000, NULL),
(399, 414, 162, '80', 35.000, '35'),
(400, 411, 163, '900', 152.000, '152'),
(401, 414, 164, '1', 35.000, '35'),
(402, 414, 165, '100', 35.000, '35'),
(403, 429, 166, '10', 10.000, '10'),
(404, 432, 167, '1', 25.610, '25.61');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_cotis`
--

CREATE TABLE `productos_cotis` (
  `prod_coti_id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_coti` int(11) NOT NULL,
  `cantidad` double(6,2) DEFAULT NULL,
  `precio` double(10,5) DEFAULT NULL,
  `precio_producto` decimal(10,5) DEFAULT NULL,
  `name_precio_producto` text DEFAULT NULL,
  `costo` double(10,5) DEFAULT NULL,
  `medida` varchar(100) DEFAULT NULL,
  `presenta` varchar(100) DEFAULT NULL,
  `presenta_cnt` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `productos_cotis`
--

INSERT INTO `productos_cotis` (`prod_coti_id`, `id_producto`, `id_coti`, `cantidad`, `precio`, `precio_producto`, `name_precio_producto`, `costo`, `medida`, `presenta`, `presenta_cnt`, `fecha_registro`, `id_usuario`) VALUES
(34466, 120, 6193, 1.00, 33.50000, 6.70000, 'precio4', 7.50000, 'Kilos', '1', 5, '2025-03-28 12:28:47', 63),
(34467, 293, 6193, 1.00, 33.00000, 11.00000, 'precio', 9.20000, 'Kilos', '1', 3, '2025-03-28 12:28:47', 63),
(34468, 320, 6194, 1.00, 14.00000, 2.80000, 'precio', 2.20000, 'Kilos', '1', 5, '2025-03-28 12:30:51', 63),
(34496, 236, 6197, 1.00, 9.90000, 3.30000, 'precio', 2.90000, 'Kilos', '1', 3, '2025-03-28 12:48:25', 62),
(34497, 238, 6197, 1.00, 13.50000, 4.50000, 'precio', 4.10000, 'Kilos', '1', 3, '2025-03-28 12:48:25', 62),
(34498, 342, 6197, 1.00, 31.50000, 10.50000, 'precio', 9.70000, 'Kilos', '1', 3, '2025-03-28 12:48:25', 62),
(34499, 120, 6197, 1.00, 20.10000, 6.70000, 'precio4', 7.50000, 'Kilos', '1', 3, '2025-03-28 12:48:25', 62),
(34500, 319, 6197, 1.00, 20.10000, 6.70000, 'sin referencia', 6.00000, 'Kilos', '1', 3, '2025-03-28 12:48:25', 62),
(34501, 285, 6197, 1.00, 16.50000, 5.50000, 'precio', 4.60000, 'Kilos', '1', 3, '2025-03-28 12:48:25', 62),
(34502, 336, 6197, 1.00, 12.60000, 4.20000, 'precio', 3.50000, 'Kilos', '', 3, '2025-03-28 12:48:25', 62),
(41409, 290, 6195, 1.00, 60.00000, 6.00000, 'precio', 5.20000, 'Kilos', '1', 10, '2025-03-28 12:40:56', 62),
(41410, 92, 6195, 1.00, 47.00000, 4.70000, 'precio', 4.40000, 'Kilos', '1', 10, '2025-03-28 12:40:56', 62),
(41411, 285, 6195, 1.00, 55.00000, 5.50000, 'precio', 4.60000, 'Kilos', '1', 10, '2025-03-28 12:40:56', 62),
(41412, 96, 6195, 1.00, 40.00000, 40.00000, 'sin referencia', 40.00000, 'Unidad', '1', 1, '2025-03-28 12:40:56', 62),
(41413, 120, 6195, 1.00, 67.00000, 6.70000, 'precio4', 7.50000, 'Kilos', '1', 10, '2025-03-28 12:40:56', 62),
(41414, 342, 6195, 1.00, 105.00000, 10.50000, 'precio', 9.70000, 'Kilos', '1', 10, '2025-03-28 12:40:56', 62),
(41415, 258, 6195, 1.00, 22.50000, 4.50000, 'precio', 4.20000, 'Kilos', '1', 5, '2025-03-28 12:40:56', 62),
(41416, 257, 6195, 1.00, 22.50000, 4.50000, 'precio', 3.80000, 'Kilos', '1', 5, '2025-03-28 12:40:56', 62),
(41417, 29, 6195, 1.00, 45.00000, 9.00000, 'sin referencia', 9.00000, 'Kilos', '', 5, '2025-03-28 12:40:56', 62),
(41434, 282, 6196, 1.00, 19.50000, 6.50000, 'precio', 6.00000, 'Kilos', '1', 3, '2025-03-28 12:42:33', 63),
(41435, 285, 6196, 1.00, 27.50000, 5.50000, 'precio', 4.60000, 'Kilos', '1', 5, '2025-03-28 12:42:33', 63),
(41436, 310, 6196, 1.00, 12.00000, 4.00000, 'precio', 3.50000, 'Kilos', '1', 3, '2025-03-28 12:42:33', 63),
(41437, 142, 6196, 1.00, 19.50000, 6.50000, 'precio', 6.20000, 'Kilos', '', 3, '2025-03-28 12:42:33', 63),
(41438, 12, 6196, 1.00, 69.00000, 69.00000, 'precio2', 68.00000, 'Unidad', '', 1, '2025-03-28 12:42:33', 63),
(41439, 352, 6196, 1.00, 20.00000, 20.00000, 'precio', 17.00000, 'Unidad', '', 1, '2025-03-28 12:42:33', 63),
(41440, 306, 6196, 1.00, 28.50000, 9.50000, 'precio3', 9.00000, 'Kilos', '', 3, '2025-03-28 12:42:33', 63),
(485176, 410, 51470, 1.00, 125.00000, NULL, NULL, 120.00000, 'Unidad', '1', 1, '2026-07-08 12:31:57', 107),
(485177, 410, 51471, 1.00, 125.00000, NULL, NULL, 120.00000, 'Unidad', '1', 1, '2026-07-09 14:53:10', 107),
(485178, 414, 51472, 2.00, 42.00000, NULL, NULL, 35.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485179, 415, 51473, 3.00, 24.50000, NULL, NULL, 19.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485180, 416, 51473, 6.00, 96.00000, NULL, NULL, 78.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485181, 416, 51474, 4.00, 96.00000, NULL, NULL, 78.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485182, 417, 51474, 7.00, 6.50000, NULL, NULL, 4.80000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485183, 418, 51474, 10.00, 168.00000, NULL, NULL, 140.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485184, 417, 51475, 5.00, 6.50000, NULL, NULL, 4.80000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485185, 418, 51476, 6.00, 168.00000, NULL, NULL, 140.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485186, 414, 51476, 9.00, 42.00000, NULL, NULL, 35.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485187, 414, 51477, 7.00, 42.00000, NULL, NULL, 35.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485188, 415, 51477, 10.00, 24.50000, NULL, NULL, 19.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485189, 416, 51477, 4.00, 96.00000, NULL, NULL, 78.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485190, 415, 51478, 8.00, 24.50000, NULL, NULL, 19.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485191, 416, 51479, 9.00, 96.00000, NULL, NULL, 78.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485192, 417, 51479, 3.00, 6.50000, NULL, NULL, 4.80000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485193, 417, 51480, 10.00, 6.50000, NULL, NULL, 4.80000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485194, 418, 51480, 4.00, 168.00000, NULL, NULL, 140.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485195, 414, 51480, 7.00, 42.00000, NULL, NULL, 35.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485196, 418, 51481, 2.00, 168.00000, NULL, NULL, 140.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485197, 414, 51482, 3.00, 42.00000, NULL, NULL, 35.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485198, 415, 51482, 6.00, 24.50000, NULL, NULL, 19.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485199, 415, 51483, 4.00, 24.50000, NULL, NULL, 19.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485200, 416, 51483, 7.00, 96.00000, NULL, NULL, 78.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485201, 417, 51483, 10.00, 6.50000, NULL, NULL, 4.80000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485202, 416, 51484, 5.00, 96.00000, NULL, NULL, 78.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485203, 417, 51485, 6.00, 6.50000, NULL, NULL, 4.80000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485204, 418, 51485, 9.00, 168.00000, NULL, NULL, 140.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485205, 418, 51486, 7.00, 168.00000, NULL, NULL, 140.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485206, 414, 51486, 10.00, 42.00000, NULL, NULL, 35.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485207, 415, 51486, 4.00, 24.50000, NULL, NULL, 19.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485208, 414, 51487, 8.00, 42.00000, NULL, NULL, 35.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485209, 415, 51488, 9.00, 24.50000, NULL, NULL, 19.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485210, 416, 51488, 3.00, 96.00000, NULL, NULL, 78.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485211, 416, 51489, 10.00, 96.00000, NULL, NULL, 78.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485212, 417, 51489, 4.00, 6.50000, NULL, NULL, 4.80000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485213, 418, 51489, 7.00, 168.00000, NULL, NULL, 140.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485214, 417, 51490, 2.00, 6.50000, NULL, NULL, 4.80000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485215, 418, 51491, 3.00, 168.00000, NULL, NULL, 140.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485216, 414, 51491, 6.00, 42.00000, NULL, NULL, 35.00000, 'Unidad', '1', 1, '2026-07-10 01:43:05', 108),
(485217, 414, 51492, 1.00, 42.00000, NULL, NULL, 35.00000, 'Unidad', '1', 1, '2026-07-10 04:31:37', 108),
(485218, 410, 51492, 10.00, 125.00000, NULL, NULL, 120.00000, 'Unidad', '1', 1, '2026-07-10 04:31:37', 108),
(485219, 413, 51493, 10.00, 180.00000, NULL, NULL, 1.00000, 'Sacos', '1', 1, '2026-07-10 04:53:10', 108),
(485220, 410, 51494, 10.00, 125.00000, NULL, NULL, 120.00000, 'Unidad', '1', 1, '2026-07-10 05:02:34', 108),
(485221, 416, 51495, 10.00, 96.00000, NULL, NULL, 78.00000, 'Unidad', '1', 1, '2026-07-10 05:11:27', 108),
(485222, 409, 51496, 1.00, 45.00000, NULL, NULL, 43.00000, 'Cajas', '1', 1, '2026-07-21 13:58:03', 107),
(485223, 409, 51497, 1.00, 45.00000, NULL, NULL, 43.00000, 'Cajas', '1', 1, '2026-07-22 16:03:29', 107);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_ventas`
--

CREATE TABLE `productos_ventas` (
  `id_producto` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `id_venta` int(11) NOT NULL,
  `cantidad` double(6,2) DEFAULT NULL,
  `precio` double(10,5) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `igv_prod` tinyint(4) NOT NULL DEFAULT 0,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `costo` double(10,5) DEFAULT NULL,
  `precio_usado` char(1) DEFAULT NULL,
  `medida` varchar(100) DEFAULT NULL,
  `presenta` varchar(100) DEFAULT NULL,
  `presenta_cnt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `productos_ventas`
--

INSERT INTO `productos_ventas` (`id_producto`, `descripcion`, `id_venta`, `cantidad`, `precio`, `total`, `igv_prod`, `descuento`, `costo`, `precio_usado`, `medida`, `presenta`, `presenta_cnt`) VALUES
(412, NULL, 229, 10.00, 150.00000, 0.00, 0, 0.00, 120.00000, '5', 'Unidad', '4', 1),
(367, NULL, 231, 1.00, 340.00000, 0.00, 0, 0.00, 33.00000, '5', 'Unidad', '1', 10),
(16, NULL, 233, 1.00, 268.00000, 0.00, 0, 0.00, 69.00000, NULL, 'Unidad', '1', 4),
(406, NULL, 233, 1.00, 134.40000, 0.00, 0, 0.00, 2.80000, NULL, 'Unidad', '1', 48),
(367, NULL, 233, 1.00, 66.00000, 0.00, 0, 0.00, 33.00000, NULL, 'Unidad', '1', 2),
(37, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1),
(40, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1),
(36, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1),
(116, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1),
(44, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1),
(39, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1),
(319, NULL, 233, 1.00, 32.50000, 0.00, 0, 0.00, 6.10000, NULL, 'Kilos', '1', 5),
(383, NULL, 233, 1.00, 20.00000, 0.00, 0, 0.00, 3.60000, NULL, 'Kilos', '1', 5),
(176, NULL, 233, 1.00, 37.50000, 0.00, 0, 0.00, 6.00000, NULL, 'Kilos', '1', 5),
(134, NULL, 233, 1.00, 12.00000, 0.00, 0, 0.00, 10.00000, NULL, 'Sacos', '1', 1),
(331, NULL, 233, 1.00, 19.50000, 0.00, 0, 0.00, 6.10000, NULL, 'Kilos', '1', 3),
(172, NULL, 233, 1.00, 11.40000, 0.00, 0, 0.00, 3.40000, NULL, 'Kilos', '1', 3),
(290, NULL, 233, 1.00, 42.00000, 0.00, 0, 0.00, 3.80000, NULL, 'Kilos', '1', 10),
(89, NULL, 233, 1.00, 205.00000, 0.00, 0, 0.00, 172.00000, NULL, 'Unidad', '1', 1),
(284, NULL, 233, 1.00, 140.00000, 0.00, 0, 0.00, 140.00000, NULL, 'Unidad', '1', 1),
(123, NULL, 233, 1.00, 50.00000, 0.00, 0, 0.00, 5.50000, NULL, 'Kilos', '1', 10),
(102, NULL, 233, 1.00, 45.00000, 0.00, 0, 0.00, 72.00000, NULL, 'Unidad', '1', 1),
(120, NULL, 233, 1.00, 82.00000, 0.00, 0, 0.00, 5.80000, NULL, 'Kilos', '1', 10),
(346, NULL, 233, 1.00, 72.00000, 0.00, 0, 0.00, 28.50000, NULL, 'Unidad', '1', 2),
(12, NULL, 233, 1.00, 132.00000, 0.00, 0, 0.00, 68.00000, NULL, 'Unidad', '1', 2),
(165, NULL, 233, 82.00, 10.00000, 0.00, 0, 0.00, 14.00000, NULL, 'Sacos', '1', 1),
(342, NULL, 234, 1.00, 108.00000, 0.00, 0, 0.00, 10.00000, NULL, 'Kilos', '1', 10),
(298, NULL, 234, 1.00, 23.10000, 0.00, 0, 0.00, 5.80000, NULL, 'Kilos', '1', 3),
(310, NULL, 234, 1.00, 24.00000, 0.00, 0, 0.00, 3.50000, NULL, 'Kilos', '1', 5),
(311, NULL, 234, 1.00, 24.00000, 0.00, 0, 0.00, 3.50000, NULL, 'Kilos', '1', 5),
(129, NULL, 234, 1.00, 19.00000, 0.00, 0, 0.00, 3.40000, NULL, 'Kilos', '1', 5),
(412, NULL, 234, 1.00, 135.00000, 0.00, 0, 0.00, 120.00000, NULL, 'Unidad', '', 1),
(241, NULL, 234, -3.00, 14.00000, 0.00, 0, 0.00, 2.40000, NULL, 'Kilos', '', 5),
(7, 'ACEITE DEL CAMPO *1LT', 236, 1.00, 74.00000, 74.00, 0, 0.00, NULL, NULL, NULL, NULL, NULL),
(410, 'AZUCAR BLANCA IMPORTADA*50K', 237, 1.00, 125.00000, 125.00, 0, 0.00, 120.00000, NULL, NULL, NULL, NULL),
(410, 'AZUCAR BLANCA IMPORTADA*50K', 238, 1.00, 125.00000, 125.00, 0, 0.00, 120.00000, NULL, NULL, NULL, NULL),
(410, 'AZUCAR BLANCA IMPORTADA*50K', 239, 1.00, 125.00000, 125.00, 0, 0.00, 120.00000, NULL, NULL, NULL, NULL),
(418, 'LECHE EVAPORADA PACK 48UND (TEST)', 240, 3.00, 168.00000, 504.00, 0, 0.00, 140.00000, NULL, NULL, NULL, NULL),
(414, 'ARROZ EXTRA SACO 10KG (TEST)', 240, 6.00, 42.00000, 252.00, 0, 0.00, 35.00000, NULL, NULL, NULL, NULL),
(417, 'HARINA PREPARADA BOLSA 1KG (TEST)', 241, 2.00, 6.50000, 13.00, 0, 0.00, 4.80000, NULL, NULL, NULL, NULL),
(414, NULL, 242, 2.00, 42.00000, 0.00, 0, 0.00, 35.00000, NULL, 'Unidad', '1', 1),
(415, NULL, 243, 3.00, 24.50000, 0.00, 0, 0.00, 19.00000, NULL, 'Unidad', '1', 1),
(416, NULL, 243, 6.00, 96.00000, 0.00, 0, 0.00, 78.00000, NULL, 'Unidad', '1', 1),
(416, NULL, 244, 4.00, 96.00000, 0.00, 0, 0.00, 78.00000, NULL, 'Unidad', '1', 1),
(417, NULL, 244, 7.00, 6.50000, 0.00, 0, 0.00, 4.80000, NULL, 'Unidad', '1', 1),
(418, NULL, 244, 10.00, 168.00000, 0.00, 0, 0.00, 140.00000, NULL, 'Unidad', '1', 1),
(417, NULL, 245, 5.00, 6.50000, 0.00, 0, 0.00, 4.80000, NULL, 'Unidad', '1', 1),
(418, NULL, 246, 6.00, 168.00000, 0.00, 0, 0.00, 140.00000, NULL, 'Unidad', '1', 1),
(414, NULL, 246, 9.00, 42.00000, 0.00, 0, 0.00, 35.00000, NULL, 'Unidad', '1', 1),
(414, NULL, 247, 7.00, 42.00000, 0.00, 0, 0.00, 35.00000, NULL, 'Unidad', '1', 1),
(415, NULL, 247, 10.00, 24.50000, 0.00, 0, 0.00, 19.00000, NULL, 'Unidad', '1', 1),
(416, NULL, 247, 4.00, 96.00000, 0.00, 0, 0.00, 78.00000, NULL, 'Unidad', '1', 1),
(415, NULL, 248, 8.00, 24.50000, 0.00, 0, 0.00, 19.00000, NULL, 'Unidad', '1', 1),
(416, NULL, 249, 9.00, 96.00000, 0.00, 0, 0.00, 78.00000, NULL, 'Unidad', '1', 1),
(417, NULL, 249, 3.00, 6.50000, 0.00, 0, 0.00, 4.80000, NULL, 'Unidad', '1', 1),
(417, NULL, 250, 10.00, 6.50000, 0.00, 0, 0.00, 4.80000, NULL, 'Unidad', '1', 1),
(418, NULL, 250, 4.00, 168.00000, 0.00, 0, 0.00, 140.00000, NULL, 'Unidad', '1', 1),
(414, NULL, 250, 7.00, 42.00000, 0.00, 0, 0.00, 35.00000, NULL, 'Unidad', '1', 1),
(418, NULL, 251, 2.00, 168.00000, 0.00, 0, 0.00, 140.00000, NULL, 'Unidad', '1', 1),
(414, NULL, 252, 3.00, 42.00000, 0.00, 0, 0.00, 35.00000, NULL, 'Unidad', '1', 1),
(415, NULL, 252, 6.00, 24.50000, 0.00, 0, 0.00, 19.00000, NULL, 'Unidad', '1', 1),
(415, NULL, 253, 4.00, 24.50000, 0.00, 0, 0.00, 19.00000, NULL, 'Unidad', '1', 1),
(416, NULL, 253, 7.00, 96.00000, 0.00, 0, 0.00, 78.00000, NULL, 'Unidad', '1', 1),
(417, NULL, 253, 10.00, 6.50000, 0.00, 0, 0.00, 4.80000, NULL, 'Unidad', '1', 1),
(416, NULL, 254, 5.00, 96.00000, 0.00, 0, 0.00, 78.00000, NULL, 'Unidad', '1', 1),
(417, NULL, 255, 6.00, 6.50000, 0.00, 0, 0.00, 4.80000, NULL, 'Unidad', '1', 1),
(418, NULL, 255, 9.00, 168.00000, 0.00, 0, 0.00, 140.00000, NULL, 'Unidad', '1', 1),
(418, NULL, 256, 7.00, 168.00000, 0.00, 0, 0.00, 140.00000, NULL, 'Unidad', '1', 1),
(414, NULL, 256, 10.00, 42.00000, 0.00, 0, 0.00, 35.00000, NULL, 'Unidad', '1', 1),
(415, NULL, 256, 4.00, 24.50000, 0.00, 0, 0.00, 19.00000, NULL, 'Unidad', '1', 1),
(414, NULL, 257, 8.00, 42.00000, 0.00, 0, 0.00, 35.00000, NULL, 'Unidad', '1', 1),
(415, NULL, 258, 9.00, 24.50000, 0.00, 0, 0.00, 19.00000, NULL, 'Unidad', '1', 1),
(416, NULL, 258, 3.00, 96.00000, 0.00, 0, 0.00, 78.00000, NULL, 'Unidad', '1', 1),
(416, NULL, 259, 10.00, 96.00000, 0.00, 0, 0.00, 78.00000, NULL, 'Unidad', '1', 1),
(417, NULL, 259, 4.00, 6.50000, 0.00, 0, 0.00, 4.80000, NULL, 'Unidad', '1', 1),
(418, NULL, 259, 7.00, 168.00000, 0.00, 0, 0.00, 140.00000, NULL, 'Unidad', '1', 1),
(414, 'ARROZ EXTRA SACO 10KG (TEST)', 260, 1.00, 42.00000, 42.00, 0, 0.00, 35.00000, NULL, NULL, NULL, NULL),
(410, 'AZUCAR BLANCA IMPORTADA*50K', 260, 10.00, 125.00000, 1250.00, 0, 0.00, 120.00000, NULL, NULL, NULL, NULL),
(413, 'LENTEJA ESTON USA BB VERDE *45.36K', 261, 10.00, 180.00000, 1800.00, 0, 0.00, 1.00000, NULL, NULL, NULL, NULL),
(410, 'AZUCAR BLANCA IMPORTADA*50K', 262, 8.00, 125.00000, 1000.00, 0, 0.00, 120.00000, NULL, NULL, NULL, NULL),
(416, 'ACEITE VEGETAL CAJA 12X1L (TEST)', 263, 10.00, 96.00000, 960.00, 0, 0.00, 78.00000, NULL, NULL, NULL, NULL),
(411, 'Azúcar Cartavio Blaco', 264, 1.00, 157.00000, 157.00, 0, 0.00, 152.00000, NULL, NULL, NULL, NULL),
(409, 'JABÓN DE ROPA BELTRA*175 GMS', 265, 1.00, 45.00000, 45.00, 0, 0.00, 43.00000, NULL, NULL, NULL, NULL),
(409, 'JABÓN DE ROPA BELTRA*175 GMS', 266, 1.00, 45.00000, 45.00, 0, 0.00, 43.00000, NULL, NULL, NULL, NULL),
(409, 'JABÓN DE ROPA BELTRA*175 GMS', 267, 1.00, 45.00000, 45.00, 0, 0.00, 43.00000, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `proveedor_id` int(11) NOT NULL,
  `ruc` varchar(11) DEFAULT NULL,
  `razon_social` varchar(200) DEFAULT NULL,
  `nombre_comercial` varchar(255) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `direccion2` varchar(250) NOT NULL,
  `telefono` varchar(100) DEFAULT '',
  `telefono2` varchar(250) NOT NULL,
  `email` varchar(150) DEFAULT '',
  `id_empresa` int(11) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `distrito` varchar(100) DEFAULT NULL,
  `ubigeo` varchar(100) DEFAULT NULL,
  `fecha_create` timestamp NULL DEFAULT current_timestamp(),
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`proveedor_id`, `ruc`, `razon_social`, `nombre_comercial`, `direccion`, `direccion2`, `telefono`, `telefono2`, `email`, `id_empresa`, `departamento`, `provincia`, `distrito`, `ubigeo`, `fecha_create`, `estado`) VALUES
(181, '07372103', 'ALBERTO HUARCAYA AYAUJA', 'HUARCAYA PALLAR ICA', 'MI CASAS', 'ica', '993321920', '', 'adan2025zapata@gmail.com', 12, NULL, NULL, NULL, NULL, '2026-02-18 19:12:25', 1),
(182, '10711354528', 'PORRAS SALAZAR MAYCOL OSBIN', 'Molino Paolo', 'av francisco pizarro 663 rimac', 'av francisco pizarro 663 rimac', '933121328', '983834950', 'VICTORIA                                                    ', 12, NULL, NULL, NULL, NULL, '2026-04-22 03:30:17', 1),
(183, '20613884506', 'COMERCIALIZADORA BARGAR S.A.C.', 'RIO BRANCO', 'JR. JORGE CHAVEZ NRO. 1230 BAR. HUAYCO SAN MARTIN SAN MARTIN TARAPOTO', '', '991675159', '', 'dextreaguilar@hotmail.com', 12, NULL, NULL, NULL, NULL, '2026-04-30 14:36:13', 1),
(184, '20612185019', 'EL BUEN SABOR DEL ORIENTE S.A.C.', 'RIKICHA', 'JR. OASIS DE VILLA EL SALVADO NRO. 51 LIMA LIMA VILLA EL SALVADOR', '', '997998870', '978652581', 'rodrigoyarleque7@gmail.com', 12, NULL, NULL, NULL, NULL, '2026-05-01 14:24:28', 1),
(186, '99999999999', 'Proveedor Test 2', 'Test Comercial 2', 'Av. Test 456', '', '111222333', '', 'test2@proveedor.com', 12, NULL, NULL, NULL, NULL, '2026-06-26 03:30:18', 1),
(187, '76165962', 'VICTOR RAUL CANCHARI RIQUI', 'victor 2', 'aaaaaaaaaaaaaaaa sad adwd ', '', '92670321', '', 'vcanchari38@gmail.com', 12, NULL, NULL, NULL, NULL, '2026-07-22 19:04:37', 1),
(188, '20100035121', 'MOLITALIA S.A', 'MOLITLIA', 'AV. VENEZUELA NRO. 2850 URB. ELIO LIMA LIMA LIMA, LIMA, LIMA, LIMA', '', '923654727', '', 'JCASACHAGUA@MOLITALIA.COM.PE', 0, NULL, NULL, NULL, NULL, '2026-09-08 20:35:19', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepciones`
--

CREATE TABLE `recepciones` (
  `id_recepcion` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `almacen` varchar(50) NOT NULL,
  `fecha` datetime NOT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `recepciones`
--

INSERT INTO `recepciones` (`id_recepcion`, `id_empresa`, `id_compra`, `almacen`, `fecha`, `observacion`, `id_usuario`) VALUES
(3, 12, 162, '31564165', '2026-07-10 15:27:17', NULL, 107),
(4, 12, 163, '31564165', '2026-07-10 17:08:27', NULL, 107),
(5, 12, 165, '1', '2026-07-22 15:21:02', NULL, 111),
(6, 12, 166, 'aedawdawd', '2026-07-27 23:10:38', NULL, 107);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepcion_detalle`
--

CREATE TABLE `recepcion_detalle` (
  `id_detalle` int(10) UNSIGNED NOT NULL,
  `id_recepcion` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `recepcion_detalle`
--

INSERT INTO `recepcion_detalle` (`id_detalle`, `id_recepcion`, `id_producto`, `cantidad`) VALUES
(1, 3, 414, 80),
(2, 4, 411, 900),
(3, 5, 414, 50),
(4, 6, 429, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_caja_vendedor`
--

CREATE TABLE `registros_caja_vendedor` (
  `registro_id` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `sucursal` int(11) NOT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `detalle` varchar(255) DEFAULT NULL,
  `cobros_efectivo` decimal(10,2) DEFAULT 0.00,
  `cobros_banco` decimal(10,2) DEFAULT 0.00,
  `total_cobrado` decimal(10,2) DEFAULT 0.00,
  `ingresos_efectivo` decimal(10,2) DEFAULT 0.00,
  `egresos_efectivo` decimal(10,2) DEFAULT 0.00,
  `debia_traer` decimal(10,2) DEFAULT 0.00,
  `saldo_efectivo` decimal(10,2) DEFAULT 0.00,
  `diferencia` decimal(10,2) DEFAULT 0.00,
  `estado_cuadre` varchar(20) DEFAULT NULL COMMENT 'CUADRA, FALTA, SOBRA',
  `id_caja_empresa` int(11) DEFAULT NULL COMMENT 'Referencia a la caja original',
  `usuario_registro` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_diario`
--

CREATE TABLE `resumen_diario` (
  `id_resumen_diario` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `ticket` varchar(45) DEFAULT NULL,
  `cantidad_items` int(11) DEFAULT NULL,
  `tipo` int(11) DEFAULT NULL COMMENT '1 para resumen\n2 para comunicacion de baja'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `rol_id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`rol_id`, `nombre`) VALUES
(1, 'ADMIN'),
(3, 'VENDEDOR'),
(4, 'CAJERO'),
(5, 'CONTADOR'),
(6, 'ALMACEN'),
(7, 'VENDEDOR-prueba'),
(8, 'prueba'),
(9, 'auxiliar de reparto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 3),
(1, 4),
(1, 5),
(1, 8),
(1, 9),
(2, 1),
(2, 3),
(2, 8),
(2, 9),
(3, 1),
(3, 8),
(3, 9),
(4, 1),
(4, 5),
(4, 6),
(4, 7),
(4, 8),
(4, 9),
(5, 1),
(5, 6),
(5, 7),
(5, 8),
(5, 9),
(6, 1),
(6, 3),
(6, 4),
(6, 7),
(6, 9),
(7, 1),
(7, 3),
(7, 7),
(7, 9),
(8, 1),
(8, 7),
(8, 9),
(9, 1),
(9, 3),
(9, 6),
(9, 7),
(9, 8),
(9, 9),
(10, 1),
(10, 6),
(10, 7),
(10, 8),
(10, 9),
(12, 1),
(12, 4),
(12, 5),
(12, 7),
(12, 9),
(15, 1),
(15, 3),
(15, 4),
(15, 5),
(15, 7),
(15, 8),
(15, 9),
(16, 1),
(16, 4),
(16, 7),
(16, 8),
(16, 9),
(17, 1),
(17, 3),
(17, 7),
(17, 8),
(17, 9),
(18, 1),
(18, 3),
(18, 7),
(18, 8),
(18, 9),
(19, 1),
(19, 8),
(19, 9),
(20, 1),
(20, 7),
(20, 8),
(20, 9),
(21, 1),
(21, 7),
(21, 8),
(21, 9),
(22, 1),
(22, 7),
(22, 8),
(22, 9),
(23, 1),
(23, 7),
(23, 8),
(23, 9),
(24, 1),
(24, 7),
(24, 8),
(24, 9),
(25, 1),
(25, 8),
(25, 9),
(26, 1),
(26, 8),
(26, 9),
(27, 1),
(27, 8),
(27, 9),
(28, 1),
(28, 8),
(28, 9),
(29, 1),
(29, 8),
(29, 9),
(30, 1),
(30, 8),
(30, 9),
(31, 1),
(31, 7),
(31, 9),
(32, 1),
(32, 7),
(32, 9),
(33, 1),
(33, 7),
(33, 9),
(34, 1),
(34, 7),
(34, 9),
(35, 1),
(35, 7),
(35, 9),
(36, 1),
(36, 7),
(36, 9),
(37, 1),
(37, 7),
(37, 8),
(37, 9),
(38, 1),
(38, 7),
(38, 8),
(38, 9),
(40, 1),
(40, 7),
(40, 8),
(40, 9),
(41, 1),
(41, 7),
(41, 8),
(41, 9),
(42, 1),
(42, 7),
(42, 8),
(42, 9),
(43, 1),
(43, 7),
(43, 8),
(43, 9),
(44, 1),
(44, 7),
(44, 8),
(44, 9),
(45, 1),
(45, 7),
(45, 8),
(45, 9),
(46, 1),
(46, 7),
(46, 8),
(46, 9),
(47, 1),
(47, 7),
(47, 8),
(47, 9),
(48, 1),
(48, 7),
(48, 8),
(48, 9),
(50, 1),
(50, 3),
(50, 7),
(50, 9),
(51, 1),
(51, 3),
(51, 7),
(51, 9),
(52, 1),
(52, 7),
(52, 9),
(53, 1),
(53, 7),
(53, 9),
(54, 1),
(54, 7),
(54, 9),
(55, 1),
(55, 7),
(55, 9),
(56, 1),
(56, 7),
(56, 9),
(57, 1),
(57, 7),
(57, 9),
(58, 1),
(58, 7),
(58, 9),
(59, 1),
(59, 7),
(59, 9),
(60, 1),
(60, 7),
(60, 9),
(61, 1),
(61, 7),
(61, 9),
(62, 1),
(62, 7),
(62, 9),
(63, 1),
(63, 7),
(63, 9),
(64, 1),
(64, 7),
(64, 9),
(65, 1),
(65, 7),
(65, 9),
(66, 1),
(66, 7),
(66, 9),
(67, 1),
(67, 7),
(67, 9),
(68, 1),
(68, 7),
(68, 9),
(69, 1),
(69, 7),
(69, 9),
(70, 1),
(70, 7),
(70, 9),
(72, 1),
(72, 7),
(72, 9),
(73, 1),
(73, 7),
(73, 9),
(74, 1),
(74, 7),
(74, 9),
(75, 1),
(75, 7),
(75, 9),
(76, 1),
(76, 7),
(76, 9),
(77, 1),
(77, 7),
(77, 9),
(78, 1),
(78, 7),
(78, 9),
(79, 1),
(79, 7),
(79, 9),
(80, 1),
(80, 7),
(80, 9),
(81, 1),
(81, 7),
(81, 9),
(82, 1),
(82, 7),
(82, 9),
(83, 1),
(83, 7),
(83, 9),
(84, 1),
(84, 7),
(84, 9),
(85, 1),
(85, 7),
(85, 9),
(86, 1),
(86, 7),
(86, 9),
(87, 1),
(87, 7),
(87, 9),
(91, 1),
(91, 7),
(91, 9),
(92, 1),
(92, 7),
(92, 9),
(93, 1),
(93, 7),
(93, 8),
(93, 9),
(94, 1),
(94, 7),
(94, 8),
(94, 9),
(95, 1),
(95, 3),
(95, 7),
(95, 9),
(96, 1),
(96, 3),
(96, 7),
(96, 9),
(98, 1),
(98, 3),
(98, 7),
(98, 9),
(101, 1),
(101, 9),
(102, 1),
(102, 9),
(103, 1),
(103, 9),
(104, 1),
(104, 9),
(105, 1),
(105, 9),
(106, 1),
(106, 9),
(107, 1),
(107, 3),
(107, 4),
(107, 5),
(107, 7),
(107, 8),
(107, 9),
(108, 1),
(108, 4),
(108, 7),
(108, 8),
(108, 9),
(109, 1),
(109, 4),
(109, 7),
(109, 8),
(109, 9),
(110, 1),
(110, 4),
(110, 7),
(110, 8),
(110, 9),
(111, 1),
(111, 4),
(111, 7),
(111, 8),
(111, 9),
(112, 1),
(112, 4),
(112, 7),
(112, 8),
(112, 9),
(113, 1),
(113, 4),
(113, 7),
(113, 8),
(113, 9),
(114, 1),
(114, 4),
(114, 7),
(114, 8),
(114, 9),
(115, 1),
(115, 4),
(115, 7),
(115, 8),
(115, 9),
(116, 1),
(116, 3),
(116, 8),
(116, 9),
(117, 1),
(117, 8),
(117, 9),
(118, 1),
(118, 8),
(118, 9),
(119, 1),
(119, 8),
(119, 9),
(120, 1),
(120, 8),
(120, 9),
(121, 1),
(121, 7),
(121, 8),
(121, 9),
(122, 1),
(122, 7),
(122, 8),
(122, 9),
(123, 1),
(123, 7),
(123, 8),
(123, 9),
(124, 1),
(124, 7),
(124, 9),
(125, 1),
(125, 7),
(125, 9),
(126, 1),
(126, 7),
(126, 9),
(127, 1),
(127, 7),
(127, 8),
(127, 9),
(128, 1),
(128, 4),
(128, 7),
(128, 8),
(128, 9),
(129, 1),
(129, 3),
(129, 4),
(129, 5),
(129, 7),
(129, 8),
(129, 9),
(130, 1),
(130, 3),
(130, 4),
(130, 5),
(130, 7),
(130, 8),
(130, 9),
(131, 1),
(131, 4),
(131, 7),
(131, 8),
(131, 9),
(132, 1),
(132, 4),
(132, 7),
(132, 8),
(132, 9),
(134, 1),
(134, 3),
(134, 4),
(134, 5),
(134, 7),
(134, 8),
(134, 9),
(135, 1),
(135, 3),
(135, 4),
(135, 5),
(135, 7),
(135, 8),
(135, 9),
(137, 1),
(137, 4),
(137, 7),
(137, 8),
(137, 9),
(138, 1),
(138, 4),
(138, 7),
(138, 8),
(138, 9),
(139, 1),
(139, 4),
(139, 7),
(139, 8),
(139, 9),
(140, 1),
(140, 4),
(140, 7),
(140, 8),
(140, 9),
(141, 1),
(141, 4),
(141, 7),
(141, 8),
(141, 9),
(142, 1),
(142, 4),
(142, 7),
(142, 8),
(142, 9),
(143, 1),
(143, 4),
(143, 7),
(143, 8),
(143, 9),
(144, 1),
(144, 4),
(144, 7),
(144, 8),
(144, 9),
(145, 1),
(145, 4),
(145, 7),
(145, 8),
(145, 9),
(146, 1),
(146, 4),
(146, 7),
(146, 8),
(146, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas_vendedor`
--

CREATE TABLE `rutas_vendedor` (
  `id` int(11) NOT NULL,
  `id_ruta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `rutas_vendedor`
--

INSERT INTO `rutas_vendedor` (`id`, `id_ruta`, `id_usuario`) VALUES
(1, 1, 62),
(2, 2, 61),
(3, 3, 61),
(4, 4, 60),
(5, 5, 60),
(6, 6, 64),
(7, 7, 63),
(8, 8, 71);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('09b8t8Pmb4INbOmLbAgkVLLexwi3tZRMtQAZ6lX4', NULL, '194.132.202.167', 'Mozilla/5.0 (Android 14; Mobile; rv:123.0) Gecko/123.0 Firefox/123', 'ZXlKcGRpSTZJbk40TVVzM1RHOWFhemxRVWxOWVRtRlRNa2dyUjNjOVBTSXNJblpoYkhWbElqb2ljblYwZGpsUlVGUldibmxKTDI5SFkxZGpNazFEZW5ZNWEzZDFVMjVYYmk5eWVWUjBiRnBQVW5kT05raE1VMFl2UTB0TFNYVnpNRXRGV0VGdkx6ZGpaVFpNVEU5d1RGZGFOV2xVUWxocmR6bFlSMUZsZWpoblRVdGFSVTFQVTB0T0swRllRemhuVTJOTFNESmFiV2xPY2pnM1JFTldPWFpHWVVJNFpVWlVOWE13UVRnNWFtUnpOR3R6UjNsT1dHRTRLMWRyVFZWek1tZEJiVkExU1ZaSVNEWnFiSGRGVEVGTVJGTjRZbm80YVVKSU5FVjJZVXRtYTBFclIxZHVWbkZEVFZKUWJqTkJka1JxWlZCVGFFRk5RM0pLVmxoaUt6STFVbTkxV1hNMVZEQm5PR3RZZGpOU09ERkpaVXRPVlVOS1ZtcDZhVEV5ZEZsUlYxTmhNbTlDUldKNGNUUk9ZbEp3VVZsT1ozZGxibUZTYTIxWE5teHRjVFJTZFZwTVZIRXhZWEpQY2xCaWJGTm5aa3BUUWpSSmEwdEtkRUpJTXpsVWVIUXlXbmt5YTNvaUxDSnRZV01pT2lKaE5HTmhPR0ZsTjJGalpUZzJaREJoWmpBM05ETTNOVGN6TlRVMFlUaGlNRGhqWkdGaE9EQm1ZekprTkdVMllqa3pZbVU1WXpVM01qQmxNVE00TW1ZNUlpd2lkR0ZuSWpvaUluMD0=', 1788886157),
('1OnxNivuj0EVRT3v26A84Y3iI59g9zAHPv0jwFpz', NULL, '83.140.8.241', 'Mozilla/5.0 (Android 14; Mobile; rv:123.0) Gecko/123.0 Firefox/123', 'ZXlKcGRpSTZJbmR1UTFVdmFrSm5SaXRpV1ZCdU5YZFJLMmh0TkdjOVBTSXNJblpoYkhWbElqb2lMMnBOTlROalRWZEdXVXRMZVhWMFMyMXVTbHBWZWxkMVVGcFFlVGg1U0hGVlJtUkJRMk5RV1dGdGJUWlZUbW95VjFsR2QyWlJlV2QyVDA1TVR6UkNZakJ5ZEZFdlVrMTJXRGg2ZUZKdWVuSTViVGRpZEc5amNtOVJNVkJSY20wMk1XTTJVVk5QVlV4NE1qa3JMMVJ5U2pONlRuUk5iR2hUVEZwaVZuZGxNbEo1ZGtWdk5FZHROM2hLUmxBNGRuSlJRbVJpUzJGNk9HRkJhekpxUkVoaVNEZElVVTlaZWtoRldHVndNSFJzUkZaVVJHWndlVmxXTVVKcFNVRTFXSGx2V0cxWFpGRlVOMHRvYWtOcmFHczBaWFJIY0ZBdlIyeHRUVWhqZFZKc1RIQlhNR1ZMVkVkeVQxZ3hWWHBsWkVSa1RtOVBSelpvVDJGWVpEZFJRVFUwWTJneFExQmhhWEJ2WjNKWFRrWnlUMkpvUlRaTFdFTjJZbkpCVUNzNVRuQXdVaTlIZVVSeFVFSXlOV0paV21nMU0xZHZNV2w1VURaVU5WSldMM0JzTlZZMU1IQXpZMWgzYmpZNGMweE1TRGxKV1cweU0wZFJQVDBpTENKdFlXTWlPaUkzTURNek1HWXlZamRtWW1RME1XRmlZakpoWTJNd1lXVTJPV0kwWVRJMFpqRXlNall3WW1JNE56ZzRaREUzWlRnMk9URTNNR001TURsalpEY3hORFV3SWl3aWRHRm5Jam9pSW4wPQ==', 1788886157),
('3v0z5LXI4pUIbFY4QGYjXU9I3XBeJwCV9viNz0AC', NULL, '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJbFJoVWtKSllrOUhiR2t3U0RKbU5WWnFVWEZCU1VFOVBTSXNJblpoYkhWbElqb2liaTkzWmpGUFZ6bDRNVzlJTDFaNU5WbGhWa2xPWm1rMGFEaGtkamdyTkVJMFNqTm1UbmQwTkVSSlIxVjRkMlJ3WVdaSVRqWTFNVll5T0ZKb2VFMWhhbVJNZVdoeFVESTJja3RCVFVKSGJWbENTVzg1TW5oa09EbGxkWGt6YldwcE9FUmplVWRtWW5SNlQwOHZOR2t6UmpseU1rUktSRmhFUmt0TGJVdFZWM0UyU3pGcldDOUViRTlFTVZOSWJFMTBTVEZVTmtwdk9UWkZXWHBYY21wUVduZHFla3hVYlVrM1NFczNSQ3MzUm1oSGJEQXJaMEZxYURKQ1J6TllRVTFaU21GUFlVdEhhMWxyTXpGSlVsZHNZbmhYUkhkbVRHMHZRU3RyZGxscWRuUXdkWEJETmxWcVFYcHFablJoVFdRclV6WjROVFF6TjIxcWJteHdSblZMV2paUVMxZFVTR3B6VjBOS1JEbFRRbXRFTjBwNWJURXpjVEl6U2poaFNrbEJOVU00WmpGM2IwOHpRbVZIWms5SGJXWnBOVEJZVFdKTlZUZ3hUVlZUVjFSRlFYTnJkRFZDV21OeFlVVkdRV0kyZDNCd1ZFWjNQVDBpTENKdFlXTWlPaUl4TlRNeVpHWXdORFZpWkRGa1pqQmpZbVl4WW1SaU9XSTRaalptWTJNNVlqWTBaREkzT1dZMk56STRaVE14TURFMU1UZGxNV1UwT0RjeVlUbGlZV1k0SWl3aWRHRm5Jam9pSW4wPQ==', 1788878912),
('AhcpxtnvkOW6jhWTetJ4PUL76FHQnLZ8aIXu6Skh', NULL, '100.26.225.192', 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.101 Safari/537.36 OPR/40.0.2308.62', 'ZXlKcGRpSTZJbXhQU1c4eVNYbG1NWGhSZDJ3M1psTlRXR1YwTmxFOVBTSXNJblpoYkhWbElqb2lPVkIyVWk5M05TdHdha1EwVUU5eFVGZzJSUzlSTVVnMkt6RndUMWRPY1dWNFdGUlJWVzVQVUZWdVdFWk1iRk5DWjNSTGFUaEJlbWMzTTFwRFMycFpkek5CYVVWd2RGYzViV2gzTjFJdmFuZDZUM0pGT1VWalRXeGlNVGhLVFVremFWUmxjbWRXWkVGUlRteFBhMUJVVDNOVmN6WnVhM28xT1RNMVlsWXZWMjVyZUZOaU1uSXhaRTU0TkVwd1UwdzVOR3hGZERWamNsRXJSR2t5T1hkUWVXNU5lRnBSTlRrNVltYzJWM1pLZFhOcmFGSXdaMUZWZG10b0t6bE9aMnQ0ZUhOSE0zWm9jbTlVZWtKR09DOUtkMk5MZEdOUU5Gb3JWWGRtT0RkYVJHTTRZblUxZUN0dE1FNDJMMVZXTjBWbmJXZEVkMEZJUjNkcWN6UkdjV2RZVFdkRVNWRkVaRVpqZG5VMk1IRmlhMUEzVWpKYVEydHpXSEI0YUdnNFF6TlNUR2g2VnpoaFVFZFJjbXRWUWsxVFEyeGpOVVJHTlZWSlJ5OW5RVEI0TlV3aUxDSnRZV01pT2lJeVkySXhaRGMzTmpBNFpXWmpZekZqTURNek1qTXhZbU0zTW1NME1UWm1OVFV6TnprM016TXpaVFppTjJNeU5ERXdOelZrWVRoa05tWm1NelF6WW1VeUlpd2lkR0ZuSWpvaUluMD0=', 1788875738),
('BM7RQGqqWbFenYYG8vOV3GY3RmhGlCnUusr7g5uf', NULL, '66.249.66.72', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'ZXlKcGRpSTZJazl5WW5adFdsVlVNVVJRYzJSbk5XMW9SRGcwZFZFOVBTSXNJblpoYkhWbElqb2lhSE5IYkZoWWN6WlZMelZCTTNkUlIzZHlTbmxHYTJzNGEyUjZiV1JhZGpKeVpURnBUMGR4SzA5T01ubFdlVXMyVHpaT00zRkJaMjVoUlZWNWFFcE1NQzkzWm1sUk1UWmhWMWx2YmxGalJqSjFkemRQZFd3eUszVnBjbVptUzJFM1dtaHJSMVpJTjBkS2J6ZFpkVGhQWkhoWVRrMDVaMDR3ZEM5Q1NtaE5lVEpxWjJ0UmNqWnFXWGgyUTBaRE1rMXBSM0JDZVVoeVVrcFpLMXAxYTNZMWEyUmtiVGxxTXl0RU1uQkVhVkZEYTJGRlZHSnFjU3RxZFVOMVZqSTRUbmhWUTFGM0sxaHRkV3hwYjNwTGFtdFZVMEpRTmtSc2MwUk9UVFJyVWt0clJGaHFXRWg0TTBWRk4yeGFOM0p6U1UweFRtRXlXbEJGVVhSNllteFJRa2xQVUM5d2FGVTNNMlpoT0RjeFJFTktRVVJrVTBONVRqVkJPWGhtVmpReFVYWkJNVW80ZVV4eVZWRnZPVlZ4UmsxdVZXNUJkWGx2TVd0bUwycDRjVXhKY0ZFaUxDSnRZV01pT2lJek5tRTBZemsyWlRZMVlUSTJZVEU0T1dFek5qTmhNRGcyWlROaFltWTVNMk0zTW1ZMFpUVTJNell4TmpNeFlUa3hNV1l4WmpJek5tWmlZemd6TlRjMklpd2lkR0ZuSWpvaUluMD0=', 1788875313),
('cJeDaacJz7t6D1NKHdKATsjBQc4gkdBYJCyVg72a', 107, '185.227.218.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJalZtT0hKcU9EaERNeXRITjNkVGRrVjBUR1JaUzNjOVBTSXNJblpoYkhWbElqb2lablpuUm0xS1kzQjJUMWcwZVU1ck56UXZZVUpCZVN0T05sRTJVVTlwWTJSbFExQk1hVWRrWXpkMlduSndjakpQTVZOUWRUQklOU3QyUW5odlZ6TndWVGhtTUhBM2JtRkJkVGxNTW0xMFlXUnVPVTEwV214dk1pOXdTalJwYlRNeWRVVnZNbEZKUjNBMWFtRjRlbmx1V2xKbFpqWTJVWFJOZWpSaVkxRlJPVk5TWWpkc1FWUmxTVFJoUWpWNlJVbFdNV3g2UkVKdFJERXdSamt6WnpCWFkxa3haM2cyTmxaTlVWUlpXRGhGVlRGaVFtOUZaeXQyVmt4TVMyNXZURXBDU0dsWWF6VXdZVk4yZEdsNk4wRndlVmRaVFZsTGJscHFjMU5PVjJ0UU1reGljVTFqV2tob2QzSlRhelZTTjBsbFJWcHRVVmRUUjJwT1ZGQklWV2cyVFRkNWVXbGFSa3czUlhBdlUyOXZPV1ZxT1c0d05tVXJhWGR3WkcxU01rMWxhbTQ1YTI1dVZEaFRVM3A2VkVsWVNEZGxRek00WWsxWFJUbEZPSGRhWVVWTlMyOVpOR3R5YkhKWlUzVXJiM1pwWmxWNWRETnBRMUJEY2tWWlJUZzRkM1JSTWk5bU5ra3ZSa3RGVEhSVGFTODBTSGt4YlU1V1kyOTVjR2c1UlhWQmJrZzJXRnBOVDJkWVQzcDNkMUZNU3pGRk1GTllPVXBFTWpCSVVGUXpSVnBIYW5aUlNXaDZkekUwYTBzclVsSTFWbFZ4V1dKM2FuWkVVSGhYY3pCdGJHazRaVXA2ZVhFMU5VeHpabkJPV0VWTmRsSkdiMUZsYkhKblZXMUlUVFJXYTNOMWVFdEtVVmRzTTJObU5URm1ZVWt6Y0V3clp6aHpTekpVWVVOUVZUQmlhR280T0dORmRsbHJiVGRKVDNkNGJUbHNMMXBPTUVkYVMwMW1VR04yV0hWeVFXSkNOSEowTkd0VWRYZERlVEpoTHprelRqZE9jVUZIWlc5a2IwUnVXalp0YUU4eWVIVnBiVzB2YzNsbmFVWjVNMlZzZVVVMVluVmFSSEV5ZDBZeFprVjFkekZhZDBOTFVuQjFOMFp0Wm5acGFFSmpNbE5vVGxSdE5ETnBiV3BFTUVOMFdHWlFhVk5uV1d0TE1sbEZVbVJqZFZFd1FsSjJXa1E0TUVsT0wwSlJPVWR2V25ocmFYWk5ielJZTVhVM1JUaHRaMDk2YWk5MEwyTkNRblU1YTFGd1NVcHZjR1phU2pGclFWSk5TVTVTUWxCNk9VWnlVMmx3WmpSdGFXbENVV3RTT0RCVFlVUkhiVFJIUkZabVdVczJMMUpuTUhadlpFcE9iM2hTWjBKVWVWSnhWR2t5TWsxTFRVNXhhSGxMZFhwRVlWaG5UMlJNWm5kUVoydzRiREZzVlRGeE9WVkxWSHBLSzNoeGNWQkdTak5yYUdWNFNpOXlXV05DVDNNM1lubGpTbHBDYVdodlUwNVhNVkpQUW5CWmJYaHlVM1Z2ZFhSS1luZ3ZaRGMxYzFoNVEzcEdURk42Y0ZaeVJXWm5Wa3h0WjBWTlNXSTVhMjFWUmtWNFZHdEJiVUY0Y1hKVVpsRkhUSGh3UVhSRmVVTnFiU3RrTkZKeGNHd3pWakp4VVhaWmJVYzBXWFpTTW1WVWVGQXhMelZJT1dOWk4xUnNLMHQxY0hRelVHeEpaVTA0YnpnNU9VcG1kSFJuYmpVM1ZrOXRWU3NyWkM5RlpqVXZSMmhNUWxOS1JraDBUM0pHZFhOSlVtRklSM3BrUWxveE9YQldUbUZuV25oSGRtbzBUWEp3ZUhWc2JreENkVTFwTkdoWlRFRm9WRzlyYkVOc1IwZDVNR1UwVXpJNWJVOXBNSHBPUW1ocFZtUTNNV1pZVEZoWGVHMXhZVWhwV0dzNE4zUTVWV1JtTlV0V1MwVnhWVTh3TkVGdFRtTTNVMmhvUjJoWE1HdERXWGhXVEhGbGJtOWthV05DVmswckt6WnBkVFZEVEdnMldsWkxUVFZqZWxkRVNuUlVNamswYWpadFUycFBRa2czVm1Wd0t6VktWblV6UkZFdlQwTnZiMW94UzB4Tk5WWnBaMlV2THpOVWEwc3lXR3R1WkhNM1UwWndlVmg2TDBjcmJIWlBRM2xPVUU5UlJYUlVaVlJLU3pSNUsyaFJLMWN4WkVKRFRXUktkSHB0Tm1sWVRDOUZiVEpYYjJKSFdqUXJSRE5tTVZoWmFUSTJSMnBqV0dkQ1RsbEZkM04xU0ZWa1JGWXZiMlZFUTNWa01sQXpSV1Y2UTBaaVptVlJjMWwxTkhRMGJuRlZWa0ZPY2t0NU5HaEZaV3R6UTFVMlNrbExSbXAyTms5VE1XWnVOVVZoUWxWcldtUldUbFl5VlVaMFpHMWpZMWdyVm1ob0szaDFTakZNUkZwNGVuQmlVRlJrY2pSR01rSnNTa3hPV0RaMWMwbERjMWRPWW5WR2QyNXNSV3d5YVRFd05qaHJXVlp2VVVWSlFqVllhVXBxUTJKR1lscFRZbEpySzJaNGNIcDZiV1V6TkZoak4xQmxkWFYyUzNCNVUxRkhiRVJQVld0SU5qWkJSSHBPTVdGNkszRnRRbWhGVTA1bVpIWjVZVm8wVUU0MlYzWkhVbEJLTUdkRlFUTktkRmMxZUZCS1drNTNOV2gwYXpCM1p5dFJhVmRMWW5GdUwwVXJaMFo0Um5sV2NGWkVTV2hpVEdsaFNtbGFRbHBuTkhSSE1UZGlXRzAxZWpNMlNVWlVTR2R3V0hoV1dUQndhR2x3ZFcxalVqSkpTVmh4TWpkNmNHVXdZM1ZOYkRkQ1ltOHZRM1JWTnpJemR6Z3lORTV4ZFVjeVFXWnNZV2RRVmxKMVlYVnNSMmR3V0hsME4xVlJaRU5tU0c1bFdVdzRhVzF0ZFdWSmFsbDRkWGczWkcxSU9WbDNUV1JqWm5SMlFpOVNiMFJSU2poa1dIVmpVME5QVFROMlZXdDFUQ3RQYzBKVlIwMVBaMVZuTjI1UVRFUXZka3B3U21oTFYzcE1NSFJGZG5oQlZXOW5aMmxGWkhSbVVUUktOMGQ1ZFZkT1FURndhalpDYUdoWlpteG1ZWGg0THpaNFpXSlZSQ3RzWml0R2JHdFFaMFp5TUVkT2VrSlBOR0kxV0ZKYU5VRmxRMnBJUjBGck5GVmlWRUp5TURNM1luRm1aMmhZUkhsamVreFZjbE5TTkRVeGJrTXlkamRCUkhaSVVFVTBTRVp1VjNadk4yRkRVM1p0VURKaGRsWnJVVGxJY0VwWllVeFFjblJDYldKVlRTdGlSeXM1WmxONFkweFFjbUZsZERaRloxcFJVMmhuVGtWaVFtWkRiVWw0WTBGdGVVY3hUMDVKT1c5RlpGUkNSbmxWY1RaVGJGSkNSVzlOYkhSTVNHMUVabG8xVTJodk1EQTFkazVLY1dJNVREVm9la3RXYXl0Uk9GVm5TMnh1VEVwRFl6TnZjalVyYlZCWmFsWXpjVWR4T0hOaVdWaGhZMjVvT1dWQlRIWjNaSGhIZWtsVFJsVTRUa0Z6ZFRSd1FtSlRTblZRVWtWb1YwRlhVMGc1UVVkV1RYTmxWazV0YkN0VFNFZFpNbUYyYkZoaFZESjJOVm95YURKT1VUSmtORGg0UXpSb2FYTkpSeXM1YWpCQlIxTmtNMk4xVkhSSGFDOVJaamhGTld0T05uRkRVRE5qV25OTloyNDNTa3NyY25vMVZYRlJNWE13TjFvNVJGYzRLMG8wVEUxdVlucHpSMUppTUZGbEwwdG5aa1EyYW5oRlltTlpUVTR3ZHpZelNEbFNhMHMxTldReVJsUnViRUpsWWxwMFlYWjFkMFJNWVc5a09XZHhVMHcwTlZsRkwxbDFNVmswT1hwdWVteFlUMDVyTTNZNE5HMUlaSFYzV0hWMWFVVXJlbGRZYW5oRWNYaGlPRzA0V1RoUU9WZEliakJRVFM5bmFUTkNSMWQzZEdrMFFqRjBTRFF2VlM5WGJrcE9WbTV5VmxaU1MwOU9TRVIxTDJ3eWRscFJUMHRRVjFCeFQya3hWVUoyWWxOb2EyUktiemRqZVVaQ1FUQXJRaXRsYTJsdGJYUnhla3hrWjJSS1FrRTNhMHQ2YTFOMmNUVkJkMUIyVFZKaFNrRkdja0ZES3k5b1FWaGhTVXA2Y1VKblJISlhjWE5GY2pseVluRXJaMXBVZGl0ME1tSlVVbU0zTVRaSlZWQnpaMkl3Vm5WTGFUaG5LM0kwWlVSd1V6ZzlJaXdpYldGaklqb2lZV1k1T0RBeU9HVXpZbUk0TTJFd1ptWXlPREpqWkRZMFlqZzRaRGc1WlRBNE5XSTBOV1ptT1RCaVpUVXlNalEwTUdFd01XUTVOVEF6WXpsallUZzVZeUlzSW5SaFp5STZJaUo5', 1788897078),
('EcaWtonugHDQQ6zD8xhcwajthP7k1bZ0HxzakOqF', NULL, '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJbG93UzNOWU5XSTJUbVJUWWxKUVRHTllVMFJQU1ZFOVBTSXNJblpoYkhWbElqb2libEJqVXpkU1FsVnlaSGhRZEcxclVWZ3dPR1o2TjA1NGRqbE1SV0pGV2s4NGJHUjVhekJpVWxsSVYyZEpRMnB2Wm1WSWFVMWxXbFYzVDNKTGMwaGlSakZ0VWl0ak9XaHZLMDlVU2t3MGRHNWtTelJMVURjdlR6SXpRa054WTNJNWJpOVNMMFZLYW5wSWEzaHhNMnBGY1dSRVZsWjNRa3hsTlZKYWJVUlRRVFpoWTJRM2F5OTZiVEJpUkdKd1lXOXNReXRRSzNaVmJWb3JReTlEVVZFMVIyOVlkVlJwTTFkSFkzRnJQU0lzSW0xaFl5STZJakl4T0RrMk9XUmtZekZoTlRJMFpqVmhZVFZqT0Roa1lqZGpZMk0yTVRJMk1UTmlNMk16TXpFd01UY3pObVU1Tm1Rek4ySXpPV0psWlRVeU16Z3pOV0VpTENKMFlXY2lPaUlpZlE9PQ==', 1788887903),
('f6OLb1deqGV6dVHGkvW8f8MGZlOmYxk6YL8PdXtJ', NULL, '100.26.225.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Config/99.2.4111.12', 'ZXlKcGRpSTZJblJMZGs1eE0wdFNiMUF3TmxCM1UzUnVTMWcyTlZFOVBTSXNJblpoYkhWbElqb2lUV1I2UjFkSlpreGFOM2RRVGxJMVRYRkRZMmxMZFc1VlVERmtUbnBMTVUxc1NuSlRlVFk1V205bWJHNXJVVTFUTDNoemRIZDVZVk40VTJSNFkySkNSMFp5UjFKd2MybHNVVXR3V0c5RVNFVkZUbTg1WkZRNVptZDJOekZ2YzNSUVpHeDRNSFIxVldKVFJFWkhlVGhoVEhSNmJGVTJaRFpFSzJ0UGNYWm5iRkpXVVRacmFXb3hhVTlFWjA1MVJqQnRNRVpCU0RReWJITmpiMlpqTHlzMlFVaFRORGhDUVZKTmQzZFJlREZCVUdvdmRsVnpTa3c0VVRORGR5ODFZa1ZwVUU0elIycHFZMVV5TUZobFVVZDRTWFY0S3psdlEzVnpiazgyWkhoMWJqYzRPVWxvU1hWWmJ6ZGljakUzUm5OcFQwWlhkaTl2VGtKd05GY3lSRU5UVkRKdVpUTjNTR1pYYjJsR2JITjBSaTl3VUdjeFpsaFpaMDFrTWpGS1NTczJSazU1SzJFMVptMHljMFEyUW0xRU1uaEhiV0pZVkdscWFtZHVWMjh3T1VNaUxDSnRZV01pT2lJNFlqVXpZamcyWkRObVpXTTBPVGN6WkRFd1pEWmpOekJtTmpZeU9EVTNPRFZrT0Rrd056RXpPR1F3T1RoaU5tVm1aVFF6WlRNd1pUWXpPVFZoTkRneElpd2lkR0ZuSWpvaUluMD0=', 1788875738),
('GXiL6XjutMZTtSTwojYApgom78MqICefxIqQ2RyG', 114, '179.6.29.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJa0o1WVZwWVptcHRZblo2VHpkUWFVTlpVamhYZWxFOVBTSXNJblpoYkhWbElqb2lUV0pUTW5wc1lWQktRakF5ZDJKSVVtTjJWWGM1YkhZNGJtMUtURnBHZEZka1kyaHdNbVpLSzJ4M04zTXlUblZVZEVzMFZVeGFiMlY2Wmt3MlpuTXpiVlpyYjNablZFa3dPRWRsYm5oVldEaG9iRWxoU2xGcFltTXpiVWhLZEdsSk56QTVWemxFU0U5b1VGQnJjRTlKYXpoUFRtSnFNa2RpU1V0elVXUkNVV1l2WjFvclJHWmFiVTVrYVhGQ2JYVXpjRFJzVVVrdlZtZHRiV0ZGWkVGWE1USlRSRW8yWnl0bU9VTlRlRUp5YzNGSVJYVTRNMDU0WTFWU1prTnhja1pWVjBSQlJGSTJiMDExWlhFeWNXSnhNREpuVHl0MVJFeDRla0ZvTlcxeFZYTmpaMkpNVURsMk5UTkdNbnB5TWxaMWVXcHNMelY0YmxJMlRHcFBWbTV3WkZKemVIVk1ia1pRTlVsTVVrSmxUMFJWT1dRcmFrMVpXREp3YTFSUGVXODJTVkJPUW5Kb1ZsQnJXalZCZVdveE1qazRkRGRCUlhkdFlXbzBWSGx2VVVwWmJGZ3JSM0ZaWnk5Q04wOW1NREpuUmlzMVRUSktNMDVIYldSRFNXTXdjbVZMVTFWa0wwZzJXUzlsYWs5eVJsazJVVXgyWjBGSldUQXlUV2N3WnpaVFpuVkRiSE5oYkdkSFZsSktURXhJVFhWRGRVVnJTblpGWlZGMGNWSllha0o0WlZabE16WmFlRVoxZUU5TlIyRnlUVkJwTjFVMVRtYzVSVVpRZWtrM2JGaEVaa3R6WkdGYWNXUk9VamhvTkV4RVZpdHpaakZWY0VWSmNEUTVSRVp1WVdGVE1YbG1aemwyUXpBMVpITXJUVmhWWWxWck1tUnlTRGRDSzNWb1kzTnNWVGMyYmtSQmEwcFdZVUphVW5WSFdFNW9WMjFCTHpCS1RHdFJTREV6V0U4elNEUlRWVzA1VlV3MlQyNXROMkZ2V1hvd1JYTkVOSEYxYjJKa2FGcGxNMkpNWXpoUmFWRTJRV1ZoVVhoTGN5dEJLMkk0ZUZSaFlWQlhUWE4xSzA5QldqWnNOMVJGVEZOMFZITlFOak4zVmtoMWFtOTBOMEpFYmtGSmVteHFWU3N4Y3l0WVZGZzRjMXBQUjBWM2QxWXJUbXhrZURCcFRHVmhRVzFoWTJaNWJHc3hla3RuV0UxR2VGZHNTWEV3YzJkcEwzaFpWRXg2VEdkMlZ6QXZjREF2Um1sUlJqUTRSV0Z1VlZoQ2JESndUa1FyUTJnelIyZG9MMko2V0VaVk1qbEhkalIwUVVjMmVUSTVVUzh6TlhJd2MzTlJkMVpITlRka05YVXljM0phZFRKQ1ZGQlZRM3BJZG5GWWRFaEpSbGRrYUdaV1dFTnlkRzVsUzFkMFIzSmlRbk1yYUdoWGVXeDZNbmw0SzJGdldFZFZlbWtySzA5R09HODFWWEZXU0dkaFNrVm9ZV052WlZWNVVURklVRXhUYjFOblNGaHBMMnBTUWs5bllVUk9TSFl4TW5wWVZubFFVMnhaT1V4dVNUVk1jVTlFVm1GbE9IRldLMmRXU2pCWmQzQjZVSFpGYkU4eVdHUmlkVEJ5YWt0aGNteERielJTUzBNd1ZEbG5XamhxUkZNeEswbzBlR2xtWVVwcFVrRkliVFZhYkdGRU4xcFJlVXhKWVRKd2FIaE5SSGhFWVM5R1puZzNObkZQYlhvclNtMVNUa2xWYWpKcE5taDRZbEJyVG05WlltOXNaVTVVUlVoTmRXczJkeTlETWxwTVVEWkRWREZpUjA5a2RGZHJlVFI0TmxGVFFTc3ZSVU50WVVWaGNuQlliM05ETUVnMUszcHFNblZqVEhaSVNTdEVObEJKUldOWE1XbzJOa1l6TlM5UlNHOWtMM2RtWWs1b1dtVjJTek0xV1N0S1lsa3pPV2t3VGxBNU5tTmFTVkJpZUZkTmQzbDNaRGh3YVdacVFqSjVSM2dyYm01UlkweHFjVmgwUVhkSVRuUlJSazVIV25KaWFIcHNObEZWTVd4NVpIcEtaekZaWW14QmQzQmFSMFJzZGtaWE5DOVVXbkZSVEVoVGFqTTBLekEyVkV3NE1WcGhaVlptUW0xVFprZE5kbGxLV0RoWVlqRnBjVVJJT0c1UGVtRnJiMEp4WWxnNE1YbEhURVl5WVVKUFZEbG5aVWd6VGtveVdVeHhNVUZJVFRNeldXTnVRVmRzVFZCb1JWVndSazg1YUVwQ01URlJiREpVY2swd1EzUlNWMjlqUTBjM1YzRklZV0ZsY21KWk9XRk5kVzFFVVdrNFZsaFBkRmRLZW0xT2NWSndOV1V3T1VsT2FXTkxNV2hHYkd4aVlsSkhSVlpZT1hCRlFXcHhkMGg2WkhsMGFXWndRVmt2UjNCdk5FOTFVbFV5VnpGamMzY3lOWHBXU1Vkb1EwUndTV2xCZEdaVFUyUkJTVVZXZVhWSldHdDBjbVExWWxGTlN5OXBhVkVyV0dSNmQxcG5RbHBYUTBSeFYwUkxiVE5NZUhBdldrZ3hNV0ZKYVc5blVqUmphWGRHTlZBclNYRTVkRFZHTmtGalNtVXhXRFpHYW1OYWFVbEtjVXBaYVhCb1RTczNjR2gwT1hJcmFGZEZXR1o0YjFCeGRXTm5SMVJ2VDJkWlRIUlpNR2xoTjFocGVWbHJaekZDWlZkU2NsQndNRk1yWVhnd1YzVjJUakphTlRCWmFrcHBTWFZDVDNSMVVHRllaMU5LWlZaRlJqTmtVV0o2UW5NeGVVTXphWEZEU1ZZemVERmFTa3RKZEV0UFZ6QjNkekpGWW1SWE1XeHVhM0JwVWpWTFFVdzJabVpUU0RsWlpYTjBOa0ppTlVvNFVrWjRRVTQzYkdkWU1IcE9aVWhsYW1kMU5IUkZOazlIV1ZabmRrdzFNV3RUWkZkMlYwVklUMVYwWW1wWlpVNUJjMmxOTm5KeGVHSTNXaTlUU2xWd05XWk5Za3N2T0dkMVR6WndjSFpxTmt4MFJXVmtUbUZuUmxJd1RtczNaWFI0TmpodlVrMUhVbGhHVUUxMVIyNUJjM0Z1YkdOUFNXWTJjR0U1UTJWV2NESklRMVZYVGxKUWFHMTRXRnBHVWtoYWJFbDVlV3BzUkhoQlVHTnhRazFGYm1ScFRsRmpSamhSU2xveVYwNTFka2RMVDA1ek5uUXlVMFJ2U0RSTlpWRXdMMmhTTTFrNFVHaGhNM2RtU25Wd0swUnVaMHByYjNvek5uQXhTR042U0V4V04wTkpkV2MzZEdkbVFUZGtNRWRyYkRKdWRVODNjVkJsWVVVMFdHMWxhbXhQZW1Rd1JXMVVSbUoyVTJKSFZqUjBlWEZoTXpOV2MxTkxVWFp1U1UxemMzUmlZV001YW01alVuWnZNVTF6TmpjNWR6QnRTMEZaTURZNVdWWnpVbXhxYWxjdllsQnpUbHBSWTB0TFlrdE9kalZUUTJsbmFsRnVTMUZIVWxCUVpHUlVkM1UzYzNoU00zaHZRVVE1Y0RSc09ISlBRWEZIWjNsNFdEbG5LM2RKYVZjd2JqVXhSM055VVRoWlZITk9abUpzWm5KQlZrWk9Ra1J1Y1hocGFuSlNMMFpMVERWQ2RHdFdVVEpMU1ZVM1QwUnBla3Q0V0ROMGMxWldTazV5UW5BNVRFeENWWGN2ZHpnME5VeHhaM1F4T0hWQ1Jsb3pUWGRJYm14TlpIUkZPRGRCVEZodlEybGtaRkIxWTBVeWJtdE5ibkE1UzNFeVVTOVBlRkJCYnpCcFdFbDRNM1JST0V0TE16aEpTMDkzTWpKYVIzQnhlSE5PZDFFdmVESnZhVFp4VGtoVk9VOTRRalpTYXlzMFJUbEJhM0JMU1hwUk9XcEdaalYzTmpnM2RERTVkM2ROYTJOM2FVcFpPVmxtWjNnMWFUUXpiV3RyUXpCMFJuRmhjSFJVYTFac0t6WjFURXBHYWpodGNuSXdWWFJrYkN0T0syOW5OM000YVVKRVRuVkpXSFZOZWpWT1VsQkhaek0xT0VFMFVuQnJNWEJvZW5Sb2IzVmhVR2RDZDFWSFp6bFNZVEpoV25ZMVFuQjRXR3A2YzBkdWIwNWhkWEJvV25GbVdGZzFjV2ROU21KSmRWUTRlVWRzYVdsM2JEQmthamRXVGlzNVdYUXdlRkZ0Vkd4Wk1GbFdWR2xCVmxKSU0wWmtkVzVEZEdzdlVqTkVaQzl2V0RReVMxSnlVeTl1YUd4S2RraFNOV2RrYjBKWWFqVmxXQzlSWVM5YWFHZ3ZZMUpHUm01RWR6RXJWRVI0YVVkblVEZzRPRVlyYVhkRE1YRmFVR0Z0YnpFekwxWTFSMEU1V1VGTmNrUm1WMVZaVDBGVU1qUllVVll5VkZGaWFXNDFWbGxpUWtwblJWRkNZVGh1YjFFdlEwZEhkSGhCVjNGQlVqWkhTRTlNUVRkeGNVcFVPVWt5ZW1oNloyaEliVU50VkRod2VuYzJaVlZIU0dJMlNEVnJWVFZCYTBGTVJHZEJkM2RMY0RONllVWndZeXRTWkhOak9UQXlOMFYwWmpJMmNFTm5PWEE1YXpWbFRGWlpNVzlSTURSTU9GaEVWa2d4TkVsMWJVMTBaV1ZIYkUxUlEyVndaeXRtZFZWWWJGVktaRVpXU1ZBNEswWkJkbnByU0RkdGVreEtkakE1VEVGRE5uZFJhMmxFWlZNd1VuTndhMnhUYWs5Q1R6azRaMjlUYzB3d05sb3lUMkZSZFhWVGJXdExiMWxDYmsxbVNVNVdOWHA1YWtzNWIwSk9TblJQT0ZwcWExbGhUMVJSVjFwb2NuaFNkMHhCWkRoNlpFbG1NMlJJTUVFM2NDdHFWVzl6ZDBsVU1qSnNVSGszUlM5dGFtSlpZVXd4Y2tSb09HbG5kR0ZNU2tkMmJUTlZkRXBwT1RGM2NHSjRkVWRQVEVvMFFVZGxlVE01Y2pOTlMyNXdTbWt6Tkdnd1FtVjZaM1JETXpJeFRrb3JiREJTUW5KdFVWTnVSSHBPTUZnNFpWZG9Ra1V5Tnk5SVUwSnRPR2xUZW1FM2VsbFdSRkZSZG5WdFJ6SkdXbmgwTkVSck9GTTBVblJQVlVaVFpEQkNOMXBLVlhnMlRHdGlXa2xwUlRoVGMzbFJiV29yVm1sTFZUUkZiVkZ2TDJoaVUzbE1jVXR0WVRCSVVYaEdWbnBYV1dvclFYaE9WM2x5ZW1samVHbzFLMHhuZFRGM1ZtRkhaVXBSZFVSeGFHNDRVRzR4ZVRCMlJpOVJPV2RsVVZaMmVFNUJWMkpoWVhFMVJERnlhMjVUZFU1MFRrNWhPREZoVTBWR2RVcG9TbFpEVTBwak1USjNjRlp0ZDJOMlNsbHpkVmhDZG1sSU1sZGtXbFphWjBGR1VIWmlRMnMwZVZkWGMyVTRaU3N2Y0ZWNE0yWkhhbFZ2ZWpWaWIxbEZUR05CY0ZKVmIxSjFRa1JOTUUxT1JubzFWVkJpT1dFeE5XTkdhMlE0UVdWaWFHNVBVREZqVGpOaWRqQkVabWs0T0hWVmRTOURSbGhMWlV4c1NESmtNM0JXVVVOaGJtMWhlRlpuYjI5TVpuQkpVVklyV0hFdlVEbERUMU5FZGk5a1pVZEpjSHBwYm5OYVYwc3hlbVFyTVhKcVIxRlhkbUZLZGk5Mk1XZFVkVU51TDBwaksxbFJPRGxvU1dVNVNEVTNRa3d5VEdOR1EwMW1WMVpPTlN0b09WRjNaa3RaYVVaRk1YZE5aMm80YWtWVlFUSlhVM2hTVm5WaWNHRnJiVnBYVVdsbk1EbDNaVlF5VkN0RGJHVlJSREpJTTFwaU16TnNaMnhxV201aU5YbG5TR0pIUkdsR1JVZERkREJaVEdGV09FRTFZM05oU1N0TVJXaEJNa2QxVUcxWVptcFRXRTkxVld4UEszUjFLMUpTWkhOblptbFdhRE55V21VMGNXRnBkMXBoVEZSRlpsZzNkVXhEYVZCYVl6Qm5ZMUJvZDBwelVXWnZkbWxOY0dWWWNIbDVVbXhJTjNSYVMwOUJRazVYU2pabVNFbzJUMEYzYm1ZeFJsRk5SaXRHVEV4UlRYVTFkVGxGVGpoT01WSmlWMVV5ZFRKall6ZFpRMlZJVm1KYVQyaEpVelJaT0hSa1YyUjFhbTU2T0dkNVlXSk1RMlJZTmpoUVpVdHVUVkJMZDJsblNsaExlamRXZFVGek0xRTJSV2xsWVVSaFpHYzBZMWxUYUVscFkyd3daMVJyV1c1R1FXODVMMHhWVUhKeU1YUldjV3AzTkc5UU5IbzJOWFJJYWxSeVIxQjRNVlpzVWtzdlRubHdZV016ZW5GTFdtOW5abTlYUmpaNFJEaEROM2xLWlUxa1psRk1TbXhFWTNkSk1uRmtTMWhyZUcxaFNrRmtXbUYyWnk4ME4xVXdWbXN2UVZGUU9FSjFkbmRTUlhoTk5VeGxTR1JTV0hGT00ycHhPSFJLUWk4clNFdE1hMnhvTm14Mk9HNVVhRWRwZUVOSmQzSnVTSE5EVjNwd1VGRm5SalZFWkVrd1owSnpWVGxrYlVSaGVtdGFUMlF2TlhsRVpVRTVlRmMzVWl0eUsxQjNhMnRVTWk5MVFUVlRSRlJ4ZERrNGQyOVpaV0k0TmtaSE1rbGpObU55Wkd3MU9UVjBiazFOT0ZKcWMwTkZNWEkwU0dSVVFrZzVlWE0zZFdkVmJGYzFaWFUzVGtsWVIzaFNhRlp0TjBFd1QyaFVaR05WVW01TFJuSnpORzFJYzBwV1JsWkRkR1ZKTm5SRFVFbG9OVGRFWWtjNGNqWTBNVUkyYzFOMFRtUmhNWHBvWVhWblIzUXpSa1pTTjNOT2MzaEhVMW8wWWxaUE4wMWFSWE5RV2xJclIxSnNTR2xVUmtGSFdHMDFkRU0zWms5aFpsWllSVGRMWW1WeWVVWlBLM3BITWpoM1dHWk5OM2RHYmxkdVNYZFNRbkZNY2taeVJtMTFka0pOY0RoVGVIY3JaVkpJTW1SRlN6QmxUakJJTkN0T2RVSmFOMVF5VTFoQmJ6WkhSV0pUUXl0cllUa3dTVFpzVFhob1RsZHdLMDUzVm5ZMGRuUlVUMUJ3ZFRReVpFVjVkbkV6ZHpGbmQyaFFOV0ozUlhGcGJ6Sm1ZWFJ0VkZrNWJVZ3JTWE55TlN0RWFWQXZRV3c1TjI5T2MxaDRZbVZEUkZwd1JFaDNkVWxLYVdGV2FGTkhSa3B4TVd0VGNsbHVOM1ZuTkVsMVZGbGtXV2M1UTBGa01VeGtTblk0Y1U5WVVrUjVSVzkxTUZselYzaEpjR05QZURkUlpVZ3pNV2x2UkZaaVMwUnViM1JJYlZGMlR6WkRVWEY0WlhCMlZFaEVRME5OZEZkcVRsQnhZVUoyUzJObFRtZHpOMHRXWkZwMk1tUkxWbFJwUW5oc1dqWnVOV296Wkd4RFJqbEhlRGhzYms5NlJFaFpObWRGZVhSNGJuUXpSVWhvVkZkemVUZHpVMGx5UmtwSlIwVnVRM1oyT0RSb2MxRTBTbEJ4ZW1wVU0yMDRTWGxDYkdaSGRsRnpUbEphVTJZNVUxZFpNRkp6U0hwb2RYb3ZMMlZQVFhOTFNFZE5SbXg2VUVKVmVVSTJNbVJ0WlZrdk5YVnhhV0phUWxOMFZHTnFXVlp5TkZoS2VIRnlOWFpOYkVkNGIyMTVNVnBCU3pWd2FpOXJTRlJvUnpCRmVYZDFPRXQ2WlM5aWIwUkJUekJOVjJJNFMxbERMelpFZW5WS1ZuaEhkVzB2ZEdkT1dETmFNRmc1WWtWalVrWTVORTlXTTNSUlptTllXVEkwWWxwWk9WcHdWbWhLWnpScmRHNVlZM1pMZUdoSlEyUXZibWxJVms1c00zSmpNR295UzBRMk5FVXlSVTVLU0hkck1rODJNRlEwUVZGVEwxTjNLM1l5TlhGb2QwaHRRekpoTlVwR2JWZGtiMmRwZFhJeVMxcHVUVmRrVlV4WFpDc3pWV0kyY2xBNWFHWmtPRzB4TkZobWJVMXNSbEpMVm5Oc04ybG9ZMlEyV2k5bFJXVnJRa0ZPWVRacFNqaHdWWGRZWVhGb1Ntb3ZjM1I1U1hoYVUyeExTbGRETXk4MlJtaDVTMUowZVhobVRHOWpXVkJUVWxGMlRFMHpUbGRWUTJkU1lUQmlSM0ZaYUdSM1VGZGxZa1ZYVXpGblVHSm9aVkZTZVhsV1oxaHJLMG94ZDBveVV6ZHZWa1pGZFc5R04zaGhVRmRPYWtKTk5WUTNNVlZYUlhkSFVscERWa1ZsYms1ek9HNUNWVWh3VGpVMVFXMWtOSEJoYlhCRGRVc3JURWt6YkhKbE5VVkVSMlpSU1U1QlZWbEJZamN3UkhkVk5XNXNTaXRTU1dwcGRITTBUbkp1ZGtoUlpUWm9ZVVZyYldwSllWSXJNR0UyYmxOdk1Hb3hSa2xoZURSTmVGbGFVSGxNV0hweFNsUlVVRWw2Y1dOUWNHcFpabGhtU1VZMmEwTnNiVVZwUW10VGRYcEZTMVp4Ym5GeWVHVnVVVFV2ZDBvNVdGRlRLMHBqYTJ4dmIydzJiWGgyUVdsSGJVRm9lV2RIZDFSNk1FNHpjemxKVkVwRkx6QkxVelJUWWpsa1QwdFVha1paVG1ScWVtTm9PVXhMYm1sblVGZFFjbWRyY0dWR1IyOWxWMWhaU0RObmFYSXpaR1ZNT1VsMFVDOU1kRWR4Wm1WblUzUTNRbUpOUTJGYWQxTXJOVzVUUlVwcWRHOVVURTQ0UTJGa1JpczRiWEZTY1hGR1oxYzNiRWx3WVZoME5sRnFTbGxVY0VFME9GVnhXVUZVWmtGNFJFdDFaM05EYkdkVGJFWmhXRUZaV1dSNVVVSnFjak5DUVdkV1VHNXVRbXhtTnl0dmEydERhR1JWTDNkelNVcEhiMWc0YWpNeWIyeFNUR1poY0hoRk1YRnZkR0ZXVGtSTVl6aG1RM3A2T0hOc1EyTTNiVGxhT1ZWS1JHNTBhVW8yTmpkeU1IcHRaVzlrVDFSbVRXTjJXVkZUYzBKd1VuQnJSRzE2Y205blVXTTNjVEJLVFRKUFQyWk5hVEF5VlcxWk5Fa3JlRWxCZG5CaU5qQTJZelp3ZWtSMU5uSXZkamhDV1d4T1N6TTRUa2M1VHpNeVlYZFdiWFEyTWxNMFdVMVBiRzlIUldSMk5FSkNNR3BuUW5ObFUyNXlSelF4UXpkeVFqYzRORTB4TUhOc2NVY3JXbFUxY0RScFUwTnBXakUyVlc0d0swdHhMM2gxVG5seVNuUlpaekZ1UmpGV1lrRmpZbGhqZFdob0szTlVkVmRsVUVKS1MxUkVWVXRNVFVoMFF6Vm1ZMnd3UkhwTGRIbFpTakJWT0VKTVJtNVFkM2xTYkZaS1ZWTXZSV1F5UmpCTE1Xd3hhV2g0YjB4Q1lXUlBVelpzWVVGT2FsTXdMMDVpUW1oUVZEbEdSbWxoTTBwcVVqSkJkRTV3VFRkWlVUSk5XVk5GZWpneksyazNTVVJIUjNCUFIwUXpiMUJ5ZEN0TVJVaFVVQ3RvWkRsNE0zRlNNM2xJTWt0RFVEZGxPRkEzUTBzeU9IZHlkV2cxZWprM2RWZFJTekpyYm1GRVlsSmtMM2QxVXpaRVVUWnZiM0l2TVRoalpHTjJjV0pWUzFaWFpUSnNhVmxQTTNKcEszcHZVVEpUT1VaTlFXVTNjMWxUTkRadWRsbDBlVU52U0hVM2VFNXBkWE53YlZSS01qbE1ZelZ2WWxaVVkyeGlSWFoyV2xKRFFubFVSV012TTNkbE1VOUVPR056ZG5OM2MxZEVUalozVFRWeUwzQXhkME5VZDJWU1R5dG9ObHBhY25GRGNsRXZjbWQzTkhOSFZuWmlWelJzWWtWM1dYUkhWblUwVG5CSE5WcFpSMlZoVlRSSk1GUldNRGxVUmxObFptVnFlRTVTU3pSd2JFUXdVR2hrTmxCSlRsQnlSakp1TnpaeEx6QjNaVGMxZDNCNFNtbFBZa2hXUTA1aFMweFhia001YXpObE5rZzBUa2RKUmxCRmFIUjJOR05XZG1kbk1rcFNOWGxET1V0UmJrVkJTMDFWVGtGWVRGWjVja3MwY2pkbUwxZ3daQzlqVHl0dFVGaHhPVlozU0ZSeFoyNXJTM1pIVUVsaU9XUlhlREpVVHl0a1RHVmpXRGc0TUU1S2JVTnJlaXREYXpObmREWTVVV0pZWm1waVdqSTBVbUpxUWs1SVNuZFpTR1ExTkhSWUwyb3hVbVIxVERob1V6VXdVMlJEVEZGbWRYWkpNMHhpVVc1S0wwWnhVWGd6YmtkTWRVOUZkVEJZTDBKRU9UWnlNbGhPYlRaSWVHMHphVUZGVHl0WlpVSk9kamx6VG1WcU1TOXBUMnRxTjA5cEsyTXlRVmhrV2tsRmFXVnZRekpWT1Zwc1YxRmpkMGgwTldsUU1Tc3JlWEZUT0ZNMWJHczFVbXRGZVRWdWEwWktaVUZWZFZONVIwRlNSRlZSUW1GeWR6VkdhVGhTWVdVeFV6TklZalZyTmpKT1ZraFRUblI0SWl3aWJXRmpJam9pTURRMU5USTRNelk1WW1KbU1HWTFNR0ZsWTJWa05UVmxNamN3T0dVNE9UZzFabVpsTmpjM01qUTROelJpTkRZNE1ERmxNV0poT1dGa056Y3pOalJtTXlJc0luUmhaeUk2SWlKOQ==', 1788896232),
('hXkclmWPPK40Rc7y4yV2ey1BKZLxrrltSm8BqUa0', NULL, '100.26.225.192', 'Mozilla/5.0 (Linux; Android 11; DN2101) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.41 Mobile Safari/537.36', 'ZXlKcGRpSTZJamgxUnl0cVUxbGpUSGRPYUZaeFVUSTVlVkZhVmxFOVBTSXNJblpoYkhWbElqb2lXV0l6YUhONUwwUlhjMUJzY1VkcGQySjRhbUZNY2xBckwyOXRNVEpxY2toRWEwd3JORmxYWW1Ob0swcDZaV2hDUkVsVVdWVldXbmxzUzNGT2NFMXFRMGhJUTNGclltRkZNakZQVkRCVFpEWnRhakV4VGpoMFZGaFNlalo1UjFJek5YUXdhRk41UW10V2FYaE9aMVpoY2l0bGFIQkxVV2hyV2tZMVRHRjBhVXB2UVdvMFMwczRVVlEwY1M4eVZWSmhOSEF2Wms5eWNub3dOVVk0THpOVWFGY3hkbGxDYTB3M05IcDZRMFpvWWt0eFVWTTFabFJ0WjFodWJHSlphVkZRUzJKTlRXYzNZMVZpWmtOcU0wbzFNMFZuT1d4b01sZERVVEYyV0VOdmNYSjBPVFU1UW5rME5rVXdZbTE1ZWtoMmQyZEhXVXhVT0dSUU1FUlZSV1Z2U3l0R1ZtZDNZV1p3WldaclFtcHhORkoyWXpRd05ETkJTM1ZPUkVWcFJHbzVlU3RyVEVSWkt6ZHhUMGsxUzNKbVZXczNaM3BWYW5odGVUTXdRMnhTUXpVaUxDSnRZV01pT2lJeE1UYzNNRGsxTkRnMk16QTFOamxsWkRNelpEWXpPV1kxTVRSak16Z3pOakEyTkRVMU5HSXpPRGN6WmpSak5ESXlPV1EzT0RaaE56VTBPV1ZrWXpWaElpd2lkR0ZuSWpvaUluMD0=', 1788875738),
('IXmKyihyKAqBiz9EmZSdDl5Ga9LsX4HolvMcOUzr', NULL, '18.153.79.176', 'Go-http-client/2.0', 'ZXlKcGRpSTZJbkIzUm5adlFYTmxSMjF3VFZKelkyMDNOV3MwYUVFOVBTSXNJblpoYkhWbElqb2lOakp2TTBkWVZFVXlUV054VG0xaFZtdFhhMUF3ZWxrM05uQkdlWFE0WTFwUE1IcG5aWEJOWkdsSGJYZ3dMeXRXU0dKWFJGVk5jMGxxV0M4NWRtNHdVVFJoT1hkVUwyVlJSRVkyYkRkVVVqTnlOMlpxVEZOd0wwdERVRXBPYkVNMWJYQkVUV0ZHVWxWblpVNUtSbFV4ZGpneU5XSnNSRTVoZG5oclkxVjFVbFJ5U0RKbllqY3hhRTVwUzFsMFFrMWtZaXMyYVhwaU1UVmFSbFJXYmxaS1kxTkNjbE4xTlRZd1lYbHJTRFZtWldRMlVUTkJaV05hYlc0eVRGcFROR2RSWVdjdlNrcDRSamRNTnk5RFdVZHFSVVZrY0VZd1F6Rk9aVTV4UlUxUU16QXZlRW81VFhCaU5FNTBhamR2WkRKak9VSnBabEZDWjI1VmMzTllZbWhNY0RJeU1rSkRSVmRSVGtsd1UxbzVOVzgxV0Zsb1FubFBhblJsZW5CcFdFWldaWFIyZGpOc1YzSnlkbEp1VWxkWGFXMXpTRWRDV2tGRlRGUXJXa1prTTFVek0xVmxUa3BpWWxkdU5YRnZTVzUxUkRsRmRITkJQVDBpTENKdFlXTWlPaUkzWlRJeVpXRTFZekZqWkRRM01qTTFOR1UwTm1VMll6a3laamt3WlRNNU9XUTJZbUUwT1RjeFlUVm1ZV0ZoT0RKaU1qUTROMk5qT1RabE5EZGxNVGhsSWl3aWRHRm5Jam9pSW4wPQ==', 1788890920),
('k9aNeiUJgyBtDESK8DVmOBm5NzVKBHIy5NYrnmcz', NULL, '132.251.2.143', 'WhatsApp/2.2634.101 W', 'ZXlKcGRpSTZJbnBhV201Q0wzZzRPR2R1V2k5M1JDOVVUVlptZGxFOVBTSXNJblpoYkhWbElqb2lhR1k0Y0hJMlZrZ3hUa0k1U2poRGJXSTNlWE01ZW5SaU1VeFZURzVDVEZGMlJsaEZZWFJKVlZwb2JWcHZNekl3YW5sVE9FdGFlRVJaZFRaS2NsbHlSRkYzSzJKVE9UZzVPVloxYUhObVVHTm9iR1IzUWxWdU9GbGlOMXB5VUZkUWFYRjVSSGRJWVdaSVlYZHpiMHRwTVZKVWVtMWthVzlVTUZCMGJXRmFSVVUyTlhRNFUxQjZhVFp4VFVsbmRUTjZVblE1SzFSaFpHdEtkeXRTUTFsV1NuTnNTV2hvTDNnMk4yVTBSM2RSVUZkbVdWQXhjMGN3YW1nMFJFOUNTM3B1U2pVdlRISTJLeTlqTml0dVJEZEhhV1kxU214bU1qSTBORlJHTVd0eVZuZExiVTloTDFOcGJFUmFMMHhrU0RkMVVGQk1LMnRDU0Zadk5tSlJlU3R3YURkeU9IZEhURTFrYWpBeGNsRkxVRmhEZUZSRE1XZE1lWGR5UlZoU1JtTkpXbVpZU0U1QlUxbExaR05QWms1d01rcFdSRGt6YUhnME5tZGhVMlZhYWpFaUxDSnRZV01pT2lJMk56UTJOelpoT1RSa016ZzBNR0kyWmpCbE56QXdOVGs1TXpjd1ptWTRNR015WmpabVlXVXdPREl3TW1JeU16UXlZMkkwTlRjNVkyTmhPVFprWXpRNElpd2lkR0ZuSWpvaUluMD0=', 1788878810);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('MgAfHGpTuJ01hCgJypowQvji2Mf93fBhmgIXTpJo', 108, '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJalF2WlRCeWQzVTBNU3NyZFZSdWJGSlRjalpRVkZFOVBTSXNJblpoYkhWbElqb2lhVFJMUjFkeFF6UjBNbXgxWmk5QllYWnFZbk5SYVZSTGIxcEtXR2s0ZEd4R1JGSlRhekpGZEZvNUszQnpaMEUxT1RrMkszcEVhV1JuY1hkR1draFJNVTF1UkZVNFlXWkVWWGhSZVZwamVtcDJlVUpZUXpKR1FuWjVPVkpEYjNSU1R6QlpabWh0YlZwblVqbHVOMkZrWVhSUFJUbHVkRmhqYlRoUlEwWnNka04wZEhSUk5GaGFWM0pqTVZabFVEUTJXRTlZV1U5dU9XaGhaV3huTnpSa2JrdFpNRlprYW5GSFNFUlljbFJGU1dOVWNXVnVOR2Q1VFdseWVsZFpVV2h1Ulc4MmFFSkpWMWRvYlVkMFduVkRkbmxtZDNsVE4zVXZSVmxwT1VaWVpETkJkVUYyYkZGQ2RIWnRaemxCYkdkUlEzUXpOM0l3ZEdoWk1HWkZUbmxsYmtab1lrUXJWMlZaV1RSU2IzWk1RbUpoVFV0QlJDOU5MMlkzV0RSbVRFRlNVbkpFVTJaMFpsZEZObkpLY0hFemJFOURibVJoYjBGQ1QyTnFSa3h6UWpGbGRsSlRWMnRYU1cxQ2RIWmhOMEZSUnpoTWJscENOVmQxWWxvMVRFaHhMMDl1Y0haNGNGcEJiVWM0SzAxaGJUSkRTRkJCVlZCNk5XWXpOVlpCVlZWSWRrbDRNV1ZPVTBFd2VrcEtSV0p4VjJGa05uUklOVGt2WjFScGIwUm9OVU0zZFdSS1NqQkxNMnRvYkRCSUwxaFlUMU5xUm01VU9URjJlV1UxT0hKVmIwUktURTloUlhGYWRUTjVRemxrTWs4NWNYQXlRM1Y1V25waVJrODFRbUo1V21kR2JuSldUMjkzTUVRME5EUTNkWGRMY0RkbE1FRnJaRTUwVW01dVkzYzNTMlJDV0hkMVdrRlJhWGgxTWpaM2EybENVRkpSYUV0TlpucE5jRTluWmt4a1pGRXJOekpXWVdGRmJUZFZPR2xHZDFSTVEyWldOMWhGT1VSdlVUTXhUSFZvTDFSSFZFeGllRGhSU1dST1QxWnZZU3N5UzA5WlVtbHBXVEZGYkZsaFpESXpkVmN2ZENzeU9YY3lWVXcyYjA1VU1UUnpZV3hrT0VwNFZFODFiRnB2VjBwQlZuaFRkM2s0TkVKT1lVTk5MM053YlN0UGFWTnRUekZoZFVSU1luTndObkpyYURNME1HNTRSRkowVWtKeVdFbFBUVUZhUVVOblp6Y3hjbVZFYUhCT2VXNHlTRkEyVFdvMlIzbFlSVEF6UkRObmFqZGFUa2RaVVM4eVRVVkJUMWRSWkdobWFtOW1URGszTDNkdVZqbG1SRlIxTjFWR2JrVTBTSGRtSzI5RFJtUmlkR1UwZG1Ka05tOVdWSEl5ZDBsS2FXVkhjblpCUkRGdmEyazBaVkprVlZVMWFscEZNR3dyUTFWWk1tODRTRVk1TWpNdlEyZ3ZXbEZUYWxwR1ZIWllOV2h6YXpRMmRuZFJTVXgzUml0dVFsUTJNVkJVUldWVlVrTkllSEk1VmpoTU5VUkpZakEzZVdGU05ESkxla3B1YW5ZMmJHbHpUbVZRVkdWMk9GQnVjMnh2V0ZBcmFXUlJhMGQ1TkRNemJHUkhPSG92TUZjd1kxRjZRemd3YWxCelFrMTRLME4zYjFNelkyMHJUakY1WVhoRlFrb3djSEJCWVd4dU5FVnRPVkJGUlRsYU1uQm5iV0ZpSzJkcGJuVjRPRTVHVFZJclZHNXViRFJ5WjJreWRtRjZOMGQzWVVwVWRrSnZUR0ZDZDBSUlZUaENjbU14VFdsclZuUnlOamhEZVRFNFkxTkhhalZ3YzFCak1uQjVTREo2U2tFdmVIQnRNRkl4VnpkVlRERkJZV2xvV0VFM1VrMTBkbFZpYWt0V2JuWlVVSEpyYUdOSFZtdE9SVkp4UVZwRmVHWTJVWHBaU21WVEszTjRiSE5uUzNndlprUTJZemhtVDFWUlEzUkRVSFZYYlZKRmVsWkhjbWhKY1d4dldWUjNNVTk0ZUVKbVVuZGFMek53UVRGUllpdFhPRUpGUjJWcFVFVkpOMHcwWWs5U1dqbHJUMHBDYVdKamJXaFBlV3hLWjI1RGMwOUhVRlpxT0VaNFdrUlJlVEZaVmtwemJ5OU1URVJOYjI5dWVEWXlVamMwUmlzMlpsaDVTMjk0ZFVGVGVVSkpNMnBJTURCa1NURm1VbEJOZEZaTmNHdEZNa1JvZVRaeVpscE5aemR4VTBsSlYyOXlkSFZOTkdaWFdFcEtVbVJDU3pKSFIxRldVVXQ0VUUxcU4zRndiekYwTW5GTFYxSlBSbEJQZEV4R1VHRnZUa3NyVldsUUt6azFZVlp4VFZvNE5VNU9lbGxpTXpGalpHOWxSWGxyUTJ4T1ExaE9SamN3UjFOQ1MzQTVVbVZhVFdreWVHdHFZV1pGYXprMGEwTkxVVTU0UlhwNlZtbE9WbHA0UnpSM01VbFZjbXRtUm5OemFHbG9XRkJCYkVjeFZrZHFaMHR5VVRSSWJtUnhOV3RMTW1KaE1VcDRVbEo0U1RaWmQyRjRUbVE1TldwUWNYTm9VMkZJTTNoM1ZsRm1helJZVGtWblQyWkJjV0ZUSzB4akt6UkNTamhtVDNoRWIwdG1jVmhFWVhreWFEaGlUWFUwVTJGU1dHWkNNV1ZpZG1adlUycEpMMnMwY0ZGWWFrRlBSbWd5UkhFMVIwMWFRa1pyWW1kaVpDOWtkaXR2VVd3eldXSlVOemRHYkhVemJucHVOSGhXV0RSbWJGbElXbXROV20xQlNHeHJVakJOWjFoVFZUSTBaMmg1ZERkS00zcDJRVk5xT0VsUGVHZ3ljak5KZVVjck5FdzRaRVJvU3pCSVVGWk9aVk0xWmxneFlqRmFNbFJNVG1sSFREZ3dVMGRHYW10aU1ucFRXWHAzYTBONE1XazRWR3Q2VmtSQ2JVdFNRMGt3T1RWNVNrRk9Oak5DTVhWdWRXSmxhWGQ2ZEZKWEsyVnFWVVJsYW5sTGFXaHRPR3QzYUdWUk5GaERkM0ZPZUc1SlVWVXJVM0pVTlhsclNHazRjRlppUTA5NmNtazVhaTlpVlZGUU5uSlpSMFpZZGlzclJuWXZTRUZqTTFScFMydzBaaTg0Y2tKSlUyRkZSMmhuY0hKdVNEVnFhbFZ4TkdGcFNXcFNlVTVIY0hCbGRuUmxSMnh0ZGk5dFZreGhVVmxUYldOTk1GVXpiMU0xVTI1bFVUY3lhSEZNTlZJNFptaDFValUzUVVWaU0zaHRTbkpuYjJ4S1ZtcG1kVTA0UzBRemMwMW9PVkpSUVZwdlVWZEVkMkpPUmxoRWREUk1hVVZHY2tjd2FGZEdOMVV4Ym1VelZqQklURE41ZFdSV04zcEVRbXRSVWtjeVQwWlRkekJQWVhob1VuTjBOVmd3Y0hkblNGZGhWSGMyTkRWellWUjVWa2xETjBWdFowRlNUMU0xT1d4SldYb3lZVFpuUVdwV05qVjRlR05KZW05MFUxZEhORk5YUWxwSVVIaHRPVTFPY3paMmRrWnNNMU5aV0ZsR1MzTk1kREYyZVZwVGFVUmpZamMxVGxNMldHRXdUVUZOVDJWdVkzaFpVMFpTWkN0NlduUkdjaXRZYVU0NUt6bHdOVWhCWVhacFdIWTNWM3BwUWs0emJrVkpTRWMyYTNGVlYwcE9MMGhSY3pKT1ZXMTNWVGd5TW1SdWVIWnliMHhuZURKR2NsRXJhakZYVkRRMmNYWTBWekJpUVhGV1FWaE5iVWxRYWxwQlV6Qk9UbFpvT0VSUE1rRnNkMjlUZUdoUWRtZDJZbmQ0Y1ZFeFpHZHdUV281VW5oNFdqTm9RVGxyTURWM1YyUktMMlZIZUdsdFozVlZLM0pSTUN0cWMwY3JSaXRFU0VJeVFWZEdjWEpxVlVkVFpWQlVhMWw2V1RKSWEyTnlTRE4yTWtkWWFuWm9lV2Q0WTFKWmNrRm5XazUxYkZWd2FHdGxTMFJ3VW1waGJuWTVhR3h5VlZodWRYaFplVTVvWWtreVpEQkVNWFE0UTNJd1QxWm5UV2t5WkVaaFFuVlJiSGRFYmpOaFlXVmtabWhFWkRWRmREUmFaRkZWWTNsalJqQldUVkJKUVVsdFIydDVaWGhSTkhCelNYWTFaR05oY0U5S1ZGTTBWa3h3VFVKQllsbzRhRmxzWkRKa1ozUmxSMU4zYTBaQ04yOXJabVV3TDFSMmNEbE1RMkpRYVVaU1VERlViMnh6TlZORFFXWXdjMnh4WlZCSkt6TnZkVmw0U1ZnelFUaEhTRkJhUTNjNFRqQnliRVkyVDBaS1ZEZHlUVWRDVldkUlFYZHVaMGhyYmtkc2VrNDNlbGgxY2xCeGExVnlVelF6V0ZKd1JXWkJXRXAyVUU5RVYyVm5LM2xMYTJsNE4zRmFSRWMzYVhWWlZqbExhRlZJUTJ4V2IzSnhkekZUVUZoa09XMXpSSHB0UVdoNE0xUm5UMEk0TW01VmFVcHZhVTlqWkhaMk1ESlBWa1ZxY1hnNFNIRjNhREYyUkVGTlJXVnFXVFpRUlc5SVpVRmpTMHBZZERaUlJHSjVVbEpWVm1sUFozcHBkMmhhTTFCRkswWTJRV1JQSzJKcGNGZHRUVTl3Y0doek5IUlBWalE0Y0dsRllUSkhRWFJ6Y2xrMFNXeGthaXRtU0dsTFl6aGlSbFJwVUZKaVppOVVOVXAwUWxSaGJDOUdkRzVSWTBSeFN6VjZRWFZOT0VaeVpuUmllbUZrU1RaUk5HVnhXSGw0UW0xNldEQlNNMWd5UmtZclIyWXJVazVaVVRoWVptOWpjRFJJY1c1YU9YQXhlVkp5YzJrMFpHVTNNelZXWkhCVGJIVlNVRmRpV1VWNlZXVmxPRlozWmk5cGEyNVNUMkkxVG1ZM1QyRlBRak5EYlhaUWQxQlNkR3RvVnpORVVXZDRjM054VVRZek9HUllkMnBPVDFwQ1lWQjVXRFZwZW5WaFFuUkhRV3h6ZUhwNlVUSlpTbTVOUTJOcWVYVkJjbEJpTjB4TFNraFVNVUZuTlhGS1ZsUjVjbWxuZWtWdmNHRTFla1ZRVTNsVWNsRm9USEp5YVVGamVVVlphMVpNTldzeWJVTkRRMlZHTkVGRU5Xc3hha2hOYXpNek5HSnNTV2xQUTJSQlUzY3lSSFJ4UnpZck1YbFpNazUzVXpRMWJ6VkhOakl4ZFRWYU1GcEtUMVU0UkhSWlUwWmFWVFJ6ZEdoc1pWaG5ZVWRaWXpJNE5UWnNUbEZrZVU5NU5HNXBiVWxDVVZoUWRHTlFkR3B5TjFBNVpHUkJWVkpNYzNsS1VubEdlRkV2WlhSMFQwTnVaRkJ0VVRCWmRHUlRUVGxCZVN0elVFbFNVMGxDY1ZjMmVsVlJabUU0WVVoRU9YTjBVM2hsZWpoTlRYbHFRakJWUjAxWlZtWlhjemxzYm1kT05UWm9lakJqY0M5dVNEWkVVWE0xZDFZNVpuRXlaMjVwTVdGdmVtZGFOazFDV2xjNFJUUTFOV1EyZGpORlVIaG9XVkozSzJjdlV6SktNelowWTNWcVFYZzJNMWhvUlRKa1YxRXpVV3BwVkZCSmQySnZlbFI1VGxscWNqSm5Xa0pPUWs5WGJYQTRja2haYUVoNk1WbG5hM05OY2tGcFVIbFpja2xUWTJOc1ZqVkVUWFZqU2xZMlRGZDZUWHBXTWtzMVptTldXVGxLU2tGYU0xSTNWMjVCVUdkSFEyaEZaMWRuTTNGTE1uTjVNWEpQYWxsSk4yZE5ZM0pHV2k5bEwwazVkSFZ4SzA5dU9XNTRaQ3R5UkdZMEsyVjFOMGd6VlVRelFWZFhiR0kxYVZRM1IyaFJhVTV5ZFhvdk4yRTVVMGhLU20xcGJHeFVORGgxY21wWll5OVhUREEwYmxOb2JFWlpLM05MZEVreE4yRjZkVWwxVjNKRFVITkpja013YTI5clREUTVURmt4UmxOc1dsVjVUMWh6ZDJ3eWJHaHFUMjVVV1ROdk5HUTJjMnB5VkUxYVRqRnRZakZQVDBORFpXSjFTRE55Ym1GbFVXSkdTSGhEU0dwVFdIZHZNa3hQVFRobVUxSlpXR2g2UVdSVk9VODRTa1J6Y2k5cWRFNXJlWFpLYm5vM1psVmlObFZHWjAxV1JtUlVWazQyYURkaU5YbHBiR2MyV2tadk1TdHdiRXRLUWpCQ1JYUk9aa1pGV1dVdlVHVTVSa2xQWm1rMlVIcEZSM2g0Umt4Rk5XUndWVXRNTTJRMVJXTkxibWN6UlZWMlFqQlRiMmhPUzJaTWJEazRaR2x2VmxCbVEwNU1NbUZqU25KUVJVRXdWVGRyTUhaSGN6aHhiRVJWVWpNdlZFd3plbVZhZFdwclYyWnhhbG92YVdaTGQxcDJVVGd6TDBobFdUTnpaM1JCZWsxSmRGcGhiRWhoVFVOak1VVmpORTByTURKaVVtMXJTVEZHUmpWUWVWUllhM3BzSzJkRmVVWlBVbVpUV1VabVMwSm5SeTloZVZNd1FtRlZNbTR4Ulc0NVN6UXljemcwTmtKVVVESkNUbVkyVUVWaFJsUkpRamt2Y1VKbFIwdE9lRWwyUzBGYVVVMWtTRUo1ZHk5dVlYcERTbEZSVHpOVmFGbHdReXRXWTFOdWVIVjVUbGh0VmtJMVUyTTFhelF4TlVKbFNFSnFVVEZMWWtkdFYyb3ZRVkUyYXk4clpHSjJaRk5EV0ZCTWJXSkxXa1pVWkhaek5teE5aMEY2ZVRscmREUjJhbkJWSzFjclJtOXVOa0pRY25aRGRTc3lkbFUxTmpObVJGaFRkRVJIYjFaRFJtWkNhRWhTYWpsMlVqSkZia2xsV0cxdFVVVTRhSE5hVEhoaVVHSnpkR1YwZFZFM2JqRnlURXB4WjJGa1EzQTFUbWgyUVZOS1MxRlpRamt5U0d0cmJVTndSSGcwVFc5U01WQk5SV1V5UVRGcFdsaE9lREJWVm5sRGQyNU1SMHQwZVUxQmNUSklNbEJHVm1oaWEzRlVhelpwYmtreEszbFJla05aWkdWR1MxZHFVVmQ1UkV3d2RuRXdTMHByVDNGVlF6UmtMMUk0WjBjNU1ucHlkRTVFVVVGQ1NtUldja3hVWm1adWIwSm9RV1JaZG5KME5rcFpTRXBPY2k4eWVUTXJiVGhRZWt3NVJsbFdla3hDTTBkUFoxWlRXVzhyVjFGc1FVWlNLeXRWY0Rsb1ptWkNMek5ZWlUwNVdHWXhkRVpSSzJkamVGTmlOR3BSWW5wQ2RURXhjM1I1UWk5blJqbHFjMHg2WjFsaGQzQjFVMHMyTUd4dVJrbzNkMVYyWkRCS1ltdG5la0paZFcxU2NFWk5SRlpQVTNOc09HY3ZhMmRXVmpWUk5HNVdiQzh6VDBOWVUydHpTblpXT1RSWlNtaFBlRzF1YW5CUGJERjJVRGQzTVRWUFdISllZV0o1YzFnMlFUaG9XRWRqYmpZMGRtWktZVWRrU1ZWdmNtcFJhVTFNYlhaUWVubDRRakJYVEdWMWNtWkJNUzg1WldGaVMzYzBVazFyTXpWcGIySTRlV3RNWkhReFpETXdRM2xSYVhkNVNuTnBVVlpVWTJaaGRXaDJkRUo1WlRKWlJrVk1NMjlqUldFMVVtMVJiemt6WldKVmIwMVhhbFpQVlRSSlpVcGtObnBQYlRFNVdqa3laRmgzTlN0SWR6WllkMnB2WmtkQ0wwdFpWa1ZrT1RKblFua3lZV1l4SzFaSFVVcE5jM051YTJWNGVXZzJTRkp5V1ZaaU1WZHFTek5EVVhGYVlYUXlPWHBWV1dwMGFXRXhNblExY1ZSS1ZEQk5OWFJZYmpWamNub3pibmhIVVRkV01rbEpZbnBFUTBsdE9FNWxhMHhRT1RsRGFETjVNRFJsUVhOaU9WSkVZbGhzWjI1bE5HRnlWV2g0TlZOWldYRndPVUpqUjB0QmQzUjRiVk13ZW05SlRTOXhjalozZUVGSlVIQjRTRmx4TnpreFFUTnRNekpOYUZob2MwWnNUalkwTVVkdWFERndWSE5ZZERCNWF6Um1PVTVqVUhkMVdHd3hNMnN3SzBnNFQycHZUMnRQU1VKaE5tRkNabTF6ZFcxR2FqY3ZUVWxhVEZobEsySjBUWFZ5ZURSb0wwZFVWMmxJVDNrd09YQjJRVE53YlhkQlFYWkpUVmhaVUhrdlV6VllUMjFsUVVkbE9UbENjMkoxY1hCSVFUQlpVbnBRUTBRNE16VlNVVlJEVUVRMmNDOHJlR1JFVG1SdlZFMVdVblU0Wlhsclp6bGlXRmh1WldkeFoyUkpPV1pOVUc0MGRHUmxXVmhxUW14emFtcGlZbUp0TW1ad2VGbFVRM0l5VG05aVFXdHVhbGg1WTNKMFkycFJOMVUzTWpJMmJsQkRWemswVDFoYVRGQXJla3RTYkhSdE1pOXJOelkzY2pKUGNXTXZZWHBRT0hsdk56aFFTVTAxV1Zkak0yVmxla2hZV1Vjd1V6SlZWa3hFYVc1emMzUnJXbE5TY1dsbkt6WnlOREZ6UWxWa04yWTJWMlZMWkZoWFZWRTFVM2QyY0hSRlUyVktkVk5WT1ZOc1RFZERSRlUxVVVGRlIwNTVia3RWWlZaVmNsbGFlSGRGUVdsaVZrMXFZalpWTDNRMmEyWkpVa0V4UzFoTFZWcEJSbUpJV0RoNWNrMHlURW8zWW1GM1VuUXpVR0pCYm1nM1VuUm1TbkpKUzBadE9IRkhlVk40YjBSWk4zRmhReTlXTmxSdE5WY3dNbkEzTDFWMWNqUjJka1ZyZFhnM2VXMTRWR2hNYkZWbldtVkdVSEpwTUdkaE5IaHBVbms1TW1kTWFXUkpWbXhpVTNsWWVGWTBlRmxxZEdSSlJHVnFWRUVyYVZoT1owWlBRVmc1TUhoeGR6RllZamhMTm1WbVdtaHVTbWxoU1c1T1NqQlNUR1ZEUzFweU1GbzVZM05uVVVrNFpTdFVkVXRqWVUxeFVTODJZVWhXVm1oNmQxWm5UbVl2WldaVmJpdHBkWGRsY0cxUlVVSndia2RYWkZwa1ltNVhkV1pXYVN0UVUyaFFVM2d2TUZVeFZrdHpWMmRKSzJFdlNETnVlbU5yU0RGek1ISXlPRkprYTJSaWR6RlNkMHc0ZFcxMWNsRTRjUzlaWVV0UmNEWlNUVW8wY2xkNFlXdG1OMGRSV2xaaFdVMTVaRzEwWjFWRFFWbHRVamRYVTJaS1FYVldTREZ1WkV4bFRqSlliMmR3VFZaclJGa3pSR2xSTUdSQlkwTlFLMmRwUVU1RFRIRldla3AzYUdsbVNHWlNLMk5UTVRFNVYxWTFlbFZhWmtSekt6aGpaMDUxVkcxV2MweHZNR2RrVVVNeFRVMVhPVVpsVWpCcVNFVTNWbWxJTkd4cFIxcFhhV0YxTVVjMFptSlFkRXBVZEV0clVtOXRjMkp0Y1dJelVpOVNSakZEVGtwNlRqWnJiMVF4TjNOak4yeHdNMEZ0ZFZGVlpWQk5WMmd2Y0Zwb1ZtUmhablkxUm5SMlNuaDFORTl1T1ZKRkx6TXZkM1Z5YzI4Mk5ucHlNMlU0UjNCTFdXSldNVU5NWWs1bFdtOU5jVlJNY0hRclJ6aEVlVzV2ZVROUk5WZ3lRV2RFYWxVMVVVcElablpQUkU0M2R5OXlablJJZDJJeU5TdEpaRE1yVlV0MFVIWTRjek5vVXpBMVJHeFZhV2xUWXpVNFYybDROUzkxYlhkSFVIVnRiM296YzNwdlVrcE9jRGxWUjNCNWJqSm5SM2RWY0U1a1ltZHJiMWhwUlVKQ2NVZFZXWGd3TVdzd2EyMVlTVFJJTHpsREx6TTVSa016UkRndllUTjRkRTV1ZEhwTlIyVjBkVWRLTkhwVU4yaE5aSGhsZFVWNFpteFFSRXBVWVVsRmJVWkZSbUpaTm5oWWFVSm5SVTlOWldKVk1WUkhjQ3N2VGt0VGNtVjNOR1IxVEVoMFNHVkVWMlU1VUdJNWNFcFhOa2N6T0ZBM1VrTTVVMkpKWkZvcmNsaEJOa2QzWWtnNVlYVm9iRkJGTHk5RWRHRk1TVEozU0hkc1lUZzBlRk15VFhkUWQweFNMelpsY1dwb2MxQnpNMnBuU1doa05VTktWbEZvTlVGcGJua3lTamhoT1ZvNU1WWmlVR1V2WkhKTFRFZ3lTbXB6VUZSbWMyeHZOMmR3TW01TE9YRk5LM2xpVTJGU2JGVXpWMW94WjNadFRIWnlVMEp2TjFCSFpsWm1WaTlJWVhvcmQwUTJNR1p2VUc1UVRFWnJVVWM0WTNWbVNIQlhUV1ZvTVRBd1lYbDZkalZMUWxabUsyczBOR3d3TUZoWVVYRnNaREp2THpkbVdIWklPRXhKZVVNM2RGTnFibG8zY0RBeVFVTmtLemxWU2xaVlYwcDJWRUUyU21wS2RWSndVbWh0YW5BNWRFNDNhMlpQVkVFME4zb3lhV3BxU1RWR1ExbGpUMU16Y0RVd2NVa3hVR3QyTVdjMFZYTlBUMnhYU0RoUWFrRmtkMUJ3YkhWNldHVjJPSEpJWVRkUGIyMXRVaXM0VUhwR2NGaHFhRkZ6YlU4eE1rbFJOemwzUTJGV05WbFRRakZ4Vm1GQ2RtNWFVbGcwYlVkVFJuTTBWblZGYmxGVGR6bHNlVTVLY0hCSEsxSlNOVlZtUVdzM1VqaDBiMmQ2Y25adlpqSjJaRkEzVDBkeFMzbEtZbFZIVWxCUmVsTlpia2hSVlM5TFdsWllRMDR2U1c5MVIxRmtZa0U1VWk4MlEzVlhVazVGUTA0MFFVbG9SMFk1VW1sT1JEWkRiVGhyUXl0bldHMHphR1ZSWlVzd1lqSlNRelZxTW14cFJ6aGpNR2hIZVdOaWNYcGlOMFo1V1ZwS1YwY3dZV29yVG1STmFXWkNMMGRoUlZvMFdsbExaMUE1TlZGUE1VVjNlR0YzWjBORlNHSk9PR1kzUW5aS1Jsb3lRM3BNVFZSVFZqVTBUbEJ6WTFsS2NXWTRUM2RQZVhoc1drOUhTazUxTm00ck4ybFVhbFpSVmpGVVRVSjVNak0zUkhSM05UQXhVMFJZTldWdlRGVXZUR2Q0Y2tKek9XRnZjME5pWVRSMVkzbFFhR2xSWTJ0NWNHMXNRM2x1VDNoclR6TkxiV1ZQUW01VlNGTnRjMDVDYkhkVFJXMUlhM2QxUVhZMVpVRTBWVGxyUVZwbU1WTk5iWE5tYURkSmEyUlpUVU00VGxoc1RtRkdWRWxQVGxSemJURnJNVGh1V2t4MlNVbGtlRzF0U3lzM1F6QkJWVkpKU1U5dllVZFFURmhhYlRCbGRHNUVTMFJIYjBGek1sVXpablJNZDFZM1YyVlNOekJETWtGTEwwUnBiRkZEZUU4d1lYTjZSbWxuTmpGMmFEaDFUV1ZzZVdWQk1XaFlaa3BxVDBSdWVISk9SbGh5ZFdoYVpVVXdUaTlPYWxaQ01VWldVbW8yYVhGRVNVSTRTQzkyTm0wMk9HSldjVmRTV1dRd0swSlljM3BPV1hkWGRIUXZSa1F2TjJWS1R5OVVZMGxRZUc5dWF6ZHVOM1JtVm5ObGJ6RldhR2RGYkdkemNteHVRbWc0WTJGUlJYTlRNMUJTVFc5MVRWbFhSV1l4SzNOMmRtaDJlWHByUVRseFVrSlNZekZYWVdwTFoxQjZja1pVSzFWeU56WktVbVoyZUhCVVZHeDBlWHAxZERWaFEyVkNhSGQ2VWs5cmFISmxibmRXZEVWNGRWaEtUbTEwVVRVeFJYbFBiVTR6TmtWRWVqVm1NVmh1TW1STmNpdG1OV1paTURacWJHTm1hVzVTVWpsME9HMVFVVlExYm5oWVVGSjBSVVpsU2tWUlJTdHNha056Ym5sWU9YUTRRazA0TWtGdmVVaEhVRXQ1UjJKdlJWVkhSa05YUkVRdk5VSTFlVVZVUkdvdlpsRlFWUzlqTnpZeWRIZFJkVlYxVUhoQ1MwTmlMMWwyY25aNkt6UktVMWMzUlN0NVRFYzJTbVp0ZEVaaFNHeHZRVGRhVVdsdVYwNVVNbkExYjJkVU9XUnVTR0poT0d4UFNXRllUWGRXVmxGS1luVkhlazVNTWxVcldrTjFZazQxVFdONGJtcHBiakJ0TkVOWFMzWmpNV0pSZVdGcE4xRnphME5DZWtaNlZtOTBSR2h2Vkd0YVN6bEhVa2w2U2xSblptSnVTbUpZVEU1UGMyeDVOVUZKYTBOUFptRTVWblU1ZWpKdFdFWk1NRGx3VEVWS2MwZFJjaTgxV2xwNVVsUlhjakl5U3pGdWFscFFOMWgzV1VzMFptbzJMMGxGY0V3d1IyRk1WSEZRWjFKRVRqWmpOMGt2V2xadVQyY3hWV2RHWWs5bGQwNVpWMlkzV1Rrd2JYSkVSbXAxVDI5eFVHaGFaMGR1UzI5TVJVdG9SelpFVW1VMFUwaFdRelZMUVVaemNWcERlR2RVV0VnM1F6bFhORXRLZUVwUmFUVm9aVWcyTUUxaGRHeFhORUZyUms5QmJtVktXbWhwV1VOMGJqWmhkRTVKU2s1RlUwOVlNbGc0V1d4Q1VHZDFObTFVTW1WRFNHSkxkRmg2YVc5YU4wRlhUVEZPYVc0MVVWZzFWbFZMUlRORFJuUjBlSHB1SzJvemIza3djRUZaUVU1TFZsVmhWMnQ0TUVacE0zWjFUVTR4UXl0dWVVbGliVTkxUWt3MVFYZHFWMmwxUm1OUWJtb3hka05yVFhkS1RtVnpSbmczTlVGWlNteDVSVEprTDNKTFRrNWpWbkkwSzJaTmFXTjZVelpITlhReVpXRktZVVZQT0ZwR0x6aFlOSHBFWW1oSGQyOHhNRUZQU2s5clZYcE5UVGc1WTBGQk15OVhWV1ZsU25SSE16aExOVkJ6WTIwNEx6WTFWWFZaY21oT1FYaEpjbWxuYlVRNVdHZDFXR1ZaVEZKemEzVmhWa3gyWkhCRlFraHplbVIxVVVGR05ERk1UbGRqTDFweFlqaFhibWxFTTJkeGRGcHZZekV4YmtsNldIUlhOMkYyYkRVclRXOUphV0Z5V0hGamExWmxiREI1V21scWNVNTZLelpLSzBOR1lscHRLMkpwZVVKM1RHaFJkbG92WVdaMWFUUjJRV1ZvZHpSelVXdDVaRFIwTVROUE9WTldSbmwyUVVacVltTlJaQzg1VW10a05rcFNaWFZVWjAxWlRGTjBaalJxTnpWbWJucFlNbk50YW0xc1FubzRUblZyVG1sUldGQnJXVEppZWxZelMwbElVMWgwUVVkNlkyUjZhRzFhU3pkelVqVjVhekZuTkVORGQzRm1WR3hEUVROellVUjBjWFJUVjNCTVluaHZhV3czVmtWMlEyVXZXWGQ1V1hoT1RHNVZiMGg1ZUhGM2JtMWFkalJEUzB4V1JrWnNWSHBYU0V4WlNUQjFLM0F2Y214a1pXMVdWakl4ZGtaS2JHVk5TVXRFZUdVeE1rSlphbmg2UkRKcFFuUXhOemRQY1V0WVQyRk5VSGQ2TVZJNVFrVjJiakJhUlhKVFMwbEhUR0ZRWjBOMlVuVkJXSGx0UW5sMlpuZDVUbUZMUkVWcGFsVlpTM0ZqUkhSbmRFNUtkVmhVUlN0bFNEbDBReTlPTlZGVFpucHJabTlKZVdSb04yVlJNRnB2Y1ZwVlMwUmllVFJvTTJsaFkxWTNRbGg1YUZObllWY3dTRGRhY0U1VmRFODJiV1ZpVUVSVVRrcHFZV2hwU1RkMGVISnliRWxUVlhGdmMxRTFRMXAwTkRaVlYyUnRjSGc1VEVVMlFYZDVjVk56ZGtwWVZ6aHRkRkZ6UkdWMFVXTjRUbEl3ZEhwMWRqUTFPVEJDTjFoMk1HTmlNbVJPT1dWMGEwZHVZa2N5T1dRemJqTXdURk40ZURkcGR6TlBja051VFVKYVdHZG9iVkpvUWtablJVUTRUMmxrZURGTlVsRTJabFJXY1hWYU1TODRSMmQzVDNaR1JIUnNiRFJXY1dwMVdWbDNRVXd4VTFsQ1NIcHpjR2g1YW1velQxWXpNMkZaUldsa1UwOXRVbTQwS3pOamRWbzBSemMwVjAxb1VXaDVMMDB3ZGtsSVVqazRZbEZtU21KcVJEQnFNMjlEUTJkaFNGQlFaM1pRVms1NVRIVXpkMkl3Y21sSlJIVndUR3R5YWs5aVVuQktTekZvYkZaSFl6QnlWekZ3YW05Q2QyRjVTakJSZGpKa2VFaG1PRFpQYmtVdk9YTktiblV6ZFZWRU1HVjNMekJ0VFVObE5WcHpOM0ExTkUxelZEVlRaMHg2TUU1bFpHVjRZMDlKUW1wNVIwcE5OVkJYT1VFMlVqWmFhR1JWWVd3NVZrZ3dURGh2Y21wT1JFUkhVV0ZJVEdONmIwdHJORE5HYkUxNldVNU9aWEoxUlhnNGFrOTBabGh2T1VkRU4wdHViV1J4WkV4SlJsVmlXbEY2TkVKb05EaEtSRUV3VkRCTFRFOXZRV2RYYzI1TlJtWkhibnB6YjFaU2VrZzBla3A0WVRCcWFVVXliVmhtZWtaU01ESlRkMVYyUVdwTk5ITlpkVkZqVUZRMFdsUmhOVUZxZUROWmMyWjNTR3BtYVd0cWFYSnZVbmQxTDNaclZFVTVZbFp2WVhCT01EUjVSRnBEZUdwaVVuWmhPRkpCVW5CalNtUTFVR2xCYjJ0Q1owUjRPR0p4TkVWcVdHcFhjbU5EUVhGTVFVSnBZbTAxWVRjd00wUllOMlpYWW1sT2JIWm1aRzFZY210aWR5dHVSRWxVU1VWcE5XdHFPVzE0ZFdWVlExUndVRXhXY2l0Q1luRk1SVFJNWW1WdGVHbFZiMEpYTjBVdmMwdHlaSEpVTm1OWGRHdGhTME5ZU2xwVFJXMVNRbEpGVTJkRlMyeHhhR04xTDI1MGFtaE1TeTh6V0dGcU0zQm9jRVpFTDNBMVIwSXpZblIzTWtVM1NGRTBZM1p3ZG5ObE4yeFJhbkZDY0VoM2VqUTBRbGxGZDBkb2NXSk5ZMk5LV2tvdmJqSjBRamhSWWtWMFFrVk1SMEZ4UW05TWVXNUZUMFp1T0VKcVVuSXZUR1UyYml0VmNtbEhibVpoTlhaeVltMXFWVTl0UkUxeEsyazVlVlJQWjJaTFpFTXhiV054UXk5RllYUklOWFp5ZDBsSFRVeFZaMUJxV2pBMFVVRTJjamM1YVd3eVJtWXJkall4SzBGR2RIZzNiVFJhUVVsMWMxWjZWM0k0WW1aWVJGVnFPQzlOVjJVclZrbEZSSFpvYVRGWmRHRkpNSFZ0UjJsMGVYUjRUR2h4WjBOaFMyTjBWQzlVS3pBNFQydFBha2R2WXpOQlNIaHNWVlZuYm5SNmQxZHJaMlJZZVV0Q1RVTlhkblJKYUZONllVcFlla280ZFV0MWFXZHhNRlJEU2paRlMwbHdSa1I1YUdWRFJHdE5RamcxWVVsWlJYcGljRkZ0VWxkSFRFa3JUSGRWVDJWQmNTdFRUa013YkZKd1kyZEpiblV5UjA5WFVtRnlkM1p6ZUhGR2JTODBORk5YYlhwS1MxSndSMWdyZG5wU00zWktlRzl1YlRsb1NURnlaRzVuUzBWS05HSjVWR0ZRVjFVeGMwa3pVRFZaYzJVck5FMDVORVZzTW5GTFRtVkVWRTFvUWtodGRHZE9ZbGRRU0dsSVdWRm5Ra0VyTTFCVkwyRTBjRE42UVU1eVJXZFZkbTlRYXpkT01EUjNOMU5SZUV4emMwMDVPVFZzZVc1aWNXcDFTRWMxUW5jeGRYZGlkMmg2Um14d1FUbHRUM0ZWSzFWM1VVcDNLMEpaUVhCQmFERmlPRzFIU205cFYxTm5ZbXcxYldkUE1UTldabkZOWXpGbGVEVnhjazlrVms1aVJqWmFTakpXVjBSNk1FZzVZa0pNTlZnNVRUbGhiRk5SUzFaVWRIbERObUU0UzBOR1JrSmhkek5IVVZwNE1HRmxPVkYxYjJWeFVEazRTM1pHYlROMVNrcE1Wa3hUYkdaVVRYaFJNVWR1TWxkWFNuTlpLeXRSWnpaek5EVkxlV0UwUm1od1VVNUJaekYxUVVWM2JrOVJjRVpMU1dOWGFHSmtPV051YjFKc1dFRjBPRU0zVkhGb1ZrRk9aemRyY0hoNVN6ZG9jMnRqWTFkNU1VMXJTRlJNWWtkSU9FRnNjMjR2TkhKeEwyVkZjbEkyYUZRdmNGWm5SR042UjFSRmQwcFVTRzAzU3pkdFppdDRjM05SVGtkSVQzWkxVbVpUVUVwUGRFUlNZazlKTW1GT1VWYzJaMWRtYlVobVFVMDRjakUzU2poNFpETm1lR05ZYjNKRGFHUTFWMDFPVnpVMmJFeHlZbFZTUXlzMlQzUjFLM1paV2swdk5ESkdlRXAzZUhBemMyeGhZekpHYzBaM1ZGVk9lSGs0YWtjelIwRldka0ZLWkM5eU1XcElPSFZzVUVOQlNHOU5hMkUyVm5GU2NHUTVhVFptVEdsYVNraFBaaTl2UVZOUVNVcFBObkp4T1doeE1VTkJSRmROTW5wd0t6aFVVaTkxU21WbWJWSTNUSGR2Y2xGM1lubEpXbkJ1TVVGYWNtUkhRbTFhVGtRMWMxVnBNVlZETXk5NlRDdEZVRzF2ZGs5R1R5OU9kWEZuZEdOamRTdFdkelphUkhOdFpFeEJSbmd3Tlc4MU0wNVdaa2h3YUZOcFJUTlBNelZ0ZFZWR1pHVlFUekl6UzNSSWFWQmtOMjl3WXprNUsyRmlkMUppT0hveVdqQk9iamxzVWtWQ1ltdDFRamRIV0hodVdrSXZNRmh6VkVSWk1ubFVNVkJ4TDBWd2NGVkpOV05uZEZOaWRYSjNlRzlwZEV4SGFEWkxSMUZ4U2twblNEaENMMVZPY2pCNVNXWkxjMUJhZDJ4clN6WkxWVzFtVDNaeU5ITlNSUzlsTlU5U1NsVlZNMFJMVWxKelJIaDFiMGN3ZUdKa05tdFFUSGswYkhaTFdtaEtTVEJRVURVeVVVUTJPRTVWVW5aeGEwTjJlblJOTmt4cWVXWk1PSEpRU2pVdmFYQTBjbGRTVTFadE5rVTRibE5HVEV0bFEzYzNiV1ZZVmxka1NWRnpOekptV21ORWVtcFRkQ3N2V25OVk9IWlhjMWRRTkZaT1JsaDBRekZ1Ykc1RlpUZzBhek5UVFZKeFJYVXdXbFp2TURNNVluaG1lRFo1UzBGdE1EaHpiMjl3Tmpoa2RGcFdhblEwYzBzME9XcHRRelJQUmtwd1pWZzFiMngwV0hOcmEzVnpUMGhtWTFVMlIyVkRkMGRETVhOcWFFaGtRWGM0Vlc0eGVHNTVUbE16WlVWNEwyaHhVa0V3YldkTWFEY3ZRVTlVWW1SSlZVNW1SMGh4VVhreVUyOUxOMDlzUTJSRE9IaE5WVUV6TmpCbWFYZDFWRGQzVEZwcVFtMVFNM1V4YkRWdFNEUnlVV2hqYjBSRE5GZzJXazV1YUdoWVNra3pjakpQYVdSblNUbHpMeXQwV0cweU5HbFVTMGRLWWlzM1owdFZOVWxRZEVobGJYSkVZMVV3TldnMVVUTlRjSGxMV0dvMlltaEJORmxpU1hkTGJYUTBkRm94Unk5RVRXWXZWWFZ3Ym1wRVZHaFZZVmxLTWtoVFYxZERUa1o2UzJwWFEwZFhWMW8xZW1oV1dHOUpUbUpZUlU0dmVrMDNURFpLZEVGVGFGUlFVSEZTVEhORVlqSlZla0kwZDNKMWRrOW1VSHB4V1dsdkx6QnlSMjVSTW5CYWJETm9UM1pPTmtKcVMwRkNla3M1YkRWVU9IRkVVMlF5VVdSMVJ6QndjRWc1UzNZd2FtaFhWbVJUZW5kSlIzZE5WRXAxWjNJeWFFbFhUbU4xVUhSV2RGcG9OaTh5V25BM1RFZEVOazVQUTFKbFkyOUlVbGxJVlhaSU9ITk5TRlI1VUhrd2VXVk9aVXBLU3pKUE9ETXJkMGw0UkdoUWVraHBPREZDTm1wMU9WRktTMnhsVVc5aFYzcGxObEJvYld0eFFURTRVVVJ5YlRaM09GbFRPVU5FY21OWk5uVnZiR2RYWTNVMlR6WTRSMHR2S3pVeWRuZExTVzV2WkRaUFZWWkVhamhpZDB4dGFYRm9ORlJLYmpGbGMxTmhTR3hpY1VGbVlqZERNMHBNYkhKWVN6SnRPRmhPZVZaQmJVVnJXazg1Y3pKQ1JXWnRRVGxXWTNWeFUweHBibFI1ZW5OWFpVdHNSWHBXTjNJdlVXaEVlbTFSVFhSek5DOXZSVFlyV201cGFHZExjREpZUkhaM2JtczJha0UzVUcxT1JXMUNOVUpCYjNGelprTllPVzg0UlhoeEwydHBSMkU0Y2xocmQwSlJjVEpNVlhoTldFdEhhMVptTUZSV01sWXdOVzh4UWxKSGN6RkJXVFZhWkdjNWJUUklLemc1ZVU5VlNEWmhVMjkyYm1kTVZubFhkMVpvYVVsYVNFMWpTWFpSUVVGcmRYWTViSEpaUWtoVlVYazRUbWRYWVhseFpIa3ZUVUY0YkhST2NGTjFUREZtWmxsUUx6VkVRbEJFVW5odlZFTjFXVU5zTUVkdmRYbHJaSHBUTkZKYU4yeEtRM1JPTkdwdlZHZE9Va3BPWmxoR1ZYZGxkVTFQYTNSamJ6ZElXVko1Y0hSUFIyZEZkakY2TTFWVU1HRlljWFZ6WVRoVGNXSjVVMmMxY1UxYWRYTlBkVVp4UkZOTVRsQnNjMjgxZVU1V05rOWFNVTl1ZFM5aVZIUjFTbWxrVTNCRVJIUmpLMDUzTW1ZNFFrTkhRV04xU1VsWlJWSlBkbkZtVnpKck1VRlVNV1JqZVdwNVdXRkpNR3R1WjFFemJFZDVOV1Z6WVd4TFdrZE9kVlJ1YzA4elIwTjNVVmRSY2psSVMyVnFTVEpUZVRaaGRXbDFiRUprV25Wb1VrbFJVbFJvYlZvMFFrMTVjSFpST0hsS1NsTTRSMlJOSzBKQ1RtcHpaSHByVlVRMlNTdElMelZhVFdwak1tSklkRlZYU1RnNU5VVkpOMDFyY1hOWk5EVlBkVWhYTVdrNE1FcEZWRkZtWmxaa0wybHZhRzFXTmxoVVVERlJNekpIV25STlRYVlZRbnB0VVdwNFdVOXJXVEZpSzBKQmRHRnROVlZrY0VzMlVYSkthMHhqWVd0UllsZHRNV3gyUzJKamRGYzVXRGxUSzJONGQxRlROa0ZETWtVNWNsaEpMME5oTTNsaGR6TmFTeXRoUkVkWWFuUXJjME5HS3pKNFZXeFdkalZGUTI0NWVuVjNVRGh2VlN0Q1pUWkhWVkZaTHpjMFpVbENLekEwVjBsRGFqSTJTRmw0T0ZSaFZHa3lOM3BRVjJkV1YwOXdhazV1YlVkRU5rRk1VR2xPYWsxT1FtOXJSalZ3TUhoMmMwWTNWbGtyYUhGMlJHSkROR3BOWWxaek1DdG5hSEZrWTNkRE4waHhRV3N6V21OWllqaDNTRXRDWWtnek9FNU5aWGdyTmpsSmQybzVkVTV6WmtSdWNHNU1UMnRaWjBSVldHRXdTVWxQTUdvd1dVZG5OVTF0YlU1MVoxZGFOVmdyWkUxVmJUaHdkbWxhV0RKcldGZGpSbmQwWjNwcGNIUkpNVmhJWlRkVk5HMU9jbFJNVWpJeVpISlFTRmRQU1RSalkwVlRlbmxQTkhSMGQyZGljSGhNY1dsa01EbHlRbEowUVhZelEwOUpVRGt3WW5kdU4wbHJkWGRzU2xkNGFGbDNkQ3RYVkhWd2NXTnpMM2wzZDFsT1ZIRkxUR0pOZUM5QlVHNDNibFJ0VEZoNlMwcGpaMjVsTlhkSlFsSllkemhMYWtoV01EUlJNMHRaU2t4MFZVZFNUMWN6S3pOM1dVWm1TR1UyWVZGemJHbGhOakpPVFhVdmNYQTBaR3haY1RKVWJYQlNiMnMyU2xKSFYyaEdhelZGVDAxQkwwNUdLM1FyVm5wU2REVktXa2d5YkdWeE1ucHpibE5HYWpOME9ERjJjSFE1Vmt0UVZ6aDNlSFkyTldwUlFYWkVaR1p2ZHpscWQzRXhiSFF3WVN0YWNteDFNMHRFYzFoaVkwaHNVbkJ5WTI5cGFIQlZOWEJwTjNBNVEyOXFha3A0ZFhCTmFucEJhaXRaWjI5bGFHWm1iVzlwYldGRVZuSjBPR0ZFZDNGWmJVaHRhQ3RZZFdwVmFTdHlVVTVUYTI1dmRtWjRlWGg0YzFsRGFteGtTVmhvZUVSYU9FTXhRekZHWmpWTFYzZElVM2xsU21keVREZEVkekJETVVsS2RqZEplVUpWU1dWU00xQnlWak5tWTBjdmVqRlJPVFJSVFVkWFJHZDBSbWRJS3l0bFptRjNhMk0yTmsxMk5tWllTRzFpYVdwNFJrMUtRbXBsYW1WR1ZWTjJNMll5ZEd4bE5IRklWemxuVmpRNFVVaG9ja2szUlRST016SjNNVFpZWXpjM2MydGliRW93ZERWSWNubGhWa3h3VFhSb2FpOUpPRmh0UlV4T0wwbFpZbEV2Y20xclJrbzRTblJ6UVZZeGFHTnhiVmhYVjNOV2RWaE5WRWxyTHpGNlZUVm1Vek5OVEdwT2NFVmxNVkozUXpodVJFWmtlRE5wYldoTVRuaGFNVk5JVTJoMWVGTjBSWGhaUTJOMFNIVlpNR3ROWWxWdGNXcFVPVWxsVDFneFRWWkxNbTFIU0ZnMWFFMHZNV2xOVW1wM2RWRllZVkYzYlUxc05GWTRUWFF5Y0hoWlltZzRkbVZNWmpOMFIyb3ZNelJaV1RWdlJsRlFaVUZ5ZVVaQ2NFNUhaR1JNY1dneVpEQlRSM0pCVDNGMk9YWkVSRVZ2VW01RFlURk5RelpyV2t0eVpGbDJabVJ4V0RCWFpYQjZWR1kwY1dwaFFVSXlSVWd2TlU1WGVWVndjVXhKV1dkeWFqUXJWakpRVFRoTWJtRkRhSGd5UzNGb2NtZG5jbVI2UkRoVlQwWXpkVTVhWmtOc1Ztd3JObXA0ZEdsd2MxVm9MMWhDTmtGWFltMWljalpaTlc1TlZpdHNhV0l6YWpsVWVtUjFRbkJ0YjFGYU56QTBjMk5oY0M5SVZFNDRiR1p3SzFKNE1YaFVRV05rTDNnNWRUbFpZMlU0Y1M5TlpVSXlTbWxJV1RScloyOTZaMjF0VmpSRlMwMWhPV2RuTkN0dE9HTXdWR3RSYkVwUE4yeHdVMDlpZWxCd04waFRjRlpyTDB4eGRFUmpMMnhMZGt4VVExRkNPRUptYm04M1UzZGpZM1Y2WTFoamJHUm1jRmt4TVVOeVJrRkdXRGQwYjI5NGFEZFpjSEJLVUZSS2REbEljMjVRTkcxbmRtSm1hRlJ4UjIxMlpIbE5WakJ5WmpKak5ubHBSRkkzTVdKSmRITkNNbEZyTldScFZGWkVVazEyY1drNGIwSmlhelZXWjJkQ1ZUZzRPRTAzTTJsWGNEUjRVRzFqVFhOcmQxUk1PV052VUd4amFrRjViU3RKUVhjNVlUVkxLM1ZLYW5aNGMyaHBMMmszYUdWRFoxaEhiMDVJT1hVM1RWWnpaako2THpCcVlqRmpSV3RqVW1kQ1VVWmhVV1pCUW1GNmNrSTViRUp3UlVkb1N6TTNVVzgwVUhwT1RVTjJkVE5NUkZKaE4wcHRRVk5CTjBzM1puRkViVTF1U0RCa2NWSlFTWGx5ZG1kSVMxTXpPRlZ5ZG1oR09UQjZhVTgwYVRGU1VrTlhVREk1U0VKc1dYZHRVSGxJU2tRd1JHODBPVVZHYzJ4dFZYTTFTMEk1Tm1od1NreFVZVm96WW04NFMxbG9jbFZVZGpaUlFuZDNaVVpvT1dRNVJHNURUMll6VW1kemNIQk1abVV3YW5Wa1MzRlhNR1JSVmtjMk9VVlhOSEpOVW1SME56aEdlR3BPTUhkWU5FZzJUMjVhVldGT1UxQnRkMnRXTUZwd2J6RTRjRFpxVkRSVGFFNXVVMEZqUVZsSEx6TnpPV3AwWlhVMGNXNXhWSEpYV2xJemVVTlBVVTlXVFcxRk56ZzNiM05tZWxwSWMxSTBkbmxYVVd4UVJXUjVhblI2TmsweVZ5dHZjRXRHV1hsek0xTk1OelUwYVcxTVpXOHpUMHRhUlV3MmJYQjBRbmxSV1ZGeFFWQmliVXd5WTFkc1pEQnRjMXBDUlhkdWJYUTNlVm8wZDNadEszbG5Lek5SUlVJeU0xQjBkMUZHTW1SWlUwa3ZNVzk2VW5KQ2IyaHBURE54Wnl0RVQybEpOazByTldFeVR6VkpZbXR6SzJSVFFtNUVhV3h3YW1WU04wbFlSRkp5VjA1dU4wbG9kWFp1VWpWVGNIbEVLMjAwT1hwQ1NGaGtXV05vVmxKbVNubDVialozWWxjeFlUZFdUU3RuTlhoemRVVXdaMVJyV1ROUFVYaHRZMVJXWkRGMk1YQnBTVGxEWVRnNGVGY3dkVEpRZWtoalZXcHVOekIzVWxWMVRrUTVTbXB0ZUNzNFQzTmhVMGxNWldzeU0zVmFTVlo1U1dGcFVqWndTWFpXVWpVMWVFMDBkMmROWlhwaFpXTnJkakZKYzFWSGNFSjJUVkJrUVhFeGVITnNWelZJTmpGRWEySkhPVWhXY1VvMFYyNUtiMk0zU1c5cmVIQmpNblZHYTFkcmRsbHZUVEJ1TDNJNU1EWk9NbTVuVlVWTWIzbEpUVms1YmxGaGRXMVJhbFZIU1ZFMlVuaE9VbEprVTNoNlJ6QkZMMk0xZURrM00xcG9jRWN5U3lzMldpOTVZVEZ4VVVOSGJVMU5TM2hUZDB3M1NVc3JURkJIV2xFNVQwcFhVbEp1YjFwc1ZETlZibWN4WVdWcFRuRlpkRmwzVHpBeGJsUXZkazQwTWpneE1UZFRXa3hCTDBzeFdIaEhVVmgyY0c1TWRYVlRhemhyTHpKMk9VNTNZMlZzTjBNeVFuZGlaRVo1YUc5M0wzWmxkMjlST1hSbWJXSnVkVVpLVGxFeU1FNUJNV2hLV0hVM1EwTjRLMU5ZVURSYVpIQkthM1JpVGxWbVVFdFhlV1p4TUdkM1RUVlVaV2hhVG1oVmEyeHlXRU5NYkhWSlNXWlpjbEpEZUU5S1RrbHZWaTh4YmtJMGNUZHdVVk5sZGt0cVJVdGtSVFZxTVdOWFFsQjVWbWcyTUhad2JuZHJOMWw0VFZKeWVWUk9UR015YXpsUWRFcGtlbTh2YjJwUU5sWlVkbFJpYWxFeWNuUXhkbGszZVRoek1DczNXbkYxTTBRd0szcFVORU42Umk5Q1RISkpObGhHUzJ3MWVqZDZRbFJyVkM5T1VWWkllREJNVFM4Mk5GVklMMEZpUm1OM1VVbFBhMWxET1ZnMWRGZHRPWEpWY0hkV1RtVnFLMlJPZFdsS05rVjRSMEpUWmtkdUx5OXRaamRMWVhkcWNHNXBiQzlETjJzelVVcEpja3MxVEVjclMxaHFhWFZSZVd0VVptZE1NbFJQVGk4emFrOW9LMjlsZWxWdFdFZFRVMVJwVm5rMGVIVnJNM0E1VTBNNFZFTk9VSEJPYlhjMVprUTBTRUUyWVZaNVFXTjJjbU5XY1VwTU5UbGtla2x0VDFWaFJTODBOMUpFZGl0bU5EUkdTRXBpZFdFNGFpOTRja2d3TUZWUFdFSm1VVnByTld3eE5sSkVOVUZLYzFWcE0wSmxUVkJyZEhwU2F6WlpiVlpOYmlzMmNWTTFXWE5DUlVnMVMyRmtkV0pSUXpkTGNXVnBUQzlqT1VoaWEzRnpNRGcySzFCQ1JUQXZSMHBXZFd4WEwySndUMGNyWTNFeVdsTkNaMWxRUVZSdVpWSnJNSGMyZEZsbFFXbFdNVGd6SzFsM1RUQnhkMGROWkRaUGRWVjBhMDAyZG1JclpHMTZibXAwWTBGa2VIUnhWVkJTVEZkbGJIVmFkREpYZWlzMGJuRXZjekUwUVZCNk5qWm1jR0Y2UzFrNFNUWndZelpUTURsQkx6RnNZVTlPVXpJNVJsUjFZVmxZUjBSQk5FeFZOMlJvYlV0S1dqaDZNRXB0UmpnMFJXUk9SblJ5ZVcwNFREY3pPRmROVUZSRVNYcE9aMFJtTkhKTmFtWkpVbUV3VUVsUVRUSk1kalZuV1hORlQyUlVUSFV2YkdoWE4xZE5hbWhoYVVneWNFdFZXbFkxV0VKT1ZESkdObXh3TVRJNWNFRlpUMGt5VTFCdFZuZFJjbm93TVZCNVRYVlVibEJwY1ZFMlFVUkJiRkJ2YW5FelYxVlFWbHBCVlhGU1UyeEdTR1JFYW0xMlozRmtUVnBsUmxsT2NtdEpNMHMwVGtsbksyeHRTbG93YjB0bFUxZGlkMVpGTWpoeFZtaDRTMnhCYTJOd1dtVlpaR1k0T1ZwaWFWaHFlSEpGUTNWdWNtOU9ZamhwVWs5U1VIb3lRemcwZFVsNFpIQTVkVFZSTDNBd1pFeFVjamRVY2xCYVRFOVNUMjlYYlRJNE5rUlZUV015YkVWWVpIRm9jeTlVYW5kV1lpOU1hbkZpWTFCUmNXZFpTR1Z4TlZOblJtWTFPRzFWVGxCU1JUbEZaMDVYTTJkQ1REVkpMekZqVkRsSWIyWkZXV2hLTDJjell6TjJlbFZ2UldOM1N6UnFNV1p6Umt0UlFuaHZORk5UUkRWWkszSTFWVWRDVjFoU1RDc3dXU3QxYVhCd1NtZHdjRk00YmtKVlRXZFNjbnBhVHpWU1NuTnRUemx5Ynk5cU56bGtaazVKTTFGTVYzWnhOMU5DZEhCd01VbHpUMjFJWkZKMWFYRnNTV0ZNVFRWV2MzTnZZVEp1WlVwdmFFeFNPR3hLTm1wdVVteE9ZWG94VWsxQ0wxVmhSR05RVmtFMFMwVTJTa0l6ZVdwWlYzWlNhSFExUWpWbVYyYzRja1l4VmxoVVEybGxkMXBJZDBwMlQzQXJTa2QyYmxodWN6TmpiMXBYU3l0TU0wRnNZMFZWUmtGTk4wWnRjemxQVXpKelpIbElLM05FY2xJNGNuUmxUVGgxWkVSeFJIQkxXbk0zZFVOelozQkVUVlF3TWtOUmJucEZUbXBPUkdKVWNtbFJLMHh0VVc1WWFsQjJWVFI0VUVwR1UxaHJjVzVyZDNCclRsWlZlako1TVRkaWFHRTBOWEV5YlRRdlF6VklNbWxMUVdWTmRUSjZaMnN3YVRGNFkzZDVNRmsyTkRGamNsSTVMelV2TlhSSGMxUnRjU3QyUVZndk5XeFRhWFl3UldOeFZISlZWWFV5WTFkMlJYbElORzlVWW1zelVYUkpNRWhLYlVOR0wyOUZRamxHTVhCYU1uazBjeTh4YkdOYU4wcDNWRXRJUm5CcE1UbDJSeXN6WnpaNlZrdENVWE0zZFUwdlNUaGFUWE5hVkdKYWNrSlRPRUo1ZGpFeGN6QjBNbHBVZFhKalduZHFUM2RyUkZGV1pWbHFZM0EwTnprMFFYVlphMUpqTkdjNE5uVkdkRzltUWxoa1dtVlhUVE52UjJGaVNFMVFWbEZCTW5kNGVERkVaRE5UU1d4bE56aG9NRVZRVFc5UVdub3JSRUpUY1ZkRVJqRkNURkZFYzNWa2VucE1aVTlVVGxOV1p6QXJWMXB1VjAweVFsWnZRM05oYUdwWVNEZHZkakUyWlN0VE1EaFlUemxxU1N0R1NuSTNhVU4wWm1nd2RYVlRZMjV4Y0ZkcU5UWnVSblExYWxWbmMxRnpabTV6ZUZKUVVTOXdPVUYwWVc1a2IwczBLMjl2ZW14d2JWUkNUMGh1TjBVMFlTdGxTbFowYzJORGFsSndUMU15UzJveVRFNU5NMmt3U0dFeGFuQjVSemhWT1hSUlNFbHhlbU5qUVhJeVR6Qk9WbkJ6TkRKUlNEZFFNM1ZxYm1KRWFsaFpUamhRZFRkalV6aFFZMmxZVTNSemJrTlNjbWxvUlhwQlVtMDBMMUY2UWxSQ1NXbzRiVTF3TWxwYWF5dEdTVU5JVTJwVE9FMXdOUzlRVUV4NVFXczBTekJGUkcxbWVVbEtZV0ZZYkN0VlVuQllXa1ZuTVhRMGFFOUZSR1V2UVZKaE9VOVhTV2RaVmxORWNFMUZRVTlZUWpoQlNIUlZPRk42YkVsdFFuWmliV2hLZWxaeGQxaDNOVkJCYkRoSWFqbHpRbVEwTVRjNGVucGtjRUZxYzI1SGNHSTNjMjV6TTJzMkt6TmFVMWRoWmtsTk9GRk9XRE1yV1ZGTU1WQkJXVGxMZGxwQllXZFVWVXhGWkdFM2RrZG5PRTAwVFVWNVMyOXRkWFZTVnpKUFNXZ3pOakZHTTBWNU5VSm1XbEZyV2tsaFlXOVRRVkZIVUdSWU0zUnhSRmxFVTAxM1NuYzFTelZrTmsxaFlXRkNkbFU0WjNGNVZIbzNTRlJpZDI5dFVVMVpZa0l3YUZVMEszWXJkbTU2ZUZWdVFVRXZWRkY1ZG5NME1YRlBSRVJLWmpoRFoyVnhPVk13UkdGUE5FSjRUMWgxV25neE9Fc3JiamRvTjNWU1pXdEJRMlV6VjJzNE9HeDVNR3hrTVhWMFNDdE1jSEI0ZVVwWVJqVkZaVEoxVTFoVFIwbEdiVmxPVmpNMmQwZ3ZZVXB0UjNkbmFqTk9OSEZ6YmtzMFRGbHJaSGxJWTBOaFYwdzFSVTFPY2xWV2F5ODFablZMWVcxeWFtUlhPRUV4ZEVRdlRIUkpkVGxuUVRZeFdVZHBSRmM1VjNNeFVHUmFWV3RoUkVsSU9HbE1XRzlqTmxaSGFqSm5jRlZEUTJaa2FEVjBXak5GZUV4a1NHdDFRM0l2VmtKd2FHMXhMMVp0VGsxV2NHdERiRVZhWkM5cVdVUkxRMlY1TVVaNWNVcHhSVFF6WkN0UVoySnpaVXhDT1VOYU1UUTJaRTl1VjBsbE9VbDJNRlpNY1ZCWFdXWnljRFkyYXpSWFpGYzFOVGwzU1VKVU1tNHJiVWd6UTNOSmFITkxObGc1VmxKWlYwdHBiV3BpU0hndmNGbHdZMGMyY0U5M1NtSjBWV3BGVFVoaGVVNWxOemxMYzNObFpHTlRWblZ5TTB3M1VWTmxUSEIyYjJkaGFVb3dUMHNyVmxsTGFsaExXWEkzV25VeWJWaHZWQzlwWW5ST1IzVlRlV2xITm5GdE4xQkxhR3hUZUcxWVVWTkRTWFZHYjJoU1duRnhUbkYyZEROcFFtdFlhVlIyUjJkSGFXUXhSVFJtYm5NeWRXOVpaazlFUzIxNFZ6RkNWMkV5VUdkRVZtOTFUbEp5VVVWcFUwc3pkRFZ1VlZaa1lVZDFVMUpXV1V4MGFYbFJSRU5HUVd3MWRDOW1NeTlLTm0xTFkybHlWRlUwYkdrMU1WVk5jR3M0YlZSRFREVmpjRVF4T0VGWVF6UkRSazgxTTAxU0wzcHNhRFphUkRsT09WQnZabXRZTkZaYVluaGtZbkJKWWxBNGEyNDRVM1pGWW5oRWRYQkVhR3g0TjJadVYwZFlSMFF6YjFKaUt5OVZVbVE1ZVU0d2NVRjFTVUZSVFc5V2JtVkxOMFZCZDFwVU5FRnpZelZEUkdkQmNuVm1WREJYUTBNMVpDczJMMGc1TlRCWVFscFFhbVZKYldSRGEwdFBVSGc0U25Cb2NFTXlLMjlVVWpWTmJ6TldRMll3ZFdSdE9YbHVWbkZoTDIwM1VEQktTblZYSzFKcGFuaGphRU5KZFZkRVZuTkdNRWRaTnpsaFozSjRRVkJNYTNwWldpdFZZbGxhYzJaSU1HMDFWbEI2YjBkS09VeEdaVVF3ZUVrMU5GSTBkVTE2TjJFcmFFdFhOR1JtYkRST2EzRldZemt5T0dOUmRTOVJSR2RzTlRCdllWVkRkMVI0VjFkcFFXdEtlVlUzYW1KUlVsbDBVSE4xVDFscVprOUljaXRIVXpkeWVFTmxVbnBtYURsd2NGUjVUVGhuZW1aaFQybDNjV0ZUUmt4dldtUjFhV0ZoTDFWeEsyOWlUMFZEUVdveVVGSnNRak5DYlN0SlZXUm1XREZVYkd3d1NURjBRVmxWU1VoTE5qbFBjMjl0TXk5QmIxZHFUVk5oVnpWc04yVXZhSEpVZG1abk9GWm5aM1JGYm0xeE4yUnJOemM0YUVKQ1MwdFphQzk2Tm1WSlJWSjJMMDVLT0dRMVdWSk9iV2swWkVjM1F6ZGpja0ZsVDFoQldWRkhZMU5DTURJMUt6bHZlVzA0VTBGcE9DOTJUVEJXWms0NVVubHVNREZpU0dWVWRHOXBTVmhLY0RSNU1YQjBTRlZLZWxJMFFUaEhlVmcyZGtSeFJrTXdWRmx0YUZsaUsyUnpSVVZ0VG1KaE1YVTBiMHh4SzJGamVFZHVkVkUwVVdSSll6WlVVa05PY21kUlEydG1XSFJNYkhkWFN6ZFRaVTFHZERSRk1XWjZUMkZWY0VKeFoydFdaMFV4UlZob1FXdFFkRzFLTVUxV1QwUlFTR0pKSzJoc05XRmpiekJ5SzBwbk9YUlJTbWw0U2pselZGbGFaMFl6Um1OUE5ub3plbmR1Vm1kWVJWVlRMMjA0YTJKTVpreDBZemROWTJWa1VVSkdObU5oZFZOSVNqQTRjV05MT0V0aVZIQTBZa2gzWTJFNVJXRnpOWGxQT1hSaFkyRlhTazlQYVZrd1ZEUkRNalZ2WTNoNVREaHBaekp4ZEd3eFZuUkNNazlvV210S2FERlNOVVZUYzFnMlVUWlNaRXh3UWsxSGFGQnhNWFJoVVZSM1ZqZG1OMDFvWlRaV1ZYcGpXR05MVTFjNU9VZHVRelZ6ZDJscEwybGthSGw0SzFwUVF6QkJlbFY2VDBaWlJFVTVWblZWY2t0cVZqZGFSR1pPUjBOMU1YRmtUM2xvVERkVmFIWjZSMlpvTnpsQ05rbGxUbEYwVmt0aWJEVkRkVWN5UmtKb05YcHBjSGx3Y1RGdFZ6a3lkamhKWlU1SFRHbGhNVzVRVDFCeE0ycElaamg1YW1wU01FVkVZMk5LUTNJdmRVOW1ibmxVZDNsRUsxYzVURzB6U1ZsU1J6TlNRbVZ6UzFoVFFVdFNXSGx0Wld4WFVEbFFSelJYUlZSMWRXdGhVbFZoYWpac01WTnZaek5rT0dOaFVGcGlWa3hKWWxWVVkxWmtNVkpqZEVwUFZTOXJkRTFTZEdKUlZFMXBXakZuZGt0WmQzbzNNa3hZTWpZeE5VeEdUSEpWT0dNM2FGY3dkMVZEVTJ4blpuRklVMFpDUzJReVptZHFXblJ1VUVKVlRrNWxkM0pZZEhnMFZFcGxORnBPUzA1MldGRnRZVVZFUWxkTVFXVlpjVUYzU2xreVJURXpNRkpzVlRCb2RFWTFkVGRCUVdVMk5rcEpjVTEzVVRKUVUyRjRXVGxtUVROaWJsZFpPWFpQVW1kYVpDdDVVamcxUW5WVVltWkJURGwzVVVSdVkzbFNjR1pQUjNWVU4wWkNUWEJFTWt0c0wwcDNTV3BDUW5oc2FUVTFZMUZITlVVM01qUTViR3B4VEdGeFR6SjNjWEJDVTBsTU0xbDJiRmhQZEdKYWMwRmxOR2xCYW5oVVIwZDBZa05oVEdSNGVFYzVaMDFPWTFkc1FYTTBSbFI1T1dFemJEQnpXQ3RZVFRoU2NIZ3laVlF2YTFSS1oySmpORWR5ZGl0SFJ6bFdVRWRsYlRZMVJVZE5TMU00VlRWUlFXVlROa2R6UjNKeFluWkhPRlk1UkRVeU5HOWpNRzF6Wm5oWlNuaGtXa1ZvWkVGeGEySnJUbTFsTVRSeE9EQnBVbGxhUmtGME1FUTNZV3d6ZUdJeFptZG9NVzl5YXpCSmRrcEtWR0U0VldWd09IaFBNbEU1YzJsSlNrSk9ORmhPTW01YWJGWTFkVVJLYms5aFJUUlpORTUxYkRCVlExUjBOVmhZWmtVck9GSlJXWEJYYUZvd2VsbHliVWg0V2xwc1VISkhWbVZNVldKbVoydFNSREZ5WTFWRFQxWjJSa3A2UkdGS1MxcHBjQzlsWVVORVZsaFJSV0pJTlhnME5VeFlhbnBHVGxGTFpXWnRWMDQ0TjNCNVdsRlRkVU0xUWl0bFJsTmhlWGhHTkVScFNVUnlSMWMwZHpGbGVUVTJlRmhtTDFobWNFcHVVMDVRWVVWV2IweHJkbGh3YUZCRVUzQnpVbVpzUlRkMlluRjViVzQ0VTBwMlZqRklPRWR0U2pFelkzQm9iVU5YWTAxTVoySjRRM3BGYmpGQlJ6TTFTbUZJTUVwRVVURnllVUZDYm14eGRUaFVZekYyV1dSU01XdEZkVlpLV0VwbVJFMW9Ra1pGYkVaVWRHSXlPRTR6VldGaFJtZFZZVE5RU1dkRk16WmxaU3MzVFVremRrOXBaMlp6UzIxTmExTldiek14ZDFBMFRsVlJZV2tyUm0xQmQyWjFjVmhGUTB4dVdtTlRkM0JCTVc1bk1YWnlaVEJLYmtWSmNsbFhUV0ZYVGs5bE5IQmtUWGQ1TmxCU2NEZDRWR3R2Y1hkME5VeElkMmcyUnpBMk5GcFhhMlowYVd3dlVGSlVja0YzVVd4TGVVRmlZek5yU0dKUGNHb3ZZakZKWWprNFdVVkdRMXBXUWtScWMwWlVkVFkxTUd4b1VIaGtkWFJST0RWaFVESmpSVXMzZEhCbWFXZHRlQzk1TlhkR09HMWhLMEUyTDFoTFQweEpVbUZEVnprek4yUnRUMHh4VVc4eGFHWjJkSEJoV2twMVRuSjZVM3B3WkVsWWJuUjVVVWc1U1dkME1XTlhhbkZWTWswd2VGSTRMemRTYldoTU5YQm5hbXRxVlVwSk5FSktVVlkzSzNCRlJrcHNVR2xDSzBrNE9XaE9ORlk0Tm1STGEwdERkREZRU1UxRk5rdHlTREJRYUVobVZUZFFLM1UzZUdwWFFWbEVXbkYyVlhwdVJrUjNiM1JqUlhKd0wwTnNOVWxYYzFoNVdreEpWV0ZGUlZOa2JrMDRaRGhQT0Znd0wyaDRjMFZCUlVaSllqbHBjazUwYldjME5XMU9LMDlRZEdOU1FYbHNibGxIV1V4SWRIZGxSWFJvUmxoRlZFTjJObWx6VnpkeFYxSlZaazh5Tml0NFVUVjFUbkk1U25Kbk5GZGtWVEl2TVZKNE5uSlZhSGQzTXk5SVRrTjZZVzFITlhsRWRFRnRORVJ3TnpGWFV6WlNkSGhTUTFCa2RtRmFOeXRUVG10UVYyWmxlbGgxZWtvdlprUnlia1JzY1RVNVZTOTZhSGN4Y3paaFdITnZSVVZOUms1c2FDOVJWVnBRVmxsSk1IaDBjbkF3TUN0YVYybE9PRFUxYWxoa1VGaDNLeXRoVkVsQmFGTndSekpxWnpCWFlVSlFaV2hJWnl0YWIybG1PRFpuZFVOWk1VdExVMGx4U2pFNU4yUTVTbTlPUXpWM1QxTndTVFJaTVUwemVXeHdTMUZYUTJkaWFYbHVTRWg1TmtZeWMwWlFPVkEwVGs5TVpsVklSa2xSUm5KdU1HWmtUVXA0Tm5nMVlWZFJNMjkwV25CQkwwRmpkR3g2VW1Od1ZVZFhXRlZZYkhGWFpGRnZhVVJZUkdwdVZIQmpOVWhGUTJOR1FXMUpaVlJGTlhGTGNtZHJSVzgyVGpsSVdVWklOMEV5TTBOS1YxWXljME5hZGxsRVkwWXlibE5LVjNWUmNuRjRXbHBoU2podFZ6UmFVQzh5V2tWaFdESktlSFpKUWxoSlNqbDJlVWx3U0hSdE1VNVpOVGRYY0ZCblp6ZExiblZDWlhOM05HZG5Va3BPYmtsVk5UZFVVbFZEYlUwMFNHaHJkMFZYZHpjeGVraExObk5OUVdsclNXODRObTVrU2xoTFJrRTFNVkpWVEVNMFFUbERXamgzS3pNNVdFTkRUVGMzZEdGaFVsRlFTWEF3YlZWNFN6TTBORXB5VkZWM1RsaEhVM2d4ZGtaVVZGUlZUMnBCVFc1SlJVRjBhWGwzTjBacFZXWjRVSEZZUkVWM1ZXeFhXa0pIYzJsbE9DdFZXWEIyVEhWT0x6VlZlbmhrVUZwdGRHbzBhakpqUldRMFYzcHJXVmR0TWxOblFrWjRNblV2UTNCU1NEZDBhMVE0TDI5dVptMDVSbUpaV2xwcU4xUk5ka3gwZEhwYVlraFdhMmRrYjFsV1VsZGhjMmhOWW5sb1owZE5OVmRwYmxsVWFXeGthalkyVW5OblRWb3JaelkwZEZCSFRVNVJVMEo2VG1rdlVEWm5OVkZMZVRjMFZVNHdjbXR0WkhRdlpUQnVlRlI1ZW1odVVUSm1RMWxvY0dOaE9YZHNaM2xSVTJ0cWJuSmxSME5JUlRJNE5rTm9NVzRyU214SFdHUXpWblV5YjFaMFVHbGpkVTVSWnpWS2QxbEVZMDgxU2pCUGJEVjZabTUwYTBGS2RsQlhURVJFT0M5Qk5GWXJjR3hEUzI1c2FYTXZlWFl4YkVKQk5XTndiblJGYmpCRk4zWnRTVUp4YldOS1RuZHBhblZPWm5wTVJUa3ZkMXBaTjJ0U2JtZExVamx4Y0dKb1pGRjVVSEJvVUZRd1YyeE1XazVFZEN0R1lsbExZVWhSYUZOaFVYTjNUWEZVY0RSbE5GWk9TVVkwU1ZJdlEwTlhkRUZQTjNSSWFVaEdWRlJFWVRsQ05tbEJiekUyTUhSdFRpdFJWMmxPVG10blVTdDNiMGwyUlhCVlZHRlJkalJtVlRRclRIZGtibVpFYVhjemF5ODJjblpUZVVSdFJURmxaalJaVjNCd2RVOUdWM3BPWlUxclZISXJTMUJGUW5kamFUQkVNM1JPS3l0NVZXaHpSMDFVUVV4c1dHcG9hR1YxVHpRdmFYRmtabkl5UVVWSllrMUtTVXg1Um5aWlkyWldRbmM1T1hKRk5pdENVMFZYUVdKU1VETldSWEZYTDBKM1QyRnFSblpvWjBKMVdrcElkbmgyZGpZMFZrb3ljRFJrWVdsUFpYQjNWemxLZEZSM2JVRjVMMWxtZDFGblFWQmtkbUZWZEZoWWVsazFXSGxzY0hoR1prMUNTRFZUWW1Gb2FXNU9NbXg1T0ZscVIyOUVhMlkwUmpSTVV5dFZWRXRVVVhBeGNuZzFOQzlTWVZGNE1FcE5SRTltVjB4SlZrcHBRbkJsY1Roc2MzQXlPRkpyVFc4d1RIQTVjVlJ4V2pGc1pYTlJVRVJEYjI4NGJVd3JObU5sTkhkNlFWQkVWemRtTmtsemFuSXZOamxDTkN0R1pubDZZMmhHVkZoRGEySnpUQzkzVkdveGVrUTBiSGg1VGxwcVFWaERRVFJsWVdwMlkxWnpPRFZ0ZFc1aGMyeFdWVE5OWWxOU1lUbENjM1JqZVRGSGNUaGhkRm92TTNsS2NtRnZaRkJGVDNaSVN6TXhUa3hvYVVoSFl6YzJZamxCV0RaeU5HbEJWV3hyTDNaUlp6SkhTRWRuWVVONlJURlZNV2w1VXk4ek4xbFljVmRDWW5BMFNHSmhlSFJPVW1RNFMyOTBRVGxqVjFWMmJrZzNNblpvWWpsUmQzTnpZbk5qYVVwT1VXcEtSblpqTUN0d1JtbE9aMUkwVjFKU1FqUkVZbmxxTTBsbFFtRXphSFZxZG5OMmExRTNSRXBLU2pSVFJYUkxRM3B2TUVaemNEZFJiV1JNZDIxTVQyUlZSRWhXVEU1b2IxcDFhR3BIY0VwYVQwNUJOSFpwT1hWbFkwa3paMEYwVm5VMVpFRmtRMWRoY2xoeFNWTldSalZCTkcxbk9IcFFOa2RVZDJsdWMycEpjVlp6YkVWck1YQjFPVmhwYW1OeVEyeHNiRkZvTmtKMVREbFFlVVJ1YlVsTFppdHVXSEpEUlhOalVHUTBjMUJEUWtSQk5HTkxNRmRCYVZsc1VYWjJiME5PVW1sU0szazFNRU40WkhWYVYzRnBVVmxQYlZVdlEzQm5ObHA1U2pOdWFHODBLemxvU0RaNGEwVXlOalJVWlhSbk1IRndMMDFpZHpCb1ZWZEhNR2hSWmxSUmRIcGFNM0l3U1hCU2FIQnpNRUZCYVc4MGIyUkJkV0ZRVDFwWFNuWlhlV1JKTlhCV1RUQlhTMkZZUmxrMWR6bFFObkY1VUcxV2VtWjZTV0prYlZKa01FdFhSazl4VmxFck1GRlNObWxuUmxrMllVZHFWM0UyY0dwaVoyUTVVM3BWYVdWV2JsUjRXazVyUm1oRlRETndORU5IZUU1eE1tZFNaVWhLZUVOcmNWaEJXVmwzUTFVellrTkVTRGRtYUZoblRGRTFVekV5VjJ4M1dtc3dPRU5KSzNwVlJWRnRPVEUyZDFCTmQxcGliRGRDYXpZclUyNDBXVGw1TlROdE0wWlpkVE5UU0VOWVVVbDZiVmh4SzFwMlExQldObkJrZUVOM01sTTBPVzVGV0c5bFFYTjFPRFZSTDJSMFkxTlhNVTlwV1U1MlRrVmFjMEprTms5VVJrVjZSbUpaTVhKaGFrVldaVGRvVEc5ek5UaHpjMGQyZVZwb1pIaHFjeTl0TkZKamVsTnFXVEp3VFZkc2QyMXBabTlUY1hWb1JuYzFkVlV4TVNzclVHNTZXV3RDWW14c1R6UmlOSHBZTld3M1ZIRkRUVkZNYlVwcU5YSTNjM2d3VEdWTE5qUm1ia1ZJSzFodE5IUnVPRlFyT1d0RVppOXJTMm8xWlhsek9ITmxhVEJ1Y2xWd2NHVTRNMWc0VkdSUlBTSXNJbTFoWXlJNkltSmlOVFl4WVRObE1qQmtZelEzTW1ZME1EWTFaVFJpT0RJMU9EUmpZbVV6Tm1GbFltTm1aRGhtTkRrell6ZzJPVFF4T1dJeFpqbGpOVEkxWWpFMFpEVWlMQ0owWVdjaU9pSWlmUT09', 1788885065),
('mVmCFRSn4KhmIOFCEL0u8Ia6uEmgwVrEb1jHSKc5', NULL, '18.153.79.176', 'Go-http-client/2.0', 'ZXlKcGRpSTZJbkZQYWtoQlRGUnhNRE5KVGxwbFJESXhOa05YTW5jOVBTSXNJblpoYkhWbElqb2lVM1phWWpBdlRXMXBRa0ZKZURnMWVIVlhZMVZCVTAxd0sxTnRMMXBWZFZSQk5pOXpOa2d5ZERsNVRYTkpWRkpWVGxvMFZuRm9TbWw2VEZFeVVEVnJRVGRFWVZaNlMyOW5RbGRhU1ZZMGMzbHNiMnhKUVZCaE5Hb3lXbkJ0U3pkdGMwVm1lVnBMVW5jeFFURlhkMU40VkRRNFNETXlkVWR0Ymt4SFJtaEJObTE1YkhCRmVHUTVlbXRzTVdrdmJDOTRlREl6ZGxndk5qbFZlbTVDY20wMFZqZHNOaXMzTDNBMGFXZFVNVU5tYzNsWWIzUXJZakpzUjFKRVFsZG5kMUkzYXpKNWNFOUpTMkp3TVROVU1rVkVSakEwVDBvdmFIY3ZlR2x0V2taNFZtSm1kbTF5V21aalQwVTRXRU5JWm5kMlNsZENlakFyV2tGNk9ESlNkWEJ4VTAwemIxZERNVGxNT0c5bU5HMXJTMnN2YzBFMVNuZFNRVTB6ZDBkMFVEUmxOV3RySzFRd2RGZFFXbU5vVkhsVWRXWmhXa0kyV1hsRWJqWTFaWEV3V2praUxDSnRZV01pT2lJd1kyVTVNakJtTXpWak5UWmhPVGRsWm1NNU56VXhaVGRtWmpCak0ySmxNR1EzWkRnME0yRmlaV1F6TWprNE1HVmxOakkxTldRMllUWXpabVJoWkRFeElpd2lkR0ZuSWpvaUluMD0=', 1788890920),
('NxOrKDw1DqoEkTA9j6JZygaQJWLcJL3tZ7qTxx6r', NULL, '100.26.225.192', 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/527  (KHTML, like Gecko, Safari/419.3) Arora/0.6 (Change: )', 'ZXlKcGRpSTZJa2hZUmpkYU5FcFRaR3RZZG1wbFMyRjJiV3RhTmtFOVBTSXNJblpoYkhWbElqb2lSREp3Y1Zkc1IxWjVjbXhWZWxOQlNFaDJPV012WlhkREszVnlWSEJ0WXpKcE0wbFBTVmRzZGxVdmNtZzRkbTFxYlVOTEwwOXdUSE5hTVhwUFZrVnBZVGhsUlhVMVFUZENZVzVwWWxCQ00wOW1WVEExY1RGclJHTlhSM05GYkhWdGVIUkRNbEZ1UjFCQlVETjVUbkJuTUM5S1puTjFOR3BrTVZGSWFEbHJNSGxQY2xreWJGbDJaeXRvYzFSV2MzRkxhVVZPWjFSNVNscE1lWFZyUlM5c1dFcG5ZVEZLSzFvMmRrbE9jSE5EY0M5MlJqbFhNR1JUUlRoemFGaG5Nemw0VkZkSVpFTlRhbkJFTkcxTE5XZHRhVWxWTkZvd05VSk9OVmM0WW5FMmFUbHpXR2hFY2pJMU1UUnJUSFl4VFZSWFpXMUNRbTVDUVVnNVMyVkJkbm92YkdRd1VpdG5SRXAwUm5OME0wMTFhbXBQU1hkSVduTnpTRGQxTW1WaVNUZzVRV1JWTkV0aGR6Qmhaa1k0YUhCaFEyUXJiR1I1VjJGUlEwZzRSR3h3Wm5kSlZsY3haMGx4YjAwMGFtTnFUVWRWVDJsTFNXVlJQVDBpTENKdFlXTWlPaUkzTTJOaU5USTNOR016TVdFd09UWmxaakF5T1RVMll6RmtPR1l4TXpNNU9UZGhZalppTm1VM01qQTJOekV5Tnprell6ZGhZekZsT1dJMFl6SmpNV0UzSWl3aWRHRm5Jam9pSW4wPQ==', 1788875738),
('o4R2xmj0Gbw5ZdaZ8lFn0ddl7JTKdI9DbfyrSmTF', NULL, '167.250.206.228', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJblZMTnpsS1NFNXlla1V6UjFSR05HcEliRlZCV1VFOVBTSXNJblpoYkhWbElqb2lhMGh1ZVZkVmNVdFFZM0ZETUdNM1prUmpiMkZsUm01MFExRTRPR0p6V0ZKTlYzWktLMUpPWTBrMWFIbGxTazAwTUZsRGJuUkZNWE5DUnpaR2J5dE9ZVnBMY1haeVkySkdWV3haVm1aMFNGbERkWHAwYXpad05rUkdaVFozTkhkU1lqTk5TWGRhVEdONk9EVXZSWEZtTmpkRWVYRkVNRGt6WlZwRVNESlBSMnhFYlVOdlVrbG1Na3hqVW14Rk5IVjZVMjV3VldzeE9UZFdWalZvS3paMlpFaEhaa3g1ZW5nME4ySktNMWhEVHpFelkxRXpUMDgxTVZScFdtbGFkWGM1WjFrM0szQndaRWsyYUVkV0wxaG9jalF2ZWpGYVQwZExWa1ZJWlU5aFVrVmtSVlYwY0ZodlEwNDROM2R1UzNwS2FIazJSVVJ6YURKTVRuTXJMM2xEY3pKR2NsbHBOMGxoVFZWeE1YVmtNVTFtTUVkbmIwNW5lVXcxYkVOWk4zbERlVE16YzJwM1oydElSakJyYTBoNFRtMTFUelJCUmpSblpFVkNTVGxIY25FaUxDSnRZV01pT2lJMk9EVXdPV1U0WVRJeVpHWmlZakJqTVRFd05qTXdOakZsTmpkak9UazFNelE0T1dGbU0yRmhaRFV5WldGaE0yTTROakV5TjJVME5qUTRPR0ZqWmpreklpd2lkR0ZuSWpvaUluMD0=', 1788887556);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('oSZRWIbX6tfWYP3etBlN0JzeOZvYV97vu28xBDmM', 107, '132.251.2.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJamRvU0RJeVV6TnBUM1ZhVERoSlkyRklRbFJ6YmtFOVBTSXNJblpoYkhWbElqb2lZVTFtYzJkcGQyRklZWG8xYVRWT1EyZGxLMlpKYURkVGNscElWbTFDVVhGRFFVVlRiWGxYY2xNNWJYVmlSak5rTlVGbGFIRmtSVU5sVTBaclpVNXJabFpXYTNsRWVHWjJiVWhpY1dsV1VrSXZkbmROVldwWlZrNHlRVFl2ZVVWWVlrbFNRWGszUVdGUGNVcEZjSGhNUldsak9EbFBSV3AxVWpod1JpOTVTRE5SYUhWalMxSkVkMkZGU25SUGVsbFJORUpEVjB4T2RGSlhhMFphUVdjMWJEUkdabTF2ZW5GUVJVbENTVEJ6V1c5Nk5qVXZPWGhKVmpRdk1FeEhLMUJxTTBaU2JFZHNXWEZwZGxKblUzTlhhVGRDY0dVM04waEJlblU0YUdzeGQyVXJWRWRMZEVFelNtbzBXR2hDYXpabGFIaHhiVmhSWTFKNlVWZG5jRkZJTkhaS1FWZFdibWM0Y1VGcE1XdFRhV2xaWnpSbFNXNW1SMEZCUm1SbE5IcEpVRXB5V2pSSFRVZGhVSFJQUTFKV1dHTk5NREZ6VVVOUVJsZHVSbG81YzBNM1IzSmxPWFJuVDNWaFdIUnFTR3gzVWpVeU0wVkNhMkpvZEdwQlRqTTRORGREYmtOdlVFWktTVFU0UzBSb01rNXJlRkJHYjBKek1HeE1hbGR1U1drMFdHSTFkMDVCSzA1VGNYRmpWbmNyUzBjMWRHNUtUVTU1UkZOck9FWXZSbXBUVVhodWJVZ3ZZV1V3TDJSUFZYZ3hTVUV3ZWxoak1rSmxMMjFMTDNGRWF6VXdPV1ZPYTIxbU1HcEZXbVl6TUc5U05VVmpWVnB1TDNGelpqZ3JlVlJMZEdKeGJHdFlPU3RxV21Kc1UyUnFibVJ0WWpObVZtdHdiemxMV0dzMVduVkNNbWhYUWtkV2JYQkhPVmxaTjJoS1dXUmFPWGwyVjNoU2NsZHllVEZ1TVRoaVdXUnZTV2R2VjFveEsxZENPRGwzTTIxalZFbHJaVGN4YjJSRkszRlVUbFZOUzBZM056VTFibFJ6UVRJMFFUSlhjRTgwZEZwWU5VOTFaelZDWlZCb1pITXliM05GZFdONVpXdENRbWs0VmxCMFNVUTVkbEV4Wm1KaFJIWnNLM1ZvV2xOWlkxUnhTRE5vYlhWNmEwUlFiR2RFV21SV1l5dEJWemxXVkRGdE9Tc3dXRXhHWjJNeldEazBTR0phZUVkSlJuTm9VUzkwYTI1NGEySmxiRTVRWlRKMkwzUjVOMEZGWVN0WU9XVjRkbVp0VjJSWE5scDNka3R1VFRGT2FrTlVXRWRWZUhkMGQwUkJTSE5UUVc5T1ptZDBZblpVUWl0TlZEbDVSRmhCUmpacVZWSlNjSEEzY21Od2VEVmtXbHBGWlRnd01UbFVTbVYwYjIwMldraG5abVp2TUVOS2NWWTRiV050UWtRMU5IWnZhVWxMVmxKV1FYSnBWMDFqZVV0V1RqVTJabnBwTmxGdmNVdFhXRTFFU1RGWWIxRnlUVWhhYUZoRWFtRkVSRFJoZDFoRlpEZEZTRkZDYUU5c1MwNHJSalprWlVnMVlXbENSVkZFV0dVMVQweDNVMDFKYlZoaGJERldMM1ZZWjJKQ2FsSktNMVF6Y3psQk1XUkxZa3RXTkROUEwyRnNUM0YyZVVoWlVUaHFOMmhsZWpKaU5ETlBTa3RIVDBaWVJEbHlkVmhQVDA5UWRpOVljbUYxUWtKeVMzaDFUbEZEWVhWSlIwUkhMMVJEVUdGcU1GQnFhbkpYZW1Gck5IaEtjWE5wVjFVeFZGRmxlRXBZUW5rMU4zTnJVek5JYTBSdVdGVnlObk5IVEdwSVRVUktUbGhqZUVjMlFWQlhXVWxuUzJ3NWVEWXhNMngyV0VORlZqTlFhakl3V25SblNWTnRNRTlxVUVFeFdWa3ZlRmh5Y25FMFIzbExVamxoTDBKblJtZGlaVFZITlRFMU9VUjNhRkpHVGpjMFpVeG5SRXBhU0RsamIyRmFiazk1UVhwaFZESnlVMWg0UTI1c1RHUlROamxsV0dwSVIxbDJSRzVHWm01Wkx6bFZTakJQUzFreGEyMVRSM1JZV0RZeFltZHBORE5wSzBadVpFTjJSa1JHTTJ4VlRrbERRVTFDVERCd1FsSmpkRUU0YjNWQmEyWklabEJxVlhNMWMwWlRiR2R1YkdaaE1HUnVlbXQxY0dJdlYxVlFVWFV2WlhOSU5Xb3dTbmhNZW5kTk5qUkxOazh2TkVaeE1qQmxNMWhYZDFWaldEaEhPR00zZEZST1ZrNVJRMHR4V2tZdlVqUkxNVmxvY21SVWFuVXJSV0ZvWTNkeGMyaExORzFsUkRKYU9GRkdhMWxaTkhKMGRFOHpiVUZCYkc5a2JsUjVablJvWmtnd1REVkpNVGRpTlhVMGVHczBaMjFGTkc5d2JXNXVha0oySzFodmJEQllUV1JsWVZOUFpESnBka3RXYVZrMmFXOVFhVkpUVEhsUVUzZG9RVk1yWVc5clZXcFdXbEpOVDI4eGNuUXJWazVoVEdGcWJucEJNRlZaTVRVek5FeHZabUV4Y1RaTVVtZHlaMWxTUlRob1dreFdRa3MyVEZKU2MwOWpTbkIwVm5OWFp6bFdOa3cwV2s1UVlUbGxSMEkwVjI5dGVGRlpXRXNyY1RsalUxRlRNMUJKV2pkWlVHOHZSUzl1V25ONmFGaGFiU3N5ZVU5TlRXWldWMjVyY1RacVdUZDVjMFZyWTFWT2VVeHlVWFpRV21kWlFYWkdiazFWZW5jMFZUTXdSWEZaVmpSNVJuVnVVbmhFVkZwNlNtVm5NRTh3Y0djNFkzVXhVak00T1RGQ2JrcElUbXBGYkdSa1NUQnZhazVvTVhWblMwdElOSEphTTJKWEwzaDVaV3hEZVZJeVl6SjNTbWhVZDJOSFdtczFlVGxzSzBneWQyTktZVGcwVlRkVldUUjRVRWxUUXpsd01rZzVVbWRHV0hWemVqRTBZM1ZFY2xkeUszTmhOMDF5WkVadmIzaDJabUUwVFhjM09VRllMM01yWjIxVGMxWkVWRGhXT1dkT2RUVktXRmR6VmxvMFJWQndUVVEyUkdkT1oxWTFaalZWZG1NMmJtZG1VbEpqT0VkVk55OHdjVzFRU1VoUldHRkpSVFpzV0ZCS1VpOUJOakpxV2t0elpYTnZObHBvUTJ3emIxTkVhRWhYU0daWFdDdHFlV2R1VDI5eE4xSjJkMk0zVmxwSlYzZHZURkpTYzFOS1pFZDRWRk4zZUZaMVoxY3pZWFZtVDBRNWJWVndSbEJ6YVVFcllXTnVaMkZqY201YWRFTTBSbXN5ZGtRMU1qQktaRkV6ZG5RMFdUaEJlRU0wWVZJclFYbHVNVzlEYnpocGNsa3hOekpwUWpoYVIxZDNla1JtZUhNclRESkhaMjR4VG1GUFNqWnBMekJHTmxsNlVWbGlaa3d2TmxkbFdGVjNaUzlrVGl0WE9ISnFiRWcxTVUxbE1GaFBWV2RuTUUxcFNrOVFiMVkyY1doek1rUlZPRFZEVVd4T1pTOXJRbUl5YkdaT05XaFBkVVZZTmtWWmFGbHZkelZFWldkTVlWaElWRFpSUkdWWFdWUlNZVTloZGtRMlkyMTRaVFp2U25WeU1pOUlOM2R5VkU1YWMwcE1jVXh2V21Vdk9FTTVkWEJRVVRkdFNrcEJNa04wZFZWNFpXZzFlVGRaT0M4MFNVNUVWMVJVY0ZkVFQzSkVhekprU2s5RWRrMTVSMWhqUm5vNVVXdHlOM0k1Y1V3MFpraEpielV3Unl0WGRXOUlNSEp2TVZaVVNVUm9TR1JFY204MFozSmFOM3BGSzFGeFEyTnhSMXBIUkVaeFNuUnFVVTVwTjFOVVZscGtTSE42TVhodlFscDBkVTVqZG5wR2VqRXlaVTFYV1dGblMydzBORVF2TDNjMGMyTXJVVUlyZDNsUk4xVlBiMWhZZUZoeFZqZEpaRkpIVFdwbVluaERlbFZQTldScVFURnNPV2xCWVU5MFVXdExZamRoUjJwalVYTTBOa05RT1ZWalNHWmFaVEk0Y1dOSFJXcDZRV2xTVEhsMk1FVmpiRlJwZEZKRVkxWnBWMG80V1RNMlMyMXJkRzluUXpaS1VGVlRkMkpCYVZGRVZrOVRUMkpZV0ZWbVdreERaMU5pTkhWYUt6VkRNWGxWTWtsM2NFTlZNQzlKUkhadVVYVk5TR04zZEVsd1JXNVFkWE5tV0doa2EyeGtUSE5WZFhkUWFEVk1NMGhMUjNKSldtZDRhVFJvTWpad1RGQlZRa2x5UkU1a01UVllOVVJzYldKTmNsTnhka2xzUldSRE1XY3hPREEyYTBsM0wybFlORzh6UlVvM1FsSlJlRmxNYmsweGNFNXVXRFZaYjBOVEwxTkRaMDlUUTJoclFqUkxhQ3ROU2t4alRDOVJkbTQ1YlU4NGVuUjNiVEl4ZUZveFZVVlRPSGx4T0ZOSFFqa3dTbU5rYURsdE9VTlpRalpoV1RaTVRHOWlOMGxvUVdaMGMweGFiRW92ZW5seVVVTTFNSHBLTW1rMFJ6ZE1SM0pCY2xveE1rbEdLMFl2ZGpGalVFOWpjamRxYVZFeFNDc3hZbUZ4VkU1VlVYZHZlbFp6VlRCVE1UWjJiSE5JVTI1UlRUQTRWR0ZIV0dWVmJrOXdLMU5aVjBRMlptaHZja2cxWVRaYVZFNUZTV1p6VVVwSU9FcE1LMVozWWtkWldubHpVR0l2Vm1kNUwwaHZaMXB3Y2tKVFkzWldabWM0ZGl0VmRFcEVTSEUwV1dsbGRIbFlUVUZhZDNGSFdFNVZiRTF1VlVOdFV6RmhTbVpzYmpKU04ydDBOVlpYU0ZBdkswSlFPRzFQZEZGeVJucEZiWGRKYVhOUE4zSlBZemcyYmpSclIwWXhTWGwwY2xnM1FVTjVLMUJwTUVoRWREVnBiMUpGTkZZNVIwTnhWRmhNYVd0VGJsTXZORmMyTW1wcmJVazRTSFpVVFZSbU9EWXJUVE5PTnpocVYwUkhWVzkyUVhJeWNTdHJOblp4YWl0Qk1VVkVMMFIxZFc5Tk9XUXpNMnRsVFhCM0wxSTJjV2R2TVcxeE5IUllWa3AwUjAxWGFERkRZemREUkZsSGRVNVlUMGxGTWtFMmRWQm1UVkp3VVdaV1RXOVpjMVpHZG1oWmNXRmFSWFl2UzJFclREaFFZekpwYlV4dlZIQjZSM1pJTkZOcVNtRkRUREl3TTFwcU5tRlNRakp4VFZoS1lubGtkMlZIUlU0dlpUTk9XSGQzYmtsREswOXBObUo0TUhSMGFXZDZZMEoyYlhsWWMzVlpWa293VGxKd1NWSjVWbW9yTTFCcFNFUk1kMEpJVmtSQ05HbGtNMnROZFVKV2FraHFaVEZtV2xKdU4xVkxNemhMYldKU1FUWnRTV2xEVm5oNVRuVXJkV2xwTkhCU01sTTFZa1pZUWxNM2IzZHBLMGxQU1V4bmVWYzJSVzgzTlN0c2VEUmhhbXBQZG1ac1JVRmpiMHBJTURSTVNrSm5WVEJVYURCUllUUXhlbXAwYVRsUWVWRmxSMHBzZDNsaFMyZEZObkUzWlZGRlJEaGFUMHBKWlROWU1UUk9VamQyTkVSamFYWnhSazR3ZEVob05qUkxibFkwVDBSeFJVeE9SSGhyV214a1JTOVRja0ZzUVd4U1RWVXlTRTByYXl0RGRWazRSMU5VV1ZaVlkxbDFZMmhJWVVkRFZFUm1kM2hQYVdNME5tTktXV1oyZDJSelMyTjZLM0ZOT0VoMllYWk9VMGxxZURZemNHaFpRbU53ZUZWb2FFTm9hek5wU1RGYVozQnVkRkpKYlhWVmRrMW5jblZHVFVvMU0xWlRVSEIzUmsxdE1HSkNjbXBqYTFOUE5td3piekkyUjJWVlpETnZZa3R5YVZoaWNYUkhObE5uZWtkM0wwTjFPVkZIUlhSdU5WZDRTMEZ0WXl0b2RsSnhkM1V2T1hSNU1Xd3pTa2h0VDFGdmVrdHJjRXd5VkRsTE5tdE5LMHRrVlRkV2JrRlNUbGxtUXpkalpWY3ZPWE5MZDA5clFYTldiVU5yTkUxakwxTnFjVFV3TlZnME9VSlVhak5vWW5SUGFucDZNRXBuZGs1VVNUVk5XVEJhTVU5clEzQk5lR2h4WWtVeWJVbEhXVm93VFhFNGNHcHljR1pSVm5wc00xZDVaV2d5ZWlzMlFqbEVjbE5MWWpsNFQxUk1jREZGWW1OUVpuSXhVV1ZxVmk5b2QzaFZWa2R1VnpoaU9HcHZXblZzY0dObGNtVTBLM2hqY1cwelRGRXhVWGt2UVRBck4ybHZNRWc1T1VkVVJERllPV2R4Yms1VFZFUm9TWE5vUm1oQmFtMXBWVWxhVmpKNlNsZGpURzFpVG0xT2NUbG9TMU5ZVG5CblpGbFVOSFZ0WlZnd2JuUldhV1VyY0ZOS2VqTTNXbVpCTkdOa0wyMXlUVEkyY0RFNWRtdzFSa1JWWm1Ob2IxcEhORkV4ZDFGR2VIWTRPR3RQUlZwNmVrbEVURmd6ZDJsWldFMUZOVlE1VlhCVGFHMXhRbklyUjNoVVdFZHVVVWRDSzFWdlkyOVpNVWMwU1ZOcWFFMXVUVVZSUm1Oc1ZGb3Zha2RNUjFWeGNIRjJVazl1TDBwaVNVUmFPU3RGS3pkWFpHeEZMMFpTVmxwa1ZrZGFhbVpUY3l0c1JrODFjM1ZFZVVOSWFIVmFObVpWTTJoclZFY3dkVFZ3V2xsQ2MwcEpVbmg2TmtWak5XVjFSRFZQT1dScE9XZHRjVTlwWlZSMFNuVnRhRUZNTWxoNVRHMVlSMlk1T0dRclQwTk9OemhGVm1SQ01tNU9Sa1ZLTWxNd1RXTnpUVVI0UjB3NU1EVkJNelZ1T0hFNWRXOUxTMHhWWm5wM1UzaEhUelF6TjJka1pHUTFhM1l6VlN0bllXTnBWbmRvTUhWQ2RWcFljMGwxYVc5TmMycHJXbWx1UVVKTlRXVkNRMU5QYWpoamJrZG1NV2xMWTBKbGEzTm5aMjVDVEZFeVNVWkpkbmcyY2t0NlZVd3ZhVWx6VjIweGRWTlhla0puTTA5TFlUTjRabGg1U25SV05YSXZiVFJHUjAxUlJtVlhaR0phYTNWbk5HTm9SV3BJTkdkVE9XOUhla05GVUZNek1sbDJjamg2ZWxWVFNqYzVNbUpIVURaNE1EbE5jQ3RhYkZkQ2JEYzVSMHRDTTFReGJEVkVWa05WVW14dFozSkRhRUZJZEVwRmVUTkljbmhPVVZOUFpUTk5hazlQU25ORFRuTTBVbmRKYWtaUU4yNU9kVGM0YkZRdlMwSnVkelU0V0Raa1dsWlNiR0V4ZVd4TFZYaERVVGhqWlVNM1JuazNVbVpPYmxWV2VHOVBUR3czTVZGQmNtczNSWGh1Vm0xclZ6aHBVREZGTmpRMFVXdDNXbmt2UmtWMFJXaHNjWGhQYVhsS1JWcFBVbGR0UTBWSFMxcDVabVZvU25Nd1JGZEdkVWQ1VjIxV1RVdFhNM2h0UTAxSVZsWlFjVEYzT0VGR1F6Um5NblpQUWxOdk9YWkZaREZFTTB0MFExUk5jREV6ZFU5UmFsUnVPRVJ1U0U1T2JFNTVPRlZNVlRGVldtTkZUamc1YVdSWFpFbHZiVWhRUm5WVlduRmxielJaWVdOa1VHSk9jRFpyYTBoTlExcE9kM2hTU0RWSU5FRmlablJWYUZWRmVXUk5Sazl5UTBkTE4zcHZSeXRuUkRCUmRWRmtVMGx4Wm5waFRVMU9PREp0V2sxR2FGcExZbVpDYjJsTFIwMXdjRkJXVXpSQ09YWkhZbFI0UVVoSFdtaE9WMU15WVROMmMyeGpaV1J2TWpBdk9WbzBXVzQwZHpCeU9VaG9OVmt2UW5KRGNXUmpWWGMxZEhob1lrcDBNVkZEYlhJd2RYUnBOblpUTVZJeWFEWnZNV2hEUkU5d05uTkJlVEptYTFwdlpWZ3ZhVXRIV0VGR01XVTRRMmd2V1ZsaVdWRmxlRFE0TWpCaFNFVnFORkpxWjI5cldYaHFjSFYzYzFKUFQwcGxTa1pDWW5KNkwyNDNkMGR5WVVwTlZVbDJaMFJPYVV0M2RIaENVMHBHWjBZMFVEZHlaMkUzV1dGeVpsVndXR3h3Vkc1cFUxaGlNa1Z6ZGxVd0sweHhLMnhQUTI5MWJFWlVTRkI2VERab1pVcElabHBaUlhjd2IwdHdNVUZLVmxBM1pFeHdZak5LTWtaRE5sTjJWMGMwVEhOWlRIaDBTRk5DYkhvdlFqVXpTVzlDY0ZVeGIyMVpOMXBsZFhCbFF6RXdlVWs1YVRCeU9TdHFUSEpZYVVwbmVUSkVWekUwV0dWaWFXSTJibGh1YlU1RmQxbHJaVkpXUzFWbk5FdGxjaTlRZW1SNGRXUnNWVWx3TkVad1lYWllOVWs1UlhaTWIzYzFkek01TVZvMFFXbGFkalJDWlVGUVFYWkViSFpaZGtjd2RGZDJUM2x1VlZOeVRUWXlkbFZEY2t0U1RITm1ZV1V6Y2pRelJESXdjbWw0Wlc0M1VGWnBNVGhYTnpsVWFHWlBTa013U204MVpHbzVkSEJ1VTBsT1NUSTBiM29yZFVacFJFcHVhME5EY1hsVk9FMU1WUzlJVVdaVWVXZG9aM0JPUjI5c01UUndVVEl5SzBWcGJXUXZZbll6V0hsbFRXWnBTREJyU21wVmFITlNWR2RUV2xsR01tMXBhbTV0S3psQk1sUjZXbEV6V0hGT1ptOWhZbkZyUzJWYWRsWnRXRmhZWkd0MVVYSnRPU3QyYW5KdFZHZ3hTVkJWVVdzMmMyUm5RV2h4WlZkWlNWRmxhRmxUVUhncmVHRXpaaXRtTlZrMVZWVXdTa0YyTms1dFpYaENSbUo2ZVZGNVpVcEVUVkpUTDFoRmVtUlJVbXh6YkVoSVVrRTRkMGxzY3l0aU5FaEpSMXBIVlZaUmVIbG5VbVpQUlVkaGRVWjFTVmwxWTB0UVFYUmtPVkZ5Wm0xSWNFUXZVMVk1WlN0NFdFcEdUWGRpWWpSM1Z6aDNObmxyZEZFNVF6QkhUbHBMUTB0TWFUTjNUMHQwVVRSbFQzZHZZVzFDVldONFIyOVFiVE5MVUVZelJFSjBlVE5IVWpWMFluQnliRlIwVFZaSWFHbHpWbEkzYkdnMWRUWXdjSGgxVGtOSFVXRjZOMUJMTlZsbFFUWnVXbGRzTjNoUGRqRkdOREJhZG1ZeFJuVkJUMEV2V1VWNlpVNUZPVmxSV0RGWlFsZExkV3BhUWpNNFVEbFNSVUp0TlRaM1VFdFdVM2g2YTFSa1dsUXdLMVZWTWtGb1FsbFlhM05zZEVOS1RTdFVhVVZpT1UxTlUyeDVZVnB1VTJwdVFsUklOSGx3WVhkaVdYcHhZWEF2V1Rkck5XWmpUSEJhU0hCUU9HdG9ibGt4TmtGa2FXZHNha280ZVVGbFNHTlBTU3RHVEU5UlUwcGhWRGt5ZDB4TmJ6WlFPVUZuU1ZRM2JEQTVOV1k0UzFJemJuZEJWRXhoYm1KQkswVmtPV1ZQYW1SbWRqRnJSVzF6UVRsM1FVVTFNMFYzZW5JM2VIVTBNV05zTkVOcmNrdEtSVnBEYm1wcllYaGtiWHA0YmpCdGNrUmhURWc1ZVdWVU1HVkhOVVJUUTFJeWJUSjFjbk5OYkZSUk1WQjNkM3BHVW5Kc1kyWTRhbTFST0dSRFVubHZUazlJWjFReE1sTkVUMmRCY2t4WmJYQjRWVmROZVhCV1ltUk5jRzVEYVZVclVYWlRObUpVTDI5VGRVWTBWVlJVTjJkdU1GWjVSV1J3VFdoVmRFZFVZMXBvTUZwd2MyWjRVVEYyVUZKSGFXVjVOVWhtVDA1M2FWTXlNbVI2U0hsVVlrTkJZalJvYzNZd2RHZ3lPREpGZDFwR1V6QlZkekJzYWpFMFNDOXVkV2t6Tlc1SmQyUkZhM0p5V1ZZMVFubEdjVEJ6ZVVjeVEwTndWR2xUTVZkM2MwTnZNV2t2Umk5a1VtbzFjazlzZUhnNFEzWmllR3BHUjNCbmVrNUpXV3RFYmtSd0x6QTJhV1V5UVhGcFpESkRlRk12WjBwVWJISnlaMHR0T1VScUsxaFJabFI1ZW5WNFdIcEVOV3d5YVZselZEVXZRMEV6VERGclMweFNlVk5XY0daQmFuWnFiMko2TUU5R2QyTkdlbmhzVURGUU5GWk5heXQxVDJaaVFtbElNRGxtTlZCV1YxUnZOM0puV0dac09FUTRWMmwyYzNJMWNtcFJSM0JaUm5aWlYwWm1OVmhTVldGNUwxUlphRE15YldaaU56VkdjbU56VVU1a1YzUkhNM1l6YXpJd1pHWlVPRk13VUdsNlUxY3hVR3BrTDFFd01qaFVZazV0WnpCQk5qQTVhek5IY0dSblJuUldSRkZKWm1kTE5uUjRNMFZSWVVaTU56azVNMDgyWjBWRmRHVTRkbFpES3pCR2QzbzJPRWxhUm5sak9EWjFWR2MxYWpadUwycG9Oakp5T1hkc00yRXlaMkZLVm5CT2RUTTVXVVZHYnpVNWVVVldSVXhHWVhoNFVrZExWR0ZOVkhsbVFqRXdjbkZGVEhGa1NVZFdiUzlQWlRSNU5XMTBhVGRCWkhSWU5IQjNkMDR3ZVdwS1NEaDFSREZwV1RGdlNVaFlNSEJNU1hjeWRUbE1ha2Q0UTFwRFRsQnNZVzFzUldSUFRHbERLMWhNU0ZSd1ZXVjJXVUZhVDFKVU5VTTNUaXR3WmtwMU5HUlJkRlV5U0Rac0x6Vm1kMFpYVW1rNWVVeFNTR2RxWmxOS1ZrNVNOR1pXTDA1eksyZFFRMnAwUTBKbFJVODVaU3RXZFcxMVl6TlNjSEJUWkhKNGEwMTZVVFZhY2xaV2QxVk1TV1JGWmpFMlNFVTNNM1l2WTFsUE9YSTNRbE5LYzBkcVpVNVRVbGs0UTJWTFdtRm9XbkZRTjJRMFRIaE1hemhhWnpOV0wzSm9UVTAwYzFSQk0wUk5SRFpLZFV0SVRuTk9RV2wwTWs4MVpIWnpRa3hUVkU1QmREUnVWbEpTWVVsRlRIbHlWV050TjAxcVUzbG9ZMlpMU0M5MUszSXZNbEZFU0RVMk4xTlJRMlJEVTIxMk1UbE5jRkZETlV4VFR6UmhkM2hWUnpaak1FcElRak5tTUZWbk5WQlZhRXRtUWtSU2VVMXhWMHAyYzNZNFJISnhjbkFyUzBGWk5sVjBVRk14VW1kQ2RqVkRVRmQ1ZHprM1QxQnNjRTVxZW1kSWIyeG9RMmgyY1dkcWExWkdUVGRRVVU1WGFXcE5NRTVQTWxwSmVYRlJNVkIzT1M5b2JXYzRia2hUWVRobFlVWnBaazVGUm01bE56bHpZMVY1ZDBrMlJXTlpUMnhWVmk5WFNtOVViM0JJVldacWQwYzFkbGhoUjFOemFURXhXSGxsYkRCMEwybHhkbkl5WlVoUVJXOXdLM0p6VFVWQk9XVXpNbXBpZFZOT2VqVTRaRGhIYWxwSU5taEViRFZvTlVKT1JrTk1iMjVETVRaUFEwbGhWV05WYTBRMk9YTkhjMVJSYTJ0M2FtaFNlbk5FU25FMmJtOVpWbFJPVkVsek9GZDFUQzlhUkRSeldIVndiVFp5V1VkeGRHSTRLMUl2Y1RKMFpFTkdUWGhIWjJ0TFRXTnNSR3RUT1c1MFQwZEJNMlJDWmpsR1ptbFVVbEZ5TVdKbFdUbE9Xa2R4T0RNemVGRjZMMmNyWlRkb1NreG1NRkpYU0RoWWN6bEROVk5LU0dGRGMwSlpTbU16UzAxUlR6aEpZVmRJVm1SV1EzWlVkSEpzUlc5VlJrOXdhR05ZVlRKaU1sZFVWak13VUhoeE1HeHhhVVZSYzNwSU4zRnpTVGRwWW5GbWJHaEZhSHBTYURKc09WSktaVEJ1VW5oMlpIRXZWMDFzWW1ZMVkyeElla050VWsxcWMycDFMelJvVmxwMEsxUXJLMlJVYmtwQ1lYVjVUMDVWU0hGNWFGWTFSMUJFU1VWWE5VVlJWR2N2U2xOSllWbGFNbVptZEhoUVJreGlUM0Z6UTJOS05rdDRZM2xWZWswNVpGQm9hMFpYZFdkeWN6RnZZVVUzUkZSYVRUUkhRWEp5YjFkdWJFeDRRbGxYUm5acWFHMHplbk5DTVZWTFJuTjRSMmxwTW1KNFpVMUJWR2RYWTJGV2NUaHdibWRqZDJjcmExTTNaMVZyU0N0SGRtRXdjM2szZEV4elltbGpiVXRaYUVOR05tbzJNQ3RZTDIxTWN6bDVkekoyUTBjeGVGcE9Nbkl4ZDNoaE0weHhPR1pTYmxSSWJsaDZNRVpRUXl0cldHWkNTVmt3TVRaU2QyeDZZME40ZGtkbWQwZzRlRzQxYlRaTVpFaHpTekZUV21oWVdYaFdMMDgxYTNNcmFYRmxMMHRyUnpFeWFHRjRhVFY1VldJMFlVeE5TelZEYkRKSFFubDRRMW81WVhkRVNHSk1hMEZZUWpkNEwzcHZlRnBDTW1aelNVZExhM3BhVTNGeFFucFFLM2hOV1ZwWVdEZHFOamxsTkhjd1puVnRlVzAxVjNsM05YZEJXVnB6YTNCR1UyTlVNRlJrUXpBcmJqSnRkQzlEY0dWR1VHSm9UekpvV2poUFZWVTVPRXN6WW0xWE5WVlVVMUp5Y0dSa1RteHJVemhUVm5GMWRtZHNkVEZDZDFBNGVGUllUM055UjFreFNuSmhkWHBYWmxoUE5WVnJVMUZoZGsxUE5rRjJiRnBqUjBwd1VHRjFOMmxQYjJsTFdqTmpaM2hJUWtWblRESXdibEZ4YXpGelFVbEtlVEZoUm04NFdISlpkRmtyUTJkbEwxWmFWMU0xVTFaYWNsaG5kakF5WVd0SlNFeEVUMkpaZDFsVWFVRmlUa1ZIWVdoelp6RklPR3BhTjBGRFJtVjZORFp4Um5Nd2REWXpiek5OYzJOV1pXSlRibTV4WVVGTEszQXdWSFZOWW1sQlYwMUZiSGRLY2k4d1VWUmFPV1I0VkVWNU1rWktZblJpUlUxeVRqRXhTakJ1TmxWeGJtNVhSWGxxVUZWNVRFeDBSMDQ1YWtsSVZ5dG5WVlUxVDBOdFRtZDBlV0o2V0hwSVZXNXVZeTlyVDJzclRUVTNORTFOVkV0cFRVTTVObTFoTjJ3NFRXcFlWMWt4ZUcxQ1RUTklWRUUwVkVSUE0xVlliRlZ3YkdzNVdHTXdZM1ZQYld4dlJFWk1TMjUyVUd0VEsxb3djR3RaU3paSGVYQXlTbTl3ZDNkU1dETk5OSFJqVWl0VmNFVmhObEl5YkRsaEx6SXJiM2hQVVVodWFDdEJiWFZyTW5BMVozbHVXbWRMVldoVFp6STJNVUpDWldGVFYxWkxNV2MxWTNFeGRqVXhXWFJqTDFaeFNERjFTMnQ1T1c5MlRYaFZTVTFaUTJaa2NEZE9abVJvY0V0aVpUSXdjV1Y2YkZsT2VYWXhZbEppYlVWUVFtSlNkRTk1YmtWdE9VZG9TSGRNZW5ocFYyRkhjSEo0WWpoRVJIY3hSV2syZUdSQ2NqQmlaMng0WnpoWWRWbGtaVTVCUTFoTlpqaFNNVGN2VnpBNFUxZHhaM1ZvYUZWQlVFVlBXbXhCWVZFeGRHdFBZVVpxTUVWTVNub3hOREJZVDFKVlJEQkJabU5rTm05UWFrSkZaMFZuWWpsTFpEWnhWRTh6VTFOM1kwZDZRbmRaWTBkYWJWZDNNVmczZUVkd2VFdG9VeXRxUnpWVlVrdzFTMXAyVFhOdFNEUlpSbUpsU1d4UWJFTTNPV3BITjJ0cVNWcEZOUzh6YlV4dFducEVSVGRhYVRCMFlVWXhNak5PYTAxUGJVeElUV3hLYmxsSk1XSjRXVmxOUlUxdlZUWXZjRTFyYVZsUVpESXhjRWhoYlhBMmVWbFhibTV2VEhwclpWRjBNM00wUm5CeUt6RkxSelZGUm1GbE4xbFpjMDl0SzJka1FVVmhTWE5SZDFWMVdqUklXVUYyU21aaFJHUTRUbTVJWTFWbWRVZzVVSFo2Y2twemVIZDJjSEZhVVd4MVJrUnFURzlLS3k5d2FWTlZjRlJqTlRCc00zRllNRnBXUjI5aWMzQnZVVzF5ZFd0TVMxRk9lRUozVDI5aVJsazFTa0YxZFVoTllqSjFTRnB4VFdOdE5rcDJjRmxRZVN0d1lqRlNaSFo0U0hOeWFVTnZjRGhxTXpKRk9XRXhVSHB1VXpCc09EQk9aWFJhSzBKYVQxVm5NSEZwYVRGNE4yeFJVVFJhVERoSFNtSmhjMkZ3YTBzMWFGQkhSRnBOVG1sNlRHNVZTekJCSzBWWWIxTkRWWFIwTUdKelRtZEVOU3R6YkRabmFXdzBUSHB4ZURCRGNFRXhaM1kxTkZCb1VGbHBNbnA0VlVOb2FISlpVbHB6TTFKcVVtbG5Ua2xJTW05T1kzWkNNeXR4TmxSSlRUVXpPRkUwT0VwTFRFd3hhazVCVjNrNGVXbE9SbWtyZEZGNFVYQlhkRGx2ZGxKR2EyeG9TMEZRUkVVM01WRjRZMVZIUzFrd01scFFZbmMzYmxNeUwySjFaRVY2ZDI1NU1rbEplbU5PVW5oR1FqWnZOMXBLWldoNWJHeDZVVm96ZVhZMWRscG9lRGxXT1VaM1EwOVRMMUJOZDFSdFRraFJNMGR6ZUZkdWJ6VlVRM1psZFdKd1MyMUxSREZ3T1dGMVNXSkdhelFyVVdkR2JrcG5ZV0pzTmtJcmRUUTVhRWhHZEcxblFWUXlXalJNZGxWMVp5OU9OVlY2ZWl0d2RYVTFPVnB6VkhFclVWTTFhMWhwY3pOVU5sWmtXRmc0UVV0TFV6VmtNVk5IV1RBd1QxbEhUbGczU0ZGelRFcG1kM0ZuUlhSVE1WcHpja1JDT1RaaFJGWlpjRE5xTnpKbFYzWjRZbEZhT0c4MGRUYzBXazB6Y1hOc2VYQXhlVWRQT0Zac1lYZDBlbTB5VTNOcVRHeG9VbWhVTUdsVlYyRnBWMmhuYW1aYVVHOUtkRUZ2ZWpCek5WQm5OMUZFZVhKclRqTXpORXhUY2pWSVRXZFVZa1JtTldaTVJtdHVaMDVtWVVaMmFXYzFiMUp3TmpaSlJrZEVVbTgwV2l0SVJYRmxhelJPUTBjdk9FYzNUbEp1VkdoRGF6UnNhVFZvTkdvMWFrZHZhVkJ0T1ZoamEycGFiVlpLU2pCdlJsSkdlbGwwYW5oSGFYQllaeXMwWVZGNVJUbDNVSGx4ZDA5NFJrUnFiVk5JVERGVldVbDVkVFphUTBGVFZuZ3hjV1YzUzAwMlEyRTRWRlZrUzNGME1VUjVhMWhFV25aNFRsYzNZVmx0WWsxTFdUZE1ORk5OYm1SRlprTnZZblo2UzJVelFWaEpZaXR5ZWlzdlJUQnBaMGRxTUhOaUt6Tm1RbmRuYVVzMU1rNUpkVVJ4Uld4RVdYUTJWVTkzY0ROU1VXODNOSEY0VW5CTmFUUllTa0l4WTNkMWVGSjZTVGQ2ZWxKdVRFeHBWM0JIVTBsT1ZFNDRiRzF3VlhvMWRFWkJWakJaTVRWaFRIQndaMDlsVG1wTWFEWlJhWHBJVlN0R1RqbG1RVGRqZHpSNE5GcGxjMlZXY0RWa1VuTm5OWEZrTDFWQ1JqUTRjVWhZUm1zM2NGUjJWVmdyZEd0MFVVOVdWMFZLSzI5b1dWazBaM0JGV0hVeVRVNUpiMFJCZUdNMGNVTk9TamhhVGxaRVYwcEpURVZ6ZEZGbWFtOUNkVkZrZGpkbU9IcEtVRFJIVVVsV1FqZzRjbkJNV2tSdU1YRldOM3BMVW1KcWFqZHNjV0Y0ZWtSNVJXcHJabmMyUVVsUFNtZHBNa1k0ZGtwV1ZWQXlSbHB1UTI1V2JUaE5SbFlyVUdKMldsZHpPRTFFYmxvMVJYZExkRGQyTW5SQ2FYaE1jUzlZTUZkdGJESnJiR3BrTldaR00ySm5VbFVyYkZBeVduZERlbFZsTWxsUmRFNVBTMjltWm1aV2RXZzNZa2wyWnlzeE1VeFRlbGx4U2pkdVpVMW5OSGRSYzBGbGVtNVRWSE5oWVNzMmRXTXlUbTh2TVhWV1Rsb3ZTM2RSYUVWdVRsa3hZbFJtUXpGQ1RtWmFjSHBSYTJzd2JFRlpNMncxTW1GTlNHRjJTMnRTZDFWblN5OVFiMjFoVFVoeVkwZDBlVTlVTUhCbGVYZzJlVVF2Y3pkeGFYbFNlbFJKU0RKSlVVaGxZbEJ4U1VKRVdtVXpaVEZJY1N0Mk5FSlRkbTV1V0VORk5GQlJaWFZqVDBGUVVFSlRUVkJJYVc1aFFXMHlXSHBFUWxoTVIzWnRVM0l2TXpSTU9UVlFSVU51VVdSM1dVeEVhVWhUVFZKdmN6Tk9iemhRWVdaclZraEVkRGhHWmtSWmQzVkVXVFJEYjBSaWNWVXdOekJJUTIwd2FFRlRTRzlNVm1ScVZrbHVNa1paTlhWQ1pqQlBXVGR2Ulcxak1XbDNTbkpWYzJ0WVUza3hUMmM0VnpGc2RVVXZOWGR4TVVsNGVIUnNSazAwVVcxaVNDdExWbkI0YkdZNWNrWm9OMWxJY1VSNE1uRnZTMFp3ZFdwT1lUaFpSU3MyVjJjeFVXRkRkVmRLYkhobmNWVXlkbXBZVVVwUVdYSktXSGR6VmxVeFFuaGlLMFpDUVZkek0wNXZhMVZNYzA1TE5rdHFaa2hPWVV0WWFsVkhSbU4yUVVoMmVYUlVNMk5GT0c5RlJEUlpTV0o1VGtRd0x6TkRkMm94UVhJeE5rZHpaVWhKUVd4d2VEVm9jR0ZTY1RkdVMxaGxOa1Z6UW14TmJrVmxlR1oxY0RSR1Ewd3ZaRW96WldaQksyRkpLMGM1UWpSUVFqZFhaMWREZFZOMk1EUjNhMU56VHk5MWRHTnRRamswUTFOUVowOUxaVU5hZHpab1VuWXliSEZKUVdsbFUzRjZaMEZoZUV3dmRscHlVVWhOWVdoNlN6bDFiSGhuU1hWNVRraFNWSGhFVVhaYVpYbHRTMnhaY1dOU1JreG5XRmQ2ZW5wMFVXMXlkVmQxWTBaVGRHSmFiMGhNZEdGVlpVZFJWblJhWlZsSkwydEZNekozTTJkd1EydzBiUzlPYVVka1RrNTZXRWhpVHpOQ1N6SnBWVGwyYjFGWmFWbzJPVE5WVDJ4VksycDNlVXhJUVc0d2NVbFBja2sxUW1vd2FtWlBWRVpVU0Zod01rdGplRzVtZG5CTlVsSllSVWM1YjJOVlUyOUZZMDFCZWtJd1JUaG1XbWRtTDNkM2Qwc3lVMmgzT0U5U2ExRTNlVFJ4WkhKTWR6VjBaMGRaY0Zwc1pHUllhRTVwV0RKc04xQk9WWGcyUVdWR1NtczRVVWRCTWxKMUsxWm5jemh2YUhselVXMUtiMkZDUVVSaWFYcEVVM2QyVG5GVWFVTXlUR2xDZGxOWlZXTndWMUl6WjNBMFNITnpkSE52Y1hCRFNscGFOeXRSTVRGTFkyRTJOWEpyWTFGTU1pczBkSEkyZW5WYVdWZGthbFp1VUhkTmNHRmxhRXBhYVhBMldEQnpUVzFoVlZJeU1IWjZUelI0Ums1dFptTmpOMjRyYUM5d1VXZGFUSElyYVVjMmJrRnlhRlpEYzAxQ1kwZHlMMGRLV2k4d2FtWkpVa1EwVVhKcUwwODFSRU5QVjNvNWVqRlhhVlJNWkZwbWFrSnJhV1JUWkRkc2N6QkNWbUZHUmtGQmNHRXdiMnhwYkU1blRFSkVRaXRhVlcxelRURjFTUzk2VkVWck5sZFNNMnQyUkhoWWVWUlVhbEZ2YWpGUFNrSmpNREkyYlhoQ1psSm9WRUZ3YTA1NVJsQjJZVllyYVRaTmJsWjBiMHBXYzNaR1kxRkVZVFZSY0ZobGRFaGtUVWh6UzBRMk5UaFZSV0pZVVdZMWFEVldXbWxSYVZwWVlVSTBaSFoyU0ZFcmRrUkViRXBOYTJZMlNrWXZPRXRrTDJGT1NrWTRSa3BxWjFsRFFYZHRRMHhqUm1kbGJubHNNbWR4Y0RaVlVFNUpWMEYxYlZGa01tUmlkSFpOVmtkVGVrTjBZbkoyZWt4V2RtRk1iVFUwTUZsT09TOUVZWEJUVUhFcmNWazVaVFJCZEV0ak1GRkZWbmh2Y2pOVGVWUXhja1JNUXpKTGMwdGtWRFZ3YWs1a1psZFRNekJYUTJwS1kya3lhbFJhYldwTFdXNDNVVkpVZWl0UU1ra3ZhRTFuZG5kaU5EVndZVkZRZEZWQmRqSXpRMkpPUzI5aFJHcHJOekpTZVVwbVRFNVNZWEpaWVRkaGQyZHBUbUk1VW05YVVEZDZkMWhRYm5WVE9VdE1ZaTh5YmxFck5rMDBZamMxUm5OSmEycEVTRkp2Y1Rsbk9GSXpjWE5uVWpRNGJDdE1hMWxRUjFoVFZEVlplVkZ4YTJaRWJWQllSbWt6V2pGSGRGbFJUR3hWV0hKWmNYTnhaalZHZFU1Q1VuUnZka0l2Ukhsd05XeFdRMnRVYmpjd016YzBPVGRPUWtGR2EzTkNNMmRLVmt4RlkzSkRabXRUU0d3elFXOHliMWxHVG5KU1ZVNHdURWx6ZG1ob1RtMUJMMDEwU0ZRMmJreHlkbEkyTUVKU2QySjZhbXcxTWtWTWFHMXNiVlYwVERoNlJEVnJTMUJuVm1oT1luWkRTWGxZVkZGclIzcFpTMlZsVTJzMmFWbHFkbGxGT0ZKSFdYbGpVbTlXZG1abGRHUm5VMDVrWVU5c1ZGUTFRalJUVURGeFpDOUphRzF4ZGtkS1FWQkNRM2RYY0RCRGRsRmtNVlZyZGxKV1FsTlpOemRpZW5kbGRXa3lWa1EyU2t0RlVFdDVWRTB4TjJaeFdrdEhlamh3VTFCcE5uZ3hZV2RRU2taREwxWmFlbTFpY201Q1ZpdEJObHBtTURneWVFWmFUVEowU2xOeVNFeHVXRVF2TDNWYWRWZEJNR1pYTTNvMGRHTkVabG80ZVVGWFIzbFBiMU00ZURKd1FYUm1jVTU2UVRBcllrSkNlRFZPZEZOUVVUUldNbTFHVkc1WWRWZHNMMVZxYW5wSWVrMTJVekJrTm5VclpHTjVNVFZ1Wm1sdE1taFpLMWxLU0dwelltMXFMeTl5U25OdFZVcFRUMEZPU2t0c04yMTBhR0pEV2tneWRpdHVhbVpaWnpCMFpFOXdWVkZwVERKMFlrdFdZMXBYTVRWcmRGbHBVMGxPYzFOWmRHSXlVQzlNUWxFNWRXb3plRGhzYlVWek1qZHFiQzk1Tm5ORE1rSjRaV2N2Um5GTk1qbHJPRXR4ZERWSGJrRjNOV3RIUjNSaFZYbGFZbFJ4Ym1OelNWVjZZa3cwU1hoRU1uaFRhRXh5YUc1SGQydFBMemRxYTJ0RVJHVk5VRk00U0Vwd1RFWm5hbTFMY1dzMlRtbzVkMFJGVDBFMVRtNVVWMnBsVFZscFdtTnRkazFPVmtFd1ZXaFZhVE54TDB0ck1GVldSRm96TTJkNmFFVXhTakp0T1hKRWQyWk9NR0pZZFRKM1YwOUlObWQzVjNObmNXdDBXbE56Y3l0alNtaEZOWFV5VDB3d1pDczJVMGxpZDNKMlRVbGFkRlV3VmpFNGJqZG5ZVlp6YjFsSU1tNDNOMWh4UTJrM1lVOU1kRzVsWlhGUWF6QnJPSEJJWmxkbFVHWkxPRFF6VldzMEwyVkpPRk12YVdOWlpIQmtkWFZaV0hodFVWUk1VR2x4T0ZsWVdIUmFNRE5aUTBWdmMxcEpabTVFZW1sVFpTOXpLMHRGV1UwcldTOXpZMGQzYld4a1FtUnFXQ3MxUlZOWVluUXZWblJPTjFJelpGUjZiVFpSWkVWb09ERjBSMk5LZFdRelNIZG1URU4zTlRGU05rSXdObTFoVkV4SUswcHRjemRzT1hSQk1qSk9hV1pxWjBaUVpYUkVabEZIUWpGamVXcGlVVGhHZVdaSk0zbzVTVVJJV1daNmNqWlBZazVOUm0xeGRHZDZORU5yYmxaWWRWVjFiRFZxV0cwNVVYUnVOVTQyUVVzME1HbzNRVGx6VDJOVWRtcDJNbEoyVW1jdlpVSkJXVGRLV1dVM1NsSmFSRTE0V2xkVFdqaHRNa2RSTm1OUlFuTlRhWGd6VVhRdmRIRlRhSFUwVWs1alMwNHdja0ZIU2pGNWF6WnlObWxqVDFVNWFraFlTMnd6T1hOUmJFb3lOMGhqVTJJeE4xVjRiRmRhYUU5UmMwVlNjalZRVlhCSk5WSTBhSFY2VXpCblVpdG5ZVlp6S3k5SVEwVXpVVEJQY1hRME5rSkhkbXRJUmxKVWFTdElVMlZFYzFocVZsZGxlWEZFVFRCYVVGWTRNbWRFZUV0RmNrdHdiamN5ZHpsNVNIUkpRVlowWTJnd01taFVUREIyWkM5U0wyOVZiM2ROZWpCRksxbG9RVXBrWlhod2RrRklNbkY1TDFneVUyeEhOMEZJTUhWd2R6SjNVVTlyWVVWaFNrZ3hTSGwyZW5sQlVYTnpXbk50UlZwclRrcGhNRTlJWWxWNmRIVnpMMU4xVW1oTU1IaERSWFo0VjJoRVVuQlJjRzlrTVdaVWVVMHpRbU4zVVhWUFVtWlhjREF2V1VoMVltaExkbE5yYW14U1EwRmlhVmRPYUUxcFRURm9ObHBOTjBVMFVtOXVPRWx0UTFKdVdqVnRObFY1YVVsNGFVTTJXRmt4UWtOWGJFOHJZM0pVYnpKcWR6Y3ljVzk0VTA5WVZuZGFOMmMzWW5Cc1QxRkRaak0wTVZKeFJFMXdNa1pCUzBkSFVtUk1Ua2haYlRJdlZVTlBZMlpMWm04d1pqaFFUakJJZWxWRk1VUk1WRWxqVkhSa09HdE1MM2hPTWpNMFNEZDRXRzlIYURZeVlqZzFaVzVhVERCT1FWSTVSbVEzUkdKNFdYbzBkVWxvTlRKMlZXMVhORmhYVWsxeVQwbzBRa2xSU0ZnM1RtNTZTelZOT0c0NU5VZFhjbXhUTDJOM1NDdEtkR2hhYjNjNFZYQk5ZakZvVlZaU1RESlNWRlp3VkRsRmJraEpkRWNyTm5WcFJTc3lkak5VYkZWTmFsRlpUekZQVnpBd01FTkJVbTB2WmtSU2JVSjFOa1l4TkVOV1RHeFpaU3RZUTJWQlVURjVaa2RQVW1VclJFdzJkSHBpVEcxTk0wOXZZWGRuVW5WaFoxbDNRVzlrS3paUFNFSTVMMUEwZVdoSmRYRktWSEZLZFV4TU5FUmhNVkE1YTFKU1JsTXhkMXBDTjJFMFZUZGpOemxZVFU1cFdFVjFZazlSVmxoT1ExVkZRWHBzTWsxRmVVdFJkVEVyYkU1cE1rNUhjRTgxWVhNMWVsVk1LMGxxWkhsaFVYWkZaWE5HYjNkbWNDOVFRMHhNTUVwSGRFSkVWbXhUTm5WNVZUUkNOME5hZWtJNFJ6RktNRVoyZVZaV2JVUlZTMjU0VGl0c1pqSXdTREpIU1UxNGNubExUMjVVVDIxSFJESTJaVmxXYUUxRGRsVXpOR3QwUm5GNFkydEJRVFJGYm5jeGFGUkNXRko2T0hrM05qVklhamx2TW5Obk1rTmxaa3BuTm5OUmExTnNkbFJuYm5kMGNqRjRVVVkxTDFZelZFMUJPVFpKZUhwdWNVNUNTblo2YW1wWFVrNUJWamgwTTJWR1JVODBZV2RGZFRCSlNsZGhXRXh0YXpocGIwNDJjMGQ0VmtkS01qVXhVRkZIV1d4RWVFTjVOekJMVTBrd1pGUnNUbXhZY0N0UWNHNXpaR3BDV0UxaEswOHhVVVJNTW05MmQzcEpNbTlLUmk5TlR6Sk5TMnRDV2tZNFRHOXBaMWh1U21WRFdYQllkV3RqSzI1NVNqbEJaVFJKU2xwSFFVNXlZM2xIYW5jMVNrZEtaMG8zZG5kSlkwWjJORlJ6YWxwb1FsZDJaRGRyWnpCRmJHdzFkbFZIYTFOdWFWQlNUSFZWTW5FeVZ5dFlZVzF4Y0ZKaFFtUk5WRE5CUm1kRlEwZE1jV3haU1dSNEwzQllkM0pGVnpGbWJIWkJXRUYzUWtRME9VWjNOVmx6YjIwMk5uZFdjazVUY1hSaGFrTm9SMk5SV2tzek4xWkNVRkpCYzNSc05uRTVhalpXTTFZMWRHNVlkbkkxVVdWeU5XcEJjVWN3V2pjdlQwVXdiRFphZDJGeFozbGhUbTl1UlhsUk0wazJSek42ZGs5S2VXWjVhMUJqSzJwbVFVeEVSMkoxV1dVemIycHhSWE5OWms1SU4zZFNXbTkwVTFKTk5tRnJUR1ZtUlhGemRXVTVNWHAzUjJrd1ZGTm5VMlJhUVdSS2FtTkZUekpaYjFob1JIZzFhMDg1YVhCMldXZExiakpvYWxkaU9VTTFSa1ZsZDJWcGRsZGFNMU5rUnpCQllWVTBNa3RMVHpndlpWSlZWVmxFUTFSbWQwTlZiekJOYmxWdWJqVmpWR0o1VUVoTFpWRnFTbXhSTlZZNGRrbHNXa05EWkdFMU1HdGpObE5VV21OdFVWVlJVM0JrZUhOT2VVdE1lVE5sZERkUk5YY3pUVzVhYWxkTk9FcEpiMDQwU1U5M2QwRjNVWEJpVTFOS1FUUmxaalJCVTNadGFETk5UMDF1YVdGU1EyaGhhalEwWmtOMFJEbExSMDlQU0RSNU5XRk9XRlZCUzNWb1JVUlRNa2d4YnpZclNUZzRjV1Z1WW05QmFuQTVhMlJ0YkUxemFWQmFjRTl3YUVzd2RrWktjemRNVmxGTE5UWXdRMHRvYm5CQmRHcFZjVVl6V1daMWNuUmtSbWd6VEdKMlZtcFBhbFppTkc1cVZVOUlVWGx6VVU1UFlXbElNaXM1ZEZReVZFcDVaV1I0TUM5VVp6WTRhMUZFUXpWck5rcEZlalZhU2sxaldWQlZPRFZJVEdOUlNtMTRPVVpvWlU5aFNVRjFkSFJ5WjJNMWJITk1abGxsWW1Wb1JYRjJjRTVYZDFkd1NVc3lla050YWprMU1sUTFUbFZ0VlhNM01HMHdjazhyV0RZMFdWTTNVVGxJVm0xdFJERmhjbWRYUmtSa1JHaFdNMkphT1dGeVVHbEpTbkpVZVRWU2VVYzFORGt3V1N0dE1XeGFiV2cwTDFBcmF6SlhSVkZ0UW1OaldEVlFkMmw0YVVwa1NISk1UQ3RETjJOclZYQk1TV3d5WVZvMFJsTnhVVGxZUTBJd05XeHBiRXR2ZDNwR2FVRnJiWGx5YlROclJrbEpTQzlsT1UwNVVWTTVSVEVyTTNSdVN5dEJPSEk0YWt4dlVqZHFVVFJuUTJKaVQwRXlWM3BHT1c0emNrazBjRzF2VG5Zek1Fc3ZUVFZqUkROQ1dteHVOalk0U2t0dlVVUkphMVJKY25JMU1HaGtTbVFyTjNGdVNta3plVGQyZEcxMFZFOUJLMUpVU1RCRFdIVnBlRVYxY0VVd1FVTXdiV2RoV1hJNVZIUXdOVkZVZG5Fck5TOWxlVFpKVjNGelRGRlBZV2RVTWxoRVZqVkRUMHhoZUdZM1ZYVnpOV1JQWjFCeFdqRlJZakEwYUVKYVdraEdlVXA2U0dFd00xTjJVSGhPV0VOVVREa3djSEIxSzNkUldVdzVWRWR1WnpsVE5ISkdja05NYlZsMU4yaHZURTh4TlU5cVpGaEpiMWh5VG14M1ZXODVkMVJQT0ZWYVIzSjFTWHBRYTBzeGJHSk9jVFZYWjB3MlkyWm1TeTlGUlhWSmJFb3pkR3hWYTNVNFdsazBhbkpSVEZwWFptRktSMVpoWldadVJHTktVbWh5UW5SSVdDODJWQzlxTjFnd0wwTkNiRzh4VVRsS1NFTm5SWE55TTNKWVQxWk5kWEY0U2prM1IyYzRTa2xyTjBSRFpGSk1kRzVqVURZeVYzTllkVXBWT0dKVGQzQjBVbGt6T1ZrelNTdHBRVlpqVVM5RU0wcEZkR0pxUjJRclUyeGpNMkZTYkdZdmRrcHVlWE5TT0dwbFRTdEhkRmxoWkhGdVlWVllRbWh4TmtacGQwNXhSMjE0WjI1MlprUTNWR1pHVTFreFNuUm9la051TjJSNVNIaExhRVE0Y20xSE1uRkxVM2xyYTFrcldpOWhXbWRIZUVGRFFuVjRhR0ZoY1dkQ05tWXlLM1F6UjBRNWJERjROV1Z1U1dacmNuZFBZWE40VTFKeE1qRlJkRzlEV21odWFtbDJhV1YzYVVkRVlrbFFhVlpsZVc5MVNXbEdMMEUwV20wMVFpOU9UVFI1VjJ4S2VGRTNRVFpFUVdWamFrdFdiMmxIYnpSSUsydFdaRkZ5VEdsa1IzWXlVelJYU0dweWFHMHpkVE52UTA1bk1rVklSVEZzVHpKVFJTOUNORGt3WjJaT05YZHdRME12TWxRNFNWZG1kMHhRVjJoYU1IcE5NMVJCT1d0alprczJWMUJqWWpCNWNFSTROMUpRWlNzM2RXbFRla0l5UTFabFEzcDZWVFp4WjB0cmRucFZWV1ZoT1Zwb1RVUmlTMDVTSzBwVkwydzFLMWh6VjNKWmRHOXdkVXh5V1c5NmNIaE9SazhyWWpCbk9HeHZUVEJ3SzNrNFQweElaRTFVUWs5aWFFcElkakpuTVU5Q1RVZFRPVzlsSzNZd1pqWm1NSE5pYUVkSWNuZzRiMUJCUVdoS2RYQkZSM0ZKY1U0MVlUbDJSeTh6SzFSMU5IVkRRamxoY1hoV01qSkZSRFJwV0VaMU9IaFVTVTlVZGs1ak9YZHlaMUpUYTNWYWRqVXJjVWhPVEdSQmJUWkJPSEJVV21WRFRGcExRblU1TjFGUFNTOVhTVE5WY0U5R1VuTTVWak5KZVZRd1FqTjFTMlpIY0RWU1dWZFlWWGh4ZVVGVFRrTjVTM2xsYUM5bVZXWXJLemx6YlVKMVdHd3hlWGhuY0ROTU5FbDJhV0k1Ukc5QmRXRkJZVTVCTjBOd1VubHdTbGwyVW5RME1YWTBUa0Y1TmxWSkwyaHFVVTlpVGpFeGFVUlpOelpFV1M5cWRsQXhRMmxVYkhRNFkxUllUamR4VjNBM1ptTXdkVXR6WTBSbVZtTkJaSFF5V0RCb2RrTlpTM0l4ZFVWRFpsSlBlbTFQWm5WTVVVbzBTWFZsVWxGSlpIWnBNa1YwZDJSSlpWaEVkbTFKWnl0aWNGVkliQ3RUUWxGT0wyRTNlVlpMVW1aMldsQTBLMGhwWTI1clQxWndhWGt5Tlc1clJtUXlPRXRqYVZabloyUmpLMVkyWnpFMFFYQTRRVFZPYkRjNWFIVnliMUJJWlhFeVdFRnRTVkk1Y2pSWFYybEpiRVZXYkVkTVZqWnNOazU1WjJreGRDdDZhV053U0V0TlVFcENTWFpJU1Vwb2IyMHpTRmx5UldkeVFVWnNlbGRYUlVkbVptNUJZMkZoU21OcmRrMUpNMnRSTVZsNll5dEpRbGd6V0hNNEwzTkRaRUpDWTJSRVIwMDJOR013Tm04NWQyOUROVU5CZDFOUVRWcDRiak5wYlhjck9HcE5WRXd3ZEZWVGVqY3ZMMVY0ZGtsWGJqWXZPV2xpYjBsUFducDROalpYVDI1M2FFRndXRU5VWVhvMFJIa3hWSGxGUVVJeFpFTnpNMDl4Um5WU2JFMVNUWE5ZVkZsaVZtVkZPRTVLUlhOdU0zQnNZa2wyUmpCbU5DOVBNSElyUnpCMFlWRnZRM3A2TkV0bE5ETnhTVkZ6UWxkSlF6ZGFjMDlMVkRKTVNWUjVNRGc0YkVkd0wxZzBTRGcyYVc4MWEwZDVZMmxPZUdWVFRrcFRWVFZtU1ZaMmFEUndZMU5PWXpsa2VDczVValZ0Ynl0UWJqQk9TMUZLY2xSeFRFaG1Ramd3TVZrM1dreEdhbGN6YW5WeFVrUXlXRkZHYVdad2FFMDJkbW80V1VabFZXeEJPRFpTY1ZreFZ6VmtVRVpEWW1NM01EWnFhRTltUlhKd1ZscGxkVEZLTVZwbFZGRXdVVmhCZG1Zdk5FNU5Za1UxVFRkc2RFNUhkbTVMSzFSS1dGZElWV3R3TjBvMVIyOUxabEZwUnpkaFZXOWhjM0JyYTFGVkwxTk9jMEp5WlhreFVFODRhSGxqZVVFdlJUZElWMUpvU1dKdFJYTkpNbEZFYXpscFdHTlJZbmsyUkhWVVRrZG9iRkkzUzFCc2IxTkJjR3BIVkNzeklpd2liV0ZqSWpvaU5tUXlORFJtWlRaalptSmhNMlEwT0RNME5qVXlaalk0WkRFd05UZzBPR0pqWTJNNE0ySTBNbU5rT1RRek4ySXpNVFJrTWpnMVpHSTBZalkyTkRBMk9TSXNJblJoWnlJNklpSjk=', 1788892107),
('SNjWGwiCYrusbB0tKom3S72yZ8s2grHlHQbbmOPN', NULL, '66.249.66.71', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'ZXlKcGRpSTZJbGhhVERsck5sQkllalowYmxkeVEySm9VR0ZDT0djOVBTSXNJblpoYkhWbElqb2lOVXREVkVJM1NrOWthRkV6VkdSS05tWlBkR0ZwUjJGaGFXZFBkV1p0VFVaQ2IwOUtNVkJ6YWxacFlVRkhjelZVS3k5V01IWnpNakl3VmtWSlRrMXZNRko2VXpSbVJHWnNURW8xYm1NeFducE5kR2hIYTFKUVlqZFlWMmh2YUdrMVFuWnFZV2RsTDJrdlJ5OUdLelVyZUhaM1JIbEtUbWRLUkU0ek5GVkRhWEJ2THpVMlMwZDVhSGNyV0VwMldtTjNMM0EyY1hKeFZUbEJSRzloYkRSTGNFaGFOVmREVDJkcVN6TnZkakpyTDNKRGNGQXlPRFp6UkRaRWJ6bEVURTlVTUVKak9EQlVXRTlhYVZscVMyOU5jV2R0VFRnemFGSmhWM3B6WjBsSlZGVlBRVFI1U0VVMVVITjNVMWx6Y3poTWJETkpOMUZDTmtwSFoybE5lRnBSYURsbk9VTm1OMDl1TUVaQmJHTjJSa2xPZW1WM01YWnZaWGRpUlc4NFIyTnhlbTlpYnpGdk1rNHhlbVEwUlRGemFuaDRhREppYkhablkxQXJTVlJ4VFd3aUxDSnRZV01pT2lKa1pHWTFZVGMxTURjeVkySTFNVEF5TXpjNVlqRmpabVUzT0dGaFl6ZzNNekkxTm1FMk1HTTVOakE1WlROaU9EUTRabUUwTnpFME1UWTBOV001WW1ZeElpd2lkR0ZuSWpvaUluMD0=', 1788875313),
('VmwFZHglertwfwJbdDyLLwAhLuXFxVWBTXIa3tIQ', NULL, '18.153.79.176', 'Go-http-client/2.0', 'ZXlKcGRpSTZJbWhIYVRkR0syaGhZemcwUkRSNWFuTXhRM3BJVW5jOVBTSXNJblpoYkhWbElqb2lXWGwxVjNwblJTdFZhVFowU0ZsUFpqUlJTa1JxUmpoR1VHWXJZeXM0Tm1KSmNVRmlVa1JtYnpWa05HaFNhalZhU2pWUlNVbFJja2xWYmtSdVIzZzVVbEJCTUhObGRrVTJlRzByWjFwRGVFMVNjRnBhVURsRFdXRkVZa3RMTXk5VWNGbDFZM1ZMUlhsTlNFVlVOMUV4UVZKdmNXcFhORFZXZVdkeE4wRnhURmMxYTNodUt6VklRVWhtYmtjMVoxTnNja1ZYSzNCakswRmlOWHBrVmpnNWJ5OXdNRWN6Y0RaYVl6QjNhMGxPT1ZaS2RtVnhhVklyVFhKYVZEUXlNVkphVW5GSE9IWXlZM05SWmxkcVZGZFljVzEyVjFkdVVsUnNlalZoUmpScFlWSkZRVVZpTldaWEwyRlVlakpWTWtScFNtOVBla1Z0WjNGUFNIVlJjM2g2VEU5RFNXSmlUMlZDTmtzekwyNTJXbFp2Y2tsSFZEUXpOQ3RsVFZGdVUzZG5jVTg0TVU1QlNqUmpVbkpsYnpkM0syMWFMM05JVUU5S2VubEJSRmRuY3pFaUxDSnRZV01pT2lKbE1tWTFOV1V4TmpGaU1qVmtPV1UxWkdFelpHUTVPRE5qTW1VMk5qUXdZbU0wTUdWbVpHTTRORFF4T1RSbVltWTVORFExTjJJeU5HTXlNMk5qTjJVeUlpd2lkR0ZuSWpvaUluMD0=', 1788890919),
('VWJ3q1b99VdqsbkEviURs5uGq4RCrw1RP02AEIcS', NULL, '193.235.141.156', 'Mozilla/5.0 (Android 14; Mobile; rv:123.0) Gecko/123.0 Firefox/123', 'ZXlKcGRpSTZJbXRyUzJSalV6VkxWa0ZFZEhsblozRXZUV0pIVUhjOVBTSXNJblpoYkhWbElqb2lTemRUVFhjMk4zZEtNa1pNTlhWWWQwVTBXVlpRVG5wcGNreEpkR3BIUmpFeVF6SktWbFJWVldGRGJDdEdZVkpCTWpWU1FteEZZbHBITW5Nd05VNHpURThyU0dOWk5FRnZkRTE2ZEVwbE1HTmxRWEJ6WVVSeVN6aGxhazlrYmpRM01HaFVSa3M0TkVSR1RIWlhkRzFYWjBaU2VURnRUMkozVERoSFlrOUlOMVJNY1dobGVGTTNSWGR1TjNCd2JtaGtMM1J0WkdsRmNUUlFiVFZKTWpkWlNEZEVTbXRhTm5aallYTkhWRFZtV21KWmJUWnJNbGRpWWt3MlFtNVpVMDlWVDFRMGMyWTVTbFJRWTBST1ExZHhka0ZrTWtOaFMwVnhRMnhxVWxKYVFrSXhUM2hFU2xOQ1VXRlhWWFp6U1UxRGRuQmtUbEpXT0ROTU0ydHJVMDlIV2sxVGVtcFBkbmR3WWxoME1uaERSekVyZERKUGFFRkljRTVNSzB0SE0zSnFaMFZWUWtOd1EwSTVRVmxYV25GamVVRkxRVFJvTldWclkyVmtkMnBwYTFvaUxDSnRZV01pT2lJM1kyUXhZVEZqTnpFelpEbGxNR1pqTlRWa05UYzJZekUzT0RVd1lXRTBZek13T1RCbFpXRm1NREU1TnpNMU5UTXlNRGxrTmpZNU5USmhZakZsTm1WbUlpd2lkR0ZuSWpvaUluMD0=', 1788886156);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategorias`
--

CREATE TABLE `subcategorias` (
  `id_subcategoria` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `subcategorias`
--

INSERT INTO `subcategorias` (`id_subcategoria`, `nombre`, `descripcion`, `id_categoria`, `id_empresa`, `estado`) VALUES
(1, 'mmm', NULL, 1, 12, '1'),
(2, 'Fideos Largos', NULL, 2, 12, '1'),
(3, 'Fideos Cortos', NULL, 2, 12, '1'),
(4, 'Pastas Instantáneas', NULL, 2, 12, '1'),
(5, 'Pastas Especiales', NULL, 2, 12, '1'),
(6, 'Salsas de Tomate', NULL, 3, 12, '1'),
(7, 'Conservas Vegetales', NULL, 3, 12, '1'),
(8, 'Conservas de Pescado', NULL, 3, 12, '1'),
(9, 'Aceites Comestibles', NULL, 4, 12, '1'),
(10, 'Margarinas', NULL, 4, 12, '1'),
(11, 'Harinas', NULL, 5, 12, '1'),
(12, 'Pre-mezclas', NULL, 5, 12, '1'),
(13, 'Galletas Dulces', NULL, 6, 12, '1'),
(14, 'Galletas Saladas', NULL, 6, 12, '1'),
(15, 'Snacks', NULL, 6, 12, '1'),
(16, 'Néctares', NULL, 7, 12, '1'),
(17, 'Jugos en Polvo', NULL, 7, 12, '1'),
(18, 'Bebidas Hidratantes', NULL, 7, 12, '1'),
(19, 'Jabones de Tocador', NULL, 8, 12, '1'),
(20, 'Detergentes', NULL, 8, 12, '1'),
(21, 'Arroz', NULL, 9, 12, '1'),
(22, 'Azúcar', NULL, 9, 12, '1'),
(23, 'Legumbres', NULL, 9, 12, '1'),
(24, 'SOPA', 'AAAAAA', 10, 0, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `submarcas`
--

CREATE TABLE `submarcas` (
  `id_submarca` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `id_marca` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `submarcas`
--

INSERT INTO `submarcas` (`id_submarca`, `nombre`, `descripcion`, `id_marca`, `id_empresa`, `estado`) VALUES
(1, 'mm', NULL, 1, 12, '1'),
(2, 'Molitalia Clásico', NULL, 2, 12, '1'),
(3, 'Molitalia Premium', NULL, 2, 12, '1'),
(4, 'Don Vittorio Spaghetti', NULL, 3, 12, '1'),
(5, 'Don Vittorio Tallarín', NULL, 3, 12, '1'),
(6, 'Primor Clásico', NULL, 4, 12, '1'),
(7, 'Primor Soflax', NULL, 4, 12, '1'),
(8, 'Sello de Oro Clásico', NULL, 5, 12, '1'),
(9, 'Sello de Oro Light', NULL, 5, 12, '1'),
(10, 'Nutri-V Original', NULL, 6, 12, '1'),
(11, 'Nutri-V Integral', NULL, 6, 12, '1'),
(12, 'Opal Clásico', NULL, 7, 12, '1'),
(13, 'Opal Active', NULL, 7, 12, '1'),
(14, 'Patito Clásico', NULL, 8, 12, '1'),
(15, 'Patito Rellenas', NULL, 8, 12, '1'),
(16, 'Activ Original', NULL, 9, 12, '1'),
(17, 'Activ Frescura', NULL, 9, 12, '1'),
(18, 'CARCOPOLO', 'AAAAAAAA', 10, 0, '1'),
(19, 'MOLITALIA', NULL, 10, 0, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

CREATE TABLE `sucursales` (
  `id_sucursal` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `distrito` varchar(50) DEFAULT NULL,
  `provincia` varchar(50) DEFAULT NULL,
  `departamento` varchar(50) DEFAULT NULL,
  `ubigeo` varchar(50) DEFAULT NULL,
  `cod_sucursal` int(11) DEFAULT NULL,
  `estado` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`id_sucursal`, `empresa_id`, `nombre`, `direccion`, `distrito`, `provincia`, `departamento`, `ubigeo`, `cod_sucursal`, `estado`) VALUES
(1, 12, 'Sucursal 1', '', '', '', '', '', 1, '1'),
(2, 12, 'almacen2', 'MDO GAMBETA PTO 135', NULL, NULL, NULL, NULL, 2, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tamsporte_persona`
--

CREATE TABLE `tamsporte_persona` (
  `tampo_id` int(11) NOT NULL,
  `ruc` varchar(100) DEFAULT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tamsporte_persona`
--

INSERT INTO `tamsporte_persona` (`tampo_id`, `ruc`, `razon_social`, `direccion`) VALUES
(0, '20605571094', 'STORE LINGERIE SOCIEDAD ANONIMA CERRADA', 'JR. CAJAMARCA NRO 435 HUANCAYO CERCADO ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjetas`
--

CREATE TABLE `tarjetas` (
  `id_tarjeta` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `id_banco` int(10) UNSIGNED NOT NULL,
  `id_cuenta_bancaria` int(10) UNSIGNED DEFAULT NULL,
  `tipo` enum('CREDITO','DEBITO') NOT NULL DEFAULT 'DEBITO',
  `marca` enum('VISA','MASTERCARD','AMEX','DINERS') NOT NULL DEFAULT 'VISA',
  `ultimos_4` varchar(4) NOT NULL,
  `titular` varchar(200) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `estado` varchar(2) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tarjetas`
--

INSERT INTO `tarjetas` (`id_tarjeta`, `id_empresa`, `id_banco`, `id_cuenta_bancaria`, `tipo`, `marca`, `ultimos_4`, `titular`, `fecha_vencimiento`, `estado`, `created_at`, `updated_at`) VALUES
(1, 12, 1, 1, 'DEBITO', 'VISA', '6321', 'victor', '2028-06-30', '1', '2026-07-10 05:58:03', '2026-07-10 05:58:03'),
(2, 12, 3, 4, 'DEBITO', 'VISA', '6325', 'ad ada dawd', '2026-12-31', '1', '2026-07-22 19:30:49', '2026-07-22 19:30:49'),
(3, 0, 4, 5, 'DEBITO', 'VISA', '3453', 'roma', '2026-10-31', '1', '2026-09-08 20:41:06', '2026-09-08 20:41:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_pago`
--

CREATE TABLE `tipo_pago` (
  `tipo_pago_id` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tipo_pago`
--

INSERT INTO `tipo_pago` (`tipo_pago_id`, `nombre`) VALUES
(1, 'Contado'),
(2, 'Credito');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tms_conductores`
--

CREATE TABLE `tms_conductores` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `sucursal` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL,
  `nombres` varchar(120) NOT NULL,
  `documento` varchar(15) DEFAULT NULL,
  `licencia` varchar(30) DEFAULT NULL,
  `licencia_categoria` varchar(10) DEFAULT NULL,
  `licencia_vence` date DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tms_conductores`
--

INSERT INTO `tms_conductores` (`id`, `id_empresa`, `sucursal`, `id_usuario`, `nombres`, `documento`, `licencia`, `licencia_categoria`, `licencia_vence`, `telefono`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 'Carlos Mendoza López', '12345678', 'Q12345678', 'A-II', NULL, '987654321', 1, '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(2, 1, 1, NULL, 'María García Torres', '23456789', 'Q23456789', 'A-I', NULL, '976543210', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(3, 1, 1, NULL, 'Juan Pérez Castillo', '34567890', 'Q34567890', 'A-III', NULL, '965432109', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(4, 1, 1, NULL, 'Roberto Huamán Quispe', '45678901', 'Q45678901', 'A-II', NULL, '954321098', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(5, 1, 1, NULL, 'Luis Fernández Rojas', '56789012', 'Q56789012', 'A-I', NULL, '943210987', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(6, 1, 1, NULL, 'Pedro Gutiérrez Silva', '67890123', 'Q67890123', 'A-II', NULL, '932109876', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(7, 1, 1, NULL, 'Jorge Ramírez Paredes', '78901234', 'Q78901234', 'A-III', NULL, '921098765', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(8, 1, 1, NULL, 'Diego Sánchez Vargas', '89012345', 'Q89012345', 'A-I', NULL, '910987654', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(9, 1, 1, NULL, 'Andrés Navarro Flores', '90123456', 'Q90123456', 'A-II', NULL, '909876543', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(10, 1, 1, NULL, 'César Delgado Ríos', '01234567', 'Q01234567', 'A-I', NULL, '898765432', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(11, 12, 1, NULL, 'Alejandro Torres Vega', '11223344', 'T11223344', 'A-II', '2027-06-15', '987654001', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(12, 12, 1, NULL, 'Fernanda Castillo Ríos', '22334455', 'T22334455', 'A-I', '2027-08-20', '987654002', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(13, 12, 1, NULL, 'Ricardo Paredes Lozano', '33445566', 'T33445566', 'A-III', '2026-12-10', '987654003', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(14, 12, 1, NULL, 'Camila Guerrero Mendoza', '44556677', 'T44556677', 'A-II', '2028-01-05', '987654004', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(15, 12, 1, NULL, 'Esteban Flores Morales', '55667788', 'T55667788', 'A-I', '2027-03-22', '987654005', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(16, 12, 1, NULL, 'Valentina Herrera Pizarro', '66778899', 'T66778899', 'A-II', '2027-11-30', '987654006', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(17, 12, 1, NULL, 'Migángel Rojas Córdova', '77889900', 'T77889900', 'A-III', '2026-09-18', '987654007', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(18, 12, 1, NULL, 'Gabriela Salazar Hence', '88990011', 'T88990011', 'A-I', '2028-04-14', '987654008', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(19, 12, 1, NULL, 'David Quispe Mamani', '99001122', 'T99001122', 'A-II', '2027-07-07', '987654009', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(20, 12, 1, NULL, 'Sofía Beltrán Cardenas', '00112233', 'T00112233', 'A-I', '2028-02-28', '987654010', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(21, 12, 1, NULL, 'Paolo Hurtado Ávila', '10293847', 'P10293847', 'A-II', '2026-10-05', '987654011', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(22, 12, 1, NULL, 'Lucía Montenegro Pacheco', '56473829', 'L56473829', 'A-III', '2027-05-12', '987654012', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(23, 12, 1, NULL, 'Hernán Bravo Cuestas', '37485920', 'H37485920', 'A-I', '2027-09-25', '987654013', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(24, 12, 1, NULL, 'Patricia Villanueva Soto', '84756291', 'P84756291', 'A-II', '2028-06-01', '987654014', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(25, 12, 1, NULL, 'Gonzalo Tapia Chávez', '19283746', 'G19283746', 'A-III', '2026-08-19', '987654015', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(26, 12, 1, NULL, 'CONDUCTOR PRUEBA INTEGRAL', '99887755', 'Q99887755', 'A-III', '2027-07-10', '999000111', 1, '2026-07-10 05:43:05', '2026-07-10 05:43:05'),
(27, 12, 1, NULL, 'Luis Alberto lazo Gomes', '10384196', 'A10384196', 'APRO13', '2026-12-07', '983507932', 1, '2026-09-08 19:45:25', '2026-09-08 19:45:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tms_despachos`
--

CREATE TABLE `tms_despachos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `sucursal` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `fecha_reparto` date NOT NULL,
  `id_ruta` int(10) UNSIGNED NOT NULL,
  `id_vehiculo` int(10) UNSIGNED NOT NULL,
  `id_conductor` int(10) UNSIGNED NOT NULL,
  `peso_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estado` varchar(15) NOT NULL DEFAULT 'PLANIFICADO' COMMENT 'PLANIFICADO|CARGADO|EN_RUTA|CERRADO|ANULADO',
  `observaciones` varchar(255) DEFAULT NULL,
  `id_usuario_creacion` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tms_despachos`
--

INSERT INTO `tms_despachos` (`id`, `id_empresa`, `sucursal`, `codigo`, `fecha_reparto`, `id_ruta`, `id_vehiculo`, `id_conductor`, `peso_total`, `estado`, `observaciones`, `id_usuario_creacion`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'DESP-001', '2026-07-01', 1, 1, 1, 749.98, 'CERRADO', 'Primer despacho del mes', 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(2, 1, 1, 'DESP-002', '2026-07-02', 2, 2, 2, 920.93, 'CERRADO', NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(3, 1, 1, 'DESP-003', '2026-07-03', 3, 3, 3, 209.26, 'EN_RUTA', 'Salida 6:00 am', 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(4, 1, 1, 'DESP-004', '2026-07-03', 4, 4, 4, 1124.24, 'CARGADO', 'Esperando confirmación', 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(5, 1, 1, 'DESP-005', '2026-07-04', 5, 5, 5, 987.05, 'PLANIFICADO', NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(6, 1, 1, 'DESP-006', '2026-07-04', 1, 6, 6, 1010.72, 'PLANIFICADO', 'Requiere refrigeración', 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(7, 1, 1, 'DESP-007', '2026-07-05', 2, 7, 7, 702.33, 'PLANIFICADO', NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(8, 1, 1, 'DESP-008', '2026-07-05', 3, 8, 8, 230.73, 'PLANIFICADO', NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(9, 12, 1, 'DSP-000009', '2026-07-11', 6, 11, 26, 2420.10, 'PLANIFICADO', 'adadadawdadawd', 107, '2026-07-10 07:27:45', '2026-07-10 07:27:45'),
(10, 12, 1, 'DSP-000010', '2026-07-10', 7, 12, 11, 520.50, 'EN_RUTA', 'a dad adawd', 107, '2026-07-10 08:34:46', '2026-07-10 09:00:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tms_despacho_costos`
--

CREATE TABLE `tms_despacho_costos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_despacho` int(10) UNSIGNED NOT NULL,
  `concepto` varchar(120) NOT NULL COMMENT 'COMBUSTIBLE|PEAJE|VIATICOS|OTRO o texto libre',
  `monto` decimal(12,2) NOT NULL,
  `id_caja` int(10) UNSIGNED DEFAULT NULL COMMENT 'Caja a la que se cargó el egreso, si aplica',
  `id_movimiento_caja` int(10) UNSIGNED DEFAULT NULL,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tms_despacho_costos`
--

INSERT INTO `tms_despacho_costos` (`id`, `id_despacho`, `concepto`, `monto`, `id_caja`, `id_movimiento_caja`, `id_usuario`, `created_at`, `updated_at`) VALUES
(1, 1, 'COMBUSTIBLE', 97.99, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(2, 1, 'PEAJE', 35.13, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(3, 2, 'COMBUSTIBLE', 26.56, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(4, 2, 'PEAJE', 128.28, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(5, 3, 'COMBUSTIBLE', 92.43, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(6, 4, 'COMBUSTIBLE', 142.29, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(7, 4, 'PEAJE', 140.03, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(8, 5, 'COMBUSTIBLE', 87.09, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(9, 5, 'PEAJE', 158.46, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(10, 6, 'COMBUSTIBLE', 79.69, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(11, 6, 'PEAJE', 68.29, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(12, 7, 'COMBUSTIBLE', 148.42, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(13, 7, 'PEAJE', 173.01, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(14, 8, 'COMBUSTIBLE', 22.31, NULL, NULL, 40, '2026-07-09 18:27:42', '2026-07-09 18:27:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tms_despacho_pedidos`
--

CREATE TABLE `tms_despacho_pedidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_despacho` int(10) UNSIGNED NOT NULL,
  `id_cotizacion` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `id_mercado` int(10) UNSIGNED DEFAULT NULL,
  `peso` decimal(12,2) NOT NULL DEFAULT 0.00,
  `monto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `orden` int(11) NOT NULL DEFAULT 0,
  `estado_entrega` varchar(15) NOT NULL DEFAULT 'PENDIENTE' COMMENT 'PENDIENTE|ENTREGADO|RECHAZADO|PARCIAL',
  `motivo_rechazo` varchar(255) DEFAULT NULL,
  `hora_entrega` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tms_despacho_pedidos`
--

INSERT INTO `tms_despacho_pedidos` (`id`, `id_despacho`, `id_cotizacion`, `id_cliente`, `id_mercado`, `peso`, `monto`, `orden`, `estado_entrega`, `motivo_rechazo`, `hora_entrega`) VALUES
(1, 1, 583, 91, 5, 249.99, 145.28, 1, 'ENTREGADO', NULL, '2026-07-08 14:27:42'),
(2, 1, 296, 6, 6, 249.99, 448.68, 2, 'ENTREGADO', NULL, '2026-07-06 14:27:42'),
(3, 1, 424, 34, 18, 249.99, 410.47, 3, 'ENTREGADO', NULL, '2026-07-08 14:27:42'),
(4, 2, 751, 73, 8, 306.98, 179.79, 1, 'ENTREGADO', NULL, '2026-07-06 14:27:42'),
(5, 2, 948, 3, 9, 306.98, 222.45, 2, 'ENTREGADO', NULL, '2026-07-08 14:27:42'),
(6, 2, 219, 29, 11, 306.98, 419.95, 3, 'ENTREGADO', NULL, '2026-07-08 14:27:42'),
(7, 3, 800, 90, 10, 52.32, 323.63, 1, 'PENDIENTE', NULL, NULL),
(8, 3, 392, 17, 7, 52.32, 202.37, 2, 'PENDIENTE', NULL, NULL),
(9, 3, 518, 22, 14, 52.32, 456.62, 3, 'PENDIENTE', NULL, NULL),
(10, 3, 159, 83, 10, 52.32, 116.15, 4, 'PENDIENTE', NULL, NULL),
(11, 4, 753, 20, 13, 281.06, 119.44, 1, 'PENDIENTE', NULL, NULL),
(12, 4, 199, 86, 15, 281.06, 290.75, 2, 'PENDIENTE', NULL, NULL),
(13, 4, 947, 16, 16, 281.06, 218.42, 3, 'PENDIENTE', NULL, NULL),
(14, 4, 340, 3, 13, 281.06, 339.28, 4, 'PENDIENTE', NULL, NULL),
(15, 5, 791, 37, 17, 493.53, 99.86, 1, 'PENDIENTE', NULL, NULL),
(16, 5, 486, 11, 12, 493.53, 277.70, 2, 'PENDIENTE', NULL, NULL),
(17, 6, 346, 96, 5, 505.36, 498.18, 1, 'PENDIENTE', NULL, NULL),
(18, 6, 795, 7, 6, 505.36, 397.38, 2, 'PENDIENTE', NULL, NULL),
(19, 7, 657, 35, 8, 175.58, 110.45, 1, 'PENDIENTE', NULL, NULL),
(20, 7, 375, 82, 9, 175.58, 110.41, 2, 'PENDIENTE', NULL, NULL),
(21, 7, 527, 1, 11, 175.58, 355.33, 3, 'PENDIENTE', NULL, NULL),
(22, 7, 654, 3, 19, 175.58, 261.63, 4, 'PENDIENTE', NULL, NULL),
(23, 8, 133, 97, 10, 57.68, 234.96, 1, 'PENDIENTE', NULL, NULL),
(24, 8, 449, 63, 7, 57.68, 451.94, 2, 'PENDIENTE', NULL, NULL),
(25, 8, 164, 6, 14, 57.68, 307.58, 3, 'PENDIENTE', NULL, NULL),
(26, 8, 254, 37, 10, 57.68, 208.19, 4, 'PENDIENTE', NULL, NULL),
(27, 9, 51472, 2518, 23, 21.00, 84.00, 1, 'PENDIENTE', NULL, NULL),
(28, 9, 51473, 2519, 23, 92.40, 649.50, 2, 'PENDIENTE', NULL, NULL),
(29, 9, 51474, 2520, 23, 262.90, 2109.50, 3, 'PENDIENTE', NULL, NULL),
(30, 9, 51475, 2521, 23, 5.50, 32.50, 4, 'PENDIENTE', NULL, NULL),
(31, 9, 51476, 2522, 23, 216.90, 1386.00, 5, 'PENDIENTE', NULL, NULL),
(32, 9, 51477, 2523, 23, 176.70, 923.00, 6, 'PENDIENTE', NULL, NULL),
(33, 9, 51478, 2524, 23, 41.60, 196.00, 7, 'PENDIENTE', NULL, NULL),
(34, 9, 51479, 2525, 23, 118.50, 883.50, 8, 'PENDIENTE', NULL, NULL),
(35, 9, 51480, 2526, 23, 166.10, 1031.00, 9, 'PENDIENTE', NULL, NULL),
(36, 9, 51481, 2527, 23, 40.80, 336.00, 10, 'PENDIENTE', NULL, NULL),
(37, 9, 51482, 2528, 23, 62.70, 273.00, 11, 'PENDIENTE', NULL, NULL),
(38, 9, 51483, 2529, 23, 121.40, 835.00, 12, 'PENDIENTE', NULL, NULL),
(39, 9, 51484, 2530, 23, 64.00, 480.00, 13, 'PENDIENTE', NULL, NULL),
(40, 9, 51485, 2531, 23, 190.20, 1551.00, 14, 'PENDIENTE', NULL, NULL),
(41, 9, 51486, 2532, 23, 268.60, 1694.00, 15, 'PENDIENTE', NULL, NULL),
(42, 9, 51487, 2533, 23, 84.00, 336.00, 16, 'PENDIENTE', NULL, NULL),
(43, 9, 51488, 2534, 23, 85.20, 508.50, 17, 'PENDIENTE', NULL, NULL),
(44, 9, 51489, 2535, 23, 275.20, 2162.00, 18, 'PENDIENTE', NULL, NULL),
(45, 9, 51490, 2536, 23, 2.20, 13.00, 19, 'PENDIENTE', NULL, NULL),
(46, 9, 51491, 2537, 23, 124.20, 756.00, 20, 'PENDIENTE', NULL, NULL),
(47, 10, 51492, 2538, 24, 510.50, 1292.00, 1, 'PENDIENTE', NULL, NULL),
(48, 10, 51493, 2516, 24, 10.00, 1800.00, 2, 'PENDIENTE', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tms_mercados`
--

CREATE TABLE `tms_mercados` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `sucursal` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `direccion` varchar(245) NOT NULL,
  `referencia` varchar(245) DEFAULT NULL,
  `distrito` varchar(120) DEFAULT NULL,
  `ubigeo` varchar(6) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tms_mercados`
--

INSERT INTO `tms_mercados` (`id`, `id_empresa`, `sucursal`, `nombre`, `direccion`, `referencia`, `distrito`, `ubigeo`, `telefono`, `estado`, `created_at`, `updated_at`) VALUES
(1, 12, 1, 'Mercado El Progreso I', 'Av. El Progreso s/n', 'Nombre reconstruido desde las direcciones de sus clientes', 'San Juan de Lurigancho', '150132', NULL, 1, '2026-07-10 05:03:39', '2026-07-10 05:03:39'),
(2, 12, 1, 'Mercado El Progreso II', 'Av. El Progreso cdra. 11', 'Nombre reconstruido desde las direcciones de sus clientes', 'San Juan de Lurigancho', '150132', NULL, 1, '2026-07-10 05:03:39', '2026-07-10 05:03:39'),
(3, 12, 1, 'Mercado Centro Cívico', 'Av. José Granda s/n', 'Nombre reconstruido desde las direcciones de sus clientes', 'San Juan de Lurigancho', '150132', NULL, 1, '2026-07-10 05:03:39', '2026-07-10 05:03:39'),
(4, 12, 1, 'Mercado 4', '(por definir)', NULL, NULL, NULL, NULL, 1, '2026-07-08 06:53:14', '2026-07-08 06:53:14'),
(5, 1, 1, 'Mercado Central', 'Jr. Junín 750', NULL, 'Cercado de Lima', NULL, '01-4281234', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(6, 1, 1, 'Mercado San José', 'Av. Argentina 3200', NULL, 'Cercado de Lima', NULL, '01-3367890', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(7, 1, 1, 'Mercado La Victoria', 'Av. 28 de Julio 1500', NULL, 'La Victoria', NULL, '01-2254567', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(8, 1, 1, 'Mercado de Surquillo', 'Av. Paseo de la República 4500', NULL, 'Surquillo', NULL, '01-4412345', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(9, 1, 1, 'Mercado San Martín', 'Av. San Martín 800', NULL, 'Miraflores', NULL, '01-4456789', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(10, 1, 1, 'Mercado Mayorista', 'Carretera Central Km 5', NULL, 'Santa Anita', NULL, '01-3623456', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(11, 1, 1, 'Mercado Ciudad de Dios', 'Av. Manuel Prado s/n', NULL, 'San Juan de Miraflores', NULL, '01-2789012', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(12, 1, 1, 'Mercado Unicachi', 'Av. Separadora Industrial 2500', NULL, 'Villa El Salvador', NULL, '01-2895678', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(13, 1, 1, 'Mercado Las Gardenias', 'Av. Las Gardenias 500', NULL, 'Los Olivos', NULL, '01-5213456', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(14, 1, 1, 'Mercado Zonal Palomino', 'Av. Próceres de la Independencia 2000', NULL, 'San Juan de Lurigancho', NULL, '01-3769012', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(15, 1, 1, 'Mercado Túpac Amaru', 'Av. Túpac Amaru 1800', NULL, 'Independencia', NULL, '01-5245678', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(16, 1, 1, 'Mercado El Bosque', 'Av. El Bosque 1200', NULL, 'Comas', NULL, '01-5357890', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(17, 1, 1, 'Mercado San Felipe', 'Av. San Felipe 400', NULL, 'Jesús María', NULL, '01-4631234', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(18, 1, 1, 'Mercado Tres Cabezas', 'Av. Venezuela 5000', NULL, 'Cercado de Lima', NULL, '01-3305678', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(19, 1, 1, 'Mercado Municipal de Barranco', 'Av. San Martín 150', NULL, 'Barranco', NULL, '01-2478901', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(20, 12, 1, 'Mercado Corazón de Jesús', 'Av. Corazón de Jesús s/n', 'Nombre reconstruido desde las direcciones de sus clientes', 'San Juan de Lurigancho', '150132', NULL, 1, '2026-07-10 05:03:39', '2026-07-10 05:03:39'),
(21, 12, 1, 'Mercado Central SJL', 'Av. Próceres de la Independencia cdra. 15', 'Nombre reconstruido desde las direcciones de sus clientes', 'San Juan de Lurigancho', '150132', NULL, 1, '2026-07-10 05:03:39', '2026-07-10 05:03:39'),
(22, 12, 1, 'Mercado 19 de Enero', 'Av. 19 de Enero s/n', 'Nombre reconstruido desde las direcciones de sus clientes', 'San Juan de Lurigancho', '150132', NULL, 1, '2026-07-10 05:03:39', '2026-07-10 05:03:39'),
(23, 12, 1, 'MERCADO TEST DESPACHO', 'AV. DE PRUEBA 123', NULL, 'AYACUCHO', NULL, NULL, 1, '2026-07-10 05:43:05', '2026-07-10 05:43:05'),
(24, 12, 1, 'mercado 1 prueba', 'PSJ.INCA ROCA MZ. 131 LT.33', 'PSJ.INCA ROCA MZ. 131 LT.33', 'LIMA', '150101', '92670321', 1, '2026-07-10 07:35:49', '2026-07-10 07:35:49'),
(25, 0, 0, 'unicachi1', 'AV. Metropolitana 2450 ', NULL, 'COMAS', '150110', '5362082', 1, '2026-09-08 20:59:07', '2026-09-08 20:59:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tms_rutas`
--

CREATE TABLE `tms_rutas` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `sucursal` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` varchar(245) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tms_rutas`
--

INSERT INTO `tms_rutas` (`id`, `id_empresa`, `sucursal`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Ruta Centro', 'Mercados del centro de Lima', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(2, 1, 1, 'Ruta Sur', 'Mercados de la zona sur', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(3, 1, 1, 'Ruta Este', 'Mercados de la zona este', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(4, 1, 1, 'Ruta Norte', 'Mercados de la zona norte', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(5, 1, 1, 'Ruta San Felipe', 'Cobertura Jesús María y alrededores', 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(6, 12, 1, 'RUTA TEST DESPACHO', 'Ruta de prueba para el flujo integral', 1, '2026-07-10 05:43:05', '2026-07-10 05:43:05'),
(7, 12, 1, 'Ruta Sabado', 'Ruta Sabado', 1, '2026-07-10 07:36:24', '2026-07-10 07:36:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tms_ruta_puntos`
--

CREATE TABLE `tms_ruta_puntos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_ruta` int(10) UNSIGNED NOT NULL,
  `tipo` varchar(10) NOT NULL COMMENT 'MERCADO|TIENDA',
  `id_mercado` int(10) UNSIGNED DEFAULT NULL,
  `id_cliente` int(10) UNSIGNED DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tms_ruta_puntos`
--

INSERT INTO `tms_ruta_puntos` (`id`, `id_ruta`, `tipo`, `id_mercado`, `id_cliente`, `orden`) VALUES
(1, 1, 'MERCADO', 5, NULL, 1),
(2, 1, 'MERCADO', 6, NULL, 2),
(3, 1, 'MERCADO', 18, NULL, 3),
(4, 2, 'MERCADO', 8, NULL, 1),
(5, 2, 'MERCADO', 9, NULL, 2),
(6, 2, 'MERCADO', 11, NULL, 3),
(7, 2, 'MERCADO', 19, NULL, 4),
(8, 3, 'MERCADO', 10, NULL, 1),
(9, 3, 'MERCADO', 7, NULL, 2),
(10, 3, 'MERCADO', 14, NULL, 3),
(11, 4, 'MERCADO', 13, NULL, 1),
(12, 4, 'MERCADO', 15, NULL, 2),
(13, 4, 'MERCADO', 16, NULL, 3),
(14, 5, 'MERCADO', 17, NULL, 1),
(15, 5, 'MERCADO', 12, NULL, 2),
(16, 6, 'MERCADO', 23, NULL, 1),
(17, 7, 'MERCADO', 24, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tms_tipos_vehiculo`
--

CREATE TABLE `tms_tipos_vehiculo` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tms_tipos_vehiculo`
--

INSERT INTO `tms_tipos_vehiculo` (`id`, `id_empresa`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 'CAMIONETA', 1, '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(2, 1, 'FURGONETA', 1, '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(3, 1, 'CAMION', 1, '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(4, 1, 'MOTO', 1, '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(5, 1, 'OTRO', 1, '2026-07-09 18:27:41', '2026-07-09 18:27:41'),
(6, 12, 'CAMIONETA', 1, '2026-07-10 07:32:52', '2026-07-10 07:32:52'),
(7, 12, 'FURGONETA', 1, '2026-07-10 07:32:52', '2026-07-10 07:32:52'),
(8, 12, 'CAMION', 1, '2026-07-10 07:32:52', '2026-07-10 07:32:52'),
(9, 12, 'MOTO', 1, '2026-07-10 07:32:52', '2026-07-10 07:32:52'),
(10, 12, 'OTRO', 1, '2026-07-10 07:32:52', '2026-07-10 07:32:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tms_vehiculos`
--

CREATE TABLE `tms_vehiculos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `sucursal` int(10) UNSIGNED NOT NULL,
  `placa` varchar(15) NOT NULL,
  `id_tipo` int(10) UNSIGNED DEFAULT NULL,
  `marca` varchar(60) DEFAULT NULL,
  `modelo` varchar(60) DEFAULT NULL,
  `anio` smallint(6) DEFAULT NULL,
  `capacidad_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tara_kg` decimal(10,2) DEFAULT NULL,
  `largo_m` decimal(6,2) DEFAULT NULL,
  `ancho_m` decimal(6,2) DEFAULT NULL,
  `alto_m` decimal(6,2) DEFAULT NULL,
  `capacidad_m3` decimal(8,2) DEFAULT NULL,
  `soat_vence` date DEFAULT NULL,
  `rev_tecnica_vence` date DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `tms_vehiculos`
--

INSERT INTO `tms_vehiculos` (`id`, `id_empresa`, `sucursal`, `placa`, `id_tipo`, `marca`, `modelo`, `anio`, `capacidad_kg`, `tara_kg`, `largo_m`, `ancho_m`, `alto_m`, `capacidad_m3`, `soat_vence`, `rev_tecnica_vence`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'ABC-123', 1, 'Toyota', 'Hilux', 2020, 1000.00, 1800.00, NULL, NULL, NULL, NULL, '2026-12-31', NULL, 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(2, 1, 1, 'DEF-456', 1, 'Nissan', 'NP300', 2021, 1200.00, 1750.00, NULL, NULL, NULL, NULL, '2026-11-30', NULL, 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(3, 1, 1, 'GHI-789', 2, 'Hyundai', 'H-1', 2019, 800.00, 1650.00, NULL, NULL, NULL, NULL, '2026-10-15', NULL, 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(4, 1, 1, 'JKL-012', 2, 'Mercedes', 'Sprinter', 2022, 900.00, 1900.00, NULL, NULL, NULL, NULL, '2027-01-20', NULL, 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(5, 1, 1, 'MNO-345', 3, 'Volvo', 'FH 440', 2020, 8000.00, 5000.00, NULL, NULL, NULL, NULL, '2026-08-05', NULL, 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(6, 1, 1, 'PQR-678', 3, 'Scania', 'G410', 2021, 10000.00, 5500.00, NULL, NULL, NULL, NULL, '2026-09-12', NULL, 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(7, 1, 1, 'STU-901', 3, 'Freightliner', 'M2 106', 2018, 12000.00, 6000.00, NULL, NULL, NULL, NULL, '2026-07-30', NULL, 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(8, 1, 1, 'VWX-234', 4, 'Honda', 'XR 150', 2023, 50.00, 130.00, NULL, NULL, NULL, NULL, '2027-03-15', NULL, 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(9, 1, 1, 'YZA-567', 4, 'Yamaha', 'FZ 250', 2022, 80.00, 140.00, NULL, NULL, NULL, NULL, '2027-02-28', NULL, 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(10, 1, 1, 'BCD-890', 5, 'Mitsubishi', 'L200', 2020, 500.00, 1200.00, NULL, NULL, NULL, NULL, '2026-12-01', NULL, 1, '2026-07-09 18:27:42', '2026-07-09 18:27:42'),
(11, 12, 1, 'TST-999', 3, 'Volvo', 'FH 440 (PRUEBA)', 2022, 8000.00, 5000.00, NULL, NULL, NULL, NULL, '2027-07-10', '2027-07-10', 1, '2026-07-10 05:43:05', '2026-07-10 05:43:05'),
(12, 12, 1, 'XX122', 8, 'nisan', 'nisan2', 2000, 10000.00, 8000.00, 3.00, 3.00, 5.00, NULL, '2030-01-06', '2026-07-30', 1, '2026-07-10 07:34:20', '2026-07-10 07:34:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transferencias_fondo`
--

CREATE TABLE `transferencias_fondo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_caja_origen` bigint(20) UNSIGNED NOT NULL,
  `id_caja_destino` bigint(20) UNSIGNED NOT NULL,
  `id_usuario_asigna` int(10) UNSIGNED NOT NULL,
  `id_usuario_cajero` int(10) UNSIGNED NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `monto_contado` decimal(12,2) DEFAULT NULL,
  `estado` varchar(15) NOT NULL DEFAULT 'ASIGNADA',
  `discrepancia_estado` varchar(15) DEFAULT NULL,
  `discrepancia_resolucion` varchar(20) DEFAULT NULL,
  `id_usuario_resuelve` int(10) UNSIGNED DEFAULT NULL,
  `id_movimiento_egreso` bigint(20) UNSIGNED DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `transferencias_fondo`
--

INSERT INTO `transferencias_fondo` (`id`, `id_caja_origen`, `id_caja_destino`, `id_usuario_asigna`, `id_usuario_cajero`, `monto`, `monto_contado`, `estado`, `discrepancia_estado`, `discrepancia_resolucion`, `id_usuario_resuelve`, `id_movimiento_egreso`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 107, 111, 500.00, NULL, 'ASIGNADA', NULL, NULL, NULL, 22, 'adw adadawd', '2026-07-28 03:46:13', '2026-07-28 03:46:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traslados`
--

CREATE TABLE `traslados` (
  `id_traslado` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `almacen_origen` varchar(50) NOT NULL,
  `almacen_destino` varchar(50) NOT NULL,
  `fecha` datetime NOT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `estado` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `traslados`
--

INSERT INTO `traslados` (`id_traslado`, `id_empresa`, `almacen_origen`, `almacen_destino`, `fecha`, `observacion`, `id_usuario`, `estado`) VALUES
(1, 12, '1', '31564165', '2026-07-10 17:03:49', 'adadadadaw', 107, '1'),
(2, 12, '1', '2', '2026-07-22 15:24:07', 'hbsssfj s fjshfjsf jsfsfs', 111, '1'),
(3, 0, '80105', 'AL2', '2026-09-08 17:49:53', 'aaaaaaaaaa', 115, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traslado_detalle`
--

CREATE TABLE `traslado_detalle` (
  `id_detalle` int(10) UNSIGNED NOT NULL,
  `id_traslado` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `costo` decimal(12,4) DEFAULT NULL,
  `stock_ant_origen` int(11) NOT NULL DEFAULT 0,
  `stock_nuevo_origen` int(11) NOT NULL DEFAULT 0,
  `stock_ant_destino` int(11) NOT NULL DEFAULT 0,
  `stock_nuevo_destino` int(11) NOT NULL DEFAULT 0,
  `estado` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `traslado_detalle`
--

INSERT INTO `traslado_detalle` (`id_detalle`, `id_traslado`, `id_producto`, `cantidad`, `costo`, `stock_ant_origen`, `stock_nuevo_origen`, `stock_ant_destino`, `stock_nuevo_destino`, `estado`) VALUES
(1, 1, 412, 300, 120.0000, 451, 151, 0, 300, '1'),
(2, 1, 415, 250, 19.0000, 1000, 750, 0, 250, '1'),
(3, 2, 416, 50, 78.0000, 90, 40, 0, 50, '1'),
(4, 2, 415, 50, 19.0000, 750, 700, 0, 50, '1'),
(5, 3, 434, 5, 25.6100, 50, 45, 0, 5, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubigeo_inei`
--

CREATE TABLE `ubigeo_inei` (
  `id_ubigeo` int(11) NOT NULL,
  `departamento` varchar(2) NOT NULL,
  `provincia` varchar(2) NOT NULL,
  `distrito` varchar(2) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `ubigeo_inei`
--

INSERT INTO `ubigeo_inei` (`id_ubigeo`, `departamento`, `provincia`, `distrito`, `nombre`) VALUES
(1, '01', '00', '00', 'AMAZONAS'),
(2, '01', '01', '00', 'CHACHAPOYAS'),
(3, '01', '01', '01', 'CHACHAPOYAS'),
(4, '01', '01', '02', 'ASUNCION'),
(5, '01', '01', '03', 'BALSAS'),
(6, '01', '01', '04', 'CHETO'),
(7, '01', '01', '05', 'CHILIQUIN'),
(8, '01', '01', '06', 'CHUQUIBAMBA'),
(9, '01', '01', '07', 'GRANADA'),
(10, '01', '01', '08', 'HUANCAS'),
(11, '01', '01', '09', 'LA JALCA'),
(12, '01', '01', '10', 'LEIMEBAMBA'),
(13, '01', '01', '11', 'LEVANTO'),
(14, '01', '01', '12', 'MAGDALENA'),
(15, '01', '01', '13', 'MARISCAL CASTILLA'),
(16, '01', '01', '14', 'MOLINOPAMPA'),
(17, '01', '01', '15', 'MONTEVIDEO'),
(18, '01', '01', '16', 'OLLEROS'),
(19, '01', '01', '17', 'QUINJALCA'),
(20, '01', '01', '18', 'SAN FRANCISCO DE DAGUAS'),
(21, '01', '01', '19', 'SAN ISIDRO DE MAINO'),
(22, '01', '01', '20', 'SOLOCO'),
(23, '01', '01', '21', 'SONCHE'),
(24, '01', '02', '00', 'BAGUA'),
(25, '01', '02', '01', 'BAGUA'),
(26, '01', '02', '02', 'ARAMANGO'),
(27, '01', '02', '03', 'COPALLIN'),
(28, '01', '02', '04', 'EL PARCO'),
(29, '01', '02', '05', 'IMAZA'),
(30, '01', '02', '06', 'LA PECA'),
(31, '01', '03', '00', 'BONGARA'),
(32, '01', '03', '01', 'JUMBILLA'),
(33, '01', '03', '02', 'CHISQUILLA'),
(34, '01', '03', '03', 'CHURUJA'),
(35, '01', '03', '04', 'COROSHA'),
(36, '01', '03', '05', 'CUISPES'),
(37, '01', '03', '06', 'FLORIDA'),
(38, '01', '03', '07', 'JAZÁN'),
(39, '01', '03', '08', 'RECTA'),
(40, '01', '03', '09', 'SAN CARLOS'),
(41, '01', '03', '10', 'SHIPASBAMBA'),
(42, '01', '03', '11', 'VALERA'),
(43, '01', '03', '12', 'YAMBRASBAMBA'),
(44, '01', '04', '00', 'CONDORCANQUI'),
(45, '01', '04', '01', 'NIEVA'),
(46, '01', '04', '02', 'EL CENEPA'),
(47, '01', '04', '03', 'RIO SANTIAGO'),
(48, '01', '05', '00', 'LUYA'),
(49, '01', '05', '01', 'LAMUD'),
(50, '01', '05', '02', 'CAMPORREDONDO'),
(51, '01', '05', '03', 'COCABAMBA'),
(52, '01', '05', '04', 'COLCAMAR'),
(53, '01', '05', '05', 'CONILA'),
(54, '01', '05', '06', 'INGUILPATA'),
(55, '01', '05', '07', 'LONGUITA'),
(56, '01', '05', '08', 'LONYA CHICO'),
(57, '01', '05', '09', 'LUYA'),
(58, '01', '05', '10', 'LUYA VIEJO'),
(59, '01', '05', '11', 'MARIA'),
(60, '01', '05', '12', 'OCALLI'),
(61, '01', '05', '13', 'OCUMAL'),
(62, '01', '05', '14', 'PISUQUIA'),
(63, '01', '05', '15', 'PROVIDENCIA'),
(64, '01', '05', '16', 'SAN CRISTOBAL'),
(65, '01', '05', '17', 'SAN FRANCISCO DEL YESO'),
(66, '01', '05', '18', 'SAN JERONIMO'),
(67, '01', '05', '19', 'SAN JUAN DE LOPECANCHA'),
(68, '01', '05', '20', 'SANTA CATALINA'),
(69, '01', '05', '21', 'SANTO TOMAS'),
(70, '01', '05', '22', 'TINGO'),
(71, '01', '05', '23', 'TRITA'),
(72, '01', '06', '00', 'RODRIGUEZ DE MENDOZA'),
(73, '01', '06', '01', 'SAN NICOLAS'),
(74, '01', '06', '02', 'CHIRIMOTO'),
(75, '01', '06', '03', 'COCHAMAL'),
(76, '01', '06', '04', 'HUAMBO'),
(77, '01', '06', '05', 'LIMABAMBA'),
(78, '01', '06', '06', 'LONGAR'),
(79, '01', '06', '07', 'MARISCAL BENAVIDES'),
(80, '01', '06', '08', 'MILPUC'),
(81, '01', '06', '09', 'OMIA'),
(82, '01', '06', '10', 'SANTA ROSA'),
(83, '01', '06', '11', 'TOTORA'),
(84, '01', '06', '12', 'VISTA ALEGRE'),
(85, '01', '07', '00', 'UTCUBAMBA'),
(86, '01', '07', '01', 'BAGUA GRANDE'),
(87, '01', '07', '02', 'CAJARURO'),
(88, '01', '07', '03', 'CUMBA'),
(89, '01', '07', '04', 'EL MILAGRO'),
(90, '01', '07', '05', 'JAMALCA'),
(91, '01', '07', '06', 'LONYA GRANDE'),
(92, '01', '07', '07', 'YAMON'),
(93, '02', '00', '00', 'ANCASH'),
(94, '02', '01', '00', 'HUARAZ'),
(95, '02', '01', '01', 'HUARAZ'),
(96, '02', '01', '02', 'COCHABAMBA'),
(97, '02', '01', '03', 'COLCABAMBA'),
(98, '02', '01', '04', 'HUANCHAY'),
(99, '02', '01', '05', 'INDEPENDENCIA'),
(100, '02', '01', '06', 'JANGAS'),
(101, '02', '01', '07', 'LA LIBERTAD'),
(102, '02', '01', '08', 'OLLEROS'),
(103, '02', '01', '09', 'PAMPAS'),
(104, '02', '01', '10', 'PARIACOTO'),
(105, '02', '01', '11', 'PIRA'),
(106, '02', '01', '12', 'TARICA'),
(107, '02', '02', '00', 'AIJA'),
(108, '02', '02', '01', 'AIJA'),
(109, '02', '02', '02', 'CORIS'),
(110, '02', '02', '03', 'HUACLLAN'),
(111, '02', '02', '04', 'LA MERCED'),
(112, '02', '02', '05', 'SUCCHA'),
(113, '02', '03', '00', 'ANTONIO RAYMONDI'),
(114, '02', '03', '01', 'LLAMELLIN'),
(115, '02', '03', '02', 'ACZO'),
(116, '02', '03', '03', 'CHACCHO'),
(117, '02', '03', '04', 'CHINGAS'),
(118, '02', '03', '05', 'MIRGAS'),
(119, '02', '03', '06', 'SAN JUAN DE RONTOY'),
(120, '02', '04', '00', 'ASUNCION'),
(121, '02', '04', '01', 'CHACAS'),
(122, '02', '04', '02', 'ACOCHACA'),
(123, '02', '05', '00', 'BOLOGNESI'),
(124, '02', '05', '01', 'CHIQUIAN'),
(125, '02', '05', '02', 'ABELARDO PARDO LEZAMETA'),
(126, '02', '05', '03', 'ANTONIO RAYMONDI'),
(127, '02', '05', '04', 'AQUIA'),
(128, '02', '05', '05', 'CAJACAY'),
(129, '02', '05', '06', 'CANIS'),
(130, '02', '05', '07', 'COLQUIOC'),
(131, '02', '05', '08', 'HUALLANCA'),
(132, '02', '05', '09', 'HUASTA'),
(133, '02', '05', '10', 'HUAYLLACAYAN'),
(134, '02', '05', '11', 'LA PRIMAVERA'),
(135, '02', '05', '12', 'MANGAS'),
(136, '02', '05', '13', 'PACLLON'),
(137, '02', '05', '14', 'SAN MIGUEL DE CORPANQUI'),
(138, '02', '05', '15', 'TICLLOS'),
(139, '02', '06', '00', 'CARHUAZ'),
(140, '02', '06', '01', 'CARHUAZ'),
(141, '02', '06', '02', 'ACOPAMPA'),
(142, '02', '06', '03', 'AMASHCA'),
(143, '02', '06', '04', 'ANTA'),
(144, '02', '06', '05', 'ATAQUERO'),
(145, '02', '06', '06', 'MARCARA'),
(146, '02', '06', '07', 'PARIAHUANCA'),
(147, '02', '06', '08', 'SAN MIGUEL DE ACO'),
(148, '02', '06', '09', 'SHILLA'),
(149, '02', '06', '10', 'TINCO'),
(150, '02', '06', '11', 'YUNGAR'),
(151, '02', '07', '00', 'CARLOS FERMIN FITZCARRALD'),
(152, '02', '07', '01', 'SAN LUIS'),
(153, '02', '07', '02', 'SAN NICOLAS'),
(154, '02', '07', '03', 'YAUYA'),
(155, '02', '08', '00', 'CASMA'),
(156, '02', '08', '01', 'CASMA'),
(157, '02', '08', '02', 'BUENA VISTA ALTA'),
(158, '02', '08', '03', 'COMANDANTE NOEL'),
(159, '02', '08', '04', 'YAUTAN'),
(160, '02', '09', '00', 'CORONGO'),
(161, '02', '09', '01', 'CORONGO'),
(162, '02', '09', '02', 'ACO'),
(163, '02', '09', '03', 'BAMBAS'),
(164, '02', '09', '04', 'CUSCA'),
(165, '02', '09', '05', 'LA PAMPA'),
(166, '02', '09', '06', 'YANAC'),
(167, '02', '09', '07', 'YUPAN'),
(168, '02', '10', '00', 'HUARI'),
(169, '02', '10', '01', 'HUARI'),
(170, '02', '10', '02', 'ANRA'),
(171, '02', '10', '03', 'CAJAY'),
(172, '02', '10', '04', 'CHAVIN DE HUANTAR'),
(173, '02', '10', '05', 'HUACACHI'),
(174, '02', '10', '06', 'HUACCHIS'),
(175, '02', '10', '07', 'HUACHIS'),
(176, '02', '10', '08', 'HUANTAR'),
(177, '02', '10', '09', 'MASIN'),
(178, '02', '10', '10', 'PAUCAS'),
(179, '02', '10', '11', 'PONTO'),
(180, '02', '10', '12', 'RAHUAPAMPA'),
(181, '02', '10', '13', 'RAPAYAN'),
(182, '02', '10', '14', 'SAN MARCOS'),
(183, '02', '10', '15', 'SAN PEDRO DE CHANA'),
(184, '02', '10', '16', 'UCO'),
(185, '02', '11', '00', 'HUARMEY'),
(186, '02', '11', '01', 'HUARMEY'),
(187, '02', '11', '02', 'COCHAPETI'),
(188, '02', '11', '03', 'CULEBRAS'),
(189, '02', '11', '04', 'HUAYAN'),
(190, '02', '11', '05', 'MALVAS'),
(191, '02', '12', '00', 'HUAYLAS'),
(192, '02', '12', '01', 'CARAZ'),
(193, '02', '12', '02', 'HUALLANCA'),
(194, '02', '12', '03', 'HUATA'),
(195, '02', '12', '04', 'HUAYLAS'),
(196, '02', '12', '05', 'MATO'),
(197, '02', '12', '06', 'PAMPAROMAS'),
(198, '02', '12', '07', 'PUEBLO LIBRE'),
(199, '02', '12', '08', 'SANTA CRUZ'),
(200, '02', '12', '09', 'SANTO TORIBIO'),
(201, '02', '12', '10', 'YURACMARCA'),
(202, '02', '13', '00', 'MARISCAL LUZURIAGA'),
(203, '02', '13', '01', 'PISCOBAMBA'),
(204, '02', '13', '02', 'CASCA'),
(205, '02', '13', '03', 'ELEAZAR GUZMAN BARRON'),
(206, '02', '13', '04', 'FIDEL OLIVAS ESCUDERO'),
(207, '02', '13', '05', 'LLAMA'),
(208, '02', '13', '06', 'LLUMPA'),
(209, '02', '13', '07', 'LUCMA'),
(210, '02', '13', '08', 'MUSGA'),
(211, '02', '14', '00', 'OCROS'),
(212, '02', '14', '01', 'OCROS'),
(213, '02', '14', '02', 'ACAS'),
(214, '02', '14', '03', 'CAJAMARQUILLA'),
(215, '02', '14', '04', 'CARHUAPAMPA'),
(216, '02', '14', '05', 'COCHAS'),
(217, '02', '14', '06', 'CONGAS'),
(218, '02', '14', '07', 'LLIPA'),
(219, '02', '14', '08', 'SAN CRISTOBAL DE RAJAN'),
(220, '02', '14', '09', 'SAN PEDRO'),
(221, '02', '14', '10', 'SANTIAGO DE CHILCAS'),
(222, '02', '15', '00', 'PALLASCA'),
(223, '02', '15', '01', 'CABANA'),
(224, '02', '15', '02', 'BOLOGNESI'),
(225, '02', '15', '03', 'CONCHUCOS'),
(226, '02', '15', '04', 'HUACASCHUQUE'),
(227, '02', '15', '05', 'HUANDOVAL'),
(228, '02', '15', '06', 'LACABAMBA'),
(229, '02', '15', '07', 'LLAPO'),
(230, '02', '15', '08', 'PALLASCA'),
(231, '02', '15', '09', 'PAMPAS'),
(232, '02', '15', '10', 'SANTA ROSA'),
(233, '02', '15', '11', 'TAUCA'),
(234, '02', '16', '00', 'POMABAMBA'),
(235, '02', '16', '01', 'POMABAMBA'),
(236, '02', '16', '02', 'HUAYLLAN'),
(237, '02', '16', '03', 'PAROBAMBA'),
(238, '02', '16', '04', 'QUINUABAMBA'),
(239, '02', '17', '00', 'RECUAY'),
(240, '02', '17', '01', 'RECUAY'),
(241, '02', '17', '02', 'CATAC'),
(242, '02', '17', '03', 'COTAPARACO'),
(243, '02', '17', '04', 'HUAYLLAPAMPA'),
(244, '02', '17', '05', 'LLACLLIN'),
(245, '02', '17', '06', 'MARCA'),
(246, '02', '17', '07', 'PAMPAS CHICO'),
(247, '02', '17', '08', 'PARARIN'),
(248, '02', '17', '09', 'TAPACOCHA'),
(249, '02', '17', '10', 'TICAPAMPA'),
(250, '02', '18', '00', 'SANTA'),
(251, '02', '18', '01', 'CHIMBOTE'),
(252, '02', '18', '02', 'CACERES DEL PERU'),
(253, '02', '18', '03', 'COISHCO'),
(254, '02', '18', '04', 'MACATE'),
(255, '02', '18', '05', 'MORO'),
(256, '02', '18', '06', 'NEPEÑA'),
(257, '02', '18', '07', 'SAMANCO'),
(258, '02', '18', '08', 'SANTA'),
(259, '02', '18', '09', 'NUEVO CHIMBOTE'),
(260, '02', '19', '00', 'SIHUAS'),
(261, '02', '19', '01', 'SIHUAS'),
(262, '02', '19', '02', 'ACOBAMBA'),
(263, '02', '19', '03', 'ALFONSO UGARTE'),
(264, '02', '19', '04', 'CASHAPAMPA'),
(265, '02', '19', '05', 'CHINGALPO'),
(266, '02', '19', '06', 'HUAYLLABAMBA'),
(267, '02', '19', '07', 'QUICHES'),
(268, '02', '19', '08', 'RAGASH'),
(269, '02', '19', '09', 'SAN JUAN'),
(270, '02', '19', '10', 'SICSIBAMBA'),
(271, '02', '20', '00', 'YUNGAY'),
(272, '02', '20', '01', 'YUNGAY'),
(273, '02', '20', '02', 'CASCAPARA'),
(274, '02', '20', '03', 'MANCOS'),
(275, '02', '20', '04', 'MATACOTO'),
(276, '02', '20', '05', 'QUILLO'),
(277, '02', '20', '06', 'RANRAHIRCA'),
(278, '02', '20', '07', 'SHUPLUY'),
(279, '02', '20', '08', 'YANAMA'),
(280, '03', '00', '00', 'APURIMAC'),
(281, '03', '01', '00', 'ABANCAY'),
(282, '03', '01', '01', 'ABANCAY'),
(283, '03', '01', '02', 'CHACOCHE'),
(284, '03', '01', '03', 'CIRCA'),
(285, '03', '01', '04', 'CURAHUASI'),
(286, '03', '01', '05', 'HUANIPACA'),
(287, '03', '01', '06', 'LAMBRAMA'),
(288, '03', '01', '07', 'PICHIRHUA'),
(289, '03', '01', '08', 'SAN PEDRO DE CACHORA'),
(290, '03', '01', '09', 'TAMBURCO'),
(291, '03', '02', '00', 'ANDAHUAYLAS'),
(292, '03', '02', '01', 'ANDAHUAYLAS'),
(293, '03', '02', '02', 'ANDARAPA'),
(294, '03', '02', '03', 'CHIARA'),
(295, '03', '02', '04', 'HUANCARAMA'),
(296, '03', '02', '05', 'HUANCARAY'),
(297, '03', '02', '06', 'HUAYANA'),
(298, '03', '02', '07', 'KISHUARA'),
(299, '03', '02', '08', 'PACOBAMBA'),
(300, '03', '02', '09', 'PACUCHA'),
(301, '03', '02', '10', 'PAMPACHIRI'),
(302, '03', '02', '11', 'POMACOCHA'),
(303, '03', '02', '12', 'SAN ANTONIO DE CACHI'),
(304, '03', '02', '13', 'SAN JERONIMO'),
(305, '03', '02', '14', 'SAN MIGUEL DE CHACCRAMPA'),
(306, '03', '02', '15', 'SANTA MARIA DE CHICMO'),
(307, '03', '02', '16', 'TALAVERA'),
(308, '03', '02', '17', 'TUMAY HUARACA'),
(309, '03', '02', '18', 'TURPO'),
(310, '03', '02', '19', 'KAQUIABAMBA'),
(311, '03', '03', '00', 'ANTABAMBA'),
(312, '03', '03', '01', 'ANTABAMBA'),
(313, '03', '03', '02', 'EL ORO'),
(314, '03', '03', '03', 'HUAQUIRCA'),
(315, '03', '03', '04', 'JUAN ESPINOZA MEDRANO'),
(316, '03', '03', '05', 'OROPESA'),
(317, '03', '03', '06', 'PACHACONAS'),
(318, '03', '03', '07', 'SABAINO'),
(319, '03', '04', '00', 'AYMARAES'),
(320, '03', '04', '01', 'CHALHUANCA'),
(321, '03', '04', '02', 'CAPAYA'),
(322, '03', '04', '03', 'CARAYBAMBA'),
(323, '03', '04', '04', 'CHAPIMARCA'),
(324, '03', '04', '05', 'COLCABAMBA'),
(325, '03', '04', '06', 'COTARUSE'),
(326, '03', '04', '07', 'HUAYLLO'),
(327, '03', '04', '08', 'JUSTO APU SAHUARAURA'),
(328, '03', '04', '09', 'LUCRE'),
(329, '03', '04', '10', 'POCOHUANCA'),
(330, '03', '04', '11', 'SAN JUAN DE CHACÑA'),
(331, '03', '04', '12', 'SAÑAYCA'),
(332, '03', '04', '13', 'SORAYA'),
(333, '03', '04', '14', 'TAPAIRIHUA'),
(334, '03', '04', '15', 'TINTAY'),
(335, '03', '04', '16', 'TORAYA'),
(336, '03', '04', '17', 'YANACA'),
(337, '03', '05', '00', 'COTABAMBAS'),
(338, '03', '05', '01', 'TAMBOBAMBA'),
(339, '03', '05', '02', 'COTABAMBAS'),
(340, '03', '05', '03', 'COYLLURQUI'),
(341, '03', '05', '04', 'HAQUIRA'),
(342, '03', '05', '05', 'MARA'),
(343, '03', '05', '06', 'CHALLHUAHUACHO'),
(344, '03', '06', '00', 'CHINCHEROS'),
(345, '03', '06', '01', 'CHINCHEROS'),
(346, '03', '06', '02', 'ANCO-HUALLO'),
(347, '03', '06', '03', 'COCHARCAS'),
(348, '03', '06', '04', 'HUACCANA'),
(349, '03', '06', '05', 'OCOBAMBA'),
(350, '03', '06', '06', 'ONGOY'),
(351, '03', '06', '07', 'URANMARCA'),
(352, '03', '06', '08', 'RANRACANCHA'),
(353, '03', '07', '00', 'GRAU'),
(354, '03', '07', '01', 'CHUQUIBAMBILLA'),
(355, '03', '07', '02', 'CURPAHUASI'),
(356, '03', '07', '03', 'GAMARRA'),
(357, '03', '07', '04', 'HUAYLLATI'),
(358, '03', '07', '05', 'MAMARA'),
(359, '03', '07', '06', 'MICAELA BASTIDAS'),
(360, '03', '07', '07', 'PATAYPAMPA'),
(361, '03', '07', '08', 'PROGRESO'),
(362, '03', '07', '09', 'SAN ANTONIO'),
(363, '03', '07', '10', 'SANTA ROSA'),
(364, '03', '07', '11', 'TURPAY'),
(365, '03', '07', '12', 'VILCABAMBA'),
(366, '03', '07', '13', 'VIRUNDO'),
(367, '03', '07', '14', 'CURASCO'),
(368, '04', '00', '00', 'AREQUIPA'),
(369, '04', '01', '00', 'AREQUIPA'),
(370, '04', '01', '01', 'AREQUIPA'),
(371, '04', '01', '02', 'ALTO SELVA ALEGRE'),
(372, '04', '01', '03', 'CAYMA'),
(373, '04', '01', '04', 'CERRO COLORADO'),
(374, '04', '01', '05', 'CHARACATO'),
(375, '04', '01', '06', 'CHIGUATA'),
(376, '04', '01', '07', 'JACOBO HUNTER'),
(377, '04', '01', '08', 'LA JOYA'),
(378, '04', '01', '09', 'MARIANO MELGAR'),
(379, '04', '01', '10', 'MIRAFLORES'),
(380, '04', '01', '11', 'MOLLEBAYA'),
(381, '04', '01', '12', 'PAUCARPATA'),
(382, '04', '01', '13', 'POCSI'),
(383, '04', '01', '14', 'POLOBAYA'),
(384, '04', '01', '15', 'QUEQUEÑA'),
(385, '04', '01', '16', 'SABANDIA'),
(386, '04', '01', '17', 'SACHACA'),
(387, '04', '01', '18', 'SAN JUAN DE SIGUAS'),
(388, '04', '01', '19', 'SAN JUAN DE TARUCANI'),
(389, '04', '01', '20', 'SANTA ISABEL DE SIGUAS'),
(390, '04', '01', '21', 'SANTA RITA DE SIGUAS'),
(391, '04', '01', '22', 'SOCABAYA'),
(392, '04', '01', '23', 'TIABAYA'),
(393, '04', '01', '24', 'UCHUMAYO'),
(394, '04', '01', '25', 'VITOR'),
(395, '04', '01', '26', 'YANAHUARA'),
(396, '04', '01', '27', 'YARABAMBA'),
(397, '04', '01', '28', 'YURA'),
(398, '04', '01', '29', 'JOSE LUIS BUSTAMANTE Y RIVERO'),
(399, '04', '02', '00', 'CAMANA'),
(400, '04', '02', '01', 'CAMANA'),
(401, '04', '02', '02', 'JOSE MARIA QUIMPER'),
(402, '04', '02', '03', 'MARIANO NICOLAS VALCARCEL'),
(403, '04', '02', '04', 'MARISCAL CACERES'),
(404, '04', '02', '05', 'NICOLAS DE PIEROLA'),
(405, '04', '02', '06', 'OCOÑA'),
(406, '04', '02', '07', 'QUILCA'),
(407, '04', '02', '08', 'SAMUEL PASTOR'),
(408, '04', '03', '00', 'CARAVELI'),
(409, '04', '03', '01', 'CARAVELI'),
(410, '04', '03', '02', 'ACARI'),
(411, '04', '03', '03', 'ATICO'),
(412, '04', '03', '04', 'ATIQUIPA'),
(413, '04', '03', '05', 'BELLA UNION'),
(414, '04', '03', '06', 'CAHUACHO'),
(415, '04', '03', '07', 'CHALA'),
(416, '04', '03', '08', 'CHAPARRA'),
(417, '04', '03', '09', 'HUANUHUANU'),
(418, '04', '03', '10', 'JAQUI'),
(419, '04', '03', '11', 'LOMAS'),
(420, '04', '03', '12', 'QUICACHA'),
(421, '04', '03', '13', 'YAUCA'),
(422, '04', '04', '00', 'CASTILLA'),
(423, '04', '04', '01', 'APLAO'),
(424, '04', '04', '02', 'ANDAGUA'),
(425, '04', '04', '03', 'AYO'),
(426, '04', '04', '04', 'CHACHAS'),
(427, '04', '04', '05', 'CHILCAYMARCA'),
(428, '04', '04', '06', 'CHOCO'),
(429, '04', '04', '07', 'HUANCARQUI'),
(430, '04', '04', '08', 'MACHAGUAY'),
(431, '04', '04', '09', 'ORCOPAMPA'),
(432, '04', '04', '10', 'PAMPACOLCA'),
(433, '04', '04', '11', 'TIPAN'),
(434, '04', '04', '12', 'UÑON'),
(435, '04', '04', '13', 'URACA'),
(436, '04', '04', '14', 'VIRACO'),
(437, '04', '05', '00', 'CAYLLOMA'),
(438, '04', '05', '01', 'CHIVAY'),
(439, '04', '05', '02', 'ACHOMA'),
(440, '04', '05', '03', 'CABANACONDE'),
(441, '04', '05', '04', 'CALLALLI'),
(442, '04', '05', '05', 'CAYLLOMA'),
(443, '04', '05', '06', 'COPORAQUE'),
(444, '04', '05', '07', 'HUAMBO'),
(445, '04', '05', '08', 'HUANCA'),
(446, '04', '05', '09', 'ICHUPAMPA'),
(447, '04', '05', '10', 'LARI'),
(448, '04', '05', '11', 'LLUTA'),
(449, '04', '05', '12', 'MACA'),
(450, '04', '05', '13', 'MADRIGAL'),
(451, '04', '05', '14', 'SAN ANTONIO DE CHUCA'),
(452, '04', '05', '15', 'SIBAYO'),
(453, '04', '05', '16', 'TAPAY'),
(454, '04', '05', '17', 'TISCO'),
(455, '04', '05', '18', 'TUTI'),
(456, '04', '05', '19', 'YANQUE'),
(457, '04', '05', '20', 'MAJES'),
(458, '04', '06', '00', 'CONDESUYOS'),
(459, '04', '06', '01', 'CHUQUIBAMBA'),
(460, '04', '06', '02', 'ANDARAY'),
(461, '04', '06', '03', 'CAYARANI'),
(462, '04', '06', '04', 'CHICHAS'),
(463, '04', '06', '05', 'IRAY'),
(464, '04', '06', '06', 'RIO GRANDE'),
(465, '04', '06', '07', 'SALAMANCA'),
(466, '04', '06', '08', 'YANAQUIHUA'),
(467, '04', '07', '00', 'ISLAY'),
(468, '04', '07', '01', 'MOLLENDO'),
(469, '04', '07', '02', 'COCACHACRA'),
(470, '04', '07', '03', 'DEAN VALDIVIA'),
(471, '04', '07', '04', 'ISLAY'),
(472, '04', '07', '05', 'MEJIA'),
(473, '04', '07', '06', 'PUNTA DE BOMBON'),
(474, '04', '08', '00', 'LA UNION'),
(475, '04', '08', '01', 'COTAHUASI'),
(476, '04', '08', '02', 'ALCA'),
(477, '04', '08', '03', 'CHARCANA'),
(478, '04', '08', '04', 'HUAYNACOTAS'),
(479, '04', '08', '05', 'PAMPAMARCA'),
(480, '04', '08', '06', 'PUYCA'),
(481, '04', '08', '07', 'QUECHUALLA'),
(482, '04', '08', '08', 'SAYLA'),
(483, '04', '08', '09', 'TAURIA'),
(484, '04', '08', '10', 'TOMEPAMPA'),
(485, '04', '08', '11', 'TORO'),
(486, '05', '00', '00', 'AYACUCHO'),
(487, '05', '01', '00', 'HUAMANGA'),
(488, '05', '01', '01', 'AYACUCHO'),
(489, '05', '01', '02', 'ACOCRO'),
(490, '05', '01', '03', 'ACOS VINCHOS'),
(491, '05', '01', '04', 'CARMEN ALTO'),
(492, '05', '01', '05', 'CHIARA'),
(493, '05', '01', '06', 'OCROS'),
(494, '05', '01', '07', 'PACAYCASA'),
(495, '05', '01', '08', 'QUINUA'),
(496, '05', '01', '09', 'SAN JOSE DE TICLLAS'),
(497, '05', '01', '10', 'SAN JUAN BAUTISTA'),
(498, '05', '01', '11', 'SANTIAGO DE PISCHA'),
(499, '05', '01', '12', 'SOCOS'),
(500, '05', '01', '13', 'TAMBILLO'),
(501, '05', '01', '14', 'VINCHOS'),
(502, '05', '01', '15', 'JESÚS NAZARENO'),
(503, '05', '01', '16', 'ANDRÉS AVELINO CÁCERES DORREGAY'),
(504, '05', '02', '00', 'CANGALLO'),
(505, '05', '02', '01', 'CANGALLO'),
(506, '05', '02', '02', 'CHUSCHI'),
(507, '05', '02', '03', 'LOS MOROCHUCOS'),
(508, '05', '02', '04', 'MARIA PARADO DE BELLIDO'),
(509, '05', '02', '05', 'PARAS'),
(510, '05', '02', '06', 'TOTOS'),
(511, '05', '03', '00', 'HUANCA SANCOS'),
(512, '05', '03', '01', 'SANCOS'),
(513, '05', '03', '02', 'CARAPO'),
(514, '05', '03', '03', 'SACSAMARCA'),
(515, '05', '03', '04', 'SANTIAGO DE LUCANAMARCA'),
(516, '05', '04', '00', 'HUANTA'),
(517, '05', '04', '01', 'HUANTA'),
(518, '05', '04', '02', 'AYAHUANCO'),
(519, '05', '04', '03', 'HUAMANGUILLA'),
(520, '05', '04', '04', 'IGUAIN'),
(521, '05', '04', '05', 'LURICOCHA'),
(522, '05', '04', '06', 'SANTILLANA'),
(523, '05', '04', '07', 'SIVIA'),
(524, '05', '04', '08', 'LLOCHEGUA'),
(525, '05', '04', '09', 'CANAYRE'),
(526, '05', '04', '10', 'UCHURACCAY'),
(527, '05', '04', '11', 'PUCACOLPA'),
(528, '05', '05', '00', 'LA MAR'),
(529, '05', '05', '01', 'SAN MIGUEL'),
(530, '05', '05', '02', 'ANCO'),
(531, '05', '05', '03', 'AYNA'),
(532, '05', '05', '04', 'CHILCAS'),
(533, '05', '05', '05', 'CHUNGUI'),
(534, '05', '05', '06', 'LUIS CARRANZA'),
(535, '05', '05', '07', 'SANTA ROSA'),
(536, '05', '05', '08', 'TAMBO'),
(537, '05', '05', '09', 'SAMUGARI'),
(538, '05', '05', '10', 'ANCHIHUAY'),
(539, '05', '06', '00', 'LUCANAS'),
(540, '05', '06', '01', 'PUQUIO'),
(541, '05', '06', '02', 'AUCARA'),
(542, '05', '06', '03', 'CABANA'),
(543, '05', '06', '04', 'CARMEN SALCEDO'),
(544, '05', '06', '05', 'CHAVIÑA'),
(545, '05', '06', '06', 'CHIPAO'),
(546, '05', '06', '07', 'HUAC-HUAS'),
(547, '05', '06', '08', 'LARAMATE'),
(548, '05', '06', '09', 'LEONCIO PRADO'),
(549, '05', '06', '10', 'LLAUTA'),
(550, '05', '06', '11', 'LUCANAS'),
(551, '05', '06', '12', 'OCAÑA'),
(552, '05', '06', '13', 'OTOCA'),
(553, '05', '06', '14', 'SAISA'),
(554, '05', '06', '15', 'SAN CRISTOBAL'),
(555, '05', '06', '16', 'SAN JUAN'),
(556, '05', '06', '17', 'SAN PEDRO'),
(557, '05', '06', '18', 'SAN PEDRO DE PALCO'),
(558, '05', '06', '19', 'SANCOS'),
(559, '05', '06', '20', 'SANTA ANA DE HUAYCAHUACHO'),
(560, '05', '06', '21', 'SANTA LUCIA'),
(561, '05', '07', '00', 'PARINACOCHAS'),
(562, '05', '07', '01', 'CORACORA'),
(563, '05', '07', '02', 'CHUMPI'),
(564, '05', '07', '03', 'CORONEL CASTAÑEDA'),
(565, '05', '07', '04', 'PACAPAUSA'),
(566, '05', '07', '05', 'PULLO'),
(567, '05', '07', '06', 'PUYUSCA'),
(568, '05', '07', '07', 'SAN FRANCISCO DE RAVACAYCO'),
(569, '05', '07', '08', 'UPAHUACHO'),
(570, '05', '08', '00', 'PAUCAR DEL SARA SARA'),
(571, '05', '08', '01', 'PAUSA'),
(572, '05', '08', '02', 'COLTA'),
(573, '05', '08', '03', 'CORCULLA'),
(574, '05', '08', '04', 'LAMPA'),
(575, '05', '08', '05', 'MARCABAMBA'),
(576, '05', '08', '06', 'OYOLO'),
(577, '05', '08', '07', 'PARARCA'),
(578, '05', '08', '08', 'SAN JAVIER DE ALPABAMBA'),
(579, '05', '08', '09', 'SAN JOSE DE USHUA'),
(580, '05', '08', '10', 'SARA SARA'),
(581, '05', '09', '00', 'SUCRE'),
(582, '05', '09', '01', 'QUEROBAMBA'),
(583, '05', '09', '02', 'BELEN'),
(584, '05', '09', '03', 'CHALCOS'),
(585, '05', '09', '04', 'CHILCAYOC'),
(586, '05', '09', '05', 'HUACAÑA'),
(587, '05', '09', '06', 'MORCOLLA'),
(588, '05', '09', '07', 'PAICO'),
(589, '05', '09', '08', 'SAN PEDRO DE LARCAY'),
(590, '05', '09', '09', 'SAN SALVADOR DE QUIJE'),
(591, '05', '09', '10', 'SANTIAGO DE PAUCARAY'),
(592, '05', '09', '11', 'SORAS'),
(593, '05', '10', '00', 'VICTOR FAJARDO'),
(594, '05', '10', '01', 'HUANCAPI'),
(595, '05', '10', '02', 'ALCAMENCA'),
(596, '05', '10', '03', 'APONGO'),
(597, '05', '10', '04', 'ASQUIPATA'),
(598, '05', '10', '05', 'CANARIA'),
(599, '05', '10', '06', 'CAYARA'),
(600, '05', '10', '07', 'COLCA'),
(601, '05', '10', '08', 'HUAMANQUIQUIA'),
(602, '05', '10', '09', 'HUANCARAYLLA'),
(603, '05', '10', '10', 'HUAYA'),
(604, '05', '10', '11', 'SARHUA'),
(605, '05', '10', '12', 'VILCANCHOS'),
(606, '05', '11', '00', 'VILCAS HUAMAN'),
(607, '05', '11', '01', 'VILCAS HUAMAN'),
(608, '05', '11', '02', 'ACCOMARCA'),
(609, '05', '11', '03', 'CARHUANCA'),
(610, '05', '11', '04', 'CONCEPCION'),
(611, '05', '11', '05', 'HUAMBALPA'),
(612, '05', '11', '06', 'INDEPENDENCIA'),
(613, '05', '11', '07', 'SAURAMA'),
(614, '05', '11', '08', 'VISCHONGO'),
(615, '06', '00', '00', 'CAJAMARCA'),
(616, '06', '01', '00', 'CAJAMARCA'),
(617, '06', '01', '01', 'CAJAMARCA'),
(618, '06', '01', '02', 'ASUNCION'),
(619, '06', '01', '03', 'CHETILLA'),
(620, '06', '01', '04', 'COSPAN'),
(621, '06', '01', '05', 'ENCAÑADA'),
(622, '06', '01', '06', 'JESUS'),
(623, '06', '01', '07', 'LLACANORA'),
(624, '06', '01', '08', 'LOS BAÑOS DEL INCA'),
(625, '06', '01', '09', 'MAGDALENA'),
(626, '06', '01', '10', 'MATARA'),
(627, '06', '01', '11', 'NAMORA'),
(628, '06', '01', '12', 'SAN JUAN'),
(629, '06', '02', '00', 'CAJABAMBA'),
(630, '06', '02', '01', 'CAJABAMBA'),
(631, '06', '02', '02', 'CACHACHI'),
(632, '06', '02', '03', 'CONDEBAMBA'),
(633, '06', '02', '04', 'SITACOCHA'),
(634, '06', '03', '00', 'CELENDIN'),
(635, '06', '03', '01', 'CELENDIN'),
(636, '06', '03', '02', 'CHUMUCH'),
(637, '06', '03', '03', 'CORTEGANA'),
(638, '06', '03', '04', 'HUASMIN'),
(639, '06', '03', '05', 'JORGE CHAVEZ'),
(640, '06', '03', '06', 'JOSE GALVEZ'),
(641, '06', '03', '07', 'MIGUEL IGLESIAS'),
(642, '06', '03', '08', 'OXAMARCA'),
(643, '06', '03', '09', 'SOROCHUCO'),
(644, '06', '03', '10', 'SUCRE'),
(645, '06', '03', '11', 'UTCO'),
(646, '06', '03', '12', 'LA LIBERTAD DE PALLAN'),
(647, '06', '04', '00', 'CHOTA'),
(648, '06', '04', '01', 'CHOTA'),
(649, '06', '04', '02', 'ANGUIA'),
(650, '06', '04', '03', 'CHADIN'),
(651, '06', '04', '04', 'CHIGUIRIP'),
(652, '06', '04', '05', 'CHIMBAN'),
(653, '06', '04', '06', 'CHOROPAMPA'),
(654, '06', '04', '07', 'COCHABAMBA'),
(655, '06', '04', '08', 'CONCHAN'),
(656, '06', '04', '09', 'HUAMBOS'),
(657, '06', '04', '10', 'LAJAS'),
(658, '06', '04', '11', 'LLAMA'),
(659, '06', '04', '12', 'MIRACOSTA'),
(660, '06', '04', '13', 'PACCHA'),
(661, '06', '04', '14', 'PION'),
(662, '06', '04', '15', 'QUEROCOTO'),
(663, '06', '04', '16', 'SAN JUAN DE LICUPIS'),
(664, '06', '04', '17', 'TACABAMBA'),
(665, '06', '04', '18', 'TOCMOCHE'),
(666, '06', '04', '19', 'CHALAMARCA'),
(667, '06', '05', '00', 'CONTUMAZA'),
(668, '06', '05', '01', 'CONTUMAZA'),
(669, '06', '05', '02', 'CHILETE'),
(670, '06', '05', '03', 'CUPISNIQUE'),
(671, '06', '05', '04', 'GUZMANGO'),
(672, '06', '05', '05', 'SAN BENITO'),
(673, '06', '05', '06', 'SANTA CRUZ DE TOLED'),
(674, '06', '05', '07', 'TANTARICA'),
(675, '06', '05', '08', 'YONAN'),
(676, '06', '06', '00', 'CUTERVO'),
(677, '06', '06', '01', 'CUTERVO'),
(678, '06', '06', '02', 'CALLAYUC'),
(679, '06', '06', '03', 'CHOROS'),
(680, '06', '06', '04', 'CUJILLO'),
(681, '06', '06', '05', 'LA RAMADA'),
(682, '06', '06', '06', 'PIMPINGOS'),
(683, '06', '06', '07', 'QUEROCOTILLO'),
(684, '06', '06', '08', 'SAN ANDRES DE CUTERVO'),
(685, '06', '06', '09', 'SAN JUAN DE CUTERVO'),
(686, '06', '06', '10', 'SAN LUIS DE LUCMA'),
(687, '06', '06', '11', 'SANTA CRUZ'),
(688, '06', '06', '12', 'SANTO DOMINGO DE LA CAPILLA'),
(689, '06', '06', '13', 'SANTO TOMAS'),
(690, '06', '06', '14', 'SOCOTA'),
(691, '06', '06', '15', 'TORIBIO CASANOVA'),
(692, '06', '07', '00', 'HUALGAYOC'),
(693, '06', '07', '01', 'BAMBAMARCA'),
(694, '06', '07', '02', 'CHUGUR'),
(695, '06', '07', '03', 'HUALGAYOC'),
(696, '06', '08', '00', 'JAEN'),
(697, '06', '08', '01', 'JAEN'),
(698, '06', '08', '02', 'BELLAVISTA'),
(699, '06', '08', '03', 'CHONTALI'),
(700, '06', '08', '04', 'COLASAY'),
(701, '06', '08', '05', 'HUABAL'),
(702, '06', '08', '06', 'LAS PIRIAS'),
(703, '06', '08', '07', 'POMAHUACA'),
(704, '06', '08', '08', 'PUCARA'),
(705, '06', '08', '09', 'SALLIQUE'),
(706, '06', '08', '10', 'SAN FELIPE'),
(707, '06', '08', '11', 'SAN JOSE DEL ALTO'),
(708, '06', '08', '12', 'SANTA ROSA'),
(709, '06', '09', '00', 'SAN IGNACIO'),
(710, '06', '09', '01', 'SAN IGNACIO'),
(711, '06', '09', '02', 'CHIRINOS'),
(712, '06', '09', '03', 'HUARANGO'),
(713, '06', '09', '04', 'LA COIPA'),
(714, '06', '09', '05', 'NAMBALLE'),
(715, '06', '09', '06', 'SAN JOSE DE LOURDES'),
(716, '06', '09', '07', 'TABACONAS'),
(717, '06', '10', '00', 'SAN MARCOS'),
(718, '06', '10', '01', 'PEDRO GALVEZ'),
(719, '06', '10', '02', 'CHANCAY'),
(720, '06', '10', '03', 'EDUARDO VILLANUEVA'),
(721, '06', '10', '04', 'GREGORIO PITA'),
(722, '06', '10', '05', 'ICHOCAN'),
(723, '06', '10', '06', 'JOSE MANUEL QUIROZ'),
(724, '06', '10', '07', 'JOSE SABOGAL'),
(725, '06', '11', '00', 'SAN MIGUEL'),
(726, '06', '11', '01', 'SAN MIGUEL'),
(727, '06', '11', '02', 'BOLIVAR'),
(728, '06', '11', '03', 'CALQUIS'),
(729, '06', '11', '04', 'CATILLUC'),
(730, '06', '11', '05', 'EL PRADO'),
(731, '06', '11', '06', 'LA FLORIDA'),
(732, '06', '11', '07', 'LLAPA'),
(733, '06', '11', '08', 'NANCHOC'),
(734, '06', '11', '09', 'NIEPOS'),
(735, '06', '11', '10', 'SAN GREGORIO'),
(736, '06', '11', '11', 'SAN SILVESTRE DE COCHAN'),
(737, '06', '11', '12', 'TONGOD'),
(738, '06', '11', '13', 'UNION AGUA BLANCA'),
(739, '06', '12', '00', 'SAN PABLO'),
(740, '06', '12', '01', 'SAN PABLO'),
(741, '06', '12', '02', 'SAN BERNARDINO'),
(742, '06', '12', '03', 'SAN LUIS'),
(743, '06', '12', '04', 'TUMBADEN'),
(744, '06', '13', '00', 'SANTA CRUZ'),
(745, '06', '13', '01', 'SANTA CRUZ'),
(746, '06', '13', '02', 'ANDABAMBA'),
(747, '06', '13', '03', 'CATACHE'),
(748, '06', '13', '04', 'CHANCAYBAÑOS'),
(749, '06', '13', '05', 'LA ESPERANZA'),
(750, '06', '13', '06', 'NINABAMBA'),
(751, '06', '13', '07', 'PULAN'),
(752, '06', '13', '08', 'SAUCEPAMPA'),
(753, '06', '13', '09', 'SEXI'),
(754, '06', '13', '10', 'UTICYACU'),
(755, '06', '13', '11', 'YAUYUCAN'),
(756, '07', '00', '00', 'CALLAO'),
(757, '07', '01', '00', 'PROV. CONST. DEL CALLAO'),
(758, '07', '01', '01', 'CALLAO'),
(759, '07', '01', '02', 'BELLAVISTA'),
(760, '07', '01', '03', 'CARMEN DE LA LEGUA REYNOSO'),
(761, '07', '01', '04', 'LA PERLA'),
(762, '07', '01', '05', 'LA PUNTA'),
(763, '07', '01', '06', 'VENTANILLA'),
(764, '07', '01', '07', 'MI PERÚ'),
(765, '08', '00', '00', 'CUSCO'),
(766, '08', '01', '00', 'CUSCO'),
(767, '08', '01', '01', 'CUSCO'),
(768, '08', '01', '02', 'CCORCA'),
(769, '08', '01', '03', 'POROY'),
(770, '08', '01', '04', 'SAN JERONIMO'),
(771, '08', '01', '05', 'SAN SEBASTIAN'),
(772, '08', '01', '06', 'SANTIAGO'),
(773, '08', '01', '07', 'SAYLLA'),
(774, '08', '01', '08', 'WANCHAQ'),
(775, '08', '02', '00', 'ACOMAYO'),
(776, '08', '02', '01', 'ACOMAYO'),
(777, '08', '02', '02', 'ACOPIA'),
(778, '08', '02', '03', 'ACOS'),
(779, '08', '02', '04', 'MOSOC LLACTA'),
(780, '08', '02', '05', 'POMACANCHI'),
(781, '08', '02', '06', 'RONDOCAN'),
(782, '08', '02', '07', 'SANGARARA'),
(783, '08', '03', '00', 'ANTA'),
(784, '08', '03', '01', 'ANTA'),
(785, '08', '03', '02', 'ANCAHUASI'),
(786, '08', '03', '03', 'CACHIMAYO'),
(787, '08', '03', '04', 'CHINCHAYPUJIO'),
(788, '08', '03', '05', 'HUAROCONDO'),
(789, '08', '03', '06', 'LIMATAMBO'),
(790, '08', '03', '07', 'MOLLEPATA'),
(791, '08', '03', '08', 'PUCYURA'),
(792, '08', '03', '09', 'ZURITE'),
(793, '08', '04', '00', 'CALCA'),
(794, '08', '04', '01', 'CALCA'),
(795, '08', '04', '02', 'COYA'),
(796, '08', '04', '03', 'LAMAY'),
(797, '08', '04', '04', 'LARES'),
(798, '08', '04', '05', 'PISAC'),
(799, '08', '04', '06', 'SAN SALVADOR'),
(800, '08', '04', '07', 'TARAY'),
(801, '08', '04', '08', 'YANATILE'),
(802, '08', '05', '00', 'CANAS'),
(803, '08', '05', '01', 'YANAOCA'),
(804, '08', '05', '02', 'CHECCA'),
(805, '08', '05', '03', 'KUNTURKANKI'),
(806, '08', '05', '04', 'LANGUI'),
(807, '08', '05', '05', 'LAYO'),
(808, '08', '05', '06', 'PAMPAMARCA'),
(809, '08', '05', '07', 'QUEHUE'),
(810, '08', '05', '08', 'TUPAC AMARU'),
(811, '08', '06', '00', 'CANCHIS'),
(812, '08', '06', '01', 'SICUANI'),
(813, '08', '06', '02', 'CHECACUPE'),
(814, '08', '06', '03', 'COMBAPATA'),
(815, '08', '06', '04', 'MARANGANI'),
(816, '08', '06', '05', 'PITUMARCA'),
(817, '08', '06', '06', 'SAN PABLO'),
(818, '08', '06', '07', 'SAN PEDRO'),
(819, '08', '06', '08', 'TINTA'),
(820, '08', '07', '00', 'CHUMBIVILCAS'),
(821, '08', '07', '01', 'SANTO TOMAS'),
(822, '08', '07', '02', 'CAPACMARCA'),
(823, '08', '07', '03', 'CHAMACA'),
(824, '08', '07', '04', 'COLQUEMARCA'),
(825, '08', '07', '05', 'LIVITACA'),
(826, '08', '07', '06', 'LLUSCO'),
(827, '08', '07', '07', 'QUIÑOTA'),
(828, '08', '07', '08', 'VELILLE'),
(829, '08', '08', '00', 'ESPINAR'),
(830, '08', '08', '01', 'ESPINAR'),
(831, '08', '08', '02', 'CONDOROMA'),
(832, '08', '08', '03', 'COPORAQUE'),
(833, '08', '08', '04', 'OCORURO'),
(834, '08', '08', '05', 'PALLPATA'),
(835, '08', '08', '06', 'PICHIGUA'),
(836, '08', '08', '07', 'SUYCKUTAMBO'),
(837, '08', '08', '08', 'ALTO PICHIGUA'),
(838, '08', '09', '00', 'LA CONVENCION'),
(839, '08', '09', '01', 'SANTA ANA'),
(840, '08', '09', '02', 'ECHARATE'),
(841, '08', '09', '03', 'HUAYOPATA'),
(842, '08', '09', '04', 'MARANURA'),
(843, '08', '09', '05', 'OCOBAMBA'),
(844, '08', '09', '06', 'QUELLOUNO'),
(845, '08', '09', '07', 'KIMBIRI'),
(846, '08', '09', '08', 'SANTA TERESA'),
(847, '08', '09', '09', 'VILCABAMBA'),
(848, '08', '09', '10', 'PICHARI'),
(849, '08', '09', '11', 'INKAWASI'),
(850, '08', '09', '12', 'VILLA VIRGEN'),
(851, '08', '10', '00', 'PARURO'),
(852, '08', '10', '01', 'PARURO'),
(853, '08', '10', '02', 'ACCHA'),
(854, '08', '10', '03', 'CCAPI'),
(855, '08', '10', '04', 'COLCHA'),
(856, '08', '10', '05', 'HUANOQUITE'),
(857, '08', '10', '06', 'OMACHA'),
(858, '08', '10', '07', 'PACCARITAMBO'),
(859, '08', '10', '08', 'PILLPINTO'),
(860, '08', '10', '09', 'YAURISQUE'),
(861, '08', '11', '00', 'PAUCARTAMBO'),
(862, '08', '11', '01', 'PAUCARTAMBO'),
(863, '08', '11', '02', 'CAICAY'),
(864, '08', '11', '03', 'CHALLABAMBA'),
(865, '08', '11', '04', 'COLQUEPATA'),
(866, '08', '11', '05', 'HUANCARANI'),
(867, '08', '11', '06', 'KOSÑIPATA'),
(868, '08', '12', '00', 'QUISPICANCHI'),
(869, '08', '12', '01', 'URCOS'),
(870, '08', '12', '02', 'ANDAHUAYLILLAS'),
(871, '08', '12', '03', 'CAMANTI'),
(872, '08', '12', '04', 'CCARHUAYO'),
(873, '08', '12', '05', 'CCATCA'),
(874, '08', '12', '06', 'CUSIPATA'),
(875, '08', '12', '07', 'HUARO'),
(876, '08', '12', '08', 'LUCRE'),
(877, '08', '12', '09', 'MARCAPATA'),
(878, '08', '12', '10', 'OCONGATE'),
(879, '08', '12', '11', 'OROPESA'),
(880, '08', '12', '12', 'QUIQUIJANA'),
(881, '08', '13', '00', 'URUBAMBA'),
(882, '08', '13', '01', 'URUBAMBA'),
(883, '08', '13', '02', 'CHINCHERO'),
(884, '08', '13', '03', 'HUAYLLABAMBA'),
(885, '08', '13', '04', 'MACHUPICCHU'),
(886, '08', '13', '05', 'MARAS'),
(887, '08', '13', '06', 'OLLANTAYTAMBO'),
(888, '08', '13', '07', 'YUCAY'),
(889, '09', '00', '00', 'HUANCAVELICA'),
(890, '09', '01', '00', 'HUANCAVELICA'),
(891, '09', '01', '01', 'HUANCAVELICA'),
(892, '09', '01', '02', 'ACOBAMBILLA'),
(893, '09', '01', '03', 'ACORIA'),
(894, '09', '01', '04', 'CONAYCA'),
(895, '09', '01', '05', 'CUENCA'),
(896, '09', '01', '06', 'HUACHOCOLPA'),
(897, '09', '01', '07', 'HUAYLLAHUARA'),
(898, '09', '01', '08', 'IZCUCHACA'),
(899, '09', '01', '09', 'LARIA'),
(900, '09', '01', '10', 'MANTA'),
(901, '09', '01', '11', 'MARISCAL CACERES'),
(902, '09', '01', '12', 'MOYA'),
(903, '09', '01', '13', 'NUEVO OCCORO'),
(904, '09', '01', '14', 'PALCA'),
(905, '09', '01', '15', 'PILCHACA'),
(906, '09', '01', '16', 'VILCA'),
(907, '09', '01', '17', 'YAULI'),
(908, '09', '01', '18', 'ASCENSIÓN'),
(909, '09', '01', '19', 'HUANDO'),
(910, '09', '02', '00', 'ACOBAMBA'),
(911, '09', '02', '01', 'ACOBAMBA'),
(912, '09', '02', '02', 'ANDABAMBA'),
(913, '09', '02', '03', 'ANTA'),
(914, '09', '02', '04', 'CAJA'),
(915, '09', '02', '05', 'MARCAS'),
(916, '09', '02', '06', 'PAUCARA'),
(917, '09', '02', '07', 'POMACOCHA'),
(918, '09', '02', '08', 'ROSARIO'),
(919, '09', '03', '00', 'ANGARAES'),
(920, '09', '03', '01', 'LIRCAY'),
(921, '09', '03', '02', 'ANCHONGA'),
(922, '09', '03', '03', 'CALLANMARCA'),
(923, '09', '03', '04', 'CCOCHACCASA'),
(924, '09', '03', '05', 'CHINCHO'),
(925, '09', '03', '06', 'CONGALLA'),
(926, '09', '03', '07', 'HUANCA-HUANCA'),
(927, '09', '03', '08', 'HUAYLLAY GRANDE'),
(928, '09', '03', '09', 'JULCAMARCA'),
(929, '09', '03', '10', 'SAN ANTONIO DE ANTAPARCO'),
(930, '09', '03', '11', 'SANTO TOMAS DE PATA'),
(931, '09', '03', '12', 'SECCLLA'),
(932, '09', '04', '00', 'CASTROVIRREYNA'),
(933, '09', '04', '01', 'CASTROVIRREYNA'),
(934, '09', '04', '02', 'ARMA'),
(935, '09', '04', '03', 'AURAHUA'),
(936, '09', '04', '04', 'CAPILLAS'),
(937, '09', '04', '05', 'CHUPAMARCA'),
(938, '09', '04', '06', 'COCAS'),
(939, '09', '04', '07', 'HUACHOS'),
(940, '09', '04', '08', 'HUAMATAMBO'),
(941, '09', '04', '09', 'MOLLEPAMPA'),
(942, '09', '04', '10', 'SAN JUAN'),
(943, '09', '04', '11', 'SANTA ANA'),
(944, '09', '04', '12', 'TANTARA'),
(945, '09', '04', '13', 'TICRAPO'),
(946, '09', '05', '00', 'CHURCAMPA'),
(947, '09', '05', '01', 'CHURCAMPA'),
(948, '09', '05', '02', 'ANCO'),
(949, '09', '05', '03', 'CHINCHIHUASI'),
(950, '09', '05', '04', 'EL CARMEN'),
(951, '09', '05', '05', 'LA MERCED'),
(952, '09', '05', '06', 'LOCROJA'),
(953, '09', '05', '07', 'PAUCARBAMBA'),
(954, '09', '05', '08', 'SAN MIGUEL DE MAYOCC'),
(955, '09', '05', '09', 'SAN PEDRO DE CORIS'),
(956, '09', '05', '10', 'PACHAMARCA'),
(957, '09', '05', '11', 'COSME'),
(958, '09', '06', '00', 'HUAYTARA'),
(959, '09', '06', '01', 'HUAYTARA'),
(960, '09', '06', '02', 'AYAVI'),
(961, '09', '06', '03', 'CORDOVA'),
(962, '09', '06', '04', 'HUAYACUNDO ARMA'),
(963, '09', '06', '05', 'LARAMARCA'),
(964, '09', '06', '06', 'OCOYO'),
(965, '09', '06', '07', 'PILPICHACA'),
(966, '09', '06', '08', 'QUERCO'),
(967, '09', '06', '09', 'QUITO-ARMA'),
(968, '09', '06', '10', 'SAN ANTONIO DE CUSICANCHA'),
(969, '09', '06', '11', 'SAN FRANCISCO DE SANGAYAICO'),
(970, '09', '06', '12', 'SAN ISIDRO'),
(971, '09', '06', '13', 'SANTIAGO DE CHOCORVOS'),
(972, '09', '06', '14', 'SANTIAGO DE QUIRAHUARA'),
(973, '09', '06', '15', 'SANTO DOMINGO DE CAPILLAS'),
(974, '09', '06', '16', 'TAMBO'),
(975, '09', '07', '00', 'TAYACAJA'),
(976, '09', '07', '01', 'PAMPAS'),
(977, '09', '07', '02', 'ACOSTAMBO'),
(978, '09', '07', '03', 'ACRAQUIA'),
(979, '09', '07', '04', 'AHUAYCHA'),
(980, '09', '07', '05', 'COLCABAMBA'),
(981, '09', '07', '06', 'DANIEL HERNANDEZ'),
(982, '09', '07', '07', 'HUACHOCOLPA'),
(983, '09', '07', '09', 'HUARIBAMBA'),
(984, '09', '07', '10', 'ÑAHUIMPUQUIO'),
(985, '09', '07', '11', 'PAZOS'),
(986, '09', '07', '13', 'QUISHUAR'),
(987, '09', '07', '14', 'SALCABAMBA'),
(988, '09', '07', '15', 'SALCAHUASI'),
(989, '09', '07', '16', 'SAN MARCOS DE ROCCHAC'),
(990, '09', '07', '17', 'SURCUBAMBA'),
(991, '09', '07', '18', 'TINTAY PUNCU'),
(992, '10', '00', '00', 'HUANUCO'),
(993, '10', '01', '00', 'HUANUCO'),
(994, '10', '01', '01', 'HUANUCO'),
(995, '10', '01', '02', 'AMARILIS'),
(996, '10', '01', '03', 'CHINCHAO'),
(997, '10', '01', '04', 'CHURUBAMBA'),
(998, '10', '01', '05', 'MARGOS'),
(999, '10', '01', '06', 'QUISQUI'),
(1000, '10', '01', '07', 'SAN FRANCISCO DE CAYRAN'),
(1001, '10', '01', '08', 'SAN PEDRO DE CHAULAN'),
(1002, '10', '01', '09', 'SANTA MARIA DEL VALLE'),
(1003, '10', '01', '10', 'YARUMAYO'),
(1004, '10', '01', '11', 'PILLCO MARCA'),
(1005, '10', '01', '12', 'YACUS'),
(1006, '10', '02', '00', 'AMBO'),
(1007, '10', '02', '01', 'AMBO'),
(1008, '10', '02', '02', 'CAYNA'),
(1009, '10', '02', '03', 'COLPAS'),
(1010, '10', '02', '04', 'CONCHAMARCA'),
(1011, '10', '02', '05', 'HUACAR'),
(1012, '10', '02', '06', 'SAN FRANCISCO'),
(1013, '10', '02', '07', 'SAN RAFAEL'),
(1014, '10', '02', '08', 'TOMAY KICHWA'),
(1015, '10', '03', '00', 'DOS DE MAYO'),
(1016, '10', '03', '01', 'LA UNION'),
(1017, '10', '03', '07', 'CHUQUIS'),
(1018, '10', '03', '11', 'MARIAS'),
(1019, '10', '03', '13', 'PACHAS'),
(1020, '10', '03', '16', 'QUIVILLA'),
(1021, '10', '03', '17', 'RIPAN'),
(1022, '10', '03', '21', 'SHUNQUI'),
(1023, '10', '03', '22', 'SILLAPATA'),
(1024, '10', '03', '23', 'YANAS'),
(1025, '10', '04', '00', 'HUACAYBAMBA'),
(1026, '10', '04', '01', 'HUACAYBAMBA'),
(1027, '10', '04', '02', 'CANCHABAMBA'),
(1028, '10', '04', '03', 'COCHABAMBA'),
(1029, '10', '04', '04', 'PINRA'),
(1030, '10', '05', '00', 'HUAMALIES'),
(1031, '10', '05', '01', 'LLATA'),
(1032, '10', '05', '02', 'ARANCAY'),
(1033, '10', '05', '03', 'CHAVIN DE PARIARCA'),
(1034, '10', '05', '04', 'JACAS GRANDE'),
(1035, '10', '05', '05', 'JIRCAN'),
(1036, '10', '05', '06', 'MIRAFLORES'),
(1037, '10', '05', '07', 'MONZON'),
(1038, '10', '05', '08', 'PUNCHAO'),
(1039, '10', '05', '09', 'PUÑOS'),
(1040, '10', '05', '10', 'SINGA'),
(1041, '10', '05', '11', 'TANTAMAYO'),
(1042, '10', '06', '00', 'LEONCIO PRADO'),
(1043, '10', '06', '01', 'RUPA-RUPA'),
(1044, '10', '06', '02', 'DANIEL ALOMIAS ROBLES'),
(1045, '10', '06', '03', 'HERMILIO VALDIZAN'),
(1046, '10', '06', '04', 'JOSE CRESPO Y CASTILLO'),
(1047, '10', '06', '05', 'LUYANDO'),
(1048, '10', '06', '06', 'MARIANO DAMASO BERAUN'),
(1049, '10', '07', '00', 'MARAÑON'),
(1050, '10', '07', '01', 'HUACRACHUCO'),
(1051, '10', '07', '02', 'CHOLON'),
(1052, '10', '07', '03', 'SAN BUENAVENTURA'),
(1053, '10', '08', '00', 'PACHITEA'),
(1054, '10', '08', '01', 'PANAO'),
(1055, '10', '08', '02', 'CHAGLLA'),
(1056, '10', '08', '03', 'MOLINO'),
(1057, '10', '08', '04', 'UMARI'),
(1058, '10', '09', '00', 'PUERTO INCA'),
(1059, '10', '09', '01', 'PUERTO INCA'),
(1060, '10', '09', '02', 'CODO DEL POZUZO'),
(1061, '10', '09', '03', 'HONORIA'),
(1062, '10', '09', '04', 'TOURNAVISTA'),
(1063, '10', '09', '05', 'YUYAPICHIS'),
(1064, '10', '10', '00', 'LAURICOCHA'),
(1065, '10', '10', '01', 'JESUS'),
(1066, '10', '10', '02', 'BAÑOS'),
(1067, '10', '10', '03', 'JIVIA'),
(1068, '10', '10', '04', 'QUEROPALCA'),
(1069, '10', '10', '05', 'RONDOS'),
(1070, '10', '10', '06', 'SAN FRANCISCO DE ASIS'),
(1071, '10', '10', '07', 'SAN MIGUEL DE CAURI'),
(1072, '10', '11', '00', 'YAROWILCA'),
(1073, '10', '11', '01', 'CHAVINILLO'),
(1074, '10', '11', '02', 'CAHUAC'),
(1075, '10', '11', '03', 'CHACABAMBA'),
(1076, '10', '11', '04', 'CHUPAN'),
(1077, '10', '11', '05', 'JACAS CHICO'),
(1078, '10', '11', '06', 'OBAS'),
(1079, '10', '11', '07', 'PAMPAMARCA'),
(1080, '10', '11', '08', 'CHORAS'),
(1081, '11', '00', '00', 'ICA'),
(1082, '11', '01', '00', 'ICA'),
(1083, '11', '01', '01', 'ICA'),
(1084, '11', '01', '02', 'LA TINGUIÑA'),
(1085, '11', '01', '03', 'LOS AQUIJES'),
(1086, '11', '01', '04', 'OCUCAJE'),
(1087, '11', '01', '05', 'PACHACUTEC'),
(1088, '11', '01', '06', 'PARCONA'),
(1089, '11', '01', '07', 'PUEBLO NUEVO'),
(1090, '11', '01', '08', 'SALAS'),
(1091, '11', '01', '09', 'SAN JOSE DE LOS MOLINOS'),
(1092, '11', '01', '10', 'SAN JUAN BAUTISTA'),
(1093, '11', '01', '11', 'SANTIAGO'),
(1094, '11', '01', '12', 'SUBTANJALLA'),
(1095, '11', '01', '13', 'TATE'),
(1096, '11', '01', '14', 'YAUCA DEL ROSARIO'),
(1097, '11', '02', '00', 'CHINCHA'),
(1098, '11', '02', '01', 'CHINCHA ALTA'),
(1099, '11', '02', '02', 'ALTO LARAN'),
(1100, '11', '02', '03', 'CHAVIN'),
(1101, '11', '02', '04', 'CHINCHA BAJA'),
(1102, '11', '02', '05', 'EL CARMEN'),
(1103, '11', '02', '06', 'GROCIO PRADO'),
(1104, '11', '02', '07', 'PUEBLO NUEVO'),
(1105, '11', '02', '08', 'SAN JUAN DE YANAC'),
(1106, '11', '02', '09', 'SAN PEDRO DE HUACARPANA'),
(1107, '11', '02', '10', 'SUNAMPE'),
(1108, '11', '02', '11', 'TAMBO DE MORA'),
(1109, '11', '03', '00', 'NAZCA'),
(1110, '11', '03', '01', 'NAZCA'),
(1111, '11', '03', '02', 'CHANGUILLO'),
(1112, '11', '03', '03', 'EL INGENIO'),
(1113, '11', '03', '04', 'MARCONA'),
(1114, '11', '03', '05', 'VISTA ALEGRE'),
(1115, '11', '04', '00', 'PALPA'),
(1116, '11', '04', '01', 'PALPA'),
(1117, '11', '04', '02', 'LLIPATA'),
(1118, '11', '04', '03', 'RIO GRANDE'),
(1119, '11', '04', '04', 'SANTA CRUZ'),
(1120, '11', '04', '05', 'TIBILLO'),
(1121, '11', '05', '00', 'PISCO'),
(1122, '11', '05', '01', 'PISCO'),
(1123, '11', '05', '02', 'HUANCANO'),
(1124, '11', '05', '03', 'HUMAY'),
(1125, '11', '05', '04', 'INDEPENDENCIA'),
(1126, '11', '05', '05', 'PARACAS'),
(1127, '11', '05', '06', 'SAN ANDRES'),
(1128, '11', '05', '07', 'SAN CLEMENTE'),
(1129, '11', '05', '08', 'TUPAC AMARU INCA'),
(1130, '12', '00', '00', 'JUNIN'),
(1131, '12', '01', '00', 'HUANCAYO'),
(1132, '12', '01', '01', 'HUANCAYO'),
(1133, '12', '01', '04', 'CARHUACALLANGA'),
(1134, '12', '01', '05', 'CHACAPAMPA'),
(1135, '12', '01', '06', 'CHICCHE'),
(1136, '12', '01', '07', 'CHILCA'),
(1137, '12', '01', '08', 'CHONGOS ALTO'),
(1138, '12', '01', '11', 'CHUPURO'),
(1139, '12', '01', '12', 'COLCA'),
(1140, '12', '01', '13', 'CULLHUAS'),
(1141, '12', '01', '14', 'EL TAMBO'),
(1142, '12', '01', '16', 'HUACRAPUQUIO'),
(1143, '12', '01', '17', 'HUALHUAS'),
(1144, '12', '01', '19', 'HUANCAN'),
(1145, '12', '01', '20', 'HUASICANCHA'),
(1146, '12', '01', '21', 'HUAYUCACHI'),
(1147, '12', '01', '22', 'INGENIO'),
(1148, '12', '01', '24', 'PARIAHUANCA'),
(1149, '12', '01', '25', 'PILCOMAYO'),
(1150, '12', '01', '26', 'PUCARA'),
(1151, '12', '01', '27', 'QUICHUAY'),
(1152, '12', '01', '28', 'QUILCAS'),
(1153, '12', '01', '29', 'SAN AGUSTIN'),
(1154, '12', '01', '30', 'SAN JERONIMO DE TUNAN'),
(1155, '12', '01', '32', 'SAÑO'),
(1156, '12', '01', '33', 'SAPALLANGA'),
(1157, '12', '01', '34', 'SICAYA'),
(1158, '12', '01', '35', 'SANTO DOMINGO DE ACOBAMBA'),
(1159, '12', '01', '36', 'VIQUES'),
(1160, '12', '02', '00', 'CONCEPCION'),
(1161, '12', '02', '01', 'CONCEPCION'),
(1162, '12', '02', '02', 'ACO'),
(1163, '12', '02', '03', 'ANDAMARCA'),
(1164, '12', '02', '04', 'CHAMBARA'),
(1165, '12', '02', '05', 'COCHAS'),
(1166, '12', '02', '06', 'COMAS'),
(1167, '12', '02', '07', 'HEROINAS TOLEDO'),
(1168, '12', '02', '08', 'MANZANARES'),
(1169, '12', '02', '09', 'MARISCAL CASTILLA'),
(1170, '12', '02', '10', 'MATAHUASI'),
(1171, '12', '02', '11', 'MITO'),
(1172, '12', '02', '12', 'NUEVE DE JULIO'),
(1173, '12', '02', '13', 'ORCOTUNA'),
(1174, '12', '02', '14', 'SAN JOSE DE QUERO'),
(1175, '12', '02', '15', 'SANTA ROSA DE OCOPA'),
(1176, '12', '03', '00', 'CHANCHAMAYO'),
(1177, '12', '03', '01', 'CHANCHAMAYO'),
(1178, '12', '03', '02', 'PERENE'),
(1179, '12', '03', '03', 'PICHANAQUI'),
(1180, '12', '03', '04', 'SAN LUIS DE SHUARO'),
(1181, '12', '03', '05', 'SAN RAMON'),
(1182, '12', '03', '06', 'VITOC'),
(1183, '12', '04', '00', 'JAUJA'),
(1184, '12', '04', '01', 'JAUJA'),
(1185, '12', '04', '02', 'ACOLLA'),
(1186, '12', '04', '03', 'APATA'),
(1187, '12', '04', '04', 'ATAURA'),
(1188, '12', '04', '05', 'CANCHAYLLO'),
(1189, '12', '04', '06', 'CURICACA'),
(1190, '12', '04', '07', 'EL MANTARO'),
(1191, '12', '04', '08', 'HUAMALI'),
(1192, '12', '04', '09', 'HUARIPAMPA'),
(1193, '12', '04', '10', 'HUERTAS'),
(1194, '12', '04', '11', 'JANJAILLO'),
(1195, '12', '04', '12', 'JULCAN'),
(1196, '12', '04', '13', 'LEONOR ORDOÑEZ'),
(1197, '12', '04', '14', 'LLOCLLAPAMPA'),
(1198, '12', '04', '15', 'MARCO'),
(1199, '12', '04', '16', 'MASMA'),
(1200, '12', '04', '17', 'MASMA CHICCHE'),
(1201, '12', '04', '18', 'MOLINOS'),
(1202, '12', '04', '19', 'MONOBAMBA'),
(1203, '12', '04', '20', 'MUQUI'),
(1204, '12', '04', '21', 'MUQUIYAUYO'),
(1205, '12', '04', '22', 'PACA'),
(1206, '12', '04', '23', 'PACCHA'),
(1207, '12', '04', '24', 'PANCAN'),
(1208, '12', '04', '25', 'PARCO'),
(1209, '12', '04', '26', 'POMACANCHA'),
(1210, '12', '04', '27', 'RICRAN'),
(1211, '12', '04', '28', 'SAN LORENZO'),
(1212, '12', '04', '29', 'SAN PEDRO DE CHUNAN'),
(1213, '12', '04', '30', 'SAUSA'),
(1214, '12', '04', '31', 'SINCOS'),
(1215, '12', '04', '32', 'TUNAN MARCA'),
(1216, '12', '04', '33', 'YAULI'),
(1217, '12', '04', '34', 'YAUYOS'),
(1218, '12', '05', '00', 'JUNIN'),
(1219, '12', '05', '01', 'JUNIN'),
(1220, '12', '05', '02', 'CARHUAMAYO'),
(1221, '12', '05', '03', 'ONDORES'),
(1222, '12', '05', '04', 'ULCUMAYO'),
(1223, '12', '06', '00', 'SATIPO'),
(1224, '12', '06', '01', 'SATIPO'),
(1225, '12', '06', '02', 'COVIRIALI'),
(1226, '12', '06', '03', 'LLAYLLA'),
(1227, '12', '06', '04', 'MAZAMARI'),
(1228, '12', '06', '05', 'PAMPA HERMOSA'),
(1229, '12', '06', '06', 'PANGOA'),
(1230, '12', '06', '07', 'RIO NEGRO'),
(1231, '12', '06', '08', 'RIO TAMBO'),
(1232, '12', '06', '99', 'MAZAMARI-PANGOA'),
(1233, '12', '07', '00', 'TARMA'),
(1234, '12', '07', '01', 'TARMA'),
(1235, '12', '07', '02', 'ACOBAMBA'),
(1236, '12', '07', '03', 'HUARICOLCA'),
(1237, '12', '07', '04', 'HUASAHUASI'),
(1238, '12', '07', '05', 'LA UNION'),
(1239, '12', '07', '06', 'PALCA'),
(1240, '12', '07', '07', 'PALCAMAYO'),
(1241, '12', '07', '08', 'SAN PEDRO DE CAJAS'),
(1242, '12', '07', '09', 'TAPO'),
(1243, '12', '08', '00', 'YAULI'),
(1244, '12', '08', '01', 'LA OROYA'),
(1245, '12', '08', '02', 'CHACAPALPA'),
(1246, '12', '08', '03', 'HUAY-HUAY'),
(1247, '12', '08', '04', 'MARCAPOMACOCHA'),
(1248, '12', '08', '05', 'MOROCOCHA'),
(1249, '12', '08', '06', 'PACCHA'),
(1250, '12', '08', '07', 'SANTA BARBARA DE CARHUACAYAN'),
(1251, '12', '08', '08', 'SANTA ROSA DE SACCO'),
(1252, '12', '08', '09', 'SUITUCANCHA'),
(1253, '12', '08', '10', 'YAULI'),
(1254, '12', '09', '00', 'CHUPACA'),
(1255, '12', '09', '01', 'CHUPACA'),
(1256, '12', '09', '02', 'AHUAC'),
(1257, '12', '09', '03', 'CHONGOS BAJO'),
(1258, '12', '09', '04', 'HUACHAC'),
(1259, '12', '09', '05', 'HUAMANCACA CHICO'),
(1260, '12', '09', '06', 'SAN JUAN DE ISCOS'),
(1261, '12', '09', '07', 'SAN JUAN DE JARPA'),
(1262, '12', '09', '08', '3 DE DICIEMBRE'),
(1263, '12', '09', '09', 'YANACANCHA'),
(1264, '13', '00', '00', 'LA LIBERTAD'),
(1265, '13', '01', '00', 'TRUJILLO'),
(1266, '13', '01', '01', 'TRUJILLO'),
(1267, '13', '01', '02', 'EL PORVENIR'),
(1268, '13', '01', '03', 'FLORENCIA DE MORA'),
(1269, '13', '01', '04', 'HUANCHACO'),
(1270, '13', '01', '05', 'LA ESPERANZA'),
(1271, '13', '01', '06', 'LAREDO'),
(1272, '13', '01', '07', 'MOCHE'),
(1273, '13', '01', '08', 'POROTO'),
(1274, '13', '01', '09', 'SALAVERRY'),
(1275, '13', '01', '10', 'SIMBAL'),
(1276, '13', '01', '11', 'VICTOR LARCO HERRERA'),
(1277, '13', '02', '00', 'ASCOPE'),
(1278, '13', '02', '01', 'ASCOPE'),
(1279, '13', '02', '02', 'CHICAMA'),
(1280, '13', '02', '03', 'CHOCOPE'),
(1281, '13', '02', '04', 'MAGDALENA DE CAO'),
(1282, '13', '02', '05', 'PAIJAN'),
(1283, '13', '02', '06', 'RAZURI'),
(1284, '13', '02', '07', 'SANTIAGO DE CAO'),
(1285, '13', '02', '08', 'CASA GRANDE'),
(1286, '13', '03', '00', 'BOLIVAR'),
(1287, '13', '03', '01', 'BOLIVAR'),
(1288, '13', '03', '02', 'BAMBAMARCA'),
(1289, '13', '03', '03', 'CONDORMARCA'),
(1290, '13', '03', '04', 'LONGOTEA'),
(1291, '13', '03', '05', 'UCHUMARCA'),
(1292, '13', '03', '06', 'UCUNCHA'),
(1293, '13', '04', '00', 'CHEPEN'),
(1294, '13', '04', '01', 'CHEPEN'),
(1295, '13', '04', '02', 'PACANGA'),
(1296, '13', '04', '03', 'PUEBLO NUEVO'),
(1297, '13', '05', '00', 'JULCAN'),
(1298, '13', '05', '01', 'JULCAN'),
(1299, '13', '05', '02', 'CALAMARCA'),
(1300, '13', '05', '03', 'CARABAMBA'),
(1301, '13', '05', '04', 'HUASO'),
(1302, '13', '06', '00', 'OTUZCO'),
(1303, '13', '06', '01', 'OTUZCO'),
(1304, '13', '06', '02', 'AGALLPAMPA'),
(1305, '13', '06', '04', 'CHARAT'),
(1306, '13', '06', '05', 'HUARANCHAL'),
(1307, '13', '06', '06', 'LA CUESTA'),
(1308, '13', '06', '08', 'MACHE'),
(1309, '13', '06', '10', 'PARANDAY'),
(1310, '13', '06', '11', 'SALPO'),
(1311, '13', '06', '13', 'SINSICAP'),
(1312, '13', '06', '14', 'USQUIL'),
(1313, '13', '07', '00', 'PACASMAYO'),
(1314, '13', '07', '01', 'SAN PEDRO DE LLOC'),
(1315, '13', '07', '02', 'GUADALUPE'),
(1316, '13', '07', '03', 'JEQUETEPEQUE'),
(1317, '13', '07', '04', 'PACASMAYO'),
(1318, '13', '07', '05', 'SAN JOSE'),
(1319, '13', '08', '00', 'PATAZ'),
(1320, '13', '08', '01', 'TAYABAMBA'),
(1321, '13', '08', '02', 'BULDIBUYO'),
(1322, '13', '08', '03', 'CHILLIA'),
(1323, '13', '08', '04', 'HUANCASPATA'),
(1324, '13', '08', '05', 'HUAYLILLAS'),
(1325, '13', '08', '06', 'HUAYO'),
(1326, '13', '08', '07', 'ONGON'),
(1327, '13', '08', '08', 'PARCOY'),
(1328, '13', '08', '09', 'PATAZ'),
(1329, '13', '08', '10', 'PIAS'),
(1330, '13', '08', '11', 'SANTIAGO DE CHALLAS'),
(1331, '13', '08', '12', 'TAURIJA'),
(1332, '13', '08', '13', 'URPAY'),
(1333, '13', '09', '00', 'SANCHEZ CARRION'),
(1334, '13', '09', '01', 'HUAMACHUCO'),
(1335, '13', '09', '02', 'CHUGAY'),
(1336, '13', '09', '03', 'COCHORCO'),
(1337, '13', '09', '04', 'CURGOS'),
(1338, '13', '09', '05', 'MARCABAL'),
(1339, '13', '09', '06', 'SANAGORAN'),
(1340, '13', '09', '07', 'SARIN'),
(1341, '13', '09', '08', 'SARTIMBAMBA'),
(1342, '13', '10', '00', 'SANTIAGO DE CHUCO'),
(1343, '13', '10', '01', 'SANTIAGO DE CHUCO'),
(1344, '13', '10', '02', 'ANGASMARCA'),
(1345, '13', '10', '03', 'CACHICADAN'),
(1346, '13', '10', '04', 'MOLLEBAMBA'),
(1347, '13', '10', '05', 'MOLLEPATA'),
(1348, '13', '10', '06', 'QUIRUVILCA'),
(1349, '13', '10', '07', 'SANTA CRUZ DE CHUCA'),
(1350, '13', '10', '08', 'SITABAMBA'),
(1351, '13', '11', '00', 'GRAN CHIMU'),
(1352, '13', '11', '01', 'CASCAS'),
(1353, '13', '11', '02', 'LUCMA'),
(1354, '13', '11', '03', 'MARMOT'),
(1355, '13', '11', '04', 'SAYAPULLO'),
(1356, '13', '12', '00', 'VIRU'),
(1357, '13', '12', '01', 'VIRU'),
(1358, '13', '12', '02', 'CHAO'),
(1359, '13', '12', '03', 'GUADALUPITO'),
(1360, '14', '00', '00', 'LAMBAYEQUE'),
(1361, '14', '01', '00', 'CHICLAYO'),
(1362, '14', '01', '01', 'CHICLAYO'),
(1363, '14', '01', '02', 'CHONGOYAPE'),
(1364, '14', '01', '03', 'ETEN'),
(1365, '14', '01', '04', 'ETEN PUERTO'),
(1366, '14', '01', '05', 'JOSE LEONARDO ORTIZ'),
(1367, '14', '01', '06', 'LA VICTORIA'),
(1368, '14', '01', '07', 'LAGUNAS'),
(1369, '14', '01', '08', 'MONSEFU'),
(1370, '14', '01', '09', 'NUEVA ARICA'),
(1371, '14', '01', '10', 'OYOTUN'),
(1372, '14', '01', '11', 'PICSI'),
(1373, '14', '01', '12', 'PIMENTEL'),
(1374, '14', '01', '13', 'REQUE'),
(1375, '14', '01', '14', 'SANTA ROSA'),
(1376, '14', '01', '15', 'SAÑA');
INSERT INTO `ubigeo_inei` (`id_ubigeo`, `departamento`, `provincia`, `distrito`, `nombre`) VALUES
(1377, '14', '01', '16', 'CAYALTÍ'),
(1378, '14', '01', '17', 'PATAPO'),
(1379, '14', '01', '18', 'POMALCA'),
(1380, '14', '01', '19', 'PUCALÁ'),
(1381, '14', '01', '20', 'TUMÁN'),
(1382, '14', '02', '00', 'FERREÑAFE'),
(1383, '14', '02', '01', 'FERREÑAFE'),
(1384, '14', '02', '02', 'CAÑARIS'),
(1385, '14', '02', '03', 'INCAHUASI'),
(1386, '14', '02', '04', 'MANUEL ANTONIO MESONES MURO'),
(1387, '14', '02', '05', 'PITIPO'),
(1388, '14', '02', '06', 'PUEBLO NUEVO'),
(1389, '14', '03', '00', 'LAMBAYEQUE'),
(1390, '14', '03', '01', 'LAMBAYEQUE'),
(1391, '14', '03', '02', 'CHOCHOPE'),
(1392, '14', '03', '03', 'ILLIMO'),
(1393, '14', '03', '04', 'JAYANCA'),
(1394, '14', '03', '05', 'MOCHUMI'),
(1395, '14', '03', '06', 'MORROPE'),
(1396, '14', '03', '07', 'MOTUPE'),
(1397, '14', '03', '08', 'OLMOS'),
(1398, '14', '03', '09', 'PACORA'),
(1399, '14', '03', '10', 'SALAS'),
(1400, '14', '03', '11', 'SAN JOSE'),
(1401, '14', '03', '12', 'TUCUME'),
(1402, '15', '00', '00', 'LIMA'),
(1403, '15', '01', '00', 'LIMA'),
(1404, '15', '01', '01', 'LIMA'),
(1405, '15', '01', '02', 'ANCON'),
(1406, '15', '01', '03', 'ATE'),
(1407, '15', '01', '04', 'BARRANCO'),
(1408, '15', '01', '05', 'BREÑA'),
(1409, '15', '01', '06', 'CARABAYLLO'),
(1410, '15', '01', '07', 'CHACLACAYO'),
(1411, '15', '01', '08', 'CHORRILLOS'),
(1412, '15', '01', '09', 'CIENEGUILLA'),
(1413, '15', '01', '10', 'COMAS'),
(1414, '15', '01', '11', 'EL AGUSTINO'),
(1415, '15', '01', '12', 'INDEPENDENCIA'),
(1416, '15', '01', '13', 'JESUS MARIA'),
(1417, '15', '01', '14', 'LA MOLINA'),
(1418, '15', '01', '15', 'LA VICTORIA'),
(1419, '15', '01', '16', 'LINCE'),
(1420, '15', '01', '17', 'LOS OLIVOS'),
(1421, '15', '01', '18', 'LURIGANCHO'),
(1422, '15', '01', '19', 'LURIN'),
(1423, '15', '01', '20', 'MAGDALENA DEL MAR'),
(1424, '15', '01', '21', 'PUEBLO LIBRE (MAGDALENA VIEJA)'),
(1425, '15', '01', '22', 'MIRAFLORES'),
(1426, '15', '01', '23', 'PACHACAMAC'),
(1427, '15', '01', '24', 'PUCUSANA'),
(1428, '15', '01', '25', 'PUENTE PIEDRA'),
(1429, '15', '01', '26', 'PUNTA HERMOSA'),
(1430, '15', '01', '27', 'PUNTA NEGRA'),
(1431, '15', '01', '28', 'RIMAC'),
(1432, '15', '01', '29', 'SAN BARTOLO'),
(1433, '15', '01', '30', 'SAN BORJA'),
(1434, '15', '01', '31', 'SAN ISIDRO'),
(1435, '15', '01', '32', 'SAN JUAN DE LURIGANCHO'),
(1436, '15', '01', '33', 'SAN JUAN DE MIRAFLORES'),
(1437, '15', '01', '34', 'SAN LUIS'),
(1438, '15', '01', '35', 'SAN MARTIN DE PORRES'),
(1439, '15', '01', '36', 'SAN MIGUEL'),
(1440, '15', '01', '37', 'SANTA ANITA'),
(1441, '15', '01', '38', 'SANTA MARIA DEL MAR'),
(1442, '15', '01', '39', 'SANTA ROSA'),
(1443, '15', '01', '40', 'SANTIAGO DE SURCO'),
(1444, '15', '01', '41', 'SURQUILLO'),
(1445, '15', '01', '42', 'VILLA EL SALVADOR'),
(1446, '15', '01', '43', 'VILLA MARIA DEL TRIUNFO'),
(1447, '15', '02', '00', 'BARRANCA'),
(1448, '15', '02', '01', 'BARRANCA'),
(1449, '15', '02', '02', 'PARAMONGA'),
(1450, '15', '02', '03', 'PATIVILCA'),
(1451, '15', '02', '04', 'SUPE'),
(1452, '15', '02', '05', 'SUPE PUERTO'),
(1453, '15', '03', '00', 'CAJATAMBO'),
(1454, '15', '03', '01', 'CAJATAMBO'),
(1455, '15', '03', '02', 'COPA'),
(1456, '15', '03', '03', 'GORGOR'),
(1457, '15', '03', '04', 'HUANCAPON'),
(1458, '15', '03', '05', 'MANAS'),
(1459, '15', '04', '00', 'CANTA'),
(1460, '15', '04', '01', 'CANTA'),
(1461, '15', '04', '02', 'ARAHUAY'),
(1462, '15', '04', '03', 'HUAMANTANGA'),
(1463, '15', '04', '04', 'HUAROS'),
(1464, '15', '04', '05', 'LACHAQUI'),
(1465, '15', '04', '06', 'SAN BUENAVENTURA'),
(1466, '15', '04', '07', 'SANTA ROSA DE QUIVES'),
(1467, '15', '05', '00', 'CAÑETE'),
(1468, '15', '05', '01', 'SAN VICENTE DE CAÑETE'),
(1469, '15', '05', '02', 'ASIA'),
(1470, '15', '05', '03', 'CALANGO'),
(1471, '15', '05', '04', 'CERRO AZUL'),
(1472, '15', '05', '05', 'CHILCA'),
(1473, '15', '05', '06', 'COAYLLO'),
(1474, '15', '05', '07', 'IMPERIAL'),
(1475, '15', '05', '08', 'LUNAHUANA'),
(1476, '15', '05', '09', 'MALA'),
(1477, '15', '05', '10', 'NUEVO IMPERIAL'),
(1478, '15', '05', '11', 'PACARAN'),
(1479, '15', '05', '12', 'QUILMANA'),
(1480, '15', '05', '13', 'SAN ANTONIO'),
(1481, '15', '05', '14', 'SAN LUIS'),
(1482, '15', '05', '15', 'SANTA CRUZ DE FLORES'),
(1483, '15', '05', '16', 'ZUÑIGA'),
(1484, '15', '06', '00', 'HUARAL'),
(1485, '15', '06', '01', 'HUARAL'),
(1486, '15', '06', '02', 'ATAVILLOS ALTO'),
(1487, '15', '06', '03', 'ATAVILLOS BAJO'),
(1488, '15', '06', '04', 'AUCALLAMA'),
(1489, '15', '06', '05', 'CHANCAY'),
(1490, '15', '06', '06', 'IHUARI'),
(1491, '15', '06', '07', 'LAMPIAN'),
(1492, '15', '06', '08', 'PACARAOS'),
(1493, '15', '06', '09', 'SAN MIGUEL DE ACOS'),
(1494, '15', '06', '10', 'SANTA CRUZ DE ANDAMARCA'),
(1495, '15', '06', '11', 'SUMBILCA'),
(1496, '15', '06', '12', 'VEINTISIETE DE NOVIEMBRE'),
(1497, '15', '07', '00', 'HUAROCHIRI'),
(1498, '15', '07', '01', 'MATUCANA'),
(1499, '15', '07', '02', 'ANTIOQUIA'),
(1500, '15', '07', '03', 'CALLAHUANCA'),
(1501, '15', '07', '04', 'CARAMPOMA'),
(1502, '15', '07', '05', 'CHICLA'),
(1503, '15', '07', '06', 'CUENCA'),
(1504, '15', '07', '07', 'HUACHUPAMPA'),
(1505, '15', '07', '08', 'HUANZA'),
(1506, '15', '07', '09', 'HUAROCHIRI'),
(1507, '15', '07', '10', 'LAHUAYTAMBO'),
(1508, '15', '07', '11', 'LANGA'),
(1509, '15', '07', '12', 'LARAOS'),
(1510, '15', '07', '13', 'MARIATANA'),
(1511, '15', '07', '14', 'RICARDO PALMA'),
(1512, '15', '07', '15', 'SAN ANDRES DE TUPICOCHA'),
(1513, '15', '07', '16', 'SAN ANTONIO'),
(1514, '15', '07', '17', 'SAN BARTOLOME'),
(1515, '15', '07', '18', 'SAN DAMIAN'),
(1516, '15', '07', '19', 'SAN JUAN DE IRIS'),
(1517, '15', '07', '20', 'SAN JUAN DE TANTARANCHE'),
(1518, '15', '07', '21', 'SAN LORENZO DE QUINTI'),
(1519, '15', '07', '22', 'SAN MATEO'),
(1520, '15', '07', '23', 'SAN MATEO DE OTAO'),
(1521, '15', '07', '24', 'SAN PEDRO DE CASTA'),
(1522, '15', '07', '25', 'SAN PEDRO DE HUANCAYRE'),
(1523, '15', '07', '26', 'SANGALLAYA'),
(1524, '15', '07', '27', 'SANTA CRUZ DE COCACHACRA'),
(1525, '15', '07', '28', 'SANTA EULALIA'),
(1526, '15', '07', '29', 'SANTIAGO DE ANCHUCAYA'),
(1527, '15', '07', '30', 'SANTIAGO DE TUNA'),
(1528, '15', '07', '31', 'SANTO DOMINGO DE LOS OLLEROS'),
(1529, '15', '07', '32', 'SURCO'),
(1530, '15', '08', '00', 'HUAURA'),
(1531, '15', '08', '01', 'HUACHO'),
(1532, '15', '08', '02', 'AMBAR'),
(1533, '15', '08', '03', 'CALETA DE CARQUIN'),
(1534, '15', '08', '04', 'CHECRAS'),
(1535, '15', '08', '05', 'HUALMAY'),
(1536, '15', '08', '06', 'HUAURA'),
(1537, '15', '08', '07', 'LEONCIO PRADO'),
(1538, '15', '08', '08', 'PACCHO'),
(1539, '15', '08', '09', 'SANTA LEONOR'),
(1540, '15', '08', '10', 'SANTA MARIA'),
(1541, '15', '08', '11', 'SAYAN'),
(1542, '15', '08', '12', 'VEGUETA'),
(1543, '15', '09', '00', 'OYON'),
(1544, '15', '09', '01', 'OYON'),
(1545, '15', '09', '02', 'ANDAJES'),
(1546, '15', '09', '03', 'CAUJUL'),
(1547, '15', '09', '04', 'COCHAMARCA'),
(1548, '15', '09', '05', 'NAVAN'),
(1549, '15', '09', '06', 'PACHANGARA'),
(1550, '15', '10', '00', 'YAUYOS'),
(1551, '15', '10', '01', 'YAUYOS'),
(1552, '15', '10', '02', 'ALIS'),
(1553, '15', '10', '03', 'AYAUCA'),
(1554, '15', '10', '04', 'AYAVIRI'),
(1555, '15', '10', '05', 'AZANGARO'),
(1556, '15', '10', '06', 'CACRA'),
(1557, '15', '10', '07', 'CARANIA'),
(1558, '15', '10', '08', 'CATAHUASI'),
(1559, '15', '10', '09', 'CHOCOS'),
(1560, '15', '10', '10', 'COCHAS'),
(1561, '15', '10', '11', 'COLONIA'),
(1562, '15', '10', '12', 'HONGOS'),
(1563, '15', '10', '13', 'HUAMPARA'),
(1564, '15', '10', '14', 'HUANCAYA'),
(1565, '15', '10', '15', 'HUANGASCAR'),
(1566, '15', '10', '16', 'HUANTAN'),
(1567, '15', '10', '17', 'HUAÑEC'),
(1568, '15', '10', '18', 'LARAOS'),
(1569, '15', '10', '19', 'LINCHA'),
(1570, '15', '10', '20', 'MADEAN'),
(1571, '15', '10', '21', 'MIRAFLORES'),
(1572, '15', '10', '22', 'OMAS'),
(1573, '15', '10', '23', 'PUTINZA'),
(1574, '15', '10', '24', 'QUINCHES'),
(1575, '15', '10', '25', 'QUINOCAY'),
(1576, '15', '10', '26', 'SAN JOAQUIN'),
(1577, '15', '10', '27', 'SAN PEDRO DE PILAS'),
(1578, '15', '10', '28', 'TANTA'),
(1579, '15', '10', '29', 'TAURIPAMPA'),
(1580, '15', '10', '30', 'TOMAS'),
(1581, '15', '10', '31', 'TUPE'),
(1582, '15', '10', '32', 'VIÑAC'),
(1583, '15', '10', '33', 'VITIS'),
(1584, '16', '00', '00', 'LORETO'),
(1585, '16', '01', '00', 'MAYNAS'),
(1586, '16', '01', '01', 'IQUITOS'),
(1587, '16', '01', '02', 'ALTO NANAY'),
(1588, '16', '01', '03', 'FERNANDO LORES'),
(1589, '16', '01', '04', 'INDIANA'),
(1590, '16', '01', '05', 'LAS AMAZONAS'),
(1591, '16', '01', '06', 'MAZAN'),
(1592, '16', '01', '07', 'NAPO'),
(1593, '16', '01', '08', 'PUNCHANA'),
(1594, '16', '01', '09', 'PUTUMAYO'),
(1595, '16', '01', '10', 'TORRES CAUSANA'),
(1596, '16', '01', '12', 'BELÉN'),
(1597, '16', '01', '13', 'SAN JUAN BAUTISTA'),
(1598, '16', '01', '14', 'TENIENTE MANUEL CLAVERO'),
(1599, '16', '02', '00', 'ALTO AMAZONAS'),
(1600, '16', '02', '01', 'YURIMAGUAS'),
(1601, '16', '02', '02', 'BALSAPUERTO'),
(1602, '16', '02', '05', 'JEBEROS'),
(1603, '16', '02', '06', 'LAGUNAS'),
(1604, '16', '02', '10', 'SANTA CRUZ'),
(1605, '16', '02', '11', 'TENIENTE CESAR LOPEZ ROJAS'),
(1606, '16', '03', '00', 'LORETO'),
(1607, '16', '03', '01', 'NAUTA'),
(1608, '16', '03', '02', 'PARINARI'),
(1609, '16', '03', '03', 'TIGRE'),
(1610, '16', '03', '04', 'TROMPETEROS'),
(1611, '16', '03', '05', 'URARINAS'),
(1612, '16', '04', '00', 'MARISCAL RAMON CASTILLA'),
(1613, '16', '04', '01', 'RAMON CASTILLA'),
(1614, '16', '04', '02', 'PEBAS'),
(1615, '16', '04', '03', 'YAVARI'),
(1616, '16', '04', '04', 'SAN PABLO'),
(1617, '16', '05', '00', 'REQUENA'),
(1618, '16', '05', '01', 'REQUENA'),
(1619, '16', '05', '02', 'ALTO TAPICHE'),
(1620, '16', '05', '03', 'CAPELO'),
(1621, '16', '05', '04', 'EMILIO SAN MARTIN'),
(1622, '16', '05', '05', 'MAQUIA'),
(1623, '16', '05', '06', 'PUINAHUA'),
(1624, '16', '05', '07', 'SAQUENA'),
(1625, '16', '05', '08', 'SOPLIN'),
(1626, '16', '05', '09', 'TAPICHE'),
(1627, '16', '05', '10', 'JENARO HERRERA'),
(1628, '16', '05', '11', 'YAQUERANA'),
(1629, '16', '06', '00', 'UCAYALI'),
(1630, '16', '06', '01', 'CONTAMANA'),
(1631, '16', '06', '02', 'INAHUAYA'),
(1632, '16', '06', '03', 'PADRE MARQUEZ'),
(1633, '16', '06', '04', 'PAMPA HERMOSA'),
(1634, '16', '06', '05', 'SARAYACU'),
(1635, '16', '06', '06', 'VARGAS GUERRA'),
(1636, '16', '07', '00', 'DATEM DEL MARAÑÓN'),
(1637, '16', '07', '01', 'BARRANCA'),
(1638, '16', '07', '02', 'CAHUAPANAS'),
(1639, '16', '07', '03', 'MANSERICHE'),
(1640, '16', '07', '04', 'MORONA'),
(1641, '16', '07', '05', 'PASTAZA'),
(1642, '16', '07', '06', 'ANDOAS'),
(1643, '16', '08', '00', 'PUTUMAYO'),
(1644, '16', '08', '01', 'PUTUMAYO'),
(1645, '16', '08', '02', 'ROSA PANDURO'),
(1646, '16', '08', '03', 'TENIENTE MANUEL CLAVERO'),
(1647, '16', '08', '04', 'YAGUAS'),
(1648, '17', '00', '00', 'MADRE DE DIOS'),
(1649, '17', '01', '00', 'TAMBOPATA'),
(1650, '17', '01', '01', 'TAMBOPATA'),
(1651, '17', '01', '02', 'INAMBARI'),
(1652, '17', '01', '03', 'LAS PIEDRAS'),
(1653, '17', '01', '04', 'LABERINTO'),
(1654, '17', '02', '00', 'MANU'),
(1655, '17', '02', '01', 'MANU'),
(1656, '17', '02', '02', 'FITZCARRALD'),
(1657, '17', '02', '03', 'MADRE DE DIOS'),
(1658, '17', '02', '04', 'HUEPETUHE'),
(1659, '17', '03', '00', 'TAHUAMANU'),
(1660, '17', '03', '01', 'IÑAPARI'),
(1661, '17', '03', '02', 'IBERIA'),
(1662, '17', '03', '03', 'TAHUAMANU'),
(1663, '18', '00', '00', 'MOQUEGUA'),
(1664, '18', '01', '00', 'MARISCAL NIETO'),
(1665, '18', '01', '01', 'MOQUEGUA'),
(1666, '18', '01', '02', 'CARUMAS'),
(1667, '18', '01', '03', 'CUCHUMBAYA'),
(1668, '18', '01', '04', 'SAMEGUA'),
(1669, '18', '01', '05', 'SAN CRISTOBAL'),
(1670, '18', '01', '06', 'TORATA'),
(1671, '18', '02', '00', 'GENERAL SANCHEZ CERRO'),
(1672, '18', '02', '01', 'OMATE'),
(1673, '18', '02', '02', 'CHOJATA'),
(1674, '18', '02', '03', 'COALAQUE'),
(1675, '18', '02', '04', 'ICHUÑA'),
(1676, '18', '02', '05', 'LA CAPILLA'),
(1677, '18', '02', '06', 'LLOQUE'),
(1678, '18', '02', '07', 'MATALAQUE'),
(1679, '18', '02', '08', 'PUQUINA'),
(1680, '18', '02', '09', 'QUINISTAQUILLAS'),
(1681, '18', '02', '10', 'UBINAS'),
(1682, '18', '02', '11', 'YUNGA'),
(1683, '18', '03', '00', 'ILO'),
(1684, '18', '03', '01', 'ILO'),
(1685, '18', '03', '02', 'EL ALGARROBAL'),
(1686, '18', '03', '03', 'PACOCHA'),
(1687, '19', '00', '00', 'PASCO'),
(1688, '19', '01', '00', 'PASCO'),
(1689, '19', '01', '01', 'CHAUPIMARCA'),
(1690, '19', '01', '02', 'HUACHON'),
(1691, '19', '01', '03', 'HUARIACA'),
(1692, '19', '01', '04', 'HUAYLLAY'),
(1693, '19', '01', '05', 'NINACACA'),
(1694, '19', '01', '06', 'PALLANCHACRA'),
(1695, '19', '01', '07', 'PAUCARTAMBO'),
(1696, '19', '01', '08', 'SAN FCO. DE ASÍS DE YARUSYACÁN'),
(1697, '19', '01', '09', 'SIMON BOLIVAR'),
(1698, '19', '01', '10', 'TICLACAYAN'),
(1699, '19', '01', '11', 'TINYAHUARCO'),
(1700, '19', '01', '12', 'VICCO'),
(1701, '19', '01', '13', 'YANACANCHA'),
(1702, '19', '02', '00', 'DANIEL ALCIDES CARRION'),
(1703, '19', '02', '01', 'YANAHUANCA'),
(1704, '19', '02', '02', 'CHACAYAN'),
(1705, '19', '02', '03', 'GOYLLARISQUIZGA'),
(1706, '19', '02', '04', 'PAUCAR'),
(1707, '19', '02', '05', 'SAN PEDRO DE PILLAO'),
(1708, '19', '02', '06', 'SANTA ANA DE TUSI'),
(1709, '19', '02', '07', 'TAPUC'),
(1710, '19', '02', '08', 'VILCABAMBA'),
(1711, '19', '03', '00', 'OXAPAMPA'),
(1712, '19', '03', '01', 'OXAPAMPA'),
(1713, '19', '03', '02', 'CHONTABAMBA'),
(1714, '19', '03', '03', 'HUANCABAMBA'),
(1715, '19', '03', '04', 'PALCAZU'),
(1716, '19', '03', '05', 'POZUZO'),
(1717, '19', '03', '06', 'PUERTO BERMUDEZ'),
(1718, '19', '03', '07', 'VILLA RICA'),
(1719, '19', '03', '08', 'CONSTITUCION'),
(1720, '20', '00', '00', 'PIURA'),
(1721, '20', '01', '00', 'PIURA'),
(1722, '20', '01', '01', 'PIURA'),
(1723, '20', '01', '04', 'CASTILLA'),
(1724, '20', '01', '05', 'CATACAOS'),
(1725, '20', '01', '07', 'CURA MORI'),
(1726, '20', '01', '08', 'EL TALLAN'),
(1727, '20', '01', '09', 'LA ARENA'),
(1728, '20', '01', '10', 'LA UNION'),
(1729, '20', '01', '11', 'LAS LOMAS'),
(1730, '20', '01', '14', 'TAMBO GRANDE'),
(1731, '20', '01', '15', 'VEINTISÉIS DE OCTUBRE'),
(1732, '20', '02', '00', 'AYABACA'),
(1733, '20', '02', '01', 'AYABACA'),
(1734, '20', '02', '02', 'FRIAS'),
(1735, '20', '02', '03', 'JILILI'),
(1736, '20', '02', '04', 'LAGUNAS'),
(1737, '20', '02', '05', 'MONTERO'),
(1738, '20', '02', '06', 'PACAIPAMPA'),
(1739, '20', '02', '07', 'PAIMAS'),
(1740, '20', '02', '08', 'SAPILLICA'),
(1741, '20', '02', '09', 'SICCHEZ'),
(1742, '20', '02', '10', 'SUYO'),
(1743, '20', '03', '00', 'HUANCABAMBA'),
(1744, '20', '03', '01', 'HUANCABAMBA'),
(1745, '20', '03', '02', 'CANCHAQUE'),
(1746, '20', '03', '03', 'EL CARMEN DE LA FRONTERA'),
(1747, '20', '03', '04', 'HUARMACA'),
(1748, '20', '03', '05', 'LALAQUIZ'),
(1749, '20', '03', '06', 'SAN MIGUEL DE EL FAIQUE'),
(1750, '20', '03', '07', 'SONDOR'),
(1751, '20', '03', '08', 'SONDORILLO'),
(1752, '20', '04', '00', 'MORROPON'),
(1753, '20', '04', '01', 'CHULUCANAS'),
(1754, '20', '04', '02', 'BUENOS AIRES'),
(1755, '20', '04', '03', 'CHALACO'),
(1756, '20', '04', '04', 'LA MATANZA'),
(1757, '20', '04', '05', 'MORROPON'),
(1758, '20', '04', '06', 'SALITRAL'),
(1759, '20', '04', '07', 'SAN JUAN DE BIGOTE'),
(1760, '20', '04', '08', 'SANTA CATALINA DE MOSSA'),
(1761, '20', '04', '09', 'SANTO DOMINGO'),
(1762, '20', '04', '10', 'YAMANGO'),
(1763, '20', '05', '00', 'PAITA'),
(1764, '20', '05', '01', 'PAITA'),
(1765, '20', '05', '02', 'AMOTAPE'),
(1766, '20', '05', '03', 'ARENAL'),
(1767, '20', '05', '04', 'COLAN'),
(1768, '20', '05', '05', 'LA HUACA'),
(1769, '20', '05', '06', 'TAMARINDO'),
(1770, '20', '05', '07', 'VICHAYAL'),
(1771, '20', '06', '00', 'SULLANA'),
(1772, '20', '06', '01', 'SULLANA'),
(1773, '20', '06', '02', 'BELLAVISTA'),
(1774, '20', '06', '03', 'IGNACIO ESCUDERO'),
(1775, '20', '06', '04', 'LANCONES'),
(1776, '20', '06', '05', 'MARCAVELICA'),
(1777, '20', '06', '06', 'MIGUEL CHECA'),
(1778, '20', '06', '07', 'QUERECOTILLO'),
(1779, '20', '06', '08', 'SALITRAL'),
(1780, '20', '07', '00', 'TALARA'),
(1781, '20', '07', '01', 'PARIÑAS'),
(1782, '20', '07', '02', 'EL ALTO'),
(1783, '20', '07', '03', 'LA BREA'),
(1784, '20', '07', '04', 'LOBITOS'),
(1785, '20', '07', '05', 'LOS ORGANOS'),
(1786, '20', '07', '06', 'MANCORA'),
(1787, '20', '08', '00', 'SECHURA'),
(1788, '20', '08', '01', 'SECHURA'),
(1789, '20', '08', '02', 'BELLAVISTA DE LA UNION'),
(1790, '20', '08', '03', 'BERNAL'),
(1791, '20', '08', '04', 'CRISTO NOS VALGA'),
(1792, '20', '08', '05', 'VICE'),
(1793, '20', '08', '06', 'RINCONADA LLICUAR'),
(1794, '21', '00', '00', 'PUNO'),
(1795, '21', '01', '00', 'PUNO'),
(1796, '21', '01', '01', 'PUNO'),
(1797, '21', '01', '02', 'ACORA'),
(1798, '21', '01', '03', 'AMANTANI'),
(1799, '21', '01', '04', 'ATUNCOLLA'),
(1800, '21', '01', '05', 'CAPACHICA'),
(1801, '21', '01', '06', 'CHUCUITO'),
(1802, '21', '01', '07', 'COATA'),
(1803, '21', '01', '08', 'HUATA'),
(1804, '21', '01', '09', 'MAÑAZO'),
(1805, '21', '01', '10', 'PAUCARCOLLA'),
(1806, '21', '01', '11', 'PICHACANI'),
(1807, '21', '01', '12', 'PLATERIA'),
(1808, '21', '01', '13', 'SAN ANTONIO'),
(1809, '21', '01', '14', 'TIQUILLACA'),
(1810, '21', '01', '15', 'VILQUE'),
(1811, '21', '02', '00', 'AZANGARO'),
(1812, '21', '02', '01', 'AZANGARO'),
(1813, '21', '02', '02', 'ACHAYA'),
(1814, '21', '02', '03', 'ARAPA'),
(1815, '21', '02', '04', 'ASILLO'),
(1816, '21', '02', '05', 'CAMINACA'),
(1817, '21', '02', '06', 'CHUPA'),
(1818, '21', '02', '07', 'JOSE DOMINGO CHOQUEHUANCA'),
(1819, '21', '02', '08', 'MUÑANI'),
(1820, '21', '02', '09', 'POTONI'),
(1821, '21', '02', '10', 'SAMAN'),
(1822, '21', '02', '11', 'SAN ANTON'),
(1823, '21', '02', '12', 'SAN JOSE'),
(1824, '21', '02', '13', 'SAN JUAN DE SALINAS'),
(1825, '21', '02', '14', 'SANTIAGO DE PUPUJA'),
(1826, '21', '02', '15', 'TIRAPATA'),
(1827, '21', '03', '00', 'CARABAYA'),
(1828, '21', '03', '01', 'MACUSANI'),
(1829, '21', '03', '02', 'AJOYANI'),
(1830, '21', '03', '03', 'AYAPATA'),
(1831, '21', '03', '04', 'COASA'),
(1832, '21', '03', '05', 'CORANI'),
(1833, '21', '03', '06', 'CRUCERO'),
(1834, '21', '03', '07', 'ITUATA'),
(1835, '21', '03', '08', 'OLLACHEA'),
(1836, '21', '03', '09', 'SAN GABAN'),
(1837, '21', '03', '10', 'USICAYOS'),
(1838, '21', '04', '00', 'CHUCUITO'),
(1839, '21', '04', '01', 'JULI'),
(1840, '21', '04', '02', 'DESAGUADERO'),
(1841, '21', '04', '03', 'HUACULLANI'),
(1842, '21', '04', '04', 'KELLUYO'),
(1843, '21', '04', '05', 'PISACOMA'),
(1844, '21', '04', '06', 'POMATA'),
(1845, '21', '04', '07', 'ZEPITA'),
(1846, '21', '05', '00', 'EL COLLAO'),
(1847, '21', '05', '01', 'ILAVE'),
(1848, '21', '05', '02', 'CAPASO'),
(1849, '21', '05', '03', 'PILCUYO'),
(1850, '21', '05', '04', 'SANTA ROSA'),
(1851, '21', '05', '05', 'CONDURIRI'),
(1852, '21', '06', '00', 'HUANCANE'),
(1853, '21', '06', '01', 'HUANCANE'),
(1854, '21', '06', '02', 'COJATA'),
(1855, '21', '06', '03', 'HUATASANI'),
(1856, '21', '06', '04', 'INCHUPALLA'),
(1857, '21', '06', '05', 'PUSI'),
(1858, '21', '06', '06', 'ROSASPATA'),
(1859, '21', '06', '07', 'TARACO'),
(1860, '21', '06', '08', 'VILQUE CHICO'),
(1861, '21', '07', '00', 'LAMPA'),
(1862, '21', '07', '01', 'LAMPA'),
(1863, '21', '07', '02', 'CABANILLA'),
(1864, '21', '07', '03', 'CALAPUJA'),
(1865, '21', '07', '04', 'NICASIO'),
(1866, '21', '07', '05', 'OCUVIRI'),
(1867, '21', '07', '06', 'PALCA'),
(1868, '21', '07', '07', 'PARATIA'),
(1869, '21', '07', '08', 'PUCARA'),
(1870, '21', '07', '09', 'SANTA LUCIA'),
(1871, '21', '07', '10', 'VILAVILA'),
(1872, '21', '08', '00', 'MELGAR'),
(1873, '21', '08', '01', 'AYAVIRI'),
(1874, '21', '08', '02', 'ANTAUTA'),
(1875, '21', '08', '03', 'CUPI'),
(1876, '21', '08', '04', 'LLALLI'),
(1877, '21', '08', '05', 'MACARI'),
(1878, '21', '08', '06', 'NUÑOA'),
(1879, '21', '08', '07', 'ORURILLO'),
(1880, '21', '08', '08', 'SANTA ROSA'),
(1881, '21', '08', '09', 'UMACHIRI'),
(1882, '21', '09', '00', 'MOHO'),
(1883, '21', '09', '01', 'MOHO'),
(1884, '21', '09', '02', 'CONIMA'),
(1885, '21', '09', '03', 'HUAYRAPATA'),
(1886, '21', '09', '04', 'TILALI'),
(1887, '21', '10', '00', 'SAN ANTONIO DE PUTINA'),
(1888, '21', '10', '01', 'PUTINA'),
(1889, '21', '10', '02', 'ANANEA'),
(1890, '21', '10', '03', 'PEDRO VILCA APAZA'),
(1891, '21', '10', '04', 'QUILCAPUNCU'),
(1892, '21', '10', '05', 'SINA'),
(1893, '21', '11', '00', 'SAN ROMAN'),
(1894, '21', '11', '01', 'JULIACA'),
(1895, '21', '11', '02', 'CABANA'),
(1896, '21', '11', '03', 'CABANILLAS'),
(1897, '21', '11', '04', 'CARACOTO'),
(1898, '21', '12', '00', 'SANDIA'),
(1899, '21', '12', '01', 'SANDIA'),
(1900, '21', '12', '02', 'CUYOCUYO'),
(1901, '21', '12', '03', 'LIMBANI'),
(1902, '21', '12', '04', 'PATAMBUCO'),
(1903, '21', '12', '05', 'PHARA'),
(1904, '21', '12', '06', 'QUIACA'),
(1905, '21', '12', '07', 'SAN JUAN DEL ORO'),
(1906, '21', '12', '08', 'YANAHUAYA'),
(1907, '21', '12', '09', 'ALTO INAMBARI'),
(1908, '21', '12', '10', 'SAN PEDRO DE PUTINA PUNCO'),
(1909, '21', '13', '00', 'YUNGUYO'),
(1910, '21', '13', '01', 'YUNGUYO'),
(1911, '21', '13', '02', 'ANAPIA'),
(1912, '21', '13', '03', 'COPANI'),
(1913, '21', '13', '04', 'CUTURAPI'),
(1914, '21', '13', '05', 'OLLARAYA'),
(1915, '21', '13', '06', 'TINICACHI'),
(1916, '21', '13', '07', 'UNICACHI'),
(1917, '22', '00', '00', 'SAN MARTIN'),
(1918, '22', '01', '00', 'MOYOBAMBA'),
(1919, '22', '01', '01', 'MOYOBAMBA'),
(1920, '22', '01', '02', 'CALZADA'),
(1921, '22', '01', '03', 'HABANA'),
(1922, '22', '01', '04', 'JEPELACIO'),
(1923, '22', '01', '05', 'SORITOR'),
(1924, '22', '01', '06', 'YANTALO'),
(1925, '22', '02', '00', 'BELLAVISTA'),
(1926, '22', '02', '01', 'BELLAVISTA'),
(1927, '22', '02', '02', 'ALTO BIAVO'),
(1928, '22', '02', '03', 'BAJO BIAVO'),
(1929, '22', '02', '04', 'HUALLAGA'),
(1930, '22', '02', '05', 'SAN PABLO'),
(1931, '22', '02', '06', 'SAN RAFAEL'),
(1932, '22', '03', '00', 'EL DORADO'),
(1933, '22', '03', '01', 'SAN JOSE DE SISA'),
(1934, '22', '03', '02', 'AGUA BLANCA'),
(1935, '22', '03', '03', 'SAN MARTIN'),
(1936, '22', '03', '04', 'SANTA ROSA'),
(1937, '22', '03', '05', 'SHATOJA'),
(1938, '22', '04', '00', 'HUALLAGA'),
(1939, '22', '04', '01', 'SAPOSOA'),
(1940, '22', '04', '02', 'ALTO SAPOSOA'),
(1941, '22', '04', '03', 'EL ESLABON'),
(1942, '22', '04', '04', 'PISCOYACU'),
(1943, '22', '04', '05', 'SACANCHE'),
(1944, '22', '04', '06', 'TINGO DE SAPOSOA'),
(1945, '22', '05', '00', 'LAMAS'),
(1946, '22', '05', '01', 'LAMAS'),
(1947, '22', '05', '02', 'ALONSO DE ALVARADO'),
(1948, '22', '05', '03', 'BARRANQUITA'),
(1949, '22', '05', '04', 'CAYNARACHI'),
(1950, '22', '05', '05', 'CUÑUMBUQUI'),
(1951, '22', '05', '06', 'PINTO RECODO'),
(1952, '22', '05', '07', 'RUMISAPA'),
(1953, '22', '05', '08', 'SAN ROQUE DE CUMBAZA'),
(1954, '22', '05', '09', 'SHANAO'),
(1955, '22', '05', '10', 'TABALOSOS'),
(1956, '22', '05', '11', 'ZAPATERO'),
(1957, '22', '06', '00', 'MARISCAL CACERES'),
(1958, '22', '06', '01', 'JUANJUI'),
(1959, '22', '06', '02', 'CAMPANILLA'),
(1960, '22', '06', '03', 'HUICUNGO'),
(1961, '22', '06', '04', 'PACHIZA'),
(1962, '22', '06', '05', 'PAJARILLO'),
(1963, '22', '07', '00', 'PICOTA'),
(1964, '22', '07', '01', 'PICOTA'),
(1965, '22', '07', '02', 'BUENOS AIRES'),
(1966, '22', '07', '03', 'CASPISAPA'),
(1967, '22', '07', '04', 'PILLUANA'),
(1968, '22', '07', '05', 'PUCACACA'),
(1969, '22', '07', '06', 'SAN CRISTOBAL'),
(1970, '22', '07', '07', 'SAN HILARION'),
(1971, '22', '07', '08', 'SHAMBOYACU'),
(1972, '22', '07', '09', 'TINGO DE PONASA'),
(1973, '22', '07', '10', 'TRES UNIDOS'),
(1974, '22', '08', '00', 'RIOJA'),
(1975, '22', '08', '01', 'RIOJA'),
(1976, '22', '08', '02', 'AWAJUN'),
(1977, '22', '08', '03', 'ELIAS SOPLIN VARGAS'),
(1978, '22', '08', '04', 'NUEVA CAJAMARCA'),
(1979, '22', '08', '05', 'PARDO MIGUEL'),
(1980, '22', '08', '06', 'POSIC'),
(1981, '22', '08', '07', 'SAN FERNANDO'),
(1982, '22', '08', '08', 'YORONGOS'),
(1983, '22', '08', '09', 'YURACYACU'),
(1984, '22', '09', '00', 'SAN MARTIN'),
(1985, '22', '09', '01', 'TARAPOTO'),
(1986, '22', '09', '02', 'ALBERTO LEVEAU'),
(1987, '22', '09', '03', 'CACATACHI'),
(1988, '22', '09', '04', 'CHAZUTA'),
(1989, '22', '09', '05', 'CHIPURANA'),
(1990, '22', '09', '06', 'EL PORVENIR'),
(1991, '22', '09', '07', 'HUIMBAYOC'),
(1992, '22', '09', '08', 'JUAN GUERRA'),
(1993, '22', '09', '09', 'LA BANDA DE SHILCAYO'),
(1994, '22', '09', '10', 'MORALES'),
(1995, '22', '09', '11', 'PAPAPLAYA'),
(1996, '22', '09', '12', 'SAN ANTONIO'),
(1997, '22', '09', '13', 'SAUCE'),
(1998, '22', '09', '14', 'SHAPAJA'),
(1999, '22', '10', '00', 'TOCACHE'),
(2000, '22', '10', '01', 'TOCACHE'),
(2001, '22', '10', '02', 'NUEVO PROGRESO'),
(2002, '22', '10', '03', 'POLVORA'),
(2003, '22', '10', '04', 'SHUNTE'),
(2004, '22', '10', '05', 'UCHIZA'),
(2005, '23', '00', '00', 'TACNA'),
(2006, '23', '01', '00', 'TACNA'),
(2007, '23', '01', '01', 'TACNA'),
(2008, '23', '01', '02', 'ALTO DE LA ALIANZA'),
(2009, '23', '01', '03', 'CALANA'),
(2010, '23', '01', '04', 'CIUDAD NUEVA'),
(2011, '23', '01', '05', 'INCLAN'),
(2012, '23', '01', '06', 'PACHIA'),
(2013, '23', '01', '07', 'PALCA'),
(2014, '23', '01', '08', 'POCOLLAY'),
(2015, '23', '01', '09', 'SAMA'),
(2016, '23', '01', '10', 'CORONEL GREGORIO ALBARRACÍN L'),
(2017, '23', '02', '00', 'CANDARAVE'),
(2018, '23', '02', '01', 'CANDARAVE'),
(2019, '23', '02', '02', 'CAIRANI'),
(2020, '23', '02', '03', 'CAMILACA'),
(2021, '23', '02', '04', 'CURIBAYA'),
(2022, '23', '02', '05', 'HUANUARA'),
(2023, '23', '02', '06', 'QUILAHUANI'),
(2024, '23', '03', '00', 'JORGE BASADRE'),
(2025, '23', '03', '01', 'LOCUMBA'),
(2026, '23', '03', '02', 'ILABAYA'),
(2027, '23', '03', '03', 'ITE'),
(2028, '23', '04', '00', 'TARATA'),
(2029, '23', '04', '01', 'TARATA'),
(2030, '23', '04', '02', 'CHUCATAMANI'),
(2031, '23', '04', '03', 'ESTIQUE'),
(2032, '23', '04', '04', 'ESTIQUE-PAMPA'),
(2033, '23', '04', '05', 'SITAJARA'),
(2034, '23', '04', '06', 'SUSAPAYA'),
(2035, '23', '04', '07', 'TARUCACHI'),
(2036, '23', '04', '08', 'TICACO'),
(2037, '24', '00', '00', 'TUMBES'),
(2038, '24', '01', '00', 'TUMBES'),
(2039, '24', '01', '01', 'TUMBES'),
(2040, '24', '01', '02', 'CORRALES'),
(2041, '24', '01', '03', 'LA CRUZ'),
(2042, '24', '01', '04', 'PAMPAS DE HOSPITAL'),
(2043, '24', '01', '05', 'SAN JACINTO'),
(2044, '24', '01', '06', 'SAN JUAN DE LA VIRGEN'),
(2045, '24', '02', '00', 'CONTRALMIRANTE VILLAR'),
(2046, '24', '02', '01', 'ZORRITOS'),
(2047, '24', '02', '02', 'CASITAS'),
(2048, '24', '02', '03', 'CANOAS DE PUNTA SAL'),
(2049, '24', '03', '00', 'ZARUMILLA'),
(2050, '24', '03', '01', 'ZARUMILLA'),
(2051, '24', '03', '02', 'AGUAS VERDES'),
(2052, '24', '03', '03', 'MATAPALO'),
(2053, '24', '03', '04', 'PAPAYAL'),
(2054, '25', '00', '00', 'UCAYALI'),
(2055, '25', '01', '00', 'CORONEL PORTILLO'),
(2056, '25', '01', '01', 'CALLARIA'),
(2057, '25', '01', '02', 'CAMPOVERDE'),
(2058, '25', '01', '03', 'IPARIA'),
(2059, '25', '01', '04', 'MASISEA'),
(2060, '25', '01', '05', 'YARINACOCHA'),
(2061, '25', '01', '06', 'NUEVA REQUENA'),
(2062, '25', '01', '07', 'MANANTAY'),
(2063, '25', '02', '00', 'ATALAYA'),
(2064, '25', '02', '01', 'RAYMONDI'),
(2065, '25', '02', '02', 'SEPAHUA'),
(2066, '25', '02', '03', 'TAHUANIA'),
(2067, '25', '02', '04', 'YURUA'),
(2068, '25', '03', '00', 'PADRE ABAD'),
(2069, '25', '03', '01', 'PADRE ABAD'),
(2070, '25', '03', '02', 'IRAZOLA'),
(2071, '25', '03', '03', 'CURIMANA'),
(2072, '25', '04', '00', 'PURUS'),
(2073, '25', '04', '01', 'PURUS'),
(2074, '99', '00', '00', 'EXTRANJERO'),
(2075, '99', '99', '00', 'EXTRANJERO'),
(2076, '99', '99', '99', 'EXTRANJERO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medida`
--

CREATE TABLE `unidades_medida` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `abreviatura` varchar(15) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `unidades_medida`
--

INSERT INTO `unidades_medida` (`id`, `id_empresa`, `nombre`, `abreviatura`, `estado`, `created_at`, `updated_at`) VALUES
(1, 12, 'Cajas', NULL, 1, '2026-07-08 06:53:14', '2026-07-08 06:53:14'),
(2, 12, 'Sacos', NULL, 1, '2026-07-08 06:53:14', '2026-07-08 06:53:14'),
(3, 12, 'Unidad', NULL, 1, '2026-07-08 06:53:14', '2026-07-08 06:53:14'),
(5, 12, 'Kilogramo', 'kg', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(6, 12, 'Litro', 'L', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(7, 12, 'Gramo', 'g', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(8, 12, 'Mililitro', 'mL', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(9, 12, 'Caja', 'cja', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(10, 12, 'Paquete', 'pqte', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(11, 12, 'Saco', 'saco', 1, '2026-07-10 18:16:11', '2026-07-10 18:16:11'),
(12, 0, 'CAJA POMAROLA ', 'CP', 1, '2026-09-08 20:20:57', '2026-09-08 20:20:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `num_doc` varchar(20) DEFAULT NULL,
  `usuario` varchar(200) DEFAULT NULL,
  `clave` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `nombres` varchar(200) DEFAULT NULL,
  `apellidos` varchar(200) DEFAULT NULL,
  `rubro` varchar(100) DEFAULT NULL,
  `sucursal` int(11) DEFAULT NULL,
  `telefono` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `token_reset` varchar(130) DEFAULT NULL,
  `estado` char(1) DEFAULT '1',
  `mensaje` varchar(220) DEFAULT NULL,
  `rotativo` smallint(6) DEFAULT 0,
  `fecha_inicio` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `funciones` varchar(255) NOT NULL,
  `id_ruta` int(11) DEFAULT NULL,
  `available_status` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL COMMENT 'Laravel Auth token',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `id_empresa`, `id_rol`, `num_doc`, `usuario`, `clave`, `email`, `nombres`, `apellidos`, `rubro`, `sucursal`, `telefono`, `foto`, `token_reset`, `estado`, `mensaje`, `rotativo`, `fecha_inicio`, `fecha_salida`, `funciones`, `id_ruta`, `available_status`, `remember_token`, `updated_at`, `created_at`) VALUES
(107, 12, 1, '32961853', '32961853', '$2y$12$iCZy/WDQGT28q3tPYsLgmetPbN3WHxS2osKTdl5ukQKMb4CA0wZ6C', 'zoegv2602@gmail.com', 'ZOILA PATRICIA', 'GUTIERREZ VELA', NULL, 1, '946423341', 'usuarios/fotos/01M20ZRH4F7GGEEVQAMSPW67PG.jpeg', NULL, '1', NULL, 0, '2024-01-01', '2030-12-31', '', NULL, 1, 'Tbi0FQ0vHrxZNXXeqk4GL27TjeVhDQSQLQvKi18WAad8VzEs4fn5NHJrMZP4', NULL, '2026-06-26 18:10:27'),
(108, 12, 8, '99887766', 'VICTOR', '$2y$12$mFVHHlIUaXwSqFMXdC.Db.E.2evF7WhCZTor7kZR0Y.Y2k8hcPIHW', 'vcanchari38@gmail.com', 'VICTOR RAUL', 'CANCHARI RIQUI', NULL, 1, '92670321', NULL, NULL, '1', NULL, 0, '2026-07-10', '2030-12-31', '', NULL, 1, NULL, NULL, NULL),
(110, 12, 5, '77425200', 'conta', '$2y$12$atNOstr8CGjhrS2ImtRkzODxFd3sPTIIMk8tPxwb5RNChbwQ46eLC', 'adan2025zapata@gmail.com', 'EMER RODRIGO', 'YARLEQUE ZAPATA', NULL, 1, '993321920', NULL, NULL, '1', NULL, 0, '2026-07-10', '2030-12-31', '', NULL, 1, NULL, NULL, NULL),
(111, 12, 7, '76165962', 'victor12', '$2y$12$8Hxmxh4C/foJXUz6FhqBVODz2meYQGi2haI1yggCrexTTl91qVxs6', 'vcanchari@gmail.com', 'Victor', 'Canchari', NULL, 1, '92670321', NULL, NULL, '1', NULL, 0, '2026-07-22', '2030-12-31', '', NULL, 1, 'gJKafJcHUuQaf0ueuly5YAo68wFDibmBWIi9lwBHvgQepAZemf0GGhV7W2aS', NULL, NULL),
(112, 12, 9, '71012821', '71012821', '$2y$12$tOT5kr9vfHyunxnGLtGSzOqtpmI8fK1nMM6DPDw8LkvpZmufyW2b2', 'choquevallejosmiguelangel@gmail.com', 'MIGUEL ANGEL', 'CHOQUE VALLEJOS', NULL, 1, '932348127', NULL, NULL, '1', NULL, 0, '2026-09-08', '2030-12-31', '', NULL, 1, NULL, NULL, NULL),
(113, 12, 3, '45006566', '45006566', '$2y$12$WwTI.0rCTaAz1RUz9bbJWOa7nDjMojjMPv5pwbVSxJSabqZq3z9O.', 'milagroselohas.a@gmail.com', 'ELOHA MILAGROS', 'SOTO AYALA', NULL, 1, '979941429', 'usuarios/fotos/01M20Y9SFGCBENN2ZRH89ZMA3E.jpg', NULL, '1', NULL, 0, '2026-09-08', '2030-12-31', '', NULL, 1, NULL, NULL, NULL),
(114, 12, 1, '32970115', '32970115', '$2y$12$oMJOg4Fn6W2Ot6CLZfYESOiTLGKvWQ88wixsV5pOTdo2WOGKZ9AZG', 'dorothymunoz24@gmail.com', 'SILVIA DOROTHY', 'MUÑOZ REGIS', NULL, 1, '32961853', NULL, NULL, '1', NULL, 0, '2026-09-08', '2030-12-31', '', NULL, 1, 'Zqt8FRnFbT97rDt2Caojj2o4xVXPwPoAFq8anq1TTB5SrVa7Jtcc0kgePdvM', NULL, NULL),
(115, 12, 1, '76165962', 'VICTORAUL', '$2y$12$WXf4dmMkTU91m8Teeg72FOp7MFORg7vcPUtk1aHQgvZUZGB.eG5la', NULL, 'VICTOR RAUL', 'CANCHARI RICKY', NULL, 1, '32970115', NULL, NULL, '1', NULL, 0, '2026-09-08', '2030-12-31', '', NULL, 1, 'O2wnO2bjOKDIDCzW6wrKHA6F0mjMIulflAkAfTgzsFOdMtJCjMkhCK55UPv5', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_tido` int(11) NOT NULL,
  `id_tipo_pago` int(11) DEFAULT NULL,
  `metodo_pago` varchar(20) DEFAULT NULL,
  `pago_referencia` varchar(60) DEFAULT NULL,
  `pago_voucher` varchar(255) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `dias_pagos` varchar(200) DEFAULT NULL,
  `direccion` varchar(220) NOT NULL,
  `serie` varchar(4) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `id_cliente` int(11) NOT NULL,
  `total` double(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` char(1) DEFAULT NULL,
  `enviado_sunat` char(1) DEFAULT NULL,
  `sunat_estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `sunat_mensaje` text DEFAULT NULL,
  `hash_cpe` varchar(100) DEFAULT NULL,
  `xml_ruta` varchar(255) DEFAULT NULL,
  `cdr_ruta` varchar(255) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `sucursal` int(11) DEFAULT NULL,
  `apli_igv` char(1) DEFAULT '1',
  `tipo_igv` varchar(12) NOT NULL DEFAULT 'gravado',
  `observacion` varchar(220) DEFAULT NULL,
  `igv` double(10,2) DEFAULT 0.18,
  `medoto_pago_id` int(11) DEFAULT NULL,
  `pagado` varchar(100) DEFAULT NULL,
  `is_segun_pago` char(1) DEFAULT NULL,
  `medoto_pago2_id` int(11) DEFAULT NULL,
  `pagado2` varchar(100) DEFAULT NULL,
  `moneda` int(11) DEFAULT 1,
  `cm_tc` varchar(100) DEFAULT NULL,
  `id_coti` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_tido`, `id_tipo_pago`, `metodo_pago`, `pago_referencia`, `pago_voucher`, `fecha_emision`, `fecha_vencimiento`, `dias_pagos`, `direccion`, `serie`, `numero`, `id_cliente`, `total`, `subtotal`, `estado`, `enviado_sunat`, `sunat_estado`, `sunat_mensaje`, `hash_cpe`, `xml_ruta`, `cdr_ruta`, `id_empresa`, `sucursal`, `apli_igv`, `tipo_igv`, `observacion`, `igv`, `medoto_pago_id`, `pagado`, `is_segun_pago`, `medoto_pago2_id`, `pagado2`, `moneda`, `cm_tc`, `id_coti`, `id_vendedor`) VALUES
(229, 6, 2, NULL, NULL, NULL, '2026-03-28', '2026-03-28', '', 'MDO SAN PEDRO PTO 4', 'NV01', 2945, 1239, 1500.00, 0.00, '2', '0', 'pendiente', NULL, NULL, NULL, NULL, 12, 1, '1', 'gravado', '', 0.18, 12, '', '1', 12, '', 1, '', 47127, 40),
(231, 6, 2, NULL, NULL, NULL, '2026-04-16', '2026-04-16', '', 'RIMAC', 'NV01', 2945, 2497, 340.00, 0.00, '2', '0', 'pendiente', NULL, NULL, NULL, NULL, 12, 1, '1', 'gravado', '', 0.18, 12, '', '1', 12, '', 1, '', 49252, 40),
(233, 1, 2, NULL, NULL, NULL, '2026-06-25', '2026-06-25', '', '1', 'B001', 596, 1763, 2288.30, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'HrfOwLKNNiYij8CFaFC1isbR6jM=', 'sunat/xml/20000000001/20000000001-03-B001-596.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-596.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 46559', 349.06, NULL, '0', NULL, NULL, NULL, 1, NULL, 48324, 40),
(234, 1, 2, NULL, NULL, NULL, '2026-06-26', '2026-06-26', '', '1', 'B001', 597, 4, 291.10, 0.00, '1', '0', 'pendiente', NULL, NULL, NULL, NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 39032', 44.41, NULL, '0', NULL, NULL, NULL, 1, NULL, 40777, 40),
(236, 1, 1, NULL, NULL, NULL, '2026-06-26', '2026-06-26', NULL, '-', 'B001', 598, 2516, 74.00, 62.71, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'E1RLMNq3nrC7aeN5wLMloonNGeg=', 'sunat/xml/20000000001/20000000001-03-B001-598.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-598.zip', 12, 1, '1', 'gravado', NULL, 0.18, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 40),
(237, 1, 1, NULL, NULL, NULL, '2026-07-09', '2026-07-09', NULL, '-', 'B001', 599, 2516, 125.00, 105.93, '0', '1', 'aceptado', 'Aceptado por SUNAT.', '9A/kC1N/O0BZhgRfWoZv5H3kMUI=', 'sunat/xml/20000000001/20000000001-03-B001-599.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-599.zip', 12, 1, '1', 'gravado', NULL, 19.07, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 107),
(238, 6, 1, NULL, NULL, NULL, '2026-07-09', '2026-07-09', NULL, '1adadawda', 'NV01', 2949, 2517, 125.00, 105.93, '1', '0', 'pendiente', NULL, NULL, NULL, NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2948', 19.07, NULL, NULL, NULL, NULL, NULL, 1, NULL, 51471, 107),
(239, 1, 1, NULL, NULL, NULL, '2026-07-09', '2026-07-09', NULL, '-', 'B001', 600, 2516, 125.00, 105.93, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'aB9Y0FfbsawI87IgW/VLn8aEdj8=', 'sunat/xml/20000000001/20000000001-03-B001-600.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-600.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2947', 19.07, NULL, NULL, NULL, NULL, NULL, 1, NULL, 51470, 107),
(240, 6, 1, 'EFECTIVO', NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'NV01', 2970, 2537, 756.00, 640.68, '1', '0', 'pendiente', NULL, NULL, NULL, NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2969', 115.32, NULL, NULL, NULL, NULL, NULL, 1, NULL, 51491, 107),
(241, 6, 1, 'EFECTIVO', NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'NV01', 2971, 2536, 13.00, 11.02, '1', '0', 'pendiente', NULL, NULL, NULL, NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2968', 1.98, NULL, NULL, NULL, NULL, NULL, 1, NULL, 51490, 107),
(242, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 601, 2518, 84.00, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', '49qcfOuyEYFHlZ1mba1B89jXth8=', 'sunat/xml/20000000001/20000000001-03-B001-601.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-601.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2950', 12.81, NULL, '0', NULL, NULL, NULL, 1, NULL, 51472, 108),
(243, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 602, 2519, 649.50, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', '3P7jW/pPGfjn6eRfR8CBiHBFrn8=', 'sunat/xml/20000000001/20000000001-03-B001-602.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-602.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2951', 99.08, NULL, '0', NULL, NULL, NULL, 1, NULL, 51473, 108),
(244, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 603, 2520, 2109.50, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'dMA/7RYLegV52GhZ9c4mqV+umrE=', 'sunat/xml/20000000001/20000000001-03-B001-603.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-603.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2952', 321.79, NULL, '0', NULL, NULL, NULL, 1, NULL, 51474, 108),
(245, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 604, 2521, 32.50, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', '23/uqignkghHJ0pMmWm6G1BsBWA=', 'sunat/xml/20000000001/20000000001-03-B001-604.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-604.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2953', 4.96, NULL, '0', NULL, NULL, NULL, 1, NULL, 51475, 108),
(246, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 605, 2522, 1386.00, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', '+vLjrvVLY1aeoPNIDcwf462QNzs=', 'sunat/xml/20000000001/20000000001-03-B001-605.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-605.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2954', 211.42, NULL, '0', NULL, NULL, NULL, 1, NULL, 51476, 108),
(247, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 606, 2523, 923.00, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', '5+pNOdgSzp/bO0c3VlQoj+OuLDU=', 'sunat/xml/20000000001/20000000001-03-B001-606.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-606.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2955', 140.80, NULL, '0', NULL, NULL, NULL, 1, NULL, 51477, 108),
(248, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 607, 2524, 196.00, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'ZGEH9wEbfWpue92jtqT67jotafc=', 'sunat/xml/20000000001/20000000001-03-B001-607.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-607.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2956', 29.90, NULL, '0', NULL, NULL, NULL, 1, NULL, 51478, 108),
(249, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 608, 2525, 883.50, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'cIGSCQKwcNlcqSQvuvA61Bomhes=', 'sunat/xml/20000000001/20000000001-03-B001-608.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-608.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2957', 134.77, NULL, '0', NULL, NULL, NULL, 1, NULL, 51479, 108),
(250, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 609, 2526, 1031.00, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', '8viYDS9NgsT1IWbYKorWK7jLKHs=', 'sunat/xml/20000000001/20000000001-03-B001-609.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-609.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2958', 157.27, NULL, '0', NULL, NULL, NULL, 1, NULL, 51480, 108),
(251, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 610, 2527, 336.00, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'sgiiYkTCthECFyQ6VTjl/HRvcUA=', 'sunat/xml/20000000001/20000000001-03-B001-610.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-610.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2959', 51.25, NULL, '0', NULL, NULL, NULL, 1, NULL, 51481, 108),
(252, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 611, 2528, 273.00, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'rT2z2VbVwZ3X3dKPR3/sJRLou40=', 'sunat/xml/20000000001/20000000001-03-B001-611.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-611.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2960', 41.64, NULL, '0', NULL, NULL, NULL, 1, NULL, 51482, 108),
(253, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 612, 2529, 835.00, 0.00, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'TPOo2ND758wv67uMGZZWJJdQIDI=', 'sunat/xml/20000000001/20000000001-03-B001-612.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-612.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2961', 127.37, NULL, '0', NULL, NULL, NULL, 1, NULL, 51483, 108),
(254, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 613, 2530, 480.00, 0.00, '1', '0', 'rechazado', 'Rechazado por SUNAT.', 'a0VLBgFzyCxCrpP+ZK8ZNzLhJ10=', 'sunat/xml/20000000001/20000000001-03-B001-613.xml', NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2962', 73.22, NULL, '0', NULL, NULL, NULL, 1, NULL, 51484, 108),
(255, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 614, 2531, 1551.00, 0.00, '1', '0', 'rechazado', 'Rechazado por SUNAT.', 'VwIMNSfjZY6BTbQjahCCF5/6Wwk=', 'sunat/xml/20000000001/20000000001-03-B001-614.xml', NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2963', 236.59, NULL, '0', NULL, NULL, NULL, 1, NULL, 51485, 108),
(256, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 615, 2532, 1694.00, 0.00, '1', '0', 'rechazado', 'Rechazado por SUNAT.', 'Inw7HKuk/aAYHFRwW/UrK+fTuK0=', 'sunat/xml/20000000001/20000000001-03-B001-615.xml', NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2964', 258.41, NULL, '0', NULL, NULL, NULL, 1, NULL, 51486, 108),
(257, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 616, 2533, 336.00, 0.00, '1', '0', 'rechazado', 'Rechazado por SUNAT.', 'z5Nqir/oEjln/IdAkIQc2cVVfK0=', 'sunat/xml/20000000001/20000000001-03-B001-616.xml', NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2965', 51.25, NULL, '0', NULL, NULL, NULL, 1, NULL, 51487, 108),
(258, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 617, 2534, 508.50, 0.00, '1', '0', 'rechazado', 'Rechazado por SUNAT.', '15JtRAIOcp2FA+8EY1ErdOd05LU=', 'sunat/xml/20000000001/20000000001-03-B001-617.xml', NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2966', 77.57, NULL, '0', NULL, NULL, NULL, 1, NULL, 51488, 108),
(259, 1, 1, NULL, NULL, NULL, '2026-07-10', '2026-07-10', NULL, '1', 'B001', 618, 2535, 2162.00, 0.00, '1', '0', 'rechazado', 'Rechazado por SUNAT.', 'TNtyzyon/VLmQA/7iaUMgBmPOIU=', 'sunat/xml/20000000001/20000000001-03-B001-618.xml', NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2967', 329.80, NULL, '0', NULL, NULL, NULL, 1, NULL, 51489, 108),
(260, 1, 1, 'EFECTIVO', NULL, NULL, '2026-07-10', '2026-07-10', NULL, 'PSJ.INCA ROCA MZ. 131 LT.33', 'B001', 619, 2538, 1292.00, 1094.92, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'ryJp0SjkPmoQ/ECLT2Tl7jiXT+s=', 'sunat/xml/20000000001/20000000001-03-B001-619.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-619.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2972', 197.08, NULL, NULL, NULL, NULL, NULL, 1, NULL, 51492, 108),
(261, 1, 2, NULL, NULL, NULL, '2026-07-10', '2026-07-16', NULL, 'sdcsdcsd', 'B001', 620, 2516, 1800.00, 1525.42, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'Q7YBbU7RUOMIBpYjBx7RsxraWD0=', 'sunat/xml/20000000001/20000000001-03-B001-620.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-620.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2973', 274.58, NULL, NULL, NULL, NULL, NULL, 1, NULL, 51493, 108),
(262, 1, 1, 'BILLETERA|1', '66322541', 'vouchers/01KX56K9YYMR5HZG3SFKVS97VN.png', '2026-07-10', '2026-07-10', NULL, 'sdcsdcsd', 'B001', 621, 2516, 1000.00, 847.46, '1', '1', 'aceptado', 'Aceptado por SUNAT.', 'eKkpH/ZldJt8OsrDvKr3zAHKcyE=', 'sunat/xml/20000000001/20000000001-03-B001-621.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-621.zip', 12, 1, '1', 'gravado', 'Convertido de cotización N° 2974', 152.54, NULL, NULL, NULL, NULL, NULL, 1, NULL, 51494, 108),
(263, 6, 1, 'CUENTA|3', 'adadadadawdaw', 'vouchers/01KX571WV6FFBKWA2HJXPWA1M1.png', '2026-07-10', '2026-07-10', NULL, 'sdcsdcsd', 'NV01', 2976, 2516, 960.00, 813.56, '1', '0', 'pendiente', NULL, NULL, NULL, NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2975', 146.44, NULL, NULL, NULL, NULL, NULL, 1, NULL, 51495, 108),
(264, 1, 1, 'EFECTIVO', NULL, NULL, '2026-07-22', '2026-07-22', NULL, 'sdcsdcsd', 'B001', 622, 2516, 157.00, 133.05, '0', '1', 'aceptado', 'Aceptado por SUNAT.', '8MO6wqrGPIz5JxjvB7vp5UlHjF4=', 'sunat/xml/20000000001/20000000001-03-B001-622.xml', 'sunat/cdr/20000000001/R-20000000001-03-B001-622.zip', 12, 1, '1', 'gravado', NULL, 23.95, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 107),
(265, 6, 2, NULL, NULL, NULL, '2026-07-22', '2026-07-24', NULL, 'sdcsdcsd', 'NV01', 2979, 2516, 45.00, 38.14, '1', '0', 'pendiente', NULL, NULL, NULL, NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2978', 6.86, NULL, NULL, NULL, NULL, NULL, 1, NULL, 51497, 107),
(266, 6, 1, 'EFECTIVO', NULL, NULL, '2026-07-22', '2026-07-22', NULL, 'sdcsdcsd', 'NV01', 2980, 2516, 45.00, 38.14, '1', '0', 'pendiente', NULL, NULL, NULL, NULL, 12, 1, '1', 'gravado', 'Convertido de cotización N° 2977', 6.86, NULL, NULL, NULL, NULL, NULL, 1, NULL, 51496, 107),
(267, 1, 1, 'EFECTIVO', NULL, NULL, '2026-07-22', '2026-07-22', NULL, 'sdcsdcsd', 'B001', 623, 2516, 45.00, 38.14, '1', '0', 'pendiente', 'XML generado, pendiente de envío.', 'CjqTBSKJe0SxBiPYc51JpEke+jg=', 'sunat/xml/20000000001/20000000001-03-B001-623.xml', NULL, 12, 1, '1', 'gravado', NULL, 6.86, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 107);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_pagos`
--

CREATE TABLE `ventas_pagos` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `metodo_pago` varchar(255) DEFAULT NULL,
  `monto` varchar(255) DEFAULT NULL,
  `npago` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_referencias`
--

CREATE TABLE `ventas_referencias` (
  `id_venta` int(11) NOT NULL,
  `id_referencia` int(11) NOT NULL,
  `id_motivo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_servicios`
--

CREATE TABLE `ventas_servicios` (
  `id_venta` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `descripcion` varchar(245) NOT NULL,
  `monto` double(8,2) NOT NULL,
  `cantidad` double(9,2) NOT NULL,
  `codsunat` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_sunat`
--

CREATE TABLE `ventas_sunat` (
  `id_venta` int(11) NOT NULL,
  `hash` varchar(45) DEFAULT NULL,
  `nombre_xml` varchar(45) DEFAULT NULL,
  `qr_data` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `ventas_sunat`
--

INSERT INTO `ventas_sunat` (`id_venta`, `hash`, `nombre_xml`, `qr_data`) VALUES
(229, '-', '-', '-'),
(231, '-', '-', '-');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_adicional`
--

CREATE TABLE `venta_adicional` (
  `id` int(11) NOT NULL,
  `id_venta` varchar(255) DEFAULT NULL,
  `cuota` varchar(255) DEFAULT NULL,
  `porcentaje` varchar(255) DEFAULT NULL,
  `monto` varchar(255) DEFAULT NULL,
  `neto` varchar(255) DEFAULT NULL,
  `leyenda` varchar(255) DEFAULT NULL,
  `cuenta` varchar(255) DEFAULT NULL,
  `bien` varchar(255) DEFAULT NULL,
  `medio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_anexo`
--

CREATE TABLE `venta_anexo` (
  `idventa` int(11) NOT NULL,
  `texto` varchar(245) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_cuotas`
--

CREATE TABLE `venta_cuotas` (
  `id` int(11) NOT NULL,
  `id_venta` varchar(255) DEFAULT NULL,
  `ncuota` varchar(255) DEFAULT NULL,
  `fecha` varchar(255) DEFAULT NULL,
  `monto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_pagos`
--

CREATE TABLE `venta_pagos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_dias_venta` int(11) DEFAULT NULL,
  `metodo_pago` varchar(40) NOT NULL DEFAULT 'EFECTIVO',
  `monto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `referencia` varchar(60) DEFAULT NULL,
  `comprobantes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`comprobantes`)),
  `id_movimiento_caja` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  ADD PRIMARY KEY (`id_almacen`) USING BTREE,
  ADD KEY `almacenes_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `arqueos_diarios`
--
ALTER TABLE `arqueos_diarios`
  ADD PRIMARY KEY (`arqueo_id`) USING BTREE,
  ADD KEY `idx_fecha` (`fecha_arqueo`) USING BTREE,
  ADD KEY `idx_empresa` (`id_empresa`,`sucursal`) USING BTREE,
  ADD KEY `idx_vendedor` (`vendedor_id`) USING BTREE;

--
-- Indices de la tabla `arqueo_detalle`
--
ALTER TABLE `arqueo_detalle`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `arqueo_efectivo_detalle`
--
ALTER TABLE `arqueo_efectivo_detalle`
  ADD PRIMARY KEY (`detalle_id`) USING BTREE,
  ADD KEY `idx_arqueo` (`arqueo_id`) USING BTREE;

--
-- Indices de la tabla `arqueo_pagos_digitales`
--
ALTER TABLE `arqueo_pagos_digitales`
  ADD PRIMARY KEY (`pago_digital_id`) USING BTREE,
  ADD KEY `idx_arqueo` (`arqueo_id`) USING BTREE,
  ADD KEY `idx_tipo_pago` (`tipo_pago`) USING BTREE;

--
-- Indices de la tabla `asientos_contables`
--
ALTER TABLE `asientos_contables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asientos_contables_numero_unique` (`numero`);

--
-- Indices de la tabla `asientos_detalle`
--
ALTER TABLE `asientos_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asientos_detalle_asiento_id_foreign` (`asiento_id`),
  ADD KEY `asientos_detalle_plan_cuenta_id_foreign` (`plan_cuenta_id`);

--
-- Indices de la tabla `audits`
--
ALTER TABLE `audits`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `bancos`
--
ALTER TABLE `bancos`
  ADD PRIMARY KEY (`id_banco`) USING BTREE,
  ADD KEY `bancos_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `billeteras_digitales`
--
ALTER TABLE `billeteras_digitales`
  ADD PRIMARY KEY (`id_billetera`) USING BTREE,
  ADD KEY `billeteras_digitales_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `billetera_tipos`
--
ALTER TABLE `billetera_tipos`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`) USING BTREE;

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`) USING BTREE;

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `caja_aperturas`
--
ALTER TABLE `caja_aperturas`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `caja_aperturas_id_caja_foreign` (`id_caja`) USING BTREE;

--
-- Indices de la tabla `caja_apertura_detalles`
--
ALTER TABLE `caja_apertura_detalles`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `caja_apertura_detalles_id_apertura_foreign` (`id_apertura`) USING BTREE;

--
-- Indices de la tabla `caja_chica`
--
ALTER TABLE `caja_chica`
  ADD PRIMARY KEY (`caja_chica_id`) USING BTREE,
  ADD KEY `id_caja_empresa` (`id_caja_empresa`) USING BTREE;

--
-- Indices de la tabla `caja_cierre_deudas`
--
ALTER TABLE `caja_cierre_deudas`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `caja_cierre_deudas_id_cierre_index` (`id_cierre`) USING BTREE,
  ADD KEY `caja_cierre_deudas_id_usuario_estado_index` (`id_usuario`,`estado`) USING BTREE;

--
-- Indices de la tabla `caja_empresa`
--
ALTER TABLE `caja_empresa`
  ADD PRIMARY KEY (`caja_id`) USING BTREE;

--
-- Indices de la tabla `caja_instrumentos`
--
ALTER TABLE `caja_instrumentos`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `ci_uniq` (`id_caja`,`instrumento_tipo`,`instrumento_id`) USING BTREE;

--
-- Indices de la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`) USING BTREE,
  ADD KEY `categorias_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `cierre_caja`
--
ALTER TABLE `cierre_caja`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`) USING BTREE,
  ADD KEY `fk_clientes_empresas_idx` (`id_empresa`) USING BTREE,
  ADD KEY `idx_cli_empresa` (`id_empresa`) USING BTREE,
  ADD KEY `idx_cli_documento` (`documento`) USING BTREE;

--
-- Indices de la tabla `cliente_venta`
--
ALTER TABLE `cliente_venta`
  ADD PRIMARY KEY (`id_cliente`) USING BTREE;

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id_compra`) USING BTREE,
  ADD KEY `id_empresa` (`id_empresa`) USING BTREE,
  ADD KEY `id_tipo_pago` (`id_tipo_pago`) USING BTREE,
  ADD KEY `id_tido` (`id_tido`) USING BTREE,
  ADD KEY `id_proveedor` (`id_proveedor`) USING BTREE;

--
-- Indices de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD PRIMARY KEY (`cotizacion_id`) USING BTREE,
  ADD KEY `id_tido` (`id_tido`) USING BTREE,
  ADD KEY `id_tipo_pago` (`id_tipo_pago`) USING BTREE,
  ADD KEY `id_cliente` (`id_cliente`) USING BTREE,
  ADD KEY `idx_coti_empresa_estado` (`id_empresa`,`estado`) USING BTREE;

--
-- Indices de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  ADD PRIMARY KEY (`id_cuenta`) USING BTREE,
  ADD KEY `cuentas_bancarias_id_banco_foreign` (`id_banco`) USING BTREE,
  ADD KEY `cuentas_bancarias_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `cuotas_cotizacion`
--
ALTER TABLE `cuotas_cotizacion`
  ADD PRIMARY KEY (`cuota_coti_id`) USING BTREE,
  ADD KEY `id_coti` (`id_coti`) USING BTREE,
  ADD KEY `id_usuario` (`id_usuario`) USING BTREE;

--
-- Indices de la tabla `cxc_abonos`
--
ALTER TABLE `cxc_abonos`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `cxc_abonos_id_dias_venta_index` (`id_dias_venta`) USING BTREE,
  ADD KEY `cxc_abonos_id_venta_index` (`id_venta`) USING BTREE;

--
-- Indices de la tabla `devoluciones_nv`
--
ALTER TABLE `devoluciones_nv`
  ADD PRIMARY KEY (`id_devolucion`) USING BTREE,
  ADD KEY `id_producto` (`id_producto`) USING BTREE,
  ADD KEY `id_usuario` (`id_usuario`) USING BTREE,
  ADD KEY `id_venta` (`id_venta`) USING BTREE;

--
-- Indices de la tabla `dias_compras`
--
ALTER TABLE `dias_compras`
  ADD PRIMARY KEY (`dias_compra_id`) USING BTREE,
  ADD KEY `id_compra` (`id_compra`) USING BTREE;

--
-- Indices de la tabla `dias_ventas`
--
ALTER TABLE `dias_ventas`
  ADD PRIMARY KEY (`dias_venta_id`) USING BTREE,
  ADD KEY `id_venta` (`id_venta`) USING BTREE,
  ADD KEY `id_usuario` (`id_usuario`) USING BTREE;

--
-- Indices de la tabla `documentos_empresas`
--
ALTER TABLE `documentos_empresas`
  ADD KEY `fk_empresas_has_documentos_sunat_documentos_sunat1_idx` (`id_tido`) USING BTREE,
  ADD KEY `fk_empresas_has_documentos_sunat_empresas1_idx` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `documentos_sunat`
--
ALTER TABLE `documentos_sunat`
  ADD PRIMARY KEY (`id_tido`) USING BTREE;

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING BTREE;

--
-- Indices de la tabla `guia_detalles`
--
ALTER TABLE `guia_detalles`
  ADD PRIMARY KEY (`guia_detalle_id`) USING BTREE,
  ADD KEY `id_guia` (`id_guia`) USING BTREE;

--
-- Indices de la tabla `guia_detalle_transporte`
--
ALTER TABLE `guia_detalle_transporte`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `guia_remision`
--
ALTER TABLE `guia_remision`
  ADD PRIMARY KEY (`id_guia_remision`) USING BTREE,
  ADD KEY `fk_guia_remision_ventas1_idx` (`id_venta`) USING BTREE;

--
-- Indices de la tabla `guia_sunat`
--
ALTER TABLE `guia_sunat`
  ADD PRIMARY KEY (`id_guia`) USING BTREE;

--
-- Indices de la tabla `guia_transporte`
--
ALTER TABLE `guia_transporte`
  ADD PRIMARY KEY (`id_guia_remision`) USING BTREE,
  ADD KEY `fk_guia_remision_ventas1_idx` (`id_venta`) USING BTREE;

--
-- Indices de la tabla `ingreso_egreso`
--
ALTER TABLE `ingreso_egreso`
  ADD PRIMARY KEY (`intercambio_id`) USING BTREE,
  ADD KEY `id_usuario` (`id_usuario`) USING BTREE,
  ADD KEY `id_producto` (`id_producto`) USING BTREE;

--
-- Indices de la tabla `inventario_movimientos`
--
ALTER TABLE `inventario_movimientos`
  ADD PRIMARY KEY (`id_movimiento`) USING BTREE,
  ADD KEY `inventario_movimientos_id_empresa_index` (`id_empresa`) USING BTREE,
  ADD KEY `inventario_movimientos_id_producto_index` (`id_producto`) USING BTREE;

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `jobs_queue_index` (`queue`) USING BTREE;

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id_marca`) USING BTREE,
  ADD KEY `marcas_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `mes`
--
ALTER TABLE `mes`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id_metodo_pago`) USING BTREE;

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`) USING BTREE,
  ADD KEY `model_has_permissions_model_index` (`model_id`,`model_type`) USING BTREE;

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`) USING BTREE,
  ADD KEY `model_has_roles_model_index` (`model_id`,`model_type`) USING BTREE;

--
-- Indices de la tabla `motivos_movimiento`
--
ALTER TABLE `motivos_movimiento`
  ADD PRIMARY KEY (`id_motivo`) USING BTREE,
  ADD KEY `motivos_movimiento_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `motivo_documento`
--
ALTER TABLE `motivo_documento`
  ADD PRIMARY KEY (`id_motivo`) USING BTREE,
  ADD KEY `fk_motivo_documento_documentos_sunat1_idx` (`id_tido`) USING BTREE;

--
-- Indices de la tabla `notas_electronicas`
--
ALTER TABLE `notas_electronicas`
  ADD PRIMARY KEY (`nota_id`) USING BTREE,
  ADD KEY `tido` (`tido`) USING BTREE,
  ADD KEY `id_venta` (`id_venta`) USING BTREE;

--
-- Indices de la tabla `notas_electronicas_sunat`
--
ALTER TABLE `notas_electronicas_sunat`
  ADD PRIMARY KEY (`id_notas_electronicas`) USING BTREE;

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`) USING BTREE;

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`) USING BTREE,
  ADD KEY `pat_tokenable_index` (`tokenable_type`,`tokenable_id`) USING BTREE;

--
-- Indices de la tabla `plan_cuentas`
--
ALTER TABLE `plan_cuentas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plan_cuentas_codigo_unique` (`codigo`),
  ADD KEY `plan_cuentas_padre_id_foreign` (`padre_id`);

--
-- Indices de la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `presentaciones_id_empresa_nombre_unique` (`id_empresa`,`nombre`) USING BTREE;

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id_prestamo`) USING BTREE,
  ADD KEY `prestamos_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `prestamo_detalle`
--
ALTER TABLE `prestamo_detalle`
  ADD PRIMARY KEY (`id_detalle`) USING BTREE,
  ADD KEY `prestamo_detalle_id_prestamo_index` (`id_prestamo`) USING BTREE;

--
-- Indices de la tabla `prestamo_devoluciones`
--
ALTER TABLE `prestamo_devoluciones`
  ADD PRIMARY KEY (`id_devolucion`) USING BTREE,
  ADD KEY `prestamo_devoluciones_id_prestamo_index` (`id_prestamo`) USING BTREE;

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`) USING BTREE,
  ADD KEY `fk_productos_empresas1_idx` (`id_empresa`) USING BTREE,
  ADD KEY `idx_prod_empresa_estado` (`id_empresa`,`estado`) USING BTREE,
  ADD KEY `idx_prod_barra` (`cod_barra`) USING BTREE,
  ADD KEY `idx_prod_codigo` (`codigo`) USING BTREE;

--
-- Indices de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  ADD PRIMARY KEY (`id_producto_venta`) USING BTREE,
  ADD KEY `id_producto` (`id_producto`) USING BTREE,
  ADD KEY `id_compra` (`id_compra`) USING BTREE;

--
-- Indices de la tabla `productos_cotis`
--
ALTER TABLE `productos_cotis`
  ADD PRIMARY KEY (`prod_coti_id`) USING BTREE;

--
-- Indices de la tabla `productos_ventas`
--
ALTER TABLE `productos_ventas`
  ADD KEY `fk_productos_has_ventas_ventas1_idx` (`id_venta`) USING BTREE,
  ADD KEY `fk_productos_has_ventas_productos1_idx` (`id_producto`) USING BTREE;

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`proveedor_id`) USING BTREE,
  ADD UNIQUE KEY `ruc` (`ruc`) USING BTREE;

--
-- Indices de la tabla `recepciones`
--
ALTER TABLE `recepciones`
  ADD PRIMARY KEY (`id_recepcion`) USING BTREE,
  ADD KEY `recepciones_id_empresa_index` (`id_empresa`) USING BTREE,
  ADD KEY `recepciones_id_compra_index` (`id_compra`) USING BTREE;

--
-- Indices de la tabla `recepcion_detalle`
--
ALTER TABLE `recepcion_detalle`
  ADD PRIMARY KEY (`id_detalle`) USING BTREE,
  ADD KEY `recepcion_detalle_id_recepcion_index` (`id_recepcion`) USING BTREE;

--
-- Indices de la tabla `registros_caja_vendedor`
--
ALTER TABLE `registros_caja_vendedor`
  ADD PRIMARY KEY (`registro_id`) USING BTREE,
  ADD KEY `idx_fecha` (`fecha_registro`) USING BTREE,
  ADD KEY `idx_vendedor` (`id_vendedor`) USING BTREE,
  ADD KEY `idx_empresa` (`id_empresa`,`sucursal`) USING BTREE;

--
-- Indices de la tabla `resumen_diario`
--
ALTER TABLE `resumen_diario`
  ADD PRIMARY KEY (`id_resumen_diario`) USING BTREE,
  ADD KEY `fk_resumen_diario_empresas1_idx` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`rol_id`) USING BTREE;

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`) USING BTREE;

--
-- Indices de la tabla `rutas_vendedor`
--
ALTER TABLE `rutas_vendedor`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `sessions_user_id_index` (`user_id`) USING BTREE,
  ADD KEY `sessions_last_activity_index` (`last_activity`) USING BTREE;

--
-- Indices de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD PRIMARY KEY (`id_subcategoria`) USING BTREE,
  ADD KEY `subcategorias_id_categoria_index` (`id_categoria`) USING BTREE,
  ADD KEY `subcategorias_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `submarcas`
--
ALTER TABLE `submarcas`
  ADD PRIMARY KEY (`id_submarca`) USING BTREE,
  ADD KEY `submarcas_id_marca_index` (`id_marca`) USING BTREE,
  ADD KEY `submarcas_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id_sucursal`) USING BTREE,
  ADD KEY `empresa_id` (`empresa_id`) USING BTREE;

--
-- Indices de la tabla `tamsporte_persona`
--
ALTER TABLE `tamsporte_persona`
  ADD PRIMARY KEY (`tampo_id`) USING BTREE;

--
-- Indices de la tabla `tarjetas`
--
ALTER TABLE `tarjetas`
  ADD PRIMARY KEY (`id_tarjeta`) USING BTREE,
  ADD KEY `tarjetas_id_banco_foreign` (`id_banco`) USING BTREE,
  ADD KEY `tarjetas_id_cuenta_bancaria_foreign` (`id_cuenta_bancaria`) USING BTREE,
  ADD KEY `tarjetas_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `tipo_pago`
--
ALTER TABLE `tipo_pago`
  ADD PRIMARY KEY (`tipo_pago_id`) USING BTREE;

--
-- Indices de la tabla `tms_conductores`
--
ALTER TABLE `tms_conductores`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `tms_conductores_id_empresa_sucursal_index` (`id_empresa`,`sucursal`) USING BTREE;

--
-- Indices de la tabla `tms_despachos`
--
ALTER TABLE `tms_despachos`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `tms_despachos_id_empresa_sucursal_fecha_reparto_index` (`id_empresa`,`sucursal`,`fecha_reparto`) USING BTREE;

--
-- Indices de la tabla `tms_despacho_costos`
--
ALTER TABLE `tms_despacho_costos`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `tms_despacho_costos_id_despacho_index` (`id_despacho`) USING BTREE;

--
-- Indices de la tabla `tms_despacho_pedidos`
--
ALTER TABLE `tms_despacho_pedidos`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `tms_despacho_pedidos_id_despacho_index` (`id_despacho`) USING BTREE;

--
-- Indices de la tabla `tms_mercados`
--
ALTER TABLE `tms_mercados`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `tms_mercados_id_empresa_sucursal_index` (`id_empresa`,`sucursal`) USING BTREE;

--
-- Indices de la tabla `tms_rutas`
--
ALTER TABLE `tms_rutas`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `tms_rutas_id_empresa_sucursal_index` (`id_empresa`,`sucursal`) USING BTREE;

--
-- Indices de la tabla `tms_ruta_puntos`
--
ALTER TABLE `tms_ruta_puntos`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `tms_ruta_puntos_id_ruta_index` (`id_ruta`) USING BTREE;

--
-- Indices de la tabla `tms_tipos_vehiculo`
--
ALTER TABLE `tms_tipos_vehiculo`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `tms_tipos_vehiculo_id_empresa_nombre_unique` (`id_empresa`,`nombre`) USING BTREE;

--
-- Indices de la tabla `tms_vehiculos`
--
ALTER TABLE `tms_vehiculos`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `tms_vehiculos_id_empresa_sucursal_index` (`id_empresa`,`sucursal`) USING BTREE,
  ADD KEY `tms_vehiculos_id_tipo_foreign` (`id_tipo`) USING BTREE;

--
-- Indices de la tabla `transferencias_fondo`
--
ALTER TABLE `transferencias_fondo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transferencias_fondo_id_caja_destino_estado_index` (`id_caja_destino`,`estado`);

--
-- Indices de la tabla `traslados`
--
ALTER TABLE `traslados`
  ADD PRIMARY KEY (`id_traslado`) USING BTREE,
  ADD KEY `traslados_id_empresa_index` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `traslado_detalle`
--
ALTER TABLE `traslado_detalle`
  ADD PRIMARY KEY (`id_detalle`) USING BTREE,
  ADD KEY `traslado_detalle_id_traslado_index` (`id_traslado`) USING BTREE;

--
-- Indices de la tabla `ubigeo_inei`
--
ALTER TABLE `ubigeo_inei`
  ADD PRIMARY KEY (`id_ubigeo`) USING BTREE;

--
-- Indices de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `unidades_medida_id_empresa_nombre_unique` (`id_empresa`,`nombre`) USING BTREE;

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`) USING BTREE,
  ADD KEY `id_empresa` (`id_empresa`) USING BTREE,
  ADD KEY `id_rol` (`id_rol`) USING BTREE;

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`) USING BTREE,
  ADD KEY `fk_ventas_documentos_sunat1_idx` (`id_tido`) USING BTREE,
  ADD KEY `fk_ventas_clientes1_idx` (`id_cliente`) USING BTREE,
  ADD KEY `fk_ventas_empresas1_idx` (`id_empresa`) USING BTREE,
  ADD KEY `id_tipo_pago` (`id_tipo_pago`) USING BTREE,
  ADD KEY `medoto_pago_id` (`medoto_pago_id`) USING BTREE,
  ADD KEY `idx_ventas_emp_suc_estado` (`id_empresa`,`sucursal`,`estado`,`fecha_emision`) USING BTREE,
  ADD KEY `idx_ventas_cliente` (`id_cliente`) USING BTREE,
  ADD KEY `idx_ventas_vendedor` (`id_vendedor`) USING BTREE;

--
-- Indices de la tabla `ventas_pagos`
--
ALTER TABLE `ventas_pagos`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `ventas_referencias`
--
ALTER TABLE `ventas_referencias`
  ADD PRIMARY KEY (`id_venta`) USING BTREE,
  ADD KEY `fk_ventas_referencias_ventas2_idx` (`id_referencia`) USING BTREE,
  ADD KEY `fk_ventas_referencias_motivo_documento1_idx` (`id_motivo`) USING BTREE;

--
-- Indices de la tabla `ventas_servicios`
--
ALTER TABLE `ventas_servicios`
  ADD PRIMARY KEY (`id_venta`,`id_item`) USING BTREE;

--
-- Indices de la tabla `ventas_sunat`
--
ALTER TABLE `ventas_sunat`
  ADD PRIMARY KEY (`id_venta`) USING BTREE;

--
-- Indices de la tabla `venta_adicional`
--
ALTER TABLE `venta_adicional`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `venta_anexo`
--
ALTER TABLE `venta_anexo`
  ADD PRIMARY KEY (`idventa`) USING BTREE;

--
-- Indices de la tabla `venta_cuotas`
--
ALTER TABLE `venta_cuotas`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `venta_pagos`
--
ALTER TABLE `venta_pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_pagos_id_venta_index` (`id_venta`),
  ADD KEY `venta_pagos_id_dias_venta_index` (`id_dias_venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  MODIFY `id_almacen` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `arqueos_diarios`
--
ALTER TABLE `arqueos_diarios`
  MODIFY `arqueo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=707;

--
-- AUTO_INCREMENT de la tabla `arqueo_detalle`
--
ALTER TABLE `arqueo_detalle`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `arqueo_efectivo_detalle`
--
ALTER TABLE `arqueo_efectivo_detalle`
  MODIFY `detalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=696;

--
-- AUTO_INCREMENT de la tabla `arqueo_pagos_digitales`
--
ALTER TABLE `arqueo_pagos_digitales`
  MODIFY `pago_digital_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4818;

--
-- AUTO_INCREMENT de la tabla `asientos_contables`
--
ALTER TABLE `asientos_contables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asientos_detalle`
--
ALTER TABLE `asientos_detalle`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `audits`
--
ALTER TABLE `audits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT de la tabla `bancos`
--
ALTER TABLE `bancos`
  MODIFY `id_banco` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `billeteras_digitales`
--
ALTER TABLE `billeteras_digitales`
  MODIFY `id_billetera` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `billetera_tipos`
--
ALTER TABLE `billetera_tipos`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `caja_aperturas`
--
ALTER TABLE `caja_aperturas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `caja_apertura_detalles`
--
ALTER TABLE `caja_apertura_detalles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `caja_chica`
--
ALTER TABLE `caja_chica`
  MODIFY `caja_chica_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `caja_cierre_deudas`
--
ALTER TABLE `caja_cierre_deudas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `caja_empresa`
--
ALTER TABLE `caja_empresa`
  MODIFY `caja_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `caja_instrumentos`
--
ALTER TABLE `caja_instrumentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `cierre_caja`
--
ALTER TABLE `cierre_caja`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2541;

--
-- AUTO_INCREMENT de la tabla `cliente_venta`
--
ALTER TABLE `cliente_venta`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  MODIFY `cotizacion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51498;

--
-- AUTO_INCREMENT de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  MODIFY `id_cuenta` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cuotas_cotizacion`
--
ALTER TABLE `cuotas_cotizacion`
  MODIFY `cuota_coti_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236736;

--
-- AUTO_INCREMENT de la tabla `cxc_abonos`
--
ALTER TABLE `cxc_abonos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `devoluciones_nv`
--
ALTER TABLE `devoluciones_nv`
  MODIFY `id_devolucion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT de la tabla `dias_compras`
--
ALTER TABLE `dias_compras`
  MODIFY `dias_compra_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `dias_ventas`
--
ALTER TABLE `dias_ventas`
  MODIFY `dias_venta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=418;

--
-- AUTO_INCREMENT de la tabla `documentos_sunat`
--
ALTER TABLE `documentos_sunat`
  MODIFY `id_tido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `guia_detalles`
--
ALTER TABLE `guia_detalles`
  MODIFY `guia_detalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5603;

--
-- AUTO_INCREMENT de la tabla `guia_remision`
--
ALTER TABLE `guia_remision`
  MODIFY `id_guia_remision` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=428;

--
-- AUTO_INCREMENT de la tabla `guia_transporte`
--
ALTER TABLE `guia_transporte`
  MODIFY `id_guia_remision` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ingreso_egreso`
--
ALTER TABLE `ingreso_egreso`
  MODIFY `intercambio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `inventario_movimientos`
--
ALTER TABLE `inventario_movimientos`
  MODIFY `id_movimiento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id_marca` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de la tabla `motivos_movimiento`
--
ALTER TABLE `motivos_movimiento`
  MODIFY `id_motivo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `notas_electronicas`
--
ALTER TABLE `notas_electronicas`
  MODIFY `nota_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plan_cuentas`
--
ALTER TABLE `plan_cuentas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id_prestamo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `prestamo_detalle`
--
ALTER TABLE `prestamo_detalle`
  MODIFY `id_detalle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `prestamo_devoluciones`
--
ALTER TABLE `prestamo_devoluciones`
  MODIFY `id_devolucion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=436;

--
-- AUTO_INCREMENT de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  MODIFY `id_producto_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=405;

--
-- AUTO_INCREMENT de la tabla `productos_cotis`
--
ALTER TABLE `productos_cotis`
  MODIFY `prod_coti_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=485224;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `proveedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT de la tabla `recepciones`
--
ALTER TABLE `recepciones`
  MODIFY `id_recepcion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `recepcion_detalle`
--
ALTER TABLE `recepcion_detalle`
  MODIFY `id_detalle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `registros_caja_vendedor`
--
ALTER TABLE `registros_caja_vendedor`
  MODIFY `registro_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rutas_vendedor`
--
ALTER TABLE `rutas_vendedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  MODIFY `id_subcategoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `submarcas`
--
ALTER TABLE `submarcas`
  MODIFY `id_submarca` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id_sucursal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tarjetas`
--
ALTER TABLE `tarjetas`
  MODIFY `id_tarjeta` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tms_conductores`
--
ALTER TABLE `tms_conductores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `tms_despachos`
--
ALTER TABLE `tms_despachos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tms_despacho_costos`
--
ALTER TABLE `tms_despacho_costos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `tms_despacho_pedidos`
--
ALTER TABLE `tms_despacho_pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `tms_mercados`
--
ALTER TABLE `tms_mercados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `tms_rutas`
--
ALTER TABLE `tms_rutas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tms_ruta_puntos`
--
ALTER TABLE `tms_ruta_puntos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `tms_tipos_vehiculo`
--
ALTER TABLE `tms_tipos_vehiculo`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tms_vehiculos`
--
ALTER TABLE `tms_vehiculos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `transferencias_fondo`
--
ALTER TABLE `transferencias_fondo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `traslados`
--
ALTER TABLE `traslados`
  MODIFY `id_traslado` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `traslado_detalle`
--
ALTER TABLE `traslado_detalle`
  MODIFY `id_detalle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268;

--
-- AUTO_INCREMENT de la tabla `ventas_pagos`
--
ALTER TABLE `ventas_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `ventas_sunat`
--
ALTER TABLE `ventas_sunat`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT de la tabla `venta_adicional`
--
ALTER TABLE `venta_adicional`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `venta_cuotas`
--
ALTER TABLE `venta_cuotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `venta_pagos`
--
ALTER TABLE `venta_pagos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `arqueo_efectivo_detalle`
--
ALTER TABLE `arqueo_efectivo_detalle`
  ADD CONSTRAINT `fk_detalle_arqueo` FOREIGN KEY (`arqueo_id`) REFERENCES `arqueos_diarios` (`arqueo_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `arqueo_pagos_digitales`
--
ALTER TABLE `arqueo_pagos_digitales`
  ADD CONSTRAINT `fk_pago_arqueo` FOREIGN KEY (`arqueo_id`) REFERENCES `arqueos_diarios` (`arqueo_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asientos_detalle`
--
ALTER TABLE `asientos_detalle`
  ADD CONSTRAINT `asientos_detalle_asiento_id_foreign` FOREIGN KEY (`asiento_id`) REFERENCES `asientos_contables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asientos_detalle_plan_cuenta_id_foreign` FOREIGN KEY (`plan_cuenta_id`) REFERENCES `plan_cuentas` (`id`);

--
-- Filtros para la tabla `caja_aperturas`
--
ALTER TABLE `caja_aperturas`
  ADD CONSTRAINT `caja_aperturas_id_caja_foreign` FOREIGN KEY (`id_caja`) REFERENCES `cajas` (`id`);

--
-- Filtros para la tabla `caja_apertura_detalles`
--
ALTER TABLE `caja_apertura_detalles`
  ADD CONSTRAINT `caja_apertura_detalles_id_apertura_foreign` FOREIGN KEY (`id_apertura`) REFERENCES `caja_aperturas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  ADD CONSTRAINT `cuentas_bancarias_id_banco_foreign` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `mhp_permission_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `plan_cuentas`
--
ALTER TABLE `plan_cuentas`
  ADD CONSTRAINT `plan_cuentas_padre_id_foreign` FOREIGN KEY (`padre_id`) REFERENCES `plan_cuentas` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `prestamo_detalle`
--
ALTER TABLE `prestamo_detalle`
  ADD CONSTRAINT `prestamo_detalle_id_prestamo_foreign` FOREIGN KEY (`id_prestamo`) REFERENCES `prestamos` (`id_prestamo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `rhp_permission_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tarjetas`
--
ALTER TABLE `tarjetas`
  ADD CONSTRAINT `tarjetas_id_banco_foreign` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarjetas_id_cuenta_bancaria_foreign` FOREIGN KEY (`id_cuenta_bancaria`) REFERENCES `cuentas_bancarias` (`id_cuenta`) ON DELETE SET NULL;

--
-- Filtros para la tabla `tms_despacho_costos`
--
ALTER TABLE `tms_despacho_costos`
  ADD CONSTRAINT `tms_despacho_costos_id_despacho_foreign` FOREIGN KEY (`id_despacho`) REFERENCES `tms_despachos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tms_despacho_pedidos`
--
ALTER TABLE `tms_despacho_pedidos`
  ADD CONSTRAINT `tms_despacho_pedidos_id_despacho_foreign` FOREIGN KEY (`id_despacho`) REFERENCES `tms_despachos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tms_ruta_puntos`
--
ALTER TABLE `tms_ruta_puntos`
  ADD CONSTRAINT `tms_ruta_puntos_id_ruta_foreign` FOREIGN KEY (`id_ruta`) REFERENCES `tms_rutas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tms_vehiculos`
--
ALTER TABLE `tms_vehiculos`
  ADD CONSTRAINT `tms_vehiculos_id_tipo_foreign` FOREIGN KEY (`id_tipo`) REFERENCES `tms_tipos_vehiculo` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
