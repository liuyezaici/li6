<?php

// 公共助手函数
function list_page($model, $where = '', $page = 1, $pagesize = 20, $order = 'id desc', $field = '*', $join = null, $group = null){
	$page = $page ? : 1;
	$pagesize = $pagesize ? : 20;
	
	$total = $model
			->join($join)
			->field($field)
			->where($where)
			->group($group)
			->count();
	$list = $model
			->join($join)
			->field($field)
			->where($where)
			->group($group)
			->order($order)
			->page($page, $pagesize)
			->select();
	$list = collection($list)->toArray();
	return array("total" => $total, "totalpage" => ceil($total / $pagesize), "list" => $list);       
}

function split2array($str, $split1 = ',', $split2 = ':'){
	$arr = explode($split1, $str);
	if(!$split2)return $arr;
	foreach($arr as &$v){
		$v = explode($split2, $v);
	}
	unset($v);
	return $arr;
}

/** 
 * PHP计算两个时间段是否有交集（边界重叠不算） 
 * 
 * @param string $beginTime1 开始时间1 
 * @param string $endTime1 结束时间1 
 * @param string $beginTime2 开始时间2 
 * @param string $endTime2 结束时间2 
 * @return bool 
 */  
function is_time_cross($beginTime1 = '', $endTime1 = '', $beginTime2 = '', $endTime2 = '')  
{  
    $diff1 = $beginTime2 - $beginTime1;  
    if ($diff1 > 0)  
    {  
        $diff2 = $beginTime2 - $endTime1;  
        if ($diff2 >= 0)  
        {  
            return false;  
        }  
        else  
        {  
            return true;  
        }  
    }  
    else  
    {  
        $diff2 = $endTime2 - $beginTime1;  
        if ($diff2 > 0)  
        {  
            return true;  
        }  
        else  
        {  
            return false;  
        }  
    }  
}  

//$address = '东莞南城'
function address2location($address){
    $settingModel = \fast\Addon::getModel('setting');
    if(!$settingModel) exit('未安装setting组件');
    $mapkey = $settingModel->getSetting('gaodemap_key');
    if(!$mapkey) exit('未配置系统参数：gaodemap_key');//bf51f0c3f7e961fe0a6ab833e525e429
    $url = 'http://restapi.amap.com/v3/geocode/geo?address='.urlencode($address).'&output=JSON&key='.$mapkey;
    $response = post_url($url);
	$geocode = json_decode($response, true);
//	if(!isset($geocode['geocodes'])) return $geocode['info'];
	if(!isset($geocode['geocodes'])) {//调试
        $response = '{"status":"1","info":"OK","infocode":"10000","count":"1","geocodes":[{"formatted_address":"广东省东莞市","province":"广东省","citycode":"0769","city":"东莞市","district":[],"township":[],"neighborhood":{"name":[],"type":[]},"building":{"name":[],"type":[]},"adcode":"441900","street":[],"number":[],"location":"113.751765,23.020536","level":"市"}]}';
        $geocode = json_decode($response, true);
    }
	$location = explode('|',$geocode['geocodes'][0]['location']);
	$location = explode(',', $location[0]);
	return array('lng' => $location[0], 'lat' => $location[1]);
}

//$location = '113.7434,23.01278';
function location2address($location){
    $settingModel = \fast\Addon::getModel('setting');
    if(!$settingModel) exit('未安装setting组件');
    $mapkey = $settingModel->getSetting('gaodemap_key');
    if(!$mapkey) exit('未配置系统参数：gaodemap_key');
    $url = 'http://restapi.amap.com/v3/geocode/regeo?location='.$location.'&output=JSON&key='.$mapkey;
    $response = post_url($url);
    $geocode = json_decode($response, true);
    if(!isset($geocode['regeocode'])) return $geocode['info'];
	return $geocode['regeocode']['addressComponent'];
}

if (!function_exists('create_qrcode')){
    /**
     * 生成二唯码
     * @param string    $url 数据
     * @param array     $label 标签
     * @param string    $view 直接输出
     * @return mixed
     */
	function create_qrcode($url, $label = '', $view = true){
		$qrcode = new \Endroid\QrCode\QrCode();
		$qrcode->setText($url)
                ->setSize(300)
                ->setPadding(10)
                ->setErrorCorrection('high')
                ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
                ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
                ->setLabel($label)
                ->setLabelFontSize(16)
                ->setImageType(\Endroid\QrCode\QrCode::IMAGE_TYPE_PNG);
        if($view){
			@header('Content-Type: '.$qrcode->getContentType());
			$qrcode->render();
			exit;
		}else{
			return $qrcode->writeString();
		}
	}
}

