<?php
class page
{
    public $options = NULL; //页面参数数组
    public $userClass = NULL; //当前userclass
    public $userId = 0; //当前登录身份uid
    public $userNick = 0; //当前登录身份
    public $userType = 0; //当前登录身份
    public $isPhone = false; //wap端
    protected $templatefile = NULL; //页面组装中的模板
    private $temp_data = [];//页面的数据
    private $temp_html = ''; //页面html代码
    private $checkOperatePower = false; //所有操作是否需要校验权限
    public function doAction() {}

    //得到显示页数据（子页面如果需要实现不同数据和模板，只需要index.php带参数show，同时覆盖此方法即可）
    public function getData() {}

    public function __construct($options =[], $checkOperatePower=false)
    {
        $this->options = $options;
        $GLOBALS['memcache'] = new Cache();//config中定义的缓存要赋值
        $uhash = $this->getOption('uhash'); //允许本地提交uhash
        $userClass = new Users($uhash);
        $this->userClass = $userClass; //保存当前的合法用户类
        $this->userId = $userClass->userId; //保存当前登录的用户id
        $this->userNick = $userClass->userNick;
        $this->userType = $userClass->userType;
        $this->checkOperatePower = $checkOperatePower;
        $this->isPhone = Func::checkIfPhone();
    }

    //获取参数
    public function getOption($key = '', $defauleValue = '', $format = '')
    {
        if ($format == 'trim') {
            return isset($this->options[$key]) ? trim($this->options[$key]) : $defauleValue;
        } elseif ($format == 'int') {
            return isset($this->options[$key]) ? intval($this->options[$key]) : $defauleValue;
        } elseif ($format == 'float') {
            return isset($this->options[$key]) ? floatval($this->options[$key]) : $defauleValue;
        } elseif ($format == 'array') { //数组
            return isset($this->options[$key]) ? $this->options[$key] : $defauleValue;
        } else {
            if (isset($this->options[$key])) {
                if (is_array($this->options[$key])) { //数组格式直接返回
                    return isset($this->options[$key]) ? $this->options[$key] : [];
                } else { //默认返回字符
                    return isset($this->options[$key]) ? trim($this->options[$key]) : '';
                }
            } else {
                if ($format == 'array') { //数组格式直接返回
                    return array();
                } else { //默认返回字符
                    return $defauleValue;
                }
            }
        }
    }

    //获取所有post get 参数
    public static function postGet()
    {
        //过滤所有参数
        function filterArray($array)
        {
            foreach ($array as $key => $value) {
                if (is_string($value)) {
                    $str = addslashes($value);
                    //替换反动不文明非法词语
                    $badwords = 'truncate|declare'; //不能过滤sex 支付宝里有
                    //弱非法字符 转译即可
                    $badwords2 = ''; //不能过滤sex 支付宝里有   select算合法词 在选择购物车时用到
                    $zangAy = explode('|', $badwords);
                    $zangAy2 = explode('|', $badwords2);
                    foreach ($zangAy as $v) {
                        $str = str_replace($v, '**', $str);
                    }
                    //弱非法字符，只转译处理
                    foreach ($zangAy2 as $v) {
                        $str = str_replace($v, Str::unicodeEncode($v), $str);
                    }
                    $array[$key] = $str;
                } elseif (is_array($value)) {
                    $array[$key] = filterArray($value);
                }
            }
            return $array;
        }



        $newGET = $_GET;
        $newPost = $_POST;
        $newGET = filterArray($newGET);
        $newPost = filterArray($newPost);//\\bEXEC\\b|UPDATE.+?SET|(DELETE).+?FROM|
        return array_merge($newGET, $newPost, array('Files' => $_FILES));
    }

    //index入口调用 ： 页面初始化
    public function init()
    {
        $options = $this::postGet(); //处理页面提交数据：post get file
        //以下两个获取方式不能用 getOption获取 因为options参数在最后才初始化赋值
        $modelName = isset($options['m']) ? $options['m'] : 'default'; //默认模块
        $routerStr = isset($options['router']) ? $options['router'] : ''; //获取路由参数
        /*如果空路由，就用原生类的参数接收*/
        $router_result = array();
        if (!$routerStr) {
            $model_name = "mod_" . $modelName;
        } else {
            $routerClass = new router();
            $router_result = $routerClass->run($routerStr);
            $model_name = "mod_" . $router_result['m'];
        }
        //合并参数
        $array_merge = array_merge($options, $router_result);
        //判断和运行类
        if (class_exists($model_name)) {
            $myModel = new $model_name($array_merge);
        } else {
            $myModel = new mod_page404($array_merge);
        }
        //执行输入
        $myModel->output();
    }

