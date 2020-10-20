CREATE TABLE IF NOT EXISTS `__PREFIX__article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `rq` int(11) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `typeId` int(11) NOT NULL DEFAULT '0' COMMENT '文章分类id',
  `ctime` int(11) NOT NULL,
  `thatDate` int(11) NOT NULL DEFAULT '0' COMMENT '发生日期',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `title` (`title`) USING BTREE,
  KEY `typeId` (`typeId`,`status`) USING BTREE,
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='文章';




CREATE TABLE IF NOT EXISTS `__PREFIX__article_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  PRIMARY KEY (`id`),
  KEY `title` (`title`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章分类';

