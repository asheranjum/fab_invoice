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

 Date: 03/04/2025 10:03:40
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
) ENGINE = MyISAM AUTO_INCREMENT = 477 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoice_items
-- ----------------------------
INSERT INTO `invoice_items` VALUES (467, 19, '3', '1', '5', 'DISAS+', 3.00, '1', '2025-03-31', '2025-03-31 06:58:44', '2025-03-31 06:58:44');
INSERT INTO `invoice_items` VALUES (466, 19, '3', '3', '4', 'DISAS+', 3.00, '1', '2025-03-31', '2025-03-31 06:58:44', '2025-03-31 06:58:44');
INSERT INTO `invoice_items` VALUES (465, 19, '2', '2', '2', 'ASSEM+', 22.00, '2', '2025-03-31', '2025-03-31 06:58:02', '2025-03-31 06:58:02');
INSERT INTO `invoice_items` VALUES (468, 20, 'Name1', '001', '2', 'DELIV+', 20.00, '11101', '2025-04-01', '2025-04-02 05:02:31', '2025-04-02 05:02:31');
INSERT INTO `invoice_items` VALUES (469, 20, 'Name4', '003', '3', 'DELIV+', 4.00, '02225', '2025-04-25', '2025-04-02 05:02:31', '2025-04-02 05:15:00');
INSERT INTO `invoice_items` VALUES (470, 20, 'Name2', '002', '4', 'RUB+', 4.00, '02225', '2025-04-25', '2025-04-02 05:02:31', '2025-04-02 05:15:00');
INSERT INTO `invoice_items` VALUES (471, 20, 'Name23', '005', '6', 'ASSEM+', 3.00, '02225', '2025-04-25', '2025-04-02 05:14:12', '2025-04-02 05:15:00');
INSERT INTO `invoice_items` VALUES (472, 20, 'Name1', '001', '2', 'BRTRANS+', 3.00, '11101', '2025-04-01', '2025-04-02 05:14:46', '2025-04-02 05:14:46');
INSERT INTO `invoice_items` VALUES (473, 21, 'abc', '003', '2', 'DELIV+', 44.00, '222', '2025-04-02', '2025-04-02 05:15:59', '2025-04-02 05:15:59');
INSERT INTO `invoice_items` VALUES (474, 21, 'abc', '003', '2', 'RUB+', 44.00, '222', '2025-04-02', '2025-04-02 05:15:59', '2025-04-02 05:15:59');
INSERT INTO `invoice_items` VALUES (475, 21, 'bbb', '243', '3', 'DISAS+', 44.00, '222', '2025-04-02', '2025-04-02 05:15:59', '2025-04-02 05:15:59');
INSERT INTO `invoice_items` VALUES (476, 21, '66', '66', '5', 'ASSEM+', 66.00, '5', '2025-04-02', '2025-04-02 05:16:38', '2025-04-02 05:16:38');

-- ----------------------------
-- Table structure for invoices
-- ----------------------------
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date` date NOT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `abn` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `runsheet_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `runsheet_date` date NULL DEFAULT NULL,
  `sub_total` decimal(10, 2) NULL DEFAULT NULL,
  `tax_rate` decimal(5, 2) NULL DEFAULT NULL,
  `other_cost` decimal(10, 2) NULL DEFAULT NULL,
  `total_cost` decimal(10, 2) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoices
-- ----------------------------
INSERT INTO `invoices` VALUES (21, '10002', '2025-04-26', 'C Name', 'A Name', '030303', 'Abn', NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-02 05:15:11', '2025-04-02 05:16:10', '');
INSERT INTO `invoices` VALUES (20, '10001', '2025-04-01', 'BBB', 'BBB', 'BB', 'BBB', '', NULL, 28.00, 0.00, 0.00, 28.00, '2025-04-02 05:02:31', '2025-04-02 05:15:20', '');

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
