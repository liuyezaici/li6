<?php

namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Admin;
use app\common\controller\Backend;
use fast\Random;
use think\Db;
use think\Exception;

/**
 * 旧版数据过度
 * @internal
 */
class Table extends Backend
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }


    protected function importsql($sql)
    {
        $lines = explode(PHP_EOL, $sql);
        $templine = '';
        foreach ($lines as $line)
        {
            if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*')
                continue;

            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';')
            {
                $templine = str_ireplace('__PREFIX__', config('database.prefix'), $templine);
                $templine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $templine);
                try
                {
                    Db::getPdo()->exec($templine);
                }
                catch (\PDOException $e)
                {
                    //$e->getMessage();
                }
                $templine = '';
            }
        }
        return true;
    }

    /**
     * 写入默认的系统表
     */
    public function index()
    {
        print_r('<h4>写入默认的系统表：</h4>');
        $sql = "CREATE TABLE IF NOT EXISTS `fa_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '昵称',
  `password` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码',
  `salt` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '电子邮箱',
  `loginfailure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` int(10) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登录IP',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(59) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
  `privilege` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';";

        Db::query($sql);

//        $sql = "INSERT INTO `fa_admin` VALUES ('1', 'admin', 'Manager', '/4qMkXaj3eLoZT2R9iHWnU3vYzcFMSIhOCuX9iUdQOc=', '6Rk0Zs', '/uploads/20210114/e2e0f0d8f3e56a463cd1d944de554da7.jpg', 'rui6ye@163.com', '8', '1502029281', '127.0.0.1', '1492186163', '1611889155', 'e394bdc3-90de-473e-aca7-cb943a412984', 'normal', '0');";
//
//        Db::query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `fa_admin_log` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
              `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
              `username` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '管理员名字',
              `url` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '操作页面',
              `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '日志标题',
              `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
              `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'IP',
              `useragent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'User-Agent',
              `createtime` int(10) DEFAULT NULL COMMENT '操作时间',
              PRIMARY KEY (`id`),
              KEY `name` (`username`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员日志表';";
        Db::query($sql);



        $sql = "CREATE TABLE IF NOT EXISTS `fa_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图标',
  `condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '条件',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `pid` (`pid`),
  KEY `weigh` (`weigh`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='节点表';";
        Db::query($sql);

        $sql = "INSERT INTO `fa_auth_rule` VALUES ('1', 'file', '0', 'dashboard', 'Dashboard', 'fa fa-dashboard', '', '', '1', '1497429920', '1611041191', '3000', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('2', 'file', '0', 'general', 'General', 'fa fa-cogs', '', '', '1', '1497429920', '1497430169', '137', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('3', 'file', '0', 'category', 'Category', 'fa fa-leaf', '', 'Category tips', '0', '1497429920', '1611630281', '119', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('4', 'file', '0', 'addon', 'Addon', 'fa fa-rocket', '', 'Addon tips', '0', '1502035509', '1611623848', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('5', 'file', '0', 'auth', 'Auth', 'fa fa-group', '', '', '1', '1497429920', '1497430092', '99', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('6', 'file', '2', 'general/config', 'Config', 'fa fa-cog', '', 'Config tips', '1', '1497429920', '1497430683', '60', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('7', 'file', '2', 'general/attachment', 'Attachment', 'fa fa-file-image-o', '', 'Attachment tips', '1', '1497429920', '1497430699', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('8', 'file', '2', 'general/profile', 'Profile', 'fa fa-user', '', '', '1', '1497429920', '1497429920', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('9', 'file', '5', 'auth/admin', 'Admin', 'fa fa-user', '', 'Admin tips', '1', '1497429920', '1497430320', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('10', 'file', '5', 'auth/adminlog', 'Admin log', 'fa fa-list-alt', '', 'Admin log tips', '1', '1497429920', '1497430307', '113', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('11', 'file', '5', 'auth/group', 'Group', 'fa fa-group', '', 'Group tips', '1', '1497429920', '1497429920', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('12', 'file', '5', 'auth/rule', 'Rule', 'fa fa-bars', '', 'Rule tips', '1', '1497429920', '1497430581', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('13', 'file', '1', 'dashboard/index', 'View', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '136', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('14', 'file', '1', 'dashboard/add', 'Add', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '135', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('15', 'file', '1', 'dashboard/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '133', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('16', 'file', '1', 'dashboard/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '134', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('17', 'file', '1', 'dashboard/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '132', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('18', 'file', '6', 'general/config/index', 'View', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '52', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('19', 'file', '6', 'general/config/add', 'Add', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '51', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('20', 'file', '6', 'general/config/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '50', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('21', 'file', '6', 'general/config/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '49', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('22', 'file', '6', 'general/config/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '48', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('23', 'file', '7', 'general/attachment/index', 'View', 'fa fa-circle-o', '', 'Attachment tips', '0', '1497429920', '1497429920', '59', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('24', 'file', '7', 'general/attachment/select', 'Select attachment', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '58', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('25', 'file', '7', 'general/attachment/add', 'Add', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '57', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('26', 'file', '7', 'general/attachment/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '56', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('27', 'file', '7', 'general/attachment/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '55', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('28', 'file', '7', 'general/attachment/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '54', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('29', 'file', '8', 'general/profile/index', 'View', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '33', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('30', 'file', '8', 'general/profile/update', 'Update profile', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '32', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('31', 'file', '8', 'general/profile/add', 'Add', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '31', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('32', 'file', '8', 'general/profile/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '30', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('33', 'file', '8', 'general/profile/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '29', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('34', 'file', '8', 'general/profile/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '28', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('35', 'file', '3', 'category/index', 'View', 'fa fa-circle-o', '', 'Category tips', '0', '1497429920', '1497429920', '142', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('36', 'file', '3', 'category/add', 'Add', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '141', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('37', 'file', '3', 'category/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '140', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('38', 'file', '3', 'category/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '139', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('39', 'file', '3', 'category/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '138', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('40', 'file', '9', 'auth/admin/index', 'View', 'fa fa-circle-o', '', 'Admin tips', '0', '1497429920', '1497429920', '117', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('41', 'file', '9', 'auth/admin/add', 'Add', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '116', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('42', 'file', '9', 'auth/admin/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '115', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('43', 'file', '9', 'auth/admin/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '114', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('44', 'file', '10', 'auth/adminlog/index', 'View', 'fa fa-circle-o', '', 'Admin log tips', '0', '1497429920', '1497429920', '112', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('45', 'file', '10', 'auth/adminlog/detail', 'Detail', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '111', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('46', 'file', '10', 'auth/adminlog/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '110', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('47', 'file', '11', 'auth/group/index', 'View', 'fa fa-circle-o', '', 'Group tips', '0', '1497429920', '1497429920', '108', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('48', 'file', '11', 'auth/group/add', 'Add', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '107', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('49', 'file', '11', 'auth/group/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '106', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('50', 'file', '11', 'auth/group/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '105', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('51', 'file', '12', 'auth/rule/index', 'View', 'fa fa-circle-o', '', 'Rule tips', '0', '1497429920', '1497429920', '103', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('52', 'file', '12', 'auth/rule/add', 'Add', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '102', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('53', 'file', '12', 'auth/rule/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '101', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('54', 'file', '12', 'auth/rule/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '100', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('55', 'file', '4', 'addon/index', 'View', 'fa fa-circle-o', '', 'Addon tips', '0', '1502035509', '1502035509', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('56', 'file', '4', 'addon/add', 'Add', 'fa fa-circle-o', '', '', '0', '1502035509', '1502035509', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('57', 'file', '4', 'addon/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1502035509', '1502035509', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('58', 'file', '4', 'addon/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1502035509', '1502035509', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('59', 'file', '4', 'addon/downloaded', 'Local addon', 'fa fa-circle-o', '', '', '0', '1502035509', '1502035509', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('60', 'file', '4', 'addon/state', 'Update state', 'fa fa-circle-o', '', '', '0', '1502035509', '1502035509', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('63', 'file', '4', 'addon/config', 'Setting', 'fa fa-circle-o', '', '', '0', '1502035509', '1502035509', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('64', 'file', '4', 'addon/refresh', 'Refresh', 'fa fa-circle-o', '', '', '0', '1502035509', '1502035509', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('65', 'file', '4', 'addon/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1502035509', '1502035509', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('66', 'file', '0', 'user', 'User', 'fa fa-list', '', '', '0', '1516374729', '1611624958', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('67', 'file', '66', 'user/user', 'User', 'fa fa-user', '', '', '0', '1516374729', '1611624612', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('68', 'file', '67', 'user/user/index', 'View', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('69', 'file', '67', 'user/user/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('70', 'file', '67', 'user/user/add', 'Add', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('71', 'file', '67', 'user/user/del', 'Del', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('72', 'file', '67', 'user/user/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('73', 'file', '66', 'user/group', 'User group', 'fa fa-users', '', '', '0', '1516374729', '1611113022', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('74', 'file', '73', 'user/group/add', 'Add', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('75', 'file', '73', 'user/group/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('76', 'file', '73', 'user/group/index', 'View', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('77', 'file', '73', 'user/group/del', 'Del', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('78', 'file', '73', 'user/group/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('79', 'file', '66', 'user/rule', 'User rule', 'fa fa-circle-o', '', '', '0', '1516374729', '1611113031', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('80', 'file', '79', 'user/rule/index', 'View', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('81', 'file', '79', 'user/rule/del', 'Del', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('82', 'file', '79', 'user/rule/add', 'Add', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('83', 'file', '79', 'user/rule/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('84', 'file', '79', 'user/rule/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1516374729', '1516374729', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('93', 'file', '0', 'example', '开发示例管理', 'fa fa-magic', '', '', '1', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('94', 'file', '93', 'example/bootstraptable', '表格完整示例', 'fa fa-table', '', '', '1', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('95', 'file', '94', 'example/bootstraptable/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('96', 'file', '94', 'example/bootstraptable/detail', '详情', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('97', 'file', '94', 'example/bootstraptable/change', '变更', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('98', 'file', '94', 'example/bootstraptable/del', '删除', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('99', 'file', '94', 'example/bootstraptable/multi', '批量更新', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('100', 'file', '93', 'example/customsearch', '自定义搜索', 'fa fa-table', '', '', '1', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('101', 'file', '100', 'example/customsearch/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('102', 'file', '100', 'example/customsearch/del', '删除', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('103', 'file', '100', 'example/customsearch/multi', '批量更新', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('104', 'file', '93', 'example/customform', '自定义表单示例', 'fa fa-edit', '', '', '1', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('105', 'file', '104', 'example/customform/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('106', 'file', '93', 'example/tablelink', '表格联动示例', 'fa fa-table', '', '点击左侧日志列表，右侧的表格数据会显示指定管理员的日志列表', '1', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('107', 'file', '106', 'example/tablelink/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('108', 'file', '93', 'example/colorbadge', '彩色角标', 'fa fa-table', '', '左侧彩色的角标会根据当前数据量的大小进行更新', '1', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('109', 'file', '108', 'example/colorbadge/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('110', 'file', '108', 'example/colorbadge/del', '删除', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('111', 'file', '108', 'example/colorbadge/multi', '批量更新', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('112', 'file', '93', 'example/controllerjump', '控制器间跳转', 'fa fa-table', '', '点击IP地址可以跳转到新的选项卡中查看指定IP的数据', '1', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('113', 'file', '112', 'example/controllerjump/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('114', 'file', '112', 'example/controllerjump/del', '删除', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('115', 'file', '112', 'example/controllerjump/multi', '批量更新', 'fa fa-circle-o', '', '', '0', '1610594758', '1610594758', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('116', 'file', '93', 'example/cxselect', '多级联动', 'fa fa-table', '', '基于jquery.cxselect实现的多级联动', '1', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('117', 'file', '116', 'example/cxselect/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('118', 'file', '116', 'example/cxselect/del', '删除', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('119', 'file', '116', 'example/cxselect/multi', '批量更新', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('120', 'file', '93', 'example/multitable', '多表格示例', 'fa fa-table', '', '展示在一个页面显示多个Bootstrap-table表格', '1', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('121', 'file', '120', 'example/multitable/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('122', 'file', '120', 'example/multitable/del', '删除', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('123', 'file', '120', 'example/multitable/multi', '批量更新', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('124', 'file', '93', 'example/relationmodel', '关联模型示例', 'fa fa-table', '', '列表中的头像、用户名和昵称字段均从关联表中取出', '1', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('125', 'file', '124', 'example/relationmodel/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('126', 'file', '124', 'example/relationmodel/del', '删除', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('127', 'file', '124', 'example/relationmodel/multi', '批量更新', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('128', 'file', '93', 'example/tabletemplate', '表格模板示例', 'fa fa-table', '', '', '1', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('129', 'file', '128', 'example/tabletemplate/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('130', 'file', '128', 'example/tabletemplate/detail', '详情', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('131', 'file', '128', 'example/tabletemplate/del', '删除', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('132', 'file', '128', 'example/tabletemplate/multi', '批量更新', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('133', 'file', '93', 'example/baidumap', '百度地图示例', 'fa fa-map-pin', '', '', '1', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('134', 'file', '133', 'example/baidumap/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('135', 'file', '133', 'example/baidumap/map', '详情', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('136', 'file', '133', 'example/baidumap/del', '删除', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('137', 'file', '93', 'example/echarts', '统计图表示例', 'fa fa-bar-chart', '', '', '1', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('138', 'file', '137', 'example/echarts/index', '查看', 'fa fa-circle-o', '', '', '0', '1610594759', '1610594759', '0', 'hidden');
INSERT INTO `fa_auth_rule` VALUES ('139', 'file', '0', 'device', 'Device', 'fa fa-cube', '', '', '1', '1610602661', '1611041173', '2000', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('140', 'file', '139', 'addons/device/index/index', 'List', 'fa fa-list', '', '', '1', '1610610003', '1611112712', '100', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('141', 'file', '0', 'policy', 'Policy', 'fa fa-edit', '', '', '1', '1610689597', '1610689597', '2', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('142', 'file', '141', 'addons/policy/orders', 'Orders', 'fa fa-caret-right', '', '', '1', '1610689677', '1610689981', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('143', 'file', '141', 'addons/policy/ordercontrol', 'Orders Controllers', 'fa fa-caret-right', '', '', '1', '1610689750', '1610689976', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('144', 'file', '141', 'addons/policy/base', 'Base', 'fa fa-caret-right', '', '', '1', '1610689807', '1610689972', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('145', 'file', '141', 'addons/policy/basecontrol', 'Base Control', 'fa fa-caret-right', '', '', '1', '1610689886', '1610689967', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('146', 'file', '141', 'addons/policy/offline', 'OffLine', 'fa fa-caret-right', '', '', '1', '1610689960', '1610690016', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('147', 'file', '141', 'addons/policy/offcontrol', 'OffLine Control', 'fa fa-caret-right', '', '', '1', '1610690048', '1610690048', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('148', 'file', '139', 'addons/device/deviceimport', 'Import', 'fa fa-cloud-upload', '', '', '1', '1611041264', '1611045352', '80', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('149', 'file', '139', 'addons/device/devicecfgmodel', 'Device Config Model', 'fa fa-cog', '', '', '1', '1611045303', '1611045369', '70', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('150', 'file', '0', 'addons/tools', 'Tools', 'fa fa-wrench', '', '', '1', '1611104349', '1611104349', '70', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('151', 'file', '150', 'addons/tools/electric', 'Electric Rate Plans', 'fa fa-caret-right', '', '', '1', '1611104415', '1611104569', '80', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('152', 'file', '150', 'addons/tools/dsttrigger', 'DST Trigger', 'fa fa-caret-right', '', '', '1', '1611104460', '1611104583', '70', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('153', 'file', '150', 'addons/tools/loganalysis', 'Log Analysis Config', 'fa fa-caret-right', '', '', '1', '1611104534', '1611104604', '60', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('154', 'file', '0', 'addons/usageprediction', 'Usage Prediction', 'fa fa-database', '', '', '1', '1611104655', '1611104655', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('155', 'file', '154', 'addons/usageprediction/xmldata', 'XML Data', 'fa fa-caret-right', '', '', '1', '1611104711', '1611105095', '100', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('156', 'file', '154', 'addons/usageprediction/datachart', 'Data Chart', 'fa fa-caret-right', '', '', '1', '1611104978', '1611105087', '90', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('157', 'file', '154', 'addons/usageprediction/usagepredicting', 'Usage Predicting', 'fa fa-caret-right', '', '', '1', '1611105072', '1611105106', '80', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('158', 'file', '154', 'addons/usageprediction/usagepredictions', 'Usage Predictions', 'fa fa-caret-right', '', '', '1', '1611105173', '1611105187', '70', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('159', 'file', '139', 'addons/device/mydevice/index', 'My Device', 'fa fa-list', '', '', '1', '1611113105', '1611121942', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('160', 'file', '139', 'addons/device/index/editnote', 'editNote', 'fa fa-circle-o', '', '', '0', '1611122659', '1611122779', '0', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('161', 'file', '139', 'addons/device/balancer/index', 'balancer', 'fa fa-circle-o', '', '', '0', '1611122905', '1611122905', '0', 'normal');";
        $this->importsql($sql);


        $sql = "CREATE TABLE IF NOT EXISTS `fa_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '组名',
  `rules` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则ID',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='分组表';";
        Db::query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `fa_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '级别ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限分组表';";
        Db::query($sql);

    }


    /**
     * 写入默认的系统表
     */
    public function authto()
    {
        print_r('<h4>写入默认的组权限：</h4>');
        $sql = "INSERT INTO `fa_auth_group` VALUES ('1', '0', 'Admin group', '*', '1490883540', '149088354', 'normal');";

        Db::query($sql);

        $sql = "INSERT INTO `fa_auth_group_access` VALUES ('1', '1');";
        Db::query($sql);


    }

    /**
     * 写入新版设备表
     */
    public function device()
    {
        print_r('<h4>写入新版设备表：</h4>');

        $sql = "CREATE TABLE IF NOT EXISTS `fa_device` (
  `deviceId` int(11) NOT NULL AUTO_INCREMENT,
  `deviceSn` varchar(32) NOT NULL DEFAULT '',
  `deviceCode` varchar(32) NOT NULL,
  `deviceCapacity` decimal(5,1) NOT NULL,
  `firmwareVer` varchar(20) NOT NULL DEFAULT '',
  `deviceModel` varchar(20) NOT NULL DEFAULT '',
  `rsaPublicKey` varchar(1024) NOT NULL,
  `ecdsaPublicKey` varchar(1024) NOT NULL,
  `ecdsaSignMode` int(11) NOT NULL DEFAULT '0',
  `ECAccount` varchar(64) DEFAULT NULL,
  `ECPasswd` varchar(64) DEFAULT NULL,
  `basicPolicyID` int(11) unsigned DEFAULT NULL,
  `sensorConfigID` int(11) unsigned DEFAULT NULL,
  `ZigBee_Mac` varchar(20) NOT NULL,
  `ZigBee_Installation` varchar(20) NOT NULL,
  `ZigBee_LinkKey` varchar(32) NOT NULL,
  `ZigBee_PrivateKey` varchar(42) NOT NULL,
  `ZigBee_Certificate` varchar(96) NOT NULL,
  `EPName` varchar(64) DEFAULT NULL,
  `Note` varchar(1024) DEFAULT NULL,
  `totalProfit` decimal(9,2) NOT NULL DEFAULT '0.00',
  `timezoneName` varchar(64) NOT NULL,
  `location` varchar(64) NOT NULL,
  `timezone` tinyint(3) NOT NULL DEFAULT '0',
  `dst` tinyint(2) NOT NULL DEFAULT '0',
  `longitude` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `latitude` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `regionid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `balancer` varchar(1024) DEFAULT NULL,
  `config` varchar(1024) NOT NULL DEFAULT '{\"LogLevel\":1,\"AutoRestart\":0,\"ems_config\":{}}',
  `userid` int(11) NOT NULL DEFAULT '0',
  `password` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`deviceId`),
  UNIQUE KEY `sn_UNIQUE` (`deviceSn`) USING BTREE,
  UNIQUE KEY `devId_UNIQUE` (`deviceId`) USING BTREE,
  KEY `deviceCode_UNIQUE` (`deviceCode`) USING BTREE,
  KEY `EPName` (`EPName`) USING BTREE,
  KEY `deviceModel` (`deviceModel`),
  CONSTRAINT `fa_device_ibfk_1` FOREIGN KEY (`EPName`) REFERENCES `electricplan` (`EPName`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

        Db::query($sql);


        $sql = "CREATE TABLE IF NOT EXISTS  `fa_user_has_device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_userId` int(11) NOT NULL DEFAULT '0',
  `fk_deviceId` int(11) NOT NULL DEFAULT '0',
  `uhdOwner` tinyint(1) NOT NULL DEFAULT '0',
  `uhdReady` tinyint(1) NOT NULL DEFAULT '0',
  `uhdAddTime` int(11) NOT NULL DEFAULT '0',
  `uhdDevName` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `uhdDevDesc` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uhdId_UNIQUE` (`id`) USING BTREE,
  KEY `fk_userId` (`fk_userId`) USING BTREE,
  KEY `fk_deviceId` (`fk_deviceId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;";

        Db::query($sql);

        $deviceList = Db::table('device')->select();
        $insertNum = 0;
        foreach ($deviceList as $v) {
            if(!$v['deviceId']) continue;
            //检测新表
            if(!Db::table('fa_device')->where(['deviceId'=>$v['deviceId']])-> find()) {
                //写入新表
                Db::table('fa_device')->insert($v);
                $insertNum++;
            }
        }
        $this->success('success Num:'.$insertNum);

    }

    /**
     * 写入附件表
     */
    public function files()
    {
        print_r('<h4>写入附件表：</h4>');

        $sql = "CREATE TABLE IF NOT EXISTS  `fa_attachment` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片帧数',
  `filename` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '文件名称',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '透传数据',
  `createtime` int(10) DEFAULT NULL COMMENT '创建日期',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `uploadtime` int(10) DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '文件 sha1编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='附件表';";

        Db::query($sql);


    }

    /* cfg */

    public function cfg() {
        $sql = 'CREATE TABLE IF NOT EXISTS  `fa_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT \'\' COMMENT \'变量名\',
  `group` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT \'\' COMMENT \'分组\',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT \'\' COMMENT \'变量标题\',
  `tip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT \'\' COMMENT \'变量描述\',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT \'\' COMMENT \'类型:string,text,int,bool,array,datetime,date,file\',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT \'变量值\',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT \'变量字典数据\',
  `rule` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT \'\' COMMENT \'验证规则\',
  `extend` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT \'\' COMMENT \'扩展属性\',
  `setting` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT \'\' COMMENT \'配置\',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=\'系统配置\';';
        Db::query($sql);
        $sql='INSERT INTO `fa_config` VALUES (\'1\', \'name\', \'basic\', \'Site name\', \'请填写站点名称\', \'string\', \'My Website\', \'\', \'required\', \'\', null);
                    INSERT INTO `fa_config` VALUES (\'2\', \'beian\', \'basic\', \'Beian\', \'粤ICP备15000000号-1\', \'string\', \'\', \'\', \'\', \'\', null);
                    INSERT INTO `fa_config` VALUES (\'3\', \'cdnurl\', \'basic\', \'Cdn url\', \'如果全站静态资源使用第三方云储存请配置该值\', \'string\', \'\', \'\', \'\', \'\', null);
                    INSERT INTO `fa_config` VALUES (\'4\', \'version\', \'basic\', \'Version\', \'如果静态资源有变动请重新配置该值\', \'string\', \'1.0.1\', \'\', \'required\', \'\', null);
                    INSERT INTO `fa_config` VALUES (\'5\', \'timezone\', \'basic\', \'Timezone\', \'\', \'string\', \'Asia/Shanghai\', \'\', \'required\', \'\', null);
                    INSERT INTO `fa_config` VALUES (\'6\', \'forbiddenip\', \'basic\', \'Forbidden ip\', \'一行一条记录\', \'text\', \'\', \'\', \'\', \'\', null);
                    INSERT INTO `fa_config` VALUES (\'7\', \'languages\', \'basic\', \'Languages\', \'\', \'array\', \'{\"backend\":\"en\",\"frontend\":\"en\"}\', \'\', \'required\', \'\', null);
                    INSERT INTO `fa_config` VALUES (\'8\', \'fixedpage\', \'basic\', \'Fixed page\', \'请尽量输入左侧菜单栏存在的链接\', \'string\', \'dashboard\', \'\', \'required\', \'\', null);
                    INSERT INTO `fa_config` VALUES (\'9\', \'categorytype\', \'dictionary\', \'Category type\', \'\', \'array\', \'{\"default\":\"Default\",\"page\":\"Page\",\"article\":\"Article\",\"test\":\"Test\"}\', \'\', \'\', \'\', \'\');
                    INSERT INTO `fa_config` VALUES (\'10\', \'configgroup\', \'dictionary\', \'Config group\', \'\', \'array\', \'{\"basic\":\"Basic\",\"email\":\"Email\",\"dictionary\":\"Dictionary\",\"user\":\"User\",\"example\":\"Example\"}\', \'\', \'\', \'\', \'\');
                    INSERT INTO `fa_config` VALUES (\'11\', \'mail_type\', \'email\', \'Mail type\', \'选择邮件发送方式\', \'select\', \'1\', \'[\"请选择\",\"SMTP\",\"Mail\"]\', \'\', \'\', \'\');
                    INSERT INTO `fa_config` VALUES (\'12\', \'mail_smtp_host\', \'email\', \'Mail smtp host\', \'错误的配置发送邮件会导致服务器超时\', \'string\', \'smtp.qq.com\', \'\', \'\', \'\', \'\');
                    INSERT INTO `fa_config` VALUES (\'13\', \'mail_smtp_port\', \'email\', \'Mail smtp port\', \'(不加密默认25,SSL默认465,TLS默认587)\', \'string\', \'465\', \'\', \'\', \'\', \'\');
                    INSERT INTO `fa_config` VALUES (\'14\', \'mail_smtp_user\', \'email\', \'Mail smtp user\', \'（填写完整用户名）\', \'string\', \'10000\', \'\', \'\', \'\', \'\');
                    INSERT INTO `fa_config` VALUES (\'15\', \'mail_smtp_pass\', \'email\', \'Mail smtp password\', \'（填写您的密码）\', \'string\', \'password\', \'\', \'\', \'\', \'\');
                    INSERT INTO `fa_config` VALUES (\'16\', \'mail_verify_type\', \'email\', \'Mail vertify type\', \'（SMTP验证方式[推荐SSL]）\', \'select\', \'2\', \'[\"无\",\"TLS\",\"SSL\"]\', \'\', \'\', \'\');
                    INSERT INTO `fa_config` VALUES (\'17\', \'mail_from\', \'email\', \'Mail from\', \'\', \'string\', \'10000@qq.com\', \'\', \'\', \'\', \'\');';
        $this->importsql($sql);
        echo 'success';
    }

    //写入绑定表
    public function addBind() {
        $sql = 'CREATE TABLE IF NOT EXISTS  `fa_device_bind_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ctime` int(11) NOT NULL DEFAULT \'0\',
  `deviceId` int(11) NOT NULL DEFAULT \'0\' COMMENT \'设备id\',
  `installerUid` int(11) NOT NULL DEFAULT \'0\' COMMENT \'安装商uid\',
  `customerUid` int(11) NOT NULL DEFAULT \'0\' COMMENT \'使用者uid\',
  `installAddress` varchar(255) NOT NULL DEFAULT \'\' COMMENT \'安装地址\',
  `customerName` varchar(255) NOT NULL DEFAULT \'\' COMMENT \'客户姓名\',
  `customerEmail` varchar(50) NOT NULL DEFAULT \'\' COMMENT \'邮箱\',
  `installTime` int(11) NOT NULL DEFAULT \'0\' COMMENT \'设备安装日期\',
  `memo` text COMMENT \'备注信息\',
  PRIMARY KEY (`id`),
  KEY `deviceId` (`deviceId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=\'安装商绑定用户的操作日志\';';
        Db::query($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS `fa_device_bind_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ctime` int(11) NOT NULL DEFAULT \'0\',
  `deviceId` int(11) NOT NULL DEFAULT \'0\' COMMENT \'设备id\',
  `installerUid` int(11) NOT NULL DEFAULT \'0\' COMMENT \'安装商uid\',
  `customerUid` int(11) NOT NULL DEFAULT \'0\' COMMENT \'使用者uid\',
  `installAddress` varchar(255) NOT NULL DEFAULT \'\' COMMENT \'安装地址\',
  `customerName` varchar(255) NOT NULL DEFAULT \'\' COMMENT \'客户姓名\',
  `customerEmail` varchar(50) NOT NULL DEFAULT \'\' COMMENT \'邮箱\',
  `installTime` int(11) NOT NULL DEFAULT \'0\' COMMENT \'设备安装日期\',
  `memo` text COMMENT \'备注信息\',
  PRIMARY KEY (`id`),
  UNIQUE KEY `deviceId` (`deviceId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=\'安装商绑定用户的记录，一个设备只能绑定一个用户。暂时不用绑定具体的uid。\';';
        Db::query($sql);
        echo 'success';

    }
}
