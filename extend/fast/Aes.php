<?php

namespace fast;

class Aes
{

    protected function encrypt($input)
    {
        $data = openssl_encrypt($input, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $this->hexToStr($this->hex_iv));
        $data = base64_encode($data);
        return $data;
    }

    protected function decrypt($input)
    {
        $decrypted = openssl_decrypt(base64_decode($input), 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $this->hexToStr($this->hex_iv));
        return $decrypted;
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
    private static function __aesEncode($data,$privateKey, $iv=''){
        //新版
        if (strlen($data) % 16) {
            $data = str_pad($data,strlen($data) + 16 - strlen($data) % 16, "\0");
        }
        $encrypted = openssl_encrypt($data, 'AES-128-CBC', $privateKey, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
//      $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
        return rtrim((base64_encode($encrypted)));
    }
    private static function _aesDecode($data,$privateKey, $iv){
        $encryptedData = base64_decode($data);
        //新版
        $decrypted = openssl_decrypt($encryptedData, 'AES-128-CBC', $privateKey, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
//     $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
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
    public static function aesHash($data=[], $aesKey, $iv) {
        $toJson = is_array($data) ? json_encode($data, true) : $data;
        return self::__aesEncode($toJson, $aesKey, $iv);
    }


    //统一解密data简化加密写法
    public static function aesDecode($encrypted='', $aesKey, $iv) {
        return self::_aesDecode($encrypted, $aesKey, $iv);
    }



}
