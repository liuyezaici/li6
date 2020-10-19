<?php
namespace app\admin\addon\message\model;

use think\Model;
use fast\Addon;

class Message extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';



    //获取管理员id
    public function getAdminId() {
        $configResult = Addon::getAddonConfig($this->addonName);
        if(!$configResult)  return '未写入config配置信息';
        $cfgInfo = $configResult['config'] ;
        if(empty($cfgInfo['admin_id'])) return '未配置 admin_id';
        return $cfgInfo['admin_id'];
    }


    //写入站内信
    public function inserMsg($data = []){
        $admin_id = $this->getAdminId();
        if(!is_numeric($admin_id)) return $admin_id;
        $details = !empty($data['details'])?$data['details']:'';//信息内容
        $title = !empty($data['title'])?$data['title']:'';//信息标题
        $source = !empty($data['source'])?$data['source']:'';//数据源组件名
        $source_id = !empty($data['source_id'])?$data['source_id']:0;//数据源id
        $form_uid = !empty($data['form_uid'])?$data['form_uid']:$this->getAdminId(); //发送人
        $to_uid = !empty($data['to_uid'])?$data['to_uid']:0;//接收人
        if(!$to_uid) return '收件人不能为空';
        if(!$details) return '内容不能为空';
        if(!$title) return '标题不能为空';

        $addData = [
            'details'=>$details,
            'title'=>$title,
            'source'=>$source,
            'source_id'=>$source_id,
            'form_uid'=>$form_uid,
            'to_uid'=>$to_uid,
            'ctime'=>time(),
        ];
        $result = $this->insert($addData);
        if(!$result){
            return '插入站内信失败';
        }
        return true;
    }
}
