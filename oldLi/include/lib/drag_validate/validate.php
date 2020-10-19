<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");//系统参数配置
$pageClass = new page();
$validateSetClass = new drag_validate();
$validateSessionName = 'lr_validate_pic';
$wrongMaxTimes = 12; //错误几次则强制刷新
$options = page::postGet();
@session_start();

$do = isset($options['do']) ? trim($options['do']) : '';
$point = isset($options['point']) ? trim($options['point']) : 0;

if($do == 'validate') {
    $wrongTimes = $validateSetClass->getWrongTimes();
    $old = isset($_SESSION['lr_validate_pic']) ? $_SESSION['lr_validate_pic']: 0;
    $near = $validateSetClass->compareValidate($point);
    if($near != 0) {
        //$errText = '差了'. abs($near) .'点';
        $errText = '差了'. $point .'|'. $old .'点';
        $wrongTimes ++;
        $validateSetClass->setWrongTimes($wrongTimes);
        //错误次数超过5次 则清空刷新
        if($wrongTimes >= $wrongMaxTimes) {
            $validateSetClass -> clearAllCache();
            print_r(message::getMsgJson('0022', '错误次数超过'. $wrongMaxTimes .'次,自动刷新'));//请刷新
            exit;
        }
        print_r(message::getMsgJson('0502', '手不要抖啊,'. $errText));
        exit;
    } else {
        $validateSetClass -> clearWrongCache();
        print_r(message::getMsgJson('0038'));
        exit;
    }
}
$data = $validateSetClass->getOkPng();
$temp = array_chunk($data['data'],20);
$left_pic = $temp[0];
$right_pic = $temp[1];
$pg_bg = $data['bg_pic'];
$ico_pic = $data['ico_pic'];
$y_point = $data['y_point'];
?>
<script src="/resource/pub/js/jquery-1.7.2.min.js"></script>
<link href="css/drag.css" rel="stylesheet" type="text/css"/>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>图片验证码</title>
</head>
<style>
    body {
        padding: 0;
        margin: 0;
    }
    .gt_cut_fullbg_slice {
        float: left;
        width: 13px;
        height: 58px;
        margin: 0 !important;
        border: 0px;
        padding: 0 !important;
        background-image: url("<?=$pg_bg?>");
    }
    .drag_box .xy_img_bord{
        background-image: url(<?=$ico_pic['url']?>);z-index: 999;width: <?=$ico_pic['w']?>px;height:<?=$ico_pic['h']?>px;
    }
</style>
<body>
<div style="width: 260px;height: 116px;" class="drag_box" id="lr_drag_validate">
    <?php
    foreach ($left_pic as $vo) {
        $left_symbol  = $vo[0] == 0 ?'':'-';
        $right_symbol = $vo[1] == 0 ?'':'-';
        echo "<div class=\"gt_cut_fullbg_slice\" style=\"background-position: {$left_symbol}{$vo[0]}px {$right_symbol}{$vo[1] }px;\"></div>";
    }
    foreach ($right_pic as $vo) {
        $left_symbol  = $vo[0] == 0 ?'':'-';
        $right_symbol = $vo[1] == 0 ?'':'-';
        echo "<div class=\"gt_cut_fullbg_slice\" style=\"background-position: {$left_symbol}{$vo[0]}px {$right_symbol}{$vo[1] }px;\"></div>";
    }
    ?>
    <div style="top: <?php echo($y_point);?>px;left: 0;display: none;" class="xy_img_bord"></div>
    <div id="drag_bar" class="drag_bar"></div>
</div>
</body>

<script src="js/drag.js?aa" type="text/javascript"></script>
<script>
    $('#lr_drag_validate').drag('#drag_bar', '.xy_img_bord');
</script>