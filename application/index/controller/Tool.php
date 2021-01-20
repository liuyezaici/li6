<?php

namespace app\index\controller;

use app\admin\addon\fujian\model\Fujian;
use app\common\controller\Frontend;
use app\common\model\Users;
use fast\File;
use fast\Date;
use fast\Addon;
use \app\admin\addon\article\model\Article as ArticleModel;
use \app\admin\addon\article\model\ArticleTypes as ArticleTypesModel;

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

    public function get_rows() {
        $this->success('', '', [
            [
                'id' => 1,
                'title' => 'aa'
            ],
            [
                'id' => 2,
                'title' => 'bb'
            ]
        ]);
    }
    public function get_group() {
        $this->success('', '', ['group' => 1]);
    }
}
