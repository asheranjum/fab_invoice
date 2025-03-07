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

 Date: 07/03/2025 02:55:40
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
) ENGINE = InnoDB AUTO_INCREMENT = 79 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoice
-- ----------------------------
INSERT INTO `invoice` VALUES (73, '2025-03-01', 10001, 'Test', 'Test', 'Test', '', 'Test', 'Test', 0.00, 580.00, 0.00, 0.00, 580.00, '2025-03-01 13:51:39', '2025-03-01 13:51:39');
INSERT INTO `invoice` VALUES (74, '2025-03-08', 10002, 'COMPANY NAME:333', 'ADDREESS:', 'PHONE N', '', 'ABN', 'RUNSHEET', 0.00, 107.00, 0.00, 0.00, 107.00, '2025-03-01 22:53:22', '2025-03-06 13:34:58');
INSERT INTO `invoice` VALUES (75, '2025-03-07', 10003, 'asher_designs', 'Sec 9 Block D', '3082826308', '', '222', '4444', 0.00, 163.00, 0.00, 0.00, 163.00, '2025-03-02 05:03:21', '2025-03-02 05:03:21');
INSERT INTO `invoice` VALUES (79, '2025-03-08', 10004, 'bbbb', 'vvv', '333', '', '33', '23', 0.00, 107.00, 0.00, 0.00, 107.00, '2025-03-06 14:04:44', '2025-03-07 01:58:34');
INSERT INTO `invoice` VALUES (80, '2025-03-07', 10005, '333', '333', '333', '', '33', '33', 0.00, 66.00, 0.00, 0.00, 66.00, '2025-03-07 02:54:19', '2025-03-07 02:54:19');

