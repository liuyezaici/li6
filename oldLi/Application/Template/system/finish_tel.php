<style>
    .finish_tel_body .tmp_title {
        margin: 50px 30px;
        height: 50px;
        border-bottom: 1px solid #d8d8d8;
        font-size: 26px;
        position: relative;
    }
    .finish_tel_body .tmp_title .other {
        font-size: 12px;
        color: #666;
        position: absolute;
        right: 10px;
        bottom: 5px;
    }
    .finish_tel_body #code_image {
        display: inline-block;
        cursor: pointer;
        vertical-align: middle;
        width: 77px;
        height: 29px;
        border: 1px solid #aaa;
        border-right-width: 0;
        box-sizing: border-box;
    }
    /* 主体的css */
    #reg_form ul li .submit_btn {
        margin-left: 108px;
        width: 201px;
        padding: 8px 0;
    }
</style>
<div class="finish_tel_body">
    <div class="tmp_title">
        最后一步：完善手机
    </div>
    <form method="post"  class="front_form" id="finish_form">
        <input type="hidden" value="<?=$userHash?>" id="u_nick_hash" />
        <ul>
            <li>
                <em>手机：</em>
                <span id="make_tel_box" data-value="<?=$leftTime?>"></span>
            </li>
            <li>
                <em>短信验证码：</em>
                <span id="make_sms_box"></span>
            </li>
            <li>
                <em> </em>
                <input type="submit" value="完善手机" class="btn btn-info submit_btn" />
            </li>
            <li style="color: #888; padding-top: 20px;line-height: 20px;">
                如果您的手机已经被其他帐号使用，则需进行以下操作：<br/>
                用手机登录，然后在“控制面板”里点击“绑定QQ登录” <br/>
            </li>
        </ul>
    </form>
</div>
<script src="/resource/system/js/finish_tel.js"></script>