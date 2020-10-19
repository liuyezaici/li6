<?php
//会员登录时 任何会员都可以访问的页面
abstract class pageanyuser extends page
{
    public function __construct( $options = '', $checkuser = true )
    {
        parent::__construct( $options,$checkuser );
        $this->checkPower(5); //只允许会员身份： 1买家 2卖家 3雇员 4买家或卖家  5任何会员
    }
}