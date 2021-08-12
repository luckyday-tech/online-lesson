/*
 Navicat Premium Data Transfer

 Source Server         : JYJ_Computer
 Source Server Type    : MySQL
 Source Server Version : 100414
 Source Host           : 192.168.2.138:3306
 Source Schema         : onlinelesson_db

 Target Server Type    : MySQL
 Target Server Version : 100414
 File Encoding         : 65001

 Date: 11/08/2021 01:44:21
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for online_users
-- ----------------------------
DROP TABLE IF EXISTS `online_users`;
CREATE TABLE `online_users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `peer_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_teacher` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for rooms
-- ----------------------------
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `host_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of rooms
-- ----------------------------
INSERT INTO `rooms` VALUES (1, 'test_lesson', 1, '2021-07-13 11:48:27', '2021-07-13 15:38:58');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `updated_at` timestamp(0) NOT NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'teacher01', 'teacher01@gmail.com', 1, '2021-07-13 11:46:43', '2021-07-13 11:46:43');
INSERT INTO `users` VALUES (2, 'student01', 'student01@gmail.com', 0, '2021-07-13 11:48:05', '2021-07-13 11:48:05');
INSERT INTO `users` VALUES (3, 'student02', 'student02@gmail.com', 0, '2021-07-13 11:48:05', '2021-07-13 11:48:05');
INSERT INTO `users` VALUES (4, 'student03', 'student03@gmail.com', 0, '2021-07-13 11:48:05', '2021-07-13 11:48:05');
INSERT INTO `users` VALUES (5, 'student04', 'student04@gmail.com', 0, '2021-07-13 11:48:05', '2021-07-13 11:48:05');
INSERT INTO `users` VALUES (6, 'student05', 'student05@gmail.com', 0, '2021-07-13 11:48:05', '2021-07-13 11:48:05');
INSERT INTO `users` VALUES (7, 'student06', 'student06@gmail.com', 0, '2021-07-13 11:48:05', '2021-07-13 11:48:05');
INSERT INTO `users` VALUES (8, 'student07', 'student07@gmail.com', 0, '2021-07-13 11:48:05', '2021-07-13 11:48:05');
INSERT INTO `users` VALUES (9, 'student08', 'student08@gmail.com', 0, '2021-07-13 11:48:05', '2021-07-13 11:48:05');
INSERT INTO `users` VALUES (10, 'student09', 'student09@gmail.com', 0, '2021-07-13 11:48:05', '2021-07-13 11:48:05');
INSERT INTO `users` VALUES (11, 'student10', 'student10@gmail.com', 0, '2021-07-13 11:48:06', '2021-07-13 11:48:06');

SET FOREIGN_KEY_CHECKS = 1;
