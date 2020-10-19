<?php

namespace app\admin\addon\news\model;

use think\Model;

class NewsConfig extends Model
{
    protected $type = [
        'config'  => 'json',
        'customtable'  => 'json',
    ];
}
