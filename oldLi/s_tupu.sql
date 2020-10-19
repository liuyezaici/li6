/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : new

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-09-25 22:22:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `s_tupu`
-- ----------------------------
DROP TABLE IF EXISTS `s_tupu`;
CREATE TABLE `s_tupu` (
  `s_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_title` varchar(255) DEFAULT NULL,
  `s_uid` int(11) DEFAULT '0',
  `s_addtime` datetime DEFAULT '0000-00-00 00:00:00',
  `s_item_ids` text COMMENT '包含的单元索引ids',
  `t_from_url` varchar(255) DEFAULT NULL COMMENT '来路url',
  `s_desc` text,
  PRIMARY KEY (`s_id`),
  KEY `s_uid` (`s_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of s_tupu
-- ----------------------------

-- ----------------------------
-- Table structure for `s_tupu_item`
-- ----------------------------
DROP TABLE IF EXISTS `s_tupu_item`;
CREATE TABLE `s_tupu_item` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT,
  `t_sids` varchar(255) DEFAULT '0' COMMENT '归属图谱ids 可以被多个图谱所公用',
  `t_title` varchar(255) DEFAULT NULL,
  `t_uid` int(11) DEFAULT '0',
  `t_addtime` datetime DEFAULT '0000-00-00 00:00:00',
  `t_attrs` int(11) DEFAULT '0' COMMENT '属性个数',
  `t_public` tinyint(4) DEFAULT '0' COMMENT '0私有 1公共',
  `t_articles` int(11) DEFAULT NULL COMMENT '文章数',
  PRIMARY KEY (`t_id`),
  KEY `s_uid` (`t_uid`),
  KEY `t_sid` (`t_sids`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of s_tupu_item
-- ----------------------------

-- ----------------------------
-- Table structure for `s_tupu_item_articles`
-- ----------------------------
DROP TABLE IF EXISTS `s_tupu_item_articles`;
CREATE TABLE `s_tupu_item_articles` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT,
  `a_item_id` int(11) DEFAULT '0' COMMENT '归属单元id',
  `a_typeid` int(11) DEFAULT '0' COMMENT '分类id',
  `a_title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `a_uid` int(11) DEFAULT '0',
  `a_addtime` datetime DEFAULT '0000-00-00 00:00:00',
  `a_content` text COMMENT '文章内容',
  `a_order` int(11) DEFAULT '0' COMMENT '排序 从小到大',
  PRIMARY KEY (`a_id`),
  KEY `t_sid` (`a_item_id`),
  KEY `a_order` (`a_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of s_tupu_item_articles
-- ----------------------------

-- ----------------------------
-- Table structure for `s_tupu_item_articles_fujian`
-- ----------------------------
DROP TABLE IF EXISTS `s_tupu_item_articles_fujian`;
CREATE TABLE `s_tupu_item_articles_fujian` (
  `f_id` int(11) NOT NULL AUTO_INCREMENT,
  `f_article_id` int(11) DEFAULT '0' COMMENT '文章id',
  `f_adduid` int(11) DEFAULT '0' COMMENT '发布人',
  `f_addtime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  `f_edittime` datetime DEFAULT NULL,
  `f_filename` varchar(255) DEFAULT NULL COMMENT '文件名字',
  `f_fileurl` text COMMENT '下载链接',
  `f_filesize` int(11) DEFAULT '0' COMMENT '文件大小 bit',
  `f_geshi` varchar(15) DEFAULT NULL COMMENT '文件格式',
  `f_order` int(11) DEFAULT '0' COMMENT '排序 越小越前',
  PRIMARY KEY (`f_id`),
  KEY `f_sid` (`f_article_id`) USING BTREE,
  KEY `f_adduid` (`f_adduid`) USING BTREE,
  KEY `f_filename` (`f_filename`),
  KEY `f_order` (`f_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章附件';

-- ----------------------------
-- Records of s_tupu_item_articles_fujian
-- ----------------------------

-- ----------------------------
-- Table structure for `s_tupu_item_types`
-- ----------------------------
DROP TABLE IF EXISTS `s_tupu_item_types`;
CREATE TABLE `s_tupu_item_types` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT,
  `t_sid` int(255) DEFAULT '0' COMMENT '归属图谱id',
  `t_title` varchar(255) DEFAULT NULL COMMENT '分类标题',
  `t_uid` int(11) DEFAULT '0',
  `t_addtime` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`t_id`),
  KEY `s_uid` (`t_uid`),
  KEY `t_sid` (`t_sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章分类';

-- ----------------------------
-- Records of s_tupu_item_types
-- ----------------------------
