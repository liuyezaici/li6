<?php
//编辑我的地址
case 'modify_address':
    $addressid = !empty($this->options['addressid']) ? intval($this->options['addressid']) : 0;
    $modify = !empty($this->options['modify']) ? trim($this->options['modify']) : 'add';//add edit del
    if($modify == 'edit' || $modify=='del') {
    if(!$addressid) {
    print_r(message::getMessage('0065'));//缺少数据id
    exit;
    }
    }
    $cardClass = new card();
    $all_province = $cardClass->getAllProvince($db);
    $provinceHtml= '';
    foreach($all_province as $n => $v) {
    $provinceHtml .= "<option value='". $v['a_id'] ."'>". $v['a_name'] ."</option>";
    }
    if($modify=='add') {//添加地址
    $arr['modify'] = 'add';
    $arr['s_id'] = 0;
    $arr['provinceHtml'] = $provinceHtml;
    $arr['s_username'] = '';
    $arr['s_province'] = 0;
    $arr['s_city'] = 0;
    $arr['s_address'] = '';
    $arr['s_phone'] = '';
    $arr['s_ismain'] = 0;
    $arr['btn_text'] = '保存';
    }
    elseif($modify=='edit') {//编辑地址
    $addressInfo = DbBase::getRowBy("c_user_address", "s_id,s_username,s_province,s_city,s_address,s_phone,s_ismain", "s_id='". $addressid ."' AND s_uid='". $userid ."'");
    if(!$addressInfo) {
    print_r(message::getMessage('0049')); //数据不存在
    exit;
    }
    $all_province = $cardClass->getAllProvince($db);
    $provinceHtml= '';
    foreach($all_province as $n => $v) {
    $css_ = '';
    if($v['a_id'] == $addressInfo['s_province']) $css_ = ' selected';
    $provinceHtml .= "<option value='". $v['a_id'] ."'". $css_ .">". $v['a_name'] ."</option>";
    }
    $arr = $addressInfo;
    $arr['modify'] = 'edit';
    $arr['provinceHtml'] = $provinceHtml;
    $arr['btn_text'] = '修改';
    }
    $htmlname = 'manage/buyer/f_modify_address';
    break;
?>