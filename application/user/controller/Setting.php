<?php

namespace app\user\controller;

use app\common\controller\Frontend;
use app\common\model\Users;
use Func\Str;

class Setting extends Frontend
{

    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $keyword = '';
    protected $allTypes = [];

    public function _initialize()
    {
        parent::_initialize();
    }
    //生成会员头像
    protected static function createUserFaceUrl($userId) {
        return UPLOAD_PATH ."/avatar/". $userId ."_". Str::getRam(8).".jpg";
    }

    //选择系统头像
    public function chose_face(){
        $faceid = input('faceid', 1, 'int');
        if( !$faceid ){
            return $this->error('0023');//缺少必填的信息，请重试
        }
        //判断系统文件是否存在
        $fileUrl = '/resource/system/images/face/' . $faceid . '.jpg';
        $target_url = self::createUserFaceUrl($this->userId);
        if (!file_exists(RootPath. $fileUrl)) {
            return $this->error('0502', '本地图片不存在:' . $fileUrl); //图片文件不存在
        }
        $file_url_local = RootPath . trim($fileUrl, '/');
        $myUrl = RootPath . trim($target_url, '/');
        //检测目录是否存在，不存在则创建
        if(!file_exists(dirname($myUrl))){
            mkdir (dirname($myUrl), 0755, true );
        };
        @copy($file_url_local, $myUrl);
        Users::where('id', $this->auth->id)->update(['avatar' => $target_url]); //更新头像地址
        return $this->error('0267', $target_url); //头像设置成功

    }
    //我的文章
    public function index(){
        $uInfo = $this->auth->getToken();
        $this->view->assign('webTitle',   '控制面板');
        $this->view->assign('uInfo',   $uInfo);
        print_r($this->view->fetch());
    }

}
