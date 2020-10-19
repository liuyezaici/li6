<?php

namespace app\admin\command;

use think\addons\AddonException;
use think\addons\Service;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Exception;
use think\exception\PDOException;

class Addon extends Command
{

    protected function configure()
    {
        $this
                ->setName('addon')
                ->addOption('name', 'a', Option::VALUE_REQUIRED, 'addon name', null)
                ->addOption('action', 'c', Option::VALUE_REQUIRED, 'action(create/enable/disable/install/uninstall/refresh/upgrade/package)', 'create')
                ->addOption('force', 'f', Option::VALUE_OPTIONAL, 'force override', null)
                ->addOption('release', 'r', Option::VALUE_OPTIONAL, 'addon release version', null)
                ->addOption('uid', 'u', Option::VALUE_OPTIONAL, 'fastadmin uid', null)
                ->addOption('token', 't', Option::VALUE_OPTIONAL, 'fastadmin token', null)
                ->setDescription('Addon manager');
    }

    /**
     * 获取创建菜单的数组
     * @param array $menu
     * @return array
     */
    protected function getCreateMenu($menu)
    {
        $result = [];
        foreach ($menu as $k => & $v)
        {
            $arr = [
                'name'  => $v['name'],
                'title' => $v['title'],
            ];
            if ($v['icon'] != 'fa fa-circle-o')
            {
                $arr['icon'] = $v['icon'];
            }
            if ($v['ismenu'])
            {
                $arr['ismenu'] = $v['ismenu'];
            }
            if (isset($v['childlist']) && $v['childlist'])
            {
                $arr['sublist'] = $this->getCreateMenu($v['childlist']);
            }
            $result[] = $arr;
        }
        return $result;
    }

    /**
     * 写入到文件
     * @param string $name
     * @param array $data
     * @param string $pathname
     * @return mixed
     */
    protected function writeToFile($name, $data, $pathname)
    {
        $search = $replace = [];
        foreach ($data as $k => $v)
        {
            $search[] = "{%{$k}%}";
            $replace[] = $v;
        }
        $stub = file_get_contents($this->getStub($name));
        $content = str_replace($search, $replace, $stub);

        if (!is_dir(dirname($pathname)))
        {
            mkdir(strtolower(dirname($pathname)), 0755, true);
        }
        return file_put_contents($pathname, $content);
    }

    /**
     * 获取基础模板
     * @param string $name
     * @return string
     */
    protected function getStub($name)
    {
        return __DIR__ . '/Addon/stubs/' . $name . '.stub';
    }

}
