<style type="text/css">
<!--
.all_my_card {
    margin: -20px 0 0 -10px;
}
.my_cards {
    border: 1px solid #dedede;
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
<div class="right_list all_my_card">
    <div class="my_cards">
        <?php
        if($mycards) {
        ?>
        <h3>我绑定的银行卡</h3>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr class="top_tr">
                <td width="10"></td>
                <td>银行类型</td>
                <td>卡号</td>
                <td>银行卡姓名</td>
                <td>选择银行卡</td>
            </tr>
            <?php
            foreach($mycards as $n => $v) {
                ?>
                <tr>
                    <td></td>
                    <td><?=$v['c_bank_name']?></td>
                    <td><?=$v['c_card_num']?></td>
                    <td><?=$v['c_card_uname']?></td>
                    <td>
                        <?php if($v['c_is_validate']==2){?>
                        <a href="javascript: void(0);" title="提现到此卡" onclick="getThisCard('<?=$v['c_bank_name']?>','<?=$v['c_card_num']?>','<?=$v['c_card_uname']?>',<?=$v['c_id']?>);" target="_self"
                           style="color: #0000ff;">提现到此卡</a>
                        <?php }
                        else{
                            echo '<a href="http://www.fanlishiyong.com/help-read-176.html" title="此卡未验证或者没有通过验证，查看帮助"><span class="red">此卡未验证或者没有通过验证，查看帮助</span> </a> ';
                        }
                        ?>
                    </td>
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
    //选择银行卡
    function getThisCard(bankName,cardNum, cardUname, cardId) {

        //如果银行手续费为空，去获取
            //
            var feeData = {
                'card_id':cardId
            }
            post("/?s=user&do=search_bank_card_fee&json=true", feeData, function(data){

                $('#tixian_type_table').find('.yinlian').find('.desc').html("银行："+bankName+"<br />卡号："+cardNum+"<br />开户名："+cardUname);
                $('#submit_tixian_form').find('#tixian_card').val(cardId);
                $('#submit_tixian_form').find('#bank_card_fee').val(data.info);
                hideNewBox();
            });


    }
</script>