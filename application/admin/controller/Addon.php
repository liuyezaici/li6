<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\admin\model\AuthRule;
use think\addons\AddonException;
use think\addons\Service;
use think\Cache;
use think\Config;
use think\Exception;
use fast\Addon as AddonModel;
use app\common\model\Config as ConfigModel;
use think\Db;

/**
 * 插件管理
 *
 * @icon fa fa-circle-o
 * @remark 可在线安装、卸载、禁用、启用插件，同时支持添加本地插件。FastAdmin已上线插件商店 ，你可以发布你的免费或付费插件：<a href="http://www.fastadmin.net/store.html" target="_blank">http://www.fastadmin.net/store.html</a>
 */
class Addon extends Backend
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 配置
     */
    public function config($addonname = NULL)
    {
        if (!$addonname)
        {
            $this->error('组件名字不能为空');
        }
        if(!AuthRule::getbyauthPath($addonname)) {
            $this->error('组件未安装');
        }
        $config = AddonModel::getAddonConfig($addonname);
        if ($this->request->get('get_info'))
        {
            return $this->result($config, 1);
        }
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                foreach ($config as $k => &$v)
                {
                    if (isset($v['name']) && isset($params[$v['name']]))
                    {
                        if ($v['type'] == 'array')
                        {
                            $fieldarr = $valuearr = [];
                            $field = $params[$v['name']]['field'];
                            $value = $params[$v['name']]['value'];

                            foreach ($field as $m => $n)
                            {
                                if ($n != '')
                                {
                                    $fieldarr[] = $field[$m];
                                    $valuearr[] = $value[$m];
                                }
                            }
                            $params[$v['name']] = array_combine($fieldarr, $valuearr);
                            $value = $params[$v['name']];
                        }
                        else
                        {
                            $value = is_array($params[$v['name']]) ? implode(',', $params[$v['name']]) : $params[$v['name']];
                        }

                        $v['value'] = $value;
                    }
                }
//                print_r($params);exit;
                try
                {
                    //更新配置文件 set_addon_config
                    set_addon_fullconfig($addonname, $params);
                    $this->success();
                }
                catch (Exception $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("addonName", $addonname);
        $this->view->assign("addon", ['config' => $config]);
//        print_r($config);exit;
        $viewPath = \fast\Addon::getAddonPathUrl($addonname) . '/'. $addonname . '/view/config/edit.html';
        print_r($this->view->fetch($viewPath));
    }

    /**
     * 安装
     */
    public function install($addonname)
    {
        if (!$addonname)
        {
            $this->error('组件名不能为空');
        }
        if(AuthRule::getbyauthPath($addonname)) {
            $this->error('组件已安装过了');
        }
        Db::startTrans();
        try
        {
            (new \fast\Addon()) ->installAddon($addonname);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage(), $e->getCode());
        }
        $this->success(__('Install successful'));
    }

    /**
     * 卸载
     */
    public function uninstall($addonname='')
    {
        if (!$addonname)
        {
            $this->error('组件名不能为空');
        }
        if(!AuthRule::getbyauthPath($addonname)) {
            $this->error('组件未安装');
        }
        try
        {
            (new \fast\Addon()) ->uninstall($addonname);
            $this->success(__('Install successful'));
        }
        catch (AddonException $e)
        {
            $this->result($e->getData(), $e->getCode(), $e->getMessage());
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage(), $e->getCode());
        }
    }
	
	public function info(){
        $name = $this->request->post("name");
        $package = $this->request->post("package");
        $info = get_package_info($name);
        return $this->result($info, 1, __('Info successful'));
	}

    /**
     * 禁用
     */
    public function forbid($addonname='')
    {
        if (!$addonname)
        {
            $this->error('组件名不能为空');
        }
        if(!AuthRule::getbyauthPath($addonname)) {
            $this->error('组件未安装');
        }
        try
        {
            (new \fast\Addon()) ->disable($addonname);
            $this->success(__('Install successful'));
        }
        catch (AddonException $e)
        {
            $this->result($e->getData(), $e->getCode(), $e->getMessage());
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage(), $e->getCode());
        }
    }
    /**
     * 启用
     */
    public function allow($addonname='')
    {
        if (!$addonname)
        {
            $this->error('组件名不能为空');
        }
        if(!AuthRule::getbyauthPath($addonname)) {
            $this->error('组件未安装');
        }
        try
        {
            (new \fast\Addon()) ->enable($addonname);
            $this->success(__('Install successful'));
        }
        catch (AddonException $e)
        {
            $this->result($e->getData(), $e->getCode(), $e->getMessage());
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage(), $e->getCode());
        }
    }


    /**
     * 查看
     */
    public function index()
    {

        // ajax加载
        if ($this->request->isPost()) {
            $page = input('page', 1, 'int');
            $limit = (int) $this->request->get("limit", 10);
            $search = strtolower($this->request->get("title"));
            $search = trim(htmlspecialchars(strip_tags($search)));
            $group = $this->request->get("group", '');
//            echo $group;
//            exit;
            $group = trim(htmlspecialchars(strip_tags($group)));
            $offset = ($page-1) * $limit;
            $list = [];
            $packages = get_package_addon();
            foreach ($packages as $k => $v)
            {
                //标题搜索
                if ($search && !strstr($v['name'], $search) && !strstr($v['title'], $search)){
                    continue;
                }
                //分组搜索
                if($group && $group != '全部' && isset($v['group']) && $group != trim($v['group'])) {
                    continue;
                }
                if($group == '未分组' && (!isset($v['group']) || !empty($v['group']))) continue;
                $v['group'] = isset($v['group']) ? $v['group'] : '其它';
                $list[] = $v;
            }
            $total = count($list);
            if ($limit)
            {
                $list = array_slice($list, $offset, $limit);
            }
            $result = array("total" => $total, "page" => $page, "rows" => $list);
            $callback = $this->request->get('callback') ? "jsonp" : "json";
            return $callback($result);
        }
        $addons = get_package_addon();
        $group = [];
        $group[] = [
            'value' => '全部',
            'text' => '全部',
        ];;
        $arrayGroup = [];
        foreach($addons as $v){
            if(isset($v['group'])) {
                if(in_array(trim($v['group']), $arrayGroup)) continue;
                $group[] = [
                    'value' => trim($v['group']),
                    'text' => trim($v['group']),
                ];
                $arrayGroup[] = trim($v['group']);
            }
        }
        $group[] = [
            'value' => '未分组',
            'text' => '未分组',
        ];
        $this->view->assign('allGroup', json_encode($group));
       print_r($this->view->fetch());
    }
}
