<?php

class Aes
{
    static $iv = '0987654321acbdef';

    static function encrypt($input, $key){
        $size = mcrypt_get_block_size('des', 'ecb');
        $input = self::pkcs5_pad($input, $size);
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        $data = preg_replace("/\s*/", '',$data);
        return str_replace(['+','/','='], ['-','*',''], $data);
    }
    static function decrypt($encrypted, $key) {
        $encrypted = str_replace(['*','','-'],['/','=','+'],$encrypted);
        $encrypted = base64_decode($encrypted);
        $td = mcrypt_module_open('des','','ecb','');
        //使用MCRYPT_DES算法,cbc模式                  
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        //初始处理                  
        $decrypted = mdecrypt_generic($td, $encrypted);
        //解密                
        mcrypt_generic_deinit($td);
        //结束              
        mcrypt_module_close($td);
        $y=self::pkcs5_unpad($decrypted);
        return $y;
    }
    static function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    static function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, -1 * $pad);
    }
    private static function __aesEncode($data,$privateKey){
        //新版
//        if (strlen($data) % 16) {
//            $data = str_pad($data,strlen($data) + 16 - strlen($data) % 16, "\0");
//        }
//      $encrypted = openssl_encrypt($data, 'AES-128-CBC', $privateKey, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, self::$iv);
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC,self::$iv);
        return rtrim((base64_encode($encrypted)));
    }
    private static function __aesDecode($data,$privateKey){
        $encryptedData = base64_decode($data);
        //新版
//      $decrypted = openssl_decrypt($encryptedData, 'AES-128-CBC', $privateKey, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, self::$iv);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC,self::$iv);
        return rtrim($decrypted);
    }


    //保护url给前端
    public static function protectSourceUrl($urlName) {
        $urlName = base64_encode($urlName);
        return str_replace('=', '-d', $urlName);
    }
    //解密保护url
    public static function unProtectSourceUrl($urlName) {
        $urlName = str_replace('-d', '=', $urlName);
        return base64_decode($urlName);
    }
    
    //统一加密data简化加密写法
    public static function aesHash($data=[], $aesKey='') {
        $toJson = is_array($data) ? json_encode($data, true) : $data;
        $aesKey = $aesKey ? $aesKey : CommonCfg::get('hashRule.enKey');
        return self::__aesEncode($toJson, $aesKey);
    }
    //统一加密data简化加密写法
    public static function aesSysHash($data=[]) {
        $toJson = is_array($data) ? json_encode($data, true) : $data;
        return self::__aesEncode($toJson, CommonCfg::get('hashRule.enKey'));
    }

    //统一解密data简化加密写法
    public static function aesDecode($encrypted='', $aesKey='') {
        $aesKey = $aesKey ? $aesKey : CommonCfg::get('hashRule.enKey');
        return self::__aesDecode($encrypted, $aesKey);
    }
}
