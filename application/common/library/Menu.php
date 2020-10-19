<?php

namespace app\common\library;

use app\admin\model\AuthRule;
use fast\Tree;
use think\Exception;
use think\exception\PDOException;

class Menu
{

    /**
     * 创建菜单
     * @param array $menu
     * @param mixed $parent 父类的name或pid
     * @param mixed $ver package插件版本
     */
    public static function create($menu, $parent = 0)
    {
        if (!is_numeric($parent))
        {
            $parentRule = AuthRule::getByPath($parent);
            $pid = $parentRule ? $parentRule['id'] : 0;
        }
        else
        {
            $pid = $parent;
        }
        $allow = array_flip(['file', 'auth_path', 'title', 'icon', 'order', 'condition', 'remark', 'ismenu']);
        foreach ($menu as $k => $v)
        {
            $hasChild = isset($v['sublist']) && $v['sublist'] ? true : false;

            $data = array_intersect_key($v, $allow);
            $data['auth_path'] = isset($v['name']) ? $v['name'] : '';
            $data['order'] = isset($v['weigh']) ? $v['weigh'] : 0;
            $data['ismenu'] = isset($v['ismenu']) ? $v['ismenu'] : ($hasChild ? 1 : 0);
            $data['icon'] = isset($v['icon']) ? $v['icon'] : ($hasChild ? 'fa fa-list' : 'fa fa-circle-o');
            $data['pid'] = $pid;
            $data['status'] = 1;
//            print_r($data);exit;
            try
            {
                $menu = AuthRule::create($data);
                if ($hasChild)
                {
                    self::create($v['sublist'], $menu->id);
                }
            }
            catch (PDOException $e)
            {
                throw new Exception('err:'. AuthRule::getlastsql());
//                throw new Exception('err:'.$e->getMessage());
            }
        }
    }

    /**
     * 删除菜单
     * @param string $name 规则name 
     * @return boolean
     */
    public static function delete($name)
    {
        $ids = self::getByRule($name);
        if (!$ids)
        {
            return false;
        }
        AuthRule::destroy($ids);
        return true;
    }

    /**
     * 启用菜单
     * @param string $name
     * @return boolean
     */
    public static function enable($name)
    {
        $ids = self::getByRule($name);
        if (!$ids)
        {
            return false;
        }
        AuthRule::where('id', 'in', $ids)->update(['status' => 1]);
        return true;
    }

    /**
     * 禁用菜单
     * @param string $name
     * @return boolean
     */
    public static function disable($name)
    {
        $ids = self::getByRule($name);
        if (!$ids)
        {
            return false;
        }
        AuthRule::where('id', 'in', $ids)->update(['status' => 0]);
        return true;
    }

    /**
     * 导出指定名称的菜单规则
     * @param string $name
     * @return array
     */
    public static function export($pathName, $root = true)
    {
        $ids = self::getByRule($pathName);
        if (!$ids)
        {
            return [];
        }
        $menuList = [];
        $menu = AuthRule::getByauthPath($pathName);
        if ($menu)
        {
            $ruleList = collection(AuthRule::where('id', 'in', $ids)->select())->toArray();
            $menuList = Tree::instance()->init($ruleList)->getTreeArray($menu['id']);
			if($root){
				$temp = $menu->toArray();
				$temp['childlist'] = $menuList;
				$menuList = $temp;
			}
        }
        return $menuList;
    }

    /**
     * 根据名称获取规则IDS
     * @param string $name
     * @return array
     */
    public static function getByRule($authPath='')
    {
        $menu = AuthRule::getByauthPath($authPath);
        $menuId = $menu['id'];
        if (!$menuId) return 0;
        {
            // 必须将结果集转换为数组
            $ruleList = collection(AuthRule::order('order', 'desc')->field('id,pid,auth_path')->select())->toArray();
            // 构造菜单数据
            $ids = Tree::instance()->init($ruleList)->getChildrenIds($menuId, true);
        }
        return $ids;
    }

}
