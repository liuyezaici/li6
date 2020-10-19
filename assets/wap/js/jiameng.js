(function() {
    var submitUrl = '/adppp/jiameng/api/index/submitJiameng/' ;
    contentObj.appendHeader('');
    //渲染数据
    var postDa = {
    };
    postDa[parent.wapCfg.tokenKey] = parent.userToken;
    var bodyObj = makeForm({
        'style': 'border: 0.2rem solid #f1f1f1;',
        'type': 'post',
        url: submitUrl,
        value:  [
            makeTable({
                'style': 'border-radius: 10px;background-color: #fff; ',
                tr_1: [
                    {
                    td: [
                        {
                            'padding': '0.2rem 0 0.2rem 0.2rem',
                            value: makeSpan({
                                value: '合作意向',
                            })
                        }
                    ]
                },
                {
                    td: [
                        {
                            'padding': '0.2rem 0.2rem 0.2rem 0.2rem',
                            value: makeEditor({
                                name: 'content',
                                place: '请描述意向、优势，并留下联系方式，谢谢!',
                                height: '4rem',
                                type: 'text',
                                value: ''
                            })
                        }
                    ]
                },
                {
                    td: [
                        {
                            'padding': '0.2rem 0 0.2rem 0.2rem',
                            value: [makeSpan({
                                value: '联系人',
                            }),makeInput({
                                value: '',
                                name: 'username',
                                margin_left: '0.2rem',
                                place: '请输入',
                            })]
                        }
                    ]
                },  {
                    td: [
                        {
                            'padding': '0.2rem 0 0.2rem 0.2rem',
                            value: [makeSpan({
                                value: '联系电话',
                            }),makeInput({
                                value: '',
                                name: 'tel',
                                margin_left: '0.2rem',
                                place: '请输入',
                            })]
                        }
                    ]
                }]
            }),
            makeBtn({
                value: '提交',
                type: 'submit',
                margin_top: '0.2rem',
                'class': 'btn btn-lg btn-block btn-info no_radius'
            })],
        submit: function (obj, ev) {//提交时回调
            // console.log(obj);
            // console.log(ev);
            // ev.preventDefault();
            // console.log('on submit');
            // return false;// 可以让表单停止提交 但要记得 ev.preventDefault();
        },
        success_key: 'code',
        success_value: '1',
        success_func: function (data) {
            hideNewBox();
            msgTis(data.msg);
            bodyObj.after(makeDiv({
                'class': 'well',
                value: '谢谢您的参与,我们会尽快回复您.'
            })).remove();
        },
        err_func: function (data) {
            msgTis(data.msg);
        }
    });
    contentObj.appendBody(bodyObj);
    contentObj.addBody('');
    document.title = '商务合作';
});