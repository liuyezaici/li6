define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'adminlte'], function ($, undefined, Backend, Table, Form, Adminlte) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'addon/userinvite/index/index',
                    add_url: 'addon/userinvite/index/add',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                sortName: 'id',
                columns: [
                    [
                        {field: 'id', title: 'ID'},
                        {field: 'keyname', title: '邀请秘钥'},
                        {field: 'main_username', title: '主邀请人'},
                        {field: 'ctime', title: '发起时间', formatter: Table.api.formatter.datetime},
                        {field: 'successnum', title: '成功注册数量'}
                    ]
                ]
            });


            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        add: function () {
            Form.api.bindevent($("form[role=form]"), function (data) {
                Fast.api.close(data);
            });
            Controller.api.bindevent();
        },
        edit: function () {
            Form.api.bindevent($("form[role=form]"));
            Controller.api.bindevent();
        },

        api: {
            formatter: {
                prevImg:function(value){
                    return "<a href='javascript:;'><img onclick=\"layer.open({content:'<img src="+ value +" width=100% />'});\" src='"+ value +"' width='60px'/></a>";
                }
            },
            bindevent: function () {
                var getAppFileds = function (app) {
                    var app = apps[app];
                    var appConfig = app['config'];
                    var str = '';
                    for (i in appConfig) {
                        var options = appConfig[i]['options'];
                        options = options.split(',');
                        var option_str = '';
                        if (appConfig[i]['type'] == 'select') {
                            for (o in options) {
                                var option = options[o];
                                var item = option.split(':');
                                option_str += '<option value="' + item[0] + '">' + item[1] + '</option>';
                            }
                            option_str = '<select class="form-control" name="row[content][' + appConfig[i]['field'] + ']">' + option_str + '</select>';
                        } else if (appConfig[i]['type'] == 'checkbox') {
                            for (o in options) {
                                var option = options[o];
                                var item = option.split(':');
                                option_str += '<input type="checkbox" name="row[content][' + appConfig[i]['field'] + '][]" value="' + item[0] + '"> <label>' + item[1] + '</label> ';
                            }

                        } else if (appConfig[i]['type'] == 'radio') {
                            for (o in options) {
                                var option = options[o];
                                var item = option.split(':');
                                option_str += '<input type="radio" name="row[content][' + appConfig[i]['field'] + ']" value="' + item[0] + '"> <label>' + item[1] + '</label> ';
                            }
                        }
                        str += '<div class="form-group"><label for="content" class="control-label col-xs-12 col-sm-2">' + appConfig[i]['caption'] + ':</label><div class="col-xs-12 col-sm-8">' + option_str + ' </div> </div>';
                    }
                    return str;
                };
            }
        }
    };
    return Controller;
});