//竖向的数组转横向
if (!function_exists('array_col2row'))
{
	function array_col2row($arr){
		$result = array();
		if(!$arr)return $result;
		$keys = array_keys($arr);
		foreach($arr[$keys[0]] as $k => $v){
			foreach($keys as $kk){
				$result[$k][$kk] = $arr[$kk][$k];
			}
		}
		return $result;
	}
}

//横向的数组转竖向
if (!function_exists('array_row2col'))
{
	function array_row2col($arr, $kkey = false, $vkey = false){
		$result = array();
		if(!$arr)return $result;
		$arr = array_values($arr);
		$keys = array_keys($arr[0]); 
		foreach($arr as $k => $v){
			$result[$v[$kkey ? : $keys[0]]] = $vkey ? $v[$vkey] : $v;
		}
		return $result;
	}
}

//拼接html中的图片链接为完整链接
if (!function_exists('html_long_url'))
{
function html_long_url($html, $baseurl = ''){
	if(!$baseurl)$baseurl = site_url(false, false);
	return preg_replace("/(\=)(.*?)\/uploads\//i", "$1$2".$baseurl."/uploads/", $html);
}
}

//拼接完整域名//如果有分隔符 split 会返回数组
if (!function_exists('long_url'))
{
function long_url($url, $split = false, $baseurl = ''){
	if(!$baseurl)$baseurl = site_url(false);
	$baseurl = rtrim($baseurl, '/').'/';	
	if(is_array($url) || $split){
		$urls = $url;
		if(!is_array($url)){
			if($split == "\n")$url = str_replace("\r", "", $url);
			$urls = array_filter(explode($split, $url));
		}
		foreach($urls as &$v){
			//if(!preg_match("/^[a-zA-Z]{3,12}\:\/\//", $v)){
			if(stripos($v, 'http://') === 0 || stripos($v, 'https://') === 0){
			}else{
				$v = $baseurl.ltrim($v, '/');
			}
		}
		unset($v);
		return $urls;
	}
	if(!$url)return $url;
	if(stripos($url, 'http://') === 0 || stripos($url, 'https://') === 0) return $url;
	return $baseurl.ltrim($url, '/');	
}
}

//返回相对地址//如果有分隔符 split 会返回数组
if (!function_exists('short_url'))
{
function short_url($url, $split = false, $baseurl = ''){
	if(!$baseurl)$baseurl = site_url(false);
	$baseurl = rtrim($baseurl, '/');	
	if(is_array($url) || $split){
		$urls = $url;
		if(!is_array($url)){
			if($split == "\n")$url = str_replace("\r", "", $url);
			$urls = array_filter(explode($split, $url));
		}
		foreach($urls as &$v){
			//if(!preg_match("/^[a-zA-Z]{3,12}\:\/\//", $v)){
			if(stripos($v, 'http://') !== 0 && stripos($v, 'https://') !== 0){				
			}else{
				$v = str_ireplace($baseurl, '', $v);
			}
		}
		unset($v);
		return $urls;
	}
	if(!$url)return $url;
	if(stripos($url, 'http://') !== 0 && stripos($url, 'https://') !== 0)return $url;
	return str_ireplace($baseurl, '', $url);	
}
}

//生成随机订单号
if (!function_exists('date_rand_no'))
{
    function date_rand_no($len = 20){
        $format = 'YmdHis';
        list($usec, $sec) = explode(" ", microtime());
        $rand = \fast\Random::numeric($len);
        return substr(date($format, $sec).$rand, 2, $len);
    }
}

//把字符混合在一起
if (!function_exists('encrypt_no'))
{
function encrypt_no(){
	$args = func_get_args();
	$result = '';
	foreach($args as $v){
		$l = strlen($v);
		$ll = strlen($l);
		$result .= $ll.$l.$v;
	}
	return $result;	
}
}
	
//把混合在一起的字符拆分
if (!function_exists('decrypt_no'))
{
function decrypt_no($encrypt = ''){
	$result = array();
	if(strlen($encrypt)){
		$i = 0;
		while(true){
			$ll = substr($encrypt, $i, 1);
			$l = substr($encrypt, $i + 1, $ll);
			$v = substr($encrypt, $i + 1 + $ll, $l);
			if(strlen($v) == 0)break;
			$result[] = $v;
			$i += 1 + $ll + $l;
		}
	}
	return $result;
}
}

