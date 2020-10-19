CREATE TABLE `fa_makeapi` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `keyname` varchar(80) NOT NULL COMMENT '索引名',
  `apis` longtext COMMENT '接口成员',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyname` (`keyname`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='制造接口';