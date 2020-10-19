<?php
//评论的模块类。
class mod_comment extends page
{
    function __construct( $options ='', $checkuser = false )
    {
        parent::__construct( $options,$checkuser );
        $this->name = 'help';
    }

    //处理相关功能
    function doAction()
    {

    }


    //得到显示页数据
    function getData()
    {
        $cm = new comment();
        $show = $this->getOption('show');
        switch ($show) {
            // 评论列表
            case 'list_comment':
                $page = $this->getOption('page', 1, 'int'); //页码
                $a_id = !empty($this->options['a_id'])?$this->options['a_id']:0; //活动ID
                $fid = !empty($this->options['fid'])?$this->options['fid']:0; //评论类型id 1评论促销商品 2评论免费试用 3评论试用报告
                //判断是否有参数
                if( !$a_id ){
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }
                $wh_ = "c_fid = '". $fid ."' AND c_sid = '". $a_id ."' ";
                $fields = 'c_id,c_sid,c_ctime,c_content,c_uid';//,u_id,u_nick
                $sql = "SELECT ". $fields." FROM c_comment WHERE ". $wh_ ."  ORDER BY c_id DESC ";
                $pag = new Divpage( $sql,'', '*',$page , $pagesize = 10,$menustyle = 9  );
                $pag->getDivPage();
                $listResult = $pag->getPage();
                $pageMenu = $pag->getMenu();
                foreach ($listResult as $n=>&$v) {
                    //获取会员信息
                    $userInfo = cachemysqltable::get($v['c_uid']);
                    $v['u_id'] = $v['c_uid'];
                    $v['u_nick'] = $userInfo['u_nick'];

                    $listResult[$n]['u_nick'] = Str::substr($v['u_nick'],1)."**";
                    $listResult[$n]['face'] = $GLOBALS['cfg_user_face_path']."/".$v['c_uid'].".jpg";
                }
                $arr = array(
                    'a_id' => $a_id,
                    'fid' => $fid,
                    'list_result' => $listResult,
                    'pageMenu' => $pageMenu
                );
                $htmlname = 'system/comment/part_list_comment';
                break;
            //评论列表
            case 'reply':
                $fid = $this->getOption('fid', 0, 'int'); //评论类型id
                $sid = $this->getOption('sid', 0, 'int'); //评论数据id
                $page = $this->getOption('page', 1, 'int'); //页数
                //id参数是否为空
                if( !$fid || !$sid ){
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }
                $db = mysql::getInstance();
                $cm = new comment();
                $comment_type = $cm -> getTypeNameById($fid);
                //$comment_title = $cm -> getCommentTitle($fid, $sid);
                $comment_title = '';
                //标题是否被空
                if( !$comment_type ){
                    print_r(Message::getMsgJson('0023'));//缺少必填的信息，请重试
                    exit;
                }
                $mytime = Timer::now();
                if(strlen($comment_title) > 30) $comment_title = Str::substr($comment_title,30)."...";
                $model = "/comment-". $fid ."-". $sid ."-{%u}.html";
                $action_url = $cm -> getCommentLink($fid,$sid);
                //获取全站最新的20挑评论
                $sql = "select c_id,c_uid,c_sid,c_fid,c_ctime,c_content,u_id,u_nick FROM v_comment order by c_id desc limit 20";
                $db->Query($sql); //执行sql语句
                $more_reply =  $db->getAllRecodesEx(\PDO::FETCH_ASSOC);
                foreach ($more_reply as $n=>$v) {
                    $more_reply[$n]['u_nick'] = Str::substr($v['u_nick'],1)."**";
                    $more_reply[$n]['c_content'] = Str::substr($v['c_content'],10);
                    $more_reply[$n]['time'] = Func::get_lasttime($v['c_ctime'],$mytime);
                    $more_reply[$n]['face'] = $GLOBALS['cfg_user_face_path']."/".$v['u_id'].".jpg";
                }
                //获取评论列表
                $wh_ = "c_fid = '". $fid ."' and c_sid = '". $sid ."'";
                $fildes = 'c_id,c_uid,c_sid,c_fid,c_ctime,c_content,u_id,u_nick';
                $sql = "SELECT {$fildes} FROM v_comment where ". $wh_ ."  ORDER BY c_id DESC";
                //return($sql);
                $ac = new Divpage( $sql, $model, $fildes , $page, $pagesize = 10, $menustyle = 1);
                $ac->getDivPage();
                $listResult = $ac->getPage();
                $pageMenu = $ac->getMenu();
                foreach ($listResult as $n=>$v) {
                    $listResult[$n]['u_nick'] = Str::substr($v['u_nick'],1)."**";
                    $listResult[$n]['face'] = $GLOBALS['cfg_user_face_path']."/".$v['u_id'].".jpg";
                }
                $arr['comment_type'] = $comment_type;
                $arr['comment_title'] = $comment_title;
                $arr['list_result'] = $listResult;
                $arr['more_reply'] = $more_reply;
                $arr['page_menu'] = $pageMenu;
                $arr['fid'] = $fid;
                $arr['sid'] = $sid;
                $arr['action_url'] = $action_url ;
                $header = file::readFile(\Config::get('router.sysPathes.tempPath').'/front/header.php');
                $arr['header'] = str_replace(' class="active"','',$header);
                $arr['footer'] = file::readFile(\Config::get('router.sysPathes.tempPath').'/front/footer.php');
                $htmlname = 'system/comment/comment';
                break;
        }
        $this->setTempData ($arr);
        $this->setTempPath($htmlname);//设置模板
    }
}