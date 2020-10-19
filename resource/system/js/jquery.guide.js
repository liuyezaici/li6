/***
 * jQuery-Guide向导插件
 * 编写时间：2013年6月16号
 * version: 1.0
 * author:小宇<i@windyland.com>
 *    lirui 2015-1-8 edit
***/
var isLoading = false;  //设置时间关卡，防止快速跳到下一步
(function($) {
	$.extend({
		wlGuideBox: {
			def: {
				title: "当前步骤",
				text: "这里是向导文本"
			},
			name: "D" + new Date().getTime(),
			g: new Array(),
			gn: 0,
			s: 0,
			created: false,
			create: function() {
				var the = this;
				the.created = true;
				the.ele = $("<div id='wlGuide_box" + the.name + "' class='cGuide_box'><span class='cGuide_arrow'></span><div class='cGuide_content'></div></div>");
				the.blank_all = $("<div class='cGuide_blank' id='black_background'></div>");
				the.title = $("<h2 class='cGuide_title'></h2>");
                the.text_ = $("<div class='cGuide_content_text'></div>");
				the.scount = $("<span class='cGuide_scount'></span>");
                the.btn_pre = $("<a href='javascript:void(0)' class='cGuide_button' id='pre_btn'>上一步</a>'");
                the.btn_next = $("<a href='javascript:void(0)' class='cGuide_button' id='next_btn'>下一步</a>'");
				the.button = {
                    p:  the.btn_pre.click(function() {
                        if (!$(this).hasClass("cGuide_button_unable")) {
                            if(isLoading == true) return; //设置时间关卡，防止快速跳到下一步
                            $("#every_step_view").removeAttr('style') ;//背景坐标默认回到顶部 防止重定位后再后退时背景不对
                            var ind = the.s-1;
                            the.step(-1);
                            $("#every_step_view").attr('class','step step'+ind);
                            stepEven(ind-1);
                            checkTime(); //设置时间关卡，防止快速跳到下一步
                        }
                    }),
					n:  the.btn_next.click(function() {
                        if(isLoading == true) return; //设置时间关卡，防止快速跳到下一步
						if (!$(this).hasClass("cGuide_button_unable")) {
                            $("#every_step_view").removeAttr('style') ;//背景坐标默认回到顶部 防止重定位后再后退时背景不对
                            var ind = the.s;
							the.step(1);
                            $("#every_step_view").attr('class','step step'+ (ind+1));
                            stepEven(ind);
                            checkTime(); //设置时间关卡，防止快速跳到下一步
						}
					}),
					c: $("<a href='javascript:void(0)' class='cGuide_button'>关闭</a>'").click(function() {
						if (!$(this).hasClass("cGuide_button_unable")) {
							the.close();
						}
					})
				};
				$("body").prepend(
					the.blank_all
				);
			},
			step: function(i) {
				var the = this;
				var ind = the.s += i;
				if (ind > the.gn) {
					ind = the.s = the.gn;
					return false;
				} else if (ind < 1) {
					ind = the.s = 1;
					return false;
				}
                the.button.p.attr("class", ind === 1 ? "cGuide_button_unable": "cGuide_button");
				the.button.n.attr("class", ind === the.gn ? "cGuide_button_unable": "cGuide_button");
				the.button.c.attr("class", ind === the.gn ? "cGuide_button": "cGuide_button_unable");
				if (the.g[ind]) {
					the.title.html(the.g[ind].title);
                    the.text_.html(the.g[ind].text);
					the.scount.html("(" + ind + "/" + the.gn + ")");

                    var obj = $(the.g[ind].ele);
                    the.ele.find(".cGuide_content").append(
                        the.title, the.scount, this.text_, the.button.p, the.button.n, the.button.c
                    )
                    obj.prepend(the.ele);
                    var ofs = obj.offset();
                    if ($.browser.msie && $.browser.version == "6.0") {
                        var bfs = $("body").offset()
                    }
                    obj.addClass('show');
                    var toTop = 0;
                    var toBottom = ofs.top + the.ele.outerHeight() + 20;
                    while (toBottom - toTop > $(window).height()) {
                        toTop += 200;
                    }
                    $("html,body").animate({
                            scrollTop: toTop
                        },
                        200);
                    if(i>0) {
                        $(obj.prev()).removeClass('show');
                    } else {
                        $(obj.next()).removeClass('show');
                    }
                    the.ele.show();
                    setTimeout(function() {
                        the.blank_all.css({
                            height: $(document).height()+$(document).scrollTop()
                        });
                    }, 200);
				}
			},
			show: function(gs) {
				if (!this.created) {
					this.create();
				}
				var the = this;
				the.blank_all.show().css({
                    width: "100%",
                    overflow: "hidden",
                    height: $(document).height()+$(document).scrollTop()
				});
				the.ele.fadeIn();
				the.s = the.gn = 0;
				the.g = new Array();
				$.each(gs,
				function(i, e) {
					the.g[i + 1] = $.extend({},
					the.def, e);
					the.gn++;
				});
				the.step(1);
			},
			close: function() {
				var the = this;
				the.ele.hide();
				the.blank_all.hide();
			}
		},
		wlGuide: function(gs) {
			if (gs && gs.close) {
				$.wlGuideBox.close();
				return true;
			}
            //设置键盘
            $(document).off('keyup').on('keyup',function(event){
                var code = event.keyCode;
                if(code == 37 || code == 38) {
                    $('#pre_btn').click();
                } else if(code == 39 || code == 40) {
                    $('#next_btn').click();
                }
            });
			return $.wlGuideBox.show(gs);
		}
	})
})(jQuery);