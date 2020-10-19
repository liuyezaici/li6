<?php

namespace app\admin\addon\sms\library;

use app\admin\addon\sms\model\Sms;

/**
 * 阿里大于SMS短信发送
 */
class Alisms
{

    private $_params = [];
    public $error = '';
    protected $config = [];
	
	public function setConfig($config = []){
        if(!$config) {
            $config = [
                'aliyun_key' => '',
                'aliyun_secret' => '',
                'aliyun_sign' => '',
                'sms_template' => '',
            ];
        }
        $setConfig = $config;
        $setConfig['key'] = $config['aliyun_key'];
        $setConfig['secret'] = $config['aliyun_secret'];
        $setConfig['company_sign'] = $config['company_sign'];
        $this->config = $setConfig;
	}

    /**
     * 短信发送行为
     * @param   Sms     $params
     * @return  boolean
     */
    public function smsSend($event='', $mobile='', $code='') {
        $content = '验证码：'. $code .'。请不要把验证码泄露给其他人。如非本人操作，可不用理会';
        $tempKey = $event .'_template';
        if(isset($this->config[$tempKey]) && $this->config[$tempKey]){
            $content = $this->config[$tempKey];
            $content = str_ireplace('#code#', $code, $content);
        }
        $sendContent =  $this->config['company_sign'].$content;
        $result = $this->mobile($mobile)
                ->template($sendContent)
                ->param(['code' => $code])
                ->send();
        if($result !=1 ) return '发送短信失败:'. $result;
        $result = Sms::saveSms($mobile, $code, $event, 'Alisms', $sendContent);
        if(!$result) return '保存短信失败';
    }

    /**
     * 单例
     * @param array $options 参数
     * @return Alisms
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance))
        {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * 设置签名
     * @param string $sign
     * @return Alisms
     */
    public function sign($sign = '')
    {
        $this->_params['SignName'] = $sign;
        return $this;
    }

    /**
     * 设置参数
     * @param array $param
     * @return Alisms
     */
    public function param(array $param = [])
    {
        foreach ($param as $k => &$v)
        {
            $v = (string) $v;
        }
        unset($v);
        $this->_params['TemplateParam'] = json_encode($param);
        return $this;
    }

    /**
     * 设置模板
     * @param string $code 短信模板
     * @return Alisms
     */
    public function template($code = '')
    {
        $this->_params['TemplateCode'] = $code;
        return $this;
    }

    /**
     * 接收手机
     * @param string $mobile 手机号码
     * @return Alisms
     */
    public function mobile($mobile = '')
    {
        $this->_params['PhoneNumbers'] = $mobile;
        return $this;
    }

    /**
     * 立即发送
     * @return boolean
     */
    public function send()
    {
        $this->error = '';
        $params = $this->_params();
        $params['Signature'] = $this->_signed($params);
        $response = $this->_curl($params);
        if ($response !== FALSE)
        {
            $res = (array) json_decode($response, TRUE);
            if (isset($res['Code']) && $res['Code'] == 'OK')
                return TRUE;
            $this->error = isset($res['Message']) ? $res['Message'] : 'InvalidResult';
        }
        else
        {
            $this->error = 'InvalidResult';
        }
        return FALSE;
    }

    /**
     * 获取错误信息
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    private function _params()
    {
        return array_merge([
            'AccessKeyId'      => $this->config['key'],
            'SignName'         => isset($this->config['company_sign']) ? $this->config['company_sign'] : '',
            'Action'           => 'SendSms',
            'Format'           => 'JSON',
            'Version'      => '2017-05-25',
            'SignatureVersion' => '1.0',
            'SignatureMethod'  => 'HMAC-SHA1',
            'SignatureNonce'   => uniqid(),
            'Timestamp'        => gmdate('Y-m-d\TH:i:s\Z'),
                ], $this->_params);
    }

    private function percentEncode($string)
    {
        $string = urlencode($string);
        $string = preg_replace('/\+/', '%20', $string);
        $string = preg_replace('/\*/', '%2A', $string);
        $string = preg_replace('/%7E/', '~', $string);
        return $string;
    }

    private function _signed($params)
    {
        $sign = $this->config['secret'];
        ksort($params);
        $canonicalizedQueryString = '';
        foreach ($params as $key => $value)
        {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = 'GET&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $sign . '&', true));
        return $signature;
    }

    private function _curl($params)
    {
        $uri = 'http://dysmsapi.aliyuncs.com/?' . http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.98 Safari/537.36");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $reponse = curl_exec($ch);
        curl_close($ch);
        return $reponse;
    }

}
