<?php
//会员登录时的模块页面 [验证身份页面]
abstract class pageuser extends page
{
    public function __construct( $options = '')
    {
        parent::__construct();
        $this->checkUtypePage(1); //只允许会员身份： 1买家 2卖家 3雇员 4买家或卖家  5任何会员
    }

}