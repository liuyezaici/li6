<?php
/* ClassName: file
 * Memo:News class
 * Version:1.0.0
 * EditTime:2014-04-26

 * */
NameSpace Func;
class File
{

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
    public  static function slog($logs)
    {
        $toppath=root."/log/feifa_log.txt";
        $Ts=fopen($toppath,"a+");
        fputs($Ts,$logs."\r\n");
        fclose($Ts);
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
            if(!Ip::isLocal() && $needRemote) {
                $filebackurl_no_root = $savePath."/".$imginfo[0]['saveName'];//同步到新的图片服务器 -- 【本地站点除外】
                $file_url_server = $filebackurl_no_root;
                $webUploadData = self::uploadToHttpOss(RootPath. $filebackurl_no_root, trim($file_url_server, "/"));
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

    //获取文件名 无格式
    public static function getFileName($url) {
        if(!strstr($url, '.')) return $url;
        $splitNames = explode(".", $url);//将文件原名按.分割打成字符串数组
        unset($splitNames[count($splitNames)-1]); //抹掉文件后缀名数据
        return implode('.', $splitNames);//将抹掉后缀名的数组拼接成文件名
    }

    //生成缩略图 文件名带_min [gd库]
    public static function resizeImageGD($pic_url1, $newSize = 265, $newFileName = '') {
        $fileName = basename($pic_url1);
        $savePath = dirname($pic_url1);
        //生成的小图文件名
        $newResizeFileName = $newFileName ? $newFileName : self::urlToMin($fileName);
        //获取原图内容
        $src = imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"].$pic_url1);
        //取得源图片的宽度和高度
        $size_src = getimagesize($_SERVER["DOCUMENT_ROOT"].$pic_url1);
        $old_width = $size_src['0'];
        $old_height = $size_src['1'];
        //指定缩放出来的最大的宽度（也有可能是高度）
        //计算等比尺寸
        if($old_width > $old_height){
            $new_width1 = $newSize;
            $new_height1 = $old_height * ($newSize / $old_width);
        } else {
            $new_height1 = $newSize;
            $new_width1 = $old_width * ($newSize / $old_height);
        }
        //声明一个$w宽，$h高的真彩图片扫描图
        if(function_exists("imagecopyresampled")) {
            //生成第1张图片
            $image_1 = imagecreatetruecolor($new_width1, $new_height1);
            //2.上色
            $color=imagecolorallocate($image_1, 255,255,255);
            //3.设置透明
            imagecolortransparent($image_1, $color);
            imagefill($image_1,0,0,$color);
            imagecopyresampled($image_1, $src, 0,0,0,0, $new_width1, $new_height1, $old_width, $old_height);
        } else {
            //生成第1张图片
            $image_1 = imagecreate($new_width1, $new_height1);
            //2.上色
            $color=imagecolorallocate($image_1, 255,255,255);
            //3.设置透明
            imagecolortransparent($image_1, $color);
            imagefill($image_1,0,0,$color);
            imagecopyresized($image_1, $src,0,0,0,0, $new_width1, $new_height1, $old_width, $old_height);
        }
        imagejpeg($image_1, $_SERVER["DOCUMENT_ROOT"]. "/".$newResizeFileName); //保存第1张图片
    }
    //判断文件格式是不是图片
    public static function isImg($geshi='') {
        return in_array($geshi, ['jpg', 'jpeg', 'png', 'gif']);
    }
    //获取url文件名
    public static function fileName($url='') {
        $arr1_ = explode('/', $url);
        return end($arr1_);
    }
    //获取url格式
    public static function geshi($url='') {
        $arr_ = explode('.', $url);
        return strtolower(end($arr_));
    }
    //将图片转为封面 带_min
    public static function urlToMin($oldUrl='') {
        $geshi = self::geshi($oldUrl);
        return substr($oldUrl, 0, strlen($oldUrl)-strlen($geshi)-1) .'_min.'. $geshi;
    }
    //将图片封面 恢复原图  _min 去掉
    public static function urlNoMin($oldUrl='') {
        $geshi = self::geshi($oldUrl);
        $fileName = substr($oldUrl, 0, strlen($oldUrl)-strlen($geshi)-1);
        if(substr($fileName, -4, 4) == '_min') {
            $fileName = substr($fileName, 0, strlen($fileName)-4);
        }
        return $fileName .'.'. $geshi;
    }
    //将图片封面 恢复原图  _min 换成 _preview
    public static function urlToPreview($oldUrl='') {
        $geshi = self::geshi($oldUrl);
        $fileName = substr($oldUrl, 0, strlen($oldUrl)-strlen($geshi)-1);
        if(substr($fileName, -4, 4) == '_min') {
            $fileName = substr($fileName, 0, strlen($fileName)-4);
        }
        return $fileName .'_preview.'. $geshi;
    }
    //生成缩略图 文件名带_min [imagick]
    public static function resizeImage($pic_url1, $newSize = 265, $newFileName = '') {
        //生成的小图文件名
        $newResizeFileName = $newFileName ? $newFileName : self::urlToMin($pic_url1);
        if(!file_exists(RootPath. $pic_url1)) return; //文件不存在要退出 不然页面会停止
        //取得源图片的宽度和高度
        $size_src = getimagesize(RootPath. $pic_url1);
        $old_width = $size_src['0'];
        $old_height = $size_src['1'];
        //指定缩放出来的最大的宽度（也有可能是高度）
        //计算等比尺寸 只有超出最大尺寸才压缩
        if($old_width > $newSize || $old_height > $newSize) {
            if($old_width >= $old_height){
                $new_width1 = $newSize;
            } else {
                $new_width1 = $old_width * ($newSize / $old_height);
            }
            if (class_exists('imagick')) {
                $im = new imagick(RootPath. $pic_url1);
                $im->thumbnailImage($new_width1, 0);
                $im->writeImage(RootPath.  "/".$newResizeFileName);
            }
        } else {//图片太小 也要复制成小图/*
             if (class_exists('imagick')) {
                $im = new imagick(RootPath. $pic_url1);
                 $im->thumbnailImage($old_width, 0);
                 $im->writeImage(RootPath. "/".$newResizeFileName);
            }
        }
        return $newResizeFileName;
    }
    //获取系统图片作为封面
    public static function getSystemImageForCover($pic_url='') {
        if(!$pic_url) return '';
        //生成的小图文件名
        $newResizeFileName = self::urlToMin($pic_url);
        $defauleUrl = RootPath. share::$shareFilesDefaultCover;
        copy($defauleUrl, root.$newResizeFileName);
        return $newResizeFileName;
    }
    //图片加logo
    public static function addLogo($text, $color, $size, $src, $dst, $x, $y, $font = 'ant_weiruanyahei.ttf')
    {
        $font = root."include/lib/data/".$font;
        if (class_exists('imagick')) {
            $draw = new ImagickDraw();
            $draw->setGravity(Imagick::GRAVITY_CENTER);
            $draw->setFont($font);
            $draw->setFontSize($size);
            $draw->setFillColor(new ImagickPixel($color));
            $im = new imagick();
            $properties = $im->queryFontMetrics($draw, $text);
            $im->newImage(intval($properties['textWidth'] + 5),
                intval($properties['textHeight'] + 5), new ImagickPixel('transparent'));
            $im->setImageFormat('png');
            $im->annotateImage($draw, 0, 0, 0, $text);

            $image = new Imagick($src);
            $image->compositeImage($im, Imagick::COMPOSITE_OVER, $x, $y);
            $image->writeImage($dst);
            $im->destroy();
            $image->destroy();
        }
    }
    // 添加水印图片
    public function add_watermark($path, $x = 0, $y = 0)
    {
        $watermark = new Imagick($path);
        $draw = new ImagickDraw();
        $draw->composite($watermark->getImageCompose(), $x, $y, $watermark->getImageWidth(), $watermark->getimageheight(), $watermark);
        if($this->type=='gif')
        {
            $image = $this->image;
            $canvas = new Imagick();
            $images = $image->coalesceImages();
            foreach($image as $frame)
            {
                $img = new Imagick();
                $img->readImageBlob($frame);
                $img->drawImage($draw);
                $canvas->addImage( $img );
                $canvas->setImageDelay( $img->getImageDelay() );
            }
            $image->destroy();
            $this->image = $canvas;
        }
        else
        {
            $this->image->drawImage($draw);
        }
    }
    //上传文件目录加安全校验
    public static function makeSafeUploadCode($pathUrl, $uid_) {
        return Str::getMD5($pathUrl."[save_hash_lr]".$uid_);
    }
    //判断文件是否本地文件
    public static function isLocalFile($url='') {
        $left7 = substr($url, 0, 7);
        $left7 = strtolower($left7);
        if($left7 == 'http://' || $left7 == 'https:/') {
            return false;
        }
        return true;
    }
}