<?php
namespace app\admin\addon\sms\library;

use app\admin\addon\sms\model\Sms;
use fast\File;
/**
 * 发送API
 * demo仅供参考，demo最低运行环境PHP5.3
 * 请确认开启PHP CURL 扩展
 */
class Mingding {
	private $data;	//发送数据
	private $timeout = 30; //超时
	private $apiUrl = 'http://114.118.21.238:9999/sms.aspx';//发送地址
    protected $config = [];
	
	public function setConfig($config=[]) {
	    if(!$config) {
            $config = [
                'mingding_uid' => '',
                'mingding_account' => '',
                'mingding_secret' => '',
            ];
        }
        $this->config = $config;
	}

	/* 发送验证码 */
	public function smsSend($event='', $mobile='', $code=''){
		$content = '验证码：'. $code .'。请不要把验证码泄露给其他人。如非本人操作，可不用理会';
		$tempKey = $event .'_template';
		if(isset($this->config[$tempKey]) && $this->config[$tempKey]){
			$content = $this->config[$tempKey];
            $content = str_ireplace('#code#', $code, $content);
		}
		$sendContent =  $this->config['company_sign'].$content;
        $postData = array(
            'userid' => $this->config['mingding_uid'],
            'account' => $this->config['mingding_account'],
            'password' => $this->config['mingding_secret'],
            'mobile' => $mobile,
            'content' => $sendContent, //短信内容
            'action' => 'send',
        );
        $msgData = File::post_nr_str($this->apiUrl, '', $postData);
        $p = xml_parser_create();
        $vals = [];
        xml_parse_into_struct($p, $msgData, $vals, $index);
        xml_parser_free($p);
        if(!$vals) return '短信接口无反应';
        $statusData = $vals[1];
        $messageData = $vals[3];
        $status = isset($statusData['value']) ? strtolower($statusData['value']) : '';
        $message = isset($messageData['value']) ? strtolower($messageData['value']) : '';
        if($status != 'success' ) return '发送短信失败:'. $message;
        $result = Sms::saveSms($mobile,$code, $event, 'Mingding', $sendContent);
        if(!$result) return '保存短信失败';
		return true;
	}
	
	private function httpGet() {
        $type = !empty($this->config['type'])?$this->config['type']:0;
        $requesturl = $type == 0 ? $this->apiUrl:$this->apiUrl2;
		$url = $requesturl . '?' . http_build_query($this->data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);
		$res = curl_exec($curl);
		if (curl_errno($curl)) {      
			echo 'Error GET '.curl_error($curl);      
		}      
		curl_close($curl);
		return $res;
	}

	private function httpPost(){ // 模拟提交数据函数
        $type = !empty($this->config['type'])?$this->config['type']:0;
        $requesturl = $type == 0 ? $this->apiUrl:$this->apiUrl2;
		$curl = curl_init(); // 启动一个CURL会话      
		curl_setopt($curl, CURLOPT_URL, $requesturl); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查      
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在      
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器      
		curl_setopt($curl, CURLOPT_POST, true); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS,  http_build_query($this->data)); // Post提交的数据包
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout); // 设置超时限制防止死循环      
		curl_setopt($curl, CURLOPT_HEADER, false); // 显示返回的Header区域内容      
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 获取的信息以文件流的形式返回
		$result = curl_exec($curl); // 执行操作      
		if (curl_errno($curl)) {      
			echo 'Error POST'.curl_error($curl);      
		}      
		curl_close($curl); // 关键CURL会话      
		return $result; // 返回数据      
	}

    /**
     * @param $type|提交类型 POST/GET
     * @param $isTranscoding|是否需要转 $isTranscoding 是否需要转utf-8 默认 false
     * @return mixed
     */
	public function send($type = 'GET', $isTranscoding = false) {
		$this->data['content'] 	= $isTranscoding === true ? mb_convert_encoding($this->data['content'], "UTF-8") : $this->data['content'];
		return $type == "POST" ? $this->httpPost() : $this->httpGet();
	}

}