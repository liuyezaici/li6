<?php
namespace Func;

class Cache {
    public $Memcache = FALSE; //必须允许公共，在外部才可判断是否成功安装Memcache
    public $Wincache = FALSE; //
    public $error_msg = array(); //错误信息
    function __construct() {
        if(function_exists('memcache_connect')){
            try{
                $this->Memcache = new Memcache;
                if( !$this->Memcache->connect('127.0.0.1', 11211 ,10) ){
                    $this->Memcache = FALSE;
                }
            }catch(Exception $e) {
                $this->Memcache = FALSE;
            }
        }
        if(!$this->Memcache){
            @session_start();
            return $this->Memcache;
        } else {
            return '1';
        }
    }

    public function Add($key, $var, $expire=300){
        if(!$key) return false;
        if($this->Memcache){
            $this->Memcache->delete($key, 0);
            return $this->Memcache->set($key, $var, false, $expire);
        }
        $_SESSION[$key] = $var;
        return FALSE;
    }
    public function Update($key, $var, $expire=300){
        if($this->Memcache){
            $result = $this->Memcache->replace($key, $var, false, $expire);
            if($result == false)
            {
                $result = $this->Memcache->set($key, $var, false, $expire);
            }
            return $result;
        }
        $_SESSION[$key] = $var;
    }
    public function Refresh($key, $expire=300){
        return $this->Update($key, $this->Get($key), $expire);
    }
    public function Delete($key){
        if($this->Memcache){
            return $this->Memcache->delete($key,0);
        }
        $_SESSION[$key] = '';
        return true;
    }
    public function Get($key = ''){
        if($this->Memcache){
            if(!$key) return null;
            return $this->Memcache->get($key);
        }
        return isset($_SESSION[$key]) ? $_SESSION[$key] : FALSE;
    }
    /*
     * bool Memcache::flush  ( void )
     * Memcache::flush() 立即使所有已经存在的元素失效。方法 Memcache::flush()  并不会真正的释放任何扫描图，而是仅仅标记所有元素都失效了，因此已经被使用的内存会被新的元素复写。
     * 同样你也可以使用函数 memcache_flush() 完成相同功能。
     * */
    public function flush(){
        if($this->Memcache){
            return $this->Memcache->flush();
        }
    }

    //检查是否可以执行 如果可以 立即占位
    public function checkActive($tagname, $newStr='') {
        $oldqueen = $this->Get($tagname);
        if(!$oldqueen) {
            $newStr = !$newStr ? '写入数据' : $newStr;
            $this->add($tagname, $newStr, 300);//占坑，1分钟内禁止任何人在去处理这个数据
            return true;
        } else {
            return false;
        }
    }

    //检测缓存是否存在/失效 不失效则返回缓存
    public function checkSession($cacheName='', $fields='', $timeOut=3600) {
        $cacheTimeName = $cacheName.'_time';
        $expire = true;//内容默认失效
        $cacheData = $this->Get($cacheName);
        if($cacheData) {
            //缓存存在，并且时间没有过期才允许继续执行
            if($this->Get($cacheTimeName) && $this->Get($cacheTimeName) > time() - $timeOut) {
                $expire = false;
            } else {
                return false;
            }
        } else {
            return false;
        }
        $everyFieldHasSet = true;//所有字段都有值
        if(!$expire) {
            //多字段 要判断数据中是存在该字段
            if($fields && is_array($cacheData)) {//禁止传入* 因为无法判断到底有多少个字段下标
                $fieldsArray = explode(',', $fields);
                foreach ($fieldsArray as $tmpField) {
                    if(!array_key_exists($tmpField, $cacheData)) {
                        $everyFieldHasSet = false;
                        break;
                    }
                }
            } else {
                $everyFieldHasSet = true;
            }
            //所有字段都有值，才返回缓存
            if($everyFieldHasSet) return $cacheData;
        }
        return false;
    }

