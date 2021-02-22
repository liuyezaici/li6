<?php

namespace app\index\controller;

use app\admin\addon\fujian\model\Fujian;
use app\common\controller\Frontend;
use app\common\model\Users;
use fast\File;
use fast\Area;
use fast\Addon;
use \app\admin\addon\article\model\Article as ArticleModel;
use \app\admin\addon\article\model\ArticleTypes as ArticleTypesModel;
use fast\Str;

class Tool extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $keyword = '';
    protected $allTypes = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->allTypes = ArticleTypesModel::select();
        $this->keyword = input('keyword', '', 'trim');
    }

    public function get_province() {
        $info = [
            'province' => 440000,
        ];
        $this->success('', '', $info);
    }

    public function get_all_area() {
        $info = Area::getAllArea();
        $this->success('', '', $info);
    }
    public function get_all_province() {
        $info = Area::getAllProvince();
        $this->success('', '', $info);
    }
    public function get_info() {
        $info = [
            'time' => Str::getRam(5),
            'title' => Str::getRandChar(5),
            'content' => json_encode(['a'=>Str::getRam(3), 'b'=>Str::getRam(6)]),
        ];
        $this->success('', '', $info);
    }
    public function get_rows() {
        $list = [];
        $num = input('num', 3, 'intval');
        for($i=0; $i< $num; $i++) {
            $list[] = [
                'id' => $i+1,
                'title' => Str::getRandChar(5)
            ];
        }
        $this->success('', '', $list);
    }
    public function get_group() {
        $this->success('', '', ['group' => 1]);
    }
}
