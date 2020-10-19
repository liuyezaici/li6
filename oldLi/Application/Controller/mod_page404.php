<?php
//页面不存在 404错误页面。
class mod_page404 extends page
{
	public function __construct($options='')
	{
		parent::__construct($options);
		$this->name = "syspage";
	}
	
	//处理相关功能
	function doAction()
	{
	}
		
	function getData()
	{
        $htmlname = "system/404";
        $this->setTempData('');
        $this->setTempPath($htmlname);//设置模板
	}
		
}