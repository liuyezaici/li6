CREATE TABLE IF NOT EXISTS `__PREFIX__sms` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `platform` varchar(25) NOT NULL COMMENT '短信平台',
  `event` varchar(30) NOT NULL DEFAULT '' COMMENT '事件',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `code` varchar(10) NOT NULL DEFAULT '' COMMENT '验证码',
  `content` varchar(255) DEFAULT '' COMMENT '短信内容',
  `ip` varchar(30) NOT NULL DEFAULT '' COMMENT 'IP',
  `createtime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '0未使用 1已使用 -1作废',
  PRIMARY KEY (`id`),
  KEY `platform` (`event`,`mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='短信验证码表';