    //校验当前客户身份是否登录 $checkType 当前页面支持的会员身份 1.买家 2卖家 3雇员 4前台会员 5任何会员
    public function checkUtypePage($checkType = 5)
    {
        $json = $this->getOption('json');//json提交
        $from_form = $this->getOption('from_form');//来自弹窗层 需要关闭最新窗口
        $userClass = $this->userClass;
        $hasLogin = $userClass->checkLogin($checkType);
        $outType = $userClass->outType; //获取被踢的原因,身份切换导致退出时 需要备注.
        $outReason =  $outType == 1 ? '【您身份不符或身份已切换】' :  '' ;
        //未登录
        $err_code = '0000';
        if (!$hasLogin) {
            if ($json == 'true') { //json 提交
                if($outType == 1) {//身份切换导致退出登录
                    $errMsg = $outReason . message::getMessage($err_code);
                    print_r(json_encode(array('id' => $err_code,'msg' => $errMsg)));//返回‘json:您没有登录’
                    exit;
                } else {
                    print_r(message::getMsgJson($err_code));//返回‘您没有登录’
                    exit;
                }
            }
            message::Show($outReason . message::getMessage($err_code)); //返回‘html:您没有登录’
            exit;
        }
    }

    //校验当前操作是否有权限
    protected function checkOperateAllow($doOrShow='') {
        $uid = $this->userId;
        $db = mysql::getInstance();
        $my_powerids = power::getMyPower($uid);
        $my_powerids = explode(',', $my_powerids);
        if($doOrShow == 'show') {//校验模版权限
            $model = $this->getOption('m');
            $show = $this->getOption('show');
            $form = $this->getOption('form');
            if(!$model) return;
            $checkSql = "p_model='{$model}'";
            if($show) $checkSql.= " AND p_show='{$show}'";
            if($form) $checkSql.= " AND p_show_form='{$form}'";
            $powerInfo = DbBase::getRowBy('s_power', 'p_id', $checkSql);
            if($powerInfo) {
                $powrId = $powerInfo['p_id'];
                if(!in_array($powrId, $my_powerids)) {
                    message::Show(message::getMessage('0346'));
                    exit;
                }
            }
        } else {
            $model = $this->getOption('m');
            $do = $this->getOption('do');
            if(!$model || !$do) return;
            $powerInfo = DbBase::getRowBy('s_power', 'p_id', "p_model='{$model}' AND p_do='{$do}'");
            if($powerInfo) {
                $powrId = $powerInfo['p_id'];
                if(!in_array($powrId, $my_powerids)) {
                    print_r(message::getMsgJson('0346'));
                    exit;
                }
            }
        }
    }
    //设置模板文件
    public function setTempPath($Tempfile = 'temp/system/404')
    {
        //默认为读取.html模版
        //如果传递有后缀 $html_name = 'manage/employ/adsense/adsense_category_list.php';
        $_ext = pathinfo($Tempfile, PATHINFO_EXTENSION);
        $_file = ($_ext == '') ? $Tempfile . '.html' : $Tempfile;
        $this->templatefile = $_file;
    }

    //设置数据
    public function setTempData($data) {
        $newData = $data && is_array($data) ? $data : array();
        if($this->temp_data) {
            $this->temp_data = array_merge($this->temp_data, $newData);//默认载入的数据 与 传递的新数据 合并
        } else {
            $this->temp_data = $newData;
        }
    }

    //组装页面
    public function printHtml() {
        if(!$this->temp_data || !is_array($this->temp_data)) $this->temp_data = array('a'=>'');
        extract($this->temp_data);
        if(!$this->templatefile) {
            print_r("no_temp:".$this->templatefile);
            exit;
        }
        include(\Config::get('router.sysPathes.tempPath') . '/'. trim($this->templatefile, '/'));
        print ob_get_clean();
    }

