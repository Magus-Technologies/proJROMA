/*
 Navicat Premium Dump SQL

 Source Server         : localhist
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : magusqao_roma

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 26/06/2026 13:40:19
*/
CREATE DATABASE IF NOT EXISTS magusqao_roma;
USE magusqao_roma;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for almacenes
-- ----------------------------
DROP TABLE IF EXISTS `almacenes`;
CREATE TABLE `almacenes`  (
  `id_almacen` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_sucursal` int NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_almacen`) USING BTREE,
  INDEX `almacenes_id_empresa_index`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of almacenes
-- ----------------------------
INSERT INTO `almacenes` VALUES (1, 'Almacén 1', '1', NULL, NULL, 12, '1');
INSERT INTO `almacenes` VALUES (2, 'Almacén 2', '2', NULL, NULL, 12, '1');
INSERT INTO `almacenes` VALUES (3, 'Almacén 3', '3', NULL, NULL, 12, '1');

-- ----------------------------
-- Table structure for arqueo_detalle
-- ----------------------------
DROP TABLE IF EXISTS `arqueo_detalle`;
CREATE TABLE `arqueo_detalle`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_arqueo` int UNSIGNED NOT NULL,
  `instrumento_tipo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `instrumento_id` int UNSIGNED NULL DEFAULT NULL,
  `monto_sistema` decimal(12, 2) NOT NULL,
  `monto_contado` decimal(12, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of arqueo_detalle
-- ----------------------------

-- ----------------------------
-- Table structure for arqueo_efectivo_detalle
-- ----------------------------
DROP TABLE IF EXISTS `arqueo_efectivo_detalle`;
CREATE TABLE `arqueo_efectivo_detalle`  (
  `detalle_id` int NOT NULL AUTO_INCREMENT,
  `arqueo_id` int NOT NULL,
  `billetes` decimal(10, 2) NULL DEFAULT 0.00,
  `monedas` decimal(10, 2) NULL DEFAULT 0.00,
  `pasaje` decimal(10, 2) NULL DEFAULT 0.00,
  `combustible` decimal(10, 2) NULL DEFAULT 0.00,
  `gastos` decimal(10, 2) NULL DEFAULT 0.00,
  `menu` decimal(10, 2) NULL DEFAULT 0.00,
  `otro` decimal(10, 2) NULL DEFAULT 0.00,
  `otro_descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `total_ingresos` decimal(10, 2) NULL DEFAULT 0.00,
  `total_gastos` decimal(10, 2) NULL DEFAULT 0.00,
  `total_efectivo_real` decimal(10, 2) NULL DEFAULT 0.00,
  PRIMARY KEY (`detalle_id`) USING BTREE,
  INDEX `idx_arqueo`(`arqueo_id` ASC) USING BTREE,
  CONSTRAINT `fk_detalle_arqueo` FOREIGN KEY (`arqueo_id`) REFERENCES `arqueos_diarios` (`arqueo_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 696 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of arqueo_efectivo_detalle
-- ----------------------------
INSERT INTO `arqueo_efectivo_detalle` VALUES (691, 702, 750.00, 7.80, 0.00, 0.00, 30.00, 0.00, 0.00, '', 757.80, 30.00, 787.80);
INSERT INTO `arqueo_efectivo_detalle` VALUES (692, 703, 1200.00, 10.10, 0.00, 0.00, 24.50, 0.00, 0.00, '', 1210.10, 24.50, 1234.60);
INSERT INTO `arqueo_efectivo_detalle` VALUES (693, 704, 840.00, 18.50, 0.00, 0.00, 28.00, 0.00, 460.00, 'semana telefono feriado', 858.50, 488.00, 1346.50);
INSERT INTO `arqueo_efectivo_detalle` VALUES (694, 705, 7070.00, 25.70, 0.00, 0.00, 28.00, 30.00, 446.30, 'petrolio', 7095.70, 504.30, 7600.00);
INSERT INTO `arqueo_efectivo_detalle` VALUES (695, 706, 7700.00, 19.60, 0.00, 0.00, 28.00, 30.00, 0.00, '', 7719.60, 58.00, 7777.60);

-- ----------------------------
-- Table structure for arqueo_pagos_digitales
-- ----------------------------
DROP TABLE IF EXISTS `arqueo_pagos_digitales`;
CREATE TABLE `arqueo_pagos_digitales`  (
  `pago_digital_id` int NOT NULL AUTO_INCREMENT,
  `arqueo_id` int NOT NULL,
  `cliente_nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `tipo_pago` enum('Yape','Plin','Transferencia') CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `numero_operacion` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `monto` decimal(10, 2) NOT NULL,
  `fecha_registro` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pago_digital_id`) USING BTREE,
  INDEX `idx_arqueo`(`arqueo_id` ASC) USING BTREE,
  INDEX `idx_tipo_pago`(`tipo_pago` ASC) USING BTREE,
  CONSTRAINT `fk_pago_arqueo` FOREIGN KEY (`arqueo_id`) REFERENCES `arqueos_diarios` (`arqueo_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4818 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of arqueo_pagos_digitales
-- ----------------------------
INSERT INTO `arqueo_pagos_digitales` VALUES (4759, 702, 'C- GERALDINE DOMINGUEZ - 2 DE JULIO', 'Yape', '', 82.50, '2026-05-06 09:57:10');
INSERT INTO `arqueo_pagos_digitales` VALUES (4760, 702, 'ROSA RIVERA (UNION STA ROSA)', 'Yape', '', 216.70, '2026-05-06 09:57:10');
INSERT INTO `arqueo_pagos_digitales` VALUES (4761, 702, 'REST. LA NORTEÑA', 'Yape', '', 189.50, '2026-05-06 09:57:10');
INSERT INTO `arqueo_pagos_digitales` VALUES (4762, 702, 'C-CHICHARRONERIA ELENITA (UNION STA ROSA)', 'Yape', '', 66.00, '2026-05-06 09:57:10');
INSERT INTO `arqueo_pagos_digitales` VALUES (4763, 702, 'F-LUZ SUDARIO (LAS FLORES)', 'Yape', '', 424.00, '2026-05-06 09:57:10');
INSERT INTO `arqueo_pagos_digitales` VALUES (4764, 702, 'F-YANETH SANDOVAL (LAS FLORES)', 'Yape', '', 399.60, '2026-05-06 09:57:10');
INSERT INTO `arqueo_pagos_digitales` VALUES (4765, 702, 'F-LUZ SUDARIO (LAS FLORES)', 'Yape', '', 65.00, '2026-05-06 09:57:10');
INSERT INTO `arqueo_pagos_digitales` VALUES (4766, 702, 'F-YANETH SANDOVAL (LAS FLORES)', 'Yape', '', 100.40, '2026-05-06 09:57:10');
INSERT INTO `arqueo_pagos_digitales` VALUES (4767, 703, 'C-IVAN URBANO (PROVEEDORES)', 'Yape', '', 94.00, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4768, 703, 'B-CARLOS ALBERTO (PROGRESO)', 'Yape', '', 132.00, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4769, 703, 'C-ELAR SANCHEZ (PROVEEDORES)', 'Yape', '', 528.50, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4770, 703, 'B-LUISA GALINDO (PROGRESO)', 'Yape', '', 25.00, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4771, 703, 'D-GISELA NAVEROS (GAMBETA)', 'Yape', '', 128.20, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4772, 703, 'H-LEONARDA QUISPE CARBAJAL (V.CARMEN)', 'Yape', '', 41.40, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4773, 703, 'VICTORIA NUÑEZ ABREGU', 'Yape', '', 92.10, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4774, 703, 'A-MOISES GUIZADO (RESTAURACION)', 'Yape', '', 1000.00, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4775, 703, 'B-ROSA HUAMANI CORDOVA (NOXILIA)', 'Yape', '', 120.00, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4776, 703, 'I-FIDELIA HUAMAN VARGAS(FLORES DE BREÑA', 'Yape', '', 164.00, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4777, 703, 'G-YOLANDA CHUQUIMAJO(CARHUAZ )', 'Yape', '', 106.80, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4778, 703, 'F-HUGO VILLANO (JR LORETO)', 'Yape', '', 168.00, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4779, 703, 'G-GLADIS GOMEZ (CHACRA COLORADA)', 'Yape', '', 116.40, '2026-05-06 09:57:41');
INSERT INTO `arqueo_pagos_digitales` VALUES (4780, 704, 'B-ALEXIS (V.MARIA)', 'Plin', '', 500.00, '2026-05-06 09:59:01');
INSERT INTO `arqueo_pagos_digitales` VALUES (4781, 704, 'MARYORI CHáVEZ LOPEZ', 'Yape', '', 306.60, '2026-05-06 09:59:01');
INSERT INTO `arqueo_pagos_digitales` VALUES (4782, 704, 'E-JESSENIA LAURA HUAMAN (MILAGROS)', 'Plin', '', 324.40, '2026-05-06 09:59:01');
INSERT INTO `arqueo_pagos_digitales` VALUES (4783, 704, 'C-JESSENIA ROJAS LLANIO (AMAUTA)', 'Plin', '', 100.00, '2026-05-06 09:59:01');
INSERT INTO `arqueo_pagos_digitales` VALUES (4784, 705, 'G-PAULINA ISHUIZA (CTO REY)', 'Yape', '', 69.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4785, 705, 'F- ISABEL C CASTRO (CTO REY)', 'Yape', '', 200.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4786, 705, 'ROSA RIVERA (UNION STA ROSA)', 'Yape', '', 216.70, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4787, 705, 'REST. LA NORTEÑA', 'Yape', '', 189.50, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4788, 705, 'A-YORDY (CANTO CHICO)', 'Yape', '', 100.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4789, 705, 'A-HAIDE ALANYA (CTO CHICO)', 'Yape', '', 275.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4790, 705, 'A-ALEX ALVITES (C. CHICO)', 'Yape', '', 80.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4791, 705, 'C-CHICHARRONERIA ELENITA (UNION STA ROSA)', 'Yape', '', 66.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4792, 705, 'B-MARY RONDAN (2 DE MAYO)', 'Yape', '', 100.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4793, 705, 'ANA MARIA LEANDRO ILDEFONSO', 'Yape', '', 550.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4794, 705, 'C-PAOLA HUAMANI (M.P.B)', 'Yape', '', 400.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4795, 705, 'NELLY AURORA ROJAS MARTINEZ', 'Yape', '', 100.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4796, 705, 'F-LUZ SUDARIO (LAS FLORES)', 'Yape', '', 424.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4797, 705, 'F-YANETH SANDOVAL (LAS FLORES)', 'Yape', '', 399.60, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4798, 705, 'E-HELEN (CTO REY) (BODEGA)', 'Yape', '', 300.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4799, 705, 'F- ISABEL C CASTRO (CTO REY)', 'Plin', '', 473.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4800, 705, 'D-MARIA ROSARIO ZAVALA RETAMOZO', 'Yape', '', 50.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4801, 705, 'F-LUZ SUDARIO (LAS FLORES)', 'Yape', '', 65.00, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4802, 705, 'F-YANETH SANDOVAL (LAS FLORES)', 'Yape', '', 100.40, '2026-05-06 10:00:16');
INSERT INTO `arqueo_pagos_digitales` VALUES (4803, 706, 'C-IVAN URBANO (PROVEEDORES)', 'Yape', '', 94.00, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4804, 706, 'F-CAROLINA CAMBA (MODELO #2)', 'Yape', '', 108.90, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4805, 706, 'C-ELAR SANCHEZ (PROVEEDORES)', 'Yape', '', 528.50, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4806, 706, 'H-BERNA VERA REATEGUI (P.PACOCHA)', 'Yape', '', 203.50, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4807, 706, 'F-FIDENCIA TAPIA (MODELO # 02 )', 'Yape', '', 283.00, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4808, 706, 'A-CLORINDA MORMONTOY (BOLIVAR )', 'Yape', '', 72.00, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4809, 706, 'C-SHEILA QUINTO (FAP)', 'Yape', '', 71.50, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4810, 706, 'H-LEONARDA QUISPE CARBAJAL (V.CARMEN)', 'Yape', '', 41.40, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4811, 706, 'VICTORIA NUÑEZ ABREGU', 'Yape', '', 92.10, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4812, 706, 'A-MOISES GUIZADO (RESTAURACION)', 'Yape', '', 1000.00, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4813, 706, 'B-ROSA HUAMANI CORDOVA (NOXILIA)', 'Yape', '', 120.00, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4814, 706, 'I-FIDELIA HUAMAN VARGAS(FLORES DE BREÑA', 'Yape', '', 164.00, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4815, 706, 'G-YOLANDA CHUQUIMAJO(CARHUAZ )', 'Yape', '', 106.80, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4816, 706, 'F-HUGO VILLANO (JR LORETO)', 'Yape', '', 168.00, '2026-05-06 10:00:40');
INSERT INTO `arqueo_pagos_digitales` VALUES (4817, 706, 'G-GLADIS GOMEZ (CHACRA COLORADA)', 'Yape', '', 116.40, '2026-05-06 10:00:40');

-- ----------------------------
-- Table structure for arqueos_diarios
-- ----------------------------
DROP TABLE IF EXISTS `arqueos_diarios`;
CREATE TABLE `arqueos_diarios`  (
  `arqueo_id` int NOT NULL AUTO_INCREMENT,
  `id_caja` int UNSIGNED NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NOT NULL,
  `fecha_arqueo` date NOT NULL,
  `vendedor` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `vendedor_id` int NULL DEFAULT NULL,
  `cobros_efectivo` decimal(10, 2) NULL DEFAULT 0.00,
  `cobros_bancos` decimal(10, 2) NULL DEFAULT 0.00,
  `ingresos_efectivo` decimal(10, 2) NULL DEFAULT 0.00,
  `ingresos_bancos` decimal(10, 2) NULL DEFAULT 0.00,
  `egresos_efectivo` decimal(10, 2) NULL DEFAULT 0.00,
  `egresos_bancos` decimal(10, 2) NULL DEFAULT 0.00,
  `diferencia_efectivo` decimal(10, 2) NULL DEFAULT 0.00,
  `diferencia_bancos` decimal(10, 2) NULL DEFAULT 0.00,
  `cuadra_efectivo` tinyint(1) NULL DEFAULT 0,
  `cuadra_bancos` tinyint(1) NULL DEFAULT 0,
  `usuario_registro` int NULL DEFAULT NULL,
  `fecha_creacion` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`arqueo_id`) USING BTREE,
  INDEX `idx_fecha`(`fecha_arqueo` ASC) USING BTREE,
  INDEX `idx_empresa`(`id_empresa` ASC, `sucursal` ASC) USING BTREE,
  INDEX `idx_vendedor`(`vendedor_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 707 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of arqueos_diarios
-- ----------------------------
INSERT INTO `arqueos_diarios` VALUES (702, NULL, 12, 1, '2026-05-05', 'LINDA', 91, 787.80, 82.50, 787.80, 1543.70, 30.00, 0.00, 0.00, 1461.20, 1, 1, 40, '2026-05-06 09:57:10');
INSERT INTO `arqueos_diarios` VALUES (703, NULL, 12, 1, '2026-05-05', 'ZENON', 61, 1234.60, 285.20, 1234.60, 2716.40, 24.50, 0.00, 0.00, 2431.20, 1, 1, 40, '2026-05-06 09:57:41');
INSERT INTO `arqueos_diarios` VALUES (704, NULL, 12, 1, '2026-05-05', 'MARIANELA', 80, 1346.50, 1231.00, 1346.50, 1231.00, 488.00, 0.00, 0.00, 0.00, 1, 1, 40, '2026-05-06 09:59:01');
INSERT INTO `arqueos_diarios` VALUES (705, NULL, 12, 1, '2026-05-05', 'Arly ', 66, 7600.00, 4158.20, 7600.00, 4158.20, 504.30, 0.00, 0.00, 0.00, 1, 1, 40, '2026-05-06 10:00:16');
INSERT INTO `arqueos_diarios` VALUES (706, NULL, 12, 1, '2026-05-05', 'GAMARRA', 94, 7777.60, 3170.10, 7777.60, 3170.10, 58.00, 0.00, 0.00, 0.00, 1, 1, 40, '2026-05-06 10:00:40');

-- ----------------------------
-- Table structure for bancos
-- ----------------------------
DROP TABLE IF EXISTS `bancos`;
CREATE TABLE `bancos`  (
  `id_banco` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int UNSIGNED NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_sunat` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `estado` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_banco`) USING BTREE,
  INDEX `bancos_id_empresa_index`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of bancos
-- ----------------------------

-- ----------------------------
-- Table structure for billetera_tipos
-- ----------------------------
DROP TABLE IF EXISTS `billetera_tipos`;
CREATE TABLE `billetera_tipos`  (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int UNSIGNED NOT NULL,
  `nombre` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of billetera_tipos
-- ----------------------------
INSERT INTO `billetera_tipos` VALUES (1, 1, 'Yape', '1');
INSERT INTO `billetera_tipos` VALUES (2, 1, 'Plin', '1');
INSERT INTO `billetera_tipos` VALUES (3, 1, 'Tunki', '1');
INSERT INTO `billetera_tipos` VALUES (4, 1, 'Agora', '1');
INSERT INTO `billetera_tipos` VALUES (5, 1, 'BIM', '1');
INSERT INTO `billetera_tipos` VALUES (6, 1, 'Otro', '1');

-- ----------------------------
-- Table structure for billeteras_digitales
-- ----------------------------
DROP TABLE IF EXISTS `billeteras_digitales`;
CREATE TABLE `billeteras_digitales`  (
  `id_billetera` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int UNSIGNED NOT NULL,
  `id_cuenta_bancaria` int UNSIGNED NULL DEFAULT NULL,
  `id_billetera_tipo` tinyint UNSIGNED NOT NULL,
  `telefono` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `titular` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_billetera`) USING BTREE,
  INDEX `billeteras_digitales_id_empresa_index`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of billeteras_digitales
-- ----------------------------

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cache
-- ----------------------------

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for caja_chica
-- ----------------------------
DROP TABLE IF EXISTS `caja_chica`;
CREATE TABLE `caja_chica`  (
  `caja_chica_id` int NOT NULL AUTO_INCREMENT,
  `id_caja_empresa` int NULL DEFAULT NULL,
  `hora` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `detalle` varchar(220) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tipo` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'f',
  `entrada` double(15, 2) NULL DEFAULT NULL,
  `salida` double(15, 2) NULL DEFAULT NULL,
  `metodo` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '1 = EFECTIVO 2 =TARJETAS 3 =TRANSFERENCIAS',
  PRIMARY KEY (`caja_chica_id`) USING BTREE,
  INDEX `id_caja_empresa`(`id_caja_empresa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 58 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of caja_chica
-- ----------------------------
INSERT INTO `caja_chica` VALUES (1, 1, '08:49 PM', 'Apertura de caja', 'a', 500.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (2, 1, '08:49 PM', 'Alex', 'f', 0.00, 500.00, '1');
INSERT INTO `caja_chica` VALUES (3, 2, '11:00 AM', 'Apertura de caja', 'a', 100.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (4, 2, '11:05 AM', 'favrt124', 'f', 0.00, 1000.00, '1');
INSERT INTO `caja_chica` VALUES (5, 3, '05:40 PM', 'Apertura de caja', 'a', 4500.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (6, 3, '05:40 PM', '10 gasolina', 'f', 0.00, 70.00, '1');
INSERT INTO `caja_chica` VALUES (7, 3, '05:41 PM', 'efectivo 300', 'f', 300.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (8, 4, '09:57 AM', 'Apertura de caja', 'a', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (9, 5, '05:19 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (10, 5, '06:04 PM', 'Cobro Cotización #35946 - ANY VARGAS (VIRGEN DEL ROSARIO)', 'f', 0.10, 0.00, '1');
INSERT INTO `caja_chica` VALUES (11, 6, '06:55 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (12, 6, '06:55 PM', 'Cobro Cotización #36247 - A-ELENA BLAS (ASOCIACION D COMERCIANTES)', 'f', 470.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (13, 6, '06:57 PM', 'Cobro Cotización #36245 - A-ELENA CORNEJO (MACCHUPICHU)', 'f', 50.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (14, 6, '06:57 PM', 'Cobro Cotización #36272 - B-HILDA PINEDA (BUEN JESUS)', 'f', 0.40, 0.00, '1');
INSERT INTO `caja_chica` VALUES (15, 7, '07:13 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (16, 7, '07:14 PM', 'Cobro Cotización #34378 - G-LALO MALLQUI (JR EMILIO SANDOVAL)', 'f', 102.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (17, 8, '07:15 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (18, 8, '07:16 PM', 'Cobro Cotización #35613 - E-BEATRIZ ROMANI CARRION (CASTILLA)', 'f', 58.50, 0.00, '3');
INSERT INTO `caja_chica` VALUES (19, 8, '07:16 PM', 'Cobro Cotización #26053 - F-RAFAEL CONDORI (SAN MIGUEL )', 'f', 207.60, 0.00, '1');
INSERT INTO `caja_chica` VALUES (20, 8, '07:16 PM', 'Cobro Cotización #26053 - F-RAFAEL CONDORI (SAN MIGUEL )', 'f', 500.00, 0.00, '3');
INSERT INTO `caja_chica` VALUES (21, 8, '07:17 PM', 'Cobro Cotización #36283 - D-MARIO ROSALES (LA MARINA )', 'f', 30.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (22, 7, '07:17 PM', 'Cobro Cotización #34370 - G-MANUEL DE LA CRUZ (PARADITA #3)', 'f', 23.60, 0.00, '1');
INSERT INTO `caja_chica` VALUES (23, 8, '07:17 PM', 'Cobro Cotización #36284 - D-LEONOR ESCOBERO (MARINA)', 'f', 565.50, 0.00, '1');
INSERT INTO `caja_chica` VALUES (24, 8, '07:17 PM', 'Cobro Cotización #36282 - D-VICTORIA (MARINA)', 'f', 142.60, 0.00, '1');
INSERT INTO `caja_chica` VALUES (25, 8, '07:18 PM', 'Cobro Cotización #36359 - F-DELIA MARQUEZ (SAN MIGUEL)', 'f', 149.50, 0.00, '1');
INSERT INTO `caja_chica` VALUES (26, 8, '07:21 PM', 'Cobro Cotización #36257 - AMELIA CARHUARICRA HUARI', 'f', 158.20, 0.00, '1');
INSERT INTO `caja_chica` VALUES (27, 8, '07:22 PM', 'Cobro Cotización #36292 - D-MARIO PILCO (MARINA)', 'f', 293.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (28, 8, '07:23 PM', 'Cobro Cotización #36297 - D-FABIANA PEREZ BARCAS (LA MARINA )', 'f', 39.90, 0.00, '1');
INSERT INTO `caja_chica` VALUES (29, 8, '07:23 PM', 'Cobro Cotización #36301 - F-RUTH PORRAS (SAN  MIGUEL) (AFUERA)', 'f', 242.00, 0.00, '3');
INSERT INTO `caja_chica` VALUES (30, 8, '07:23 PM', 'Cobro Cotización #36293 - D-ESTELA JULIAN (LA MARINA)', 'f', 480.40, 0.00, '3');
INSERT INTO `caja_chica` VALUES (31, 8, '07:33 PM', 'Cobro Cotización #36259 - C-NELLY VASQUEZ (LA PERLA)', 'f', 240.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (32, 8, '07:44 PM', 'Cobro Cotización #35648 - E-OSCAR (METRITO)', 'f', 1771.50, 0.00, '3');
INSERT INTO `caja_chica` VALUES (33, 8, '07:44 PM', 'Cobro Cotización #34951 - E-OSCAR (METRITO)', 'f', 1341.50, 0.00, '3');
INSERT INTO `caja_chica` VALUES (34, 8, '07:45 PM', 'Cobro Cotización #36278 - E-COMERCIAL ANGELITOS (M.CASTILLA)', 'f', 263.60, 0.00, '3');
INSERT INTO `caja_chica` VALUES (35, 8, '07:45 PM', 'Cobro Cotización #36277 - E-ERIKA MENESES (CASTILLA)', 'f', 72.40, 0.00, '1');
INSERT INTO `caja_chica` VALUES (36, 4, '08:19 PM', 'ADMIN', 'f', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (37, 9, '08:20 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (38, 9, '08:22 PM', 'Cobro Cotización #20356 - B-CEVICHERIA JACKY (CHIRA)', 'f', 500.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (39, 9, '08:22 PM', 'Cobro Cotización #20356 - B-CEVICHERIA JACKY (CHIRA)', 'f', 660.10, 0.00, '3');
INSERT INTO `caja_chica` VALUES (40, 10, '09:02 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (41, 11, '07:01 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (42, 12, '10:48 PM', 'Apertura de caja', 'a', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (43, 12, '10:51 PM', 'Cobro Cotización #35739 - B-LORENZA CHIPANA (MATEO PUMACAHUA )', 'f', 266.80, 0.00, '3');
INSERT INTO `caja_chica` VALUES (44, 12, '10:51 PM', 'Cobro Cotización #34400 - B-LUZ SEGIL (MATEO PUMACAHUA)', 'f', 100.00, 0.00, '3');
INSERT INTO `caja_chica` VALUES (45, 12, '10:52 PM', 'Cobro Cotización #34400 - B-LUZ SEGIL (MATEO PUMACAHUA)', 'f', 103.70, 0.00, '1');
INSERT INTO `caja_chica` VALUES (46, 12, '10:52 PM', 'Cobro Cotización #35293 - E-DEL CARPIO (TNE JIMENEZ)', 'f', 250.00, 0.00, '3');
INSERT INTO `caja_chica` VALUES (47, 12, '10:54 PM', 'Cobro Cotización #33687 - E-MARIA GAMARRA (TNTE JIMENEZ)', 'f', 276.90, 0.00, '3');
INSERT INTO `caja_chica` VALUES (48, 12, '10:55 PM', 'Cobro Cotización #35321 - G-CASTILLO ALCALA (PROCERES)', 'f', 50.10, 0.00, '3');
INSERT INTO `caja_chica` VALUES (49, 12, '10:55 PM', 'Cobro Cotización #35748 - G-RENE VALDERRAMA MENDOZA(PROCERES)', 'f', 100.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (50, 13, '07:09 AM', 'Apertura de caja', 'a', 0.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (51, 13, '07:10 AM', 'Cobro Cotización #35240 - H-EUGENIA DUEÑAS (V.CARMEN)', 'f', 45.00, 0.00, '3');
INSERT INTO `caja_chica` VALUES (52, 13, '07:12 AM', 'Cobro Cotización #35765 - H-EUGENIA DUEÑAS (V.CARMEN)', 'f', 200.00, 0.00, '3');
INSERT INTO `caja_chica` VALUES (53, 13, '09:29 AM', 'Cobro Cotización #35769 - H-EDUARDO QUISPE (V.CARMEN)', 'f', 42.80, 0.00, '1');
INSERT INTO `caja_chica` VALUES (54, 13, '09:54 AM', 'Cobro Cotización #35251 - H-FABIOLA (V. CARMEN)', 'f', 162.90, 0.00, '1');
INSERT INTO `caja_chica` VALUES (55, 13, '09:55 AM', 'Cobro Cotización #35766 - H-FABIOLA (V. CARMEN)', 'f', 100.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (56, 13, '10:07 AM', 'Cobro Cotización #35255 - H-NOEMY SIERRA (V. DEL CARMEN)', 'f', 150.00, 0.00, '1');
INSERT INTO `caja_chica` VALUES (57, 13, '10:33 AM', 'Cobro Cotización #35777 - H-CARMEN ROSA (V.CARMEN)', 'f', 247.00, 0.00, '3');

-- ----------------------------
-- Table structure for caja_empresa
-- ----------------------------
DROP TABLE IF EXISTS `caja_empresa`;
CREATE TABLE `caja_empresa`  (
  `caja_id` int NOT NULL AUTO_INCREMENT,
  `id_empresa` int NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  `detalle` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `fecha` datetime NULL DEFAULT NULL,
  `entrada` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `salida` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '1',
  `instrumento_tipo` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `instrumento_id` int UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`caja_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of caja_empresa
-- ----------------------------
INSERT INTO `caja_empresa` VALUES (1, 12, 1, NULL, 'Luz', '2024-10-14 00:00:00', '', '', '1', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (2, 12, 1, NULL, 'wilmer', '2025-03-17 00:00:00', '', '', '1', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (3, 12, 1, NULL, 'MIERCOLES 16 JULIO', '2025-07-16 00:00:00', '4800', '70', '0', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (4, 12, 1, NULL, '09 diciembre', '2026-01-09 00:00:00', '', '', '1', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (5, 12, 1, 60, 'Prueba', '2026-01-09 17:20:04', '0', '0', '0', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (6, 12, 1, 94, 'gamarra', '2026-01-09 18:55:23', '0', '0', '0', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (7, 12, 1, 61, 'Paz', '2026-01-09 19:13:49', '0', '0', '0', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (8, 12, 1, 92, 'YORCHS', '2026-01-09 19:15:39', '0', '0', '0', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (9, 12, 1, 40, 'admin', '2026-01-09 20:20:45', '0', '0', '0', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (10, 12, 1, 61, 'Paz', '2026-01-09 21:02:38', '0', '0', '1', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (11, 12, 1, 62, 'hum', '2026-01-09 21:42:00', '0', '0', '0', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (12, 12, 1, 80, 'Marianela', '2026-01-09 22:48:23', '0', '0', '0', NULL, NULL);
INSERT INTO `caja_empresa` VALUES (13, 12, 1, 61, 'Paz', '2026-01-10 07:09:25', '0', '0', '1', NULL, NULL);

-- ----------------------------
-- Table structure for caja_instrumentos
-- ----------------------------
DROP TABLE IF EXISTS `caja_instrumentos`;
CREATE TABLE `caja_instrumentos`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_caja` int UNSIGNED NOT NULL,
  `instrumento_tipo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `instrumento_id` int UNSIGNED NULL DEFAULT NULL,
  `estado` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ci_uniq`(`id_caja` ASC, `instrumento_tipo` ASC, `instrumento_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of caja_instrumentos
-- ----------------------------

-- ----------------------------
-- Table structure for caja_movimientos
-- ----------------------------
DROP TABLE IF EXISTS `caja_movimientos`;
CREATE TABLE `caja_movimientos`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_caja` int UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `tipo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'INGRESO|EGRESO',
  `categoria` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VENTA|COMPRA|GASTO_OP|REPOSICION|RENDICION|AJUSTE|APERTURA|MANUAL',
  `descripcion` varchar(245) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `monto` decimal(12, 2) NOT NULL,
  `instrumento_tipo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `instrumento_id` int UNSIGNED NULL DEFAULT NULL,
  `saldo_anterior` decimal(14, 2) NOT NULL DEFAULT 0.00,
  `saldo_posterior` decimal(14, 2) NOT NULL DEFAULT 0.00,
  `origen_tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `origen_id` int UNSIGNED NULL DEFAULT NULL,
  `id_usuario` int UNSIGNED NOT NULL,
  `estado` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CONFIRMADO' COMMENT 'CONFIRMADO|ANULADO',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of caja_movimientos
-- ----------------------------
INSERT INTO `caja_movimientos` VALUES (1, 1, '2025-07-16', 'INGRESO', 'MANUAL', 'MIERCOLES 16 JULIO', 4800.00, 'EFECTIVO', NULL, 0.00, 4800.00, NULL, NULL, 1, 'CONFIRMADO');

-- ----------------------------
-- Table structure for cajas
-- ----------------------------
DROP TABLE IF EXISTS `cajas`;
CREATE TABLE `cajas`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int UNSIGNED NOT NULL,
  `sucursal` int UNSIGNED NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_usuario_responsable` int UNSIGNED NULL DEFAULT NULL,
  `id_caja_padre` int UNSIGNED NULL DEFAULT NULL,
  `saldo_actual` decimal(14, 2) NOT NULL DEFAULT 0.00,
  `moneda` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `estado` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVA',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cajas
-- ----------------------------
INSERT INTO `cajas` VALUES (1, 12, 1, 'Caja Principal', NULL, NULL, 4800.00, 'PEN', 'ACTIVA');
INSERT INTO `cajas` VALUES (2, 12, 1, 'Caja Chica', NULL, 1, 0.00, 'PEN', 'ACTIVA');

-- ----------------------------
-- Table structure for categorias
-- ----------------------------
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias`  (
  `id_categoria` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_categoria`) USING BTREE,
  INDEX `categorias_id_empresa_index`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of categorias
-- ----------------------------

-- ----------------------------
-- Table structure for cierre_caja
-- ----------------------------
DROP TABLE IF EXISTS `cierre_caja`;
CREATE TABLE `cierre_caja`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_caja` int UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `saldo_declarado` decimal(14, 2) NOT NULL,
  `saldo_sistema` decimal(14, 2) NOT NULL,
  `desglose_instrumentos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `id_usuario_cierra` int UNSIGNED NOT NULL,
  `id_usuario_aprueba` int UNSIGNED NULL DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cierre_caja
-- ----------------------------

-- ----------------------------
-- Table structure for cliente_venta
-- ----------------------------
DROP TABLE IF EXISTS `cliente_venta`;
CREATE TABLE `cliente_venta`  (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_cliente`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cliente_venta
-- ----------------------------

-- ----------------------------
-- Table structure for clientes
-- ----------------------------
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes`  (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `documento` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `datos` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `distrito` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `dias_visitas` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `ultima_venta` date NULL DEFAULT NULL,
  `total_venta` double(8, 2) NULL DEFAULT NULL,
  `id_ruta` int NULL DEFAULT NULL,
  `mercado` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_cliente`) USING BTREE,
  INDEX `fk_clientes_empresas_idx`(`id_empresa` ASC) USING BTREE,
  INDEX `idx_cli_empresa`(`id_empresa` ASC) USING BTREE,
  INDEX `idx_cli_documento`(`documento` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2517 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of clientes
-- ----------------------------
INSERT INTO `clientes` VALUES (2512, '06745308', 'VICTORIA NUÑEZ ABREGU', 'MDO V. CARMEN#26 COMIDAS', 'Breña', '936480104', 'Martes', '', 12, '1000-01-01', 0.00, 3, 4);
INSERT INTO `clientes` VALUES (2513, '07134105', 'MAITE ELIZABETH (MERCADO 7 DE ABRIL) PUESTO 33 PIñATERíA', 'MERCADO 7 DE ABRIL PUESTO 33 (PIñATERíA)', '', '945160416', 'Jueves', '', 12, '1000-01-01', 0.00, 1, 0);
INSERT INTO `clientes` VALUES (2514, 'Miguel Ánge', 'D-MIGUEL ANGEL SILVA ROQUE(GAMBETA BAJA)', 'MDO GAMBETA BAJA PTO 57 CEVICHERIA', NULL, NULL, NULL, NULL, 12, NULL, NULL, NULL, NULL);
INSERT INTO `clientes` VALUES (2515, 'Cardena', 'D-MARIA CARDENAS (GAMBETA)', 'MDO GAMBETA PTO 135', NULL, NULL, NULL, NULL, 12, NULL, NULL, NULL, NULL);
INSERT INTO `clientes` VALUES (2516, '77425200', 'EMER RODRIGO YARLEQUE ZAPATA', 'sdcsdcsd', NULL, NULL, NULL, NULL, 12, '2026-06-26', 74.00, NULL, NULL);

-- ----------------------------
-- Table structure for compras
-- ----------------------------
DROP TABLE IF EXISTS `compras`;
CREATE TABLE `compras`  (
  `id_compra` int NOT NULL AUTO_INCREMENT,
  `id_tido` int NULL DEFAULT NULL,
  `id_tipo_pago` int NULL DEFAULT NULL,
  `instrumento_tipo` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL COMMENT 'EFECTIVO | CUENTA_BANCARIA | TARJETA | BILLETERA_DIGITAL',
  `instrumento_id` int UNSIGNED NULL DEFAULT NULL,
  `id_proveedor` int NULL DEFAULT NULL,
  `fecha_emision` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `fecha_vencimiento` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `dias_pagos` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `serie` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `total` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `recepcionado` tinyint NOT NULL DEFAULT 0,
  `id_empresa` int NULL DEFAULT NULL,
  `moneda` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_compra`) USING BTREE,
  INDEX `id_empresa`(`id_empresa` ASC) USING BTREE,
  INDEX `id_tipo_pago`(`id_tipo_pago` ASC) USING BTREE,
  INDEX `id_tido`(`id_tido` ASC) USING BTREE,
  INDEX `id_proveedor`(`id_proveedor` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 162 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of compras
-- ----------------------------
INSERT INTO `compras` VALUES (111, 12, 1, NULL, NULL, 181, '2026-02-18', '2026-02-18', NULL, '-', '01', '352', '6400', 1, 12, '1', 1);
INSERT INTO `compras` VALUES (118, 2, 1, NULL, NULL, 181, '2026-03-23', '2026-03-23', NULL, '-', '01', '2352', '9820', 1, 12, '1', 1);
INSERT INTO `compras` VALUES (134, 12, 1, NULL, NULL, 182, '2026-04-21', '2026-04-21', NULL, '-', '01', '5352', '8250', 1, 12, '1', 1);
INSERT INTO `compras` VALUES (142, 12, 2, NULL, NULL, 181, '2026-04-27', '2026-04-28', NULL, '-', '01', '352', '3100', 1, 12, '1', 1);
INSERT INTO `compras` VALUES (155, 12, 2, NULL, NULL, 183, '2026-04-01', '2026-04-02', NULL, '-', '01', '352', '54740', 1, 12, '1', 1);
INSERT INTO `compras` VALUES (157, 2, 1, NULL, NULL, 184, '2026-04-27', '2026-04-30', NULL, '-', 'fa01', '194', '31000', 1, 12, '1', 1);

-- ----------------------------
-- Table structure for cotizaciones
-- ----------------------------
DROP TABLE IF EXISTS `cotizaciones`;
CREATE TABLE `cotizaciones`  (
  `cotizacion_id` int NOT NULL AUTO_INCREMENT,
  `numero` int NULL DEFAULT NULL,
  `id_tido` int NOT NULL,
  `id_tipo_pago` int NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `dias_pagos` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_cliente` int NOT NULL,
  `total` double(10, 2) NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `usar_precio` int NULL DEFAULT NULL,
  `moneda` int NULL DEFAULT 1,
  `cm_tc` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_usuario` int NOT NULL,
  `observacion` varchar(225) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_venta` int NULL DEFAULT NULL,
  `fecha_registro` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`cotizacion_id`) USING BTREE,
  INDEX `id_tido`(`id_tido` ASC) USING BTREE,
  INDEX `id_tipo_pago`(`id_tipo_pago` ASC) USING BTREE,
  INDEX `id_cliente`(`id_cliente` ASC) USING BTREE,
  INDEX `idx_coti_empresa_estado`(`id_empresa` ASC, `estado` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 51470 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cotizaciones
-- ----------------------------
INSERT INTO `cotizaciones` VALUES (6193, 4479, 6, 2, '2025-03-28', '', '1', 1526, 66.50, '0', 12, 1, 5, 1, NULL, 63, '', NULL, '2025-03-28 12:28:47');
INSERT INTO `cotizaciones` VALUES (6194, 4480, 6, 2, '2025-03-28', '', '1', 2129, 14.00, '0', 12, 1, 5, 1, NULL, 63, '', NULL, '2025-03-28 12:30:51');
INSERT INTO `cotizaciones` VALUES (6195, 4481, 6, 2, '2025-03-28', '', '1', 2114, 464.00, '0', 12, 1, 1, 1, NULL, 62, '', NULL, '2025-03-28 12:40:56');
INSERT INTO `cotizaciones` VALUES (6196, 4482, 6, 2, '2025-03-28', '', '1', 1521, 196.00, '0', 12, 1, 1, 1, NULL, 63, '', NULL, '2025-03-28 12:42:33');
INSERT INTO `cotizaciones` VALUES (6197, 4483, 6, 2, '2025-03-28', '', '1', 2113, 124.20, '0', 12, 1, 1, 1, NULL, 62, '', NULL, '2025-03-28 12:48:25');

-- ----------------------------
-- Table structure for cuentas_bancarias
-- ----------------------------
DROP TABLE IF EXISTS `cuentas_bancarias`;
CREATE TABLE `cuentas_bancarias`  (
  `id_cuenta` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int UNSIGNED NOT NULL,
  `id_banco` int UNSIGNED NOT NULL,
  `tipo_cuenta` enum('CC','CA','CTS','AHORRO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CC',
  `numero_cuenta` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `cci` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `moneda` enum('PEN','USD') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `titular` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_cuenta`) USING BTREE,
  INDEX `cuentas_bancarias_id_banco_foreign`(`id_banco` ASC) USING BTREE,
  INDEX `cuentas_bancarias_id_empresa_index`(`id_empresa` ASC) USING BTREE,
  CONSTRAINT `cuentas_bancarias_id_banco_foreign` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cuentas_bancarias
-- ----------------------------

-- ----------------------------
-- Table structure for cuotas_cotizacion
-- ----------------------------
DROP TABLE IF EXISTS `cuotas_cotizacion`;
CREATE TABLE `cuotas_cotizacion`  (
  `cuota_coti_id` int NOT NULL AUTO_INCREMENT,
  `id_coti` int NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  `id_caja_empresa` int NULL DEFAULT NULL,
  `monto` double(10, 3) NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0',
  `tipo_pago` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `fecha_pago_real` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`cuota_coti_id`) USING BTREE,
  INDEX `id_coti`(`id_coti` ASC) USING BTREE,
  INDEX `id_usuario`(`id_usuario` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 236734 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cuotas_cotizacion
-- ----------------------------
INSERT INTO `cuotas_cotizacion` VALUES (26878, 6193, NULL, NULL, 66.500, '2025-04-03', '1', 'Efectivo', '2025-04-03 12:00:00');

-- ----------------------------
-- Table structure for devoluciones_nv
-- ----------------------------
DROP TABLE IF EXISTS `devoluciones_nv`;
CREATE TABLE `devoluciones_nv`  (
  `id_devolucion` int NOT NULL AUTO_INCREMENT,
  `id_venta` int NOT NULL,
  `id_producto` int NOT NULL,
  `id_usuario` int NOT NULL,
  `cantidad` double(6, 2) NOT NULL,
  `presenta` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `presenta_cnt` int NULL DEFAULT NULL,
  `signo` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_devolucion`) USING BTREE,
  INDEX `id_producto`(`id_producto` ASC) USING BTREE,
  INDEX `id_usuario`(`id_usuario` ASC) USING BTREE,
  INDEX `id_venta`(`id_venta` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 183 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of devoluciones_nv
-- ----------------------------
INSERT INTO `devoluciones_nv` VALUES (3, 229, 412, 40, 10.00, '4', 1, '+', '2026-04-08 00:26:39');
INSERT INTO `devoluciones_nv` VALUES (158, 231, 367, 40, 10.00, '1', 10, '+', '2026-04-17 00:42:27');

-- ----------------------------
-- Table structure for dias_compras
-- ----------------------------
DROP TABLE IF EXISTS `dias_compras`;
CREATE TABLE `dias_compras`  (
  `dias_compra_id` int NOT NULL AUTO_INCREMENT,
  `id_compra` int NULL DEFAULT NULL,
  `monto` double(10, 3) NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_caja` int NULL DEFAULT NULL,
  `instrumento_tipo` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `instrumento_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`dias_compra_id`) USING BTREE,
  INDEX `id_compra`(`id_compra` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 93 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dias_compras
-- ----------------------------
INSERT INTO `dias_compras` VALUES (75, 142, 3100.000, '2026-04-28', '0', NULL, NULL, NULL);
INSERT INTO `dias_compras` VALUES (90, 155, 25507.400, '2026-04-02', '1', NULL, NULL, NULL);
INSERT INTO `dias_compras` VALUES (91, 155, 10000.000, '2026-04-02', '1', NULL, NULL, NULL);
INSERT INTO `dias_compras` VALUES (92, 155, 19232.600, '2026-04-02', '0', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for dias_ventas
-- ----------------------------
DROP TABLE IF EXISTS `dias_ventas`;
CREATE TABLE `dias_ventas`  (
  `dias_venta_id` int NOT NULL AUTO_INCREMENT,
  `id_venta` int NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  `id_caja_empresa` int NULL DEFAULT NULL,
  `monto` double(10, 3) NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0',
  `tipo_pago` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `fecha_pago_real` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`dias_venta_id`) USING BTREE,
  INDEX `id_venta`(`id_venta` ASC) USING BTREE,
  INDEX `id_usuario`(`id_usuario` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 414 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dias_ventas
-- ----------------------------
INSERT INTO `dias_ventas` VALUES (410, 229, NULL, NULL, 750.000, '2026-03-28', '1', 'PLIN', NULL);
INSERT INTO `dias_ventas` VALUES (411, 229, NULL, NULL, 750.000, '2026-03-28', '1', 'YAPE', NULL);
INSERT INTO `dias_ventas` VALUES (413, 231, NULL, NULL, 340.000, '2026-04-16', '1', 'EFECTIVO', NULL);

-- ----------------------------
-- Table structure for documentos_empresas
-- ----------------------------
DROP TABLE IF EXISTS `documentos_empresas`;
CREATE TABLE `documentos_empresas`  (
  `id_empresa` int NOT NULL,
  `id_tido` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `serie` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  INDEX `fk_empresas_has_documentos_sunat_documentos_sunat1_idx`(`id_tido` ASC) USING BTREE,
  INDEX `fk_empresas_has_documentos_sunat_empresas1_idx`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of documentos_empresas
-- ----------------------------
INSERT INTO `documentos_empresas` VALUES (12, 1, 1, 'B001', 598);
INSERT INTO `documentos_empresas` VALUES (12, 2, 1, 'F001', 2358);
INSERT INTO `documentos_empresas` VALUES (12, 3, 1, 'F001', 7);
INSERT INTO `documentos_empresas` VALUES (12, 4, 1, 'F001', 1);
INSERT INTO `documentos_empresas` VALUES (12, 6, 1, 'NV01', 2946);
INSERT INTO `documentos_empresas` VALUES (12, 11, 1, 'T001', 1031);
INSERT INTO `documentos_empresas` VALUES (12, 1, 2, 'B002', 598);
INSERT INTO `documentos_empresas` VALUES (12, 2, 2, 'F002', 2359);
INSERT INTO `documentos_empresas` VALUES (12, 3, 2, 'F002', 6);
INSERT INTO `documentos_empresas` VALUES (12, 4, 2, 'F002', 1);
INSERT INTO `documentos_empresas` VALUES (12, 6, 2, 'NV02', 2946);
INSERT INTO `documentos_empresas` VALUES (12, 11, 2, 'T002', 1025);

-- ----------------------------
-- Table structure for documentos_sunat
-- ----------------------------
DROP TABLE IF EXISTS `documentos_sunat`;
CREATE TABLE `documentos_sunat`  (
  `id_tido` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cod_sunat` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `abreviatura` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_tido`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of documentos_sunat
-- ----------------------------
INSERT INTO `documentos_sunat` VALUES (1, 'BOLETA DE VENTA', '03', 'BT');
INSERT INTO `documentos_sunat` VALUES (2, 'FACTURA', '01', 'FT');
INSERT INTO `documentos_sunat` VALUES (3, 'NOTA DE CREDITO', '07', 'NC');
INSERT INTO `documentos_sunat` VALUES (4, 'NOTA DE DEBITO', '08', 'ND');
INSERT INTO `documentos_sunat` VALUES (5, 'NOTA DE RECEPCION', '09', 'GR');
INSERT INTO `documentos_sunat` VALUES (6, 'NOTA DE VENTA', '00', 'NV');
INSERT INTO `documentos_sunat` VALUES (7, 'NOTA DE SEPARACION', '00', 'NS');
INSERT INTO `documentos_sunat` VALUES (8, 'NOTA DE TRASLADO', '00', 'NT');
INSERT INTO `documentos_sunat` VALUES (9, 'NOTA DE INVENTARIO', '00', 'NIV');
INSERT INTO `documentos_sunat` VALUES (10, 'NOTA DE INGRESO', '00', 'NIG');
INSERT INTO `documentos_sunat` VALUES (11, 'GUIA DE REMISION', '09', 'GR');
INSERT INTO `documentos_sunat` VALUES (12, 'NOTA DE COMPRA', '00', NULL);

-- ----------------------------
-- Table structure for empresas
-- ----------------------------
DROP TABLE IF EXISTS `empresas`;
CREATE TABLE `empresas`  (
  `id_empresa` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `razon_social` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `comercial` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `cod_sucursal` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `email` varchar(145) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `password` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `user_sol` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `clave_sol` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `logo` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ubigeo` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `distrito` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `provincia` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `departamento` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `tipo_impresion` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `modo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `igv` double(10, 2) NULL DEFAULT 0.18,
  `propaganda` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono2` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono3` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_empresa`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 35 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of empresas
-- ----------------------------
INSERT INTO `empresas` VALUES (12, '20605356631', 'TITANIC M & S SOCIEDAD ANONIMA CERRADA - TITANIC M & S S.A.C.', 'TITANIC M & S S.A.C.', NULL, 'MANCO INCA NRO. 674 (ALT. DE LA CDRA 7 DE PIZARRO)', 'info@titanicsac.com', '988078613', '1', NULL, 'BIKERIM1', 'Biker123', 'nUmdN40McVy2i1IZUthiXjhcyOSra7IEmu3sDwf3ZBcixKbYwQjxwR4KpF09xyaWsSxUWAtSQH4AXhc0.png', '150128', 'RIMAC', 'LIMA', 'LIMA', NULL, 'beta', 0.18, '', NULL, NULL);

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for guia_detalle_transporte
-- ----------------------------
DROP TABLE IF EXISTS `guia_detalle_transporte`;
CREATE TABLE `guia_detalle_transporte`  (
  `id` int NOT NULL,
  `id_guia` int NULL DEFAULT NULL,
  `bien_normalizado` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `codigo_bien` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `codigo_sunat` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `partida_arancelaria` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `codigo_gtin` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `descripcion_detallada` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `unidad_medida` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `cantidad` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_detalle_transporte
-- ----------------------------

-- ----------------------------
-- Table structure for guia_detalles
-- ----------------------------
DROP TABLE IF EXISTS `guia_detalles`;
CREATE TABLE `guia_detalles`  (
  `guia_detalle_id` int NOT NULL AUTO_INCREMENT,
  `id_guia` int NULL DEFAULT NULL,
  `id_producto` int NULL DEFAULT NULL,
  `detalles` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `unidad` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `cantidad` int NULL DEFAULT NULL,
  `precio` double(20, 5) NULL DEFAULT NULL,
  PRIMARY KEY (`guia_detalle_id`) USING BTREE,
  INDEX `id_guia`(`id_guia` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5597 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_detalles
-- ----------------------------
INSERT INTO `guia_detalles` VALUES (5588, 419, 352, '4585 | SAL JJ D MAR 1K*25                                ', 'NIU', 1, 20.00000);
INSERT INTO `guia_detalles` VALUES (5589, 420, 174, '4585 | F CINTA ROSCA NAPOLI*5K                           ', 'NIU', 1, 24.00000);
INSERT INTO `guia_detalles` VALUES (5590, 420, 178, '4585 | F ROSCA FINA NAPOLI*5K                            ', 'NIU', 1, 24.00000);
INSERT INTO `guia_detalles` VALUES (5591, 420, 179, '4585 | F ROSCA GRUESO*5K                                 ', 'NIU', 1, 24.00000);
INSERT INTO `guia_detalles` VALUES (5592, 421, 174, '4585 | F CINTA ROSCA NAPOLI*5K                           ', 'NIU', 1, 24.00000);
INSERT INTO `guia_detalles` VALUES (5593, 421, 178, '4585 | F ROSCA FINA NAPOLI*5K                            ', 'NIU', 1, 24.00000);
INSERT INTO `guia_detalles` VALUES (5594, 421, 179, '4585 | F ROSCA GRUESO*5K                                 ', 'NIU', 1, 24.00000);
INSERT INTO `guia_detalles` VALUES (5595, 422, 15, '4585 | ACEITE PATRONA *1LT                               ', 'NIU', 1, 80.00000);
INSERT INTO `guia_detalles` VALUES (5596, 422, 120, '4585 |  CAMANEJO X1KG                                     ', 'NIU', 1, 6.20000);

-- ----------------------------
-- Table structure for guia_remision
-- ----------------------------
DROP TABLE IF EXISTS `guia_remision`;
CREATE TABLE `guia_remision`  (
  `id_guia_remision` int NOT NULL AUTO_INCREMENT,
  `id_venta` int NOT NULL,
  `fecha_emision` date NULL DEFAULT NULL,
  `dir_llegada` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ubigeo` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `tipo_transporte` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ruc_transporte` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `razon_transporte` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `vehiculo` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `chofer_brevete` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `enviado_sunat` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `hash` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `serie` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `peso` double(8, 2) NULL DEFAULT NULL,
  `nro_bultos` int NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_guia_remision`) USING BTREE,
  INDEX `fk_guia_remision_ventas1_idx`(`id_venta` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 423 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_remision
-- ----------------------------
INSERT INTO `guia_remision` VALUES (418, 0, '2025-03-11', '', '010202', '1', '', '', '', '', '0', '', '', 'T001', 1026, 1.00, 1, '1', 12, 1);
INSERT INTO `guia_remision` VALUES (419, 204, '2025-08-19', 'MDO V ROSSY', '010202', '1', '', '', '', '', '0', '', '', 'T001', 1027, 1.00, 1, '1', 12, 1);
INSERT INTO `guia_remision` VALUES (420, 220, '2025-08-19', 'MDO V ROSSY', '010202', '1', '', '', '', '', '0', '', '', 'T001', 1028, 1.00, 1, '1', 12, 1);
INSERT INTO `guia_remision` VALUES (421, 221, '2025-08-19', 'MDO V ROSSY', '010202', '1', '', '', '', '', '0', '', '', 'T001', 1029, 1.00, 1, '1', 12, 1);
INSERT INTO `guia_remision` VALUES (422, 224, '2025-08-19', 'MDO V ROSSY', '010202', '1', '', '', '', '', '0', '', '', 'T001', 1030, 1.00, 1, '1', 12, 1);

-- ----------------------------
-- Table structure for guia_sunat
-- ----------------------------
DROP TABLE IF EXISTS `guia_sunat`;
CREATE TABLE `guia_sunat`  (
  `id_guia` int NOT NULL,
  `hash` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `qr_data` varchar(220) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_guia`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_sunat
-- ----------------------------

-- ----------------------------
-- Table structure for guia_transporte
-- ----------------------------
DROP TABLE IF EXISTS `guia_transporte`;
CREATE TABLE `guia_transporte`  (
  `id_guia_remision` int NOT NULL AUTO_INCREMENT,
  `id_venta` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `fecha_emision` date NULL DEFAULT NULL,
  `dir_llegada` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ubigeo` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `tipo_transporte` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ruc_transporte` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `razon_transporte` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `vehiculo` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `chofer_brevete` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `enviado_sunat` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `hash` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `serie` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `peso` double(8, 2) NULL DEFAULT NULL,
  `nro_bultos` int NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `transbordo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `retorno` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `subcontratado` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `envases` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `pagador` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `subcontratador` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `flete` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `observacion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nom_cli` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_guia_remision`) USING BTREE,
  INDEX `fk_guia_remision_ventas1_idx`(`id_venta` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_transporte
-- ----------------------------
INSERT INTO `guia_transporte` VALUES (1, '20554454276', '2024-06-10', 'AV. LOS MAESTROS NRO 206 FND SAN JOSÉ INT 101 - ICA  ICA - ICA', '150101', '1', '20554454276', 'LABORATORIOS CLINICOS MULTIPLES S.A.C.', 'MGF-322', '72314107', '0', '', '', 'T001', 626, 0.00, 0, '1', 12, 2, 'si', 'no', 'si', 'no', 'Subcontratador', 'EXACTA OPERADOR LOGISTICO SOCIEDAD ANONIMA CERRADA - REGISTRO ÚNICO DE CONTRIBUYENTES N° 20517650871', 'EXACTA OPERADOR LOGISTICO SOCIEDAD ANONIMA CERRADA - REGISTRO ÚNICO DE CONTRIBUYENTES N° 20517650871', 'SEGUN  GUÍA DE REMISIÓN ELECTRÓNICA  TRANSPORTISTA  N° EG03 - 00026899', 'TIENDAS POR DEPARTAMENTO RIPLEY S.A.C. - REGISTRO ÚNICO DE CONTRIBUYENTES ');

-- ----------------------------
-- Table structure for ingreso_egreso
-- ----------------------------
DROP TABLE IF EXISTS `ingreso_egreso`;
CREATE TABLE `ingreso_egreso`  (
  `intercambio_id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NULL DEFAULT NULL,
  `tipo` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cantidad` int NULL DEFAULT NULL,
  `almacen_ingreso` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `almacen_egreso` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '2' COMMENT '2 = solo ingreso',
  `instrumento_tipo` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `instrumento_id` int UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`intercambio_id`) USING BTREE,
  INDEX `id_usuario`(`id_usuario` ASC) USING BTREE,
  INDEX `id_producto`(`id_producto` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ingreso_egreso
-- ----------------------------
INSERT INTO `ingreso_egreso` VALUES (4, 1, 'e', 1, '1', '1', 40, '1', NULL, NULL);
INSERT INTO `ingreso_egreso` VALUES (5, 0, 'i', 1, '1', NULL, 40, '2', NULL, NULL);
INSERT INTO `ingreso_egreso` VALUES (6, 207, 'e', 2, '2', '1', 40, '1', NULL, NULL);
INSERT INTO `ingreso_egreso` VALUES (7, 165, 'i', 3, '2', NULL, 40, '2', NULL, NULL);
INSERT INTO `ingreso_egreso` VALUES (8, 163, 'e', 3, '2', '1', 40, '1', NULL, NULL);
INSERT INTO `ingreso_egreso` VALUES (9, 209, 'e', 2, '2', '1', 40, '1', NULL, NULL);
INSERT INTO `ingreso_egreso` VALUES (10, 996, 'e', 4, '2', '1', 40, '0', NULL, NULL);
INSERT INTO `ingreso_egreso` VALUES (11, 2160, 'e', 4, '2', '1', 40, '0', NULL, NULL);
INSERT INTO `ingreso_egreso` VALUES (12, 121, 'i', 1500, '1', NULL, 40, '2', NULL, NULL);

-- ----------------------------
-- Table structure for inventario_movimientos
-- ----------------------------
DROP TABLE IF EXISTS `inventario_movimientos`;
CREATE TABLE `inventario_movimientos`  (
  `id_movimiento` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `almacen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_producto` int NOT NULL,
  `tipo` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_motivo` int NULL DEFAULT NULL,
  `cantidad` int NOT NULL,
  `stock_anterior` int NOT NULL DEFAULT 0,
  `stock_nuevo` int NOT NULL DEFAULT 0,
  `costo` decimal(12, 4) NULL DEFAULT NULL,
  `id_proveedor` int NULL DEFAULT NULL,
  `observacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id_movimiento`) USING BTREE,
  INDEX `inventario_movimientos_id_empresa_index`(`id_empresa` ASC) USING BTREE,
  INDEX `inventario_movimientos_id_producto_index`(`id_producto` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of inventario_movimientos
-- ----------------------------
INSERT INTO `inventario_movimientos` VALUES (1, 12, '1', 7, 'S', 6, 1, 10000, 9999, 70.0000, NULL, 'Venta B001-00000598', 40, '2026-06-26 17:56:24');

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for marcas
-- ----------------------------
DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas`  (
  `id_marca` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_marca`) USING BTREE,
  INDEX `marcas_id_empresa_index`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of marcas
-- ----------------------------

-- ----------------------------
-- Table structure for mes
-- ----------------------------
DROP TABLE IF EXISTS `mes`;
CREATE TABLE `mes`  (
  `id` int NOT NULL,
  `nombre` varchar(12) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of mes
-- ----------------------------
INSERT INTO `mes` VALUES (1, 'Ene');
INSERT INTO `mes` VALUES (2, 'Feb');
INSERT INTO `mes` VALUES (3, 'Mar');
INSERT INTO `mes` VALUES (4, 'Abr');
INSERT INTO `mes` VALUES (5, 'May');
INSERT INTO `mes` VALUES (6, 'Jun');
INSERT INTO `mes` VALUES (7, 'Jul');
INSERT INTO `mes` VALUES (8, 'Ago');
INSERT INTO `mes` VALUES (9, 'Set');
INSERT INTO `mes` VALUES (10, 'Oct');
INSERT INTO `mes` VALUES (11, 'Nov');
INSERT INTO `mes` VALUES (12, 'Dic');

-- ----------------------------
-- Table structure for metodo_pago
-- ----------------------------
DROP TABLE IF EXISTS `metodo_pago`;
CREATE TABLE `metodo_pago`  (
  `id_metodo_pago` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  PRIMARY KEY (`id_metodo_pago`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of metodo_pago
-- ----------------------------
INSERT INTO `metodo_pago` VALUES (1, 'TRANSFERENCIA BANCO BCP', '1');
INSERT INTO `metodo_pago` VALUES (2, 'TRANSFERENCIA BANCO NACION', '1');
INSERT INTO `metodo_pago` VALUES (3, 'TRANSFERENCIA BANCO INTERBANK', '1');
INSERT INTO `metodo_pago` VALUES (4, 'TRANSFERENCIA BANCO BBVA', '1');
INSERT INTO `metodo_pago` VALUES (5, 'YAPE', '1');
INSERT INTO `metodo_pago` VALUES (6, 'PLIN', '1');
INSERT INTO `metodo_pago` VALUES (7, 'TARJETA DE CREDITO VISA', '0');
INSERT INTO `metodo_pago` VALUES (8, 'TARJETA DE CREDITO MASTERCARD', '0');
INSERT INTO `metodo_pago` VALUES (9, 'TARJETA DE CREDITO DINNERS CLUB', '0');
INSERT INTO `metodo_pago` VALUES (10, 'POS ', '1');
INSERT INTO `metodo_pago` VALUES (11, 'TRANSFERENCIA BANCO SCOTIABANK', '1');
INSERT INTO `metodo_pago` VALUES (12, 'EFECTIVO', '1');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 46 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2024_01_01_000000_create_usuarios_table', 1);
INSERT INTO `migrations` VALUES (2, '2024_01_01_000001_create_sessions_table', 1);
INSERT INTO `migrations` VALUES (3, '2026_05_07_164349_create_permission_tables', 1);
INSERT INTO `migrations` VALUES (4, '2026_06_25_163621_fix_caja_empresa_and_ingreso_egreso_schema', 1);
INSERT INTO `migrations` VALUES (5, '2024_01_01_000002_add_security_columns', 2);
INSERT INTO `migrations` VALUES (6, '2026_06_24_000000_create_catalogo_tables', 3);
INSERT INTO `migrations` VALUES (7, '2026_06_24_000001_add_descripcion_to_catalogo', 3);
INSERT INTO `migrations` VALUES (8, '2026_06_24_000002_create_almacenes_table', 3);
INSERT INTO `migrations` VALUES (9, '2026_06_24_000003_create_inventario_movimientos', 3);
INSERT INTO `migrations` VALUES (10, '2026_06_24_000004_add_recepcionado_to_compras', 3);
INSERT INTO `migrations` VALUES (11, '2026_06_24_000005_create_prestamos', 3);
INSERT INTO `migrations` VALUES (12, '2026_06_24_000006_add_nombre_to_sucursales', 3);
INSERT INTO `migrations` VALUES (13, '2026_06_25_134257_create_prestamo_detalle_table', 3);
INSERT INTO `migrations` VALUES (14, '2026_06_25_140000_create_traslados_tables', 3);
INSERT INTO `migrations` VALUES (15, '2026_06_25_150000_add_snapshots_to_traslado_detalle', 3);
INSERT INTO `migrations` VALUES (16, '2026_06_25_160000_create_prestamo_devoluciones', 3);
INSERT INTO `migrations` VALUES (17, '2026_06_25_160846_create_bancos_table', 3);
INSERT INTO `migrations` VALUES (18, '2026_06_25_160851_create_cuentas_bancarias_table', 3);
INSERT INTO `migrations` VALUES (19, '2026_06_25_160856_create_tarjetas_table', 3);
INSERT INTO `migrations` VALUES (20, '2026_06_25_160900_create_billeteras_digitales_table', 3);
INSERT INTO `migrations` VALUES (21, '2026_06_25_160904_add_instrumento_pago_to_compras_table', 3);
INSERT INTO `migrations` VALUES (22, '2026_06_25_162145_add_instrumento_pago_to_caja_empresa_table', 3);
INSERT INTO `migrations` VALUES (23, '2026_06_25_162753_add_instrumento_pago_to_ingreso_egreso_table', 3);
INSERT INTO `migrations` VALUES (24, '2026_06_25_164838_create_billetera_tipos_and_update_billeteras_digitales', 3);
INSERT INTO `migrations` VALUES (25, '2026_06_25_170000_create_compra_recepciones', 3);
INSERT INTO `migrations` VALUES (26, '2026_06_25_180000_create_recepciones', 3);
INSERT INTO `migrations` VALUES (27, '2026_06_25_174733_create_caja_movimientos_table', 4);
INSERT INTO `migrations` VALUES (28, '2026_06_25_174733_create_cajas_table', 4);
INSERT INTO `migrations` VALUES (29, '2026_06_25_174734_create_caja_instrumentos_table', 4);
INSERT INTO `migrations` VALUES (30, '2026_06_25_174735_create_arqueo_detalle_table', 4);
INSERT INTO `migrations` VALUES (31, '2026_06_25_174735_create_caja_chica_rendiciones_table', 4);
INSERT INTO `migrations` VALUES (32, '2026_06_25_174736_alter_arqueos_diarios_add_id_caja', 4);
INSERT INTO `migrations` VALUES (33, '2026_06_25_175035_seed_cajas_from_old_tables', 5);
INSERT INTO `migrations` VALUES (34, '2026_06_25_190000_alter_cajas_drop_tipo', 5);
INSERT INTO `migrations` VALUES (35, '2026_06_25_190100_create_cierre_caja_table', 5);
INSERT INTO `migrations` VALUES (36, '2026_06_25_191000_convert_caja_instrumentos_to_metodos_pago', 5);
INSERT INTO `migrations` VALUES (37, '2026_06_25_191500_widen_productos_almacen', 5);
INSERT INTO `migrations` VALUES (38, '2026_06_25_231544_add_id_venta_to_cotizaciones', 6);
INSERT INTO `migrations` VALUES (39, '2026_06_25_191600_add_instrumento_pago_to_dias_compras', 7);
INSERT INTO `migrations` VALUES (40, '2026_06_26_000001_create_guia_remision_table', 8);
INSERT INTO `migrations` VALUES (41, '2026_06_26_000002_create_guia_detalles_table', 8);
INSERT INTO `migrations` VALUES (42, '2026_06_26_000003_create_notas_electronicas_table', 9);
INSERT INTO `migrations` VALUES (43, '2026_06_26_000004_add_missing_cols_notas_electronicas', 9);
INSERT INTO `migrations` VALUES (44, '2026_06_26_000005_add_subtotal_to_ventas', 10);
INSERT INTO `migrations` VALUES (45, '2026_06_26_000006_add_cols_to_productos_ventas', 11);

-- ----------------------------
-- Table structure for model_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions`  (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `model_has_permissions_model_index`(`model_id` ASC, `model_type` ASC) USING BTREE,
  CONSTRAINT `mhp_permission_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of model_has_permissions
-- ----------------------------

-- ----------------------------
-- Table structure for model_has_roles
-- ----------------------------
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles`  (
  `role_id` int NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `model_has_roles_model_index`(`model_id` ASC, `model_type` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of model_has_roles
-- ----------------------------

-- ----------------------------
-- Table structure for motivo_documento
-- ----------------------------
DROP TABLE IF EXISTS `motivo_documento`;
CREATE TABLE `motivo_documento`  (
  `id_motivo` int NOT NULL,
  `codigo` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombre` varchar(145) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_tido` int NOT NULL,
  PRIMARY KEY (`id_motivo`) USING BTREE,
  INDEX `fk_motivo_documento_documentos_sunat1_idx`(`id_tido` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of motivo_documento
-- ----------------------------
INSERT INTO `motivo_documento` VALUES (1, '01', 'Anulación de la operacion', 3);
INSERT INTO `motivo_documento` VALUES (2, '02', 'Anulación por error en el RUC', 3);
INSERT INTO `motivo_documento` VALUES (3, '03', 'Corrección por error en la descripción', 3);
INSERT INTO `motivo_documento` VALUES (4, '10', 'Otros Conceptos', 3);
INSERT INTO `motivo_documento` VALUES (5, '01', 'Intereses por mora', 4);
INSERT INTO `motivo_documento` VALUES (6, '02', 'Aumento en el valor', 4);
INSERT INTO `motivo_documento` VALUES (7, '03', 'Penalidades/ otros conceptos', 4);

-- ----------------------------
-- Table structure for motivos_movimiento
-- ----------------------------
DROP TABLE IF EXISTS `motivos_movimiento`;
CREATE TABLE `motivos_movimiento`  (
  `id_motivo` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `es_sistema` tinyint NOT NULL DEFAULT 0,
  `id_empresa` int NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_motivo`) USING BTREE,
  INDEX `motivos_movimiento_id_empresa_index`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of motivos_movimiento
-- ----------------------------
INSERT INTO `motivos_movimiento` VALUES (1, 'Carga inicial', 'I', 0, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (2, 'Compra', 'I', 1, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (3, 'Ajuste positivo', 'I', 0, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (4, 'Devolución de cliente', 'I', 0, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (5, 'Traslado entrada', 'I', 0, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (6, 'Venta', 'S', 1, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (7, 'Ajuste negativo', 'S', 0, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (8, 'Merma / pérdida', 'S', 0, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (9, 'Traslado salida', 'S', 0, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (10, 'Consumo interno', 'S', 0, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (11, 'Préstamo entregado', 'S', 1, 12, '1');
INSERT INTO `motivos_movimiento` VALUES (12, 'Préstamo recibido', 'I', 1, 12, '1');

-- ----------------------------
-- Table structure for notas_electronicas
-- ----------------------------
DROP TABLE IF EXISTS `notas_electronicas`;
CREATE TABLE `notas_electronicas`  (
  `nota_id` int NOT NULL,
  `id_venta` int NULL DEFAULT NULL,
  `tipo` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_empresa` int NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `tido` int NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `serie` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `total` decimal(10, 2) NULL DEFAULT 0.00,
  `motivo` int NULL DEFAULT NULL,
  `cod_motivo` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '01',
  `monto` double(15, 2) NULL DEFAULT NULL,
  `productos` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `estado_sunat` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0',
  `estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '1',
  `fecha_emision` date NULL DEFAULT NULL,
  `hash` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `enviado_sunat` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0',
  PRIMARY KEY (`nota_id`) USING BTREE,
  INDEX `tido`(`tido` ASC) USING BTREE,
  INDEX `id_venta`(`id_venta` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of notas_electronicas
-- ----------------------------
INSERT INTO `notas_electronicas` VALUES (0, 8, NULL, 12, 1, 3, '2024-08-03', 'F001', 6, 0.00, 1, '01', 192.00, '[{\"productoid\":\"\",\"descripcion\":\"kuatitos\",\"cantidad\":\"1\",\"precio\":\"192\",\"codigo\":\"\",\"costo\":\"\"}]', '0', '1', NULL, NULL, NULL, '0');

-- ----------------------------
-- Table structure for notas_electronicas_sunat
-- ----------------------------
DROP TABLE IF EXISTS `notas_electronicas_sunat`;
CREATE TABLE `notas_electronicas_sunat`  (
  `id_notas_electronicas` int NOT NULL,
  `hash` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `qr_data` varchar(220) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_notas_electronicas`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of notas_electronicas_sunat
-- ----------------------------
INSERT INTO `notas_electronicas_sunat` VALUES (0, 'CWk0mb9Jh88O1xTUKF6lZrWUjbo=', '20603319274-07-F001-6', '20603319274|07|F001-6|29.29|29.29|2024-08-03|0|00000000');

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `permissions_name_guard_name_unique`(`name` ASC, `guard_name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES (1, 'ventas.ver', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (2, 'ventas.crear', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (3, 'ventas.anular', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (4, 'compras.ver', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (5, 'compras.crear', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (6, 'clientes.ver', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (7, 'clientes.editar', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (8, 'clientes.borrar', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (9, 'productos.ver', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (10, 'productos.editar', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (11, 'reportes.ver', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (12, 'reportes.exportar', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (13, 'usuarios.gestionar', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (14, 'empresas.gestionar', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (15, 'caja.ver', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (16, 'caja.gestionar', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (17, 'cotizaciones.ver', 'web', '2026-05-07 06:38:08', NULL);
INSERT INTO `permissions` VALUES (18, 'cotizaciones.crear', 'web', '2026-05-07 06:38:08', NULL);

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `personal_access_tokens_token_unique`(`token` ASC) USING BTREE,
  INDEX `pat_tokenable_index`(`tokenable_type` ASC, `tokenable_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for prestamo_detalle
-- ----------------------------
DROP TABLE IF EXISTS `prestamo_detalle`;
CREATE TABLE `prestamo_detalle`  (
  `id_detalle` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_prestamo` int UNSIGNED NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detalle`) USING BTREE,
  INDEX `prestamo_detalle_id_prestamo_index`(`id_prestamo` ASC) USING BTREE,
  CONSTRAINT `prestamo_detalle_id_prestamo_foreign` FOREIGN KEY (`id_prestamo`) REFERENCES `prestamos` (`id_prestamo`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of prestamo_detalle
-- ----------------------------

-- ----------------------------
-- Table structure for prestamo_devoluciones
-- ----------------------------
DROP TABLE IF EXISTS `prestamo_devoluciones`;
CREATE TABLE `prestamo_devoluciones`  (
  `id_devolucion` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_prestamo` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `fecha` datetime NOT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_devolucion`) USING BTREE,
  INDEX `prestamo_devoluciones_id_prestamo_index`(`id_prestamo` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of prestamo_devoluciones
-- ----------------------------

-- ----------------------------
-- Table structure for prestamos
-- ----------------------------
DROP TABLE IF EXISTS `prestamos`;
CREATE TABLE `prestamos`  (
  `id_prestamo` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `tipo` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tercero` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_producto` int NOT NULL,
  `almacen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'P',
  `observacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `fecha_devolucion` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id_prestamo`) USING BTREE,
  INDEX `prestamos_id_empresa_index`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of prestamos
-- ----------------------------

-- ----------------------------
-- Table structure for productos
-- ----------------------------
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos`  (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `cod_barra` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `descripcion` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `precio` double(10, 4) NULL DEFAULT NULL,
  `costo` double(10, 4) NULL DEFAULT NULL,
  `cantidad` int NULL DEFAULT NULL,
  `iscbp` int NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `ultima_salida` date NOT NULL,
  `codsunat` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `usar_barra` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '0',
  `precio_mayor` double(10, 4) NULL DEFAULT NULL,
  `precio_menor` double(10, 4) NULL DEFAULT NULL,
  `peso_bruto` decimal(10, 2) NULL DEFAULT 0.00,
  `razon_social` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ruc` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  `almacen` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `precio2` double(10, 4) NULL DEFAULT 0.0000,
  `precio3` double(10, 4) NULL DEFAULT 0.0000,
  `precio4` double(10, 4) NULL DEFAULT 0.0000,
  `precio_unidad` double(10, 4) NULL DEFAULT NULL,
  `codigo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_categoria` int NULL DEFAULT NULL,
  `id_subcategoria` int NULL DEFAULT NULL,
  `id_marca` int NULL DEFAULT NULL,
  `id_submarca` int NULL DEFAULT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `activo` int NOT NULL DEFAULT 1,
  `medida` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT 'Unidad',
  `presentaciones` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cnt_presenta` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_producto`) USING BTREE,
  INDEX `fk_productos_empresas1_idx`(`id_empresa` ASC) USING BTREE,
  INDEX `idx_prod_empresa_estado`(`id_empresa` ASC, `estado` ASC) USING BTREE,
  INDEX `idx_prod_barra`(`cod_barra` ASC) USING BTREE,
  INDEX `idx_prod_codigo`(`codigo` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 414 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of productos
-- ----------------------------
INSERT INTO `productos` VALUES (409, NULL, 'JABÓN DE ROPA BELTRA*175 GMS', 45.0000, 43.0000, 1, 0, 12, 1, '1000-01-01', 'Jabel0001', '0', 1.0000, 1.0000, 1.00, 'CORPORACION BELTRAN ESPINOZA E.I.R.L. - COBELES E.I.R.L.', '20602096808', '1', '2', 46.0000, 47.0000, 44.0000, 44.0000, 'Jabel0001', NULL, NULL, NULL, NULL, NULL, 1, 'Cajas', '2', '1,2,3');
INSERT INTO `productos` VALUES (410, '', 'AZUCAR BLANCA IMPORTADA*50K', 125.0000, 120.0000, 20, 0, 12, 1, '1000-01-01', '13422', '0', 1.0000, 1.0000, 50.00, 'MANUEL QUISPE                                               ', '10099666922', '1', '1', 140.0000, 141.0000, 138.0000, 138.0000, '13422', NULL, NULL, NULL, NULL, NULL, 0, 'Unidad', '4', '1,2,3,4,5');
INSERT INTO `productos` VALUES (411, NULL, 'Azúcar Cartavio Blaco', 157.0000, 152.0000, 20, 0, 12, 1, '1000-01-01', '13422', '0', 1.0000, 1.0000, 1.00, 'MANUEL QUISPE                                               ', '10099666922', '1', '2', 0.0000, 0.0000, 0.0000, NULL, '13422', NULL, NULL, NULL, NULL, NULL, 1, 'Sacos', '4', '1,2,3,4,5');
INSERT INTO `productos` VALUES (412, '', 'LENTEJA  BB VERDE *SACO VERDE', 135.0000, 120.0000, 451, 0, 12, 1, '1000-01-01', '100198', '0', 1.0000, 1.0000, 45.36, 'INTERCOMPANY Y SR HUANCA                                    ', '20468985757', '1', '1', 136.0000, 137.0000, 135.0000, 133.0000, '100198', NULL, NULL, NULL, NULL, NULL, 1, 'Unidad', '4', '1,2,3,4,5,6');
INSERT INTO `productos` VALUES (413, NULL, 'LENTEJA ESTON USA BB VERDE *45.36K', 180.0000, 1.0000, 100, 0, 12, 1, '1000-01-01', '100198', '0', 1.0000, 1.0000, 1.00, 'INTERCOMPANY Y SR HUANCA                                    ', '20468985757', '1', '2', 185.0000, 185.0000, 177.0000, 175.0000, '100198', NULL, NULL, NULL, NULL, NULL, 1, 'Sacos', '4', '1,2,3,4,5,6');

-- ----------------------------
-- Table structure for productos_compras
-- ----------------------------
DROP TABLE IF EXISTS `productos_compras`;
CREATE TABLE `productos_compras`  (
  `id_producto_venta` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NULL DEFAULT NULL,
  `id_compra` int NULL DEFAULT NULL,
  `cantidad` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `precio` double(10, 3) NULL DEFAULT NULL,
  `costo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_producto_venta`) USING BTREE,
  INDEX `id_producto`(`id_producto` ASC) USING BTREE,
  INDEX `id_compra`(`id_compra` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 399 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of productos_compras
-- ----------------------------
INSERT INTO `productos_compras` VALUES (242, 319, 111, '1000', 4.900, NULL);
INSERT INTO `productos_compras` VALUES (243, 129, 111, '500', 3.000, NULL);
INSERT INTO `productos_compras` VALUES (254, 129, 118, '500', 3.100, NULL);
INSERT INTO `productos_compras` VALUES (255, 319, 118, '1000', 5.300, NULL);
INSERT INTO `productos_compras` VALUES (256, 222, 118, '550', 5.400, NULL);
INSERT INTO `productos_compras` VALUES (302, 236, 134, '1000', 2.500, NULL);
INSERT INTO `productos_compras` VALUES (303, 238, 134, '1000', 3.800, NULL);
INSERT INTO `productos_compras` VALUES (304, 335, 134, '500', 3.900, NULL);
INSERT INTO `productos_compras` VALUES (327, 129, 142, '1000', 3.100, NULL);
INSERT INTO `productos_compras` VALUES (383, 85, 155, '340', 161.000, NULL);
INSERT INTO `productos_compras` VALUES (386, 9, 157, '500', 62.000, NULL);

-- ----------------------------
-- Table structure for productos_cotis
-- ----------------------------
DROP TABLE IF EXISTS `productos_cotis`;
CREATE TABLE `productos_cotis`  (
  `prod_coti_id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `id_coti` int NOT NULL,
  `cantidad` double(6, 2) NULL DEFAULT NULL,
  `precio` double(10, 5) NULL DEFAULT NULL,
  `precio_producto` decimal(10, 5) NULL DEFAULT NULL,
  `name_precio_producto` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `costo` double(10, 5) NULL DEFAULT NULL,
  `medida` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `presenta` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `presenta_cnt` int NULL DEFAULT NULL,
  PRIMARY KEY (`prod_coti_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 485176 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of productos_cotis
-- ----------------------------
INSERT INTO `productos_cotis` VALUES (34466, 120, 6193, 1.00, 33.50000, 6.70000, 'precio4', 7.50000, 'Kilos', '1', 5);
INSERT INTO `productos_cotis` VALUES (34467, 293, 6193, 1.00, 33.00000, 11.00000, 'precio', 9.20000, 'Kilos', '1', 3);
INSERT INTO `productos_cotis` VALUES (34468, 320, 6194, 1.00, 14.00000, 2.80000, 'precio', 2.20000, 'Kilos', '1', 5);
INSERT INTO `productos_cotis` VALUES (34496, 236, 6197, 1.00, 9.90000, 3.30000, 'precio', 2.90000, 'Kilos', '1', 3);
INSERT INTO `productos_cotis` VALUES (34497, 238, 6197, 1.00, 13.50000, 4.50000, 'precio', 4.10000, 'Kilos', '1', 3);
INSERT INTO `productos_cotis` VALUES (34498, 342, 6197, 1.00, 31.50000, 10.50000, 'precio', 9.70000, 'Kilos', '1', 3);
INSERT INTO `productos_cotis` VALUES (34499, 120, 6197, 1.00, 20.10000, 6.70000, 'precio4', 7.50000, 'Kilos', '1', 3);
INSERT INTO `productos_cotis` VALUES (34500, 319, 6197, 1.00, 20.10000, 6.70000, 'sin referencia', 6.00000, 'Kilos', '1', 3);
INSERT INTO `productos_cotis` VALUES (34501, 285, 6197, 1.00, 16.50000, 5.50000, 'precio', 4.60000, 'Kilos', '1', 3);
INSERT INTO `productos_cotis` VALUES (34502, 336, 6197, 1.00, 12.60000, 4.20000, 'precio', 3.50000, 'Kilos', '', 3);
INSERT INTO `productos_cotis` VALUES (41409, 290, 6195, 1.00, 60.00000, 6.00000, 'precio', 5.20000, 'Kilos', '1', 10);
INSERT INTO `productos_cotis` VALUES (41410, 92, 6195, 1.00, 47.00000, 4.70000, 'precio', 4.40000, 'Kilos', '1', 10);
INSERT INTO `productos_cotis` VALUES (41411, 285, 6195, 1.00, 55.00000, 5.50000, 'precio', 4.60000, 'Kilos', '1', 10);
INSERT INTO `productos_cotis` VALUES (41412, 96, 6195, 1.00, 40.00000, 40.00000, 'sin referencia', 40.00000, 'Unidad', '1', 1);
INSERT INTO `productos_cotis` VALUES (41413, 120, 6195, 1.00, 67.00000, 6.70000, 'precio4', 7.50000, 'Kilos', '1', 10);
INSERT INTO `productos_cotis` VALUES (41414, 342, 6195, 1.00, 105.00000, 10.50000, 'precio', 9.70000, 'Kilos', '1', 10);
INSERT INTO `productos_cotis` VALUES (41415, 258, 6195, 1.00, 22.50000, 4.50000, 'precio', 4.20000, 'Kilos', '1', 5);
INSERT INTO `productos_cotis` VALUES (41416, 257, 6195, 1.00, 22.50000, 4.50000, 'precio', 3.80000, 'Kilos', '1', 5);
INSERT INTO `productos_cotis` VALUES (41417, 29, 6195, 1.00, 45.00000, 9.00000, 'sin referencia', 9.00000, 'Kilos', '', 5);
INSERT INTO `productos_cotis` VALUES (41434, 282, 6196, 1.00, 19.50000, 6.50000, 'precio', 6.00000, 'Kilos', '1', 3);
INSERT INTO `productos_cotis` VALUES (41435, 285, 6196, 1.00, 27.50000, 5.50000, 'precio', 4.60000, 'Kilos', '1', 5);
INSERT INTO `productos_cotis` VALUES (41436, 310, 6196, 1.00, 12.00000, 4.00000, 'precio', 3.50000, 'Kilos', '1', 3);
INSERT INTO `productos_cotis` VALUES (41437, 142, 6196, 1.00, 19.50000, 6.50000, 'precio', 6.20000, 'Kilos', '', 3);
INSERT INTO `productos_cotis` VALUES (41438, 12, 6196, 1.00, 69.00000, 69.00000, 'precio2', 68.00000, 'Unidad', '', 1);
INSERT INTO `productos_cotis` VALUES (41439, 352, 6196, 1.00, 20.00000, 20.00000, 'precio', 17.00000, 'Unidad', '', 1);
INSERT INTO `productos_cotis` VALUES (41440, 306, 6196, 1.00, 28.50000, 9.50000, 'precio3', 9.00000, 'Kilos', '', 3);

-- ----------------------------
-- Table structure for productos_ventas
-- ----------------------------
DROP TABLE IF EXISTS `productos_ventas`;
CREATE TABLE `productos_ventas`  (
  `id_producto` int NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_venta` int NOT NULL,
  `cantidad` double(6, 2) NULL DEFAULT NULL,
  `precio` double(10, 5) NULL DEFAULT NULL,
  `total` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `igv_prod` tinyint NOT NULL DEFAULT 0,
  `descuento` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `costo` double(10, 5) NULL DEFAULT NULL,
  `precio_usado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `medida` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `presenta` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `presenta_cnt` int NULL DEFAULT NULL,
  INDEX `fk_productos_has_ventas_ventas1_idx`(`id_venta` ASC) USING BTREE,
  INDEX `fk_productos_has_ventas_productos1_idx`(`id_producto` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of productos_ventas
-- ----------------------------
INSERT INTO `productos_ventas` VALUES (412, NULL, 229, 10.00, 150.00000, 0.00, 0, 0.00, 120.00000, '5', 'Unidad', '4', 1);
INSERT INTO `productos_ventas` VALUES (367, NULL, 231, 1.00, 340.00000, 0.00, 0, 0.00, 33.00000, '5', 'Unidad', '1', 10);
INSERT INTO `productos_ventas` VALUES (16, NULL, 233, 1.00, 268.00000, 0.00, 0, 0.00, 69.00000, NULL, 'Unidad', '1', 4);
INSERT INTO `productos_ventas` VALUES (406, NULL, 233, 1.00, 134.40000, 0.00, 0, 0.00, 2.80000, NULL, 'Unidad', '1', 48);
INSERT INTO `productos_ventas` VALUES (367, NULL, 233, 1.00, 66.00000, 0.00, 0, 0.00, 33.00000, NULL, 'Unidad', '1', 2);
INSERT INTO `productos_ventas` VALUES (37, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1);
INSERT INTO `productos_ventas` VALUES (40, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1);
INSERT INTO `productos_ventas` VALUES (36, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1);
INSERT INTO `productos_ventas` VALUES (116, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1);
INSERT INTO `productos_ventas` VALUES (44, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1);
INSERT INTO `productos_ventas` VALUES (39, NULL, 233, 1.00, 16.50000, 0.00, 0, 0.00, 16.50000, NULL, 'Unidad', '1', 1);
INSERT INTO `productos_ventas` VALUES (319, NULL, 233, 1.00, 32.50000, 0.00, 0, 0.00, 6.10000, NULL, 'Kilos', '1', 5);
INSERT INTO `productos_ventas` VALUES (383, NULL, 233, 1.00, 20.00000, 0.00, 0, 0.00, 3.60000, NULL, 'Kilos', '1', 5);
INSERT INTO `productos_ventas` VALUES (176, NULL, 233, 1.00, 37.50000, 0.00, 0, 0.00, 6.00000, NULL, 'Kilos', '1', 5);
INSERT INTO `productos_ventas` VALUES (134, NULL, 233, 1.00, 12.00000, 0.00, 0, 0.00, 10.00000, NULL, 'Sacos', '1', 1);
INSERT INTO `productos_ventas` VALUES (331, NULL, 233, 1.00, 19.50000, 0.00, 0, 0.00, 6.10000, NULL, 'Kilos', '1', 3);
INSERT INTO `productos_ventas` VALUES (172, NULL, 233, 1.00, 11.40000, 0.00, 0, 0.00, 3.40000, NULL, 'Kilos', '1', 3);
INSERT INTO `productos_ventas` VALUES (290, NULL, 233, 1.00, 42.00000, 0.00, 0, 0.00, 3.80000, NULL, 'Kilos', '1', 10);
INSERT INTO `productos_ventas` VALUES (89, NULL, 233, 1.00, 205.00000, 0.00, 0, 0.00, 172.00000, NULL, 'Unidad', '1', 1);
INSERT INTO `productos_ventas` VALUES (284, NULL, 233, 1.00, 140.00000, 0.00, 0, 0.00, 140.00000, NULL, 'Unidad', '1', 1);
INSERT INTO `productos_ventas` VALUES (123, NULL, 233, 1.00, 50.00000, 0.00, 0, 0.00, 5.50000, NULL, 'Kilos', '1', 10);
INSERT INTO `productos_ventas` VALUES (102, NULL, 233, 1.00, 45.00000, 0.00, 0, 0.00, 72.00000, NULL, 'Unidad', '1', 1);
INSERT INTO `productos_ventas` VALUES (120, NULL, 233, 1.00, 82.00000, 0.00, 0, 0.00, 5.80000, NULL, 'Kilos', '1', 10);
INSERT INTO `productos_ventas` VALUES (346, NULL, 233, 1.00, 72.00000, 0.00, 0, 0.00, 28.50000, NULL, 'Unidad', '1', 2);
INSERT INTO `productos_ventas` VALUES (12, NULL, 233, 1.00, 132.00000, 0.00, 0, 0.00, 68.00000, NULL, 'Unidad', '1', 2);
INSERT INTO `productos_ventas` VALUES (165, NULL, 233, 82.00, 10.00000, 0.00, 0, 0.00, 14.00000, NULL, 'Sacos', '1', 1);
INSERT INTO `productos_ventas` VALUES (342, NULL, 234, 1.00, 108.00000, 0.00, 0, 0.00, 10.00000, NULL, 'Kilos', '1', 10);
INSERT INTO `productos_ventas` VALUES (298, NULL, 234, 1.00, 23.10000, 0.00, 0, 0.00, 5.80000, NULL, 'Kilos', '1', 3);
INSERT INTO `productos_ventas` VALUES (310, NULL, 234, 1.00, 24.00000, 0.00, 0, 0.00, 3.50000, NULL, 'Kilos', '1', 5);
INSERT INTO `productos_ventas` VALUES (311, NULL, 234, 1.00, 24.00000, 0.00, 0, 0.00, 3.50000, NULL, 'Kilos', '1', 5);
INSERT INTO `productos_ventas` VALUES (129, NULL, 234, 1.00, 19.00000, 0.00, 0, 0.00, 3.40000, NULL, 'Kilos', '1', 5);
INSERT INTO `productos_ventas` VALUES (412, NULL, 234, 1.00, 135.00000, 0.00, 0, 0.00, 120.00000, NULL, 'Unidad', '', 1);
INSERT INTO `productos_ventas` VALUES (241, NULL, 234, -3.00, 14.00000, 0.00, 0, 0.00, 2.40000, NULL, 'Kilos', '', 5);
INSERT INTO `productos_ventas` VALUES (7, 'ACEITE DEL CAMPO *1LT', 236, 1.00, 74.00000, 74.00, 0, 0.00, NULL, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for proveedores
-- ----------------------------
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores`  (
  `proveedor_id` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `razon_social` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nombre_comercial` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `direccion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `direccion2` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `telefono2` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `id_empresa` int NULL DEFAULT NULL,
  `departamento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `provincia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `distrito` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ubigeo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fecha_create` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` int NULL DEFAULT 1,
  PRIMARY KEY (`proveedor_id`) USING BTREE,
  UNIQUE INDEX `ruc`(`ruc` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 187 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of proveedores
-- ----------------------------
INSERT INTO `proveedores` VALUES (181, '07372103', 'ALBERTO HUARCAYA AYAUJA', 'HUARCAYA PALLAR ICA', '', 'ica', '', '', '', 12, NULL, NULL, NULL, NULL, '2026-02-18 16:12:25', 1);
INSERT INTO `proveedores` VALUES (182, '10711354528', 'PORRAS SALAZAR MAYCOL OSBIN', 'Molino Paolo', 'av francisco pizarro 663 rimac', 'av francisco pizarro 663 rimac', '933121328', '983834950', 'VICTORIA                                                    ', 12, NULL, NULL, NULL, NULL, '2026-04-21 23:30:17', 1);
INSERT INTO `proveedores` VALUES (183, '20613884506', 'COMERCIALIZADORA BARGAR S.A.C.', 'RIO BRANCO', 'JR. JORGE CHAVEZ NRO. 1230 BAR. HUAYCO SAN MARTIN SAN MARTIN TARAPOTO', '', '991675159', '', 'dextreaguilar@hotmail.com', 12, NULL, NULL, NULL, NULL, '2026-04-30 10:36:13', 1);
INSERT INTO `proveedores` VALUES (184, '20612185019', 'EL BUEN SABOR DEL ORIENTE S.A.C.', 'RIKICHA', 'JR. OASIS DE VILLA EL SALVADO NRO. 51 LIMA LIMA VILLA EL SALVADOR', '', '997998870', '978652581', '', 12, NULL, NULL, NULL, NULL, '2026-05-01 10:24:28', 1);
INSERT INTO `proveedores` VALUES (186, '99999999999', 'Proveedor Test 2', 'Test Comercial 2', 'Av. Test 456', '', '111222333', '', 'test2@proveedor.com', 12, NULL, NULL, NULL, NULL, '2026-06-25 23:30:18', 1);

-- ----------------------------
-- Table structure for recepcion_detalle
-- ----------------------------
DROP TABLE IF EXISTS `recepcion_detalle`;
CREATE TABLE `recepcion_detalle`  (
  `id_detalle` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_recepcion` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  PRIMARY KEY (`id_detalle`) USING BTREE,
  INDEX `recepcion_detalle_id_recepcion_index`(`id_recepcion` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of recepcion_detalle
-- ----------------------------

-- ----------------------------
-- Table structure for recepciones
-- ----------------------------
DROP TABLE IF EXISTS `recepciones`;
CREATE TABLE `recepciones`  (
  `id_recepcion` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `id_compra` int NOT NULL,
  `almacen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `observacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_recepcion`) USING BTREE,
  INDEX `recepciones_id_empresa_index`(`id_empresa` ASC) USING BTREE,
  INDEX `recepciones_id_compra_index`(`id_compra` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of recepciones
-- ----------------------------

-- ----------------------------
-- Table structure for registros_caja_vendedor
-- ----------------------------
DROP TABLE IF EXISTS `registros_caja_vendedor`;
CREATE TABLE `registros_caja_vendedor`  (
  `registro_id` int NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `sucursal` int NOT NULL,
  `id_vendedor` int NULL DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `detalle` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cobros_efectivo` decimal(10, 2) NULL DEFAULT 0.00,
  `cobros_banco` decimal(10, 2) NULL DEFAULT 0.00,
  `total_cobrado` decimal(10, 2) NULL DEFAULT 0.00,
  `ingresos_efectivo` decimal(10, 2) NULL DEFAULT 0.00,
  `egresos_efectivo` decimal(10, 2) NULL DEFAULT 0.00,
  `debia_traer` decimal(10, 2) NULL DEFAULT 0.00,
  `saldo_efectivo` decimal(10, 2) NULL DEFAULT 0.00,
  `diferencia` decimal(10, 2) NULL DEFAULT 0.00,
  `estado_cuadre` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL COMMENT 'CUADRA, FALTA, SOBRA',
  `id_caja_empresa` int NULL DEFAULT NULL COMMENT 'Referencia a la caja original',
  `usuario_registro` int NULL DEFAULT NULL,
  `fecha_creacion` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`registro_id`) USING BTREE,
  INDEX `idx_fecha`(`fecha_registro` ASC) USING BTREE,
  INDEX `idx_vendedor`(`id_vendedor` ASC) USING BTREE,
  INDEX `idx_empresa`(`id_empresa` ASC, `sucursal` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of registros_caja_vendedor
-- ----------------------------

-- ----------------------------
-- Table structure for resumen_diario
-- ----------------------------
DROP TABLE IF EXISTS `resumen_diario`;
CREATE TABLE `resumen_diario`  (
  `id_resumen_diario` int NOT NULL,
  `id_empresa` int NOT NULL,
  `fecha` date NULL DEFAULT NULL,
  `ticket` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cantidad_items` int NULL DEFAULT NULL,
  `tipo` int NULL DEFAULT NULL COMMENT '1 para resumen\n2 para comunicacion de baja',
  PRIMARY KEY (`id_resumen_diario`) USING BTREE,
  INDEX `fk_resumen_diario_empresas1_idx`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of resumen_diario
-- ----------------------------

-- ----------------------------
-- Table structure for role_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions`  (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` int NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`) USING BTREE,
  CONSTRAINT `rhp_permission_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of role_has_permissions
-- ----------------------------
INSERT INTO `role_has_permissions` VALUES (1, 1);
INSERT INTO `role_has_permissions` VALUES (1, 3);
INSERT INTO `role_has_permissions` VALUES (1, 4);
INSERT INTO `role_has_permissions` VALUES (1, 5);
INSERT INTO `role_has_permissions` VALUES (2, 1);
INSERT INTO `role_has_permissions` VALUES (2, 3);
INSERT INTO `role_has_permissions` VALUES (3, 1);
INSERT INTO `role_has_permissions` VALUES (4, 1);
INSERT INTO `role_has_permissions` VALUES (4, 5);
INSERT INTO `role_has_permissions` VALUES (4, 6);
INSERT INTO `role_has_permissions` VALUES (5, 1);
INSERT INTO `role_has_permissions` VALUES (5, 6);
INSERT INTO `role_has_permissions` VALUES (6, 1);
INSERT INTO `role_has_permissions` VALUES (6, 3);
INSERT INTO `role_has_permissions` VALUES (6, 4);
INSERT INTO `role_has_permissions` VALUES (7, 1);
INSERT INTO `role_has_permissions` VALUES (7, 3);
INSERT INTO `role_has_permissions` VALUES (8, 1);
INSERT INTO `role_has_permissions` VALUES (9, 1);
INSERT INTO `role_has_permissions` VALUES (9, 3);
INSERT INTO `role_has_permissions` VALUES (9, 6);
INSERT INTO `role_has_permissions` VALUES (10, 1);
INSERT INTO `role_has_permissions` VALUES (10, 6);
INSERT INTO `role_has_permissions` VALUES (11, 1);
INSERT INTO `role_has_permissions` VALUES (11, 3);
INSERT INTO `role_has_permissions` VALUES (11, 4);
INSERT INTO `role_has_permissions` VALUES (11, 5);
INSERT INTO `role_has_permissions` VALUES (12, 1);
INSERT INTO `role_has_permissions` VALUES (12, 4);
INSERT INTO `role_has_permissions` VALUES (12, 5);
INSERT INTO `role_has_permissions` VALUES (13, 1);
INSERT INTO `role_has_permissions` VALUES (14, 1);
INSERT INTO `role_has_permissions` VALUES (15, 1);
INSERT INTO `role_has_permissions` VALUES (15, 3);
INSERT INTO `role_has_permissions` VALUES (15, 4);
INSERT INTO `role_has_permissions` VALUES (15, 5);
INSERT INTO `role_has_permissions` VALUES (16, 1);
INSERT INTO `role_has_permissions` VALUES (16, 4);
INSERT INTO `role_has_permissions` VALUES (17, 1);
INSERT INTO `role_has_permissions` VALUES (17, 3);
INSERT INTO `role_has_permissions` VALUES (18, 1);
INSERT INTO `role_has_permissions` VALUES (18, 3);

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `rol_id` int NOT NULL,
  `nombre` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`rol_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'ADMIN');
INSERT INTO `roles` VALUES (2, 'USUARIO');
INSERT INTO `roles` VALUES (3, 'VENDEDOR');
INSERT INTO `roles` VALUES (4, 'CAJERO');
INSERT INTO `roles` VALUES (5, 'CONTADOR');
INSERT INTO `roles` VALUES (6, 'ALMACEN');

-- ----------------------------
-- Table structure for rutas_vendedor
-- ----------------------------
DROP TABLE IF EXISTS `rutas_vendedor`;
CREATE TABLE `rutas_vendedor`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_ruta` int NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of rutas_vendedor
-- ----------------------------
INSERT INTO `rutas_vendedor` VALUES (1, 1, 62);
INSERT INTO `rutas_vendedor` VALUES (2, 2, 61);
INSERT INTO `rutas_vendedor` VALUES (3, 3, 61);
INSERT INTO `rutas_vendedor` VALUES (4, 4, 60);
INSERT INTO `rutas_vendedor` VALUES (5, 5, 60);
INSERT INTO `rutas_vendedor` VALUES (6, 6, 64);
INSERT INTO `rutas_vendedor` VALUES (7, 7, 63);
INSERT INTO `rutas_vendedor` VALUES (8, 8, 71);

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of sessions
-- ----------------------------

-- ----------------------------
-- Table structure for subcategorias
-- ----------------------------
DROP TABLE IF EXISTS `subcategorias`;
CREATE TABLE `subcategorias`  (
  `id_subcategoria` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_categoria` int NOT NULL,
  `id_empresa` int NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_subcategoria`) USING BTREE,
  INDEX `subcategorias_id_categoria_index`(`id_categoria` ASC) USING BTREE,
  INDEX `subcategorias_id_empresa_index`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of subcategorias
-- ----------------------------

-- ----------------------------
-- Table structure for submarcas
-- ----------------------------
DROP TABLE IF EXISTS `submarcas`;
CREATE TABLE `submarcas`  (
  `id_submarca` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_marca` int NOT NULL,
  `id_empresa` int NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_submarca`) USING BTREE,
  INDEX `submarcas_id_marca_index`(`id_marca` ASC) USING BTREE,
  INDEX `submarcas_id_empresa_index`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of submarcas
-- ----------------------------

-- ----------------------------
-- Table structure for sucursales
-- ----------------------------
DROP TABLE IF EXISTS `sucursales`;
CREATE TABLE `sucursales`  (
  `id_sucursal` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NULL DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `distrito` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `provincia` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `departamento` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ubigeo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cod_sucursal` int NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_sucursal`) USING BTREE,
  INDEX `empresa_id`(`empresa_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of sucursales
-- ----------------------------
INSERT INTO `sucursales` VALUES (1, 12, 'Sucursal 1', '', '', '', '', '', 1, '1');

-- ----------------------------
-- Table structure for tamsporte_persona
-- ----------------------------
DROP TABLE IF EXISTS `tamsporte_persona`;
CREATE TABLE `tamsporte_persona`  (
  `tampo_id` int NOT NULL,
  `ruc` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `razon_social` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`tampo_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tamsporte_persona
-- ----------------------------
INSERT INTO `tamsporte_persona` VALUES (0, '20605571094', 'STORE LINGERIE SOCIEDAD ANONIMA CERRADA', 'JR. CAJAMARCA NRO 435 HUANCAYO CERCADO ');

-- ----------------------------
-- Table structure for tarjetas
-- ----------------------------
DROP TABLE IF EXISTS `tarjetas`;
CREATE TABLE `tarjetas`  (
  `id_tarjeta` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int UNSIGNED NOT NULL,
  `id_banco` int UNSIGNED NOT NULL,
  `id_cuenta_bancaria` int UNSIGNED NULL DEFAULT NULL,
  `tipo` enum('CREDITO','DEBITO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DEBITO',
  `marca` enum('VISA','MASTERCARD','AMEX','DINERS') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VISA',
  `ultimos_4` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titular` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_vencimiento` date NULL DEFAULT NULL,
  `estado` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tarjeta`) USING BTREE,
  INDEX `tarjetas_id_banco_foreign`(`id_banco` ASC) USING BTREE,
  INDEX `tarjetas_id_cuenta_bancaria_foreign`(`id_cuenta_bancaria` ASC) USING BTREE,
  INDEX `tarjetas_id_empresa_index`(`id_empresa` ASC) USING BTREE,
  CONSTRAINT `tarjetas_id_banco_foreign` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `tarjetas_id_cuenta_bancaria_foreign` FOREIGN KEY (`id_cuenta_bancaria`) REFERENCES `cuentas_bancarias` (`id_cuenta`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tarjetas
-- ----------------------------

-- ----------------------------
-- Table structure for tipo_pago
-- ----------------------------
DROP TABLE IF EXISTS `tipo_pago`;
CREATE TABLE `tipo_pago`  (
  `tipo_pago_id` int NOT NULL,
  `nombre` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`tipo_pago_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tipo_pago
-- ----------------------------
INSERT INTO `tipo_pago` VALUES (1, 'Contado');
INSERT INTO `tipo_pago` VALUES (2, 'Credito');

-- ----------------------------
-- Table structure for traslado_detalle
-- ----------------------------
DROP TABLE IF EXISTS `traslado_detalle`;
CREATE TABLE `traslado_detalle`  (
  `id_detalle` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_traslado` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `stock_ant_origen` int NOT NULL DEFAULT 0,
  `stock_nuevo_origen` int NOT NULL DEFAULT 0,
  `stock_ant_destino` int NOT NULL DEFAULT 0,
  `stock_nuevo_destino` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_detalle`) USING BTREE,
  INDEX `traslado_detalle_id_traslado_index`(`id_traslado` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of traslado_detalle
-- ----------------------------

-- ----------------------------
-- Table structure for traslados
-- ----------------------------
DROP TABLE IF EXISTS `traslados`;
CREATE TABLE `traslados`  (
  `id_traslado` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `almacen_origen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `almacen_destino` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `observacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_traslado`) USING BTREE,
  INDEX `traslados_id_empresa_index`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of traslados
-- ----------------------------

-- ----------------------------
-- Table structure for ubigeo_inei
-- ----------------------------
DROP TABLE IF EXISTS `ubigeo_inei`;
CREATE TABLE `ubigeo_inei`  (
  `id_ubigeo` int NOT NULL,
  `departamento` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `provincia` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `distrito` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nombre` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id_ubigeo`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ubigeo_inei
-- ----------------------------
INSERT INTO `ubigeo_inei` VALUES (1, '01', '00', '00', 'AMAZONAS');
INSERT INTO `ubigeo_inei` VALUES (2, '01', '01', '00', 'CHACHAPOYAS');
INSERT INTO `ubigeo_inei` VALUES (3, '01', '01', '01', 'CHACHAPOYAS');
INSERT INTO `ubigeo_inei` VALUES (4, '01', '01', '02', 'ASUNCION');
INSERT INTO `ubigeo_inei` VALUES (5, '01', '01', '03', 'BALSAS');
INSERT INTO `ubigeo_inei` VALUES (6, '01', '01', '04', 'CHETO');
INSERT INTO `ubigeo_inei` VALUES (7, '01', '01', '05', 'CHILIQUIN');
INSERT INTO `ubigeo_inei` VALUES (8, '01', '01', '06', 'CHUQUIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (9, '01', '01', '07', 'GRANADA');
INSERT INTO `ubigeo_inei` VALUES (10, '01', '01', '08', 'HUANCAS');
INSERT INTO `ubigeo_inei` VALUES (11, '01', '01', '09', 'LA JALCA');
INSERT INTO `ubigeo_inei` VALUES (12, '01', '01', '10', 'LEIMEBAMBA');
INSERT INTO `ubigeo_inei` VALUES (13, '01', '01', '11', 'LEVANTO');
INSERT INTO `ubigeo_inei` VALUES (14, '01', '01', '12', 'MAGDALENA');
INSERT INTO `ubigeo_inei` VALUES (15, '01', '01', '13', 'MARISCAL CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (16, '01', '01', '14', 'MOLINOPAMPA');
INSERT INTO `ubigeo_inei` VALUES (17, '01', '01', '15', 'MONTEVIDEO');
INSERT INTO `ubigeo_inei` VALUES (18, '01', '01', '16', 'OLLEROS');
INSERT INTO `ubigeo_inei` VALUES (19, '01', '01', '17', 'QUINJALCA');
INSERT INTO `ubigeo_inei` VALUES (20, '01', '01', '18', 'SAN FRANCISCO DE DAGUAS');
INSERT INTO `ubigeo_inei` VALUES (21, '01', '01', '19', 'SAN ISIDRO DE MAINO');
INSERT INTO `ubigeo_inei` VALUES (22, '01', '01', '20', 'SOLOCO');
INSERT INTO `ubigeo_inei` VALUES (23, '01', '01', '21', 'SONCHE');
INSERT INTO `ubigeo_inei` VALUES (24, '01', '02', '00', 'BAGUA');
INSERT INTO `ubigeo_inei` VALUES (25, '01', '02', '01', 'BAGUA');
INSERT INTO `ubigeo_inei` VALUES (26, '01', '02', '02', 'ARAMANGO');
INSERT INTO `ubigeo_inei` VALUES (27, '01', '02', '03', 'COPALLIN');
INSERT INTO `ubigeo_inei` VALUES (28, '01', '02', '04', 'EL PARCO');
INSERT INTO `ubigeo_inei` VALUES (29, '01', '02', '05', 'IMAZA');
INSERT INTO `ubigeo_inei` VALUES (30, '01', '02', '06', 'LA PECA');
INSERT INTO `ubigeo_inei` VALUES (31, '01', '03', '00', 'BONGARA');
INSERT INTO `ubigeo_inei` VALUES (32, '01', '03', '01', 'JUMBILLA');
INSERT INTO `ubigeo_inei` VALUES (33, '01', '03', '02', 'CHISQUILLA');
INSERT INTO `ubigeo_inei` VALUES (34, '01', '03', '03', 'CHURUJA');
INSERT INTO `ubigeo_inei` VALUES (35, '01', '03', '04', 'COROSHA');
INSERT INTO `ubigeo_inei` VALUES (36, '01', '03', '05', 'CUISPES');
INSERT INTO `ubigeo_inei` VALUES (37, '01', '03', '06', 'FLORIDA');
INSERT INTO `ubigeo_inei` VALUES (38, '01', '03', '07', 'JAZÁN');
INSERT INTO `ubigeo_inei` VALUES (39, '01', '03', '08', 'RECTA');
INSERT INTO `ubigeo_inei` VALUES (40, '01', '03', '09', 'SAN CARLOS');
INSERT INTO `ubigeo_inei` VALUES (41, '01', '03', '10', 'SHIPASBAMBA');
INSERT INTO `ubigeo_inei` VALUES (42, '01', '03', '11', 'VALERA');
INSERT INTO `ubigeo_inei` VALUES (43, '01', '03', '12', 'YAMBRASBAMBA');
INSERT INTO `ubigeo_inei` VALUES (44, '01', '04', '00', 'CONDORCANQUI');
INSERT INTO `ubigeo_inei` VALUES (45, '01', '04', '01', 'NIEVA');
INSERT INTO `ubigeo_inei` VALUES (46, '01', '04', '02', 'EL CENEPA');
INSERT INTO `ubigeo_inei` VALUES (47, '01', '04', '03', 'RIO SANTIAGO');
INSERT INTO `ubigeo_inei` VALUES (48, '01', '05', '00', 'LUYA');
INSERT INTO `ubigeo_inei` VALUES (49, '01', '05', '01', 'LAMUD');
INSERT INTO `ubigeo_inei` VALUES (50, '01', '05', '02', 'CAMPORREDONDO');
INSERT INTO `ubigeo_inei` VALUES (51, '01', '05', '03', 'COCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (52, '01', '05', '04', 'COLCAMAR');
INSERT INTO `ubigeo_inei` VALUES (53, '01', '05', '05', 'CONILA');
INSERT INTO `ubigeo_inei` VALUES (54, '01', '05', '06', 'INGUILPATA');
INSERT INTO `ubigeo_inei` VALUES (55, '01', '05', '07', 'LONGUITA');
INSERT INTO `ubigeo_inei` VALUES (56, '01', '05', '08', 'LONYA CHICO');
INSERT INTO `ubigeo_inei` VALUES (57, '01', '05', '09', 'LUYA');
INSERT INTO `ubigeo_inei` VALUES (58, '01', '05', '10', 'LUYA VIEJO');
INSERT INTO `ubigeo_inei` VALUES (59, '01', '05', '11', 'MARIA');
INSERT INTO `ubigeo_inei` VALUES (60, '01', '05', '12', 'OCALLI');
INSERT INTO `ubigeo_inei` VALUES (61, '01', '05', '13', 'OCUMAL');
INSERT INTO `ubigeo_inei` VALUES (62, '01', '05', '14', 'PISUQUIA');
INSERT INTO `ubigeo_inei` VALUES (63, '01', '05', '15', 'PROVIDENCIA');
INSERT INTO `ubigeo_inei` VALUES (64, '01', '05', '16', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (65, '01', '05', '17', 'SAN FRANCISCO DEL YESO');
INSERT INTO `ubigeo_inei` VALUES (66, '01', '05', '18', 'SAN JERONIMO');
INSERT INTO `ubigeo_inei` VALUES (67, '01', '05', '19', 'SAN JUAN DE LOPECANCHA');
INSERT INTO `ubigeo_inei` VALUES (68, '01', '05', '20', 'SANTA CATALINA');
INSERT INTO `ubigeo_inei` VALUES (69, '01', '05', '21', 'SANTO TOMAS');
INSERT INTO `ubigeo_inei` VALUES (70, '01', '05', '22', 'TINGO');
INSERT INTO `ubigeo_inei` VALUES (71, '01', '05', '23', 'TRITA');
INSERT INTO `ubigeo_inei` VALUES (72, '01', '06', '00', 'RODRIGUEZ DE MENDOZA');
INSERT INTO `ubigeo_inei` VALUES (73, '01', '06', '01', 'SAN NICOLAS');
INSERT INTO `ubigeo_inei` VALUES (74, '01', '06', '02', 'CHIRIMOTO');
INSERT INTO `ubigeo_inei` VALUES (75, '01', '06', '03', 'COCHAMAL');
INSERT INTO `ubigeo_inei` VALUES (76, '01', '06', '04', 'HUAMBO');
INSERT INTO `ubigeo_inei` VALUES (77, '01', '06', '05', 'LIMABAMBA');
INSERT INTO `ubigeo_inei` VALUES (78, '01', '06', '06', 'LONGAR');
INSERT INTO `ubigeo_inei` VALUES (79, '01', '06', '07', 'MARISCAL BENAVIDES');
INSERT INTO `ubigeo_inei` VALUES (80, '01', '06', '08', 'MILPUC');
INSERT INTO `ubigeo_inei` VALUES (81, '01', '06', '09', 'OMIA');
INSERT INTO `ubigeo_inei` VALUES (82, '01', '06', '10', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (83, '01', '06', '11', 'TOTORA');
INSERT INTO `ubigeo_inei` VALUES (84, '01', '06', '12', 'VISTA ALEGRE');
INSERT INTO `ubigeo_inei` VALUES (85, '01', '07', '00', 'UTCUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (86, '01', '07', '01', 'BAGUA GRANDE');
INSERT INTO `ubigeo_inei` VALUES (87, '01', '07', '02', 'CAJARURO');
INSERT INTO `ubigeo_inei` VALUES (88, '01', '07', '03', 'CUMBA');
INSERT INTO `ubigeo_inei` VALUES (89, '01', '07', '04', 'EL MILAGRO');
INSERT INTO `ubigeo_inei` VALUES (90, '01', '07', '05', 'JAMALCA');
INSERT INTO `ubigeo_inei` VALUES (91, '01', '07', '06', 'LONYA GRANDE');
INSERT INTO `ubigeo_inei` VALUES (92, '01', '07', '07', 'YAMON');
INSERT INTO `ubigeo_inei` VALUES (93, '02', '00', '00', 'ANCASH');
INSERT INTO `ubigeo_inei` VALUES (94, '02', '01', '00', 'HUARAZ');
INSERT INTO `ubigeo_inei` VALUES (95, '02', '01', '01', 'HUARAZ');
INSERT INTO `ubigeo_inei` VALUES (96, '02', '01', '02', 'COCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (97, '02', '01', '03', 'COLCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (98, '02', '01', '04', 'HUANCHAY');
INSERT INTO `ubigeo_inei` VALUES (99, '02', '01', '05', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (100, '02', '01', '06', 'JANGAS');
INSERT INTO `ubigeo_inei` VALUES (101, '02', '01', '07', 'LA LIBERTAD');
INSERT INTO `ubigeo_inei` VALUES (102, '02', '01', '08', 'OLLEROS');
INSERT INTO `ubigeo_inei` VALUES (103, '02', '01', '09', 'PAMPAS');
INSERT INTO `ubigeo_inei` VALUES (104, '02', '01', '10', 'PARIACOTO');
INSERT INTO `ubigeo_inei` VALUES (105, '02', '01', '11', 'PIRA');
INSERT INTO `ubigeo_inei` VALUES (106, '02', '01', '12', 'TARICA');
INSERT INTO `ubigeo_inei` VALUES (107, '02', '02', '00', 'AIJA');
INSERT INTO `ubigeo_inei` VALUES (108, '02', '02', '01', 'AIJA');
INSERT INTO `ubigeo_inei` VALUES (109, '02', '02', '02', 'CORIS');
INSERT INTO `ubigeo_inei` VALUES (110, '02', '02', '03', 'HUACLLAN');
INSERT INTO `ubigeo_inei` VALUES (111, '02', '02', '04', 'LA MERCED');
INSERT INTO `ubigeo_inei` VALUES (112, '02', '02', '05', 'SUCCHA');
INSERT INTO `ubigeo_inei` VALUES (113, '02', '03', '00', 'ANTONIO RAYMONDI');
INSERT INTO `ubigeo_inei` VALUES (114, '02', '03', '01', 'LLAMELLIN');
INSERT INTO `ubigeo_inei` VALUES (115, '02', '03', '02', 'ACZO');
INSERT INTO `ubigeo_inei` VALUES (116, '02', '03', '03', 'CHACCHO');
INSERT INTO `ubigeo_inei` VALUES (117, '02', '03', '04', 'CHINGAS');
INSERT INTO `ubigeo_inei` VALUES (118, '02', '03', '05', 'MIRGAS');
INSERT INTO `ubigeo_inei` VALUES (119, '02', '03', '06', 'SAN JUAN DE RONTOY');
INSERT INTO `ubigeo_inei` VALUES (120, '02', '04', '00', 'ASUNCION');
INSERT INTO `ubigeo_inei` VALUES (121, '02', '04', '01', 'CHACAS');
INSERT INTO `ubigeo_inei` VALUES (122, '02', '04', '02', 'ACOCHACA');
INSERT INTO `ubigeo_inei` VALUES (123, '02', '05', '00', 'BOLOGNESI');
INSERT INTO `ubigeo_inei` VALUES (124, '02', '05', '01', 'CHIQUIAN');
INSERT INTO `ubigeo_inei` VALUES (125, '02', '05', '02', 'ABELARDO PARDO LEZAMETA');
INSERT INTO `ubigeo_inei` VALUES (126, '02', '05', '03', 'ANTONIO RAYMONDI');
INSERT INTO `ubigeo_inei` VALUES (127, '02', '05', '04', 'AQUIA');
INSERT INTO `ubigeo_inei` VALUES (128, '02', '05', '05', 'CAJACAY');
INSERT INTO `ubigeo_inei` VALUES (129, '02', '05', '06', 'CANIS');
INSERT INTO `ubigeo_inei` VALUES (130, '02', '05', '07', 'COLQUIOC');
INSERT INTO `ubigeo_inei` VALUES (131, '02', '05', '08', 'HUALLANCA');
INSERT INTO `ubigeo_inei` VALUES (132, '02', '05', '09', 'HUASTA');
INSERT INTO `ubigeo_inei` VALUES (133, '02', '05', '10', 'HUAYLLACAYAN');
INSERT INTO `ubigeo_inei` VALUES (134, '02', '05', '11', 'LA PRIMAVERA');
INSERT INTO `ubigeo_inei` VALUES (135, '02', '05', '12', 'MANGAS');
INSERT INTO `ubigeo_inei` VALUES (136, '02', '05', '13', 'PACLLON');
INSERT INTO `ubigeo_inei` VALUES (137, '02', '05', '14', 'SAN MIGUEL DE CORPANQUI');
INSERT INTO `ubigeo_inei` VALUES (138, '02', '05', '15', 'TICLLOS');
INSERT INTO `ubigeo_inei` VALUES (139, '02', '06', '00', 'CARHUAZ');
INSERT INTO `ubigeo_inei` VALUES (140, '02', '06', '01', 'CARHUAZ');
INSERT INTO `ubigeo_inei` VALUES (141, '02', '06', '02', 'ACOPAMPA');
INSERT INTO `ubigeo_inei` VALUES (142, '02', '06', '03', 'AMASHCA');
INSERT INTO `ubigeo_inei` VALUES (143, '02', '06', '04', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (144, '02', '06', '05', 'ATAQUERO');
INSERT INTO `ubigeo_inei` VALUES (145, '02', '06', '06', 'MARCARA');
INSERT INTO `ubigeo_inei` VALUES (146, '02', '06', '07', 'PARIAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (147, '02', '06', '08', 'SAN MIGUEL DE ACO');
INSERT INTO `ubigeo_inei` VALUES (148, '02', '06', '09', 'SHILLA');
INSERT INTO `ubigeo_inei` VALUES (149, '02', '06', '10', 'TINCO');
INSERT INTO `ubigeo_inei` VALUES (150, '02', '06', '11', 'YUNGAR');
INSERT INTO `ubigeo_inei` VALUES (151, '02', '07', '00', 'CARLOS FERMIN FITZCARRALD');
INSERT INTO `ubigeo_inei` VALUES (152, '02', '07', '01', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (153, '02', '07', '02', 'SAN NICOLAS');
INSERT INTO `ubigeo_inei` VALUES (154, '02', '07', '03', 'YAUYA');
INSERT INTO `ubigeo_inei` VALUES (155, '02', '08', '00', 'CASMA');
INSERT INTO `ubigeo_inei` VALUES (156, '02', '08', '01', 'CASMA');
INSERT INTO `ubigeo_inei` VALUES (157, '02', '08', '02', 'BUENA VISTA ALTA');
INSERT INTO `ubigeo_inei` VALUES (158, '02', '08', '03', 'COMANDANTE NOEL');
INSERT INTO `ubigeo_inei` VALUES (159, '02', '08', '04', 'YAUTAN');
INSERT INTO `ubigeo_inei` VALUES (160, '02', '09', '00', 'CORONGO');
INSERT INTO `ubigeo_inei` VALUES (161, '02', '09', '01', 'CORONGO');
INSERT INTO `ubigeo_inei` VALUES (162, '02', '09', '02', 'ACO');
INSERT INTO `ubigeo_inei` VALUES (163, '02', '09', '03', 'BAMBAS');
INSERT INTO `ubigeo_inei` VALUES (164, '02', '09', '04', 'CUSCA');
INSERT INTO `ubigeo_inei` VALUES (165, '02', '09', '05', 'LA PAMPA');
INSERT INTO `ubigeo_inei` VALUES (166, '02', '09', '06', 'YANAC');
INSERT INTO `ubigeo_inei` VALUES (167, '02', '09', '07', 'YUPAN');
INSERT INTO `ubigeo_inei` VALUES (168, '02', '10', '00', 'HUARI');
INSERT INTO `ubigeo_inei` VALUES (169, '02', '10', '01', 'HUARI');
INSERT INTO `ubigeo_inei` VALUES (170, '02', '10', '02', 'ANRA');
INSERT INTO `ubigeo_inei` VALUES (171, '02', '10', '03', 'CAJAY');
INSERT INTO `ubigeo_inei` VALUES (172, '02', '10', '04', 'CHAVIN DE HUANTAR');
INSERT INTO `ubigeo_inei` VALUES (173, '02', '10', '05', 'HUACACHI');
INSERT INTO `ubigeo_inei` VALUES (174, '02', '10', '06', 'HUACCHIS');
INSERT INTO `ubigeo_inei` VALUES (175, '02', '10', '07', 'HUACHIS');
INSERT INTO `ubigeo_inei` VALUES (176, '02', '10', '08', 'HUANTAR');
INSERT INTO `ubigeo_inei` VALUES (177, '02', '10', '09', 'MASIN');
INSERT INTO `ubigeo_inei` VALUES (178, '02', '10', '10', 'PAUCAS');
INSERT INTO `ubigeo_inei` VALUES (179, '02', '10', '11', 'PONTO');
INSERT INTO `ubigeo_inei` VALUES (180, '02', '10', '12', 'RAHUAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (181, '02', '10', '13', 'RAPAYAN');
INSERT INTO `ubigeo_inei` VALUES (182, '02', '10', '14', 'SAN MARCOS');
INSERT INTO `ubigeo_inei` VALUES (183, '02', '10', '15', 'SAN PEDRO DE CHANA');
INSERT INTO `ubigeo_inei` VALUES (184, '02', '10', '16', 'UCO');
INSERT INTO `ubigeo_inei` VALUES (185, '02', '11', '00', 'HUARMEY');
INSERT INTO `ubigeo_inei` VALUES (186, '02', '11', '01', 'HUARMEY');
INSERT INTO `ubigeo_inei` VALUES (187, '02', '11', '02', 'COCHAPETI');
INSERT INTO `ubigeo_inei` VALUES (188, '02', '11', '03', 'CULEBRAS');
INSERT INTO `ubigeo_inei` VALUES (189, '02', '11', '04', 'HUAYAN');
INSERT INTO `ubigeo_inei` VALUES (190, '02', '11', '05', 'MALVAS');
INSERT INTO `ubigeo_inei` VALUES (191, '02', '12', '00', 'HUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (192, '02', '12', '01', 'CARAZ');
INSERT INTO `ubigeo_inei` VALUES (193, '02', '12', '02', 'HUALLANCA');
INSERT INTO `ubigeo_inei` VALUES (194, '02', '12', '03', 'HUATA');
INSERT INTO `ubigeo_inei` VALUES (195, '02', '12', '04', 'HUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (196, '02', '12', '05', 'MATO');
INSERT INTO `ubigeo_inei` VALUES (197, '02', '12', '06', 'PAMPAROMAS');
INSERT INTO `ubigeo_inei` VALUES (198, '02', '12', '07', 'PUEBLO LIBRE');
INSERT INTO `ubigeo_inei` VALUES (199, '02', '12', '08', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (200, '02', '12', '09', 'SANTO TORIBIO');
INSERT INTO `ubigeo_inei` VALUES (201, '02', '12', '10', 'YURACMARCA');
INSERT INTO `ubigeo_inei` VALUES (202, '02', '13', '00', 'MARISCAL LUZURIAGA');
INSERT INTO `ubigeo_inei` VALUES (203, '02', '13', '01', 'PISCOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (204, '02', '13', '02', 'CASCA');
INSERT INTO `ubigeo_inei` VALUES (205, '02', '13', '03', 'ELEAZAR GUZMAN BARRON');
INSERT INTO `ubigeo_inei` VALUES (206, '02', '13', '04', 'FIDEL OLIVAS ESCUDERO');
INSERT INTO `ubigeo_inei` VALUES (207, '02', '13', '05', 'LLAMA');
INSERT INTO `ubigeo_inei` VALUES (208, '02', '13', '06', 'LLUMPA');
INSERT INTO `ubigeo_inei` VALUES (209, '02', '13', '07', 'LUCMA');
INSERT INTO `ubigeo_inei` VALUES (210, '02', '13', '08', 'MUSGA');
INSERT INTO `ubigeo_inei` VALUES (211, '02', '14', '00', 'OCROS');
INSERT INTO `ubigeo_inei` VALUES (212, '02', '14', '01', 'OCROS');
INSERT INTO `ubigeo_inei` VALUES (213, '02', '14', '02', 'ACAS');
INSERT INTO `ubigeo_inei` VALUES (214, '02', '14', '03', 'CAJAMARQUILLA');
INSERT INTO `ubigeo_inei` VALUES (215, '02', '14', '04', 'CARHUAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (216, '02', '14', '05', 'COCHAS');
INSERT INTO `ubigeo_inei` VALUES (217, '02', '14', '06', 'CONGAS');
INSERT INTO `ubigeo_inei` VALUES (218, '02', '14', '07', 'LLIPA');
INSERT INTO `ubigeo_inei` VALUES (219, '02', '14', '08', 'SAN CRISTOBAL DE RAJAN');
INSERT INTO `ubigeo_inei` VALUES (220, '02', '14', '09', 'SAN PEDRO');
INSERT INTO `ubigeo_inei` VALUES (221, '02', '14', '10', 'SANTIAGO DE CHILCAS');
INSERT INTO `ubigeo_inei` VALUES (222, '02', '15', '00', 'PALLASCA');
INSERT INTO `ubigeo_inei` VALUES (223, '02', '15', '01', 'CABANA');
INSERT INTO `ubigeo_inei` VALUES (224, '02', '15', '02', 'BOLOGNESI');
INSERT INTO `ubigeo_inei` VALUES (225, '02', '15', '03', 'CONCHUCOS');
INSERT INTO `ubigeo_inei` VALUES (226, '02', '15', '04', 'HUACASCHUQUE');
INSERT INTO `ubigeo_inei` VALUES (227, '02', '15', '05', 'HUANDOVAL');
INSERT INTO `ubigeo_inei` VALUES (228, '02', '15', '06', 'LACABAMBA');
INSERT INTO `ubigeo_inei` VALUES (229, '02', '15', '07', 'LLAPO');
INSERT INTO `ubigeo_inei` VALUES (230, '02', '15', '08', 'PALLASCA');
INSERT INTO `ubigeo_inei` VALUES (231, '02', '15', '09', 'PAMPAS');
INSERT INTO `ubigeo_inei` VALUES (232, '02', '15', '10', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (233, '02', '15', '11', 'TAUCA');
INSERT INTO `ubigeo_inei` VALUES (234, '02', '16', '00', 'POMABAMBA');
INSERT INTO `ubigeo_inei` VALUES (235, '02', '16', '01', 'POMABAMBA');
INSERT INTO `ubigeo_inei` VALUES (236, '02', '16', '02', 'HUAYLLAN');
INSERT INTO `ubigeo_inei` VALUES (237, '02', '16', '03', 'PAROBAMBA');
INSERT INTO `ubigeo_inei` VALUES (238, '02', '16', '04', 'QUINUABAMBA');
INSERT INTO `ubigeo_inei` VALUES (239, '02', '17', '00', 'RECUAY');
INSERT INTO `ubigeo_inei` VALUES (240, '02', '17', '01', 'RECUAY');
INSERT INTO `ubigeo_inei` VALUES (241, '02', '17', '02', 'CATAC');
INSERT INTO `ubigeo_inei` VALUES (242, '02', '17', '03', 'COTAPARACO');
INSERT INTO `ubigeo_inei` VALUES (243, '02', '17', '04', 'HUAYLLAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (244, '02', '17', '05', 'LLACLLIN');
INSERT INTO `ubigeo_inei` VALUES (245, '02', '17', '06', 'MARCA');
INSERT INTO `ubigeo_inei` VALUES (246, '02', '17', '07', 'PAMPAS CHICO');
INSERT INTO `ubigeo_inei` VALUES (247, '02', '17', '08', 'PARARIN');
INSERT INTO `ubigeo_inei` VALUES (248, '02', '17', '09', 'TAPACOCHA');
INSERT INTO `ubigeo_inei` VALUES (249, '02', '17', '10', 'TICAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (250, '02', '18', '00', 'SANTA');
INSERT INTO `ubigeo_inei` VALUES (251, '02', '18', '01', 'CHIMBOTE');
INSERT INTO `ubigeo_inei` VALUES (252, '02', '18', '02', 'CACERES DEL PERU');
INSERT INTO `ubigeo_inei` VALUES (253, '02', '18', '03', 'COISHCO');
INSERT INTO `ubigeo_inei` VALUES (254, '02', '18', '04', 'MACATE');
INSERT INTO `ubigeo_inei` VALUES (255, '02', '18', '05', 'MORO');
INSERT INTO `ubigeo_inei` VALUES (256, '02', '18', '06', 'NEPEÑA');
INSERT INTO `ubigeo_inei` VALUES (257, '02', '18', '07', 'SAMANCO');
INSERT INTO `ubigeo_inei` VALUES (258, '02', '18', '08', 'SANTA');
INSERT INTO `ubigeo_inei` VALUES (259, '02', '18', '09', 'NUEVO CHIMBOTE');
INSERT INTO `ubigeo_inei` VALUES (260, '02', '19', '00', 'SIHUAS');
INSERT INTO `ubigeo_inei` VALUES (261, '02', '19', '01', 'SIHUAS');
INSERT INTO `ubigeo_inei` VALUES (262, '02', '19', '02', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (263, '02', '19', '03', 'ALFONSO UGARTE');
INSERT INTO `ubigeo_inei` VALUES (264, '02', '19', '04', 'CASHAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (265, '02', '19', '05', 'CHINGALPO');
INSERT INTO `ubigeo_inei` VALUES (266, '02', '19', '06', 'HUAYLLABAMBA');
INSERT INTO `ubigeo_inei` VALUES (267, '02', '19', '07', 'QUICHES');
INSERT INTO `ubigeo_inei` VALUES (268, '02', '19', '08', 'RAGASH');
INSERT INTO `ubigeo_inei` VALUES (269, '02', '19', '09', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (270, '02', '19', '10', 'SICSIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (271, '02', '20', '00', 'YUNGAY');
INSERT INTO `ubigeo_inei` VALUES (272, '02', '20', '01', 'YUNGAY');
INSERT INTO `ubigeo_inei` VALUES (273, '02', '20', '02', 'CASCAPARA');
INSERT INTO `ubigeo_inei` VALUES (274, '02', '20', '03', 'MANCOS');
INSERT INTO `ubigeo_inei` VALUES (275, '02', '20', '04', 'MATACOTO');
INSERT INTO `ubigeo_inei` VALUES (276, '02', '20', '05', 'QUILLO');
INSERT INTO `ubigeo_inei` VALUES (277, '02', '20', '06', 'RANRAHIRCA');
INSERT INTO `ubigeo_inei` VALUES (278, '02', '20', '07', 'SHUPLUY');
INSERT INTO `ubigeo_inei` VALUES (279, '02', '20', '08', 'YANAMA');
INSERT INTO `ubigeo_inei` VALUES (280, '03', '00', '00', 'APURIMAC');
INSERT INTO `ubigeo_inei` VALUES (281, '03', '01', '00', 'ABANCAY');
INSERT INTO `ubigeo_inei` VALUES (282, '03', '01', '01', 'ABANCAY');
INSERT INTO `ubigeo_inei` VALUES (283, '03', '01', '02', 'CHACOCHE');
INSERT INTO `ubigeo_inei` VALUES (284, '03', '01', '03', 'CIRCA');
INSERT INTO `ubigeo_inei` VALUES (285, '03', '01', '04', 'CURAHUASI');
INSERT INTO `ubigeo_inei` VALUES (286, '03', '01', '05', 'HUANIPACA');
INSERT INTO `ubigeo_inei` VALUES (287, '03', '01', '06', 'LAMBRAMA');
INSERT INTO `ubigeo_inei` VALUES (288, '03', '01', '07', 'PICHIRHUA');
INSERT INTO `ubigeo_inei` VALUES (289, '03', '01', '08', 'SAN PEDRO DE CACHORA');
INSERT INTO `ubigeo_inei` VALUES (290, '03', '01', '09', 'TAMBURCO');
INSERT INTO `ubigeo_inei` VALUES (291, '03', '02', '00', 'ANDAHUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (292, '03', '02', '01', 'ANDAHUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (293, '03', '02', '02', 'ANDARAPA');
INSERT INTO `ubigeo_inei` VALUES (294, '03', '02', '03', 'CHIARA');
INSERT INTO `ubigeo_inei` VALUES (295, '03', '02', '04', 'HUANCARAMA');
INSERT INTO `ubigeo_inei` VALUES (296, '03', '02', '05', 'HUANCARAY');
INSERT INTO `ubigeo_inei` VALUES (297, '03', '02', '06', 'HUAYANA');
INSERT INTO `ubigeo_inei` VALUES (298, '03', '02', '07', 'KISHUARA');
INSERT INTO `ubigeo_inei` VALUES (299, '03', '02', '08', 'PACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (300, '03', '02', '09', 'PACUCHA');
INSERT INTO `ubigeo_inei` VALUES (301, '03', '02', '10', 'PAMPACHIRI');
INSERT INTO `ubigeo_inei` VALUES (302, '03', '02', '11', 'POMACOCHA');
INSERT INTO `ubigeo_inei` VALUES (303, '03', '02', '12', 'SAN ANTONIO DE CACHI');
INSERT INTO `ubigeo_inei` VALUES (304, '03', '02', '13', 'SAN JERONIMO');
INSERT INTO `ubigeo_inei` VALUES (305, '03', '02', '14', 'SAN MIGUEL DE CHACCRAMPA');
INSERT INTO `ubigeo_inei` VALUES (306, '03', '02', '15', 'SANTA MARIA DE CHICMO');
INSERT INTO `ubigeo_inei` VALUES (307, '03', '02', '16', 'TALAVERA');
INSERT INTO `ubigeo_inei` VALUES (308, '03', '02', '17', 'TUMAY HUARACA');
INSERT INTO `ubigeo_inei` VALUES (309, '03', '02', '18', 'TURPO');
INSERT INTO `ubigeo_inei` VALUES (310, '03', '02', '19', 'KAQUIABAMBA');
INSERT INTO `ubigeo_inei` VALUES (311, '03', '03', '00', 'ANTABAMBA');
INSERT INTO `ubigeo_inei` VALUES (312, '03', '03', '01', 'ANTABAMBA');
INSERT INTO `ubigeo_inei` VALUES (313, '03', '03', '02', 'EL ORO');
INSERT INTO `ubigeo_inei` VALUES (314, '03', '03', '03', 'HUAQUIRCA');
INSERT INTO `ubigeo_inei` VALUES (315, '03', '03', '04', 'JUAN ESPINOZA MEDRANO');
INSERT INTO `ubigeo_inei` VALUES (316, '03', '03', '05', 'OROPESA');
INSERT INTO `ubigeo_inei` VALUES (317, '03', '03', '06', 'PACHACONAS');
INSERT INTO `ubigeo_inei` VALUES (318, '03', '03', '07', 'SABAINO');
INSERT INTO `ubigeo_inei` VALUES (319, '03', '04', '00', 'AYMARAES');
INSERT INTO `ubigeo_inei` VALUES (320, '03', '04', '01', 'CHALHUANCA');
INSERT INTO `ubigeo_inei` VALUES (321, '03', '04', '02', 'CAPAYA');
INSERT INTO `ubigeo_inei` VALUES (322, '03', '04', '03', 'CARAYBAMBA');
INSERT INTO `ubigeo_inei` VALUES (323, '03', '04', '04', 'CHAPIMARCA');
INSERT INTO `ubigeo_inei` VALUES (324, '03', '04', '05', 'COLCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (325, '03', '04', '06', 'COTARUSE');
INSERT INTO `ubigeo_inei` VALUES (326, '03', '04', '07', 'HUAYLLO');
INSERT INTO `ubigeo_inei` VALUES (327, '03', '04', '08', 'JUSTO APU SAHUARAURA');
INSERT INTO `ubigeo_inei` VALUES (328, '03', '04', '09', 'LUCRE');
INSERT INTO `ubigeo_inei` VALUES (329, '03', '04', '10', 'POCOHUANCA');
INSERT INTO `ubigeo_inei` VALUES (330, '03', '04', '11', 'SAN JUAN DE CHACÑA');
INSERT INTO `ubigeo_inei` VALUES (331, '03', '04', '12', 'SAÑAYCA');
INSERT INTO `ubigeo_inei` VALUES (332, '03', '04', '13', 'SORAYA');
INSERT INTO `ubigeo_inei` VALUES (333, '03', '04', '14', 'TAPAIRIHUA');
INSERT INTO `ubigeo_inei` VALUES (334, '03', '04', '15', 'TINTAY');
INSERT INTO `ubigeo_inei` VALUES (335, '03', '04', '16', 'TORAYA');
INSERT INTO `ubigeo_inei` VALUES (336, '03', '04', '17', 'YANACA');
INSERT INTO `ubigeo_inei` VALUES (337, '03', '05', '00', 'COTABAMBAS');
INSERT INTO `ubigeo_inei` VALUES (338, '03', '05', '01', 'TAMBOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (339, '03', '05', '02', 'COTABAMBAS');
INSERT INTO `ubigeo_inei` VALUES (340, '03', '05', '03', 'COYLLURQUI');
INSERT INTO `ubigeo_inei` VALUES (341, '03', '05', '04', 'HAQUIRA');
INSERT INTO `ubigeo_inei` VALUES (342, '03', '05', '05', 'MARA');
INSERT INTO `ubigeo_inei` VALUES (343, '03', '05', '06', 'CHALLHUAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (344, '03', '06', '00', 'CHINCHEROS');
INSERT INTO `ubigeo_inei` VALUES (345, '03', '06', '01', 'CHINCHEROS');
INSERT INTO `ubigeo_inei` VALUES (346, '03', '06', '02', 'ANCO-HUALLO');
INSERT INTO `ubigeo_inei` VALUES (347, '03', '06', '03', 'COCHARCAS');
INSERT INTO `ubigeo_inei` VALUES (348, '03', '06', '04', 'HUACCANA');
INSERT INTO `ubigeo_inei` VALUES (349, '03', '06', '05', 'OCOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (350, '03', '06', '06', 'ONGOY');
INSERT INTO `ubigeo_inei` VALUES (351, '03', '06', '07', 'URANMARCA');
INSERT INTO `ubigeo_inei` VALUES (352, '03', '06', '08', 'RANRACANCHA');
INSERT INTO `ubigeo_inei` VALUES (353, '03', '07', '00', 'GRAU');
INSERT INTO `ubigeo_inei` VALUES (354, '03', '07', '01', 'CHUQUIBAMBILLA');
INSERT INTO `ubigeo_inei` VALUES (355, '03', '07', '02', 'CURPAHUASI');
INSERT INTO `ubigeo_inei` VALUES (356, '03', '07', '03', 'GAMARRA');
INSERT INTO `ubigeo_inei` VALUES (357, '03', '07', '04', 'HUAYLLATI');
INSERT INTO `ubigeo_inei` VALUES (358, '03', '07', '05', 'MAMARA');
INSERT INTO `ubigeo_inei` VALUES (359, '03', '07', '06', 'MICAELA BASTIDAS');
INSERT INTO `ubigeo_inei` VALUES (360, '03', '07', '07', 'PATAYPAMPA');
INSERT INTO `ubigeo_inei` VALUES (361, '03', '07', '08', 'PROGRESO');
INSERT INTO `ubigeo_inei` VALUES (362, '03', '07', '09', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (363, '03', '07', '10', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (364, '03', '07', '11', 'TURPAY');
INSERT INTO `ubigeo_inei` VALUES (365, '03', '07', '12', 'VILCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (366, '03', '07', '13', 'VIRUNDO');
INSERT INTO `ubigeo_inei` VALUES (367, '03', '07', '14', 'CURASCO');
INSERT INTO `ubigeo_inei` VALUES (368, '04', '00', '00', 'AREQUIPA');
INSERT INTO `ubigeo_inei` VALUES (369, '04', '01', '00', 'AREQUIPA');
INSERT INTO `ubigeo_inei` VALUES (370, '04', '01', '01', 'AREQUIPA');
INSERT INTO `ubigeo_inei` VALUES (371, '04', '01', '02', 'ALTO SELVA ALEGRE');
INSERT INTO `ubigeo_inei` VALUES (372, '04', '01', '03', 'CAYMA');
INSERT INTO `ubigeo_inei` VALUES (373, '04', '01', '04', 'CERRO COLORADO');
INSERT INTO `ubigeo_inei` VALUES (374, '04', '01', '05', 'CHARACATO');
INSERT INTO `ubigeo_inei` VALUES (375, '04', '01', '06', 'CHIGUATA');
INSERT INTO `ubigeo_inei` VALUES (376, '04', '01', '07', 'JACOBO HUNTER');
INSERT INTO `ubigeo_inei` VALUES (377, '04', '01', '08', 'LA JOYA');
INSERT INTO `ubigeo_inei` VALUES (378, '04', '01', '09', 'MARIANO MELGAR');
INSERT INTO `ubigeo_inei` VALUES (379, '04', '01', '10', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (380, '04', '01', '11', 'MOLLEBAYA');
INSERT INTO `ubigeo_inei` VALUES (381, '04', '01', '12', 'PAUCARPATA');
INSERT INTO `ubigeo_inei` VALUES (382, '04', '01', '13', 'POCSI');
INSERT INTO `ubigeo_inei` VALUES (383, '04', '01', '14', 'POLOBAYA');
INSERT INTO `ubigeo_inei` VALUES (384, '04', '01', '15', 'QUEQUEÑA');
INSERT INTO `ubigeo_inei` VALUES (385, '04', '01', '16', 'SABANDIA');
INSERT INTO `ubigeo_inei` VALUES (386, '04', '01', '17', 'SACHACA');
INSERT INTO `ubigeo_inei` VALUES (387, '04', '01', '18', 'SAN JUAN DE SIGUAS');
INSERT INTO `ubigeo_inei` VALUES (388, '04', '01', '19', 'SAN JUAN DE TARUCANI');
INSERT INTO `ubigeo_inei` VALUES (389, '04', '01', '20', 'SANTA ISABEL DE SIGUAS');
INSERT INTO `ubigeo_inei` VALUES (390, '04', '01', '21', 'SANTA RITA DE SIGUAS');
INSERT INTO `ubigeo_inei` VALUES (391, '04', '01', '22', 'SOCABAYA');
INSERT INTO `ubigeo_inei` VALUES (392, '04', '01', '23', 'TIABAYA');
INSERT INTO `ubigeo_inei` VALUES (393, '04', '01', '24', 'UCHUMAYO');
INSERT INTO `ubigeo_inei` VALUES (394, '04', '01', '25', 'VITOR');
INSERT INTO `ubigeo_inei` VALUES (395, '04', '01', '26', 'YANAHUARA');
INSERT INTO `ubigeo_inei` VALUES (396, '04', '01', '27', 'YARABAMBA');
INSERT INTO `ubigeo_inei` VALUES (397, '04', '01', '28', 'YURA');
INSERT INTO `ubigeo_inei` VALUES (398, '04', '01', '29', 'JOSE LUIS BUSTAMANTE Y RIVERO');
INSERT INTO `ubigeo_inei` VALUES (399, '04', '02', '00', 'CAMANA');
INSERT INTO `ubigeo_inei` VALUES (400, '04', '02', '01', 'CAMANA');
INSERT INTO `ubigeo_inei` VALUES (401, '04', '02', '02', 'JOSE MARIA QUIMPER');
INSERT INTO `ubigeo_inei` VALUES (402, '04', '02', '03', 'MARIANO NICOLAS VALCARCEL');
INSERT INTO `ubigeo_inei` VALUES (403, '04', '02', '04', 'MARISCAL CACERES');
INSERT INTO `ubigeo_inei` VALUES (404, '04', '02', '05', 'NICOLAS DE PIEROLA');
INSERT INTO `ubigeo_inei` VALUES (405, '04', '02', '06', 'OCOÑA');
INSERT INTO `ubigeo_inei` VALUES (406, '04', '02', '07', 'QUILCA');
INSERT INTO `ubigeo_inei` VALUES (407, '04', '02', '08', 'SAMUEL PASTOR');
INSERT INTO `ubigeo_inei` VALUES (408, '04', '03', '00', 'CARAVELI');
INSERT INTO `ubigeo_inei` VALUES (409, '04', '03', '01', 'CARAVELI');
INSERT INTO `ubigeo_inei` VALUES (410, '04', '03', '02', 'ACARI');
INSERT INTO `ubigeo_inei` VALUES (411, '04', '03', '03', 'ATICO');
INSERT INTO `ubigeo_inei` VALUES (412, '04', '03', '04', 'ATIQUIPA');
INSERT INTO `ubigeo_inei` VALUES (413, '04', '03', '05', 'BELLA UNION');
INSERT INTO `ubigeo_inei` VALUES (414, '04', '03', '06', 'CAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (415, '04', '03', '07', 'CHALA');
INSERT INTO `ubigeo_inei` VALUES (416, '04', '03', '08', 'CHAPARRA');
INSERT INTO `ubigeo_inei` VALUES (417, '04', '03', '09', 'HUANUHUANU');
INSERT INTO `ubigeo_inei` VALUES (418, '04', '03', '10', 'JAQUI');
INSERT INTO `ubigeo_inei` VALUES (419, '04', '03', '11', 'LOMAS');
INSERT INTO `ubigeo_inei` VALUES (420, '04', '03', '12', 'QUICACHA');
INSERT INTO `ubigeo_inei` VALUES (421, '04', '03', '13', 'YAUCA');
INSERT INTO `ubigeo_inei` VALUES (422, '04', '04', '00', 'CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (423, '04', '04', '01', 'APLAO');
INSERT INTO `ubigeo_inei` VALUES (424, '04', '04', '02', 'ANDAGUA');
INSERT INTO `ubigeo_inei` VALUES (425, '04', '04', '03', 'AYO');
INSERT INTO `ubigeo_inei` VALUES (426, '04', '04', '04', 'CHACHAS');
INSERT INTO `ubigeo_inei` VALUES (427, '04', '04', '05', 'CHILCAYMARCA');
INSERT INTO `ubigeo_inei` VALUES (428, '04', '04', '06', 'CHOCO');
INSERT INTO `ubigeo_inei` VALUES (429, '04', '04', '07', 'HUANCARQUI');
INSERT INTO `ubigeo_inei` VALUES (430, '04', '04', '08', 'MACHAGUAY');
INSERT INTO `ubigeo_inei` VALUES (431, '04', '04', '09', 'ORCOPAMPA');
INSERT INTO `ubigeo_inei` VALUES (432, '04', '04', '10', 'PAMPACOLCA');
INSERT INTO `ubigeo_inei` VALUES (433, '04', '04', '11', 'TIPAN');
INSERT INTO `ubigeo_inei` VALUES (434, '04', '04', '12', 'UÑON');
INSERT INTO `ubigeo_inei` VALUES (435, '04', '04', '13', 'URACA');
INSERT INTO `ubigeo_inei` VALUES (436, '04', '04', '14', 'VIRACO');
INSERT INTO `ubigeo_inei` VALUES (437, '04', '05', '00', 'CAYLLOMA');
INSERT INTO `ubigeo_inei` VALUES (438, '04', '05', '01', 'CHIVAY');
INSERT INTO `ubigeo_inei` VALUES (439, '04', '05', '02', 'ACHOMA');
INSERT INTO `ubigeo_inei` VALUES (440, '04', '05', '03', 'CABANACONDE');
INSERT INTO `ubigeo_inei` VALUES (441, '04', '05', '04', 'CALLALLI');
INSERT INTO `ubigeo_inei` VALUES (442, '04', '05', '05', 'CAYLLOMA');
INSERT INTO `ubigeo_inei` VALUES (443, '04', '05', '06', 'COPORAQUE');
INSERT INTO `ubigeo_inei` VALUES (444, '04', '05', '07', 'HUAMBO');
INSERT INTO `ubigeo_inei` VALUES (445, '04', '05', '08', 'HUANCA');
INSERT INTO `ubigeo_inei` VALUES (446, '04', '05', '09', 'ICHUPAMPA');
INSERT INTO `ubigeo_inei` VALUES (447, '04', '05', '10', 'LARI');
INSERT INTO `ubigeo_inei` VALUES (448, '04', '05', '11', 'LLUTA');
INSERT INTO `ubigeo_inei` VALUES (449, '04', '05', '12', 'MACA');
INSERT INTO `ubigeo_inei` VALUES (450, '04', '05', '13', 'MADRIGAL');
INSERT INTO `ubigeo_inei` VALUES (451, '04', '05', '14', 'SAN ANTONIO DE CHUCA');
INSERT INTO `ubigeo_inei` VALUES (452, '04', '05', '15', 'SIBAYO');
INSERT INTO `ubigeo_inei` VALUES (453, '04', '05', '16', 'TAPAY');
INSERT INTO `ubigeo_inei` VALUES (454, '04', '05', '17', 'TISCO');
INSERT INTO `ubigeo_inei` VALUES (455, '04', '05', '18', 'TUTI');
INSERT INTO `ubigeo_inei` VALUES (456, '04', '05', '19', 'YANQUE');
INSERT INTO `ubigeo_inei` VALUES (457, '04', '05', '20', 'MAJES');
INSERT INTO `ubigeo_inei` VALUES (458, '04', '06', '00', 'CONDESUYOS');
INSERT INTO `ubigeo_inei` VALUES (459, '04', '06', '01', 'CHUQUIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (460, '04', '06', '02', 'ANDARAY');
INSERT INTO `ubigeo_inei` VALUES (461, '04', '06', '03', 'CAYARANI');
INSERT INTO `ubigeo_inei` VALUES (462, '04', '06', '04', 'CHICHAS');
INSERT INTO `ubigeo_inei` VALUES (463, '04', '06', '05', 'IRAY');
INSERT INTO `ubigeo_inei` VALUES (464, '04', '06', '06', 'RIO GRANDE');
INSERT INTO `ubigeo_inei` VALUES (465, '04', '06', '07', 'SALAMANCA');
INSERT INTO `ubigeo_inei` VALUES (466, '04', '06', '08', 'YANAQUIHUA');
INSERT INTO `ubigeo_inei` VALUES (467, '04', '07', '00', 'ISLAY');
INSERT INTO `ubigeo_inei` VALUES (468, '04', '07', '01', 'MOLLENDO');
INSERT INTO `ubigeo_inei` VALUES (469, '04', '07', '02', 'COCACHACRA');
INSERT INTO `ubigeo_inei` VALUES (470, '04', '07', '03', 'DEAN VALDIVIA');
INSERT INTO `ubigeo_inei` VALUES (471, '04', '07', '04', 'ISLAY');
INSERT INTO `ubigeo_inei` VALUES (472, '04', '07', '05', 'MEJIA');
INSERT INTO `ubigeo_inei` VALUES (473, '04', '07', '06', 'PUNTA DE BOMBON');
INSERT INTO `ubigeo_inei` VALUES (474, '04', '08', '00', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (475, '04', '08', '01', 'COTAHUASI');
INSERT INTO `ubigeo_inei` VALUES (476, '04', '08', '02', 'ALCA');
INSERT INTO `ubigeo_inei` VALUES (477, '04', '08', '03', 'CHARCANA');
INSERT INTO `ubigeo_inei` VALUES (478, '04', '08', '04', 'HUAYNACOTAS');
INSERT INTO `ubigeo_inei` VALUES (479, '04', '08', '05', 'PAMPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (480, '04', '08', '06', 'PUYCA');
INSERT INTO `ubigeo_inei` VALUES (481, '04', '08', '07', 'QUECHUALLA');
INSERT INTO `ubigeo_inei` VALUES (482, '04', '08', '08', 'SAYLA');
INSERT INTO `ubigeo_inei` VALUES (483, '04', '08', '09', 'TAURIA');
INSERT INTO `ubigeo_inei` VALUES (484, '04', '08', '10', 'TOMEPAMPA');
INSERT INTO `ubigeo_inei` VALUES (485, '04', '08', '11', 'TORO');
INSERT INTO `ubigeo_inei` VALUES (486, '05', '00', '00', 'AYACUCHO');
INSERT INTO `ubigeo_inei` VALUES (487, '05', '01', '00', 'HUAMANGA');
INSERT INTO `ubigeo_inei` VALUES (488, '05', '01', '01', 'AYACUCHO');
INSERT INTO `ubigeo_inei` VALUES (489, '05', '01', '02', 'ACOCRO');
INSERT INTO `ubigeo_inei` VALUES (490, '05', '01', '03', 'ACOS VINCHOS');
INSERT INTO `ubigeo_inei` VALUES (491, '05', '01', '04', 'CARMEN ALTO');
INSERT INTO `ubigeo_inei` VALUES (492, '05', '01', '05', 'CHIARA');
INSERT INTO `ubigeo_inei` VALUES (493, '05', '01', '06', 'OCROS');
INSERT INTO `ubigeo_inei` VALUES (494, '05', '01', '07', 'PACAYCASA');
INSERT INTO `ubigeo_inei` VALUES (495, '05', '01', '08', 'QUINUA');
INSERT INTO `ubigeo_inei` VALUES (496, '05', '01', '09', 'SAN JOSE DE TICLLAS');
INSERT INTO `ubigeo_inei` VALUES (497, '05', '01', '10', 'SAN JUAN BAUTISTA');
INSERT INTO `ubigeo_inei` VALUES (498, '05', '01', '11', 'SANTIAGO DE PISCHA');
INSERT INTO `ubigeo_inei` VALUES (499, '05', '01', '12', 'SOCOS');
INSERT INTO `ubigeo_inei` VALUES (500, '05', '01', '13', 'TAMBILLO');
INSERT INTO `ubigeo_inei` VALUES (501, '05', '01', '14', 'VINCHOS');
INSERT INTO `ubigeo_inei` VALUES (502, '05', '01', '15', 'JESÚS NAZARENO');
INSERT INTO `ubigeo_inei` VALUES (503, '05', '01', '16', 'ANDRÉS AVELINO CÁCERES DORREGAY');
INSERT INTO `ubigeo_inei` VALUES (504, '05', '02', '00', 'CANGALLO');
INSERT INTO `ubigeo_inei` VALUES (505, '05', '02', '01', 'CANGALLO');
INSERT INTO `ubigeo_inei` VALUES (506, '05', '02', '02', 'CHUSCHI');
INSERT INTO `ubigeo_inei` VALUES (507, '05', '02', '03', 'LOS MOROCHUCOS');
INSERT INTO `ubigeo_inei` VALUES (508, '05', '02', '04', 'MARIA PARADO DE BELLIDO');
INSERT INTO `ubigeo_inei` VALUES (509, '05', '02', '05', 'PARAS');
INSERT INTO `ubigeo_inei` VALUES (510, '05', '02', '06', 'TOTOS');
INSERT INTO `ubigeo_inei` VALUES (511, '05', '03', '00', 'HUANCA SANCOS');
INSERT INTO `ubigeo_inei` VALUES (512, '05', '03', '01', 'SANCOS');
INSERT INTO `ubigeo_inei` VALUES (513, '05', '03', '02', 'CARAPO');
INSERT INTO `ubigeo_inei` VALUES (514, '05', '03', '03', 'SACSAMARCA');
INSERT INTO `ubigeo_inei` VALUES (515, '05', '03', '04', 'SANTIAGO DE LUCANAMARCA');
INSERT INTO `ubigeo_inei` VALUES (516, '05', '04', '00', 'HUANTA');
INSERT INTO `ubigeo_inei` VALUES (517, '05', '04', '01', 'HUANTA');
INSERT INTO `ubigeo_inei` VALUES (518, '05', '04', '02', 'AYAHUANCO');
INSERT INTO `ubigeo_inei` VALUES (519, '05', '04', '03', 'HUAMANGUILLA');
INSERT INTO `ubigeo_inei` VALUES (520, '05', '04', '04', 'IGUAIN');
INSERT INTO `ubigeo_inei` VALUES (521, '05', '04', '05', 'LURICOCHA');
INSERT INTO `ubigeo_inei` VALUES (522, '05', '04', '06', 'SANTILLANA');
INSERT INTO `ubigeo_inei` VALUES (523, '05', '04', '07', 'SIVIA');
INSERT INTO `ubigeo_inei` VALUES (524, '05', '04', '08', 'LLOCHEGUA');
INSERT INTO `ubigeo_inei` VALUES (525, '05', '04', '09', 'CANAYRE');
INSERT INTO `ubigeo_inei` VALUES (526, '05', '04', '10', 'UCHURACCAY');
INSERT INTO `ubigeo_inei` VALUES (527, '05', '04', '11', 'PUCACOLPA');
INSERT INTO `ubigeo_inei` VALUES (528, '05', '05', '00', 'LA MAR');
INSERT INTO `ubigeo_inei` VALUES (529, '05', '05', '01', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (530, '05', '05', '02', 'ANCO');
INSERT INTO `ubigeo_inei` VALUES (531, '05', '05', '03', 'AYNA');
INSERT INTO `ubigeo_inei` VALUES (532, '05', '05', '04', 'CHILCAS');
INSERT INTO `ubigeo_inei` VALUES (533, '05', '05', '05', 'CHUNGUI');
INSERT INTO `ubigeo_inei` VALUES (534, '05', '05', '06', 'LUIS CARRANZA');
INSERT INTO `ubigeo_inei` VALUES (535, '05', '05', '07', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (536, '05', '05', '08', 'TAMBO');
INSERT INTO `ubigeo_inei` VALUES (537, '05', '05', '09', 'SAMUGARI');
INSERT INTO `ubigeo_inei` VALUES (538, '05', '05', '10', 'ANCHIHUAY');
INSERT INTO `ubigeo_inei` VALUES (539, '05', '06', '00', 'LUCANAS');
INSERT INTO `ubigeo_inei` VALUES (540, '05', '06', '01', 'PUQUIO');
INSERT INTO `ubigeo_inei` VALUES (541, '05', '06', '02', 'AUCARA');
INSERT INTO `ubigeo_inei` VALUES (542, '05', '06', '03', 'CABANA');
INSERT INTO `ubigeo_inei` VALUES (543, '05', '06', '04', 'CARMEN SALCEDO');
INSERT INTO `ubigeo_inei` VALUES (544, '05', '06', '05', 'CHAVIÑA');
INSERT INTO `ubigeo_inei` VALUES (545, '05', '06', '06', 'CHIPAO');
INSERT INTO `ubigeo_inei` VALUES (546, '05', '06', '07', 'HUAC-HUAS');
INSERT INTO `ubigeo_inei` VALUES (547, '05', '06', '08', 'LARAMATE');
INSERT INTO `ubigeo_inei` VALUES (548, '05', '06', '09', 'LEONCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (549, '05', '06', '10', 'LLAUTA');
INSERT INTO `ubigeo_inei` VALUES (550, '05', '06', '11', 'LUCANAS');
INSERT INTO `ubigeo_inei` VALUES (551, '05', '06', '12', 'OCAÑA');
INSERT INTO `ubigeo_inei` VALUES (552, '05', '06', '13', 'OTOCA');
INSERT INTO `ubigeo_inei` VALUES (553, '05', '06', '14', 'SAISA');
INSERT INTO `ubigeo_inei` VALUES (554, '05', '06', '15', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (555, '05', '06', '16', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (556, '05', '06', '17', 'SAN PEDRO');
INSERT INTO `ubigeo_inei` VALUES (557, '05', '06', '18', 'SAN PEDRO DE PALCO');
INSERT INTO `ubigeo_inei` VALUES (558, '05', '06', '19', 'SANCOS');
INSERT INTO `ubigeo_inei` VALUES (559, '05', '06', '20', 'SANTA ANA DE HUAYCAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (560, '05', '06', '21', 'SANTA LUCIA');
INSERT INTO `ubigeo_inei` VALUES (561, '05', '07', '00', 'PARINACOCHAS');
INSERT INTO `ubigeo_inei` VALUES (562, '05', '07', '01', 'CORACORA');
INSERT INTO `ubigeo_inei` VALUES (563, '05', '07', '02', 'CHUMPI');
INSERT INTO `ubigeo_inei` VALUES (564, '05', '07', '03', 'CORONEL CASTAÑEDA');
INSERT INTO `ubigeo_inei` VALUES (565, '05', '07', '04', 'PACAPAUSA');
INSERT INTO `ubigeo_inei` VALUES (566, '05', '07', '05', 'PULLO');
INSERT INTO `ubigeo_inei` VALUES (567, '05', '07', '06', 'PUYUSCA');
INSERT INTO `ubigeo_inei` VALUES (568, '05', '07', '07', 'SAN FRANCISCO DE RAVACAYCO');
INSERT INTO `ubigeo_inei` VALUES (569, '05', '07', '08', 'UPAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (570, '05', '08', '00', 'PAUCAR DEL SARA SARA');
INSERT INTO `ubigeo_inei` VALUES (571, '05', '08', '01', 'PAUSA');
INSERT INTO `ubigeo_inei` VALUES (572, '05', '08', '02', 'COLTA');
INSERT INTO `ubigeo_inei` VALUES (573, '05', '08', '03', 'CORCULLA');
INSERT INTO `ubigeo_inei` VALUES (574, '05', '08', '04', 'LAMPA');
INSERT INTO `ubigeo_inei` VALUES (575, '05', '08', '05', 'MARCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (576, '05', '08', '06', 'OYOLO');
INSERT INTO `ubigeo_inei` VALUES (577, '05', '08', '07', 'PARARCA');
INSERT INTO `ubigeo_inei` VALUES (578, '05', '08', '08', 'SAN JAVIER DE ALPABAMBA');
INSERT INTO `ubigeo_inei` VALUES (579, '05', '08', '09', 'SAN JOSE DE USHUA');
INSERT INTO `ubigeo_inei` VALUES (580, '05', '08', '10', 'SARA SARA');
INSERT INTO `ubigeo_inei` VALUES (581, '05', '09', '00', 'SUCRE');
INSERT INTO `ubigeo_inei` VALUES (582, '05', '09', '01', 'QUEROBAMBA');
INSERT INTO `ubigeo_inei` VALUES (583, '05', '09', '02', 'BELEN');
INSERT INTO `ubigeo_inei` VALUES (584, '05', '09', '03', 'CHALCOS');
INSERT INTO `ubigeo_inei` VALUES (585, '05', '09', '04', 'CHILCAYOC');
INSERT INTO `ubigeo_inei` VALUES (586, '05', '09', '05', 'HUACAÑA');
INSERT INTO `ubigeo_inei` VALUES (587, '05', '09', '06', 'MORCOLLA');
INSERT INTO `ubigeo_inei` VALUES (588, '05', '09', '07', 'PAICO');
INSERT INTO `ubigeo_inei` VALUES (589, '05', '09', '08', 'SAN PEDRO DE LARCAY');
INSERT INTO `ubigeo_inei` VALUES (590, '05', '09', '09', 'SAN SALVADOR DE QUIJE');
INSERT INTO `ubigeo_inei` VALUES (591, '05', '09', '10', 'SANTIAGO DE PAUCARAY');
INSERT INTO `ubigeo_inei` VALUES (592, '05', '09', '11', 'SORAS');
INSERT INTO `ubigeo_inei` VALUES (593, '05', '10', '00', 'VICTOR FAJARDO');
INSERT INTO `ubigeo_inei` VALUES (594, '05', '10', '01', 'HUANCAPI');
INSERT INTO `ubigeo_inei` VALUES (595, '05', '10', '02', 'ALCAMENCA');
INSERT INTO `ubigeo_inei` VALUES (596, '05', '10', '03', 'APONGO');
INSERT INTO `ubigeo_inei` VALUES (597, '05', '10', '04', 'ASQUIPATA');
INSERT INTO `ubigeo_inei` VALUES (598, '05', '10', '05', 'CANARIA');
INSERT INTO `ubigeo_inei` VALUES (599, '05', '10', '06', 'CAYARA');
INSERT INTO `ubigeo_inei` VALUES (600, '05', '10', '07', 'COLCA');
INSERT INTO `ubigeo_inei` VALUES (601, '05', '10', '08', 'HUAMANQUIQUIA');
INSERT INTO `ubigeo_inei` VALUES (602, '05', '10', '09', 'HUANCARAYLLA');
INSERT INTO `ubigeo_inei` VALUES (603, '05', '10', '10', 'HUAYA');
INSERT INTO `ubigeo_inei` VALUES (604, '05', '10', '11', 'SARHUA');
INSERT INTO `ubigeo_inei` VALUES (605, '05', '10', '12', 'VILCANCHOS');
INSERT INTO `ubigeo_inei` VALUES (606, '05', '11', '00', 'VILCAS HUAMAN');
INSERT INTO `ubigeo_inei` VALUES (607, '05', '11', '01', 'VILCAS HUAMAN');
INSERT INTO `ubigeo_inei` VALUES (608, '05', '11', '02', 'ACCOMARCA');
INSERT INTO `ubigeo_inei` VALUES (609, '05', '11', '03', 'CARHUANCA');
INSERT INTO `ubigeo_inei` VALUES (610, '05', '11', '04', 'CONCEPCION');
INSERT INTO `ubigeo_inei` VALUES (611, '05', '11', '05', 'HUAMBALPA');
INSERT INTO `ubigeo_inei` VALUES (612, '05', '11', '06', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (613, '05', '11', '07', 'SAURAMA');
INSERT INTO `ubigeo_inei` VALUES (614, '05', '11', '08', 'VISCHONGO');
INSERT INTO `ubigeo_inei` VALUES (615, '06', '00', '00', 'CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (616, '06', '01', '00', 'CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (617, '06', '01', '01', 'CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (618, '06', '01', '02', 'ASUNCION');
INSERT INTO `ubigeo_inei` VALUES (619, '06', '01', '03', 'CHETILLA');
INSERT INTO `ubigeo_inei` VALUES (620, '06', '01', '04', 'COSPAN');
INSERT INTO `ubigeo_inei` VALUES (621, '06', '01', '05', 'ENCAÑADA');
INSERT INTO `ubigeo_inei` VALUES (622, '06', '01', '06', 'JESUS');
INSERT INTO `ubigeo_inei` VALUES (623, '06', '01', '07', 'LLACANORA');
INSERT INTO `ubigeo_inei` VALUES (624, '06', '01', '08', 'LOS BAÑOS DEL INCA');
INSERT INTO `ubigeo_inei` VALUES (625, '06', '01', '09', 'MAGDALENA');
INSERT INTO `ubigeo_inei` VALUES (626, '06', '01', '10', 'MATARA');
INSERT INTO `ubigeo_inei` VALUES (627, '06', '01', '11', 'NAMORA');
INSERT INTO `ubigeo_inei` VALUES (628, '06', '01', '12', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (629, '06', '02', '00', 'CAJABAMBA');
INSERT INTO `ubigeo_inei` VALUES (630, '06', '02', '01', 'CAJABAMBA');
INSERT INTO `ubigeo_inei` VALUES (631, '06', '02', '02', 'CACHACHI');
INSERT INTO `ubigeo_inei` VALUES (632, '06', '02', '03', 'CONDEBAMBA');
INSERT INTO `ubigeo_inei` VALUES (633, '06', '02', '04', 'SITACOCHA');
INSERT INTO `ubigeo_inei` VALUES (634, '06', '03', '00', 'CELENDIN');
INSERT INTO `ubigeo_inei` VALUES (635, '06', '03', '01', 'CELENDIN');
INSERT INTO `ubigeo_inei` VALUES (636, '06', '03', '02', 'CHUMUCH');
INSERT INTO `ubigeo_inei` VALUES (637, '06', '03', '03', 'CORTEGANA');
INSERT INTO `ubigeo_inei` VALUES (638, '06', '03', '04', 'HUASMIN');
INSERT INTO `ubigeo_inei` VALUES (639, '06', '03', '05', 'JORGE CHAVEZ');
INSERT INTO `ubigeo_inei` VALUES (640, '06', '03', '06', 'JOSE GALVEZ');
INSERT INTO `ubigeo_inei` VALUES (641, '06', '03', '07', 'MIGUEL IGLESIAS');
INSERT INTO `ubigeo_inei` VALUES (642, '06', '03', '08', 'OXAMARCA');
INSERT INTO `ubigeo_inei` VALUES (643, '06', '03', '09', 'SOROCHUCO');
INSERT INTO `ubigeo_inei` VALUES (644, '06', '03', '10', 'SUCRE');
INSERT INTO `ubigeo_inei` VALUES (645, '06', '03', '11', 'UTCO');
INSERT INTO `ubigeo_inei` VALUES (646, '06', '03', '12', 'LA LIBERTAD DE PALLAN');
INSERT INTO `ubigeo_inei` VALUES (647, '06', '04', '00', 'CHOTA');
INSERT INTO `ubigeo_inei` VALUES (648, '06', '04', '01', 'CHOTA');
INSERT INTO `ubigeo_inei` VALUES (649, '06', '04', '02', 'ANGUIA');
INSERT INTO `ubigeo_inei` VALUES (650, '06', '04', '03', 'CHADIN');
INSERT INTO `ubigeo_inei` VALUES (651, '06', '04', '04', 'CHIGUIRIP');
INSERT INTO `ubigeo_inei` VALUES (652, '06', '04', '05', 'CHIMBAN');
INSERT INTO `ubigeo_inei` VALUES (653, '06', '04', '06', 'CHOROPAMPA');
INSERT INTO `ubigeo_inei` VALUES (654, '06', '04', '07', 'COCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (655, '06', '04', '08', 'CONCHAN');
INSERT INTO `ubigeo_inei` VALUES (656, '06', '04', '09', 'HUAMBOS');
INSERT INTO `ubigeo_inei` VALUES (657, '06', '04', '10', 'LAJAS');
INSERT INTO `ubigeo_inei` VALUES (658, '06', '04', '11', 'LLAMA');
INSERT INTO `ubigeo_inei` VALUES (659, '06', '04', '12', 'MIRACOSTA');
INSERT INTO `ubigeo_inei` VALUES (660, '06', '04', '13', 'PACCHA');
INSERT INTO `ubigeo_inei` VALUES (661, '06', '04', '14', 'PION');
INSERT INTO `ubigeo_inei` VALUES (662, '06', '04', '15', 'QUEROCOTO');
INSERT INTO `ubigeo_inei` VALUES (663, '06', '04', '16', 'SAN JUAN DE LICUPIS');
INSERT INTO `ubigeo_inei` VALUES (664, '06', '04', '17', 'TACABAMBA');
INSERT INTO `ubigeo_inei` VALUES (665, '06', '04', '18', 'TOCMOCHE');
INSERT INTO `ubigeo_inei` VALUES (666, '06', '04', '19', 'CHALAMARCA');
INSERT INTO `ubigeo_inei` VALUES (667, '06', '05', '00', 'CONTUMAZA');
INSERT INTO `ubigeo_inei` VALUES (668, '06', '05', '01', 'CONTUMAZA');
INSERT INTO `ubigeo_inei` VALUES (669, '06', '05', '02', 'CHILETE');
INSERT INTO `ubigeo_inei` VALUES (670, '06', '05', '03', 'CUPISNIQUE');
INSERT INTO `ubigeo_inei` VALUES (671, '06', '05', '04', 'GUZMANGO');
INSERT INTO `ubigeo_inei` VALUES (672, '06', '05', '05', 'SAN BENITO');
INSERT INTO `ubigeo_inei` VALUES (673, '06', '05', '06', 'SANTA CRUZ DE TOLED');
INSERT INTO `ubigeo_inei` VALUES (674, '06', '05', '07', 'TANTARICA');
INSERT INTO `ubigeo_inei` VALUES (675, '06', '05', '08', 'YONAN');
INSERT INTO `ubigeo_inei` VALUES (676, '06', '06', '00', 'CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (677, '06', '06', '01', 'CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (678, '06', '06', '02', 'CALLAYUC');
INSERT INTO `ubigeo_inei` VALUES (679, '06', '06', '03', 'CHOROS');
INSERT INTO `ubigeo_inei` VALUES (680, '06', '06', '04', 'CUJILLO');
INSERT INTO `ubigeo_inei` VALUES (681, '06', '06', '05', 'LA RAMADA');
INSERT INTO `ubigeo_inei` VALUES (682, '06', '06', '06', 'PIMPINGOS');
INSERT INTO `ubigeo_inei` VALUES (683, '06', '06', '07', 'QUEROCOTILLO');
INSERT INTO `ubigeo_inei` VALUES (684, '06', '06', '08', 'SAN ANDRES DE CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (685, '06', '06', '09', 'SAN JUAN DE CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (686, '06', '06', '10', 'SAN LUIS DE LUCMA');
INSERT INTO `ubigeo_inei` VALUES (687, '06', '06', '11', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (688, '06', '06', '12', 'SANTO DOMINGO DE LA CAPILLA');
INSERT INTO `ubigeo_inei` VALUES (689, '06', '06', '13', 'SANTO TOMAS');
INSERT INTO `ubigeo_inei` VALUES (690, '06', '06', '14', 'SOCOTA');
INSERT INTO `ubigeo_inei` VALUES (691, '06', '06', '15', 'TORIBIO CASANOVA');
INSERT INTO `ubigeo_inei` VALUES (692, '06', '07', '00', 'HUALGAYOC');
INSERT INTO `ubigeo_inei` VALUES (693, '06', '07', '01', 'BAMBAMARCA');
INSERT INTO `ubigeo_inei` VALUES (694, '06', '07', '02', 'CHUGUR');
INSERT INTO `ubigeo_inei` VALUES (695, '06', '07', '03', 'HUALGAYOC');
INSERT INTO `ubigeo_inei` VALUES (696, '06', '08', '00', 'JAEN');
INSERT INTO `ubigeo_inei` VALUES (697, '06', '08', '01', 'JAEN');
INSERT INTO `ubigeo_inei` VALUES (698, '06', '08', '02', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (699, '06', '08', '03', 'CHONTALI');
INSERT INTO `ubigeo_inei` VALUES (700, '06', '08', '04', 'COLASAY');
INSERT INTO `ubigeo_inei` VALUES (701, '06', '08', '05', 'HUABAL');
INSERT INTO `ubigeo_inei` VALUES (702, '06', '08', '06', 'LAS PIRIAS');
INSERT INTO `ubigeo_inei` VALUES (703, '06', '08', '07', 'POMAHUACA');
INSERT INTO `ubigeo_inei` VALUES (704, '06', '08', '08', 'PUCARA');
INSERT INTO `ubigeo_inei` VALUES (705, '06', '08', '09', 'SALLIQUE');
INSERT INTO `ubigeo_inei` VALUES (706, '06', '08', '10', 'SAN FELIPE');
INSERT INTO `ubigeo_inei` VALUES (707, '06', '08', '11', 'SAN JOSE DEL ALTO');
INSERT INTO `ubigeo_inei` VALUES (708, '06', '08', '12', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (709, '06', '09', '00', 'SAN IGNACIO');
INSERT INTO `ubigeo_inei` VALUES (710, '06', '09', '01', 'SAN IGNACIO');
INSERT INTO `ubigeo_inei` VALUES (711, '06', '09', '02', 'CHIRINOS');
INSERT INTO `ubigeo_inei` VALUES (712, '06', '09', '03', 'HUARANGO');
INSERT INTO `ubigeo_inei` VALUES (713, '06', '09', '04', 'LA COIPA');
INSERT INTO `ubigeo_inei` VALUES (714, '06', '09', '05', 'NAMBALLE');
INSERT INTO `ubigeo_inei` VALUES (715, '06', '09', '06', 'SAN JOSE DE LOURDES');
INSERT INTO `ubigeo_inei` VALUES (716, '06', '09', '07', 'TABACONAS');
INSERT INTO `ubigeo_inei` VALUES (717, '06', '10', '00', 'SAN MARCOS');
INSERT INTO `ubigeo_inei` VALUES (718, '06', '10', '01', 'PEDRO GALVEZ');
INSERT INTO `ubigeo_inei` VALUES (719, '06', '10', '02', 'CHANCAY');
INSERT INTO `ubigeo_inei` VALUES (720, '06', '10', '03', 'EDUARDO VILLANUEVA');
INSERT INTO `ubigeo_inei` VALUES (721, '06', '10', '04', 'GREGORIO PITA');
INSERT INTO `ubigeo_inei` VALUES (722, '06', '10', '05', 'ICHOCAN');
INSERT INTO `ubigeo_inei` VALUES (723, '06', '10', '06', 'JOSE MANUEL QUIROZ');
INSERT INTO `ubigeo_inei` VALUES (724, '06', '10', '07', 'JOSE SABOGAL');
INSERT INTO `ubigeo_inei` VALUES (725, '06', '11', '00', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (726, '06', '11', '01', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (727, '06', '11', '02', 'BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (728, '06', '11', '03', 'CALQUIS');
INSERT INTO `ubigeo_inei` VALUES (729, '06', '11', '04', 'CATILLUC');
INSERT INTO `ubigeo_inei` VALUES (730, '06', '11', '05', 'EL PRADO');
INSERT INTO `ubigeo_inei` VALUES (731, '06', '11', '06', 'LA FLORIDA');
INSERT INTO `ubigeo_inei` VALUES (732, '06', '11', '07', 'LLAPA');
INSERT INTO `ubigeo_inei` VALUES (733, '06', '11', '08', 'NANCHOC');
INSERT INTO `ubigeo_inei` VALUES (734, '06', '11', '09', 'NIEPOS');
INSERT INTO `ubigeo_inei` VALUES (735, '06', '11', '10', 'SAN GREGORIO');
INSERT INTO `ubigeo_inei` VALUES (736, '06', '11', '11', 'SAN SILVESTRE DE COCHAN');
INSERT INTO `ubigeo_inei` VALUES (737, '06', '11', '12', 'TONGOD');
INSERT INTO `ubigeo_inei` VALUES (738, '06', '11', '13', 'UNION AGUA BLANCA');
INSERT INTO `ubigeo_inei` VALUES (739, '06', '12', '00', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (740, '06', '12', '01', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (741, '06', '12', '02', 'SAN BERNARDINO');
INSERT INTO `ubigeo_inei` VALUES (742, '06', '12', '03', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (743, '06', '12', '04', 'TUMBADEN');
INSERT INTO `ubigeo_inei` VALUES (744, '06', '13', '00', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (745, '06', '13', '01', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (746, '06', '13', '02', 'ANDABAMBA');
INSERT INTO `ubigeo_inei` VALUES (747, '06', '13', '03', 'CATACHE');
INSERT INTO `ubigeo_inei` VALUES (748, '06', '13', '04', 'CHANCAYBAÑOS');
INSERT INTO `ubigeo_inei` VALUES (749, '06', '13', '05', 'LA ESPERANZA');
INSERT INTO `ubigeo_inei` VALUES (750, '06', '13', '06', 'NINABAMBA');
INSERT INTO `ubigeo_inei` VALUES (751, '06', '13', '07', 'PULAN');
INSERT INTO `ubigeo_inei` VALUES (752, '06', '13', '08', 'SAUCEPAMPA');
INSERT INTO `ubigeo_inei` VALUES (753, '06', '13', '09', 'SEXI');
INSERT INTO `ubigeo_inei` VALUES (754, '06', '13', '10', 'UTICYACU');
INSERT INTO `ubigeo_inei` VALUES (755, '06', '13', '11', 'YAUYUCAN');
INSERT INTO `ubigeo_inei` VALUES (756, '07', '00', '00', 'CALLAO');
INSERT INTO `ubigeo_inei` VALUES (757, '07', '01', '00', 'PROV. CONST. DEL CALLAO');
INSERT INTO `ubigeo_inei` VALUES (758, '07', '01', '01', 'CALLAO');
INSERT INTO `ubigeo_inei` VALUES (759, '07', '01', '02', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (760, '07', '01', '03', 'CARMEN DE LA LEGUA REYNOSO');
INSERT INTO `ubigeo_inei` VALUES (761, '07', '01', '04', 'LA PERLA');
INSERT INTO `ubigeo_inei` VALUES (762, '07', '01', '05', 'LA PUNTA');
INSERT INTO `ubigeo_inei` VALUES (763, '07', '01', '06', 'VENTANILLA');
INSERT INTO `ubigeo_inei` VALUES (764, '07', '01', '07', 'MI PERÚ');
INSERT INTO `ubigeo_inei` VALUES (765, '08', '00', '00', 'CUSCO');
INSERT INTO `ubigeo_inei` VALUES (766, '08', '01', '00', 'CUSCO');
INSERT INTO `ubigeo_inei` VALUES (767, '08', '01', '01', 'CUSCO');
INSERT INTO `ubigeo_inei` VALUES (768, '08', '01', '02', 'CCORCA');
INSERT INTO `ubigeo_inei` VALUES (769, '08', '01', '03', 'POROY');
INSERT INTO `ubigeo_inei` VALUES (770, '08', '01', '04', 'SAN JERONIMO');
INSERT INTO `ubigeo_inei` VALUES (771, '08', '01', '05', 'SAN SEBASTIAN');
INSERT INTO `ubigeo_inei` VALUES (772, '08', '01', '06', 'SANTIAGO');
INSERT INTO `ubigeo_inei` VALUES (773, '08', '01', '07', 'SAYLLA');
INSERT INTO `ubigeo_inei` VALUES (774, '08', '01', '08', 'WANCHAQ');
INSERT INTO `ubigeo_inei` VALUES (775, '08', '02', '00', 'ACOMAYO');
INSERT INTO `ubigeo_inei` VALUES (776, '08', '02', '01', 'ACOMAYO');
INSERT INTO `ubigeo_inei` VALUES (777, '08', '02', '02', 'ACOPIA');
INSERT INTO `ubigeo_inei` VALUES (778, '08', '02', '03', 'ACOS');
INSERT INTO `ubigeo_inei` VALUES (779, '08', '02', '04', 'MOSOC LLACTA');
INSERT INTO `ubigeo_inei` VALUES (780, '08', '02', '05', 'POMACANCHI');
INSERT INTO `ubigeo_inei` VALUES (781, '08', '02', '06', 'RONDOCAN');
INSERT INTO `ubigeo_inei` VALUES (782, '08', '02', '07', 'SANGARARA');
INSERT INTO `ubigeo_inei` VALUES (783, '08', '03', '00', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (784, '08', '03', '01', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (785, '08', '03', '02', 'ANCAHUASI');
INSERT INTO `ubigeo_inei` VALUES (786, '08', '03', '03', 'CACHIMAYO');
INSERT INTO `ubigeo_inei` VALUES (787, '08', '03', '04', 'CHINCHAYPUJIO');
INSERT INTO `ubigeo_inei` VALUES (788, '08', '03', '05', 'HUAROCONDO');
INSERT INTO `ubigeo_inei` VALUES (789, '08', '03', '06', 'LIMATAMBO');
INSERT INTO `ubigeo_inei` VALUES (790, '08', '03', '07', 'MOLLEPATA');
INSERT INTO `ubigeo_inei` VALUES (791, '08', '03', '08', 'PUCYURA');
INSERT INTO `ubigeo_inei` VALUES (792, '08', '03', '09', 'ZURITE');
INSERT INTO `ubigeo_inei` VALUES (793, '08', '04', '00', 'CALCA');
INSERT INTO `ubigeo_inei` VALUES (794, '08', '04', '01', 'CALCA');
INSERT INTO `ubigeo_inei` VALUES (795, '08', '04', '02', 'COYA');
INSERT INTO `ubigeo_inei` VALUES (796, '08', '04', '03', 'LAMAY');
INSERT INTO `ubigeo_inei` VALUES (797, '08', '04', '04', 'LARES');
INSERT INTO `ubigeo_inei` VALUES (798, '08', '04', '05', 'PISAC');
INSERT INTO `ubigeo_inei` VALUES (799, '08', '04', '06', 'SAN SALVADOR');
INSERT INTO `ubigeo_inei` VALUES (800, '08', '04', '07', 'TARAY');
INSERT INTO `ubigeo_inei` VALUES (801, '08', '04', '08', 'YANATILE');
INSERT INTO `ubigeo_inei` VALUES (802, '08', '05', '00', 'CANAS');
INSERT INTO `ubigeo_inei` VALUES (803, '08', '05', '01', 'YANAOCA');
INSERT INTO `ubigeo_inei` VALUES (804, '08', '05', '02', 'CHECCA');
INSERT INTO `ubigeo_inei` VALUES (805, '08', '05', '03', 'KUNTURKANKI');
INSERT INTO `ubigeo_inei` VALUES (806, '08', '05', '04', 'LANGUI');
INSERT INTO `ubigeo_inei` VALUES (807, '08', '05', '05', 'LAYO');
INSERT INTO `ubigeo_inei` VALUES (808, '08', '05', '06', 'PAMPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (809, '08', '05', '07', 'QUEHUE');
INSERT INTO `ubigeo_inei` VALUES (810, '08', '05', '08', 'TUPAC AMARU');
INSERT INTO `ubigeo_inei` VALUES (811, '08', '06', '00', 'CANCHIS');
INSERT INTO `ubigeo_inei` VALUES (812, '08', '06', '01', 'SICUANI');
INSERT INTO `ubigeo_inei` VALUES (813, '08', '06', '02', 'CHECACUPE');
INSERT INTO `ubigeo_inei` VALUES (814, '08', '06', '03', 'COMBAPATA');
INSERT INTO `ubigeo_inei` VALUES (815, '08', '06', '04', 'MARANGANI');
INSERT INTO `ubigeo_inei` VALUES (816, '08', '06', '05', 'PITUMARCA');
INSERT INTO `ubigeo_inei` VALUES (817, '08', '06', '06', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (818, '08', '06', '07', 'SAN PEDRO');
INSERT INTO `ubigeo_inei` VALUES (819, '08', '06', '08', 'TINTA');
INSERT INTO `ubigeo_inei` VALUES (820, '08', '07', '00', 'CHUMBIVILCAS');
INSERT INTO `ubigeo_inei` VALUES (821, '08', '07', '01', 'SANTO TOMAS');
INSERT INTO `ubigeo_inei` VALUES (822, '08', '07', '02', 'CAPACMARCA');
INSERT INTO `ubigeo_inei` VALUES (823, '08', '07', '03', 'CHAMACA');
INSERT INTO `ubigeo_inei` VALUES (824, '08', '07', '04', 'COLQUEMARCA');
INSERT INTO `ubigeo_inei` VALUES (825, '08', '07', '05', 'LIVITACA');
INSERT INTO `ubigeo_inei` VALUES (826, '08', '07', '06', 'LLUSCO');
INSERT INTO `ubigeo_inei` VALUES (827, '08', '07', '07', 'QUIÑOTA');
INSERT INTO `ubigeo_inei` VALUES (828, '08', '07', '08', 'VELILLE');
INSERT INTO `ubigeo_inei` VALUES (829, '08', '08', '00', 'ESPINAR');
INSERT INTO `ubigeo_inei` VALUES (830, '08', '08', '01', 'ESPINAR');
INSERT INTO `ubigeo_inei` VALUES (831, '08', '08', '02', 'CONDOROMA');
INSERT INTO `ubigeo_inei` VALUES (832, '08', '08', '03', 'COPORAQUE');
INSERT INTO `ubigeo_inei` VALUES (833, '08', '08', '04', 'OCORURO');
INSERT INTO `ubigeo_inei` VALUES (834, '08', '08', '05', 'PALLPATA');
INSERT INTO `ubigeo_inei` VALUES (835, '08', '08', '06', 'PICHIGUA');
INSERT INTO `ubigeo_inei` VALUES (836, '08', '08', '07', 'SUYCKUTAMBO');
INSERT INTO `ubigeo_inei` VALUES (837, '08', '08', '08', 'ALTO PICHIGUA');
INSERT INTO `ubigeo_inei` VALUES (838, '08', '09', '00', 'LA CONVENCION');
INSERT INTO `ubigeo_inei` VALUES (839, '08', '09', '01', 'SANTA ANA');
INSERT INTO `ubigeo_inei` VALUES (840, '08', '09', '02', 'ECHARATE');
INSERT INTO `ubigeo_inei` VALUES (841, '08', '09', '03', 'HUAYOPATA');
INSERT INTO `ubigeo_inei` VALUES (842, '08', '09', '04', 'MARANURA');
INSERT INTO `ubigeo_inei` VALUES (843, '08', '09', '05', 'OCOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (844, '08', '09', '06', 'QUELLOUNO');
INSERT INTO `ubigeo_inei` VALUES (845, '08', '09', '07', 'KIMBIRI');
INSERT INTO `ubigeo_inei` VALUES (846, '08', '09', '08', 'SANTA TERESA');
INSERT INTO `ubigeo_inei` VALUES (847, '08', '09', '09', 'VILCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (848, '08', '09', '10', 'PICHARI');
INSERT INTO `ubigeo_inei` VALUES (849, '08', '09', '11', 'INKAWASI');
INSERT INTO `ubigeo_inei` VALUES (850, '08', '09', '12', 'VILLA VIRGEN');
INSERT INTO `ubigeo_inei` VALUES (851, '08', '10', '00', 'PARURO');
INSERT INTO `ubigeo_inei` VALUES (852, '08', '10', '01', 'PARURO');
INSERT INTO `ubigeo_inei` VALUES (853, '08', '10', '02', 'ACCHA');
INSERT INTO `ubigeo_inei` VALUES (854, '08', '10', '03', 'CCAPI');
INSERT INTO `ubigeo_inei` VALUES (855, '08', '10', '04', 'COLCHA');
INSERT INTO `ubigeo_inei` VALUES (856, '08', '10', '05', 'HUANOQUITE');
INSERT INTO `ubigeo_inei` VALUES (857, '08', '10', '06', 'OMACHA');
INSERT INTO `ubigeo_inei` VALUES (858, '08', '10', '07', 'PACCARITAMBO');
INSERT INTO `ubigeo_inei` VALUES (859, '08', '10', '08', 'PILLPINTO');
INSERT INTO `ubigeo_inei` VALUES (860, '08', '10', '09', 'YAURISQUE');
INSERT INTO `ubigeo_inei` VALUES (861, '08', '11', '00', 'PAUCARTAMBO');
INSERT INTO `ubigeo_inei` VALUES (862, '08', '11', '01', 'PAUCARTAMBO');
INSERT INTO `ubigeo_inei` VALUES (863, '08', '11', '02', 'CAICAY');
INSERT INTO `ubigeo_inei` VALUES (864, '08', '11', '03', 'CHALLABAMBA');
INSERT INTO `ubigeo_inei` VALUES (865, '08', '11', '04', 'COLQUEPATA');
INSERT INTO `ubigeo_inei` VALUES (866, '08', '11', '05', 'HUANCARANI');
INSERT INTO `ubigeo_inei` VALUES (867, '08', '11', '06', 'KOSÑIPATA');
INSERT INTO `ubigeo_inei` VALUES (868, '08', '12', '00', 'QUISPICANCHI');
INSERT INTO `ubigeo_inei` VALUES (869, '08', '12', '01', 'URCOS');
INSERT INTO `ubigeo_inei` VALUES (870, '08', '12', '02', 'ANDAHUAYLILLAS');
INSERT INTO `ubigeo_inei` VALUES (871, '08', '12', '03', 'CAMANTI');
INSERT INTO `ubigeo_inei` VALUES (872, '08', '12', '04', 'CCARHUAYO');
INSERT INTO `ubigeo_inei` VALUES (873, '08', '12', '05', 'CCATCA');
INSERT INTO `ubigeo_inei` VALUES (874, '08', '12', '06', 'CUSIPATA');
INSERT INTO `ubigeo_inei` VALUES (875, '08', '12', '07', 'HUARO');
INSERT INTO `ubigeo_inei` VALUES (876, '08', '12', '08', 'LUCRE');
INSERT INTO `ubigeo_inei` VALUES (877, '08', '12', '09', 'MARCAPATA');
INSERT INTO `ubigeo_inei` VALUES (878, '08', '12', '10', 'OCONGATE');
INSERT INTO `ubigeo_inei` VALUES (879, '08', '12', '11', 'OROPESA');
INSERT INTO `ubigeo_inei` VALUES (880, '08', '12', '12', 'QUIQUIJANA');
INSERT INTO `ubigeo_inei` VALUES (881, '08', '13', '00', 'URUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (882, '08', '13', '01', 'URUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (883, '08', '13', '02', 'CHINCHERO');
INSERT INTO `ubigeo_inei` VALUES (884, '08', '13', '03', 'HUAYLLABAMBA');
INSERT INTO `ubigeo_inei` VALUES (885, '08', '13', '04', 'MACHUPICCHU');
INSERT INTO `ubigeo_inei` VALUES (886, '08', '13', '05', 'MARAS');
INSERT INTO `ubigeo_inei` VALUES (887, '08', '13', '06', 'OLLANTAYTAMBO');
INSERT INTO `ubigeo_inei` VALUES (888, '08', '13', '07', 'YUCAY');
INSERT INTO `ubigeo_inei` VALUES (889, '09', '00', '00', 'HUANCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (890, '09', '01', '00', 'HUANCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (891, '09', '01', '01', 'HUANCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (892, '09', '01', '02', 'ACOBAMBILLA');
INSERT INTO `ubigeo_inei` VALUES (893, '09', '01', '03', 'ACORIA');
INSERT INTO `ubigeo_inei` VALUES (894, '09', '01', '04', 'CONAYCA');
INSERT INTO `ubigeo_inei` VALUES (895, '09', '01', '05', 'CUENCA');
INSERT INTO `ubigeo_inei` VALUES (896, '09', '01', '06', 'HUACHOCOLPA');
INSERT INTO `ubigeo_inei` VALUES (897, '09', '01', '07', 'HUAYLLAHUARA');
INSERT INTO `ubigeo_inei` VALUES (898, '09', '01', '08', 'IZCUCHACA');
INSERT INTO `ubigeo_inei` VALUES (899, '09', '01', '09', 'LARIA');
INSERT INTO `ubigeo_inei` VALUES (900, '09', '01', '10', 'MANTA');
INSERT INTO `ubigeo_inei` VALUES (901, '09', '01', '11', 'MARISCAL CACERES');
INSERT INTO `ubigeo_inei` VALUES (902, '09', '01', '12', 'MOYA');
INSERT INTO `ubigeo_inei` VALUES (903, '09', '01', '13', 'NUEVO OCCORO');
INSERT INTO `ubigeo_inei` VALUES (904, '09', '01', '14', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (905, '09', '01', '15', 'PILCHACA');
INSERT INTO `ubigeo_inei` VALUES (906, '09', '01', '16', 'VILCA');
INSERT INTO `ubigeo_inei` VALUES (907, '09', '01', '17', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (908, '09', '01', '18', 'ASCENSIÓN');
INSERT INTO `ubigeo_inei` VALUES (909, '09', '01', '19', 'HUANDO');
INSERT INTO `ubigeo_inei` VALUES (910, '09', '02', '00', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (911, '09', '02', '01', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (912, '09', '02', '02', 'ANDABAMBA');
INSERT INTO `ubigeo_inei` VALUES (913, '09', '02', '03', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (914, '09', '02', '04', 'CAJA');
INSERT INTO `ubigeo_inei` VALUES (915, '09', '02', '05', 'MARCAS');
INSERT INTO `ubigeo_inei` VALUES (916, '09', '02', '06', 'PAUCARA');
INSERT INTO `ubigeo_inei` VALUES (917, '09', '02', '07', 'POMACOCHA');
INSERT INTO `ubigeo_inei` VALUES (918, '09', '02', '08', 'ROSARIO');
INSERT INTO `ubigeo_inei` VALUES (919, '09', '03', '00', 'ANGARAES');
INSERT INTO `ubigeo_inei` VALUES (920, '09', '03', '01', 'LIRCAY');
INSERT INTO `ubigeo_inei` VALUES (921, '09', '03', '02', 'ANCHONGA');
INSERT INTO `ubigeo_inei` VALUES (922, '09', '03', '03', 'CALLANMARCA');
INSERT INTO `ubigeo_inei` VALUES (923, '09', '03', '04', 'CCOCHACCASA');
INSERT INTO `ubigeo_inei` VALUES (924, '09', '03', '05', 'CHINCHO');
INSERT INTO `ubigeo_inei` VALUES (925, '09', '03', '06', 'CONGALLA');
INSERT INTO `ubigeo_inei` VALUES (926, '09', '03', '07', 'HUANCA-HUANCA');
INSERT INTO `ubigeo_inei` VALUES (927, '09', '03', '08', 'HUAYLLAY GRANDE');
INSERT INTO `ubigeo_inei` VALUES (928, '09', '03', '09', 'JULCAMARCA');
INSERT INTO `ubigeo_inei` VALUES (929, '09', '03', '10', 'SAN ANTONIO DE ANTAPARCO');
INSERT INTO `ubigeo_inei` VALUES (930, '09', '03', '11', 'SANTO TOMAS DE PATA');
INSERT INTO `ubigeo_inei` VALUES (931, '09', '03', '12', 'SECCLLA');
INSERT INTO `ubigeo_inei` VALUES (932, '09', '04', '00', 'CASTROVIRREYNA');
INSERT INTO `ubigeo_inei` VALUES (933, '09', '04', '01', 'CASTROVIRREYNA');
INSERT INTO `ubigeo_inei` VALUES (934, '09', '04', '02', 'ARMA');
INSERT INTO `ubigeo_inei` VALUES (935, '09', '04', '03', 'AURAHUA');
INSERT INTO `ubigeo_inei` VALUES (936, '09', '04', '04', 'CAPILLAS');
INSERT INTO `ubigeo_inei` VALUES (937, '09', '04', '05', 'CHUPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (938, '09', '04', '06', 'COCAS');
INSERT INTO `ubigeo_inei` VALUES (939, '09', '04', '07', 'HUACHOS');
INSERT INTO `ubigeo_inei` VALUES (940, '09', '04', '08', 'HUAMATAMBO');
INSERT INTO `ubigeo_inei` VALUES (941, '09', '04', '09', 'MOLLEPAMPA');
INSERT INTO `ubigeo_inei` VALUES (942, '09', '04', '10', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (943, '09', '04', '11', 'SANTA ANA');
INSERT INTO `ubigeo_inei` VALUES (944, '09', '04', '12', 'TANTARA');
INSERT INTO `ubigeo_inei` VALUES (945, '09', '04', '13', 'TICRAPO');
INSERT INTO `ubigeo_inei` VALUES (946, '09', '05', '00', 'CHURCAMPA');
INSERT INTO `ubigeo_inei` VALUES (947, '09', '05', '01', 'CHURCAMPA');
INSERT INTO `ubigeo_inei` VALUES (948, '09', '05', '02', 'ANCO');
INSERT INTO `ubigeo_inei` VALUES (949, '09', '05', '03', 'CHINCHIHUASI');
INSERT INTO `ubigeo_inei` VALUES (950, '09', '05', '04', 'EL CARMEN');
INSERT INTO `ubigeo_inei` VALUES (951, '09', '05', '05', 'LA MERCED');
INSERT INTO `ubigeo_inei` VALUES (952, '09', '05', '06', 'LOCROJA');
INSERT INTO `ubigeo_inei` VALUES (953, '09', '05', '07', 'PAUCARBAMBA');
INSERT INTO `ubigeo_inei` VALUES (954, '09', '05', '08', 'SAN MIGUEL DE MAYOCC');
INSERT INTO `ubigeo_inei` VALUES (955, '09', '05', '09', 'SAN PEDRO DE CORIS');
INSERT INTO `ubigeo_inei` VALUES (956, '09', '05', '10', 'PACHAMARCA');
INSERT INTO `ubigeo_inei` VALUES (957, '09', '05', '11', 'COSME');
INSERT INTO `ubigeo_inei` VALUES (958, '09', '06', '00', 'HUAYTARA');
INSERT INTO `ubigeo_inei` VALUES (959, '09', '06', '01', 'HUAYTARA');
INSERT INTO `ubigeo_inei` VALUES (960, '09', '06', '02', 'AYAVI');
INSERT INTO `ubigeo_inei` VALUES (961, '09', '06', '03', 'CORDOVA');
INSERT INTO `ubigeo_inei` VALUES (962, '09', '06', '04', 'HUAYACUNDO ARMA');
INSERT INTO `ubigeo_inei` VALUES (963, '09', '06', '05', 'LARAMARCA');
INSERT INTO `ubigeo_inei` VALUES (964, '09', '06', '06', 'OCOYO');
INSERT INTO `ubigeo_inei` VALUES (965, '09', '06', '07', 'PILPICHACA');
INSERT INTO `ubigeo_inei` VALUES (966, '09', '06', '08', 'QUERCO');
INSERT INTO `ubigeo_inei` VALUES (967, '09', '06', '09', 'QUITO-ARMA');
INSERT INTO `ubigeo_inei` VALUES (968, '09', '06', '10', 'SAN ANTONIO DE CUSICANCHA');
INSERT INTO `ubigeo_inei` VALUES (969, '09', '06', '11', 'SAN FRANCISCO DE SANGAYAICO');
INSERT INTO `ubigeo_inei` VALUES (970, '09', '06', '12', 'SAN ISIDRO');
INSERT INTO `ubigeo_inei` VALUES (971, '09', '06', '13', 'SANTIAGO DE CHOCORVOS');
INSERT INTO `ubigeo_inei` VALUES (972, '09', '06', '14', 'SANTIAGO DE QUIRAHUARA');
INSERT INTO `ubigeo_inei` VALUES (973, '09', '06', '15', 'SANTO DOMINGO DE CAPILLAS');
INSERT INTO `ubigeo_inei` VALUES (974, '09', '06', '16', 'TAMBO');
INSERT INTO `ubigeo_inei` VALUES (975, '09', '07', '00', 'TAYACAJA');
INSERT INTO `ubigeo_inei` VALUES (976, '09', '07', '01', 'PAMPAS');
INSERT INTO `ubigeo_inei` VALUES (977, '09', '07', '02', 'ACOSTAMBO');
INSERT INTO `ubigeo_inei` VALUES (978, '09', '07', '03', 'ACRAQUIA');
INSERT INTO `ubigeo_inei` VALUES (979, '09', '07', '04', 'AHUAYCHA');
INSERT INTO `ubigeo_inei` VALUES (980, '09', '07', '05', 'COLCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (981, '09', '07', '06', 'DANIEL HERNANDEZ');
INSERT INTO `ubigeo_inei` VALUES (982, '09', '07', '07', 'HUACHOCOLPA');
INSERT INTO `ubigeo_inei` VALUES (983, '09', '07', '09', 'HUARIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (984, '09', '07', '10', 'ÑAHUIMPUQUIO');
INSERT INTO `ubigeo_inei` VALUES (985, '09', '07', '11', 'PAZOS');
INSERT INTO `ubigeo_inei` VALUES (986, '09', '07', '13', 'QUISHUAR');
INSERT INTO `ubigeo_inei` VALUES (987, '09', '07', '14', 'SALCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (988, '09', '07', '15', 'SALCAHUASI');
INSERT INTO `ubigeo_inei` VALUES (989, '09', '07', '16', 'SAN MARCOS DE ROCCHAC');
INSERT INTO `ubigeo_inei` VALUES (990, '09', '07', '17', 'SURCUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (991, '09', '07', '18', 'TINTAY PUNCU');
INSERT INTO `ubigeo_inei` VALUES (992, '10', '00', '00', 'HUANUCO');
INSERT INTO `ubigeo_inei` VALUES (993, '10', '01', '00', 'HUANUCO');
INSERT INTO `ubigeo_inei` VALUES (994, '10', '01', '01', 'HUANUCO');
INSERT INTO `ubigeo_inei` VALUES (995, '10', '01', '02', 'AMARILIS');
INSERT INTO `ubigeo_inei` VALUES (996, '10', '01', '03', 'CHINCHAO');
INSERT INTO `ubigeo_inei` VALUES (997, '10', '01', '04', 'CHURUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (998, '10', '01', '05', 'MARGOS');
INSERT INTO `ubigeo_inei` VALUES (999, '10', '01', '06', 'QUISQUI');
INSERT INTO `ubigeo_inei` VALUES (1000, '10', '01', '07', 'SAN FRANCISCO DE CAYRAN');
INSERT INTO `ubigeo_inei` VALUES (1001, '10', '01', '08', 'SAN PEDRO DE CHAULAN');
INSERT INTO `ubigeo_inei` VALUES (1002, '10', '01', '09', 'SANTA MARIA DEL VALLE');
INSERT INTO `ubigeo_inei` VALUES (1003, '10', '01', '10', 'YARUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1004, '10', '01', '11', 'PILLCO MARCA');
INSERT INTO `ubigeo_inei` VALUES (1005, '10', '01', '12', 'YACUS');
INSERT INTO `ubigeo_inei` VALUES (1006, '10', '02', '00', 'AMBO');
INSERT INTO `ubigeo_inei` VALUES (1007, '10', '02', '01', 'AMBO');
INSERT INTO `ubigeo_inei` VALUES (1008, '10', '02', '02', 'CAYNA');
INSERT INTO `ubigeo_inei` VALUES (1009, '10', '02', '03', 'COLPAS');
INSERT INTO `ubigeo_inei` VALUES (1010, '10', '02', '04', 'CONCHAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1011, '10', '02', '05', 'HUACAR');
INSERT INTO `ubigeo_inei` VALUES (1012, '10', '02', '06', 'SAN FRANCISCO');
INSERT INTO `ubigeo_inei` VALUES (1013, '10', '02', '07', 'SAN RAFAEL');
INSERT INTO `ubigeo_inei` VALUES (1014, '10', '02', '08', 'TOMAY KICHWA');
INSERT INTO `ubigeo_inei` VALUES (1015, '10', '03', '00', 'DOS DE MAYO');
INSERT INTO `ubigeo_inei` VALUES (1016, '10', '03', '01', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1017, '10', '03', '07', 'CHUQUIS');
INSERT INTO `ubigeo_inei` VALUES (1018, '10', '03', '11', 'MARIAS');
INSERT INTO `ubigeo_inei` VALUES (1019, '10', '03', '13', 'PACHAS');
INSERT INTO `ubigeo_inei` VALUES (1020, '10', '03', '16', 'QUIVILLA');
INSERT INTO `ubigeo_inei` VALUES (1021, '10', '03', '17', 'RIPAN');
INSERT INTO `ubigeo_inei` VALUES (1022, '10', '03', '21', 'SHUNQUI');
INSERT INTO `ubigeo_inei` VALUES (1023, '10', '03', '22', 'SILLAPATA');
INSERT INTO `ubigeo_inei` VALUES (1024, '10', '03', '23', 'YANAS');
INSERT INTO `ubigeo_inei` VALUES (1025, '10', '04', '00', 'HUACAYBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1026, '10', '04', '01', 'HUACAYBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1027, '10', '04', '02', 'CANCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1028, '10', '04', '03', 'COCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1029, '10', '04', '04', 'PINRA');
INSERT INTO `ubigeo_inei` VALUES (1030, '10', '05', '00', 'HUAMALIES');
INSERT INTO `ubigeo_inei` VALUES (1031, '10', '05', '01', 'LLATA');
INSERT INTO `ubigeo_inei` VALUES (1032, '10', '05', '02', 'ARANCAY');
INSERT INTO `ubigeo_inei` VALUES (1033, '10', '05', '03', 'CHAVIN DE PARIARCA');
INSERT INTO `ubigeo_inei` VALUES (1034, '10', '05', '04', 'JACAS GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1035, '10', '05', '05', 'JIRCAN');
INSERT INTO `ubigeo_inei` VALUES (1036, '10', '05', '06', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1037, '10', '05', '07', 'MONZON');
INSERT INTO `ubigeo_inei` VALUES (1038, '10', '05', '08', 'PUNCHAO');
INSERT INTO `ubigeo_inei` VALUES (1039, '10', '05', '09', 'PUÑOS');
INSERT INTO `ubigeo_inei` VALUES (1040, '10', '05', '10', 'SINGA');
INSERT INTO `ubigeo_inei` VALUES (1041, '10', '05', '11', 'TANTAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1042, '10', '06', '00', 'LEONCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (1043, '10', '06', '01', 'RUPA-RUPA');
INSERT INTO `ubigeo_inei` VALUES (1044, '10', '06', '02', 'DANIEL ALOMIAS ROBLES');
INSERT INTO `ubigeo_inei` VALUES (1045, '10', '06', '03', 'HERMILIO VALDIZAN');
INSERT INTO `ubigeo_inei` VALUES (1046, '10', '06', '04', 'JOSE CRESPO Y CASTILLO');
INSERT INTO `ubigeo_inei` VALUES (1047, '10', '06', '05', 'LUYANDO');
INSERT INTO `ubigeo_inei` VALUES (1048, '10', '06', '06', 'MARIANO DAMASO BERAUN');
INSERT INTO `ubigeo_inei` VALUES (1049, '10', '07', '00', 'MARAÑON');
INSERT INTO `ubigeo_inei` VALUES (1050, '10', '07', '01', 'HUACRACHUCO');
INSERT INTO `ubigeo_inei` VALUES (1051, '10', '07', '02', 'CHOLON');
INSERT INTO `ubigeo_inei` VALUES (1052, '10', '07', '03', 'SAN BUENAVENTURA');
INSERT INTO `ubigeo_inei` VALUES (1053, '10', '08', '00', 'PACHITEA');
INSERT INTO `ubigeo_inei` VALUES (1054, '10', '08', '01', 'PANAO');
INSERT INTO `ubigeo_inei` VALUES (1055, '10', '08', '02', 'CHAGLLA');
INSERT INTO `ubigeo_inei` VALUES (1056, '10', '08', '03', 'MOLINO');
INSERT INTO `ubigeo_inei` VALUES (1057, '10', '08', '04', 'UMARI');
INSERT INTO `ubigeo_inei` VALUES (1058, '10', '09', '00', 'PUERTO INCA');
INSERT INTO `ubigeo_inei` VALUES (1059, '10', '09', '01', 'PUERTO INCA');
INSERT INTO `ubigeo_inei` VALUES (1060, '10', '09', '02', 'CODO DEL POZUZO');
INSERT INTO `ubigeo_inei` VALUES (1061, '10', '09', '03', 'HONORIA');
INSERT INTO `ubigeo_inei` VALUES (1062, '10', '09', '04', 'TOURNAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1063, '10', '09', '05', 'YUYAPICHIS');
INSERT INTO `ubigeo_inei` VALUES (1064, '10', '10', '00', 'LAURICOCHA');
INSERT INTO `ubigeo_inei` VALUES (1065, '10', '10', '01', 'JESUS');
INSERT INTO `ubigeo_inei` VALUES (1066, '10', '10', '02', 'BAÑOS');
INSERT INTO `ubigeo_inei` VALUES (1067, '10', '10', '03', 'JIVIA');
INSERT INTO `ubigeo_inei` VALUES (1068, '10', '10', '04', 'QUEROPALCA');
INSERT INTO `ubigeo_inei` VALUES (1069, '10', '10', '05', 'RONDOS');
INSERT INTO `ubigeo_inei` VALUES (1070, '10', '10', '06', 'SAN FRANCISCO DE ASIS');
INSERT INTO `ubigeo_inei` VALUES (1071, '10', '10', '07', 'SAN MIGUEL DE CAURI');
INSERT INTO `ubigeo_inei` VALUES (1072, '10', '11', '00', 'YAROWILCA');
INSERT INTO `ubigeo_inei` VALUES (1073, '10', '11', '01', 'CHAVINILLO');
INSERT INTO `ubigeo_inei` VALUES (1074, '10', '11', '02', 'CAHUAC');
INSERT INTO `ubigeo_inei` VALUES (1075, '10', '11', '03', 'CHACABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1076, '10', '11', '04', 'CHUPAN');
INSERT INTO `ubigeo_inei` VALUES (1077, '10', '11', '05', 'JACAS CHICO');
INSERT INTO `ubigeo_inei` VALUES (1078, '10', '11', '06', 'OBAS');
INSERT INTO `ubigeo_inei` VALUES (1079, '10', '11', '07', 'PAMPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1080, '10', '11', '08', 'CHORAS');
INSERT INTO `ubigeo_inei` VALUES (1081, '11', '00', '00', 'ICA');
INSERT INTO `ubigeo_inei` VALUES (1082, '11', '01', '00', 'ICA');
INSERT INTO `ubigeo_inei` VALUES (1083, '11', '01', '01', 'ICA');
INSERT INTO `ubigeo_inei` VALUES (1084, '11', '01', '02', 'LA TINGUIÑA');
INSERT INTO `ubigeo_inei` VALUES (1085, '11', '01', '03', 'LOS AQUIJES');
INSERT INTO `ubigeo_inei` VALUES (1086, '11', '01', '04', 'OCUCAJE');
INSERT INTO `ubigeo_inei` VALUES (1087, '11', '01', '05', 'PACHACUTEC');
INSERT INTO `ubigeo_inei` VALUES (1088, '11', '01', '06', 'PARCONA');
INSERT INTO `ubigeo_inei` VALUES (1089, '11', '01', '07', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1090, '11', '01', '08', 'SALAS');
INSERT INTO `ubigeo_inei` VALUES (1091, '11', '01', '09', 'SAN JOSE DE LOS MOLINOS');
INSERT INTO `ubigeo_inei` VALUES (1092, '11', '01', '10', 'SAN JUAN BAUTISTA');
INSERT INTO `ubigeo_inei` VALUES (1093, '11', '01', '11', 'SANTIAGO');
INSERT INTO `ubigeo_inei` VALUES (1094, '11', '01', '12', 'SUBTANJALLA');
INSERT INTO `ubigeo_inei` VALUES (1095, '11', '01', '13', 'TATE');
INSERT INTO `ubigeo_inei` VALUES (1096, '11', '01', '14', 'YAUCA DEL ROSARIO');
INSERT INTO `ubigeo_inei` VALUES (1097, '11', '02', '00', 'CHINCHA');
INSERT INTO `ubigeo_inei` VALUES (1098, '11', '02', '01', 'CHINCHA ALTA');
INSERT INTO `ubigeo_inei` VALUES (1099, '11', '02', '02', 'ALTO LARAN');
INSERT INTO `ubigeo_inei` VALUES (1100, '11', '02', '03', 'CHAVIN');
INSERT INTO `ubigeo_inei` VALUES (1101, '11', '02', '04', 'CHINCHA BAJA');
INSERT INTO `ubigeo_inei` VALUES (1102, '11', '02', '05', 'EL CARMEN');
INSERT INTO `ubigeo_inei` VALUES (1103, '11', '02', '06', 'GROCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (1104, '11', '02', '07', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1105, '11', '02', '08', 'SAN JUAN DE YANAC');
INSERT INTO `ubigeo_inei` VALUES (1106, '11', '02', '09', 'SAN PEDRO DE HUACARPANA');
INSERT INTO `ubigeo_inei` VALUES (1107, '11', '02', '10', 'SUNAMPE');
INSERT INTO `ubigeo_inei` VALUES (1108, '11', '02', '11', 'TAMBO DE MORA');
INSERT INTO `ubigeo_inei` VALUES (1109, '11', '03', '00', 'NAZCA');
INSERT INTO `ubigeo_inei` VALUES (1110, '11', '03', '01', 'NAZCA');
INSERT INTO `ubigeo_inei` VALUES (1111, '11', '03', '02', 'CHANGUILLO');
INSERT INTO `ubigeo_inei` VALUES (1112, '11', '03', '03', 'EL INGENIO');
INSERT INTO `ubigeo_inei` VALUES (1113, '11', '03', '04', 'MARCONA');
INSERT INTO `ubigeo_inei` VALUES (1114, '11', '03', '05', 'VISTA ALEGRE');
INSERT INTO `ubigeo_inei` VALUES (1115, '11', '04', '00', 'PALPA');
INSERT INTO `ubigeo_inei` VALUES (1116, '11', '04', '01', 'PALPA');
INSERT INTO `ubigeo_inei` VALUES (1117, '11', '04', '02', 'LLIPATA');
INSERT INTO `ubigeo_inei` VALUES (1118, '11', '04', '03', 'RIO GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1119, '11', '04', '04', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (1120, '11', '04', '05', 'TIBILLO');
INSERT INTO `ubigeo_inei` VALUES (1121, '11', '05', '00', 'PISCO');
INSERT INTO `ubigeo_inei` VALUES (1122, '11', '05', '01', 'PISCO');
INSERT INTO `ubigeo_inei` VALUES (1123, '11', '05', '02', 'HUANCANO');
INSERT INTO `ubigeo_inei` VALUES (1124, '11', '05', '03', 'HUMAY');
INSERT INTO `ubigeo_inei` VALUES (1125, '11', '05', '04', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (1126, '11', '05', '05', 'PARACAS');
INSERT INTO `ubigeo_inei` VALUES (1127, '11', '05', '06', 'SAN ANDRES');
INSERT INTO `ubigeo_inei` VALUES (1128, '11', '05', '07', 'SAN CLEMENTE');
INSERT INTO `ubigeo_inei` VALUES (1129, '11', '05', '08', 'TUPAC AMARU INCA');
INSERT INTO `ubigeo_inei` VALUES (1130, '12', '00', '00', 'JUNIN');
INSERT INTO `ubigeo_inei` VALUES (1131, '12', '01', '00', 'HUANCAYO');
INSERT INTO `ubigeo_inei` VALUES (1132, '12', '01', '01', 'HUANCAYO');
INSERT INTO `ubigeo_inei` VALUES (1133, '12', '01', '04', 'CARHUACALLANGA');
INSERT INTO `ubigeo_inei` VALUES (1134, '12', '01', '05', 'CHACAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1135, '12', '01', '06', 'CHICCHE');
INSERT INTO `ubigeo_inei` VALUES (1136, '12', '01', '07', 'CHILCA');
INSERT INTO `ubigeo_inei` VALUES (1137, '12', '01', '08', 'CHONGOS ALTO');
INSERT INTO `ubigeo_inei` VALUES (1138, '12', '01', '11', 'CHUPURO');
INSERT INTO `ubigeo_inei` VALUES (1139, '12', '01', '12', 'COLCA');
INSERT INTO `ubigeo_inei` VALUES (1140, '12', '01', '13', 'CULLHUAS');
INSERT INTO `ubigeo_inei` VALUES (1141, '12', '01', '14', 'EL TAMBO');
INSERT INTO `ubigeo_inei` VALUES (1142, '12', '01', '16', 'HUACRAPUQUIO');
INSERT INTO `ubigeo_inei` VALUES (1143, '12', '01', '17', 'HUALHUAS');
INSERT INTO `ubigeo_inei` VALUES (1144, '12', '01', '19', 'HUANCAN');
INSERT INTO `ubigeo_inei` VALUES (1145, '12', '01', '20', 'HUASICANCHA');
INSERT INTO `ubigeo_inei` VALUES (1146, '12', '01', '21', 'HUAYUCACHI');
INSERT INTO `ubigeo_inei` VALUES (1147, '12', '01', '22', 'INGENIO');
INSERT INTO `ubigeo_inei` VALUES (1148, '12', '01', '24', 'PARIAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1149, '12', '01', '25', 'PILCOMAYO');
INSERT INTO `ubigeo_inei` VALUES (1150, '12', '01', '26', 'PUCARA');
INSERT INTO `ubigeo_inei` VALUES (1151, '12', '01', '27', 'QUICHUAY');
INSERT INTO `ubigeo_inei` VALUES (1152, '12', '01', '28', 'QUILCAS');
INSERT INTO `ubigeo_inei` VALUES (1153, '12', '01', '29', 'SAN AGUSTIN');
INSERT INTO `ubigeo_inei` VALUES (1154, '12', '01', '30', 'SAN JERONIMO DE TUNAN');
INSERT INTO `ubigeo_inei` VALUES (1155, '12', '01', '32', 'SAÑO');
INSERT INTO `ubigeo_inei` VALUES (1156, '12', '01', '33', 'SAPALLANGA');
INSERT INTO `ubigeo_inei` VALUES (1157, '12', '01', '34', 'SICAYA');
INSERT INTO `ubigeo_inei` VALUES (1158, '12', '01', '35', 'SANTO DOMINGO DE ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1159, '12', '01', '36', 'VIQUES');
INSERT INTO `ubigeo_inei` VALUES (1160, '12', '02', '00', 'CONCEPCION');
INSERT INTO `ubigeo_inei` VALUES (1161, '12', '02', '01', 'CONCEPCION');
INSERT INTO `ubigeo_inei` VALUES (1162, '12', '02', '02', 'ACO');
INSERT INTO `ubigeo_inei` VALUES (1163, '12', '02', '03', 'ANDAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1164, '12', '02', '04', 'CHAMBARA');
INSERT INTO `ubigeo_inei` VALUES (1165, '12', '02', '05', 'COCHAS');
INSERT INTO `ubigeo_inei` VALUES (1166, '12', '02', '06', 'COMAS');
INSERT INTO `ubigeo_inei` VALUES (1167, '12', '02', '07', 'HEROINAS TOLEDO');
INSERT INTO `ubigeo_inei` VALUES (1168, '12', '02', '08', 'MANZANARES');
INSERT INTO `ubigeo_inei` VALUES (1169, '12', '02', '09', 'MARISCAL CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1170, '12', '02', '10', 'MATAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1171, '12', '02', '11', 'MITO');
INSERT INTO `ubigeo_inei` VALUES (1172, '12', '02', '12', 'NUEVE DE JULIO');
INSERT INTO `ubigeo_inei` VALUES (1173, '12', '02', '13', 'ORCOTUNA');
INSERT INTO `ubigeo_inei` VALUES (1174, '12', '02', '14', 'SAN JOSE DE QUERO');
INSERT INTO `ubigeo_inei` VALUES (1175, '12', '02', '15', 'SANTA ROSA DE OCOPA');
INSERT INTO `ubigeo_inei` VALUES (1176, '12', '03', '00', 'CHANCHAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1177, '12', '03', '01', 'CHANCHAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1178, '12', '03', '02', 'PERENE');
INSERT INTO `ubigeo_inei` VALUES (1179, '12', '03', '03', 'PICHANAQUI');
INSERT INTO `ubigeo_inei` VALUES (1180, '12', '03', '04', 'SAN LUIS DE SHUARO');
INSERT INTO `ubigeo_inei` VALUES (1181, '12', '03', '05', 'SAN RAMON');
INSERT INTO `ubigeo_inei` VALUES (1182, '12', '03', '06', 'VITOC');
INSERT INTO `ubigeo_inei` VALUES (1183, '12', '04', '00', 'JAUJA');
INSERT INTO `ubigeo_inei` VALUES (1184, '12', '04', '01', 'JAUJA');
INSERT INTO `ubigeo_inei` VALUES (1185, '12', '04', '02', 'ACOLLA');
INSERT INTO `ubigeo_inei` VALUES (1186, '12', '04', '03', 'APATA');
INSERT INTO `ubigeo_inei` VALUES (1187, '12', '04', '04', 'ATAURA');
INSERT INTO `ubigeo_inei` VALUES (1188, '12', '04', '05', 'CANCHAYLLO');
INSERT INTO `ubigeo_inei` VALUES (1189, '12', '04', '06', 'CURICACA');
INSERT INTO `ubigeo_inei` VALUES (1190, '12', '04', '07', 'EL MANTARO');
INSERT INTO `ubigeo_inei` VALUES (1191, '12', '04', '08', 'HUAMALI');
INSERT INTO `ubigeo_inei` VALUES (1192, '12', '04', '09', 'HUARIPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1193, '12', '04', '10', 'HUERTAS');
INSERT INTO `ubigeo_inei` VALUES (1194, '12', '04', '11', 'JANJAILLO');
INSERT INTO `ubigeo_inei` VALUES (1195, '12', '04', '12', 'JULCAN');
INSERT INTO `ubigeo_inei` VALUES (1196, '12', '04', '13', 'LEONOR ORDOÑEZ');
INSERT INTO `ubigeo_inei` VALUES (1197, '12', '04', '14', 'LLOCLLAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1198, '12', '04', '15', 'MARCO');
INSERT INTO `ubigeo_inei` VALUES (1199, '12', '04', '16', 'MASMA');
INSERT INTO `ubigeo_inei` VALUES (1200, '12', '04', '17', 'MASMA CHICCHE');
INSERT INTO `ubigeo_inei` VALUES (1201, '12', '04', '18', 'MOLINOS');
INSERT INTO `ubigeo_inei` VALUES (1202, '12', '04', '19', 'MONOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1203, '12', '04', '20', 'MUQUI');
INSERT INTO `ubigeo_inei` VALUES (1204, '12', '04', '21', 'MUQUIYAUYO');
INSERT INTO `ubigeo_inei` VALUES (1205, '12', '04', '22', 'PACA');
INSERT INTO `ubigeo_inei` VALUES (1206, '12', '04', '23', 'PACCHA');
INSERT INTO `ubigeo_inei` VALUES (1207, '12', '04', '24', 'PANCAN');
INSERT INTO `ubigeo_inei` VALUES (1208, '12', '04', '25', 'PARCO');
INSERT INTO `ubigeo_inei` VALUES (1209, '12', '04', '26', 'POMACANCHA');
INSERT INTO `ubigeo_inei` VALUES (1210, '12', '04', '27', 'RICRAN');
INSERT INTO `ubigeo_inei` VALUES (1211, '12', '04', '28', 'SAN LORENZO');
INSERT INTO `ubigeo_inei` VALUES (1212, '12', '04', '29', 'SAN PEDRO DE CHUNAN');
INSERT INTO `ubigeo_inei` VALUES (1213, '12', '04', '30', 'SAUSA');
INSERT INTO `ubigeo_inei` VALUES (1214, '12', '04', '31', 'SINCOS');
INSERT INTO `ubigeo_inei` VALUES (1215, '12', '04', '32', 'TUNAN MARCA');
INSERT INTO `ubigeo_inei` VALUES (1216, '12', '04', '33', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (1217, '12', '04', '34', 'YAUYOS');
INSERT INTO `ubigeo_inei` VALUES (1218, '12', '05', '00', 'JUNIN');
INSERT INTO `ubigeo_inei` VALUES (1219, '12', '05', '01', 'JUNIN');
INSERT INTO `ubigeo_inei` VALUES (1220, '12', '05', '02', 'CARHUAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1221, '12', '05', '03', 'ONDORES');
INSERT INTO `ubigeo_inei` VALUES (1222, '12', '05', '04', 'ULCUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1223, '12', '06', '00', 'SATIPO');
INSERT INTO `ubigeo_inei` VALUES (1224, '12', '06', '01', 'SATIPO');
INSERT INTO `ubigeo_inei` VALUES (1225, '12', '06', '02', 'COVIRIALI');
INSERT INTO `ubigeo_inei` VALUES (1226, '12', '06', '03', 'LLAYLLA');
INSERT INTO `ubigeo_inei` VALUES (1227, '12', '06', '04', 'MAZAMARI');
INSERT INTO `ubigeo_inei` VALUES (1228, '12', '06', '05', 'PAMPA HERMOSA');
INSERT INTO `ubigeo_inei` VALUES (1229, '12', '06', '06', 'PANGOA');
INSERT INTO `ubigeo_inei` VALUES (1230, '12', '06', '07', 'RIO NEGRO');
INSERT INTO `ubigeo_inei` VALUES (1231, '12', '06', '08', 'RIO TAMBO');
INSERT INTO `ubigeo_inei` VALUES (1232, '12', '06', '99', 'MAZAMARI-PANGOA');
INSERT INTO `ubigeo_inei` VALUES (1233, '12', '07', '00', 'TARMA');
INSERT INTO `ubigeo_inei` VALUES (1234, '12', '07', '01', 'TARMA');
INSERT INTO `ubigeo_inei` VALUES (1235, '12', '07', '02', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1236, '12', '07', '03', 'HUARICOLCA');
INSERT INTO `ubigeo_inei` VALUES (1237, '12', '07', '04', 'HUASAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1238, '12', '07', '05', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1239, '12', '07', '06', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (1240, '12', '07', '07', 'PALCAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1241, '12', '07', '08', 'SAN PEDRO DE CAJAS');
INSERT INTO `ubigeo_inei` VALUES (1242, '12', '07', '09', 'TAPO');
INSERT INTO `ubigeo_inei` VALUES (1243, '12', '08', '00', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (1244, '12', '08', '01', 'LA OROYA');
INSERT INTO `ubigeo_inei` VALUES (1245, '12', '08', '02', 'CHACAPALPA');
INSERT INTO `ubigeo_inei` VALUES (1246, '12', '08', '03', 'HUAY-HUAY');
INSERT INTO `ubigeo_inei` VALUES (1247, '12', '08', '04', 'MARCAPOMACOCHA');
INSERT INTO `ubigeo_inei` VALUES (1248, '12', '08', '05', 'MOROCOCHA');
INSERT INTO `ubigeo_inei` VALUES (1249, '12', '08', '06', 'PACCHA');
INSERT INTO `ubigeo_inei` VALUES (1250, '12', '08', '07', 'SANTA BARBARA DE CARHUACAYAN');
INSERT INTO `ubigeo_inei` VALUES (1251, '12', '08', '08', 'SANTA ROSA DE SACCO');
INSERT INTO `ubigeo_inei` VALUES (1252, '12', '08', '09', 'SUITUCANCHA');
INSERT INTO `ubigeo_inei` VALUES (1253, '12', '08', '10', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (1254, '12', '09', '00', 'CHUPACA');
INSERT INTO `ubigeo_inei` VALUES (1255, '12', '09', '01', 'CHUPACA');
INSERT INTO `ubigeo_inei` VALUES (1256, '12', '09', '02', 'AHUAC');
INSERT INTO `ubigeo_inei` VALUES (1257, '12', '09', '03', 'CHONGOS BAJO');
INSERT INTO `ubigeo_inei` VALUES (1258, '12', '09', '04', 'HUACHAC');
INSERT INTO `ubigeo_inei` VALUES (1259, '12', '09', '05', 'HUAMANCACA CHICO');
INSERT INTO `ubigeo_inei` VALUES (1260, '12', '09', '06', 'SAN JUAN DE ISCOS');
INSERT INTO `ubigeo_inei` VALUES (1261, '12', '09', '07', 'SAN JUAN DE JARPA');
INSERT INTO `ubigeo_inei` VALUES (1262, '12', '09', '08', '3 DE DICIEMBRE');
INSERT INTO `ubigeo_inei` VALUES (1263, '12', '09', '09', 'YANACANCHA');
INSERT INTO `ubigeo_inei` VALUES (1264, '13', '00', '00', 'LA LIBERTAD');
INSERT INTO `ubigeo_inei` VALUES (1265, '13', '01', '00', 'TRUJILLO');
INSERT INTO `ubigeo_inei` VALUES (1266, '13', '01', '01', 'TRUJILLO');
INSERT INTO `ubigeo_inei` VALUES (1267, '13', '01', '02', 'EL PORVENIR');
INSERT INTO `ubigeo_inei` VALUES (1268, '13', '01', '03', 'FLORENCIA DE MORA');
INSERT INTO `ubigeo_inei` VALUES (1269, '13', '01', '04', 'HUANCHACO');
INSERT INTO `ubigeo_inei` VALUES (1270, '13', '01', '05', 'LA ESPERANZA');
INSERT INTO `ubigeo_inei` VALUES (1271, '13', '01', '06', 'LAREDO');
INSERT INTO `ubigeo_inei` VALUES (1272, '13', '01', '07', 'MOCHE');
INSERT INTO `ubigeo_inei` VALUES (1273, '13', '01', '08', 'POROTO');
INSERT INTO `ubigeo_inei` VALUES (1274, '13', '01', '09', 'SALAVERRY');
INSERT INTO `ubigeo_inei` VALUES (1275, '13', '01', '10', 'SIMBAL');
INSERT INTO `ubigeo_inei` VALUES (1276, '13', '01', '11', 'VICTOR LARCO HERRERA');
INSERT INTO `ubigeo_inei` VALUES (1277, '13', '02', '00', 'ASCOPE');
INSERT INTO `ubigeo_inei` VALUES (1278, '13', '02', '01', 'ASCOPE');
INSERT INTO `ubigeo_inei` VALUES (1279, '13', '02', '02', 'CHICAMA');
INSERT INTO `ubigeo_inei` VALUES (1280, '13', '02', '03', 'CHOCOPE');
INSERT INTO `ubigeo_inei` VALUES (1281, '13', '02', '04', 'MAGDALENA DE CAO');
INSERT INTO `ubigeo_inei` VALUES (1282, '13', '02', '05', 'PAIJAN');
INSERT INTO `ubigeo_inei` VALUES (1283, '13', '02', '06', 'RAZURI');
INSERT INTO `ubigeo_inei` VALUES (1284, '13', '02', '07', 'SANTIAGO DE CAO');
INSERT INTO `ubigeo_inei` VALUES (1285, '13', '02', '08', 'CASA GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1286, '13', '03', '00', 'BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (1287, '13', '03', '01', 'BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (1288, '13', '03', '02', 'BAMBAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1289, '13', '03', '03', 'CONDORMARCA');
INSERT INTO `ubigeo_inei` VALUES (1290, '13', '03', '04', 'LONGOTEA');
INSERT INTO `ubigeo_inei` VALUES (1291, '13', '03', '05', 'UCHUMARCA');
INSERT INTO `ubigeo_inei` VALUES (1292, '13', '03', '06', 'UCUNCHA');
INSERT INTO `ubigeo_inei` VALUES (1293, '13', '04', '00', 'CHEPEN');
INSERT INTO `ubigeo_inei` VALUES (1294, '13', '04', '01', 'CHEPEN');
INSERT INTO `ubigeo_inei` VALUES (1295, '13', '04', '02', 'PACANGA');
INSERT INTO `ubigeo_inei` VALUES (1296, '13', '04', '03', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1297, '13', '05', '00', 'JULCAN');
INSERT INTO `ubigeo_inei` VALUES (1298, '13', '05', '01', 'JULCAN');
INSERT INTO `ubigeo_inei` VALUES (1299, '13', '05', '02', 'CALAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1300, '13', '05', '03', 'CARABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1301, '13', '05', '04', 'HUASO');
INSERT INTO `ubigeo_inei` VALUES (1302, '13', '06', '00', 'OTUZCO');
INSERT INTO `ubigeo_inei` VALUES (1303, '13', '06', '01', 'OTUZCO');
INSERT INTO `ubigeo_inei` VALUES (1304, '13', '06', '02', 'AGALLPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1305, '13', '06', '04', 'CHARAT');
INSERT INTO `ubigeo_inei` VALUES (1306, '13', '06', '05', 'HUARANCHAL');
INSERT INTO `ubigeo_inei` VALUES (1307, '13', '06', '06', 'LA CUESTA');
INSERT INTO `ubigeo_inei` VALUES (1308, '13', '06', '08', 'MACHE');
INSERT INTO `ubigeo_inei` VALUES (1309, '13', '06', '10', 'PARANDAY');
INSERT INTO `ubigeo_inei` VALUES (1310, '13', '06', '11', 'SALPO');
INSERT INTO `ubigeo_inei` VALUES (1311, '13', '06', '13', 'SINSICAP');
INSERT INTO `ubigeo_inei` VALUES (1312, '13', '06', '14', 'USQUIL');
INSERT INTO `ubigeo_inei` VALUES (1313, '13', '07', '00', 'PACASMAYO');
INSERT INTO `ubigeo_inei` VALUES (1314, '13', '07', '01', 'SAN PEDRO DE LLOC');
INSERT INTO `ubigeo_inei` VALUES (1315, '13', '07', '02', 'GUADALUPE');
INSERT INTO `ubigeo_inei` VALUES (1316, '13', '07', '03', 'JEQUETEPEQUE');
INSERT INTO `ubigeo_inei` VALUES (1317, '13', '07', '04', 'PACASMAYO');
INSERT INTO `ubigeo_inei` VALUES (1318, '13', '07', '05', 'SAN JOSE');
INSERT INTO `ubigeo_inei` VALUES (1319, '13', '08', '00', 'PATAZ');
INSERT INTO `ubigeo_inei` VALUES (1320, '13', '08', '01', 'TAYABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1321, '13', '08', '02', 'BULDIBUYO');
INSERT INTO `ubigeo_inei` VALUES (1322, '13', '08', '03', 'CHILLIA');
INSERT INTO `ubigeo_inei` VALUES (1323, '13', '08', '04', 'HUANCASPATA');
INSERT INTO `ubigeo_inei` VALUES (1324, '13', '08', '05', 'HUAYLILLAS');
INSERT INTO `ubigeo_inei` VALUES (1325, '13', '08', '06', 'HUAYO');
INSERT INTO `ubigeo_inei` VALUES (1326, '13', '08', '07', 'ONGON');
INSERT INTO `ubigeo_inei` VALUES (1327, '13', '08', '08', 'PARCOY');
INSERT INTO `ubigeo_inei` VALUES (1328, '13', '08', '09', 'PATAZ');
INSERT INTO `ubigeo_inei` VALUES (1329, '13', '08', '10', 'PIAS');
INSERT INTO `ubigeo_inei` VALUES (1330, '13', '08', '11', 'SANTIAGO DE CHALLAS');
INSERT INTO `ubigeo_inei` VALUES (1331, '13', '08', '12', 'TAURIJA');
INSERT INTO `ubigeo_inei` VALUES (1332, '13', '08', '13', 'URPAY');
INSERT INTO `ubigeo_inei` VALUES (1333, '13', '09', '00', 'SANCHEZ CARRION');
INSERT INTO `ubigeo_inei` VALUES (1334, '13', '09', '01', 'HUAMACHUCO');
INSERT INTO `ubigeo_inei` VALUES (1335, '13', '09', '02', 'CHUGAY');
INSERT INTO `ubigeo_inei` VALUES (1336, '13', '09', '03', 'COCHORCO');
INSERT INTO `ubigeo_inei` VALUES (1337, '13', '09', '04', 'CURGOS');
INSERT INTO `ubigeo_inei` VALUES (1338, '13', '09', '05', 'MARCABAL');
INSERT INTO `ubigeo_inei` VALUES (1339, '13', '09', '06', 'SANAGORAN');
INSERT INTO `ubigeo_inei` VALUES (1340, '13', '09', '07', 'SARIN');
INSERT INTO `ubigeo_inei` VALUES (1341, '13', '09', '08', 'SARTIMBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1342, '13', '10', '00', 'SANTIAGO DE CHUCO');
INSERT INTO `ubigeo_inei` VALUES (1343, '13', '10', '01', 'SANTIAGO DE CHUCO');
INSERT INTO `ubigeo_inei` VALUES (1344, '13', '10', '02', 'ANGASMARCA');
INSERT INTO `ubigeo_inei` VALUES (1345, '13', '10', '03', 'CACHICADAN');
INSERT INTO `ubigeo_inei` VALUES (1346, '13', '10', '04', 'MOLLEBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1347, '13', '10', '05', 'MOLLEPATA');
INSERT INTO `ubigeo_inei` VALUES (1348, '13', '10', '06', 'QUIRUVILCA');
INSERT INTO `ubigeo_inei` VALUES (1349, '13', '10', '07', 'SANTA CRUZ DE CHUCA');
INSERT INTO `ubigeo_inei` VALUES (1350, '13', '10', '08', 'SITABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1351, '13', '11', '00', 'GRAN CHIMU');
INSERT INTO `ubigeo_inei` VALUES (1352, '13', '11', '01', 'CASCAS');
INSERT INTO `ubigeo_inei` VALUES (1353, '13', '11', '02', 'LUCMA');
INSERT INTO `ubigeo_inei` VALUES (1354, '13', '11', '03', 'MARMOT');
INSERT INTO `ubigeo_inei` VALUES (1355, '13', '11', '04', 'SAYAPULLO');
INSERT INTO `ubigeo_inei` VALUES (1356, '13', '12', '00', 'VIRU');
INSERT INTO `ubigeo_inei` VALUES (1357, '13', '12', '01', 'VIRU');
INSERT INTO `ubigeo_inei` VALUES (1358, '13', '12', '02', 'CHAO');
INSERT INTO `ubigeo_inei` VALUES (1359, '13', '12', '03', 'GUADALUPITO');
INSERT INTO `ubigeo_inei` VALUES (1360, '14', '00', '00', 'LAMBAYEQUE');
INSERT INTO `ubigeo_inei` VALUES (1361, '14', '01', '00', 'CHICLAYO');
INSERT INTO `ubigeo_inei` VALUES (1362, '14', '01', '01', 'CHICLAYO');
INSERT INTO `ubigeo_inei` VALUES (1363, '14', '01', '02', 'CHONGOYAPE');
INSERT INTO `ubigeo_inei` VALUES (1364, '14', '01', '03', 'ETEN');
INSERT INTO `ubigeo_inei` VALUES (1365, '14', '01', '04', 'ETEN PUERTO');
INSERT INTO `ubigeo_inei` VALUES (1366, '14', '01', '05', 'JOSE LEONARDO ORTIZ');
INSERT INTO `ubigeo_inei` VALUES (1367, '14', '01', '06', 'LA VICTORIA');
INSERT INTO `ubigeo_inei` VALUES (1368, '14', '01', '07', 'LAGUNAS');
INSERT INTO `ubigeo_inei` VALUES (1369, '14', '01', '08', 'MONSEFU');
INSERT INTO `ubigeo_inei` VALUES (1370, '14', '01', '09', 'NUEVA ARICA');
INSERT INTO `ubigeo_inei` VALUES (1371, '14', '01', '10', 'OYOTUN');
INSERT INTO `ubigeo_inei` VALUES (1372, '14', '01', '11', 'PICSI');
INSERT INTO `ubigeo_inei` VALUES (1373, '14', '01', '12', 'PIMENTEL');
INSERT INTO `ubigeo_inei` VALUES (1374, '14', '01', '13', 'REQUE');
INSERT INTO `ubigeo_inei` VALUES (1375, '14', '01', '14', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1376, '14', '01', '15', 'SAÑA');
INSERT INTO `ubigeo_inei` VALUES (1377, '14', '01', '16', 'CAYALTÍ');
INSERT INTO `ubigeo_inei` VALUES (1378, '14', '01', '17', 'PATAPO');
INSERT INTO `ubigeo_inei` VALUES (1379, '14', '01', '18', 'POMALCA');
INSERT INTO `ubigeo_inei` VALUES (1380, '14', '01', '19', 'PUCALÁ');
INSERT INTO `ubigeo_inei` VALUES (1381, '14', '01', '20', 'TUMÁN');
INSERT INTO `ubigeo_inei` VALUES (1382, '14', '02', '00', 'FERREÑAFE');
INSERT INTO `ubigeo_inei` VALUES (1383, '14', '02', '01', 'FERREÑAFE');
INSERT INTO `ubigeo_inei` VALUES (1384, '14', '02', '02', 'CAÑARIS');
INSERT INTO `ubigeo_inei` VALUES (1385, '14', '02', '03', 'INCAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1386, '14', '02', '04', 'MANUEL ANTONIO MESONES MURO');
INSERT INTO `ubigeo_inei` VALUES (1387, '14', '02', '05', 'PITIPO');
INSERT INTO `ubigeo_inei` VALUES (1388, '14', '02', '06', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1389, '14', '03', '00', 'LAMBAYEQUE');
INSERT INTO `ubigeo_inei` VALUES (1390, '14', '03', '01', 'LAMBAYEQUE');
INSERT INTO `ubigeo_inei` VALUES (1391, '14', '03', '02', 'CHOCHOPE');
INSERT INTO `ubigeo_inei` VALUES (1392, '14', '03', '03', 'ILLIMO');
INSERT INTO `ubigeo_inei` VALUES (1393, '14', '03', '04', 'JAYANCA');
INSERT INTO `ubigeo_inei` VALUES (1394, '14', '03', '05', 'MOCHUMI');
INSERT INTO `ubigeo_inei` VALUES (1395, '14', '03', '06', 'MORROPE');
INSERT INTO `ubigeo_inei` VALUES (1396, '14', '03', '07', 'MOTUPE');
INSERT INTO `ubigeo_inei` VALUES (1397, '14', '03', '08', 'OLMOS');
INSERT INTO `ubigeo_inei` VALUES (1398, '14', '03', '09', 'PACORA');
INSERT INTO `ubigeo_inei` VALUES (1399, '14', '03', '10', 'SALAS');
INSERT INTO `ubigeo_inei` VALUES (1400, '14', '03', '11', 'SAN JOSE');
INSERT INTO `ubigeo_inei` VALUES (1401, '14', '03', '12', 'TUCUME');
INSERT INTO `ubigeo_inei` VALUES (1402, '15', '00', '00', 'LIMA');
INSERT INTO `ubigeo_inei` VALUES (1403, '15', '01', '00', 'LIMA');
INSERT INTO `ubigeo_inei` VALUES (1404, '15', '01', '01', 'LIMA');
INSERT INTO `ubigeo_inei` VALUES (1405, '15', '01', '02', 'ANCON');
INSERT INTO `ubigeo_inei` VALUES (1406, '15', '01', '03', 'ATE');
INSERT INTO `ubigeo_inei` VALUES (1407, '15', '01', '04', 'BARRANCO');
INSERT INTO `ubigeo_inei` VALUES (1408, '15', '01', '05', 'BREÑA');
INSERT INTO `ubigeo_inei` VALUES (1409, '15', '01', '06', 'CARABAYLLO');
INSERT INTO `ubigeo_inei` VALUES (1410, '15', '01', '07', 'CHACLACAYO');
INSERT INTO `ubigeo_inei` VALUES (1411, '15', '01', '08', 'CHORRILLOS');
INSERT INTO `ubigeo_inei` VALUES (1412, '15', '01', '09', 'CIENEGUILLA');
INSERT INTO `ubigeo_inei` VALUES (1413, '15', '01', '10', 'COMAS');
INSERT INTO `ubigeo_inei` VALUES (1414, '15', '01', '11', 'EL AGUSTINO');
INSERT INTO `ubigeo_inei` VALUES (1415, '15', '01', '12', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (1416, '15', '01', '13', 'JESUS MARIA');
INSERT INTO `ubigeo_inei` VALUES (1417, '15', '01', '14', 'LA MOLINA');
INSERT INTO `ubigeo_inei` VALUES (1418, '15', '01', '15', 'LA VICTORIA');
INSERT INTO `ubigeo_inei` VALUES (1419, '15', '01', '16', 'LINCE');
INSERT INTO `ubigeo_inei` VALUES (1420, '15', '01', '17', 'LOS OLIVOS');
INSERT INTO `ubigeo_inei` VALUES (1421, '15', '01', '18', 'LURIGANCHO');
INSERT INTO `ubigeo_inei` VALUES (1422, '15', '01', '19', 'LURIN');
INSERT INTO `ubigeo_inei` VALUES (1423, '15', '01', '20', 'MAGDALENA DEL MAR');
INSERT INTO `ubigeo_inei` VALUES (1424, '15', '01', '21', 'PUEBLO LIBRE (MAGDALENA VIEJA)');
INSERT INTO `ubigeo_inei` VALUES (1425, '15', '01', '22', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1426, '15', '01', '23', 'PACHACAMAC');
INSERT INTO `ubigeo_inei` VALUES (1427, '15', '01', '24', 'PUCUSANA');
INSERT INTO `ubigeo_inei` VALUES (1428, '15', '01', '25', 'PUENTE PIEDRA');
INSERT INTO `ubigeo_inei` VALUES (1429, '15', '01', '26', 'PUNTA HERMOSA');
INSERT INTO `ubigeo_inei` VALUES (1430, '15', '01', '27', 'PUNTA NEGRA');
INSERT INTO `ubigeo_inei` VALUES (1431, '15', '01', '28', 'RIMAC');
INSERT INTO `ubigeo_inei` VALUES (1432, '15', '01', '29', 'SAN BARTOLO');
INSERT INTO `ubigeo_inei` VALUES (1433, '15', '01', '30', 'SAN BORJA');
INSERT INTO `ubigeo_inei` VALUES (1434, '15', '01', '31', 'SAN ISIDRO');
INSERT INTO `ubigeo_inei` VALUES (1435, '15', '01', '32', 'SAN JUAN DE LURIGANCHO');
INSERT INTO `ubigeo_inei` VALUES (1436, '15', '01', '33', 'SAN JUAN DE MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1437, '15', '01', '34', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (1438, '15', '01', '35', 'SAN MARTIN DE PORRES');
INSERT INTO `ubigeo_inei` VALUES (1439, '15', '01', '36', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (1440, '15', '01', '37', 'SANTA ANITA');
INSERT INTO `ubigeo_inei` VALUES (1441, '15', '01', '38', 'SANTA MARIA DEL MAR');
INSERT INTO `ubigeo_inei` VALUES (1442, '15', '01', '39', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1443, '15', '01', '40', 'SANTIAGO DE SURCO');
INSERT INTO `ubigeo_inei` VALUES (1444, '15', '01', '41', 'SURQUILLO');
INSERT INTO `ubigeo_inei` VALUES (1445, '15', '01', '42', 'VILLA EL SALVADOR');
INSERT INTO `ubigeo_inei` VALUES (1446, '15', '01', '43', 'VILLA MARIA DEL TRIUNFO');
INSERT INTO `ubigeo_inei` VALUES (1447, '15', '02', '00', 'BARRANCA');
INSERT INTO `ubigeo_inei` VALUES (1448, '15', '02', '01', 'BARRANCA');
INSERT INTO `ubigeo_inei` VALUES (1449, '15', '02', '02', 'PARAMONGA');
INSERT INTO `ubigeo_inei` VALUES (1450, '15', '02', '03', 'PATIVILCA');
INSERT INTO `ubigeo_inei` VALUES (1451, '15', '02', '04', 'SUPE');
INSERT INTO `ubigeo_inei` VALUES (1452, '15', '02', '05', 'SUPE PUERTO');
INSERT INTO `ubigeo_inei` VALUES (1453, '15', '03', '00', 'CAJATAMBO');
INSERT INTO `ubigeo_inei` VALUES (1454, '15', '03', '01', 'CAJATAMBO');
INSERT INTO `ubigeo_inei` VALUES (1455, '15', '03', '02', 'COPA');
INSERT INTO `ubigeo_inei` VALUES (1456, '15', '03', '03', 'GORGOR');
INSERT INTO `ubigeo_inei` VALUES (1457, '15', '03', '04', 'HUANCAPON');
INSERT INTO `ubigeo_inei` VALUES (1458, '15', '03', '05', 'MANAS');
INSERT INTO `ubigeo_inei` VALUES (1459, '15', '04', '00', 'CANTA');
INSERT INTO `ubigeo_inei` VALUES (1460, '15', '04', '01', 'CANTA');
INSERT INTO `ubigeo_inei` VALUES (1461, '15', '04', '02', 'ARAHUAY');
INSERT INTO `ubigeo_inei` VALUES (1462, '15', '04', '03', 'HUAMANTANGA');
INSERT INTO `ubigeo_inei` VALUES (1463, '15', '04', '04', 'HUAROS');
INSERT INTO `ubigeo_inei` VALUES (1464, '15', '04', '05', 'LACHAQUI');
INSERT INTO `ubigeo_inei` VALUES (1465, '15', '04', '06', 'SAN BUENAVENTURA');
INSERT INTO `ubigeo_inei` VALUES (1466, '15', '04', '07', 'SANTA ROSA DE QUIVES');
INSERT INTO `ubigeo_inei` VALUES (1467, '15', '05', '00', 'CAÑETE');
INSERT INTO `ubigeo_inei` VALUES (1468, '15', '05', '01', 'SAN VICENTE DE CAÑETE');
INSERT INTO `ubigeo_inei` VALUES (1469, '15', '05', '02', 'ASIA');
INSERT INTO `ubigeo_inei` VALUES (1470, '15', '05', '03', 'CALANGO');
INSERT INTO `ubigeo_inei` VALUES (1471, '15', '05', '04', 'CERRO AZUL');
INSERT INTO `ubigeo_inei` VALUES (1472, '15', '05', '05', 'CHILCA');
INSERT INTO `ubigeo_inei` VALUES (1473, '15', '05', '06', 'COAYLLO');
INSERT INTO `ubigeo_inei` VALUES (1474, '15', '05', '07', 'IMPERIAL');
INSERT INTO `ubigeo_inei` VALUES (1475, '15', '05', '08', 'LUNAHUANA');
INSERT INTO `ubigeo_inei` VALUES (1476, '15', '05', '09', 'MALA');
INSERT INTO `ubigeo_inei` VALUES (1477, '15', '05', '10', 'NUEVO IMPERIAL');
INSERT INTO `ubigeo_inei` VALUES (1478, '15', '05', '11', 'PACARAN');
INSERT INTO `ubigeo_inei` VALUES (1479, '15', '05', '12', 'QUILMANA');
INSERT INTO `ubigeo_inei` VALUES (1480, '15', '05', '13', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1481, '15', '05', '14', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (1482, '15', '05', '15', 'SANTA CRUZ DE FLORES');
INSERT INTO `ubigeo_inei` VALUES (1483, '15', '05', '16', 'ZUÑIGA');
INSERT INTO `ubigeo_inei` VALUES (1484, '15', '06', '00', 'HUARAL');
INSERT INTO `ubigeo_inei` VALUES (1485, '15', '06', '01', 'HUARAL');
INSERT INTO `ubigeo_inei` VALUES (1486, '15', '06', '02', 'ATAVILLOS ALTO');
INSERT INTO `ubigeo_inei` VALUES (1487, '15', '06', '03', 'ATAVILLOS BAJO');
INSERT INTO `ubigeo_inei` VALUES (1488, '15', '06', '04', 'AUCALLAMA');
INSERT INTO `ubigeo_inei` VALUES (1489, '15', '06', '05', 'CHANCAY');
INSERT INTO `ubigeo_inei` VALUES (1490, '15', '06', '06', 'IHUARI');
INSERT INTO `ubigeo_inei` VALUES (1491, '15', '06', '07', 'LAMPIAN');
INSERT INTO `ubigeo_inei` VALUES (1492, '15', '06', '08', 'PACARAOS');
INSERT INTO `ubigeo_inei` VALUES (1493, '15', '06', '09', 'SAN MIGUEL DE ACOS');
INSERT INTO `ubigeo_inei` VALUES (1494, '15', '06', '10', 'SANTA CRUZ DE ANDAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1495, '15', '06', '11', 'SUMBILCA');
INSERT INTO `ubigeo_inei` VALUES (1496, '15', '06', '12', 'VEINTISIETE DE NOVIEMBRE');
INSERT INTO `ubigeo_inei` VALUES (1497, '15', '07', '00', 'HUAROCHIRI');
INSERT INTO `ubigeo_inei` VALUES (1498, '15', '07', '01', 'MATUCANA');
INSERT INTO `ubigeo_inei` VALUES (1499, '15', '07', '02', 'ANTIOQUIA');
INSERT INTO `ubigeo_inei` VALUES (1500, '15', '07', '03', 'CALLAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1501, '15', '07', '04', 'CARAMPOMA');
INSERT INTO `ubigeo_inei` VALUES (1502, '15', '07', '05', 'CHICLA');
INSERT INTO `ubigeo_inei` VALUES (1503, '15', '07', '06', 'CUENCA');
INSERT INTO `ubigeo_inei` VALUES (1504, '15', '07', '07', 'HUACHUPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1505, '15', '07', '08', 'HUANZA');
INSERT INTO `ubigeo_inei` VALUES (1506, '15', '07', '09', 'HUAROCHIRI');
INSERT INTO `ubigeo_inei` VALUES (1507, '15', '07', '10', 'LAHUAYTAMBO');
INSERT INTO `ubigeo_inei` VALUES (1508, '15', '07', '11', 'LANGA');
INSERT INTO `ubigeo_inei` VALUES (1509, '15', '07', '12', 'LARAOS');
INSERT INTO `ubigeo_inei` VALUES (1510, '15', '07', '13', 'MARIATANA');
INSERT INTO `ubigeo_inei` VALUES (1511, '15', '07', '14', 'RICARDO PALMA');
INSERT INTO `ubigeo_inei` VALUES (1512, '15', '07', '15', 'SAN ANDRES DE TUPICOCHA');
INSERT INTO `ubigeo_inei` VALUES (1513, '15', '07', '16', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1514, '15', '07', '17', 'SAN BARTOLOME');
INSERT INTO `ubigeo_inei` VALUES (1515, '15', '07', '18', 'SAN DAMIAN');
INSERT INTO `ubigeo_inei` VALUES (1516, '15', '07', '19', 'SAN JUAN DE IRIS');
INSERT INTO `ubigeo_inei` VALUES (1517, '15', '07', '20', 'SAN JUAN DE TANTARANCHE');
INSERT INTO `ubigeo_inei` VALUES (1518, '15', '07', '21', 'SAN LORENZO DE QUINTI');
INSERT INTO `ubigeo_inei` VALUES (1519, '15', '07', '22', 'SAN MATEO');
INSERT INTO `ubigeo_inei` VALUES (1520, '15', '07', '23', 'SAN MATEO DE OTAO');
INSERT INTO `ubigeo_inei` VALUES (1521, '15', '07', '24', 'SAN PEDRO DE CASTA');
INSERT INTO `ubigeo_inei` VALUES (1522, '15', '07', '25', 'SAN PEDRO DE HUANCAYRE');
INSERT INTO `ubigeo_inei` VALUES (1523, '15', '07', '26', 'SANGALLAYA');
INSERT INTO `ubigeo_inei` VALUES (1524, '15', '07', '27', 'SANTA CRUZ DE COCACHACRA');
INSERT INTO `ubigeo_inei` VALUES (1525, '15', '07', '28', 'SANTA EULALIA');
INSERT INTO `ubigeo_inei` VALUES (1526, '15', '07', '29', 'SANTIAGO DE ANCHUCAYA');
INSERT INTO `ubigeo_inei` VALUES (1527, '15', '07', '30', 'SANTIAGO DE TUNA');
INSERT INTO `ubigeo_inei` VALUES (1528, '15', '07', '31', 'SANTO DOMINGO DE LOS OLLEROS');
INSERT INTO `ubigeo_inei` VALUES (1529, '15', '07', '32', 'SURCO');
INSERT INTO `ubigeo_inei` VALUES (1530, '15', '08', '00', 'HUAURA');
INSERT INTO `ubigeo_inei` VALUES (1531, '15', '08', '01', 'HUACHO');
INSERT INTO `ubigeo_inei` VALUES (1532, '15', '08', '02', 'AMBAR');
INSERT INTO `ubigeo_inei` VALUES (1533, '15', '08', '03', 'CALETA DE CARQUIN');
INSERT INTO `ubigeo_inei` VALUES (1534, '15', '08', '04', 'CHECRAS');
INSERT INTO `ubigeo_inei` VALUES (1535, '15', '08', '05', 'HUALMAY');
INSERT INTO `ubigeo_inei` VALUES (1536, '15', '08', '06', 'HUAURA');
INSERT INTO `ubigeo_inei` VALUES (1537, '15', '08', '07', 'LEONCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (1538, '15', '08', '08', 'PACCHO');
INSERT INTO `ubigeo_inei` VALUES (1539, '15', '08', '09', 'SANTA LEONOR');
INSERT INTO `ubigeo_inei` VALUES (1540, '15', '08', '10', 'SANTA MARIA');
INSERT INTO `ubigeo_inei` VALUES (1541, '15', '08', '11', 'SAYAN');
INSERT INTO `ubigeo_inei` VALUES (1542, '15', '08', '12', 'VEGUETA');
INSERT INTO `ubigeo_inei` VALUES (1543, '15', '09', '00', 'OYON');
INSERT INTO `ubigeo_inei` VALUES (1544, '15', '09', '01', 'OYON');
INSERT INTO `ubigeo_inei` VALUES (1545, '15', '09', '02', 'ANDAJES');
INSERT INTO `ubigeo_inei` VALUES (1546, '15', '09', '03', 'CAUJUL');
INSERT INTO `ubigeo_inei` VALUES (1547, '15', '09', '04', 'COCHAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1548, '15', '09', '05', 'NAVAN');
INSERT INTO `ubigeo_inei` VALUES (1549, '15', '09', '06', 'PACHANGARA');
INSERT INTO `ubigeo_inei` VALUES (1550, '15', '10', '00', 'YAUYOS');
INSERT INTO `ubigeo_inei` VALUES (1551, '15', '10', '01', 'YAUYOS');
INSERT INTO `ubigeo_inei` VALUES (1552, '15', '10', '02', 'ALIS');
INSERT INTO `ubigeo_inei` VALUES (1553, '15', '10', '03', 'AYAUCA');
INSERT INTO `ubigeo_inei` VALUES (1554, '15', '10', '04', 'AYAVIRI');
INSERT INTO `ubigeo_inei` VALUES (1555, '15', '10', '05', 'AZANGARO');
INSERT INTO `ubigeo_inei` VALUES (1556, '15', '10', '06', 'CACRA');
INSERT INTO `ubigeo_inei` VALUES (1557, '15', '10', '07', 'CARANIA');
INSERT INTO `ubigeo_inei` VALUES (1558, '15', '10', '08', 'CATAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1559, '15', '10', '09', 'CHOCOS');
INSERT INTO `ubigeo_inei` VALUES (1560, '15', '10', '10', 'COCHAS');
INSERT INTO `ubigeo_inei` VALUES (1561, '15', '10', '11', 'COLONIA');
INSERT INTO `ubigeo_inei` VALUES (1562, '15', '10', '12', 'HONGOS');
INSERT INTO `ubigeo_inei` VALUES (1563, '15', '10', '13', 'HUAMPARA');
INSERT INTO `ubigeo_inei` VALUES (1564, '15', '10', '14', 'HUANCAYA');
INSERT INTO `ubigeo_inei` VALUES (1565, '15', '10', '15', 'HUANGASCAR');
INSERT INTO `ubigeo_inei` VALUES (1566, '15', '10', '16', 'HUANTAN');
INSERT INTO `ubigeo_inei` VALUES (1567, '15', '10', '17', 'HUAÑEC');
INSERT INTO `ubigeo_inei` VALUES (1568, '15', '10', '18', 'LARAOS');
INSERT INTO `ubigeo_inei` VALUES (1569, '15', '10', '19', 'LINCHA');
INSERT INTO `ubigeo_inei` VALUES (1570, '15', '10', '20', 'MADEAN');
INSERT INTO `ubigeo_inei` VALUES (1571, '15', '10', '21', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1572, '15', '10', '22', 'OMAS');
INSERT INTO `ubigeo_inei` VALUES (1573, '15', '10', '23', 'PUTINZA');
INSERT INTO `ubigeo_inei` VALUES (1574, '15', '10', '24', 'QUINCHES');
INSERT INTO `ubigeo_inei` VALUES (1575, '15', '10', '25', 'QUINOCAY');
INSERT INTO `ubigeo_inei` VALUES (1576, '15', '10', '26', 'SAN JOAQUIN');
INSERT INTO `ubigeo_inei` VALUES (1577, '15', '10', '27', 'SAN PEDRO DE PILAS');
INSERT INTO `ubigeo_inei` VALUES (1578, '15', '10', '28', 'TANTA');
INSERT INTO `ubigeo_inei` VALUES (1579, '15', '10', '29', 'TAURIPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1580, '15', '10', '30', 'TOMAS');
INSERT INTO `ubigeo_inei` VALUES (1581, '15', '10', '31', 'TUPE');
INSERT INTO `ubigeo_inei` VALUES (1582, '15', '10', '32', 'VIÑAC');
INSERT INTO `ubigeo_inei` VALUES (1583, '15', '10', '33', 'VITIS');
INSERT INTO `ubigeo_inei` VALUES (1584, '16', '00', '00', 'LORETO');
INSERT INTO `ubigeo_inei` VALUES (1585, '16', '01', '00', 'MAYNAS');
INSERT INTO `ubigeo_inei` VALUES (1586, '16', '01', '01', 'IQUITOS');
INSERT INTO `ubigeo_inei` VALUES (1587, '16', '01', '02', 'ALTO NANAY');
INSERT INTO `ubigeo_inei` VALUES (1588, '16', '01', '03', 'FERNANDO LORES');
INSERT INTO `ubigeo_inei` VALUES (1589, '16', '01', '04', 'INDIANA');
INSERT INTO `ubigeo_inei` VALUES (1590, '16', '01', '05', 'LAS AMAZONAS');
INSERT INTO `ubigeo_inei` VALUES (1591, '16', '01', '06', 'MAZAN');
INSERT INTO `ubigeo_inei` VALUES (1592, '16', '01', '07', 'NAPO');
INSERT INTO `ubigeo_inei` VALUES (1593, '16', '01', '08', 'PUNCHANA');
INSERT INTO `ubigeo_inei` VALUES (1594, '16', '01', '09', 'PUTUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1595, '16', '01', '10', 'TORRES CAUSANA');
INSERT INTO `ubigeo_inei` VALUES (1596, '16', '01', '12', 'BELÉN');
INSERT INTO `ubigeo_inei` VALUES (1597, '16', '01', '13', 'SAN JUAN BAUTISTA');
INSERT INTO `ubigeo_inei` VALUES (1598, '16', '01', '14', 'TENIENTE MANUEL CLAVERO');
INSERT INTO `ubigeo_inei` VALUES (1599, '16', '02', '00', 'ALTO AMAZONAS');
INSERT INTO `ubigeo_inei` VALUES (1600, '16', '02', '01', 'YURIMAGUAS');
INSERT INTO `ubigeo_inei` VALUES (1601, '16', '02', '02', 'BALSAPUERTO');
INSERT INTO `ubigeo_inei` VALUES (1602, '16', '02', '05', 'JEBEROS');
INSERT INTO `ubigeo_inei` VALUES (1603, '16', '02', '06', 'LAGUNAS');
INSERT INTO `ubigeo_inei` VALUES (1604, '16', '02', '10', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (1605, '16', '02', '11', 'TENIENTE CESAR LOPEZ ROJAS');
INSERT INTO `ubigeo_inei` VALUES (1606, '16', '03', '00', 'LORETO');
INSERT INTO `ubigeo_inei` VALUES (1607, '16', '03', '01', 'NAUTA');
INSERT INTO `ubigeo_inei` VALUES (1608, '16', '03', '02', 'PARINARI');
INSERT INTO `ubigeo_inei` VALUES (1609, '16', '03', '03', 'TIGRE');
INSERT INTO `ubigeo_inei` VALUES (1610, '16', '03', '04', 'TROMPETEROS');
INSERT INTO `ubigeo_inei` VALUES (1611, '16', '03', '05', 'URARINAS');
INSERT INTO `ubigeo_inei` VALUES (1612, '16', '04', '00', 'MARISCAL RAMON CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1613, '16', '04', '01', 'RAMON CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1614, '16', '04', '02', 'PEBAS');
INSERT INTO `ubigeo_inei` VALUES (1615, '16', '04', '03', 'YAVARI');
INSERT INTO `ubigeo_inei` VALUES (1616, '16', '04', '04', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (1617, '16', '05', '00', 'REQUENA');
INSERT INTO `ubigeo_inei` VALUES (1618, '16', '05', '01', 'REQUENA');
INSERT INTO `ubigeo_inei` VALUES (1619, '16', '05', '02', 'ALTO TAPICHE');
INSERT INTO `ubigeo_inei` VALUES (1620, '16', '05', '03', 'CAPELO');
INSERT INTO `ubigeo_inei` VALUES (1621, '16', '05', '04', 'EMILIO SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1622, '16', '05', '05', 'MAQUIA');
INSERT INTO `ubigeo_inei` VALUES (1623, '16', '05', '06', 'PUINAHUA');
INSERT INTO `ubigeo_inei` VALUES (1624, '16', '05', '07', 'SAQUENA');
INSERT INTO `ubigeo_inei` VALUES (1625, '16', '05', '08', 'SOPLIN');
INSERT INTO `ubigeo_inei` VALUES (1626, '16', '05', '09', 'TAPICHE');
INSERT INTO `ubigeo_inei` VALUES (1627, '16', '05', '10', 'JENARO HERRERA');
INSERT INTO `ubigeo_inei` VALUES (1628, '16', '05', '11', 'YAQUERANA');
INSERT INTO `ubigeo_inei` VALUES (1629, '16', '06', '00', 'UCAYALI');
INSERT INTO `ubigeo_inei` VALUES (1630, '16', '06', '01', 'CONTAMANA');
INSERT INTO `ubigeo_inei` VALUES (1631, '16', '06', '02', 'INAHUAYA');
INSERT INTO `ubigeo_inei` VALUES (1632, '16', '06', '03', 'PADRE MARQUEZ');
INSERT INTO `ubigeo_inei` VALUES (1633, '16', '06', '04', 'PAMPA HERMOSA');
INSERT INTO `ubigeo_inei` VALUES (1634, '16', '06', '05', 'SARAYACU');
INSERT INTO `ubigeo_inei` VALUES (1635, '16', '06', '06', 'VARGAS GUERRA');
INSERT INTO `ubigeo_inei` VALUES (1636, '16', '07', '00', 'DATEM DEL MARAÑÓN');
INSERT INTO `ubigeo_inei` VALUES (1637, '16', '07', '01', 'BARRANCA');
INSERT INTO `ubigeo_inei` VALUES (1638, '16', '07', '02', 'CAHUAPANAS');
INSERT INTO `ubigeo_inei` VALUES (1639, '16', '07', '03', 'MANSERICHE');
INSERT INTO `ubigeo_inei` VALUES (1640, '16', '07', '04', 'MORONA');
INSERT INTO `ubigeo_inei` VALUES (1641, '16', '07', '05', 'PASTAZA');
INSERT INTO `ubigeo_inei` VALUES (1642, '16', '07', '06', 'ANDOAS');
INSERT INTO `ubigeo_inei` VALUES (1643, '16', '08', '00', 'PUTUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1644, '16', '08', '01', 'PUTUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1645, '16', '08', '02', 'ROSA PANDURO');
INSERT INTO `ubigeo_inei` VALUES (1646, '16', '08', '03', 'TENIENTE MANUEL CLAVERO');
INSERT INTO `ubigeo_inei` VALUES (1647, '16', '08', '04', 'YAGUAS');
INSERT INTO `ubigeo_inei` VALUES (1648, '17', '00', '00', 'MADRE DE DIOS');
INSERT INTO `ubigeo_inei` VALUES (1649, '17', '01', '00', 'TAMBOPATA');
INSERT INTO `ubigeo_inei` VALUES (1650, '17', '01', '01', 'TAMBOPATA');
INSERT INTO `ubigeo_inei` VALUES (1651, '17', '01', '02', 'INAMBARI');
INSERT INTO `ubigeo_inei` VALUES (1652, '17', '01', '03', 'LAS PIEDRAS');
INSERT INTO `ubigeo_inei` VALUES (1653, '17', '01', '04', 'LABERINTO');
INSERT INTO `ubigeo_inei` VALUES (1654, '17', '02', '00', 'MANU');
INSERT INTO `ubigeo_inei` VALUES (1655, '17', '02', '01', 'MANU');
INSERT INTO `ubigeo_inei` VALUES (1656, '17', '02', '02', 'FITZCARRALD');
INSERT INTO `ubigeo_inei` VALUES (1657, '17', '02', '03', 'MADRE DE DIOS');
INSERT INTO `ubigeo_inei` VALUES (1658, '17', '02', '04', 'HUEPETUHE');
INSERT INTO `ubigeo_inei` VALUES (1659, '17', '03', '00', 'TAHUAMANU');
INSERT INTO `ubigeo_inei` VALUES (1660, '17', '03', '01', 'IÑAPARI');
INSERT INTO `ubigeo_inei` VALUES (1661, '17', '03', '02', 'IBERIA');
INSERT INTO `ubigeo_inei` VALUES (1662, '17', '03', '03', 'TAHUAMANU');
INSERT INTO `ubigeo_inei` VALUES (1663, '18', '00', '00', 'MOQUEGUA');
INSERT INTO `ubigeo_inei` VALUES (1664, '18', '01', '00', 'MARISCAL NIETO');
INSERT INTO `ubigeo_inei` VALUES (1665, '18', '01', '01', 'MOQUEGUA');
INSERT INTO `ubigeo_inei` VALUES (1666, '18', '01', '02', 'CARUMAS');
INSERT INTO `ubigeo_inei` VALUES (1667, '18', '01', '03', 'CUCHUMBAYA');
INSERT INTO `ubigeo_inei` VALUES (1668, '18', '01', '04', 'SAMEGUA');
INSERT INTO `ubigeo_inei` VALUES (1669, '18', '01', '05', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (1670, '18', '01', '06', 'TORATA');
INSERT INTO `ubigeo_inei` VALUES (1671, '18', '02', '00', 'GENERAL SANCHEZ CERRO');
INSERT INTO `ubigeo_inei` VALUES (1672, '18', '02', '01', 'OMATE');
INSERT INTO `ubigeo_inei` VALUES (1673, '18', '02', '02', 'CHOJATA');
INSERT INTO `ubigeo_inei` VALUES (1674, '18', '02', '03', 'COALAQUE');
INSERT INTO `ubigeo_inei` VALUES (1675, '18', '02', '04', 'ICHUÑA');
INSERT INTO `ubigeo_inei` VALUES (1676, '18', '02', '05', 'LA CAPILLA');
INSERT INTO `ubigeo_inei` VALUES (1677, '18', '02', '06', 'LLOQUE');
INSERT INTO `ubigeo_inei` VALUES (1678, '18', '02', '07', 'MATALAQUE');
INSERT INTO `ubigeo_inei` VALUES (1679, '18', '02', '08', 'PUQUINA');
INSERT INTO `ubigeo_inei` VALUES (1680, '18', '02', '09', 'QUINISTAQUILLAS');
INSERT INTO `ubigeo_inei` VALUES (1681, '18', '02', '10', 'UBINAS');
INSERT INTO `ubigeo_inei` VALUES (1682, '18', '02', '11', 'YUNGA');
INSERT INTO `ubigeo_inei` VALUES (1683, '18', '03', '00', 'ILO');
INSERT INTO `ubigeo_inei` VALUES (1684, '18', '03', '01', 'ILO');
INSERT INTO `ubigeo_inei` VALUES (1685, '18', '03', '02', 'EL ALGARROBAL');
INSERT INTO `ubigeo_inei` VALUES (1686, '18', '03', '03', 'PACOCHA');
INSERT INTO `ubigeo_inei` VALUES (1687, '19', '00', '00', 'PASCO');
INSERT INTO `ubigeo_inei` VALUES (1688, '19', '01', '00', 'PASCO');
INSERT INTO `ubigeo_inei` VALUES (1689, '19', '01', '01', 'CHAUPIMARCA');
INSERT INTO `ubigeo_inei` VALUES (1690, '19', '01', '02', 'HUACHON');
INSERT INTO `ubigeo_inei` VALUES (1691, '19', '01', '03', 'HUARIACA');
INSERT INTO `ubigeo_inei` VALUES (1692, '19', '01', '04', 'HUAYLLAY');
INSERT INTO `ubigeo_inei` VALUES (1693, '19', '01', '05', 'NINACACA');
INSERT INTO `ubigeo_inei` VALUES (1694, '19', '01', '06', 'PALLANCHACRA');
INSERT INTO `ubigeo_inei` VALUES (1695, '19', '01', '07', 'PAUCARTAMBO');
INSERT INTO `ubigeo_inei` VALUES (1696, '19', '01', '08', 'SAN FCO. DE ASÍS DE YARUSYACÁN');
INSERT INTO `ubigeo_inei` VALUES (1697, '19', '01', '09', 'SIMON BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (1698, '19', '01', '10', 'TICLACAYAN');
INSERT INTO `ubigeo_inei` VALUES (1699, '19', '01', '11', 'TINYAHUARCO');
INSERT INTO `ubigeo_inei` VALUES (1700, '19', '01', '12', 'VICCO');
INSERT INTO `ubigeo_inei` VALUES (1701, '19', '01', '13', 'YANACANCHA');
INSERT INTO `ubigeo_inei` VALUES (1702, '19', '02', '00', 'DANIEL ALCIDES CARRION');
INSERT INTO `ubigeo_inei` VALUES (1703, '19', '02', '01', 'YANAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1704, '19', '02', '02', 'CHACAYAN');
INSERT INTO `ubigeo_inei` VALUES (1705, '19', '02', '03', 'GOYLLARISQUIZGA');
INSERT INTO `ubigeo_inei` VALUES (1706, '19', '02', '04', 'PAUCAR');
INSERT INTO `ubigeo_inei` VALUES (1707, '19', '02', '05', 'SAN PEDRO DE PILLAO');
INSERT INTO `ubigeo_inei` VALUES (1708, '19', '02', '06', 'SANTA ANA DE TUSI');
INSERT INTO `ubigeo_inei` VALUES (1709, '19', '02', '07', 'TAPUC');
INSERT INTO `ubigeo_inei` VALUES (1710, '19', '02', '08', 'VILCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1711, '19', '03', '00', 'OXAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1712, '19', '03', '01', 'OXAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1713, '19', '03', '02', 'CHONTABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1714, '19', '03', '03', 'HUANCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1715, '19', '03', '04', 'PALCAZU');
INSERT INTO `ubigeo_inei` VALUES (1716, '19', '03', '05', 'POZUZO');
INSERT INTO `ubigeo_inei` VALUES (1717, '19', '03', '06', 'PUERTO BERMUDEZ');
INSERT INTO `ubigeo_inei` VALUES (1718, '19', '03', '07', 'VILLA RICA');
INSERT INTO `ubigeo_inei` VALUES (1719, '19', '03', '08', 'CONSTITUCION');
INSERT INTO `ubigeo_inei` VALUES (1720, '20', '00', '00', 'PIURA');
INSERT INTO `ubigeo_inei` VALUES (1721, '20', '01', '00', 'PIURA');
INSERT INTO `ubigeo_inei` VALUES (1722, '20', '01', '01', 'PIURA');
INSERT INTO `ubigeo_inei` VALUES (1723, '20', '01', '04', 'CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1724, '20', '01', '05', 'CATACAOS');
INSERT INTO `ubigeo_inei` VALUES (1725, '20', '01', '07', 'CURA MORI');
INSERT INTO `ubigeo_inei` VALUES (1726, '20', '01', '08', 'EL TALLAN');
INSERT INTO `ubigeo_inei` VALUES (1727, '20', '01', '09', 'LA ARENA');
INSERT INTO `ubigeo_inei` VALUES (1728, '20', '01', '10', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1729, '20', '01', '11', 'LAS LOMAS');
INSERT INTO `ubigeo_inei` VALUES (1730, '20', '01', '14', 'TAMBO GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1731, '20', '01', '15', 'VEINTISÉIS DE OCTUBRE');
INSERT INTO `ubigeo_inei` VALUES (1732, '20', '02', '00', 'AYABACA');
INSERT INTO `ubigeo_inei` VALUES (1733, '20', '02', '01', 'AYABACA');
INSERT INTO `ubigeo_inei` VALUES (1734, '20', '02', '02', 'FRIAS');
INSERT INTO `ubigeo_inei` VALUES (1735, '20', '02', '03', 'JILILI');
INSERT INTO `ubigeo_inei` VALUES (1736, '20', '02', '04', 'LAGUNAS');
INSERT INTO `ubigeo_inei` VALUES (1737, '20', '02', '05', 'MONTERO');
INSERT INTO `ubigeo_inei` VALUES (1738, '20', '02', '06', 'PACAIPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1739, '20', '02', '07', 'PAIMAS');
INSERT INTO `ubigeo_inei` VALUES (1740, '20', '02', '08', 'SAPILLICA');
INSERT INTO `ubigeo_inei` VALUES (1741, '20', '02', '09', 'SICCHEZ');
INSERT INTO `ubigeo_inei` VALUES (1742, '20', '02', '10', 'SUYO');
INSERT INTO `ubigeo_inei` VALUES (1743, '20', '03', '00', 'HUANCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1744, '20', '03', '01', 'HUANCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1745, '20', '03', '02', 'CANCHAQUE');
INSERT INTO `ubigeo_inei` VALUES (1746, '20', '03', '03', 'EL CARMEN DE LA FRONTERA');
INSERT INTO `ubigeo_inei` VALUES (1747, '20', '03', '04', 'HUARMACA');
INSERT INTO `ubigeo_inei` VALUES (1748, '20', '03', '05', 'LALAQUIZ');
INSERT INTO `ubigeo_inei` VALUES (1749, '20', '03', '06', 'SAN MIGUEL DE EL FAIQUE');
INSERT INTO `ubigeo_inei` VALUES (1750, '20', '03', '07', 'SONDOR');
INSERT INTO `ubigeo_inei` VALUES (1751, '20', '03', '08', 'SONDORILLO');
INSERT INTO `ubigeo_inei` VALUES (1752, '20', '04', '00', 'MORROPON');
INSERT INTO `ubigeo_inei` VALUES (1753, '20', '04', '01', 'CHULUCANAS');
INSERT INTO `ubigeo_inei` VALUES (1754, '20', '04', '02', 'BUENOS AIRES');
INSERT INTO `ubigeo_inei` VALUES (1755, '20', '04', '03', 'CHALACO');
INSERT INTO `ubigeo_inei` VALUES (1756, '20', '04', '04', 'LA MATANZA');
INSERT INTO `ubigeo_inei` VALUES (1757, '20', '04', '05', 'MORROPON');
INSERT INTO `ubigeo_inei` VALUES (1758, '20', '04', '06', 'SALITRAL');
INSERT INTO `ubigeo_inei` VALUES (1759, '20', '04', '07', 'SAN JUAN DE BIGOTE');
INSERT INTO `ubigeo_inei` VALUES (1760, '20', '04', '08', 'SANTA CATALINA DE MOSSA');
INSERT INTO `ubigeo_inei` VALUES (1761, '20', '04', '09', 'SANTO DOMINGO');
INSERT INTO `ubigeo_inei` VALUES (1762, '20', '04', '10', 'YAMANGO');
INSERT INTO `ubigeo_inei` VALUES (1763, '20', '05', '00', 'PAITA');
INSERT INTO `ubigeo_inei` VALUES (1764, '20', '05', '01', 'PAITA');
INSERT INTO `ubigeo_inei` VALUES (1765, '20', '05', '02', 'AMOTAPE');
INSERT INTO `ubigeo_inei` VALUES (1766, '20', '05', '03', 'ARENAL');
INSERT INTO `ubigeo_inei` VALUES (1767, '20', '05', '04', 'COLAN');
INSERT INTO `ubigeo_inei` VALUES (1768, '20', '05', '05', 'LA HUACA');
INSERT INTO `ubigeo_inei` VALUES (1769, '20', '05', '06', 'TAMARINDO');
INSERT INTO `ubigeo_inei` VALUES (1770, '20', '05', '07', 'VICHAYAL');
INSERT INTO `ubigeo_inei` VALUES (1771, '20', '06', '00', 'SULLANA');
INSERT INTO `ubigeo_inei` VALUES (1772, '20', '06', '01', 'SULLANA');
INSERT INTO `ubigeo_inei` VALUES (1773, '20', '06', '02', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1774, '20', '06', '03', 'IGNACIO ESCUDERO');
INSERT INTO `ubigeo_inei` VALUES (1775, '20', '06', '04', 'LANCONES');
INSERT INTO `ubigeo_inei` VALUES (1776, '20', '06', '05', 'MARCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (1777, '20', '06', '06', 'MIGUEL CHECA');
INSERT INTO `ubigeo_inei` VALUES (1778, '20', '06', '07', 'QUERECOTILLO');
INSERT INTO `ubigeo_inei` VALUES (1779, '20', '06', '08', 'SALITRAL');
INSERT INTO `ubigeo_inei` VALUES (1780, '20', '07', '00', 'TALARA');
INSERT INTO `ubigeo_inei` VALUES (1781, '20', '07', '01', 'PARIÑAS');
INSERT INTO `ubigeo_inei` VALUES (1782, '20', '07', '02', 'EL ALTO');
INSERT INTO `ubigeo_inei` VALUES (1783, '20', '07', '03', 'LA BREA');
INSERT INTO `ubigeo_inei` VALUES (1784, '20', '07', '04', 'LOBITOS');
INSERT INTO `ubigeo_inei` VALUES (1785, '20', '07', '05', 'LOS ORGANOS');
INSERT INTO `ubigeo_inei` VALUES (1786, '20', '07', '06', 'MANCORA');
INSERT INTO `ubigeo_inei` VALUES (1787, '20', '08', '00', 'SECHURA');
INSERT INTO `ubigeo_inei` VALUES (1788, '20', '08', '01', 'SECHURA');
INSERT INTO `ubigeo_inei` VALUES (1789, '20', '08', '02', 'BELLAVISTA DE LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1790, '20', '08', '03', 'BERNAL');
INSERT INTO `ubigeo_inei` VALUES (1791, '20', '08', '04', 'CRISTO NOS VALGA');
INSERT INTO `ubigeo_inei` VALUES (1792, '20', '08', '05', 'VICE');
INSERT INTO `ubigeo_inei` VALUES (1793, '20', '08', '06', 'RINCONADA LLICUAR');
INSERT INTO `ubigeo_inei` VALUES (1794, '21', '00', '00', 'PUNO');
INSERT INTO `ubigeo_inei` VALUES (1795, '21', '01', '00', 'PUNO');
INSERT INTO `ubigeo_inei` VALUES (1796, '21', '01', '01', 'PUNO');
INSERT INTO `ubigeo_inei` VALUES (1797, '21', '01', '02', 'ACORA');
INSERT INTO `ubigeo_inei` VALUES (1798, '21', '01', '03', 'AMANTANI');
INSERT INTO `ubigeo_inei` VALUES (1799, '21', '01', '04', 'ATUNCOLLA');
INSERT INTO `ubigeo_inei` VALUES (1800, '21', '01', '05', 'CAPACHICA');
INSERT INTO `ubigeo_inei` VALUES (1801, '21', '01', '06', 'CHUCUITO');
INSERT INTO `ubigeo_inei` VALUES (1802, '21', '01', '07', 'COATA');
INSERT INTO `ubigeo_inei` VALUES (1803, '21', '01', '08', 'HUATA');
INSERT INTO `ubigeo_inei` VALUES (1804, '21', '01', '09', 'MAÑAZO');
INSERT INTO `ubigeo_inei` VALUES (1805, '21', '01', '10', 'PAUCARCOLLA');
INSERT INTO `ubigeo_inei` VALUES (1806, '21', '01', '11', 'PICHACANI');
INSERT INTO `ubigeo_inei` VALUES (1807, '21', '01', '12', 'PLATERIA');
INSERT INTO `ubigeo_inei` VALUES (1808, '21', '01', '13', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1809, '21', '01', '14', 'TIQUILLACA');
INSERT INTO `ubigeo_inei` VALUES (1810, '21', '01', '15', 'VILQUE');
INSERT INTO `ubigeo_inei` VALUES (1811, '21', '02', '00', 'AZANGARO');
INSERT INTO `ubigeo_inei` VALUES (1812, '21', '02', '01', 'AZANGARO');
INSERT INTO `ubigeo_inei` VALUES (1813, '21', '02', '02', 'ACHAYA');
INSERT INTO `ubigeo_inei` VALUES (1814, '21', '02', '03', 'ARAPA');
INSERT INTO `ubigeo_inei` VALUES (1815, '21', '02', '04', 'ASILLO');
INSERT INTO `ubigeo_inei` VALUES (1816, '21', '02', '05', 'CAMINACA');
INSERT INTO `ubigeo_inei` VALUES (1817, '21', '02', '06', 'CHUPA');
INSERT INTO `ubigeo_inei` VALUES (1818, '21', '02', '07', 'JOSE DOMINGO CHOQUEHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1819, '21', '02', '08', 'MUÑANI');
INSERT INTO `ubigeo_inei` VALUES (1820, '21', '02', '09', 'POTONI');
INSERT INTO `ubigeo_inei` VALUES (1821, '21', '02', '10', 'SAMAN');
INSERT INTO `ubigeo_inei` VALUES (1822, '21', '02', '11', 'SAN ANTON');
INSERT INTO `ubigeo_inei` VALUES (1823, '21', '02', '12', 'SAN JOSE');
INSERT INTO `ubigeo_inei` VALUES (1824, '21', '02', '13', 'SAN JUAN DE SALINAS');
INSERT INTO `ubigeo_inei` VALUES (1825, '21', '02', '14', 'SANTIAGO DE PUPUJA');
INSERT INTO `ubigeo_inei` VALUES (1826, '21', '02', '15', 'TIRAPATA');
INSERT INTO `ubigeo_inei` VALUES (1827, '21', '03', '00', 'CARABAYA');
INSERT INTO `ubigeo_inei` VALUES (1828, '21', '03', '01', 'MACUSANI');
INSERT INTO `ubigeo_inei` VALUES (1829, '21', '03', '02', 'AJOYANI');
INSERT INTO `ubigeo_inei` VALUES (1830, '21', '03', '03', 'AYAPATA');
INSERT INTO `ubigeo_inei` VALUES (1831, '21', '03', '04', 'COASA');
INSERT INTO `ubigeo_inei` VALUES (1832, '21', '03', '05', 'CORANI');
INSERT INTO `ubigeo_inei` VALUES (1833, '21', '03', '06', 'CRUCERO');
INSERT INTO `ubigeo_inei` VALUES (1834, '21', '03', '07', 'ITUATA');
INSERT INTO `ubigeo_inei` VALUES (1835, '21', '03', '08', 'OLLACHEA');
INSERT INTO `ubigeo_inei` VALUES (1836, '21', '03', '09', 'SAN GABAN');
INSERT INTO `ubigeo_inei` VALUES (1837, '21', '03', '10', 'USICAYOS');
INSERT INTO `ubigeo_inei` VALUES (1838, '21', '04', '00', 'CHUCUITO');
INSERT INTO `ubigeo_inei` VALUES (1839, '21', '04', '01', 'JULI');
INSERT INTO `ubigeo_inei` VALUES (1840, '21', '04', '02', 'DESAGUADERO');
INSERT INTO `ubigeo_inei` VALUES (1841, '21', '04', '03', 'HUACULLANI');
INSERT INTO `ubigeo_inei` VALUES (1842, '21', '04', '04', 'KELLUYO');
INSERT INTO `ubigeo_inei` VALUES (1843, '21', '04', '05', 'PISACOMA');
INSERT INTO `ubigeo_inei` VALUES (1844, '21', '04', '06', 'POMATA');
INSERT INTO `ubigeo_inei` VALUES (1845, '21', '04', '07', 'ZEPITA');
INSERT INTO `ubigeo_inei` VALUES (1846, '21', '05', '00', 'EL COLLAO');
INSERT INTO `ubigeo_inei` VALUES (1847, '21', '05', '01', 'ILAVE');
INSERT INTO `ubigeo_inei` VALUES (1848, '21', '05', '02', 'CAPASO');
INSERT INTO `ubigeo_inei` VALUES (1849, '21', '05', '03', 'PILCUYO');
INSERT INTO `ubigeo_inei` VALUES (1850, '21', '05', '04', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1851, '21', '05', '05', 'CONDURIRI');
INSERT INTO `ubigeo_inei` VALUES (1852, '21', '06', '00', 'HUANCANE');
INSERT INTO `ubigeo_inei` VALUES (1853, '21', '06', '01', 'HUANCANE');
INSERT INTO `ubigeo_inei` VALUES (1854, '21', '06', '02', 'COJATA');
INSERT INTO `ubigeo_inei` VALUES (1855, '21', '06', '03', 'HUATASANI');
INSERT INTO `ubigeo_inei` VALUES (1856, '21', '06', '04', 'INCHUPALLA');
INSERT INTO `ubigeo_inei` VALUES (1857, '21', '06', '05', 'PUSI');
INSERT INTO `ubigeo_inei` VALUES (1858, '21', '06', '06', 'ROSASPATA');
INSERT INTO `ubigeo_inei` VALUES (1859, '21', '06', '07', 'TARACO');
INSERT INTO `ubigeo_inei` VALUES (1860, '21', '06', '08', 'VILQUE CHICO');
INSERT INTO `ubigeo_inei` VALUES (1861, '21', '07', '00', 'LAMPA');
INSERT INTO `ubigeo_inei` VALUES (1862, '21', '07', '01', 'LAMPA');
INSERT INTO `ubigeo_inei` VALUES (1863, '21', '07', '02', 'CABANILLA');
INSERT INTO `ubigeo_inei` VALUES (1864, '21', '07', '03', 'CALAPUJA');
INSERT INTO `ubigeo_inei` VALUES (1865, '21', '07', '04', 'NICASIO');
INSERT INTO `ubigeo_inei` VALUES (1866, '21', '07', '05', 'OCUVIRI');
INSERT INTO `ubigeo_inei` VALUES (1867, '21', '07', '06', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (1868, '21', '07', '07', 'PARATIA');
INSERT INTO `ubigeo_inei` VALUES (1869, '21', '07', '08', 'PUCARA');
INSERT INTO `ubigeo_inei` VALUES (1870, '21', '07', '09', 'SANTA LUCIA');
INSERT INTO `ubigeo_inei` VALUES (1871, '21', '07', '10', 'VILAVILA');
INSERT INTO `ubigeo_inei` VALUES (1872, '21', '08', '00', 'MELGAR');
INSERT INTO `ubigeo_inei` VALUES (1873, '21', '08', '01', 'AYAVIRI');
INSERT INTO `ubigeo_inei` VALUES (1874, '21', '08', '02', 'ANTAUTA');
INSERT INTO `ubigeo_inei` VALUES (1875, '21', '08', '03', 'CUPI');
INSERT INTO `ubigeo_inei` VALUES (1876, '21', '08', '04', 'LLALLI');
INSERT INTO `ubigeo_inei` VALUES (1877, '21', '08', '05', 'MACARI');
INSERT INTO `ubigeo_inei` VALUES (1878, '21', '08', '06', 'NUÑOA');
INSERT INTO `ubigeo_inei` VALUES (1879, '21', '08', '07', 'ORURILLO');
INSERT INTO `ubigeo_inei` VALUES (1880, '21', '08', '08', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1881, '21', '08', '09', 'UMACHIRI');
INSERT INTO `ubigeo_inei` VALUES (1882, '21', '09', '00', 'MOHO');
INSERT INTO `ubigeo_inei` VALUES (1883, '21', '09', '01', 'MOHO');
INSERT INTO `ubigeo_inei` VALUES (1884, '21', '09', '02', 'CONIMA');
INSERT INTO `ubigeo_inei` VALUES (1885, '21', '09', '03', 'HUAYRAPATA');
INSERT INTO `ubigeo_inei` VALUES (1886, '21', '09', '04', 'TILALI');
INSERT INTO `ubigeo_inei` VALUES (1887, '21', '10', '00', 'SAN ANTONIO DE PUTINA');
INSERT INTO `ubigeo_inei` VALUES (1888, '21', '10', '01', 'PUTINA');
INSERT INTO `ubigeo_inei` VALUES (1889, '21', '10', '02', 'ANANEA');
INSERT INTO `ubigeo_inei` VALUES (1890, '21', '10', '03', 'PEDRO VILCA APAZA');
INSERT INTO `ubigeo_inei` VALUES (1891, '21', '10', '04', 'QUILCAPUNCU');
INSERT INTO `ubigeo_inei` VALUES (1892, '21', '10', '05', 'SINA');
INSERT INTO `ubigeo_inei` VALUES (1893, '21', '11', '00', 'SAN ROMAN');
INSERT INTO `ubigeo_inei` VALUES (1894, '21', '11', '01', 'JULIACA');
INSERT INTO `ubigeo_inei` VALUES (1895, '21', '11', '02', 'CABANA');
INSERT INTO `ubigeo_inei` VALUES (1896, '21', '11', '03', 'CABANILLAS');
INSERT INTO `ubigeo_inei` VALUES (1897, '21', '11', '04', 'CARACOTO');
INSERT INTO `ubigeo_inei` VALUES (1898, '21', '12', '00', 'SANDIA');
INSERT INTO `ubigeo_inei` VALUES (1899, '21', '12', '01', 'SANDIA');
INSERT INTO `ubigeo_inei` VALUES (1900, '21', '12', '02', 'CUYOCUYO');
INSERT INTO `ubigeo_inei` VALUES (1901, '21', '12', '03', 'LIMBANI');
INSERT INTO `ubigeo_inei` VALUES (1902, '21', '12', '04', 'PATAMBUCO');
INSERT INTO `ubigeo_inei` VALUES (1903, '21', '12', '05', 'PHARA');
INSERT INTO `ubigeo_inei` VALUES (1904, '21', '12', '06', 'QUIACA');
INSERT INTO `ubigeo_inei` VALUES (1905, '21', '12', '07', 'SAN JUAN DEL ORO');
INSERT INTO `ubigeo_inei` VALUES (1906, '21', '12', '08', 'YANAHUAYA');
INSERT INTO `ubigeo_inei` VALUES (1907, '21', '12', '09', 'ALTO INAMBARI');
INSERT INTO `ubigeo_inei` VALUES (1908, '21', '12', '10', 'SAN PEDRO DE PUTINA PUNCO');
INSERT INTO `ubigeo_inei` VALUES (1909, '21', '13', '00', 'YUNGUYO');
INSERT INTO `ubigeo_inei` VALUES (1910, '21', '13', '01', 'YUNGUYO');
INSERT INTO `ubigeo_inei` VALUES (1911, '21', '13', '02', 'ANAPIA');
INSERT INTO `ubigeo_inei` VALUES (1912, '21', '13', '03', 'COPANI');
INSERT INTO `ubigeo_inei` VALUES (1913, '21', '13', '04', 'CUTURAPI');
INSERT INTO `ubigeo_inei` VALUES (1914, '21', '13', '05', 'OLLARAYA');
INSERT INTO `ubigeo_inei` VALUES (1915, '21', '13', '06', 'TINICACHI');
INSERT INTO `ubigeo_inei` VALUES (1916, '21', '13', '07', 'UNICACHI');
INSERT INTO `ubigeo_inei` VALUES (1917, '22', '00', '00', 'SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1918, '22', '01', '00', 'MOYOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1919, '22', '01', '01', 'MOYOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1920, '22', '01', '02', 'CALZADA');
INSERT INTO `ubigeo_inei` VALUES (1921, '22', '01', '03', 'HABANA');
INSERT INTO `ubigeo_inei` VALUES (1922, '22', '01', '04', 'JEPELACIO');
INSERT INTO `ubigeo_inei` VALUES (1923, '22', '01', '05', 'SORITOR');
INSERT INTO `ubigeo_inei` VALUES (1924, '22', '01', '06', 'YANTALO');
INSERT INTO `ubigeo_inei` VALUES (1925, '22', '02', '00', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1926, '22', '02', '01', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1927, '22', '02', '02', 'ALTO BIAVO');
INSERT INTO `ubigeo_inei` VALUES (1928, '22', '02', '03', 'BAJO BIAVO');
INSERT INTO `ubigeo_inei` VALUES (1929, '22', '02', '04', 'HUALLAGA');
INSERT INTO `ubigeo_inei` VALUES (1930, '22', '02', '05', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (1931, '22', '02', '06', 'SAN RAFAEL');
INSERT INTO `ubigeo_inei` VALUES (1932, '22', '03', '00', 'EL DORADO');
INSERT INTO `ubigeo_inei` VALUES (1933, '22', '03', '01', 'SAN JOSE DE SISA');
INSERT INTO `ubigeo_inei` VALUES (1934, '22', '03', '02', 'AGUA BLANCA');
INSERT INTO `ubigeo_inei` VALUES (1935, '22', '03', '03', 'SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1936, '22', '03', '04', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1937, '22', '03', '05', 'SHATOJA');
INSERT INTO `ubigeo_inei` VALUES (1938, '22', '04', '00', 'HUALLAGA');
INSERT INTO `ubigeo_inei` VALUES (1939, '22', '04', '01', 'SAPOSOA');
INSERT INTO `ubigeo_inei` VALUES (1940, '22', '04', '02', 'ALTO SAPOSOA');
INSERT INTO `ubigeo_inei` VALUES (1941, '22', '04', '03', 'EL ESLABON');
INSERT INTO `ubigeo_inei` VALUES (1942, '22', '04', '04', 'PISCOYACU');
INSERT INTO `ubigeo_inei` VALUES (1943, '22', '04', '05', 'SACANCHE');
INSERT INTO `ubigeo_inei` VALUES (1944, '22', '04', '06', 'TINGO DE SAPOSOA');
INSERT INTO `ubigeo_inei` VALUES (1945, '22', '05', '00', 'LAMAS');
INSERT INTO `ubigeo_inei` VALUES (1946, '22', '05', '01', 'LAMAS');
INSERT INTO `ubigeo_inei` VALUES (1947, '22', '05', '02', 'ALONSO DE ALVARADO');
INSERT INTO `ubigeo_inei` VALUES (1948, '22', '05', '03', 'BARRANQUITA');
INSERT INTO `ubigeo_inei` VALUES (1949, '22', '05', '04', 'CAYNARACHI');
INSERT INTO `ubigeo_inei` VALUES (1950, '22', '05', '05', 'CUÑUMBUQUI');
INSERT INTO `ubigeo_inei` VALUES (1951, '22', '05', '06', 'PINTO RECODO');
INSERT INTO `ubigeo_inei` VALUES (1952, '22', '05', '07', 'RUMISAPA');
INSERT INTO `ubigeo_inei` VALUES (1953, '22', '05', '08', 'SAN ROQUE DE CUMBAZA');
INSERT INTO `ubigeo_inei` VALUES (1954, '22', '05', '09', 'SHANAO');
INSERT INTO `ubigeo_inei` VALUES (1955, '22', '05', '10', 'TABALOSOS');
INSERT INTO `ubigeo_inei` VALUES (1956, '22', '05', '11', 'ZAPATERO');
INSERT INTO `ubigeo_inei` VALUES (1957, '22', '06', '00', 'MARISCAL CACERES');
INSERT INTO `ubigeo_inei` VALUES (1958, '22', '06', '01', 'JUANJUI');
INSERT INTO `ubigeo_inei` VALUES (1959, '22', '06', '02', 'CAMPANILLA');
INSERT INTO `ubigeo_inei` VALUES (1960, '22', '06', '03', 'HUICUNGO');
INSERT INTO `ubigeo_inei` VALUES (1961, '22', '06', '04', 'PACHIZA');
INSERT INTO `ubigeo_inei` VALUES (1962, '22', '06', '05', 'PAJARILLO');
INSERT INTO `ubigeo_inei` VALUES (1963, '22', '07', '00', 'PICOTA');
INSERT INTO `ubigeo_inei` VALUES (1964, '22', '07', '01', 'PICOTA');
INSERT INTO `ubigeo_inei` VALUES (1965, '22', '07', '02', 'BUENOS AIRES');
INSERT INTO `ubigeo_inei` VALUES (1966, '22', '07', '03', 'CASPISAPA');
INSERT INTO `ubigeo_inei` VALUES (1967, '22', '07', '04', 'PILLUANA');
INSERT INTO `ubigeo_inei` VALUES (1968, '22', '07', '05', 'PUCACACA');
INSERT INTO `ubigeo_inei` VALUES (1969, '22', '07', '06', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (1970, '22', '07', '07', 'SAN HILARION');
INSERT INTO `ubigeo_inei` VALUES (1971, '22', '07', '08', 'SHAMBOYACU');
INSERT INTO `ubigeo_inei` VALUES (1972, '22', '07', '09', 'TINGO DE PONASA');
INSERT INTO `ubigeo_inei` VALUES (1973, '22', '07', '10', 'TRES UNIDOS');
INSERT INTO `ubigeo_inei` VALUES (1974, '22', '08', '00', 'RIOJA');
INSERT INTO `ubigeo_inei` VALUES (1975, '22', '08', '01', 'RIOJA');
INSERT INTO `ubigeo_inei` VALUES (1976, '22', '08', '02', 'AWAJUN');
INSERT INTO `ubigeo_inei` VALUES (1977, '22', '08', '03', 'ELIAS SOPLIN VARGAS');
INSERT INTO `ubigeo_inei` VALUES (1978, '22', '08', '04', 'NUEVA CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1979, '22', '08', '05', 'PARDO MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (1980, '22', '08', '06', 'POSIC');
INSERT INTO `ubigeo_inei` VALUES (1981, '22', '08', '07', 'SAN FERNANDO');
INSERT INTO `ubigeo_inei` VALUES (1982, '22', '08', '08', 'YORONGOS');
INSERT INTO `ubigeo_inei` VALUES (1983, '22', '08', '09', 'YURACYACU');
INSERT INTO `ubigeo_inei` VALUES (1984, '22', '09', '00', 'SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1985, '22', '09', '01', 'TARAPOTO');
INSERT INTO `ubigeo_inei` VALUES (1986, '22', '09', '02', 'ALBERTO LEVEAU');
INSERT INTO `ubigeo_inei` VALUES (1987, '22', '09', '03', 'CACATACHI');
INSERT INTO `ubigeo_inei` VALUES (1988, '22', '09', '04', 'CHAZUTA');
INSERT INTO `ubigeo_inei` VALUES (1989, '22', '09', '05', 'CHIPURANA');
INSERT INTO `ubigeo_inei` VALUES (1990, '22', '09', '06', 'EL PORVENIR');
INSERT INTO `ubigeo_inei` VALUES (1991, '22', '09', '07', 'HUIMBAYOC');
INSERT INTO `ubigeo_inei` VALUES (1992, '22', '09', '08', 'JUAN GUERRA');
INSERT INTO `ubigeo_inei` VALUES (1993, '22', '09', '09', 'LA BANDA DE SHILCAYO');
INSERT INTO `ubigeo_inei` VALUES (1994, '22', '09', '10', 'MORALES');
INSERT INTO `ubigeo_inei` VALUES (1995, '22', '09', '11', 'PAPAPLAYA');
INSERT INTO `ubigeo_inei` VALUES (1996, '22', '09', '12', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1997, '22', '09', '13', 'SAUCE');
INSERT INTO `ubigeo_inei` VALUES (1998, '22', '09', '14', 'SHAPAJA');
INSERT INTO `ubigeo_inei` VALUES (1999, '22', '10', '00', 'TOCACHE');
INSERT INTO `ubigeo_inei` VALUES (2000, '22', '10', '01', 'TOCACHE');
INSERT INTO `ubigeo_inei` VALUES (2001, '22', '10', '02', 'NUEVO PROGRESO');
INSERT INTO `ubigeo_inei` VALUES (2002, '22', '10', '03', 'POLVORA');
INSERT INTO `ubigeo_inei` VALUES (2003, '22', '10', '04', 'SHUNTE');
INSERT INTO `ubigeo_inei` VALUES (2004, '22', '10', '05', 'UCHIZA');
INSERT INTO `ubigeo_inei` VALUES (2005, '23', '00', '00', 'TACNA');
INSERT INTO `ubigeo_inei` VALUES (2006, '23', '01', '00', 'TACNA');
INSERT INTO `ubigeo_inei` VALUES (2007, '23', '01', '01', 'TACNA');
INSERT INTO `ubigeo_inei` VALUES (2008, '23', '01', '02', 'ALTO DE LA ALIANZA');
INSERT INTO `ubigeo_inei` VALUES (2009, '23', '01', '03', 'CALANA');
INSERT INTO `ubigeo_inei` VALUES (2010, '23', '01', '04', 'CIUDAD NUEVA');
INSERT INTO `ubigeo_inei` VALUES (2011, '23', '01', '05', 'INCLAN');
INSERT INTO `ubigeo_inei` VALUES (2012, '23', '01', '06', 'PACHIA');
INSERT INTO `ubigeo_inei` VALUES (2013, '23', '01', '07', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (2014, '23', '01', '08', 'POCOLLAY');
INSERT INTO `ubigeo_inei` VALUES (2015, '23', '01', '09', 'SAMA');
INSERT INTO `ubigeo_inei` VALUES (2016, '23', '01', '10', 'CORONEL GREGORIO ALBARRACÍN L');
INSERT INTO `ubigeo_inei` VALUES (2017, '23', '02', '00', 'CANDARAVE');
INSERT INTO `ubigeo_inei` VALUES (2018, '23', '02', '01', 'CANDARAVE');
INSERT INTO `ubigeo_inei` VALUES (2019, '23', '02', '02', 'CAIRANI');
INSERT INTO `ubigeo_inei` VALUES (2020, '23', '02', '03', 'CAMILACA');
INSERT INTO `ubigeo_inei` VALUES (2021, '23', '02', '04', 'CURIBAYA');
INSERT INTO `ubigeo_inei` VALUES (2022, '23', '02', '05', 'HUANUARA');
INSERT INTO `ubigeo_inei` VALUES (2023, '23', '02', '06', 'QUILAHUANI');
INSERT INTO `ubigeo_inei` VALUES (2024, '23', '03', '00', 'JORGE BASADRE');
INSERT INTO `ubigeo_inei` VALUES (2025, '23', '03', '01', 'LOCUMBA');
INSERT INTO `ubigeo_inei` VALUES (2026, '23', '03', '02', 'ILABAYA');
INSERT INTO `ubigeo_inei` VALUES (2027, '23', '03', '03', 'ITE');
INSERT INTO `ubigeo_inei` VALUES (2028, '23', '04', '00', 'TARATA');
INSERT INTO `ubigeo_inei` VALUES (2029, '23', '04', '01', 'TARATA');
INSERT INTO `ubigeo_inei` VALUES (2030, '23', '04', '02', 'CHUCATAMANI');
INSERT INTO `ubigeo_inei` VALUES (2031, '23', '04', '03', 'ESTIQUE');
INSERT INTO `ubigeo_inei` VALUES (2032, '23', '04', '04', 'ESTIQUE-PAMPA');
INSERT INTO `ubigeo_inei` VALUES (2033, '23', '04', '05', 'SITAJARA');
INSERT INTO `ubigeo_inei` VALUES (2034, '23', '04', '06', 'SUSAPAYA');
INSERT INTO `ubigeo_inei` VALUES (2035, '23', '04', '07', 'TARUCACHI');
INSERT INTO `ubigeo_inei` VALUES (2036, '23', '04', '08', 'TICACO');
INSERT INTO `ubigeo_inei` VALUES (2037, '24', '00', '00', 'TUMBES');
INSERT INTO `ubigeo_inei` VALUES (2038, '24', '01', '00', 'TUMBES');
INSERT INTO `ubigeo_inei` VALUES (2039, '24', '01', '01', 'TUMBES');
INSERT INTO `ubigeo_inei` VALUES (2040, '24', '01', '02', 'CORRALES');
INSERT INTO `ubigeo_inei` VALUES (2041, '24', '01', '03', 'LA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (2042, '24', '01', '04', 'PAMPAS DE HOSPITAL');
INSERT INTO `ubigeo_inei` VALUES (2043, '24', '01', '05', 'SAN JACINTO');
INSERT INTO `ubigeo_inei` VALUES (2044, '24', '01', '06', 'SAN JUAN DE LA VIRGEN');
INSERT INTO `ubigeo_inei` VALUES (2045, '24', '02', '00', 'CONTRALMIRANTE VILLAR');
INSERT INTO `ubigeo_inei` VALUES (2046, '24', '02', '01', 'ZORRITOS');
INSERT INTO `ubigeo_inei` VALUES (2047, '24', '02', '02', 'CASITAS');
INSERT INTO `ubigeo_inei` VALUES (2048, '24', '02', '03', 'CANOAS DE PUNTA SAL');
INSERT INTO `ubigeo_inei` VALUES (2049, '24', '03', '00', 'ZARUMILLA');
INSERT INTO `ubigeo_inei` VALUES (2050, '24', '03', '01', 'ZARUMILLA');
INSERT INTO `ubigeo_inei` VALUES (2051, '24', '03', '02', 'AGUAS VERDES');
INSERT INTO `ubigeo_inei` VALUES (2052, '24', '03', '03', 'MATAPALO');
INSERT INTO `ubigeo_inei` VALUES (2053, '24', '03', '04', 'PAPAYAL');
INSERT INTO `ubigeo_inei` VALUES (2054, '25', '00', '00', 'UCAYALI');
INSERT INTO `ubigeo_inei` VALUES (2055, '25', '01', '00', 'CORONEL PORTILLO');
INSERT INTO `ubigeo_inei` VALUES (2056, '25', '01', '01', 'CALLARIA');
INSERT INTO `ubigeo_inei` VALUES (2057, '25', '01', '02', 'CAMPOVERDE');
INSERT INTO `ubigeo_inei` VALUES (2058, '25', '01', '03', 'IPARIA');
INSERT INTO `ubigeo_inei` VALUES (2059, '25', '01', '04', 'MASISEA');
INSERT INTO `ubigeo_inei` VALUES (2060, '25', '01', '05', 'YARINACOCHA');
INSERT INTO `ubigeo_inei` VALUES (2061, '25', '01', '06', 'NUEVA REQUENA');
INSERT INTO `ubigeo_inei` VALUES (2062, '25', '01', '07', 'MANANTAY');
INSERT INTO `ubigeo_inei` VALUES (2063, '25', '02', '00', 'ATALAYA');
INSERT INTO `ubigeo_inei` VALUES (2064, '25', '02', '01', 'RAYMONDI');
INSERT INTO `ubigeo_inei` VALUES (2065, '25', '02', '02', 'SEPAHUA');
INSERT INTO `ubigeo_inei` VALUES (2066, '25', '02', '03', 'TAHUANIA');
INSERT INTO `ubigeo_inei` VALUES (2067, '25', '02', '04', 'YURUA');
INSERT INTO `ubigeo_inei` VALUES (2068, '25', '03', '00', 'PADRE ABAD');
INSERT INTO `ubigeo_inei` VALUES (2069, '25', '03', '01', 'PADRE ABAD');
INSERT INTO `ubigeo_inei` VALUES (2070, '25', '03', '02', 'IRAZOLA');
INSERT INTO `ubigeo_inei` VALUES (2071, '25', '03', '03', 'CURIMANA');
INSERT INTO `ubigeo_inei` VALUES (2072, '25', '04', '00', 'PURUS');
INSERT INTO `ubigeo_inei` VALUES (2073, '25', '04', '01', 'PURUS');
INSERT INTO `ubigeo_inei` VALUES (2074, '99', '00', '00', 'EXTRANJERO');
INSERT INTO `ubigeo_inei` VALUES (2075, '99', '99', '00', 'EXTRANJERO');
INSERT INTO `ubigeo_inei` VALUES (2076, '99', '99', '99', 'EXTRANJERO');

-- ----------------------------
-- Table structure for usuarios
-- ----------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios`  (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `id_empresa` int NULL DEFAULT NULL,
  `id_rol` int NULL DEFAULT NULL,
  `num_doc` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `usuario` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `clave` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombres` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `apellidos` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `rubro` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `telefono` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `token_reset` varchar(130) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  `mensaje` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `rotativo` smallint NULL DEFAULT 0,
  `fecha_inicio` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `funciones` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `id_ruta` int NULL DEFAULT NULL,
  `available_status` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL COMMENT 'Laravel Auth token',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`usuario_id`) USING BTREE,
  INDEX `id_empresa`(`id_empresa` ASC) USING BTREE,
  INDEX `id_rol`(`id_rol` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 107 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of usuarios
-- ----------------------------

-- ----------------------------
-- Table structure for venta_adicional
-- ----------------------------
DROP TABLE IF EXISTS `venta_adicional`;
CREATE TABLE `venta_adicional`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_venta` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `cuota` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `porcentaje` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `monto` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `neto` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `leyenda` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `cuenta` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `bien` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `medio` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of venta_adicional
-- ----------------------------

-- ----------------------------
-- Table structure for venta_anexo
-- ----------------------------
DROP TABLE IF EXISTS `venta_anexo`;
CREATE TABLE `venta_anexo`  (
  `idventa` int NOT NULL,
  `texto` varchar(245) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`idventa`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of venta_anexo
-- ----------------------------

-- ----------------------------
-- Table structure for venta_cuotas
-- ----------------------------
DROP TABLE IF EXISTS `venta_cuotas`;
CREATE TABLE `venta_cuotas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_venta` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `ncuota` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `fecha` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `monto` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of venta_cuotas
-- ----------------------------

-- ----------------------------
-- Table structure for ventas
-- ----------------------------
DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas`  (
  `id_venta` int NOT NULL AUTO_INCREMENT,
  `id_tido` int NOT NULL,
  `id_tipo_pago` int NULL DEFAULT NULL,
  `fecha_emision` date NULL DEFAULT NULL,
  `fecha_vencimiento` date NULL DEFAULT NULL,
  `dias_pagos` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `serie` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `id_cliente` int NOT NULL,
  `total` double(10, 2) NULL DEFAULT NULL,
  `subtotal` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `enviado_sunat` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `apli_igv` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  `observacion` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `igv` double(10, 2) NULL DEFAULT 0.18,
  `medoto_pago_id` int NULL DEFAULT NULL,
  `pagado` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `is_segun_pago` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `medoto_pago2_id` int NULL DEFAULT NULL,
  `pagado2` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `moneda` int NULL DEFAULT 1,
  `cm_tc` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_coti` int NULL DEFAULT NULL,
  `id_vendedor` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_venta`) USING BTREE,
  INDEX `fk_ventas_documentos_sunat1_idx`(`id_tido` ASC) USING BTREE,
  INDEX `fk_ventas_clientes1_idx`(`id_cliente` ASC) USING BTREE,
  INDEX `fk_ventas_empresas1_idx`(`id_empresa` ASC) USING BTREE,
  INDEX `id_tipo_pago`(`id_tipo_pago` ASC) USING BTREE,
  INDEX `medoto_pago_id`(`medoto_pago_id` ASC) USING BTREE,
  INDEX `idx_ventas_emp_suc_estado`(`id_empresa` ASC, `sucursal` ASC, `estado` ASC, `fecha_emision` ASC) USING BTREE,
  INDEX `idx_ventas_cliente`(`id_cliente` ASC) USING BTREE,
  INDEX `idx_ventas_vendedor`(`id_vendedor` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 237 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas
-- ----------------------------
INSERT INTO `ventas` VALUES (229, 6, 2, '2026-03-28', '2026-03-28', '', 'MDO SAN PEDRO PTO 4', 'NV01', 2945, 1239, 1500.00, 0.00, '2', '0', 12, 1, '1', '', 0.18, 12, '', '1', 12, '', 1, '', 47127, 40);
INSERT INTO `ventas` VALUES (231, 6, 2, '2026-04-16', '2026-04-16', '', 'RIMAC', 'NV01', 2945, 2497, 340.00, 0.00, '2', '0', 12, 1, '1', '', 0.18, 12, '', '1', 12, '', 1, '', 49252, 40);
INSERT INTO `ventas` VALUES (233, 1, 2, '2026-06-25', '2026-06-25', '', '1', 'B001', 596, 1763, 2288.30, 0.00, '1', '0', 12, 1, '1', 'Convertido de cotización N° 46559', 349.06, NULL, '0', NULL, NULL, NULL, 1, NULL, 48324, 40);
INSERT INTO `ventas` VALUES (234, 1, 2, '2026-06-26', '2026-06-26', '', '1', 'B001', 597, 4, 291.10, 0.00, '1', '0', 12, 1, '1', 'Convertido de cotización N° 39032', 44.41, NULL, '0', NULL, NULL, NULL, 1, NULL, 40777, 40);
INSERT INTO `ventas` VALUES (236, 1, 1, '2026-06-26', '2026-06-26', NULL, '-', 'B001', 598, 2516, 74.00, 62.71, '1', '0', 12, 1, '1', NULL, 0.18, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 40);

-- ----------------------------
-- Table structure for ventas_pagos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pagos`;
CREATE TABLE `ventas_pagos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_venta` int NULL DEFAULT NULL,
  `metodo_pago` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `monto` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `npago` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas_pagos
-- ----------------------------

-- ----------------------------
-- Table structure for ventas_referencias
-- ----------------------------
DROP TABLE IF EXISTS `ventas_referencias`;
CREATE TABLE `ventas_referencias`  (
  `id_venta` int NOT NULL,
  `id_referencia` int NOT NULL,
  `id_motivo` int NOT NULL,
  PRIMARY KEY (`id_venta`) USING BTREE,
  INDEX `fk_ventas_referencias_ventas2_idx`(`id_referencia` ASC) USING BTREE,
  INDEX `fk_ventas_referencias_motivo_documento1_idx`(`id_motivo` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas_referencias
-- ----------------------------

-- ----------------------------
-- Table structure for ventas_servicios
-- ----------------------------
DROP TABLE IF EXISTS `ventas_servicios`;
CREATE TABLE `ventas_servicios`  (
  `id_venta` int NOT NULL,
  `id_item` int NOT NULL,
  `descripcion` varchar(245) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `monto` double(8, 2) NOT NULL,
  `cantidad` double(9, 2) NOT NULL,
  `codsunat` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id_venta`, `id_item`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas_servicios
-- ----------------------------

-- ----------------------------
-- Table structure for ventas_sunat
-- ----------------------------
DROP TABLE IF EXISTS `ventas_sunat`;
CREATE TABLE `ventas_sunat`  (
  `id_venta` int NOT NULL AUTO_INCREMENT,
  `hash` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `qr_data` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_venta`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 232 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas_sunat
-- ----------------------------
INSERT INTO `ventas_sunat` VALUES (229, '-', '-', '-');
INSERT INTO `ventas_sunat` VALUES (231, '-', '-', '-');

-- ----------------------------
-- Table structure for view_cotizaciones
-- ----------------------------
DROP TABLE IF EXISTS `view_cotizaciones`;
CREATE TABLE `view_cotizaciones`  (
  `cotizacion_id` int NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `moneda` int NULL DEFAULT NULL,
  `cm_tc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_tido` int NULL DEFAULT NULL,
  `documento` varchar(259) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `datos` varchar(245) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `total` double(10, 2) NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `vendedor` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `usuario` int NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of view_cotizaciones
-- ----------------------------

-- ----------------------------
-- Table structure for view_cotizaciones2
-- ----------------------------
DROP TABLE IF EXISTS `view_cotizaciones2`;
CREATE TABLE `view_cotizaciones2`  (
  `cotizacion_id` int NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `moneda` int NULL DEFAULT NULL,
  `cm_tc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_tido` int NULL DEFAULT NULL,
  `documento` varchar(259) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `datos` varchar(245) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `total` double(10, 2) NULL DEFAULT NULL,
  `subtotal` varchar(417) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `igv` varchar(417) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `vendedor` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `usuario` int NULL DEFAULT NULL,
  `fecha_registro` datetime NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of view_cotizaciones2
-- ----------------------------

-- ----------------------------
-- Table structure for view_productos_1
-- ----------------------------
DROP TABLE IF EXISTS `view_productos_1`;
CREATE TABLE `view_productos_1`  (
  `id_producto` int NULL DEFAULT NULL,
  `cod_barra` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `descripcion` varchar(245) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `precio` double(10, 4) NULL DEFAULT NULL,
  `costo` double(10, 4) NULL DEFAULT NULL,
  `cantidad` int NULL DEFAULT NULL,
  `iscbp` int NULL DEFAULT NULL,
  `id_empresa` int NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `ultima_salida` date NULL DEFAULT NULL,
  `codsunat` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `usar_barra` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `precio_mayor` double(10, 4) NULL DEFAULT NULL,
  `precio_menor` double(10, 4) NULL DEFAULT NULL,
  `razon_social` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ruc` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `almacen` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `precio2` double(10, 4) NULL DEFAULT NULL,
  `precio3` double(10, 4) NULL DEFAULT NULL,
  `precio4` double(10, 4) NULL DEFAULT NULL,
  `precio_unidad` double(10, 4) NULL DEFAULT NULL,
  `codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `activo` int NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of view_productos_1
-- ----------------------------

-- ----------------------------
-- Table structure for view_productos_2
-- ----------------------------
DROP TABLE IF EXISTS `view_productos_2`;
CREATE TABLE `view_productos_2`  (
  `id_producto` int NULL DEFAULT NULL,
  `cod_barra` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `descripcion` varchar(245) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `precio` double(10, 4) NULL DEFAULT NULL,
  `costo` double(10, 4) NULL DEFAULT NULL,
  `cantidad` int NULL DEFAULT NULL,
  `iscbp` int NULL DEFAULT NULL,
  `id_empresa` int NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `ultima_salida` date NULL DEFAULT NULL,
  `codsunat` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `usar_barra` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `precio_mayor` double(10, 4) NULL DEFAULT NULL,
  `precio_menor` double(10, 4) NULL DEFAULT NULL,
  `razon_social` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ruc` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `almacen` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `precio2` double(10, 4) NULL DEFAULT NULL,
  `precio3` double(10, 4) NULL DEFAULT NULL,
  `precio4` double(10, 4) NULL DEFAULT NULL,
  `precio_unidad` double(10, 4) NULL DEFAULT NULL,
  `codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `activo` int NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of view_productos_2
-- ----------------------------

-- ----------------------------
-- Table structure for view_ventas
-- ----------------------------
DROP TABLE IF EXISTS `view_ventas`;
CREATE TABLE `view_ventas`  (
  `cod_v` int NULL DEFAULT NULL,
  `sn_v` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `datos_cl` varchar(259) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `subtotal` varchar(17) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `igv_v` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `doc_ventae` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_venta` varchar(58) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fecha_emision` date NULL DEFAULT NULL,
  `abreviatura` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `apli_igv` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `igv` double(10, 2) NULL DEFAULT NULL,
  `id_tido` int NULL DEFAULT NULL,
  `serie` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `documento` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `datos` varchar(245) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `total` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `enviado_sunat` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of view_ventas
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
