<style type="text/css">
<!--
/* 编辑银行卡信息 */
.edit_card_box {
    margin: -20px 0 0 -10px;
}
.edit_card_box .card_desc {
    background-color: #f5f8fa;
    border-radius: 4px;
    margin-top: 10px;
    padding-bottom: 10px;
}
.edit_card_box .card_desc .top {
    border-bottom: 1px solid #fff;
    margin: 0 20px;
}
.edit_card_box .card_desc .top h3 {
    height: 38px;
    line-height: 38px;
    border-bottom: 1px solid #d7dddd;
    font-size: 14px;
}
.edit_card_box .card_desc .desc {
    line-height: 26px;
    padding: 10px 20px;
    color: #dd7a69;
}
.edit_card_form {
    margin-top: 10px;
}
.edit_card_form ul li {
    padding: 5px;
    line-height: 30px;
    height: 30px;
    font-size: 14px;
}
.edit_card_form ul li em {
    width: 86px;
}
.edit_card_form ul li em.notices {
    width: auto;
    margin-left: 10px;
}

.edit_card_form .my_card {
    width: 263px;
}
.edit_card_form .card_num {
    width: 260px;
}
.edit_card_form .card_uname {
    width: 260px;
}
.my_cards {
    border: 1px solid #dedede;
    margin-top: 20px;
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
-->
</style>
<?php
//获取银行卡验证状态
$bank_card_validate_status_html = card::get_bank_status('html');;
?>
<div class="right_list edit_card_box">
    <div class="card_desc">
        <div class="top">
            <h3>提示您：</h3>
        </div>
        <div class="desc">
            修改银行卡卡号不会影响已申请提现的卡号信息
        </div>
    </div>
    <div class="edit_card_form">
        <form id="edit_card_form" method="post" action="?" >
    	<ul>
            <li>
                <input type="hidden" class="card_id" value="<?=$c_id?>" />
                <em>银行卡地区：</em> <?=$card_area?>
                </li>
            <li>
                <em>卡行：</em> <?=$c_bank_name?>
            </li>
            <li>
                <em>持卡人姓名：</em> <?=$c_card_uname?>
            </li>


            <li>
                <em>银行卡号：</em> <input type="text" class="input card_num" value="<?=$c_card_num?>" <?php if($c_is_validate==1){ ?>readonly<?php } ?> />
            </li>

            <li>
                <em>验证状态：</em>
                <em><?php echo $bank_card_validate_status_html[$c_is_validate];?></em>
            </li>
            <?php
                if($c_is_validate==0){
            ?>
                <li>
                    <a class="new_btn new_btn-info" href="javascript: void(0);" onclick="$(this).next().click();" target="_self">修改</a>
                    <input type="submit" value="修改" class="hide_btn" />
                </li>
            <?php
                }
                else if($c_is_validate==1 ){
            ?>
                <li>
                    <em>验证金额：</em> <input type="text" class="input validate_money"  />  <em class="notices">输入您的银行卡收到的金额，错误次数不能超过3次</em>
                </li>

                <a class="new_btn new_btn-info" href="javascript: void(0);" onclick="$(this).next().click();" target="_self">提交验证</a>
                <input type="submit" value="提交验证" class="hide_btn" />
            <?php } ?>
        </ul>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        //提交编辑银行卡信息
        var form = $('#edit_card_form');
        form.submit(function(e){
            e.preventDefault();
            var card_id = $.trim(form.find('.card_id').val()); //数据id
            var card_num = $.trim(form.find('.card_num').val()); //卡号
            var money = $.trim(form.find('.validate_money').val()); //验证金钱

            var bank_card = new RegExp("^[0-9]*$");//银行卡号一定是数字，正则替换

            if(!card_num) {
                msg('请输入您的卡号');
                return;
            }
            if(!bank_card.test(card_num)){
                msg('你的银行卡号有误，请输入阿拉伯数字（必须是半角的）');
                return;
            }
            var postData = {
                card_id: card_id,
                card_num: card_num,
                money:money
            };
            post('/?s=user&do=edit_card&json=true',postData,function(data) {
                if(data.id != '0043') {
                    if(money!=''){
                        data.msg +="，验证次数："+ data.info;
                    }
                    else if(data.info) {
                        data.msg += data.info;
                    }
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
        });
    });
</script>