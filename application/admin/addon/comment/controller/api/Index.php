<?php

namespace app\admin\addon\comment\controller\api;

use app\api\controller\Common;
use fast\Addon;

/**
 * 对外接口
 * @internal
 */
class Index extends Common
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();

        $this->addonName ='comment';
        $this->model = Addon::getModel($this->addonName);
        $this->statisticsModel = Addon::getModel($this->addonName,'CommentStatistics');
        $this->configModel = Addon::getAddonConfig($this->addonName);
    }
	
	public function index(){
		$this->success(__('success'), []);
	}


	//获取某个组件的评价参数
	public function getCommentOption(){
        $module = input('source','goods');//模块名
        if(!$module)$this->error('组件名不能为空');
        $moduleData = $this->configModel->getModuleConfig($module);
        $moduleData = $moduleData ? json_decode($moduleData,true):'';

        exit(json_encode(['code'=>'1','msg'=>'success','data'=>$moduleData]));
//        $this->success('success',$moduleData);
    }

    //检查是否存在attr
    protected function checkattr($source,$attr){
        $moduleData = $this->configModel->getModuleConfig($source);
        $moduleData = $moduleData ? json_decode($moduleData,true):'';
        if($moduleData){
            foreach ($moduleData as $v){
                if($v['attr']==$attr){
                    return true;
                }
            }
        }
        return false;
    }


    //检查是否存在$grade是否满足最大分
    protected function checkGrade($source, $attr, $grade){
        $moduleData = $this->configModel->getModuleConfig($source);
        $moduleData = $moduleData ? json_decode($moduleData,true):'';
        if($moduleData){
            foreach ($moduleData as $v){
                if($v['attr']==$attr && $v['max_num'] >= $grade){
                    return true;
                }
            }
        }
        return false;
    }

    //提交评价
    public function postComment(){
        $text = input('text');//评价内容
        $grade = input('grade');//评价分数
        $sourceid = input('sourceid');//组件对象id
        $pictures = input('pictures');//图片
        $source = input('source');//组件名
        $attr = input('attr');//评价属性
        if(!$attr)$this->error('attr不能为空');
        if(!$sourceid)$this->error('sourceid不能为空');
        if(!$source)$this->error('source不能为空');
        if(!$this->checkattr($source,$attr))$this->error('找不到该attr，请先配置');
        if($grade&&!$this->checkGrade($source,$attr,$grade))$this->error('分数不能超过设置的最大值');

        $data = [
            'source'=>$source,
            'attr'=>$attr,
            'sourceid' => $sourceid,
            'userid' => (int)$this->auth->id,
            'grade' => $grade,
            'text' => $text,
            'status' => 1,
            'createtime' => time(),
            'pictures' => $pictures,
        ];
        if($this->model->insert($data)){
            $this->statisticsModel->addCommentStatistics($data);
            $this->success('评价成功',$data);
        }else{
            $this->error('评价失败');
        }
    }

    //评价列表
    public function getComment(){
        $page = input('page', 1, 'int');
        $pagesize = input('pagesize', 10, 'int');

        $field = '*';

        $map =[
            'status'=>1,
        ];
        $list_page = list_page($this->model,$map,$page,$pagesize,'id desc',$field);

        $this->success('获取成功',$list_page);
    }

    //获取某个对象的评分
    public function getTargetScore(){
        $sourceid = input('sourceid');
        $source = input('source');//组件名
        if(!$sourceid)$this->error('sourceid不能为空');
        if(!$source)$this->error('source不能为空');
        $output = [];
        $moduleAttrStatistics = $this->statisticsModel->where(['sourceid'=>$sourceid,'source'=>$source])->select();
        if($moduleAttrStatistics){
            foreach ($moduleAttrStatistics as $v ){
                $attrData =  $this->statisticsModel->where(['sourceid'=>$sourceid,'source'=>$source,'attr'=>$v['attr']])->find();
                $avg = !empty($attrData['avg'])?$attrData['avg']:0;
                $output[$v['attr']] = $avg;
            }
        }

        $this->success('评分',$output ?: (object)array());
    }
}
