<?php
return [
		//news表
		'news' => [
					[
						"SUCCESS" => 1,//固定值
						"name" => "title",//字段
						"title" => "标题",//标题
						"type" => "string",//类型
						"value" => "",//默认值
						"content" => "key1|val1\r\nkey2|val2",//select/checkbox/radio列表值
						"tip" => "",//提示
						"rule" => "",//验证规则//正则
						"extend" => "",//扩展内容 data-source="****"
						"showlist" => 1,//列表显示
						"showsearch" => 1,//搜索显示
						"key" => '',//索引,多个字段英文逗号分隔//没有索引留空
						"unique" => '',//约束,多个字段英文逗号分隔//没有约束留空
						"lock" => 1,//1锁定不允许修改
					],
					[
						"SUCCESS" => 1,
						"name" => "text",
						"title" => "内容",
						"type" => "editor",
						"value" => "",
						"content" => "",
						"tip" => "",
						"rule" => "",
						"extend" => "",
						"showlist" => 0,
						"showsearch" => 0,
						"lock" => 1,
					],
					[
						"SUCCESS" => 1,
						"name" => "thumb",
						"title" => "新闻小图",
						"type" => "image",
						"value" => "",
						"content" => "",
						"tip" => "",
						"rule" => "",
						"extend" => "",
						"showlist" => 1,
						"showsearch" => 0,
						"lock" => 1,
					],
					[
						"SUCCESS" => 1,
						"name" => "status",
						"title" => "状态",
						"type" => "radio",
						"value" => "1",
						"content" => "1|显示\r\n0|隐藏",
						"tip" => "",
						"rule" => "",
						"extend" => "",
						"showlist" => 1,
						"showsearch" => 1,
						"lock" => 1,
					],
			],
	];	