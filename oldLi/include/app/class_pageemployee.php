<?php
//雇员-管理
abstract class pageemployee extends page
{
    //$checkPower 检测页面所有操作的权限
	public function __construct( $options = '', $checkOperatePower=false)
	{
		parent::__construct($options, $checkOperatePower);
		$this->checkUtypePage(3); //只允许会员身份： 1买家 2供应商 3雇员 4买家或卖家  5任何会员
	}
}