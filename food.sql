/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50534
Source Host           : localhost:3306
Source Database       : food

Target Server Type    : MYSQL
Target Server Version : 50534
File Encoding         : 65001

Date: 2015-10-22 15:39:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL COMMENT '用户名',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `role` int(255) DEFAULT NULL COMMENT '角色',
  `login_time` int(11) DEFAULT NULL COMMENT '登录时间',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('13', 'test', '51abb9636078defbf888d8457a7c76f85c8f114c', '1', '1', '1');

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `cate_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `cate_name` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of category
-- ----------------------------
INSERT INTO `category` VALUES ('1', '川菜', '1');

-- ----------------------------
-- Table structure for comment
-- ----------------------------
DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `cid` int(11) NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `fid` int(11) DEFAULT NULL COMMENT '食物文章id',
  `uid` int(11) DEFAULT NULL COMMENT '评论用户id',
  `content` varchar(255) DEFAULT NULL COMMENT '评论内容',
  `citme` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of comment
-- ----------------------------

-- ----------------------------
-- Table structure for food
-- ----------------------------
DROP TABLE IF EXISTS `food`;
CREATE TABLE `food` (
  `fid` int(11) NOT NULL AUTO_INCREMENT COMMENT '美食ID',
  `uid` int(11) DEFAULT NULL COMMENT '作者id',
  `cata_id` int(11) DEFAULT NULL COMMENT '分类id',
  `content` varchar(255) DEFAULT NULL COMMENT '文字内容',
  `imgs` text COMMENT '图片',
  `ctime` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL COMMENT '状态',
  `is_top` tinyint(4) DEFAULT NULL COMMENT '是否置顶',
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of food
-- ----------------------------

-- ----------------------------
-- Table structure for like
-- ----------------------------
DROP TABLE IF EXISTS `like`;
CREATE TABLE `like` (
  `lid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) DEFAULT NULL COMMENT '文章id',
  `uid` int(11) DEFAULT NULL COMMENT '点赞用户id',
  `ctime` int(11) DEFAULT NULL COMMENT '点赞时间',
  PRIMARY KEY (`lid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of like
-- ----------------------------

-- ----------------------------
-- Table structure for login_log
-- ----------------------------
DROP TABLE IF EXISTS `login_log`;
CREATE TABLE `login_log` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of login_log
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `avator` varchar(255) DEFAULT NULL COMMENT '头像',
  `sex` int(255) DEFAULT NULL COMMENT '性别',
  `age` int(11) DEFAULT NULL COMMENT '年龄',
  `zone` varchar(255) DEFAULT NULL COMMENT '地区',
  `sign` varchar(255) DEFAULT NULL COMMENT '个人签名',
  `point` int(11) DEFAULT NULL COMMENT '积分',
  `type` tinyint(4) DEFAULT NULL COMMENT '类型',
  `status` tinyint(255) DEFAULT NULL COMMENT '状态',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
