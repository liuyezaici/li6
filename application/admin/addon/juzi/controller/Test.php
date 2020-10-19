<?php
namespace app\admin\addon\juzi\controller;

use app\common\model\Users;
use app\common\controller\Backend;

use think\Db;
use fast\Addon;
use app\admin\addon\juzi\model\Juzi as juziModel;
use app\admin\addon\juzi\model\Juzi_from as juziFromModel;
/**
 * 句子
 * @internal
 */
class Test extends Backend
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new juziModel();
    }

    //句子列表
    public function index()
    {
         //SELECT id,authorid,title FROM `jz_juzi_from` where title like "%\n%"
            $list = juziFromModel::where('title', 'like', "%\n%")
                ->limit(100)
                ->select();
//
//                print_r(json_encode($list));
//                exit;
            foreach ($list as $n => $v) {
                $fromId = $v['id'];
                $title = $v['title'];
                $authorid = $v['authorid'];
                $title = explode('》', $title)[0].'》';
                $title = trim($title);
//                print_r($title);
//                exit;
                $sameInfo=juziFromModel::where([
                    'id'=> ['<>', $fromId],
                    'title'=>$title
                ])->find();
                if($sameInfo) {
                    juziModel::where([
                        'fromid'=>$fromId,
                    ])->update([
                        'fromid'=> $sameInfo['id'],
                    ]);
                    juziFromModel::where([
                        'id'=>  $fromId,
                    ])->delete();
                    print_r("修改：".$fromId . '为：'. $sameInfo['id']."\n");
                } else {
                    juziFromModel::where([
                        'id'=>  $fromId,
                    ])->update([
                        'title'=>$title
                    ]);
                }
//                print_r($fromId . '|'.json_encode($sameInfo));
//                exit;
            }
    }

}


