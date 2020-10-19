--
-- 其它字段在  field.php 里面定义
-- 字段索引或约束等请在install.php里面field函数内处理
--
CREATE TABLE IF NOT EXISTS `__PREFIX__news` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='新闻表';

CREATE TABLE IF NOT EXISTS `__PREFIX__news_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `config` longtext NOT NULL DEFAULT '' COMMENT 'JSON格式配置参数',
  `customtable` longtext NOT NULL DEFAULT '' COMMENT 'JSON格式自定义字段',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='新闻配置表';

INSERT IGNORE INTO `__PREFIX__news_config` VALUES ('1', '{}', '{}');