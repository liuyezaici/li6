<?php
//第三方登录模块类。
class mod_qqlogin extends page
{
    function __construct( $options ='')
    {
        parent::__construct();
        $this->name = '';
    }
    //处理相关功能
    function doAction()
    {

    }
    //得到显示页数据
    function getData()
    {
        error_reporting(E_ALL);
        //WAP端时 跨域名导致身份不一致必须将当前身份导入
        $args = !empty($this->options['args'])? $this->options['args'] : array(); // /qqlogin/uhash
        $uhash = trim(isset($args[1]) ? $args[1] : '');
        $userClass = new Users($uhash);
        $db = mysql::getInstance();
        //qqConnectAPI内部定义了session_start 所以users类必须要定义在前面
        require_once("/include/lib/qqconnect/qqConnectAPI.php");
        $userUrl = $_SERVER['SERVER_NAME'];
        if(strstr($userUrl,'wap')) { //如果使用的是wap域名 必须强制跳转到www 否则生成的qq openid会不一致;
            header('location: '. $GLOBALS['cfg_basehost'] .'/qqlogin');
            exit;
        }
        //先获取来路
        $fromUrl = !empty( $_SESSION['old_url']) ? $_SESSION['old_url'] : '/' ;
        //如果已经登录，直接绑定到QQ登录表
        $userid = $userClass->getUserAttrib('userId');
        $qc = new QC();
        $mytime = Timer::now();
        $inviter = isset($_GET['inviter']) ? intval($_GET['inviter']) : '';
        if($inviter > 0) {
            @session_start();
            $_SESSION['inviter'] = $inviter;
        }
        // 如果是登录后返回:
        $back = $_GET;
        if($back) {
            $code = isset($_GET['code']) ? trim($_GET['code']) : '';
            if($code) {
                $token = $qc->qq_callback();
                $openid = $qc->get_openid();
                if($openid) {
                    $qc = new QC($token, $openid);
                    $json = $qc->get_user_info();
                    $username = $json['nickname'];
                    $face_zone = $json['figureurl_2'];
                    $face_qq = $json['figureurl_qq_2'];
                    if(!$face_qq) $face_qq = $face_zone;
                    if($userid > 0) { //当身份已经存在，
                        $uqq = DbBase::getRowBy("c_user_qqlogin", "q_uid", "q_openid = '". $openid ."'");
                        if(isset($uqq['q_uid'])) {
                            if($uqq['q_uid'] != $userid) {
                                $old_userid = $uqq['q_uid'];
                                $userInfo = $userClass->getUserInfo($old_userid, "u_nick");
                                Message::Show('您的QQ已经被帐号【'. $userInfo['u_nick'] .'】绑定。<br/> 请先在首页用QQ快捷登录，然后解除绑定。');
                                exit;
                            } else {
                                header('location: '.$fromUrl);
                                /* //如果有来路 并且来路不是登录页面 才跳转
                                 if($fromUrl && !strstr($fromUrl,'login') && !strstr($fromUrl,'reg')) {
                                     header('location: '.$fromUrl);
                                 } else {
                                     if($userType == 3) {
                                         header('location: /?s=employee');
                                     } else {
                                         header('location: /?s=user');
                                     }
                                 }*/
                            }
                        } else {
                            //qq绑定入库
                            $newQQData = array(
                                'q_usernick' => $username,
                                'q_openid' => $openid,
                                'q_uid' => $userid,
                                'q_qq' => 0,
                                'q_faceurl' => $face_qq,
                                'q_jointime' => Timer::now(),
                                'q_token' => $token
                            );
                            if(!DbBase::insertRows('c_user_qqlogin',$newQQData)){
                                echo 'insert [c_user_qqlogin1] err!';
                                exit;
                            };
                            header('location: '.$fromUrl);
                        }
                    } else {
                        //游客使用QQ登录，先判断是否绑定过主帐号，如果有就直接登录
                        $uqq = DbBase::getRowBy("c_user_qqlogin", "q_uid", "q_openid = '". $openid ."'");
                        if($uqq) {
                            if( $uqq['q_uid'] > 0) {
                                $my_userid = $uqq['q_uid'];
                                //更新token
                                $editQQData = array(
                                    'q_faceurl' => $face_qq,
                                    'q_token' => $token
                                );
                                DbBase::updateByData('c_user_qqlogin', $openid, $editQQData, $flag='q_openid');
                                //系统帮会员自动登录
                                $uinfo = $userClass->getUserInfo($my_userid, 'u_nick,u_pwd,u_tel');
                                $nick = $uinfo['u_nick'];
                                $pwd = $uinfo['u_pwd'];
                                $u_tel = $uinfo['u_tel'];
                                $u_type = $uinfo['u_type'];
                                $res = $userClass->checkUser($nick, $pwd, $isAdmin = false, $systemLogin = true);
                                if ( $res == '0001' ) {
                                    if(!$u_tel) {
                                        //必须绑定手机
                                        header('location:/system/finish_tel');
                                        exit;
                                    }
                                    header('location: '.$fromUrl);
                                    exit;
                                } else {
                                    Message::show(Message::getMessage($res)."uid:".$my_userid, '/');
                                }
                            } else {//未绑定帐号的QQ 直接解绑 无须删除数据
                                DbBase::updateByData("c_user_qqlogin", $openid,
                                    array('q_uid'=> 0,'q_token' => date('YmdHis',time()).Str::getRam(12), 'q_openid'=> date('YmdHis',time()).Str::getRam(12)),
                                    "q_openid");
                                return;
                            }
                        } else {
                            //没绑定过QQ
                            //手动注册
                            $u_nick = Users::createUnick($db);
                            $pwd = Str::getMD5('sss_'.$u_nick);
                            $userData = array(
                                'u_nick' => $u_nick,
                                'u_pwd' => $pwd,
                                'u_regtime' => $mytime,
                                'u_inviter' => $inviter,
                                'u_ip' => Ip::getIp(),
                            );
                            $userClass = new Users();
                            $newUid = $userClass->createUser($userData, $inviter, $mytime);
                            //直接登录
                            $userClass->checkUser($u_nick, $pwd, false, true);
                            //qq绑定入库
                            $newQQData = array(
                                'q_usernick' => $username,
                                'q_openid' => $openid,
                                'q_uid' => $newUid,
                                'q_qq' => 0,
                                'q_faceurl' => $face_qq,
                                'q_jointime' => Timer::now(),
                                'q_token' => $token
                            );
                            if(!DbBase::insertRows('c_user_qqlogin',$newQQData)){
                                echo 'insert [c_user_qqlogin1] err!';
                                exit;
                            };
                            //必须绑定手机
                            header('location:/system/finish_tel');
                            exit;
                        }
                    }
                }
                exit;
            }
        }
        $qc->qq_login();
        exit;
    }
}