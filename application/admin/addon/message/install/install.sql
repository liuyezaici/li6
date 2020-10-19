

CREATE TABLE `__PREFIX__message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `details` longtext COMMENT '站内信详情',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `source` varchar(255) DEFAULT NULL COMMENT '组件名',
  `source_id` int(11) DEFAULT '0' COMMENT '组件id',
  `form_uid` int(11) DEFAULT '0' COMMENT '来源uid',
  `to_uid` int(11) DEFAULT '0' COMMENT '收件人uid',
  `is_read` tinyint(1) DEFAULT '0' COMMENT '是否已读',
  `ctime` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
    KEY `source_id` (`source_id`),
  KEY `to_uid` (`to_uid`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='站内信';

CREATE TABLE `__PREFIX__message_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='站内信配置表';


INSERT IGNORE INTO `__PREFIX__message_config` VALUES ('1', '{}');


