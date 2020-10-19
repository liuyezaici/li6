<?php
/**
 * 验证码模块
 * 
 * 
 */

class validate
{
    private $sessionTag = 'secret_session';

    public function __construct(){
        @session_start();
    }

    public function getValidate($input){
    return $input == (isset($_SESSION[$this->sessionTag])?$_SESSION[$this->sessionTag]:'');
    }

    //随机一下验证码
    public function  rndValidate(){
        $_SESSION [$this->sessionTag] = self::getRndCode();
    }

    //获取随机字符
    public static  function getRndCode(){
        $rndstring = '';
        for($i = 0; $i < 4; $i++) {
            $rndstring .= mt_rand(1,9);
        }
        return $rndstring;
    }

    //生成纯数字验证码 [全站通用]
    public function getValidateImage(){
        //输出数据头
        header ( "Pragma:no-cache\r\n" );
        header ( "Cache-Control:no-cache\r\n" );
        header ( "Expires:0\r\n");
        $w = 100;
        $h = 35;
        $im = imagecreate($w, $h);
        $fontColor = imagecolorallocate($im, 126, 156, 166);
        $white = imagecolorallocate($im, 255, 255, 255);
        //$fontType = 'include/lib/data/ant' . mt_rand ( 1, 4 ) . '.otf';
        $fontType = 'include/lib/data/ant3.otf';

        $num = rand(1000, 9999);
        $_SESSION[$this->sessionTag] = $num;
        $gray2 = imagecolorallocate($im, 118, 151, 199);

        //画背景
        imagefilledrectangle($im, 0, 0, 100, $h, $white);
        //在画布上随机生成大量点，起干扰作用;
        for ($i = 0; $i < 780; $i++) {
            imagesetpixel($im, rand(0, $w), rand(0, $h), $gray2);
        }
        //imagestring($im, 5, 5, 10, $num1, $red);
        //imagettftext(对象, 字体大小, 角度, x坐标,y坐标)
        imagettftext($im, 24, mt_rand (-15, 2 ), 15, 25, $fontColor, $fontType, $num);

        header("Content-type: image/png");
        imagepng($im);
        imagedestroy($im);
    }
}