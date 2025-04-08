/*
 Navicat Premium Data Transfer

 Source Server         : LocalHost
 Source Server Type    : MySQL
 Source Server Version : 80300
 Source Host           : localhost:3306
 Source Schema         : fab_inv

 Target Server Type    : MySQL
 Target Server Version : 80300
 File Encoding         : 65001

 Date: 08/04/2025 04:06:27
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for invoice_items
-- ----------------------------
DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE `invoice_items`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NULL DEFAULT NULL,
  `customer_invoice_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `customer_invoice_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `item_row_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `item_value` decimal(10, 2) NULL DEFAULT NULL,
  `runsheet_number` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `runsheet_date` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `invoice_id`(`invoice_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 503 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoice_items
-- ----------------------------
INSERT INTO `invoice_items` VALUES (580, 25, '655', '65', '20', 'RUB+', 444.00, '876543', '2025-04-09', '2025-04-08 02:11:19', '2025-04-08 02:11:19');
INSERT INTO `invoice_items` VALUES (578, 25, '655', '65', '20', 'DISAS+', 5.00, '876543', '2025-04-09', '2025-04-08 02:10:45', '2025-04-08 02:10:45');
INSERT INTO `invoice_items` VALUES (577, 25, '456', '765', '19', 'DOWNST+', 5.00, '876543', '2025-04-09', '2025-04-08 02:10:45', '2025-04-08 02:10:45');
INSERT INTO `invoice_items` VALUES (576, 25, '968', '46546', '17', 'PREM+', 33.00, '876543', '2025-04-09', '2025-04-08 02:10:13', '2025-04-08 02:10:13');
INSERT INTO `invoice_items` VALUES (575, 25, '968', '46546', '17', 'RUB+', 33.00, '876543', '2025-04-09', '2025-04-08 02:10:13', '2025-04-08 02:10:13');
INSERT INTO `invoice_items` VALUES (574, 25, '968', '46546', '17', 'DELIV+', 33.00, '876543', '2025-04-09', '2025-04-08 02:10:13', '2025-04-08 02:10:13');
INSERT INTO `invoice_items` VALUES (573, 25, 'a', '4', '4', 'DOWNST+', 44.00, '001', '2025-04-05', '2025-04-08 02:01:37', '2025-04-08 02:01:37');
INSERT INTO `invoice_items` VALUES (572, 25, '354534', '35345', '15', 'RUB+', 44.00, '001', '2025-04-05', '2025-04-08 02:01:19', '2025-04-08 02:01:19');
INSERT INTO `invoice_items` VALUES (571, 25, '3354', '56', '14', 'RUB+', 44.00, '001', '2025-04-05', '2025-04-08 02:01:19', '2025-04-08 02:01:19');
INSERT INTO `invoice_items` VALUES (570, 25, 'bbb', '55', '12', 'DELIV+', 55.00, '001', '2025-04-05', '2025-04-08 02:00:47', '2025-04-08 02:00:47');
INSERT INTO `invoice_items` VALUES (564, 25, '34545', '345435', '10', 'ASSEM+', 4.00, '001', '2025-04-05', '2025-04-08 01:25:20', '2025-04-08 01:25:20');
INSERT INTO `invoice_items` VALUES (563, 25, '34545', '345435', '10', 'DISAS+', 4.00, '001', '2025-04-05', '2025-04-08 01:25:20', '2025-04-08 01:25:20');
INSERT INTO `invoice_items` VALUES (562, 25, '33', '033', '8', 'RUB+', 33.00, '001', '2025-04-05', '2025-04-08 01:25:20', '2025-04-08 01:25:20');
INSERT INTO `invoice_items` VALUES (561, 25, '33', '033', '8', 'RUB+', 33.00, '001', '2025-04-05', '2025-04-08 01:25:05', '2025-04-08 01:25:05');
INSERT INTO `invoice_items` VALUES (560, 25, '33', '033', '8', 'ASSEM+', 33.00, '001', '2025-04-05', '2025-04-08 01:24:48', '2025-04-08 01:24:48');
INSERT INTO `invoice_items` VALUES (538, 25, '33', '033', '8', 'DISAS+', 3.00, '001', '2025-04-05', '2025-04-08 00:49:28', '2025-04-08 00:49:28');
INSERT INTO `invoice_items` VALUES (537, 25, 'c', '3', '6', 'DELIV+', 3.00, '001', '2025-04-05', '2025-04-08 00:48:57', '2025-04-08 00:48:57');
INSERT INTO `invoice_items` VALUES (536, 25, 'b', '8', '5', 'DELIV+', 2.00, '001', '2025-04-05', '2025-04-08 00:48:57', '2025-04-08 00:48:57');
INSERT INTO `invoice_items` VALUES (535, 25, 'a', '4', '4', 'DELIV+', 1.00, '001', '2025-04-05', '2025-04-08 00:48:57', '2025-04-08 00:48:57');
INSERT INTO `invoice_items` VALUES (502, 25, 'inv1', '02', '2', 'UPST+', 22.00, '001', '2025-04-05', '2025-04-05 14:57:54', '2025-04-05 14:57:54');
INSERT INTO `invoice_items` VALUES (500, 25, 'inv1', '02', '2', 'ASSEM+', 1.00, '001', '2025-04-05', '2025-04-05 14:57:25', '2025-04-05 14:57:25');
INSERT INTO `invoice_items` VALUES (501, 26, 'elec-inv', '0333', '2', 'DELIV+', 11.00, '333', '2025-04-05', '2025-04-05 14:57:37', '2025-04-05 14:57:37');
INSERT INTO `invoice_items` VALUES (499, 26, 'elec-inv', '0333', '2', 'P/UP(7)', 10.00, '333', '2025-04-05', '2025-04-05 14:44:34', '2025-04-05 14:44:34');
INSERT INTO `invoice_items` VALUES (498, 26, 'elec-inv', '0333', '2', 'WATERCON+', 20.00, '333', '2025-04-05', '2025-04-05 14:44:34', '2025-04-05 14:44:34');
INSERT INTO `invoice_items` VALUES (497, 26, 'elec-inv', '0333', '2', 'RELO+', 30.00, '333', '2025-04-05', '2025-04-05 14:44:34', '2025-04-05 14:44:34');
INSERT INTO `invoice_items` VALUES (496, 25, 'inv1', '02', '2', 'DISAS+', 20.00, '001', '2025-04-05', '2025-04-05 14:41:04', '2025-04-05 14:41:04');
INSERT INTO `invoice_items` VALUES (495, 25, 'inv1', '02', '2', 'DELIV+', 10.00, '001', '2025-04-05', '2025-04-05 14:41:04', '2025-04-05 14:41:04');

-- ----------------------------
-- Table structure for invoices
-- ----------------------------
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `invoice_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `date` date NOT NULL,
  `company_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `abn` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `runsheet_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `runsheet_date` date NULL DEFAULT NULL,
  `sub_total` decimal(10, 2) NULL DEFAULT NULL,
  `tax_rate` decimal(5, 2) NULL DEFAULT NULL,
  `other_cost` decimal(10, 2) NULL DEFAULT NULL,
  `total_cost` decimal(10, 2) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 34 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoices
-- ----------------------------
INSERT INTO `invoices` VALUES (25, '10001', '', '2025-04-05', 'Bedding INv', '11122', '111', '11', '', NULL, 30.00, 0.00, 0.00, 30.00, '2025-04-05 14:41:04', '2025-04-08 01:24:48', '');
INSERT INTO `invoices` VALUES (26, '10002', 'Electric', '2025-04-05', 'electric inv', 'electric inv', '222', '222', '', NULL, 60.00, 0.00, 0.00, 60.00, '2025-04-05 14:44:34', '2025-04-05 14:51:00', '');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Invoice.FabTransport.aU', '$2y$10$ClzlshwDF7htWBdIXxHTeOzBIXrT15Wy/jpG/9ZVwP8NM6Fl9tPNO', '2024-12-06 21:19:22');
INSERT INTO `users` VALUES (2, 'Invoice.FabTransport.aU4444', '$2y$10$j35N4R6VKr8XcXkkJjlWQ.rhiG73M.k8n0tI47nGZxUl4GFG10O2C', '2025-04-07 01:29:10');
INSERT INTO `users` VALUES (3, 'Invoice.FabTransport.aU4444', '$2y$10$P2dXGxbyM6nA6xUyX22fLunnnkgGNhwl4VZqQTh3Ikum7KWafwYHm', '2025-04-07 01:29:14');

SET FOREIGN_KEY_CHECKS = 1;
