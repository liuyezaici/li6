define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'adminlte'], function ($, undefined, Backend, Table, Form, Adminlte) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'addon/comment/index/index',
                    del_url: 'addon/comment/index/del',
					multi_url: "v1/comment/index/multi",
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                sortName: 'id',
                columns: [
                    [
                        {field: 'state', checkbox: true, },
                        {field: 'id', title: 'ID'},
                        {field: 'user_name', title: '用户名'},
                        {field: 'text', title: __('text')},
                        // {field: 'pictures', title: __('pictures'), formatter: Table.api.formatter.images},
                        {field: 'createtime', title:'评价时间', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), formatter: Controller.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table,
                            events: Table.api.events.operate
                            , buttons: [
                            {title: '评价详情', icon: 'fa fa-bars',
                                classname: 'btn btn-xs btn-primary btn-dialog',
                                box_top: '20px',
                                box_width: '1000px',
                                url: 'addon/comment/index/details'
                            }
                        ],
                            formatter: Table.api.formatter.operate
                        }
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
                status: function (value, row, index) {
                    return "<a href='javascript:;' class='btn btn-" + (value ? "info" : "default") + " btn-xs btn-change' data-id='"
                            + row.id + "' data-params='status=" + (value ? 0 : 1) + "'>" + (value ? __('Normal') : __('Hidden')) + "</a>";
                },
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