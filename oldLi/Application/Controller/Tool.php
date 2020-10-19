<?php
//工具 模块类。
class mod_tool extends pagefront
{
	public function __construct($options='', $checkuser = true)
	{
		parent::__construct($options, $checkuser = true);
        $this->name = 'system/404';
	}
	function doAction() {
        switch ($this->getOption('do')) {
            //测试搜素数据
            case 'search_area_for_menu':
                $area = $this->getOption('area', '', 'trim');
                if(!$area) {
                    $allCity = [
                        [
                            's_id' => 0,
                            's_name' => '请输入地区',
                        ]
                    ];
                } else {
                    $allCity = area::searchAreaByName($area, 10);
                }
                if(!$allCity) {
                    $allCity = [
                        [
                            's_id' => 0,
                            's_name' => '找不到地区',
                        ]
                    ];
                }
                return  Message::getMsgJson('0038', $allCity);
                break;
            //测试获取数据
            case 'get_area':
                $province_id= $this->getOption('area_id');
                if($province_id) {
                    $allCity = area::getAllCityByArea($province_id);
                } else {
                    $allCity = [];
                }
                return  Message::getMsgJson('0038', $allCity);
                break;
            //测试获取所有数据
            case 'get_all_area':
//                $allCity = area::getAllArea();
                $allCity = area::getAllAreaWithSons2();
                $allCity = json_encode($allCity, JSON_UNESCAPED_UNICODE); //必须PHP5.4+
                return  Message::getMsgJson('0038', $allCity);
                break;
            //测试获取所有数据
            case 'get_all_area_tree':
//                $allCity = area::getAllArea();
                $allCity = area::getAllAreaWithSons2(2);
                return  Message::getMsgJson('0038', $allCity);
                break;
            //测试获取数据
            case 'get_allprovince':
                $allCity = area::getAllProvince();
                return  Message::getMsgJson('0038', $allCity);
                break;
            //测试获取数据
            case 'get_allarea_digui':
                $allCity = area::getAllAreaDigui();
                return  Message::getMsgJson('0038', $allCity);
                break;
            //测试获取数据
            case 'get_province':
                $page = $this->getOption("page", 1, 'int');
                $fields = 's_id,s_name';
                $table_ = 's_prov_city_area_street';
                $whereSql = 'where s_parent_id=0';
                $sql = "select {$fields} from {$table_}  {$whereSql} ";
                $pag = new Divpage( $sql , '', $fields, $page , 120, $menustyle = '' );
                $pag->getDivPage();
                $returnResult = $pag->getPage();
                $pageInfo = $pag->getPageInfo();
                return  Message::getMsgJson('0038', ['list'=> $returnResult, 'info'=> $pageInfo]);
                break;
            //获取内容
            case 'get_msg':
                $fl = $this->getOption('fl');
                return  Message::getMsgJson('0043',
                    'halou nmb:'. Str::makeRadomNum(10000, 90000) . '&fl='. $fl);
                break;
            //测试获取rows
            case 'get_rows':
                return  Message::getMsgJson('0038', [
                    ['id'=>1,'title'=> '用户aaa', 'url'=> 'https://baidu1.com'],
                    ['id'=>2,'title'=> '用户bbb', 'url'=> 'https://baidu2.com'],
                    ['id'=>3,'title'=> '用户ccc', 'url'=> 'https://baidu3.com'],
                    ['id'=>4,'title'=> '用户ddd', 'url'=> 'https://baidu4.com'],
                ]);
                break;
            //测试获取rows
            case 'get_rows_list':
                return  Message::getMsgJson('0038', [
                    'list'=> [
                        ['id'=>1,'title'=> '用户aaa'],
                        ['id'=>2,'title'=> '用户bbb'],
                        ['id'=>3,'title'=> '用户ccc'],
                        ['id'=>4,'title'=> '用户ddd'],
                    ],
                    'list2'=> []
                ]);
                break;
            //测试获取今天日期
            case 'get_today':
                return  Message::getMsgJson('0038', ['today_data'=>Timer::now(), 'color'=>'#ddd']);
                break;
            //测试获取群id
            case 'get_group':
                return  Message::getMsgJson('0038', ['group'=>1]);
                break;
            //测试当前省份
            case 'get_current_province':
                return  Message::getMsgJson('0038', ['province'=>230000]);
                break;
            //测试获取多选的值
            case 'get_selects':
                return  Message::getMsgJson('0038', ['value1'=>450000, 'value2'=>450500, 'value3'=>450521]);
                break;
            //测试获取昵称
            case 'get_nick':
                return  Message::getMsgJson('0038', ['nickname'=> '某站长']);
                break;
            //测试回调
            case 'test_search':
                return  Message::getMsgJson('0038', [
                    ['id'=> '121', 'title'=> 'aaa'],
                    ['id'=> '122', 'title'=> 'bbb'],
                    ['id'=> '123', 'title'=> 'ccc'],
                ]);
                break;
            //测试回调
            case 'test':
                return  Message::getMsgJson('0043');
                break;
            //测试修改
            case 'test_edit':
                return  Message::getMsgJson('0043');
                break;
            //测试文件上传
            case 'upload_file':
//                print_r($this->options);
//                exit;
                $input_name= $this->getOption('input_name');
                $target_url = '/upload/tool_upload_test.jpg';
//                print_r($this->options);exit;
                $uploadResponse = file::uploadFile($target_url, $input_name, $this->options, 10240, false);
                if($uploadResponse[0] == 'success') {
                    $filebackurl = $uploadResponse[1];
                    return  Message::getMsgJson('0388', $filebackurl);//返回‘图片上传成功’,新url
                } else {
                    return  Message::getMsgJson('0502', $uploadResponse[1]);//返回上传失败的原因
                }
            break;
        }


    }
	
	function getData()
	{
		switch ($this->getOption('show')){
            //工具
            case 'photo':
            default:

                $router = $this->getOption('router');
                if(strstr($router, 'tool/')) {
                    $fileName = trim(explode('tool/', $router)[1]);
                } else {
                    Message::Show('路径应该是：tool/');
                    exit;
                }
                $htmlname =  'front/tool/'.$fileName.'.html';
                if(!file_exists( \Config::get('router.sysPathes.tempPath') . '/'. $htmlname)) {
                    Message::Show('文件不存在:'.$htmlname) ;
                    exit;
                }
                $arr = [];
		}
		$this->setTempData($arr);
		$this->setTempPath($htmlname);//设置模板
	}
}