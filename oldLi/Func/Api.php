<?php

namespace Func;

class Api
{
    protected $ctrl = '';
    protected $func = '';
    protected $temp_data = [];
    protected $userId = 0;
    protected $userClass = null;
    protected $userNick = '';
    protected $userType = '';
    protected $checkOperatePower = '';
    public $options = NULL; //页面参数数组
    protected $memcache = NULL; //页面参数数组
    public function __construct($options =[], $checkOperatePower=false)
    {
        //路由
        $s_ = isset($_GET['s']) ? $_GET['s'] : '';
        if($s_) {
            $s_ = trim($s_, '/');
            $s_ = explode('/', $s_);
            //注意 app应用名字不能带大写 因为是区分大小写的 所以要强制改小写
            $ctroller = $s_[0];
            $func = isset($s_[1]) ? $s_[1] : \Config::get('router.default.method');;
            if(!$func) {
                return ('未提交method');
            }
        } else {
            $ctroller = \Config::get('router.default.ctrl');
            $func = \Config::get('router.default.method');
        }
        $this->ctrl = $ctroller;
        $this->func = $func;
        $this->memcache = new Cache();//config中定义的缓存要赋值
        $this->options = array_merge($options, $this::postGet()); //处理页面提交数据：post get file;
        $uhash = $this->getOption('uhash'); //允许本地提交uhash
        $userClass = new Users($uhash);
        print_r('$userClass');
        print_r($userClass);
        exit;
        $this->userClass = $userClass; //保存当前的合法用户类
        $this->userId = $userClass->userId; //保存当前登录的用户id
        $this->userNick = $userClass->userNick;
        $this->userType = $userClass->userType;
        $this->checkOperatePower = $checkOperatePower;
    }

    //成功
    //$msg可能是0
   public static function success($msg='', $data=[]) {
       return json_encode([
           'state' => 0,
           'msg' => $msg,
           'data' => $data,
       ]);
   }
    //成功 \Rsa加密
   public static function successRsa($msg='success') {
        if(!$msg) $msg = 'success';
       if(is_array($msg))  $msg = json_encode($msg, true);
       return self::success(\Rsa::privateKeyEncrypt($msg));
   }
    //失败
   public static function error($msg='失败', $data=[]) {
       return json_encode([
           'state' => 1,
           'msg' => $msg,
           'data' => $data,
       ]);
   }
    //失败
   public static function errorDiy($code, $msg='失败') {
       return json_encode([
           'state' => $code,
           'msg' => $msg,
           'data' => [],
       ]);
   }

    //获取参数
    public function getOption($key = '', $defauleValue = '', $format = '')
    {
//        print_r($this->options);exit;
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
        include(RootPath .APP . '/'. trim($this->templatefile, '/'));
        return ob_get_clean();
    }

    //组装页面 不输出 只返回内容
    public function readTemp($tempName = '', $data = array()) {
        if(!$tempName) $tempName = $this->func . '.php';
        $appPath = \Config::get('router.sysPathes.appPath');
        $tempPath = \Config::get('router.sysPathes.tempPath');
        if(!$appPath) die('未配置 router.sysPathes.appPath');
        if(!$tempPath) die('未配置 router.sysPathes.tempPath');
        $readTempPath = RootPath . '/'. $appPath . '/'. $tempPath .'/'.  $this->ctrl .'/'. $tempName;
        ob_start();
        //整个页面的数据都可以带入给模版，比如页头数据需要赋值给页脚
        if($this->temp_data) {
            //新数据放在后面 用于覆盖前面的数据
            $this->temp_data = array_merge($this->temp_data, $data);
        } else {
            $this->temp_data = $data;
        }
        extract($this->temp_data);
        include($readTempPath);
        $ob_contents = ob_get_contents();
        ob_end_clean();
        return  $ob_contents;
    }
}