    //组装页面 不输出 只返回内容
    public function readTemp($tempPath, $data = array()) {
        $tempPath = \Config::get('router.sysPathes.tempPath') . $tempPath;
        ob_start();
        //整个页面的数据都可以带入给模版，比如页头数据需要赋值给页脚
        if($this->temp_data) {
            //新数据放在后面 用于覆盖前面的数据
            $this->temp_data = array_merge($this->temp_data, $data);
        } else {
            $this->temp_data = $data;
        }
        extract($this->temp_data);
        include($tempPath);
        $ob_contents = ob_get_contents();
        ob_end_clean();
        return  $ob_contents;
    }


    //获取访问的模块id 页面的异常exit会跳过此事件
    //页面日志表
    private $modelSettingTable = 'pf_web_model_setting';
    private $modelLogTable = 'pf_web_model_visite_log';
    public function addModelVisitLog() {
        $db = mysql::getInstance();
        $model = $this->getOption('m', 'default', 'trim');
        $do = $this->getOption('do');
        $show = $this->getOption('show');
        $showForm = $this->getOption('form');
        $getModelSql = "s_model='{$model}'";
        if($do) {
            $getModelSql.= " AND s_do='{$do}'";
            $outPut = $this->outPutJson;
            $logMemo = '';
            if(!$this->userId) $logMemo .= "[未登录]";
            if($outPut) {
                $outPutJson = json_decode($outPut, true);
                $msg = isset($outPutJson['msg']) ? $outPutJson['msg'] : '';
                $msgInfo = isset($outPutJson['info']) ? $outPutJson['info'] : '';
                if($msgInfo) $msg .= '<br />'. print_r($msgInfo, true);
                $logMemo .= '<br />'. $msg;
            }
        } else {
            $uri = $_SERVER['QUERY_STRING'];
            //保护搜索页面的物品分类
            $uri = str_replace('&gtype', '#gtype', $uri);
            $uri = urldecode($uri);
            $uri = str_replace('#gtype', '&#38;gtype', $uri);
            $logMemo = $_SERVER['PHP_SELF'].'?'.($uri);
        }
        if($show) $getModelSql.= " AND s_show='{$show}'";
        if($showForm) $getModelSql.= " AND s_show_form='{$showForm}'";
        $modelInfo = DbBase::getRowBy($this->modelSettingTable, 's_id,s_typeid,s_need_count', $getModelSql);
        if($modelInfo) {
            $settingId = $modelInfo['s_id'];
            $s_typeid = $modelInfo['s_typeid'];
            $s_need_count = $modelInfo['s_need_count'];
            if($s_need_count) {//需要统计,写日志
                $uri = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '-';
                //保护搜索页面的物品分类
                $uri = str_replace('&gtype', '#gtype', $uri);
                $uri = urldecode($uri);
                $uri = str_replace('#gtype', '&#38;gtype', $uri);
                $fromUrl =$uri;
                $logData = [
                    'l_addtime'=> Timer::now(),
                    'l_uid'=> $this->userId,
                    'l_ip'=> Ip::getIp(),
                    'l_province'=> $this->myProvinceId,
                    'l_city'=> $this->myCityId,
                    'l_memo'=> $logMemo,
                    'l_client'=> Func::checkIfPhone() ? 'wap': 'pc',
                    'l_settingid'=> $settingId,
                    'l_typeid'=> $s_typeid,
                    'l_model'=> $model,
                    'l_do'=> $do,
                    'l_show'=> $show,
                    'l_show_form'=> $showForm,
                    'l_from_url'=> $fromUrl
                ];
                DbBase::insertRows($this->modelLogTable, $logData);
            }
        }
    }

    //输出do 或 show
    public function output()
    {
        if ($this->getOption('do')) {
            if($this->checkOperatePower) $this->checkOperateAllow('do');
            $doJson = $this->doAction();
            $this->temp_html = is_array($doJson) ? json_encode($doJson) : $doJson;
        } else {
            if($this->checkOperatePower) $this->checkOperateAllow('show');
            $this->getData(); //得到模板路径和数据
            $this->printHtml(); //组装模板
        }
        echo $this->temp_html;
    }
}