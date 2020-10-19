CREATE TABLE `__PREFIX__help` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyname` varchar(150) DEFAULT '' COMMENT '标识',
  `title` varchar(150) DEFAULT '' COMMENT '标题',
  `cover_url` varchar(522) DEFAULT '' COMMENT '封面图',
  `video_url` varchar(522) NOT NULL DEFAULT '' COMMENT '视频url',
  `content` varchar(255) DEFAULT '' COMMENT '内容',
  `pic` varchar(255) DEFAULT '0' COMMENT '图片',
  `typeid` int(11) DEFAULT '0' COMMENT '分类id',
  `cuid` int(11) DEFAULT '0' COMMENT '创建人id',
  `ctime` int(11) DEFAULT '0' COMMENT '创建时间',
  `hit` int(11) DEFAULT '0' COMMENT '人气',
  `del` tinyint(4) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`),
  KEY `typeid` (`typeid`),
  KEY `keyname` (`keyname`),
  KEY `title` (`title`),
  KEY `del` (`del`),
  KEY `hit` (`hit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `__PREFIX__help_type` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

