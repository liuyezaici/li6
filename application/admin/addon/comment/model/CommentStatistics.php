<?php

namespace app\admin\addon\comment\model;

use think\Model;

class CommentStatistics extends Model
{
    protected $type = [
        'config'  => 'json',
    ];

    //插入统计
    public function addCommentStatistics($data=[]){
        $source = !empty($data['source'])?$data['source']:'';//组件名
        $attr = !empty($data['attr'])?$data['attr']:'';//评价属性
        $sourceid = !empty($data['sourceid'])?$data['sourceid']:'';//组件对象id

        $commentModel = \fast\Addon::getModel('comment');
        $commentattrScore = $commentModel->where(['sourceid'=>$sourceid,'source'=>$source,'attr'=>$attr])->avg('grade');
        if($source&&$attr&&$sourceid){
            $attrData = $this->where(['sourceid'=>$sourceid,'source'=>$source,'attr'=>$attr])->find();
            if($attrData){
                $this->where(['id'=>$attrData['id']])->update(['avg'=>$commentattrScore]);
            }else{
                $addData = [
                    'sourceid'=>$sourceid,
                    'source'=>$source,
                    'attr'=>$attr,
                    'avg'=>$commentattrScore
                ];
                $this->insert($addData);
            }
        }
    }
}
