CREATE TABLE `__PREFIX__score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '用户uid (用户ID为39的是虚拟的系统用户)',
  `score` decimal(10,2) DEFAULT '0.00' COMMENT '用户充值余额',
  `hash` varchar(255) DEFAULT '' COMMENT '积分的hash值 防止手动篡改积分',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='用户积分表';




CREATE TABLE `__PREFIX__score_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '资金流水号',
  `from_uid` int(11) DEFAULT '0' COMMENT '资金来源用户uid [给用户转账时需要定义好系统uid]',
  `to_uid` int(11) DEFAULT '0' COMMENT '资金目标用户uid [用户消费订单，资金回收给系统时需要定义好系统uid]',
  `ctime` int(10) DEFAULT '0' COMMENT '日志时间',
  `score` decimal(10,2) DEFAULT '0.00' COMMENT '变动积分 [必须>0]',
  `operate_type` int(11) DEFAULT '0' COMMENT '操作类型id',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `from_uid` (`from_uid`,`to_uid`),
  KEY `ctime` (`ctime`),
  KEY `operate_type` (`operate_type`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='用户积分变动日志表';


CREATE TABLE `__PREFIX__score_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分配置表';


INSERT IGNORE INTO `__PREFIX__score_config` VALUES ('1', '{}') ;