if (!function_exists('__'))
{

    /**
     * 获取语言变量值
     * @param string    $name 语言变量名
     * @param array     $vars 动态变量值
     * @param string    $lang 语言
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {
        if (!\think\Lang::has($name, $lang) && (is_numeric($name) || !$name))
        //if (is_numeric($name) || !$name)
            return $name;
        if (!is_array($vars))
        {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return \think\Lang::get($name, $vars, $lang);
    }

}

//当前站点域名路径
if (!function_exists('site_url')){
	function site_url($path = true, $qs = true){
		$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
		$domain = $http_type.$_SERVER['HTTP_HOST'].($_SERVER["SERVER_PORT"] == 80 || $_SERVER["SERVER_PORT"] == 443 ? '' : ':'.$_SERVER["SERVER_PORT"]);
		if(!$path)return $domain;
		if(!$qs)return $domain.($_SERVER['REDIRECT_URL'] ? : $_SERVER['PHP_SELF']);
		return $domain.$_SERVER['REQUEST_URI'];
	}
}
//当前站点域名
if (!function_exists('domain')){
	function domain(){
        return site_url(false, false);
	}
}
//本地图片转html支持多张
if (!function_exists('localPicToImgHtml'))
{
    function localPicToImgHtml($url, $newSplit = ' '){
        $urlArray = long_url($url, ',');
        foreach ($urlArray as &$v) {
            $v = "<img src='{$v}' />";
        }
		unset($v);
        return implode($newSplit, $urlArray);
    }
}

/*
$url 请求地址
$post POST参数
$files 上传文件数组,相对路径
*/
if (!function_exists('post_url'))
{
	//\fast\Http::post
	function post_url($url, $post = '', $files = '', $host = '', $referrer = '', $cookie = '', $proxy = '', $useragent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)'){
		if(empty($post) && empty($files) && empty($host) && empty($referrer) && empty($cookie) && empty($proxy) && empty($useragent))return @file_get_contents($url);
		$method = 'POST';//empty($post) && empty($files) ? 'GET' : 'POST';
		if($post && is_array($post)){
			if(count($post) != count($post, 1))$post = http_build_query($post);
		}
	
		$ch = @curl_init();
		@curl_setopt($ch, CURLOPT_URL, $url);
		if($proxy)@curl_setopt($ch, CURLOPT_PROXY, 'http://'.$proxy);
		if($referrer)@curl_setopt($ch, CURLOPT_REFERER, $referrer);
		@curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		if($cookie){
			@curl_setopt($ch, CURLOPT_COOKIE, $cookie); 
			//@curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE_FILE_PATH);
			//@curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE_FILE_PATH);
		}
		@curl_setopt($ch, CURLOPT_HEADER, 0);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		@curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		@curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		@curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

		if ($method == 'POST') {
			@curl_setopt($ch, CURLOPT_POST, 1);			
			//处理文件上传
			if($files){
				if(!$post)$post = array();
				foreach($files as $k => $v){
					if (class_exists('CURLFile')) {
						$post[$k] = new CURLFile(realpath($v));
					} else {
						$post[$k] = '@'.realpath($v);
					}
				}
			}
			@curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}

		$result = @curl_exec($ch);
		@curl_close($ch);
		return $result;
	}
}

//循环查找文件路径
if (!function_exists('file_path'))
{
	function file_path($path, $name, $filename, $layer){
		if(!is_array($name))$name = explode('.', str_replace(array('/', '\\'), '.', $name));
		$name = array_filter($name);
		for($i = 0; $i < count($name); $i ++){
			$temp = $name;
			array_splice($temp, $i, 0, $layer);	
			$file = $path . '/'. implode('/', $temp) . $filename;
			if(is_file($file))return str_replace(array('\\', '\\\\', '//'), '/', $file);
		}
		return '';
	}
}

//目录转url
if (!function_exists('file_url'))
{
	function file_url($file){
		return str_replace('\\', '/', substr($file, strlen(APP_PATH)));
	}
}

if (!function_exists('format_bytes'))
{

    /**
     * 将字节转换为可读文本
     * @param int $size 大小
     * @param string $delimiter 分隔符
     * @return string
     */
    function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 6; $i++)
            $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }

}

if (!function_exists('datetime'))
{

    /**
     * 将时间戳转换为日期时间
     * @param int $time 时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }

}

if (!function_exists('human_date'))
{

    /**
     * 获取语义化时间
     * @param int $time 时间
     * @param int $local 本地时间
     * @return string
     */
    function human_date($time, $local = null)
    {
        return \fast\Date::human($time, $local);
    }

}

if (!function_exists('cdnurl'))
{

    /**
     * 获取上传资源的CDN的地址
     * @param string $url 资源相对地址
     * @return string
     */
    function cdnurl($url)
    {
        return preg_match("/^https?:\/\/(.*)/i", $url) ? $url : \think\Config::get('upload.cdnurl') . $url;
    }

}


if (!function_exists('is_really_writable'))
{

    /**
     * 判断文件或文件夹是否可写
     * @param	string $file 文件或目录
     * @return	bool
     */
    function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/')
        {
            return is_writable($file);
        }
        if (is_dir($file))
        {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === FALSE)
            {
                return FALSE;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return TRUE;
        }
        elseif (!is_file($file) OR ( $fp = @fopen($file, 'ab')) === FALSE)
        {
            return FALSE;
        }
        fclose($fp);
        return TRUE;
    }

}

