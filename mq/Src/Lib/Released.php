<?php

namespace Pub;

use Workerman\Worker;

class Released
{
    public static function saveRam($useRam) {

        $countTotal = bcdiv($useRam, 1024*1024, 2);
        if($countTotal > Config::get('maxCanUseMemory')) {
            print_r("内存总使用量:{$countTotal}MB 执行重启 \n");
            Worker::stopAll();
        }
    }
 
}