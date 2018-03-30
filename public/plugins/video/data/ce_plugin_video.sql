/*
 Navicat Premium Data Transfer

 Source Server         : root_127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50714
 Source Host           : localhost:3306
 Source Schema         : _la-extend

 Target Server Type    : MySQL
 Target Server Version : 50714
 File Encoding         : 65001

 Date: 25/01/2018 11:22:13
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ce_plugin_video
-- ----------------------------
DROP TABLE IF EXISTS `ce_plugin_video`;
CREATE TABLE `ce_plugin_video`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(8) NOT NULL COMMENT '操作者ID',
  `vi_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `vi_label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '副标题',
  `vi_desc` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '描述',
  `vi_img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '缩略图',
  `vi_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '地址：本地or七牛',
  `vi_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '详细',
  `vi_hits` int(10) NULL DEFAULT NULL COMMENT '点击数',
  `vi_like` int(10) NULL DEFAULT NULL COMMENT '点赞数',
  `vi_hate` int(10) NULL DEFAULT NULL COMMENT '讨厌数',
  `comment_count` int(10) NULL DEFAULT NULL COMMENT '评论数',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `del_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `status` tinyint(4) NOT NULL COMMENT '状态：1显示',
  `seo_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'seo_title',
  `seo_keys` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'seo关键字',
  `seo_desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'seo描述',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