    //保存缓存
    public function saveSession($cacheName='', $data='') {
        $cacheTimeName = $cacheName.'_time';
        $this->Add($cacheName, $data,30*24*3600);
        $this->Add($cacheTimeName, time(),30*24*3600);
    }

    //缓存查询信息 $where_不需要带WHERE 返回多条数据。 $cacheTime默认缓存1个月
    //参考 getCacheData('pfgoods', '123,223', 'a_id', 'a_title');
    //注意：单条结果不能直接返还[0],会破坏获取方的格式要求，因为有些多数据查询是可能调取到单条结果。比如获取不定数量的商品属性时，不能强制返回结果[0]。
    //$getCache 需要获取缓存
    public static function getCacheData($tableName='', $sIds='', $whereFieldName='', $getFields='*', $cacheTime=2592000, $whereMore='', $getCache=true){
        $db = mysql::getInstance();
        if(!$sIds || !$whereFieldName) return [];
        $cacheClass = $GLOBALS['memcache'];
        if(!$cacheClass) $cacheClass = new Cache();//memcache 不建议重新创建 此行仅为维护方便而写
        $sIds = trim($sIds, ',');
        $idArray = explode(',', $sIds);
        $returnData = [];
        //获取所有字段其中自己想要的字段的值
        $getFieldsFromAllFields = function($data_=[], $fields='') {
            $newData = [];
            if(!$fields) return $newData;
            $fieldArray = explode(',', $fields);
            foreach ($fieldArray as $tmpField) {
                $newData[$tmpField] = isset($data_[$tmpField]) ? $data_[$tmpField] : '';
            }
            return $newData;
        };
        foreach ($idArray as $sId) {
            $sId = trim($sId);
            $cacheName = "{$tableName}_{$whereFieldName}_{$sId}";
            if($whereMore) {
                $whereMore = trim($whereMore);
                $cacheName.= '_'. $whereMore;
            }
            $cacheData = $cacheClass->checkSession($cacheName, $getFields, $cacheTime);
            if($cacheData && $getCache) {
                $returnData[] = $getFieldsFromAllFields($cacheData, $getFields);
            } else {
                //查询单个sku信息
                //必须获取所有字段 因为不同来路获取的字段可能不一致，就会导致重复获取，浪费扫描图，所以不如一次获取全部信息。'*'
                $where_ = is_int($sId) ? "{$whereFieldName} ={$sId}" : "{$whereFieldName} ='{$sId}'";
                if($whereMore) {
                    $whereMore = trim($whereMore);
                    $left3 = strtolower(substr($whereMore, 0, 3));
                    if($left3 != 'and') $whereMore = 'and '.$whereMore;
                    $where_ .= ' '. $whereMore;
                }
                $tmpSkuInfo = DbBase::getRowBy($tableName, '*', $where_);
                if($tmpSkuInfo) {//无数据 不缓存
                    $cacheClass->saveSession($cacheName, $tmpSkuInfo);
                    $returnData[] = $getFieldsFromAllFields($tmpSkuInfo, $getFields);
                }
            }
        }
        return $returnData;
    }

    //统计数据的点击是否重复
    public static function ifExist($sessionName = 'action', $sid=1) {
        @session_start();
        $oldSession = isset($_SESSION[$sessionName]) ? trim($_SESSION[$sessionName]) : '';
        $oldSession = trim($oldSession,",");
        $exist = false;
        if($oldSession && strlen($oldSession) > 0) {
            if(strstr(",".$oldSession.",", ",". $sid .",")) {
                $exist = true;
            }
        }
        if(!$exist) {
            $newSession = $oldSession.",". $sid;
            $newSession = trim($newSession,",");
            $_SESSION[$sessionName] = $newSession;
        }
        return $exist;
    }
}

//单线程方法
/*$cache = new Cache();
$cfg_cache = $GLOBALS['cfg']['queue_data']['2'];
$cacheName = $cfg_cache['tag'];
 if(!$cache->checkActive($cacheName)) {
    return ;
 }
//执行数据操作
......
//完成后清空保护
$cache->Delete($cacheName);
*/