/*
 Navicat MySQL Data Transfer

 Source Server         : 本地
 Source Server Version : 50627
 Source Host           : localhost
 Source Database       : status_center

 Target Server Version : 50627
 File Encoding         : utf-8

 Date: 11/23/2015 16:22:54 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `interface`
-- ----------------------------
DROP TABLE IF EXISTS `interface`;
CREATE TABLE `interface` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '接口名称',
  `own_uid` int(11) DEFAULT NULL COMMENT '接口所属用户',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `interface`
-- ----------------------------
BEGIN;
INSERT INTO `interface` VALUES ('1', 'test', null, '1448262890'), ('6', 'test1', null, '1448265710'), ('7', 'test2', null, '1448265773');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
