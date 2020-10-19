<?php
/**
 * 保存base64的图片类
 */
class savebase64
{
    /* 判断图片类型 */
    public static function validate_mime($url){
        if(stristr($url,'data:image/png;base64,')){
            return 'png';
        }
        else{
            return 'jpg';
        }
    }
    /* 接收数据 */
    public static function init($url,$userid){
        if(self::validate_mime($url)=='png'){
            return self::replacePng($url,$userid);
        }
        else{
            return self::replaceJpg($url,$userid);
        }
    }

    /* 替换 png */
    public static function replacePng($url,$userid){
        $data = base64_decode(str_replace('data:image/png;base64,', '', $url));
        return self::save('png', $data,$userid);
    }

    /* 替换 jpg */
    public static function replaceJpg($url,$userid){
        $data = str_replace('data:image/jpeg;base64,', '', $url);
        $data = str_replace('data:image/gif;base64,', '', $data);
        $data = base64_decode($data);
        return self::save('jpg', $data,$userid);
    }

    /* 保存 */
    public static function save($ext,$data,$userid){
        //文件保存目录路径
        $save_path =  root.'/upload/attached/';
        //文件保存目录URL
        $save_url = 'upload/attached/';

        $save_path = $save_path .'share/';
        $save_url = $save_url . "share/";
        //创建文件夹
        if (!file_exists($save_path)) {
            @mkdir($save_path,0755);
        }

        $ymd = date("Ymd");
        $save_path .= $ymd . "/";
        $save_url .= $ymd . "/";
        if (!file_exists($save_path)) {
            mkdir($save_path);
        }
        //新文件名
        $rand_name = md5(time(). rand(10000, 99999));
        $file_name = $userid . '_' . $rand_name . '.' . $ext;
        $save_path .= $file_name;
        $save_url .= $file_name;
        /* 保存 */
        file_put_contents($save_path,$data);
        //同步到新的图片服务器 -- 开始
        $file_url = self::upFileTOImgSite($save_path,$save_url);
        //同步到新的图片服务器 -- 结束
        return $file_url;
    }
    /* 删除本地和服务器群上的文件 */
    public static function unlink($url){
        global $cfg;
        /* 移除服务器 */
        if(!isset($cfg['site'])){
            die("没有配置站点的信息");
        }
        foreach($cfg['site'] as $value){
            $siteInfo = $value['verify']['ftp'];/* 获取站点下登录验证需要的ftp配置 */
            $ftp = new ftp($siteInfo['server'],$siteInfo['port'],$siteInfo['user_name'],$siteInfo['user_pwd'],$siteInfo['PASV']);
            /*重连接*/
            if(!$ftp->conn_id){
                $ftp = new ftp($siteInfo['server'],$siteInfo['port'],$siteInfo['user_name'],$siteInfo['user_pwd'],$siteInfo['PASV']);
                /*如果再次重连接还失败，记录日志*/
                if(!$ftp->conn_id){
                    self::logPHPFTP('ftp_connect',$url,$url);
                    return self::unlink_PHPFTPFile($url);/*删除服务器已上传的文件，并提示用户重新上传*/
                }
            }
            $url = str_replace($value['url'],'',$url);
            $ftp->del_file($url);
            //$ftp->close();
        }

        /* 移除本地 */
        @unlink($url);
        return  true;
    }

    public static function upFileTOImgSite($file_url_local,$file_url_server){
        global $cfg;
        if(!isset($cfg['site'])){
            die("没有配置站点的信息");
        }
        foreach($cfg['site'] as $value){
            $siteInfo = $value['verify']['ftp'];/* 获取站点下登录验证需要的ftp配置 */
            $ftp = new ftp($siteInfo['server'],$siteInfo['port'],$siteInfo['user_name'],$siteInfo['user_pwd'],$siteInfo['PASV']);
            /*重连接*/
            if(!$ftp->conn_id){
                $ftp = new ftp($siteInfo['server'],$siteInfo['port'],$siteInfo['user_name'],$siteInfo['user_pwd'],$siteInfo['PASV']);
                /*如果再次重连接还失败，记录日志*/
                if(!$ftp->conn_id){
                    self::logPHPFTP('ftp_connect',$file_url_local,$file_url_server);
                    return self::unlink_PHPFTPFile($file_url_local);/*删除服务器已上传的文件，并提示用户重新上传*/
                }
            }
            $ftp->up_file($file_url_local,$file_url_server);
            //$ftp->close();
        }
        return  $cfg['site'][1]['url'].$file_url_server;
    }

    public static function logPHPFTP($error,$file_url_local,$file_url_server){
        $file = root."/log/php_ftp_error_log.txt";
        $Ts=fopen($file,"a+");
        fputs($Ts,"错误类型: ".$error." ；-提交IP: ".$_SERVER["REMOTE_ADDR"]." ；- 时间: ".strftime("%Y-%m-%d %H:%M:%S")."；-本地文件路径：{$file_url_local}；-服务器文件路径：{$file_url_server},"."\r\n");
        fclose($Ts);
    }

    public static function unlink_PHPFTPFile($file_url_local){
        @unlink($file_url_local);/*删除服务器已上传的文件，并提示用户重新上传*/
        //die('系统原因，你上传可能没成功，请重新上传！');
        return false;
    }


}