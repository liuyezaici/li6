<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use fast\Addon;
use think\Db;

/**
 * 分成管理
 *
 * @remark   定义各种对象的分成比例，如：场地 商品
 */
class Divide extends Backend
{

    protected $noNeedRight = ['modify', 'index'];
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    //编辑
    public function modify()
    {
        if ($this->request->isPost()){

            $addonName = trim($this->request->get('addon'));
            $sourceId = trim($this->request->get('id'));
            $postData = input('row/a');
            if(!isset($postData['agent1_percen'])) $this->error('缺少参数 agent1_percen');
            if(!isset($postData['agent2_percen'])) $this->error('缺少参数 agent2_percen');
            if(!isset($postData['agent3_percen'])) $this->error('缺少参数 agent3_percen');
            if(!isset($postData['seller_percen'])) $this->error('缺少参数 seller_percen');
            $agent1_percen = trim($postData['agent1_percen']);
            $agent1_percen = floatval($agent1_percen);
            $agent2_percen = trim($postData['agent2_percen']);
            $agent2_percen = floatval($agent2_percen);
            $agent3_percen = trim($postData['agent3_percen']);
            $agent3_percen = floatval($agent3_percen);
            $seller_percen = trim($postData['seller_percen']);
            $seller_percen = floatval($seller_percen);
            if(!$addonName) $this->error('缺少参数 addon');
            if(!$sourceId) $this->error('缺少参数 id');
            $model = Addon::getModel($addonName);
            if(!$model) {
                $this->error("{$addonName} model不存在");
            }
            if(!method_exists($model, 'dataLimit')) {
                $this->error('未配置dataLimit');
            }
            $limitInfo = $model->dataLimit();
            $this->dataLimit = $limitInfo['dataLimit'];
            $this->dataLimitSelf = $limitInfo['dataLimitSelf'];
            $this->dataLimitIdent = $limitInfo['dataLimitIdent'];
            $this->dataLimitField = $limitInfo['dataLimitField'];

            $row = $model::get($sourceId);
            if (!$row) $this->error(__('No Results were found'));
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds))
            {
                if (!in_array($row[$this->dataLimitField], $adminIds))
                {
                    $this->error('您没有权限访问他人数据');
                }
            }

            $lastLog = Db::name('divide')->where([
                'addon' => $addonName,
                'sourceid' => $sourceId,
            ])->find();
            if(!$lastLog) { //new
                if($this->dataLimitIdent == 'seller' && $this->auth->identIsSeller($this->auth->identity)) { //如果我是商家  校验下级

//
//                    $sourceOwner = $model->field($this->dataLimitField)->where([
//                        'id' => $sourceId
//                    ])->value($this->dataLimitField);
//                    if(!$sourceOwner) {
//                        $this->error('资源:'. $sourceId .'不存在');
//                    }
//                    if($sourceOwner !=  $this->auth->id) {
//                        $this->error('资源:'. $sourceId .'不属于你的');
//                    }
                }
                $newLog = [
                    'addon' => $addonName,
                    'sourceid' => $sourceId,
                    'ctime' => time(),
                    'agent1_percen' => $agent1_percen,
                    'agent2_percen' => $agent2_percen,
                    'agent3_percen' => $agent3_percen,
                    'seller_percen' => $seller_percen,
                ];
                Db::name('divide')->insert($newLog);
                $this->success('更新成功');
            } else {
                $newLog = [
                    'agent1_percen' => $agent1_percen,
                    'agent2_percen' => $agent2_percen,
                    'agent3_percen' => $agent3_percen,
                    'seller_percen' => $seller_percen,
                ];
                Db::name('divide')->where([
                    'id'=> $lastLog['id']
                ])->update($newLog);
                $this->success('更新成功');
            }
        } else {
            $this->error('nopost');
        }

    }

    /**
     * 配置
     */
    public function index()
    {
        $addonName = trim($this->request->get('addon'));
        $sourceId = trim($this->request->get('id'));
        if(!$addonName) $this->error('缺少参数 addon');
        if(!$sourceId) $this->error('缺少参数 id');
        $model = Addon::getModel($addonName);
        if(!$model) {
            $this->error("{$addonName} model不存在");
        }
        if(!method_exists($model, 'dataLimit')) {
            $this->error('未配置dataLimit');
        }
        $limitInfo = $model->dataLimit();
        $this->dataLimit = $limitInfo['dataLimit'];
        $this->dataLimitSelf = $limitInfo['dataLimitSelf'];
        $this->dataLimitIdent = $limitInfo['dataLimitIdent'];
        $this->dataLimitField = $limitInfo['dataLimitField'];

        $row = $model::get($sourceId);
        if (!$row) $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error('您没有权限访问他人数据');
            }
        }

        $lastLog = Db::name('divide')->where([
            'addon' => $addonName,
            'sourceid' => $sourceId,
        ])->find();
        if(!$lastLog) {
            $lastLog = [
                'agent1_percen' => 0,
                'agent2_percen' => 0,
                'agent3_percen' => 0,
                'seller_percen' => 0,
            ];
        }
        $this->result($lastLog, 1);
    }
}
