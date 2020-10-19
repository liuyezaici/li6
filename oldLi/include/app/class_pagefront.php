<?php
//前台
abstract class pagefront extends page
{
	public function __construct( $options = '')
	{
		parent::__construct();
        //前端默认载入头部和页脚数据，不用每次打开都要编写一样的代码。
        $tmpData['footer'] = $this -> readTemp('/front/footer.php');
        $this->setTempData($tmpData);
	}

}