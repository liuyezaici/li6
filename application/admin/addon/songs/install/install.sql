CREATE TABLE `__PREFIX__songs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '歌曲名',
  `singer` varchar(255) NOT NULL DEFAULT '' COMMENT '歌手sort(1,2,3)',
  `songid` int(11) NOT NULL DEFAULT '0' COMMENT '网易歌曲id',
  `rq` int(11) NOT NULL DEFAULT '0' COMMENT '人气',
  PRIMARY KEY (`id`),
  UNIQUE KEY `songid` (`songid`),
  UNIQUE KEY `title_singer` (`title`,`singer`) USING BTREE,
  KEY `title_2` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='歌曲表';

CREATE TABLE `__PREFIX__songs_caijirule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '' COMMENT '采集名',
  `usehttps` tinyint(4) DEFAULT '0' COMMENT '是否开启https',
  `url_reg` varchar(255) DEFAULT '' COMMENT '采集url',
  `fromurl` varchar(255) DEFAULT '' COMMENT '来路url',
  `num` int(11) DEFAULT '0' COMMENT '采集数量',
  `topage` int(11) DEFAULT '1' COMMENT '采集到第几页',
  `from_str` varchar(255) DEFAULT '' COMMENT '开头字符串',
  `end_str` varchar(255) DEFAULT '' COMMENT '结束字符串',
  `repeat_explode` varchar(255) DEFAULT '' COMMENT '循环体的分割字符',
  `item_content_explode` varchar(255) DEFAULT NULL COMMENT '单元的作者字符分割',
  `item_author_explode` varchar(255) DEFAULT '' COMMENT '作者分隔符',
  `createtime` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url_reg`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='歌曲和标签的索引';


CREATE TABLE `__PREFIX__songs_singer` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '歌曲名',
  `singerid` int(11) NOT NULL DEFAULT '0' COMMENT '网易歌手id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`singerid`) USING BTREE,
  KEY `title_2` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='歌曲表';


CREATE TABLE `__PREFIX__songs_phb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '排行榜标题',
  `day` int(11) NOT NULL DEFAULT '0' COMMENT '日期',
  `mp3ids` text,
  `url` varchar(255) DEFAULT '' COMMENT '采集url',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `pid` (`day`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

