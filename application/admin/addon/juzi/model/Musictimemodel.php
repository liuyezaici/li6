<?php
namespace app\admin\addon\juzi\model;

use app\admin\addon\fujian\model\Fujian as FujianModel;
use think\Model;
use think\Db;

class Musictimemodel extends Model
{
    protected $name = 'userMusic';

    //删除旧的封面图
    public function deleteOldMusic($sid=0) {
        $lastInfo = FujianModel::field('id,fileurl_local')->where([
            'addon_name' => 'juzi.Musictimemodel',
            'addon_sourceid' => $sid,
        ])->find();
        if($lastInfo) {
            $lastId = $lastInfo['id'];
            $fileurl_local = $lastInfo['fileurl_local'];
            if(file_exists(ROOT_PATH . $fileurl_local)) unlink(ROOT_PATH . $fileurl_local);
            FujianModel::where('id', $lastId)->delete();
        }
    }
    //上传成功回调
    public function uploadSuccess($sid=0, $fileInfo=[]) {
        self::where([
            'id' => $sid,
        ])->update([
            'songPathUrl' => $fileInfo['url'],
            'songSize' => $fileInfo['size'],
            'songGeshi' => $fileInfo['type'],
            'songTime' => $fileInfo['songTime'],
        ]);
    }

}
