CREATE TABLE `__PREFIX__feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '会员ID',
  `oid` int(11) DEFAULT NULL COMMENT '订单ID',
  `question` varchar(255) DEFAULT '' COMMENT '常见问题',
  `content` text COMMENT '详细描述',
  `pic` varchar(255) DEFAULT NULL COMMENT '图片',
  `ctime` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT '' COMMENT '姓名',
  `tel` varchar(255) DEFAULT '' COMMENT '联系电话',
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `ctime` (`ctime`) USING BTREE,
  KEY `username` (`username`),
  KEY `tel` (`tel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='问题反馈、投诉建议';

