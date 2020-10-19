<?php

use \Pub\RedisMQ;
use \Pub\DbBase;
use \Pub\Group;

require_once 'Config.php';
require_once 'Src/Lib/Released.php';
require_once 'Src/Lib/RedisMQ.php';
require_once 'Src/Lib/DbBase.php';
require_once 'Src/Lib/Group.php';

RedisMQ::init();
DbBase::init();

//写入redisMQ
//$redisData = [$finalMethod, $registIds,$senderName,$content, $extra, $iosPayloadOption];
//\RedisMQ::add('groupStream', 'data', json_encode($redisData));

//消费者数量
$customerQz = RedisMQ::$mqCfg['groupName'];
new Group('c1', $customerQz);
