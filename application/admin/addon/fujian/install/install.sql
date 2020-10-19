CREATE TABLE `__PREFIX__fujian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '附件名字',
  `fileurl_local_min` varchar(500) DEFAULT '' COMMENT '本地小图',
  `fileurl_local` varchar(500) NOT NULL DEFAULT '' COMMENT '本地文件url',
  `fileurl_origin` varchar(500) DEFAULT '' COMMENT '远程图片',
  `geshi` varchar(50) DEFAULT '' COMMENT '文件格式',
  `mimetype` varchar(50) DEFAULT '' COMMENT 'mimetype',
  `filesize` int(10) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `imagewidth` decimal(10,2) DEFAULT '0.00',
  `imageheight` decimal(10,2) DEFAULT '0.00',
  `ctime` int(11) NOT NULL DEFAULT '0' COMMENT '时间',
  `update_time` int(11) DEFAULT NULL,
  `hash` varchar(50) DEFAULT '' COMMENT 'hash值',
  `filename_hash` varchar(50) DEFAULT '' COMMENT '文件名hash方便每次使用时 绑定数据id',
  `cuid` int(11) DEFAULT '0' COMMENT '上传人uid',
  `addon_name` varchar(50) DEFAULT '' COMMENT '组件名称',
  `addon_son` varchar(50) DEFAULT '' COMMENT '子组件名称',
  `addon_sourceid` int(11) DEFAULT '0' COMMENT '组件的数据id',
  `origin_type` int(11) DEFAULT '0' COMMENT '远程分类 如: 阿里云 腾讯云',
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename_hash` (`filename_hash`) USING BTREE,
  KEY `title` (`title`),
  KEY `addon_name` (`addon_name`,`addon_sourceid`,`addon_son`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='附件表';



