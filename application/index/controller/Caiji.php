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

class Caiji extends Frontend
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

    public function caijiGoogleMap() {

        print_r($this->fetch()) ;
    }
}
