<?php
/* ClassName: log

 * */
class log {
	
	private $fName = NULL;
	
	public function __construct() {
		$this->fName = $GLOBALS['cfg_savelogpath'].'/'.date('Ymd',time()).".log";
	}
	
	public function _setall($vartab) {
		if (is_array ( $vartab )) {
			foreach ( $vartab as $key => $value ) {
				$this->$key = $value;
			}
		}
	}
    //创建多级文件夹
    public function creatdir($newPath){
        if(!is_dir($newPath)) {
            if(log::creatdir(dirname($newPath))){
                mkdir($newPath,0777);
                return true;
            }
        }else{
            return true;
        }
    }
    //如果文件不存在，创建多级文件夹
    public function checkFileDir($allpath){
        if(!file_exists($allpath) && strstr($allpath,'/')) {
            $pathArray = explode('/',$allpath);
            $newPath = '';
            for ($i = 0; $i< count($pathArray)-1 ; $i ++) {
                $newPath .= $pathArray[$i]."/";
            }
            $newPath = trim($newPath,'/');
            log::creatdir($newPath);
        }
    }

	//写入日志
	public function writelog($filePath = '', $user='Uid', $memo = "Memo") {
        if(!$filePath) {
            $filePath = $GLOBALS['cfg_savelogpath'].'/'.date('Ymd',time()).'.log';
        }
        log::checkFileDir($filePath);
        $oldContent = '';
        if(file_exists($filePath)) {
            $oldContent = file_get_contents($filePath);
        }
        $str = Timer::now () . "||" . $user . "||" . Ip::getIp () . "||" . $memo . "\n";
        $newContent = $oldContent ."\r\n". $str;
        file_put_contents($filePath,$newContent);
	}
	
	public function readlog($date = "") {
		$readlog = $date == "" ? $this->fName : $GLOBALS['cfg_savelogpath'] . "/" . $date . ".log";
		if (file_exists ( $readlog )) {
			return file ( $readlog );
		} else {
			return null;
		}
	}
    //登录错误日志 操作类
    public function addLoginLog($unick ,$uip, $today){
        $db = mysql::getInstance();
        //如果没有密码错误记录，新增一条
        if(!DbBase::ifExist('s_loginerrlog',"l_nick ='". $unick ."' AND l_day='". $today ."' AND l_ip='". $uip ."'")) {
            $data = array(
                'l_addtime' => Timer::now(),
                'l_nick' => $unick,
                'l_wrongtime' => 1,
                'l_day' => $today,
                'l_ip' => $uip,
            );
            DbBase::insertRows('s_loginerrlog', $data);
        } else {
            $sql = "UPDATE s_loginerrlog SET l_wrongtime = l_wrongtime+1 WHERE l_nick = '". $unick ."' AND l_day = '". $today ."' AND l_ip = '". $uip ."'";
            $db->Execute($sql);
        }
    }

}