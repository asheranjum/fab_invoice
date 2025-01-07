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

 Date: 07/01/2025 04:47:14
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for invoice
-- ----------------------------
DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice`  (
  `invoice_id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `invoice` int NOT NULL,
  `company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `abn` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `runsheet` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(20, 2) NOT NULL,
  `sub_total` decimal(20, 2) NOT NULL,
  `tax_rate` decimal(10, 2) NULL DEFAULT 0.00,
  `other_cost` decimal(20, 2) NULL DEFAULT 0.00,
  `total_cost` decimal(20, 2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`invoice_id`) USING BTREE,
  UNIQUE INDEX `invoice`(`invoice` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoice
-- ----------------------------
INSERT INTO `invoice` VALUES (43, '2025-01-07', 10001, 'Test Company', 'Test Address', '03082826308', '75760', '54321', '4321', 0.00, 1350.00, 200.00, 100.00, 1650.00, '2025-01-07 04:10:19', '2025-01-07 04:10:19');

-- ----------------------------
-- Table structure for invoice_items
-- ----------------------------
DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE `invoice_items`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `customer_invoice_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `item_row_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `item_value` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `invoice_id`(`invoice_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoice_items
-- ----------------------------
INSERT INTO `invoice_items` VALUES (57, 43, '10001-1', '43~1', 'DELIV', 400.00);
INSERT INTO `invoice_items` VALUES (58, 43, '10001-1', '43~1', 'ASSEM', 200.00);
INSERT INTO `invoice_items` VALUES (59, 43, '10001-1', '43~1', 'RUB', 600.00);
INSERT INTO `invoice_items` VALUES (60, 43, '10001-2', '43~2', 'DELIV', 30.00);
INSERT INTO `invoice_items` VALUES (61, 43, '10001-2', '43~2', 'ASSEM', 50.00);
INSERT INTO `invoice_items` VALUES (62, 43, '10001-2', '43~2', 'RUB', 70.00);

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Invoice.FabTransport.aU', '$2y$10$ClzlshwDF7htWBdIXxHTeOzBIXrT15Wy/jpG/9ZVwP8NM6Fl9tPNO', '2024-12-06 21:19:22');

SET FOREIGN_KEY_CHECKS = 1;
