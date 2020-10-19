<?php 
/**
 * 系统信息 - 短信 类
 *
 * @version 1.0.0
 * 
 */
namespace Func;

class Message {
    protected static $msg = array(
        '0000' => '您未登录，<a href="/" target="_self">请先登录。</a>',
        '0001' => '登录系统成功！',
        '0002' => '您的登录密码错误',
        '0003' => '此用户帐号已经被禁止登录',
        '0004' => '帐号至少2位数',
        '0005' => '帐号已被注册',
        '0006' => '帐号可以使用',
        '00061' => '帐号不存在',
        '00062' => '您不是雇员,没有登录后台的权限',
        '0007' => '手机格式不正确',
        '0008' => '手机已经被注册',
        '0009' => '手机可以使用',
        '0010' => '邮箱已经被他人使用',
        '0013' => '添加成功',
        '0014' => '添加失败',
        '0023' => '缺少必填的信息，请重试',
        '0056' => '密码修改成功',
        '0038' => '获取成功',
        '0233' => '退出成功',

        '0502' => '提示您：',
        '0065' => '缺少数据ID',


        '0100' => '您没有积分',
        '0101' => '您积分不足以下载此资源',
        '0102' => '您不是会员',

        '0039' => '删除成功',
        '0040' => '删除失败',
        '0043' => '修改成功',
        '0044' => '修改失败',
        '0093' => '发送成功',
        '0094' => '发送失败',
        '0113' => '添加成功',
        '0114' => '添加失败',
        '0135' => '页面不存在',
        '0150' => '验证码错误',
        '0267' => '头像设置成功',
        '0346' => '您没有权限',
        '0375' => '您操作太快了,歇一会',
        '0388' => '上传成功',
        '0389' => '加载成功',
        '0562' => '成功加入播放列表',

    );
	static public function getMessage($id){
		return isset(self::$msg[$id]) ? self::$msg[$id] : 'no';
	}
    //返回Json格式的信息结构
	static public function json($data_) {
        $callBack = isset($_GET['callback']) ? $_GET['callback'] : '';
        if($callBack == 'flightHandler') {
            return "flightHandler(".json_encode($data_).");";
        } else {
            return json_encode($data_);
        }
	}
    //返回Json格式的信息结构
	static public function getMsgJson($id,$info=''){
        return json_encode(array('id' => $id,'msg' => self::getMessage($id),'info'=>$info));
	}
    //页面输出html 并且停止页面
    //$showHeader 是否需要显示头部html
    public static function Show($urlHtml) {
	    $needHeader = true;
        $load_text = isset($_GET['load_text']) ? trim($_GET['load_text']) : '';// load加载内容 不需要头部
        $from_form = isset($_GET['from_form']) ? trim($_GET['from_form']) : '';// 来自弹窗层 需要关闭最新窗口
        if ($load_text || $from_form)  $needHeader = false;
        $headerHtml = '';
        if($needHeader) {//需要头部
            $headerHtml = "<!DOCTYPE html>
            <html lang=\"zh-CN\">
            <head>
            <meta charset=\"utf-8\">
            <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
            <meta name=\"renderer\" content=\"webkit|ie-comp|ie-stand\"><!-- 强制360用急速模式 -->
            <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />
            <link href=\"/min/?f=/resource/pub/bootstrap-3.3.7/css/bootstrap.css,/resource/pub/css/jquery.lr_box.css,/resource/pub/css/pub.css,/resource/pub/css/jquery.lr_element.css\" rel=\"stylesheet\" media=\"all\" />
            <script src=\"/min/?f=/resource/pub/js/jq/jquery-3.2.1.js,/resource/pub/bootstrap-3.3.7/js/bootstrap.js,/resource/pub/js/jquery-lr_base.js,/resource/pub/js/jquery-lr_box.js,/resource/pub/js/jquery-lr_element.js\"></script>
            <link rel=\"shortcut icon\" href=\"/favicon.ico\" type=\"image/x-icon\" /> ";
        }

        echo   "{$headerHtml}
        <title>提示信息</title>
        <body>
        <div class=\"alert alert-warning\" style='max-width: 500px; padding-bottom: 20px; margin: 0 auto; width: 90%;'>
                <h4>提示:</h4>
                <p>{$urlHtml}</p>
           </div>
        </div>
        </body></html>";
    }
//----------------------------------------------------------------------------------------------------------------------
    
   
//----------------------------------------------------------------------------------------------------------------------
    //发送邮件
    function mailto($address, $subject='title', $body)
    {
        $posterInfo = $GLOBALS['cfg_email_poster'];
        $mail = new phpmailer(); // defaults to using php "mail()"
        $mail->IsSMTP();
        if($posterInfo[2] == 0) {
            $mail->Host = "smtp.163.com";			// SMTP 服务器
        } elseif ($posterInfo[2] == 1) {
            $mail->Host = "smtp.qq.com";			// SMTP 服务器
        } elseif ($posterInfo[2] == 2) {
            $mail->Host = "smtp.139.com";			// SMTP 服务器
        }
        $mail->SMTPAuth = true;            		// 打开SMTP 认证
        $mail->Username = $posterInfo[0]; 	// 用户名
        $mail->Password = $posterInfo[1];          // 密码
        $mail->AddReplyTo($posterInfo[0],"");
        $mail->SetFrom($posterInfo[0], 'sasasui');
        $mail->AddReplyTo($posterInfo[0],"");
        $mail->AddAddress($address, '网站会员');
        $mail->Subject = "=?UTF-8?B?".base64_encode($subject)."?=";
        $mail->IsHTML(true);
        $mail->CharSet = "utf-8";
        $mail->Encoding = "base64";
        // optional, comment out and test
        $mail->AltBody = $body;
        $mail->MsgHTML($body);
        if(!$mail->Send()) {
            return $this->getMsgJson('0093', $mail->ErrorInfo);//发送失败
        }
        else {
            return $this->getMsgJson('0092');//发送成功
        }
    }
}