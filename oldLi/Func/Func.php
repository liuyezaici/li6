<?php
namespace Func;

//系统通用方法
class Func {
    //ids分页
    public  static function getPage($ids='', $onePage = 10, $currentPage = 1, $sort_=2) {
        $idsArray = explode(",", $ids);
        sort($idsArray);
        if($sort_ == 2) { //降序
            $idsArray = array_reverse($idsArray);
        }
        $total = count($idsArray);
        $fromId = ($currentPage-1) * $onePage;
        $endId = ($currentPage) * $onePage - 1;
        if($endId > $total-1) $endId = $total-1;
        $index = 0;
        for($i = $fromId; $i < ($endId+1); $i ++) {
            $newId[$index] = $idsArray[$i];
            $index ++;
        }
        $newIDs = join(",", $newId);
        $newIDs = trim($newIDs, ",");
        return $newIDs;
    }


// ------------ 文件处理函数 -------------------------------------------------------------- //
    // php 获取
    public static function get_nr($url,$ref = '' ,$coo=''){
        $header = array("Referer: ".$ref."","Cookie: ".$coo);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        //----
        curl_setopt($ch, CURLOPT_HEADER, 1);//get cookies
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        //$contents = curl_exec($ch);
        ob_start();
        curl_exec($ch);
        $contents = ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        return $contents;
    }
    // php https 获取
    public static function get_https($url, $ref='',$coo=''){
        $header = array("Referer: ".$ref."","Cookie: ".$coo);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_HEADER, 1);//get cookies
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//
        //----
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        //$contents = curl_exec($ch);
        ob_start();
        curl_exec($ch);
        $contents = ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        return $contents;
    }
    //带来路的post
    public static function post_nr_from($url, $ref, $post_data = array()){
        $header = array("Referer: ".$ref."","Cookie: ");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        //指定post数据
        curl_setopt($ch, CURLOPT_POST, true);
        //添加变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    //php获取curl跳转的新地址，不需要新内容
    public static function curl_post_302($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 获取转向后的内容
        $data = curl_exec($ch);
        $Headers = curl_getinfo($ch);
        curl_close($ch);
        if ($data != $Headers){
            return $Headers["url"];
        }else{
            return false;
        }
    }
    // php post
    public static function post_nr($url, $post_data = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //指定post数据
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt( $ch , CURLOPT_POSTFIELDS , $post_data );
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    //有些地方post的数据要转url
    public static function post_nr_str($url, $ref, $post_data = array()){
        if (is_array ( $post_data ) && 0 < count ( $post_data )) {
            $postBodyString = "";
            foreach ( $post_data as $k => $v ) {
                if(is_string($v)) {
                    $v = urlencode ($v);
                    $postBodyString .= "$k=" . $v . "&";
                }
            }
        }

        $header = array("Referer: ".$ref."","Cookie: ");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        //指定post数据
        curl_setopt($ch, CURLOPT_POST, true);
        //添加变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    // curl post 微信专用
    public static function curl_post( $uri , $data )
    {
        $ch = curl_init();
        curl_setopt( $ch , CURLOPT_TIMEOUT , 5 );
        curl_setopt( $ch , CURLOPT_URL , $uri );
        curl_setopt( $ch , CURLOPT_RETURNTRANSFER , TRUE );
        curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER , 0 );
        curl_setopt( $ch , CURLOPT_SSL_VERIFYHOST , 0 );
        //指定post数据
        curl_setopt( $ch , CURLOPT_POST , TRUE );
        //添加变量
        curl_setopt( $ch , CURLOPT_POSTFIELDS , $data );
        $output = curl_exec( $ch );
        curl_close( $ch );
        return $output;
    }

    //curl远程执行函数
	public static function curl($url, $post_data = null)
	{
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//https 请求
		if(strlen($url) > 5 && strtolower(substr($url,0,5)) == 'https' )
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
        if (is_array ( $post_data ) && 0 < count ( $post_data )) {
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $post_data);
        }
        $reponse = curl_exec($ch);
		curl_close($ch);
		return $reponse;
	}

    //转gb2312转utf-8编码
    public  static  function gbktoutf8($str) {
        return iconv("gbk", "UTF-8//IGNORE", $str);
    }
    //模仿JS的escape
    public  static  function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') {
        $return = '';
        if (function_exists('mb_get_info')) {
            for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) {
                $str = mb_substr ( $string, $x, 1, $in_encoding );
                if (strlen ( $str ) > 1) { // 多字节字符
                    $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) );
                } else {
                    $return .= '%' . strtoupper ( bin2hex ( $str ) );
                }
            }
        }
        return $return;
    }


    //获取性别
    public static function sexName($sexid) {
        if($sexid == 0) {
            $u_sex = '女';
        } else if($sexid == 1) {
            $u_sex = '男';
        } else if($sexid == 2) {
            $u_sex = '保密';
        } else  {
            $u_sex = '保密';
        }
        return $u_sex;
    }
    //获取用户等级
    public static function get_user_level($exp = 0) {
        $level = sqrt(($exp+4)) - 2; //等级节点4倍递增 64,16,4
        return intval($level);
    }

    //获取QQ头像内容,校验绑定QQ是否一样
    public static function getQQFaceMd5($qq) {
        $QQurl = "http://q.qlogo.cn/headimg_dl?bs=qq&dst_uin=".$qq."&src_uin=".$qq."&fid=blog&spec=100";
        $QQImageContent = Func::get_nr($QQurl);
        return Str::getMD5($QQImageContent);
    }
    //判断访客是手机还是PC
    public static function checkIfPhone() {
        $isPhone = false;
        $user_agent = $_SERVER['HTTP_USER_AGENT'];//返回手机系统、型号信息
        $userUrl = $_SERVER['SERVER_NAME'];
        if(stristr($user_agent,'iPad')) {//返回值中是否有 iPad 这个关键字
            $isPhone = true;
        } else if(stristr($user_agent,'Android')) {//返回值中是否有Android这个关键字
            $isPhone = true;
        } else if(stristr($user_agent,'iPhone')) {
            $isPhone = true;
        } else if(stristr($user_agent,'Windows Phone')) { //你使用的是Windows Phone系统;
            $isPhone = true;
        } else if(stristr($userUrl,'wap')) { //你使用的是wap域名;
            $isPhone = true;
        }
        return $isPhone;
    }
    //判断是否微信浏览器
    public static function isWeixin(){
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }
    //获取当前网址
    public static function getWebUrl() {
        return 'http://'. $_SERVER['SERVER_NAME']. $_SERVER["REQUEST_URI"];
    }
    //获取万象优图尺寸后缀
    public static function getWanxiangStr($width_=100, $height=0) {
        $size_str  = '?imageView2/4/w/'. $width_ .'/';
        if($height) $size_str .= 'h/'.$height;
        return $size_str;
    }
    //获取万象优图尺寸后缀
    public static function getLogo() {
        return '!w';
    }
    //万象优图URL加尺寸
    public static function urlAddSize($url, $width_=100, $height=0) {
        if(strtolower(substr($url, 0, 14)) == 'http://images.') {
            return $url.Func::getWanxiangStr($width_, $height);
        } else {
            if(strpos($url,'_min.jpg')){
                return $url;
            }else{
                return str_replace('.jpg', '_min.jpg', $url);
            }
        }
    }


    //创建上传安全码
    public static function makeUploadHash($time='') {
        if(!$time) return '';
        return Str::getMD5($time.'[this_is_sasasui]', 10);
    }
    //阿里云oss 文件url中文替换为真实下载地址
    public static function ossUrlEncode($url = '') {
        $url = trim($url, '/');
        if(!strstr($url, $GLOBALS['cfg_aliyun_oss_weburl'])) {
            $downUrl = $GLOBALS['cfg_aliyun_oss_weburl']. '/'. urlencode($url);
        } else {
            $downUrl = $url;
        }
        return str_replace('%2F', '/', $downUrl);
    }
    //上传文件目录加安全校验
    public static function makeSafeUploadCode($pathUrl, $uid_) {
        return Str::getMD5($pathUrl."[save_hash_lr]".$uid_);
    }


    /**
     * @param array $request
     * @return array [status, tableArray, fieldArray]
     */
    public static function uri_getResult($request=[], $scope, $allowTable=[]) {

        //过滤where语句
        /**
         * @param string $whereStr
         * @param $scope 外部的$this
         * @return mixed|string
         */
        function uri_replaceWhere($whereStr='', $scope) {
            if(!$whereStr) return $whereStr;
            //替换{uid}
            $whereStr = str_replace('{uid}', $scope->userId, $whereStr);
            return $whereStr;
        }

        //提取表格
        function uri_getJoinTables($request=[], $scope, $allowTable) {
            //检查是否
            function isAllowTable($table='', $allowTables) {
                return in_array($table, $allowTables);
            }
            //检查是否禁止查询某表的某字段
            function ifForbidField($table='', $getFields='', $allFieldData) {
                if(!isset($allFieldData[$table])) return false; //表不在限制范围内
                $getFieldAy = explode(',', $getFields);
                $forbidFieldAy = explode(',', $allFieldData[$table]);
                foreach ($getFieldAy as $n=>$field_) {
                    if(in_array($field_, $forbidFieldAy)) return $field_;
                }
                return false;
            }


            $getTable = [];
            $tableJoin = "";
            //        SELECT `a`.*,`m`.* FROM `jsq_terminal` `a`
            //left JOIN `jsq_terminal_record` `b` ON `b`.`terminal_id`=`a`.`id`
            //left JOIN `jsq_admin` `m` ON `a`.`aid`=`m`.`id` LIMIT 1

            foreach ($request as $k=>$tableSql) {
                if(substr($k, 0, 6) == 'table|') {
                    $table_ = trim(substr($k, 6));
                    if(!isAllowTable($table_, $allowTable)) return ['table:'. $table_.' is not allow', [], []];
                    $getTable[] = $table_;
                    if(preg_match("/^[a-z\d]*$/i", $tableSql) && !$tableJoin) //首次定义主表名字
                    {
                        $tableJoin = "{$table_} AS {$tableSql}";
                    } else { //table_user_log= b|a.teml_id = b.id|l/r/i/f
                        if(strstr($tableSql, '|')) {
                            $array_ = explode('|', $tableSql);
                            $tableAsName = $array_[0] ;
                            $tableONSql = isset($array_[1]) ? $array_[1]: '';
                            $tableJoinTypeZ = isset($array_[2]) ? $array_[2] : 'l'; //联表类型 l/r/i/f 即是：left inner right full
                            $tableJoinType = ' LEFT JOIN';
                            if($tableJoinTypeZ == 'l') {
                                $tableJoinType = ' LEFT JOIN';
                            } elseif($tableJoinTypeZ == 'r') {
                                $tableJoinType = ' RIGHT JOIN';
                            } elseif($tableJoinTypeZ == 'i') {
                                $tableJoinType = ' INNER JOIN';
                            } elseif($tableJoinTypeZ == 'f') {
                                $tableJoinType = ' FULL JOIN';
                            }
                            if(preg_match("/^[a-z\d]*$/i", $tableAsName)) //加入联表
                            {
                                $tableONSql = uri_replaceWhere($tableONSql, $scope);
                                $tableJoin .= "{$tableJoinType} {$table_} AS {$tableAsName} ON {$tableONSql}";
                            }

                        }
                    }

                }
            }
            return [true
                , 'table'=> $getTable
                , 'table_join'=>$tableJoin
            ];
        }

        //获取页面参数
        $where_ = $scope->getOption("where"); //user.is_show = 1 and user_id={uid}
        $fields = $scope->getOption("fields"); // a.id,a.username,b.aa,b.asss
        $page = $scope->getOption("page", 1, 'int');
        $size = $scope->getOption("size", 10, 'int');
        $order = $scope->getOption("order", '', 'trim');
//        echo $where_;exit;
        $where_ = uri_replaceWhere($where_, $scope);
        //提取表格结构和join方法
        $tablesFilter = uri_getJoinTables($request, $scope, $allowTable); //获取并且保护表 [true, ['table'=>tableAy, 'table_join'=>tableJoinSql]]
//        print_r($tablesFilter);   exit;
        $whereResult = self::getUriWhereByfields($where_);
//        print_r($whereResult); exit;
        $newWhereaSql = isset($whereResult['sql']) ? $whereResult['sql'] : '';
        $newWhereaData = isset($whereResult['data']) ? $whereResult['data'] : [];
        //定义默认的返回信息
        $returnCode = 1;
        $returnMsg = '获取成功';
        $returnResult = [];
        $articlePages = [];
        if(!isset($tablesFilter[0])) {
            $returnCode = 0;
            $returnMsg = 'uri_getJoinTables 没有返回状态';
        } elseif($tablesFilter[0] !== true ) {
            $returnCode = 0;
            $returnMsg = $tablesFilter[0];
        } elseif($tablesFilter[0] !== true ) {
            $returnCode = 0;
            $returnMsg = $tablesFilter[0];
        } else {
            $tableAy = $tablesFilter['table'];
            $tableJoin = $tablesFilter['table_join'];
            if(!isset($tableAy[0])) {
                $returnCode = 0;
                $returnMsg = '没有获取到表:'. json_encode($tableAy);
            }
            $whereSql = "";
            if($newWhereaSql) $whereSql = "where {$newWhereaSql}";
            $orderSql = "";
            if($order) $orderSql = "ORDER BY {$order}";
            $sql = "select {$fields} from {$tableJoin}  {$whereSql} {$orderSql}";
//            echo $sql;exit;
            $pag = new Divpage( $sql , '', $fields = '*',$page , $size, $menustyle = 'index' );
            $pag->getDivPage();
            $returnResult = $pag->getPage();
            $articlePages = $pag->getPageInfo();
        }
        return [
            $returnCode,
            $returnMsg,
            $returnResult,
            $articlePages,
            $newWhereaData
        ];
    }
    public static function getUriWhereByfields($where_='') {
        if(!$where_) return [];
        $whereData = [];
        //过滤where条件
        $newWhereaSql = [];
        $where_ = urldecode($where_);
        $whereArray1 = explode('&', $where_);
//        print_r($whereArray1);exit;
        foreach ($whereArray1 as $n=> $valstr) {
            $whereArray2 = explode('=', $valstr);
//          print_r($whereArray2);exit;
            $field_ = $whereArray2[0];
            $val = $whereArray2[1];
//            print_r($field_ .':'. $val);
            if($val ==='') continue; //不查询 ''
//          print_r($val);exit;
            $condi_ = '';
            if(strstr($field_, '|')) {//"abc|like/gt/egt/lt/elt/in"
                $array = explode('|', $field_);
                $field_ = $array[0];
                $condi_ = $array[1];
            }
            if($condi_) {
                if($condi_=='like') {
                    $newWhereaSql[] = "{$field_} LIKE '%{$val}%'";
                } else {
                    if(!is_numeric($val)) $val = "'{$val}'";
                    $newWhereaSql[] = "{$field_} {$condi_} {$val}";
                }
            } else {
                if(!is_numeric($val)) $val = "'{$val}'";
                $newWhereaSql[] = "{$field_} = {$val}";
            }
            $whereData[$field_] = $val;
        }
//        print_r($whereData);exit;
        $newWhereaSql = join(' AND ', $newWhereaSql);
        return ['sql'=> $newWhereaSql, 'data'=> $whereData];
    }
}
