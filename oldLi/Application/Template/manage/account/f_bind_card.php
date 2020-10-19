<style type="text/css">
<!--
.bind_card_box {
    margin: -20px 0 0 -10px;
}
.bind_card_box .card_desc {
    background-color: #f5f8fa;
    border-radius: 4px;
    margin-top: 10px;
    padding-bottom: 10px;
}
.bind_card_box .card_desc .top {
    border-bottom: 1px solid #fff;
    margin: 0 20px;
}
.bind_card_box .card_desc .top h3 {
    height: 38px;
    line-height: 38px;
    border-bottom: 1px solid #d7dddd;
    font-size: 14px;
}
.bind_card_box .card_desc .desc {
    line-height: 26px;
    padding: 10px 20px;
    color: #dd7a69;
}
.bind_card_form {
    margin-top: 10px;
}
.bind_card_form ul li {
    padding: 5px;
    line-height: 30px;
    height: 30px;
    font-size: 14px;
}
.bind_card_form ul li em {
    width: 86px;
}
.bind_card_form .my_card {
    width: 263px;
}
.bind_card_form .card_num {
    width: 260px;
}
.bind_card_form .card_uname {
    width: 260px;
}
.my_cards {
    border: 1px solid #dedede;
    margin-top: 20px;
    position: relative;
}
.my_cards h3 {
    height: 28px;
    line-height: 28px;
    padding-left: 10px;
    color: #cd2400;
    font-size: 14px;
    background-color: #f1f1ff;
    border-bottom: 1px solid #dedede;
}
.my_cards .top_tr {
    background-color: #f8fdff;
    height: 26px;
    color: #666;
    font-weight: bold;
}
.my_cards .nocard {
    background-color: #ffe;
    padding: 10px;
}

.explain_bank_card_status{
    position: absolute;
    right: 10px;
    top:-30px
}
.explain_bank_card_status a{
    color: red;
}
-->
</style>
<div class="right_list bind_card_box">
    <div class="card_desc">
        <div class="top">
            <h3>银行卡绑定需知：</h3>
        </div>
        <div class="desc">
            1、一个会员可绑定多张银行卡；<br />
            2、修改银行卡卡号不会影响已申请提现的卡号信息 ；<br />
            3、一张银行卡在平台只能被绑定1次；<br />
            4、银行卡卡号必须是纯半角的阿拉伯数字，不能包涵英文、中文、空格或者其他字符，不然会导致提现不成功；<br />
            5、银行卡持卡人姓名和银行卡卡号必须对应一致；<br />
            6、银行卡持卡人姓名绑定需要与网站的真实姓名一致。
        </div>
    </div>
    <div id="bind_card_form" class="bind_card_form">
    	<ul>
            <li>
                <em>选择地区：</em>
                <select class="input my_province" style="width: 90px;" onchange="getProvinceCity($(this).val())">
                    <option value="0">选择地区</option>
                    <?php
                    foreach ($all_province as $n => $v) {
                        echo "<option value='". $v['a_id'] ."'>". $v['a_name'] ."</option>";
                    }
                    ?>
                </select>
                <span id="province_city"></span>
                <span id="city_area"></span>
                </li>
            <li>
                <em>卡类型：</em>
                <select class="input my_card_type">
                     <option value="0">选择平台支持的银行卡类型</option>
                     <?php
                     foreach ($all_card as $n => $v) {
                         echo "<option value='". $v['bank_id'] ."'>". $v['bank_name'] ."</option>";
                     }
                     ?>
                </select>
            </li>
            <li>
                <em>持卡人姓名：</em> <?=$userinfo['u_name']?>
            </li>
            <li>
                <em>银行卡号：</em> <input type="text" class="input card_num" />
            </li>
            <li>
                <a class="new_btn new_btn-info" onclick="addCard();" >添加银行卡</a>
            </li>
        </ul>
    </div>
    <div class="my_cards">
        <div class="explain_bank_card_status"><a href="http://www.fanlishiyong.com/help-read-176.html">什么是验证状态？</a></div>
        <?php

        //获取银行卡验证状态
        $bank_card_validate_status_html = card::get_bank_status('html');;

        if($mycards) {

        ?>
        <h3>我绑定的银行卡</h3>
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="list_table">
            <tr class="top_tr">
                <td width="10"></td>
                <td>银行类型</td>
                <td>卡号</td>
                <td>银行卡姓名</td>
                <td>绑定时间</td>
                <td>验证状态</td>
                <?php
                if( $v['c_is_validate']==0 || $v['c_is_validate']==1 ){
                ?>
                <td>操作</td>
                <?php } ?>
            </tr>
            <?php
            foreach($mycards as $n => $v) {
                ?>
                <tr>
                    <td></td>
                    <td><?=$v['c_bank_name']?></td>
                    <td><?=$v['c_card_num']?></td>
                    <td><?=$v['c_card_uname']?></td>
                    <td><?=$v['c_addtime']?> </td>
                    <td><?=$bank_card_validate_status_html[$v['c_is_validate']]?> </td>
                    <?php
                    if($v['c_is_validate']==0 ){
                    ?>
                        <td><a href="javascript: void(0);" onclick="editCard(<?=$v['c_id']?>);" target="_self">编辑</a></td>
                    <?php
                    }
                    else if($v['c_is_validate']==1 ){
                    ?>
                        <td><a href="javascript: void(0);" onclick="editCard(<?=$v['c_id']?>);" target="_self">提交验证</a></td>
                    <?php
                    }
                    else if($v['c_is_validate']==-2 ){
                    ?>
                    <td width="100"><?php echo Func::substr($v['c_err_validate_bank_reason'],100);?></td>
                    <?php } ?>

                </tr>
            <?php
            }
            ?>
        </table>
        <?php
        } else {
            echo "
            <div class='nocard'>您没有绑定银行卡</div>";
        }
        ?>
    </div>