-- ----------------------------
-- Table structure for invoice_items
-- ----------------------------
DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE `invoice_items`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `customer_invoice_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `customer_invoice_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `item_row_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `item_value` decimal(10, 2) NOT NULL,
  `runsheet_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `runsheet_date` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `invoice_id`(`invoice_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 497 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoice_items
-- ----------------------------
INSERT INTO `invoice_items` VALUES (399, 73, NULL, '11111', '73~1', 'DELIV+', 22.00, '', '');
INSERT INTO `invoice_items` VALUES (398, 72, NULL, '4', '72~2', 'P/UP(1)', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (397, 72, NULL, '4', '72~2', 'DOWNST+', 55.00, '', '');
INSERT INTO `invoice_items` VALUES (396, 72, NULL, '4', '72~2', 'UPST+', 88.00, '', '');
INSERT INTO `invoice_items` VALUES (395, 72, NULL, '4', '72~2', 'RUB+', 77.00, '', '');
INSERT INTO `invoice_items` VALUES (394, 72, NULL, '4', '72~2', 'ASSEM+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (393, 72, NULL, '4', '72~2', 'DISAS+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (392, 72, NULL, '4', '72~2', 'DELIV+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (391, 72, NULL, '44', '72~1', 'P/UP(1)', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (390, 72, NULL, '44', '72~1', 'DOOR/R+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (389, 72, NULL, '44', '72~1', 'WATERCON+', 6.00, '', '');
INSERT INTO `invoice_items` VALUES (388, 72, NULL, '44', '72~1', 'VOL+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (387, 72, NULL, '44', '72~1', 'H/DLIV+', 6.00, '', '');
INSERT INTO `invoice_items` VALUES (386, 72, NULL, '44', '72~1', 'INST+', 8.00, '', '');
INSERT INTO `invoice_items` VALUES (385, 72, NULL, '44', '72~1', 'BRTRANS+', 9.00, '', '');
INSERT INTO `invoice_items` VALUES (384, 72, NULL, '44', '72~1', 'PREM+', 9.00, '', '');
INSERT INTO `invoice_items` VALUES (383, 72, NULL, '44', '72~1', 'DOWNST+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (382, 72, NULL, '44', '72~1', 'UPST+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (381, 72, NULL, '44', '72~1', 'RUB+', 44.00, '', '');
INSERT INTO `invoice_items` VALUES (380, 72, NULL, '44', '72~1', 'ASSEM+', 33.00, '', '');
INSERT INTO `invoice_items` VALUES (379, 72, NULL, '44', '72~1', 'DISAS+', 33.00, '', '');
INSERT INTO `invoice_items` VALUES (378, 72, NULL, '44', '72~1', 'DELIV+', 333.00, '', '');
INSERT INTO `invoice_items` VALUES (377, 71, NULL, '1', '71~2', 'ASSEM+', 2.00, '111', '111111');
INSERT INTO `invoice_items` VALUES (376, 71, NULL, '1', '71~2', 'DISAS+', 2.00, '111', '111111');
INSERT INTO `invoice_items` VALUES (375, 71, NULL, '1', '71~2', 'DELIV+', 1.00, '111', '111111');
INSERT INTO `invoice_items` VALUES (374, 71, NULL, '2', '71~1', 'ASSEM+', 2.00, '111', '111');
INSERT INTO `invoice_items` VALUES (373, 71, NULL, '2', '71~1', 'DISAS+', 2.00, '111', '111');
INSERT INTO `invoice_items` VALUES (372, 71, NULL, '2', '71~1', 'DELIV+', 2.00, '111', '111');
INSERT INTO `invoice_items` VALUES (400, 73, NULL, '11111', '73~1', 'DISAS+', 22.00, '', '');
INSERT INTO `invoice_items` VALUES (401, 73, NULL, '11111', '73~1', 'ASSEM+', 22.00, '', '');
INSERT INTO `invoice_items` VALUES (402, 73, NULL, '11111', '73~1', 'RUB+', 22.00, '', '');
INSERT INTO `invoice_items` VALUES (403, 73, NULL, '11111', '73~1', 'UPST+', 22.00, '', '');
INSERT INTO `invoice_items` VALUES (404, 73, NULL, '11111', '73~1', 'DOWNST+', 10.00, '', '');
INSERT INTO `invoice_items` VALUES (405, 73, NULL, '11111', '73~1', 'PREM+', 10.00, '', '');
INSERT INTO `invoice_items` VALUES (406, 73, NULL, '11111', '73~1', 'BRTRANS+', 10.00, '', '');
INSERT INTO `invoice_items` VALUES (407, 73, NULL, '11111', '73~1', 'INST+', 10.00, '', '');
INSERT INTO `invoice_items` VALUES (408, 73, NULL, '11111', '73~1', 'H/DLIV+', 10.00, '', '');
INSERT INTO `invoice_items` VALUES (409, 73, NULL, '11111', '73~1', 'VOL+', 10.00, '', '');
INSERT INTO `invoice_items` VALUES (410, 73, NULL, '11111', '73~1', 'WATERCON+', 10.00, '', '');
INSERT INTO `invoice_items` VALUES (411, 73, NULL, '11111', '73~1', 'DOOR/R+', 10.00, '', '');
INSERT INTO `invoice_items` VALUES (412, 73, NULL, '11111', '73~1', 'P/UP(2)', 10.00, '', '');
INSERT INTO `invoice_items` VALUES (413, 73, NULL, '2222', '73~2', 'DELIV+', 8.00, '', '');
INSERT INTO `invoice_items` VALUES (414, 73, NULL, '2222', '73~2', 'DISAS+', 2.00, '', '');
INSERT INTO `invoice_items` VALUES (415, 73, NULL, '2222', '73~2', 'ASSEM+', 5.00, '', '');
INSERT INTO `invoice_items` VALUES (416, 73, NULL, '2222', '73~2', 'RUB+', 2.00, '', '');
INSERT INTO `invoice_items` VALUES (417, 73, NULL, '2222', '73~2', 'UPST+', 3.00, '', '');
INSERT INTO `invoice_items` VALUES (418, 73, NULL, '2222', '73~2', 'DOWNST+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (419, 73, NULL, '2222', '73~2', 'PREM+', 5.00, '', '');
INSERT INTO `invoice_items` VALUES (420, 73, NULL, '2222', '73~2', 'BRTRANS+', 6.00, '', '');
INSERT INTO `invoice_items` VALUES (421, 73, NULL, '2222', '73~2', 'INST+', 9.00, '', '');
INSERT INTO `invoice_items` VALUES (422, 73, NULL, '2222', '73~2', 'H/DLIV+', 8.00, '', '');
INSERT INTO `invoice_items` VALUES (423, 73, NULL, '2222', '73~2', 'VOL+', 7.00, '', '');
INSERT INTO `invoice_items` VALUES (424, 73, NULL, '2222', '73~2', 'WATERCON+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (425, 73, NULL, '2222', '73~2', 'DOOR/R+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (426, 73, NULL, '2222', '73~2', 'P/UP(4)', 10.00, '', '');
INSERT INTO `invoice_items` VALUES (427, 73, NULL, '35454', '73~3', 'DELIV+', 4.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (428, 73, NULL, '35454', '73~3', 'DISAS+', 4.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (429, 73, NULL, '35454', '73~3', 'ASSEM+', 6.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (430, 73, NULL, '35454', '73~3', 'RUB+', 7.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (431, 73, NULL, '35454', '73~3', 'UPST+', 8.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (432, 73, NULL, '35454', '73~3', 'DOWNST+', 3.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (433, 73, NULL, '35454', '73~3', 'PREM+', 2.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (434, 73, NULL, '35454', '73~3', 'BRTRANS+', 1.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (435, 73, NULL, '35454', '73~3', 'INST+', 2.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (436, 73, NULL, '35454', '73~3', 'H/DLIV+', 1.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (437, 73, NULL, '35454', '73~3', 'VOL+', 1.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (438, 73, NULL, '35454', '73~3', 'WATERCON+', 1.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (439, 73, NULL, '35454', '73~3', 'DOOR/R+', 1.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (440, 73, NULL, '35454', '73~3', 'P/UP(8)', 50.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (441, 73, NULL, '3534', '73~4', 'DELIV+', 5.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (442, 73, NULL, '3534', '73~4', 'DISAS+', 5.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (443, 73, NULL, '3534', '73~4', 'ASSEM+', 5.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (444, 73, NULL, '3534', '73~4', 'RUB+', 6.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (445, 73, NULL, '3534', '73~4', 'UPST+', 8.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (446, 73, NULL, '3534', '73~4', 'DOWNST+', 9.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (447, 73, NULL, '3534', '73~4', 'PREM+', 2.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (448, 73, NULL, '3534', '73~4', 'BRTRANS+', 4.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (449, 73, NULL, '3534', '73~4', 'INST+', 5.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (450, 73, NULL, '3534', '73~4', 'H/DLIV+', 6.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (451, 73, NULL, '3534', '73~4', 'VOL+', 7.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (452, 73, NULL, '3534', '73~4', 'WATERCON+', 9.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (453, 73, NULL, '3534', '73~4', 'DOOR/R+', 5.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (454, 73, NULL, '3534', '73~4', 'P/UP(8)', 60.00, '123456', '10-10-10');
INSERT INTO `invoice_items` VALUES (455, 73, NULL, '3', '73~5', 'DELIV+', 4.00, '3', '20-20-20');
INSERT INTO `invoice_items` VALUES (456, 73, NULL, '3', '73~5', 'DISAS+', 4.00, '3', '20-20-20');
INSERT INTO `invoice_items` VALUES (457, 73, NULL, '3', '73~5', 'ASSEM+', 4.00, '3', '20-20-20');
INSERT INTO `invoice_items` VALUES (458, 73, NULL, '3', '73~5', 'RUB+', 4.00, '3', '20-20-20');
INSERT INTO `invoice_items` VALUES (580, 74, NULL, '99', NULL, 'P/UP(9)', 7.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (579, 74, NULL, '99', NULL, 'INST+', 7.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (578, 74, NULL, '99', NULL, 'BRTRANS+', 7.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (577, 74, NULL, '99', NULL, 'PREM+', 7.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (576, 74, NULL, '99', NULL, 'DOWNST+', 7.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (575, 74, NULL, '883', NULL, 'P/UP(2)', 7.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (574, 74, NULL, '883', NULL, 'PREM+', 7.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (573, 74, NULL, '883', NULL, 'DOWNST+', 6.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (572, 74, NULL, '883', NULL, 'UPST+', 5.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (571, 74, NULL, '883', NULL, 'RUB+', 4.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (570, 74, NULL, '883', NULL, 'ASSEM+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (569, 74, NULL, '883', NULL, 'DISAS+', 2.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (568, 74, NULL, '883', NULL, 'DELIV+', 2.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (567, 74, NULL, '77', NULL, 'RUB+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (566, 74, NULL, '77', NULL, 'ASSEM+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (565, 74, NULL, '77', NULL, 'DISAS+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (564, 74, NULL, '77', NULL, 'DELIV+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (563, 74, NULL, '55', NULL, 'RUB+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (562, 74, NULL, '55', NULL, 'ASSEM+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (561, 74, NULL, '55', NULL, 'DISAS+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (560, 74, NULL, '55', NULL, 'DELIV+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (559, 74, NULL, '44', NULL, 'DELIV+', 0.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (558, 74, NULL, '33', NULL, 'DISAS+', 0.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (557, 74, NULL, '33', NULL, 'DELIV+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (556, 74, NULL, '22', NULL, 'RUB+', 0.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (555, 74, NULL, '22', NULL, 'ASSEM+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (554, 74, NULL, '22', NULL, 'DISAS+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (553, 74, NULL, '22', NULL, 'DELIV+', 3.00, NULL, NULL);
INSERT INTO `invoice_items` VALUES (487, 75, '4', '4', '75~1', 'DELIV+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (488, 75, '4', '4', '75~1', 'DISAS+', 4.00, '', '');
INSERT INTO `invoice_items` VALUES (489, 75, '33', '22', '75~2', 'DELIV+', 3.00, '2', '222222');
INSERT INTO `invoice_items` VALUES (490, 75, '33', '22', '75~2', 'DISAS+', 4.00, '2', '222222');
INSERT INTO `invoice_items` VALUES (491, 75, '33', '22', '75~2', 'ASSEM+', 5.00, '2', '222222');
INSERT INTO `invoice_items` VALUES (492, 75, '33', '22', '75~2', 'RUB+', 5.00, '2', '222222');
INSERT INTO `invoice_items` VALUES (493, 75, '66', '55', '75~3', 'DELIV+', 0.00, '55', '555');
INSERT INTO `invoice_items` VALUES (494, 75, '66', '55', '75~3', 'WATERCON+', 6.00, '55', '555');
INSERT INTO `invoice_items` VALUES (495, 75, '66', '55', '75~3', 'DOOR/R+', 66.00, '55', '555');
INSERT INTO `invoice_items` VALUES (496, 75, '66', '55', '75~3', 'P/UP(1)', 66.00, '55', '555');
INSERT INTO `invoice_items` VALUES (581, 79, '', '11', '79~1', 'DELIV+', 12.00, '', '');
INSERT INTO `invoice_items` VALUES (582, 79, '', '11', '79~1', 'DISAS+', 22.00, '', '');
INSERT INTO `invoice_items` VALUES (591, 79, '', '', '79~3', 'DISAS+', 1.00, '44', '44');
INSERT INTO `invoice_items` VALUES (593, 79, 'Runsheet No 99', '9911', '79~2', 'VOL+', 2.00, '99', '99');
INSERT INTO `invoice_items` VALUES (592, 79, 'Runsheet No 99', '9911', '79~2', 'H/DLIV+', 2.00, '99', '99');
INSERT INTO `invoice_items` VALUES (590, 79, 'a', '1', '79~1', 'DELIV+', 3.00, '55', '55');
INSERT INTO `invoice_items` VALUES (594, 79, 'Runsheet No 100', '9922', '79~3', 'UPST+', 2.00, '99', '99');
INSERT INTO `invoice_items` VALUES (595, 79, 'Runsheet No 100', '9922', '79~3', 'DOWNST+', 2.00, '99', '99');
INSERT INTO `invoice_items` VALUES (596, 74, 'adsadasd', '11', '74~1', 'ASSEM+', 20.00, '', '');
INSERT INTO `invoice_items` VALUES (597, 74, 'vvvv', '111', '74~2', 'DELIV+', 3.00, '111', '1111');
INSERT INTO `invoice_items` VALUES (598, 74, 'vvvv', '111', '74~2', 'DISAS+', 3.00, '111', '1111');
INSERT INTO `invoice_items` VALUES (599, 74, 'vvvv', '111', '74~2', 'ASSEM+', 3.00, '111', '1111');
INSERT INTO `invoice_items` VALUES (600, 80, '22', '22', '80~1', 'DELIV+', 22.00, '', '');
INSERT INTO `invoice_items` VALUES (601, 80, '22', '22', '80~1', 'DISAS+', 22.00, '', '');
INSERT INTO `invoice_items` VALUES (602, 80, '22', '22', '80~1', 'ASSEM+', 22.00, '', '');

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
