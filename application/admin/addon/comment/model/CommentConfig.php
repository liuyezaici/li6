<?php

namespace app\admin\addon\comment\model;

use think\Model;

class CommentConfig extends Model
{
    protected $type = [
        'config'  => 'json',
    ];

    //获取组件对应配置
    public function getModuleConfig($module=''){
        $config = $this->getFieldById(1, 'config');
        $newData = array();
        if($config){
            $configArr = json_decode($config,true);
            foreach ($configArr as $v){
                if(!empty($v['source']) && $v['source']==$module){
                    $newData[] = $v;
                }
            }
        }
        return json_encode($newData);
    }
}
