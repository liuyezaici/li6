<?php
namespace app\admin\addon\juzi\model;

use app\admin\addon\fujian\model\Fujian as FujianModel;
use think\Model;
use think\Db;

class UserMusicArticle extends Model
{
    public static function recountArticles($musicId=0) {
        $num = self::where('musicId', $musicId)->count();
        Musictimemodel::where('id', $musicId)->update([
            'articles' => $num
        ]);
    }
}
