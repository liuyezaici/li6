CREATE TABLE `__PREFIX__setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) DEFAULT NULL COMMENT '设置项描述',
  `typeid` int(11) DEFAULT '0' COMMENT '分类id',
  `cuid` int(11) DEFAULT '0' COMMENT '创建人',
  `ctime` int(11) DEFAULT NULL,
  `keyname` varchar(50) DEFAULT NULL,
  `keytype` varchar(50) DEFAULT '' COMMENT '值的类型',
  `value` text COMMENT '设置值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyname` (`keyname`) USING BTREE,
  KEY `title` (`title`) USING BTREE,
  KEY `typeid` (`typeid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统设置表';

-- ----------------------------
-- Records of __PREFIX__setting
-- ----------------------------
INSERT INTO `__PREFIX__setting` VALUES ('1', '站点名字', '1', '1', '1535104781', 'web_title', 'words', 'saasjs版');
INSERT INTO `__PREFIX__setting` VALUES ('2', '网站logo', '1', '1', '1535208992', 'web_logo', 'pic', '/uploads/20180825/20180825230403737457629765438886.jpg');
INSERT INTO `__PREFIX__setting` VALUES ('3', '首页轮播图', '64', '1', '1536030757', 'index_ppt', 'json', '{\"title\":[\"\\u56fe\\u7247\"],\"keyname\":[\"pic\"],\"keyvalue\":[\"vvvv\"],\"pic\":[\"\\/uploads\\/20180904\\/20180904111231252243366536141098.jpg\"]}');
INSERT INTO `__PREFIX__setting` VALUES ('5', 'asdasdasd', '64', '1', '1536030974', '', 'json', '[{\"title\":\"assda\",\"keyname\":\"asdsda\",\"keyvalue\":\"\",\"pic\":\"\"}]');
INSERT INTO `__PREFIX__setting` VALUES ('6', '后台站点名字', '1', '1', '1536048388', 'web_admin_title', 'words', '管理后台');



DROP TABLE IF EXISTS `__PREFIX__setting_type`;
CREATE TABLE `__PREFIX__setting_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) DEFAULT '' COMMENT '标题',
  `pid` int(11) DEFAULT '0' COMMENT '上级id',
  `cuid` int(11) DEFAULT '0' COMMENT '创建人id',
  `keyname` varchar(255) NOT NULL DEFAULT '' COMMENT '索引名',
  `ctime` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `keyname` (`keyname`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of __PREFIX__setting_type
-- ----------------------------
INSERT INTO `__PREFIX__setting_type` VALUES ('1', '站点设置', '0', '1', 'web_cfg', '1535166849');
INSERT INTO `__PREFIX__setting_type` VALUES ('2', '首页元素', '0', '1', '', '1535210534');