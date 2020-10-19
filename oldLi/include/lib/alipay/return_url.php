<?php
//系统配置参数
require_once( $_SERVER["DOCUMENT_ROOT"]."/config.php");//配置文件
require_once( $_SERVER["DOCUMENT_ROOT"]."/include/core/class_taobaoapi.php" );//基础库

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>支付宝即时到账交易接口</title>
	</head>
    <body>
    <h3>返利试用官网即时到账充值</h3>
    <?php
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			$out_trade_no = $_GET['out_trade_no'];
			$trade_no = $_GET['trade_no'];
			$trade_status = $_GET['trade_status'];
			if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                $acc = new account();
                $ret = $acc->updateAccountChangeState($out_trade_no, $trade_no);
				echo "<p>充值单据号：{$out_trade_no}</p>";
				echo "<p>支付宝交易单号：{$trade_no}</p>";
				echo "<p>资金状态：已到账</p>";
			}else {
                echo "trade_status=".$_GET['trade_status'];
			}
		}else {
			echo "充值失败，请重试";
		}
	?>
    </body>
</html>