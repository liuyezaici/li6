CREATE TABLE `__PREFIX__emailcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `code` varchar(10) DEFAULT NULL COMMENT '短信验证码',
  `ctime` int(11) DEFAULT '0' COMMENT '短信发送时间',
  `typeid` int(11) DEFAULT '0' COMMENT '验证码用途',
  `status` tinyint(4) DEFAULT '0' COMMENT '0未使用 1已经使用',
  PRIMARY KEY (`id`),
  KEY `email` (`email`,`typeid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='邮箱验证码';

CREATE TABLE `__PREFIX__emailcode_type` (
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
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

