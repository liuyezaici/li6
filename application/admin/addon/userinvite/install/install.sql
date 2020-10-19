CREATE TABLE `__PREFIX__userinvite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyname` varchar(50) DEFAULT NULL COMMENT '邀请的秘钥',
  `main_uid` int(11) DEFAULT '0' COMMENT '主邀请人',
  `ctime` int(11) DEFAULT '0' COMMENT '创建时间',
  `reg_success_func` text COMMENT '注册成功执行脚本包',
  `pay_success_func` text COMMENT '支付成功执行脚本包',
  `successnum` int(11) DEFAULT '0' COMMENT '成功邀请数量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyname` (`keyname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户邀请记录 定义注册成功后需要执行的动作 和支付后执行的动作';

CREATE TABLE `__PREFIX__userinvitelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyname` varchar(255) DEFAULT NULL COMMENT '邀请的秘钥',
  `main_uid` int(11) DEFAULT '0' COMMENT '主邀请人',
  `new_uid` int(11) DEFAULT '0' COMMENT '被邀请人uid',
  `ctime` int(11) DEFAULT '0' COMMENT '创建时间',
  `reg_success_func` text COMMENT '注册成功执行脚本包',
  `pay_success_func` text COMMENT '支付成功执行脚本包',
  `payfunc_run_times` int(11) DEFAULT '0' COMMENT '支付成功时回调执行次数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `new_uid` (`new_uid`) USING BTREE,
  KEY `keyname` (`keyname`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户邀请记录 统计用户被邀请注册成功后执行的动作次数';




