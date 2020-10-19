<?php

namespace fast;

use think\Config;

/**
 * 发送邮件
 * @author LR <rui6ye@163.com>
 */
class Email
{
    //发送邮件
    public static function mailto($address, $subject='title', $body)
    {
        $posterInfo = Config::get('cfg_email_poster');
        //引用jdk
        vendor('phpmailer.PHPMailerAutoload');
        $mail = new \PHPMailer(true);
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
        $mail->SetFrom($posterInfo[0], Config::get('web_title'));
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
            return $mail->ErrorInfo;//发送失败
        }
        else {
            return true;//发送成功
        }
    }

}
