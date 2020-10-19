CREATE TABLE `__PREFIX__signin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cuid` int(11) DEFAULT '0' COMMENT '用户uid',
  `ctime` int(10) DEFAULT '0' COMMENT '日志时间',
  `cday` int(11) DEFAULT '0' COMMENT '签到日期转数字',
  `score` decimal(10,2) DEFAULT '0.00' COMMENT '获得积分',
  `memo` text COMMENT '备注',
  `gift` tinyint(4) DEFAULT '0' COMMENT '当天是否连续签到并且赠送积分',
  `gift_num` int(11) DEFAULT '0' COMMENT '当天连续签到赠送积分',
  `continue_days` int(11) DEFAULT '0' COMMENT '连续签到天数',
  `continue_days_score` int(11) DEFAULT '0' COMMENT '送完积分后连续签到天数',
  PRIMARY KEY (`id`),
  KEY `from_uid` (`cuid`),
  KEY `ctime` (`ctime`),
  KEY `cday` (`cday`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户积分签到记录';






CREATE TABLE `__PREFIX__signin_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分签到配置表';


INSERT IGNORE INTO `__PREFIX__signin_config` VALUES ('1', '{}') ;