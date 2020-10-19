define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'adminlte'], function ($, undefined, Backend, Table, Form, Adminlte) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'addon/news/index/index',
                    add_url: 'addon/news/index/add',
                    edit_url: 'addon/news/index/edit',
                    del_url: 'addon/news/index/del',
                }
            });

			var table = $("#table");
			var tableColumns = [
				[
					{field: 'state', checkbox: true, },
					{field: 'id', title: 'ID'},
					{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
				]
			];
			
			if(typeof window.Config.addonCustomTable != 'undefined' && typeof window.Config.addonCustomTable.news != 'undefined'){
				for(var i in window.Config.addonCustomTable.news){
					(function(addonCustomItem){
						if(addonCustomItem.showlist == 1 || addonCustomItem.showsearch == 1){
							var columnsLength = tableColumns[0].length;
							var columnsIndex = columnsLength > 0  ? -1 : 0;
							if(tableColumns[0][columnsLength - 1].field != 'operate')columnsIndex = columnsLength;
							tableColumns[0].splice(columnsIndex, 0, 
								{
									field: addonCustomItem.name, 
									title: addonCustomItem.title, 
									searchable: addonCustomItem.showsearch == 1 ? true : false,
									visible: addonCustomItem.showlist == 1 ? true : false,
									data: addonCustomItem.showsearch == 1 ? addonCustomItem.extend : null,
									operate:  addonCustomItem.showsearch == 1 ? (function(addonCustomItem){
										if(addonCustomItem.type == 'select' || addonCustomItem.type == 'selects'){
											return 'FIND_IN_SET';
										}else{
											return 'LIKE %...%';
										}
									})(addonCustomItem) : false,
									searchList: addonCustomItem.showsearch == 1 ? (function(addonCustomItem){
										if(addonCustomItem.type == 'checkbox' || addonCustomItem.type == 'radio' || addonCustomItem.type == 'select' || addonCustomItem.type == 'selects'){
											return addonCustomItem.content;
										}else{
											return null;
										}
									})(addonCustomItem) : null,
									formatter: function(value, row, index){
										if(addonCustomItem.type == 'image'){
											return Table.api.formatter.image.call(this, value, row, index);
										}else if(addonCustomItem.type == 'images'){
											return Table.api.formatter.images.call(this, value, row, index);
										}else if(addonCustomItem.type == 'checkbox' || addonCustomItem.type == 'selects'){
											var values = $.isArray(value) ? value : value.split(',');
											var temp = '';
											for(var j in values){
												if(values[j]){
													if(temp != '')temp += ', ';
													temp += addonCustomItem.content[values[j]];
												}
											}
											return temp;
										}else if(addonCustomItem.type == 'radio' || addonCustomItem.type == 'select'){
											return addonCustomItem.content[value];
										}else{
											return value;
										}
									}
								}
							);	
						}
					})(window.Config.addonCustomTable.news[i]);
				}
			}
			
			// 初始化表格
			table.bootstrapTable({
				url: $.fn.bootstrapTable.defaults.extend.index_url,
				sortName: 'id',
				columns: tableColumns,
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