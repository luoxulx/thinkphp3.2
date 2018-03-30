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

 Date: 07/02/2018 11:09:20
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ce_plugin_lxcomment
-- ----------------------------
DROP TABLE IF EXISTS `ce_plugin_lxcomment`;
CREATE TABLE `ce_plugin_lxcomment`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '被回复的评论id',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发表评论的用户id',
  `to_user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '被评论的用户id,前提条件，已登录',
  `to_who` varchar(225) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '-1' COMMENT '被回复的用户的名称（匿名）,前提条件，未登录，顶级评论默认-1，回复内容必须有值',
  `object_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评论内容 id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评论时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态,1:已审核,0:未审核',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '评论类型；1实名评论',
  `table_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '评论内容所在表，不带表前缀',
  `full_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '评论者昵称',
  `email` varchar(225) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '评论者邮箱',
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '层级关系',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '原文地址',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '评论内容',
  `is_re` tinyint(3) NOT NULL DEFAULT 0 COMMENT '有新回复时是否邮件通知',
  `ip_add` varchar(225) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '评论IP及地址',
  `more` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '扩展属性',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `comment_post_ID`(`object_id`) USING BTREE,
  INDEX `comment_approved_date_gmt`(`status`) USING BTREE,
  INDEX `comment_parent`(`parent_id`) USING BTREE,
  INDEX `table_id_status`(`table_name`, `object_id`, `status`) USING BTREE,
  INDEX `createtime`(`create_time`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '评论表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
