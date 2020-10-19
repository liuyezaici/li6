<?php
//文字管理
class mod_ufonts extends pageuser
{
    function __construct()
    {
        parent::__construct();
    }
    function makeArticleCoverUrl($s_id) {
        return $GLOBALS['cfg_photo_aticle_imagefiles'].'/pic_'. $s_id .'_'. Str::getRam(12) .'.jpg';
    }

    function  doAction() {
        $userId = $this->userClass->getUserAttrib('userId');
        $mytime = Timer::now();
        $db = mysql::getInstance();
        switch ($this->getOption('do')) {
            //添加文字
            case 'add_fonts':
                $s_words = $this->getOption('s_words');
                if(!$s_words) {
                    return $this->error('请输入文字');
                }
                $s_words = str_replace(' ', '', $s_words);
                $s_words = str_replace('　', '', $s_words);
                preg_match_all("/./u", $s_words, $wordArrys);
                $wordArrys = $wordArrys[0];
                $wordArrys = array_unique($wordArrys);
                $successNum = 0;
                foreach ($wordArrys as $tmpWord) {
                    $tmpWord = trim($tmpWord);
                    if(!$tmpWord) continue;
                    $tmpWord = str_replace('\\', '\\\\', $tmpWord);
                    $tmpInfo = DbBase::getRowBy('s_keywords_zi', 's_id', "s_word='{$tmpWord}'");
                    if($tmpInfo) continue;
                    $newData = [];
                    $newData['s_word'] = $tmpWord;
                    $newData['s_addtime'] = $this->myTime;
                    if(DbBase::insertRows('s_keywords_zi', $newData) == 1) {
                        $successNum ++;
                    }
                }
                return Message::getMsgJson('0113', $successNum);
        }
    }

    function  getData() {
        $db = mysql::getInstance();
        $userId = $this->userClass->getUserAttrib('userId');
        $show = $this->getOption('show');
        $arr = array();
        switch ($show) {
            case 'form':
                break;
            //所有文字
            default:
                $searchkey = $this->getOption('searchkey');
                $page = $this->getOption('page', 1, 'int');
                $wh_ = "where 1";
                if( $searchkey ){
                    $searchkey = urldecode($searchkey);
                    $wh_ .= " AND s_word = '{$searchkey}'";
                }
                $fields = 's_id,s_word';
                $sql = "SELECT ". $fields ." FROM `s_keywords_zi` ". $wh_ ." order by s_id desc";
                $div = new Divpage($sql, "", $fields, $page, 10);
                $div -> getDivPage(2);
                $listResult = $div->getPage();
                $pageInfo = $div->getPageInfo();
                $arr2 = array(
                    'listResult' => json_encode($listResult),
                    'pageInfo' => json_encode($pageInfo),
                    'searchkey' => $searchkey,
                );
                $arr  = array_merge($arr, $arr2);
                $htmlname = "manage/font/font_list.php";
        }
        $this->setTempData ($arr);
        $this->setTempPath($htmlname);//设置模板
    }
}