CREATE TABLE `__PREFIX__comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) NOT NULL COMMENT '组件名',
  `attr` varchar(255) NOT NULL COMMENT '评价属性名',
  `sourceid` int(11) DEFAULT NULL COMMENT '组件对象具体商品id',
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `pictures` varchar(255) DEFAULT NULL,
  `grade` tinyint(1) DEFAULT '5' COMMENT '等级1-5',
  `text` text COMMENT '内容',
  `status` int(4) DEFAULT '0' COMMENT '0隐藏1显示',
  `createtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='评价内容表';

CREATE TABLE `__PREFIX__comment_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config` text COMMENT '评价配置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评价配置表';

INSERT IGNORE INTO `__PREFIX__comment_config` VALUES ('1', '');


CREATE TABLE `__PREFIX__comment_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sourceid` int(11) DEFAULT NULL COMMENT '组件商品id',
  `source` varchar(255) DEFAULT NULL COMMENT '组件名',
  `attr` varchar(255) DEFAULT NULL COMMENT '评价属性名',
  `avg` varchar(255) DEFAULT NULL COMMENT '分均分',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sourceid, source, attr` (`sourceid`,`source`,`attr`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='评价统计表';

