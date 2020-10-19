CREATE TABLE `__PREFIX__juzi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(50) DEFAULT '' COMMENT '给外部访问的uri',
  `createtime` int(11) DEFAULT '0' COMMENT '创建时间',
  `cuid` int(11) DEFAULT '0' COMMENT '作者uid',
  `author` int(11) DEFAULT '0' COMMENT '作者ID',
  `fromid` int(11) DEFAULT '0' COMMENT '来源',
  `bookid` int(11) DEFAULT '0' COMMENT '来自书id',
  `content` char(200) DEFAULT '' COMMENT '文本',
  `contenthash` varchar(50) DEFAULT '' COMMENT '内容md5',
  `tagids` char(200) DEFAULT '' COMMENT '索引ids',
  `typeid` int(11) DEFAULT '0' COMMENT '自定义分类',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cuid_2` (`cuid`,`contenthash`),
  UNIQUE KEY `uri` (`uri`),
  KEY `cuid` (`cuid`),
  KEY `content` (`content`),
  KEY `mytypeid` (`typeid`),
  KEY `author` (`author`) USING BTREE,
  KEY `bookid` (`bookid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='句子表';




CREATE TABLE `__PREFIX__juzi_type` (
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

CREATE TABLE `__PREFIX__juzi_author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT '' COMMENT '标题',
  `cuid` int(11) DEFAULT '0' COMMENT '创建人id',
  `ctime` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`) USING BTREE,
  KEY `cuid` (`cuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='句子作者id';

CREATE TABLE `__PREFIX__juzi_from` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT '' COMMENT '标题',
  `cuid` int(11) DEFAULT '0' COMMENT '创建人id',
  `ctime` int(11) DEFAULT '0' COMMENT '入库时间',
  `authorid` int(11) DEFAULT '0' COMMENT '出处作者id',
  `publishtime` int(11) DEFAULT '0' COMMENT '发布时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`) USING BTREE,
  KEY `cuid` (`cuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='句子个人自定义来源';


CREATE TABLE `__PREFIX__juzi_caijirule` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='句子和标签的索引';



