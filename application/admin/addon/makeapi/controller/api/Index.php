<?php

namespace app\admin\addon\makeapi\controller\api;

use app\api\controller\Common;
use fast\Addon;

/**
 * 对外接口
 * @internal
 */
class Index extends Common
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();

        $this->addonName = 'makeapi';
        $this->model = Addon::getModel($this->addonName);
    }
	
	public function index(){
		$this->success(__('success'), []);
	}

    // 获取接口
    public function getApi()
    {
        // 接口索引名
	    $keyname = input('keyname');

        $apis = $this->model->where('keyname', $keyname)->field('apis')->find();
        $apiArr = json_decode($apis, true);

        $returnData = array();
        foreach ($apiArr['apis'] as $key => $api)
        {
            $modelName = explode('.', $api['model']);
            $model_ = Addon::getModel($modelName[0], $modelName[1]);
            $method_ = $api['method'];

            if(!$model_) continue;
            if(!method_exists($model_, $method_)) continue;

            $params = json_decode($api['params'], true);
            if(!is_array($params)) $this->error("方法'{$method_}'参数格式错误");

            $returnData[$api['backKey']] = call_user_func_array(array($model_, $method_), $params);
        }

        return $this->success('获取成功', $returnData);
    }
}