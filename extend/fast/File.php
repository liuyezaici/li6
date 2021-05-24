<?php

namespace fast;

/**
 * 日期时间处理类
 */
class File
{

    // php 获取
    public static function get_nr($url,$ref = '' ,$coo='', $getAll=true){
        $header = array("Referer: ".$ref."","Cookie: ".$coo);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        //----
        curl_setopt($ch, CURLOPT_HEADER, intval($getAll));//get cookies
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
    public static  function send_post($url, $post_data) {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
    public static function get_https_img($url, $ref='',$coo=''){
        $header = array("Referer: ".$ref."","Cookie: ".$coo);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_HEADER, 0);//get cookies
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
    // php https 获取
    public static function get_https($url, $ref='',$coo='', $getCookies=1){
        $header = array("Referer: ".$ref."","Cookie: ".$coo);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_HEADER, $getCookies);//get cookies
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

    //获取文件路径
    public function getFilePath($savePath='', $fileName='') {
        return config::get('base_url') . 'public/' . 'uploads/' . $savePath . $fileName;
    }
    //创建多级文件夹
    public static function creatdir($path){
        if(!is_dir($path)) {
            if(self::creatdir(dirname($path))){
                mkdir($path,0755);
                return true;
            }
        }else{
            return true;
        }
    }

    //全站上传本地文件方法
    // $target_url 上传到的目标url
    // $inputName 本地文件浏览器
    //$maxSize (单位:KB)
    public static function uploadFile($target_url, $inputName = 'picurl', $options, $maxSize=1024, $needRemote=true) {
        if(strstr($target_url, ".com/")) {
            $target_url = explode(".com/", $target_url);
            $target_url = "/".$target_url[1];
        }
        $fileName = basename($target_url);
        $savePath = dirname($target_url);
        if(strstr($fileName,"?")) {
            $fileArray = explode("?", $fileName);
            $fileName = $fileArray[0];
        }
        file::creatdir(trim($savePath, "/"));
        //flash插件决定 $_FILES['Filedata']的文件名 Filedata
        $fileData = array();
        if(isset($options['file'])) {
            $fileData = $options['file'];
        } elseif(isset($options['Files'])) {
            $fileData = $options['Files'];
        } elseif(isset($_FILES['Filedata'])) {
            $fileData = $_FILES['Filedata'];
        } elseif (isset($options['fileList'])) {
            $fileData = $options['fileList'];
        }
        $fileFormat = Array('jpeg','application/octet-stream','png','x-png', 'image/png', 'gif','image/gif', 'jpg','jpeg','pjpeg','image/jpeg', 'image/pjpeg');//pjpeg,x-png ie6,ie7,ie8  application/octet-stream是flash上传的获取格式
        $up = new uploadfile($fileData, $savePath, $fileFormat, $maxSize, $overwrite = 1,$autocreatedir = 1);
        $up->setSavename(1,$fileName); //1 指定为右边名字
        if( !$up->run($inputName) ){
            return  array('0068',$up->errmsg());//返回‘上传失败’
        }
        $imginfo = $up->getInfo();
        if(isset($imginfo[0]['saveName']) ) {
            $fileInfo = $imginfo[0];
            if(!ip::isLocal() && $needRemote) {
                $filebackurl_no_root = $savePath."/".$imginfo[0]['saveName'];//同步到新的图片服务器 -- 【本地站点除外】
                $file_url_server = $filebackurl_no_root;
                $webUploadData = self::uploadToHttpOss(root . $filebackurl_no_root, trim($file_url_server, "/"));
                if(!in_array($webUploadData['id'], array('0364'))) {
                    return  array('0502', $webUploadData['msg'], $fileInfo);//返回‘图片上传失败’
                }
                $filebackurl = $webUploadData['msg'];
            } else { //本地要另外生成小图
                $filebackurl = $target_url;
            }
            return  array('success', $filebackurl, $fileInfo);//返回‘图片上传成功’,url本地 新url远程
        }  else {
            return  array('0502', '上传失败');
        }
    }

    //服务器文件转移方法
    // $target_url 上传到的目标url
    // $inputName 本地文件浏览器
    public static function moveFile($localUrl, $target_url) {
        if(strstr($target_url, ".com/")) {
            $target_url = explode(".com/", $target_url);
            $target_url = "/".$target_url[1];
        }
        $webUploadData = self::uploadToHttpOss($localUrl, trim($target_url, "/"));
        if(!in_array($webUploadData['id'], array('0364','0388'))) {
            return  array('0502', $webUploadData['msg']);//返回‘图片上传成功’,新url
        }
        $filebackurl = $webUploadData['msg'];
        return array('success', $localUrl, $filebackurl);
    }
    //删除 远程图片
    public static function delHttpFile($file_url_server) {
        //引用jdk
        require_once root.'include/lib/oss/samples/Common.php';
        $bucket = Common::getBucketName();
        $ossClient = Common::getOssClient();
        $ossClient->deleteObject($bucket, $file_url_server);
    }
    //上传本地文件到阿里云oss
    public static function uploadToHttpOss($file_url_local,$file_url_server,$isDeleteLocalFile=false){
        //引用jdk
        require_once root.'include/lib/oss/samples/Common.php';
        //删除旧文件 否则无法覆盖
        self::delHttpFile($file_url_server);
        //上传
        $bucketName = Common::getBucketName();
        $ossClient = Common::getOssClient();
        $response = $ossClient->uploadFile($bucketName, $file_url_server, $file_url_local);
        //返回信息
        if(isset($response['info']['url'])){
            //删除本地文件
            if($isDeleteLocalFile) {
                @unlink($file_url_local);
            }
            $newUrl = $response['info']['url'];
            $newUrlRight = explode('.aliyuncs.com', $newUrl)[1];
            $newUrl = func::ossUrlEncode($newUrlRight);
            return array('id' => '0364','msg' => $newUrl);
        }
        else{
            return  array('id' => '0068','msg' => print_r($response, true));//返回‘上传失败’,并返回错误信息
        }
    }
    //删除文件夹和里面的所有文件
    public static function delDirAndFile($dirName){
        if(is_dir($dirName)){
            if ( $handle = opendir( "$dirName" ) ) {
                while ( false !== ( $item = readdir( $handle ) ) ) {
                    if ( $item != "." && $item != ".." ) {
                        if ( is_dir( "$dirName/$item" ) ) {
                            self::delDirAndFile( "$dirName/$item" );
                        } else {
                            unlink( "$dirName/$item" );
                        }
                    }
                }
                closedir( $handle );
                rmdir( $dirName ) ;
            }
        }
    }
    //打包文件夹
    public static function addFilePathToZip($filePath ='', $zipUrl) {
        $zip = new ZipArchive;
        /*
        $zip->open这个方法第一个参数表示处理的zip文件名。
        第二个参数表示处理模式，ZipArchive::OVERWRITE表示如果zip文件存在，就覆盖掉原来的zip文件。
        如果参数使用ZIPARCHIVE::CREATE，系统就会往原来的zip文件里添加内容。
        如果不是为了多次添加内容到zip文件，建议使用ZipArchive::OVERWRITE。
        使用这两个参数，如果zip文件不存在，系统都会自动新建。
        如果对zip文件对象操作成功，$zip->open这个方法会返回TRUE
        */
        /*if(file_exists($zipUrl)){
            unlink($zipUrl);
        }*/
        if ($zip->open($zipUrl, ZipArchive::CREATE))
        {
            self::addFileToZip($filePath, $zip);
            $zip->close();
        }
    }
    public static function addFileToZip($path, $zip){
        $handler=opendir($path); //打开当前文件夹由$path指定。
        while(($filename=readdir($handler))!==false){
            if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
                if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                    self::addFileToZip($path."/".$filename, $zip);
                }else{ //将文件加入zip对象
                    $zip->addFile($path."/".$filename);
                }
            }
        }
        @closedir($path);
    }
    //文件大小换算成文本
    public static function formatBytes($size) {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return round($size, 2).$units[$i];
    }
    //自动保存文件函数
    public  static  function fileSave($path,$content) {
        if(!file_exists($path)) {
            file_put_contents($path,'');
            $oldContent = '';
        } else {
            $oldContent = file_get_contents($path);
        }
        $newContent = $oldContent ."\r\n". $content;
        file_put_contents($path, $newContent);
    }
    //读取指定的文件，并返回字符串
    public static function readFile( $filepath , $len = '' )
    {
        if (file_exists( $filepath )) {
            $fp = fopen( $filepath , 'r');
            $len = ($len == '')?filesize($filepath):$len;
            $str = fread($fp, $len);
            fclose( $fp );
        }else {
            $str = '';
        }
        return $str;
    }

    //获取url文件名带格式
    public static function fileName($url='') {
        $arr1_ = explode('/', $url);
        return end($arr1_);
    }
    //获取文件格式
    public static function getGeshi($url='') {
        if(!strstr($url, '.')) return '';
        $arr_ = explode('.', $url);
        return strtolower(end($arr_));
    }
    //去掉文件格式
    public static function noGeshi($fileName = '') {
        if(!strstr($fileName, '.')) return $fileName;
        $arr_ = explode('.', $fileName);
        unset($arr_[count($arr_)-1]);
        return join('.', $arr_);
    }


    //base64图片提取文件内容
    public static function gertBase64File($fileBase64='base64:://') {
        $str4 = ';base64,';
        $imgContent = explode($str4, $fileBase64)[1];
        $imgContent = base64_decode($imgContent);
        return $imgContent;
    }
}