</div>

<script type="text/javascript">
    //编辑银行卡信息
    function editCard(cardId) {
        msgWin('编辑银行卡信息','?m=user/form&form=edit_card&card_id='+cardId, 800, 200,
            "<a target='_self' class='btn' href=\"javascript:hideNewBox();editCard('"+ cardId +"');\" >刷新</a> ");
    }
    //添加银行卡
    function addCard() {
        var form = $('#bind_card_form');
        var area_id1 = form.find('.my_province').val(); //地区id
        var area_id2 = form.find('.my_city').val(); //地区id
        var my_card_type = form.find('.my_card_type').val(); //卡类型
        var card_num = $.trim(form.find('.card_num').val()); //卡号
        //var card_uname = $.trim(form.find('.card_uname').val()); //持卡人姓名

        var bank_card = new RegExp("^[0-9]*$");//银行卡号一定是数字，正则替换

        if(!area_id1 || area_id1 == 0) {
            msg('请选择您的省份');
            return;
        }
        if(!area_id2 || area_id2 == 0) {
            msg('请选择您的市/区');
            return;
        }
        if(!my_card_type || my_card_type == 0) {
            msg('请选择您的卡类型');
            return;
        }
        if(!card_num) {
            msg('请输入您的卡号');
            return;
        }
        if(!bank_card.test(card_num)){
            msg('你的银行卡号有误，请输入阿拉伯数字（必须是半角的）');
            return;
        }
        /*if(!card_uname) {
            msg('请输入您的银行卡开户人姓名');
            return;
        }*/
        var postData = {
            area_id1: area_id1, //省份id
            area_id2: area_id2, //市区id
            my_card_type: my_card_type,
            card_num: card_num
            //card_uname: encodeURIComponent(card_uname)
        };
        post('/?s=account&do=add_card&json=true',postData,function(data) {
            if(data.id != '0113') {
                if(data.info) data.msg += data.info;
                msg(data.msg,4);
                if(data.id == '0004') {
                    msg(data.msg,1);
                    loginIn();
                    return;
                }
            } else {
                openMenu(4);
                msgTis(data.msg);
                return;
            }
        });
    }
    //获取省份的市/区
    function getProvinceCity(areaId) {
        var form = $('#bind_card_form');
        if(!areaId || areaId == 0) {
            form.find('#province_city').html('');
            return;
        }
        post("/?s=system/get_city&json=true",{
            area_id : areaId
        },function(data){
            if(data.length==1) {
                var allCity = '<option value="'+ data[0]['a_id'] +'">'+ data[0]['a_name'] +'</option>';
                //getCityArea(data[0]['a_id']);
            } else if(data.length > 1){
                var allCity = '';
                for( var i in data ) {
                    allCity += '<option value="'+ data[i]['a_id'] +'">'+ data[i]['a_name'] +'</option>';
                }
            } else {
                msg('没有数据 请重试', 1);
                return;
            }
            form.find('#province_city').html('<select class="input my_city" style="width: 100px;">'+allCity+'</select>');// onchange="getCityArea($(this).val())"
            form.find('#city_area').html('');
        });
    }
    //获取市的所有地区
    function getCityArea(areaId) {
        var form = $('#bind_card_form');
        post("/?s=system/get_city_area&json=true",{
            area_id : areaId
        },function(data){
            if(data.length==1) {
                var a_id = data[0]['aid'];
                var allArea = '<option value="'+ data[0]['a_id'] +'">'+ data[0]['a_name'] +'</option>';
            } else if(data.length > 1){
                var allArea = '';
                for( var i in data ) {
                    allArea += '<option value="'+ data[i]['a_id'] +'">'+ data[i]['a_name'] +'</option>';
                }
            } else {
                return;
            }
            form.find('#city_area').html('<select id="my_area" class="input" style="width: 100px;">'+allArea+'</select>')
        });
    }

    function submitEidt() {
        var form = $('#bind_card_form');
        var oldZfb =  $.trim(form.find('#old_zfb').html());
        var zfb1 = $.trim(form.find('#zfb1').val());
        var zfb2 = $.trim(form.find('#zfb2').val());
        if(oldZfb.length > 0) {
            if (zfb1 || zfb2 ){
                msg('您已经绑定支付宝! 如需解绑请联系客服.', 4);
                return false;
            }
        }
        if (zfb1 == '' || zfb2 == ''){
            msg('请填写支付宝', 4);
            return false;
        }
        if (zfb1 != zfb2 ){
            msg('两次输入的支付宝不一致，请重新输入', 4);
            return false;
        }
        if (!confirm('支付宝绑定后将无法修改，支付宝是本站唯一支付途径，您确认已经核对无误吗？')){
            return false;
        }
        post("/?s=user&do=post_zfb&json=true",{
            zfb:zfb1
        },function(data){
            if(data.id != '0004') {
                if(data.info) data.msg += data.info;
                msg(data.msg, 4);
                if(data.id == '0000') {
                    loginIn();
                    return;
                }
            } else {
                hideAllbox();
                msg(data.msg, 1);
                return;
            }
        });
    }
</script>