<?php

namespace app\admin\addon\feedback\controller\api;

use app\api\controller\Common;
use fast\Random;
use think\Validate;
use think\Db;
use fast\Addon;
/**
 * 对外接口
 * @internal
 */
class Index extends Common
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['submitFeedback'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();

        $this->addonName ='feedback';
        $this->model = Addon::getModel($this->addonName);
    }
	
	public function index(){
		$this->success(__('success'), []);
	}

    /**
     * 常见问题
     *
     */
    public function feedbackQuestions(){
        $configInfo = Db('feedback_config')->find();
        if(!$configInfo['config'] || strlen($configInfo['config']) < 3)  {
            $this->error('未配置分类', ['data' => []]);
        }
        $question = json_decode($configInfo['config'], true);
        $data['normal_question'] = explode("\r\n",$question['normal_question']);
        $data['oid_must'] = $question['oid_must'];
//        $question = $data['normal_question'];
        $this->success('问题反馈类型', $data);
    }

    /**
     * 提交反馈信息
     */
    public  function  submitFeedback(){
        $oid =  input('oid', 0);
        $tel =  input('tel', '');
        $username =  input('username', '');
        $question =  input('question', '');
        $content =  input('content')?:$this->error('内容不能为空');
        $pic = input('pic');
//        $picResult = $this->upload('pic', true);
//        if(!isset($picResult[0]))  $picResult = [$picResult];
//        $pictures = array();
//        foreach($picResult as $k => $v){
//            if($v['url']){
//                $pictures[] = $v['url'];
//            }
//        }
//        if(count($pictures))  $pictures = implode(",", $pictures);
        $newData = [
            'uid'   =>$this->auth->id,
            'oid'   =>$oid,
            'pic'   =>$pic,
            'username'  =>$username,
            'question'  =>$question,
            'content'   =>$content,
            'tel'   =>$tel,
            'status'   =>0,
            'ctime'     =>time(),
        ];
        $data = Db('feedback')->insert($newData);
        if($data){
            $this->success('提交成功');
        }else{
            $this->error('提交失败');
        }
    }
}