if (!function_exists('rmdirs'))
{

    /**
     * 删除文件夹
     * @param string $dirname 目录
     * @param bool $withself 是否删除自身
	 * @param bool $onlydir 只删除空目录
     * @return boolean
     */
    function rmdirs($dirname, $withself = true, $onlydir = false)
    {
        if (!is_dir($dirname))
            return false;
        $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo)
        {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
			if($onlydir && $todo == 'unlink')continue;
            $todo(rtrim($fileinfo->getRealPath(), '/\\'));
        }
		
        if ($withself)
        {	
            @rmdir(rtrim($dirname, '/\\'));
        }
        return true;
    }

}

if (!function_exists('copydirs'))
{

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest 目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest))
        {
            mkdir($dest, 0755);
        }
        foreach (
        $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item
        )
        {
            if ($item->isDir())
            {
                $sontDir = $dest . DS . $iterator->getSubPathName();
                if (!is_dir($sontDir))
                {
                    mkdir($sontDir);
                }
            }
            else
            {
                copy($item, $dest . DS . $iterator->getSubPathName());
            }
        }
    }

}

if (!function_exists('mb_ucfirst'))
{

    function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }

}

if (!function_exists('addtion'))
{

    /**
     * 附加关联字段数据
     * @param array $items 数据列表
     * @param mixed $fields 渲染的来源字段
     * @return array
     */
    function addtion($items, $fields)
    {
        if (!$items || !$fields)
            return $items;
        $fieldsArr = [];
        if (!is_array($fields))
        {
            $arr = explode(',', $fields);
            foreach ($arr as $k => $v)
            {
                $fieldsArr[$v] = ['field' => $v];
            }
        }
        else
        {
            foreach ($fields as $k => $v)
            {
                if (is_array($v))
                {
                    $v['field'] = isset($v['field']) ? $v['field'] : $k;
                }
                else
                {
                    $v = ['field' => $v];
                }
                $fieldsArr[$v['field']] = $v;
            }
        }
        foreach ($fieldsArr as $k => &$v)
        {
            $v = is_array($v) ? $v : ['field' => $v];
            $v['display'] = isset($v['display']) ? $v['display'] : str_replace(['_ids', '_id'], ['_names', '_name'], $v['field']);
            $v['primary'] = isset($v['primary']) ? $v['primary'] : '';
            $v['column'] = isset($v['column']) ? $v['column'] : 'name';
            $v['model'] = isset($v['model']) ? $v['model'] : '';
            $v['table'] = isset($v['table']) ? $v['table'] : '';
            $v['name'] = isset($v['name']) ? $v['name'] : str_replace(['_ids', '_id'], '', $v['field']);
        }
        unset($v);
        $ids = [];
        $fields = array_keys($fieldsArr);
        foreach ($items as $k => $v)
        {
            foreach ($fields as $m => $n)
            {
                if (isset($v[$n]))
                {
                    $ids[$n] = array_merge(isset($ids[$n]) && is_array($ids[$n]) ? $ids[$n] : [], explode(',', $v[$n]));
                }
            }
        }
        $result = [];
        foreach ($fieldsArr as $k => $v)
        {
            if ($v['model'])
            {
                $model = new $v['model'];
            }
            else
            {
                $model = $v['name'] ? \think\Db::name($v['name']) : \think\Db::table($v['table']);
            }
            $primary = $v['primary'] ? $v['primary'] : $model->getPk();
            $result[$v['field']] = $model->where($primary, 'in', $ids[$v['field']])->column("{$primary},{$v['column']}");
        }

        foreach ($items as $k => &$v)
        {
            foreach ($fields as $m => $n)
            {
                if (isset($v[$n]))
                {
                    $curr = array_flip(explode(',', $v[$n]));

                    $v[$fieldsArr[$n]['display']] = implode(',', array_intersect_key($result[$n], $curr));
                }
            }
        }
        return $items;
    }

}

if (!function_exists('var_export_short'))
{

    /**
     * 返回打印数组结构
     * @param string $var   数组
     * @param string $indent 缩进字符
     * @return string
     */
    function var_export_short($var, $indent = "")
    {
        switch (gettype($var))
        {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value)
                {
                    $r[] = "$indent    "
                            . ($indexed ? "" : var_export_short($key) . " => ")
                            . var_export_short($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }

}

if (!function_exists('json_output'))
{
    /** json统一输出数据
     * json_output
     */
    function json_output($total=0, $list = [], $page_size=0, $page=0)
    {
        $backData = array("code" =>1 ,"total" => $total, "rows" => $list);
        if($page_size) $backData['page_size'] = $page_size;
        if($page) $backData['page'] = $page;
        return json($backData);
    }

}