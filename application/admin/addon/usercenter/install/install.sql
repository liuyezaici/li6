CREATE TABLE IF NOT EXISTS `__PREFIX__third` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `platform` varchar(25) NOT NULL COMMENT '第三方应用',
  `unionid` varchar(50) NOT NULL DEFAULT '' COMMENT '微信唯一ID',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方唯一ID',
  `openname` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方会员昵称',
  `access_token` varchar(100) NOT NULL DEFAULT '',
  `refresh_token` varchar(100) NOT NULL DEFAULT '',
  `expires_in` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '有效期',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `logintime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录时间',
  `expiretime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `platform` (`platform`,`openid`),
  KEY `user_id` (`user_id`,`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='第三方登录表' ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `__PREFIX__usercenter_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `config` longtext NOT NULL DEFAULT '' COMMENT 'JSON',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='配置表';

INSERT IGNORE INTO `__PREFIX__usercenter_config` VALUES ('1', '{}');