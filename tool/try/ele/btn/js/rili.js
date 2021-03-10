define(['require'], function (require) {
    var global = {};
    var objBindAttrsName = 'bind_attrs';
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
//创建日历
    global.makeRili = function(sourceOptions, sureSource) {
        var core = require('core');
        sureSource = sureSource || false;
        var obj = $('<div></div>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  cloneData(sourceOptions) : cloneData(copySourceOpt(sourceOptions));
        }
        var riliInput = $('<input class="diy_input" type="text" autocomplete="off">');
        obj.append(riliInput);
        obj[objAttrHasKh] = false;
        var options = cloneData(sourceOptions);
        var setBind = getOptVal(options, ['bind'], '');
        var sourceVal = getOptVal(options, ['value'], '');
        obj.extend({
            //主动更新数据
            renew: function() {
                var diyText = getOptVal(options, ['text'], {}); //自定义文本
                var textYear = getOptVal(diyText, ['year'], '年');
                var textMonth = getOptVal(diyText, ['month'], '月');
                var textWeeks = getOptVal(diyText, ['week'], []);
                var textWeek0 = isUndefined(textWeeks[0]) ? '日': textWeeks[0];
                var textWeek1 = isUndefined(textWeeks[1]) ? '一': textWeeks[1];
                var textWeek2 = isUndefined(textWeeks[2]) ? '二': textWeeks[2];
                var textWeek3 = isUndefined(textWeeks[3]) ? '三': textWeeks[3];
                var textWeek4 = isUndefined(textWeeks[4]) ? '四': textWeeks[4];
                var textWeek5 = isUndefined(textWeeks[5]) ? '五': textWeeks[5];
                var textWeek6 = isUndefined(textWeeks[6]) ? '六': textWeeks[6];
                var riliVal = isUndefined(options['value']) ? '' : options['value'];
                var yearMenuWidth = isUndefined(options['year_menu_width']) ? '228px' : options['year_menu_width'];
                var monthMenuWidth = isUndefined(options['month_menu_width']) ? '113px' : options['month_menu_width'];
                var onChoseEven = getOptVal(options, ['onChose','chose'], null);//选中时执行
                var ymSize = getOptVal(options, ['ymSize','ym_size', 'size'], 'normal');//年月的尺寸
                options.year_menu_width = isUndefined(options.year_menu_width) ? 390 : options.year_menu_width;//年份菜单宽度
                options.month_menu_width = isUndefined(options.month_menu_width) ? 180 : options.month_menu_width;//年份菜单宽度
                var now =  new Date();
                var nowYear =  now.getFullYear();//今年
                var currentDay =  now.getDay();//今天
                var fromYear = isUndefined(options.from_year) ? nowYear -40 : options.from_year;//开始年份
                var toYear = isUndefined(options.to_year) ? nowYear + 10 : options.to_year;//截止年份
                var yearMoneyDaySelect;
                var splitStr = '-';//日期分割符号
                var allYears = [];//定义所有可选的年份，防止当前年份不存
                //构建年月下拉框
                var yearData = [];
                for(var i = fromYear; i<=toYear; i++){
                    yearData.push({'value':i, 'title':i});
                    allYears.push(i);
                }
                if(!strHasKuohao(sourceVal)) {
                    riliInput.val(sourceVal);
                }
                //获取年月日
                function getStrYMD(riliVal) {
                    riliVal = riliVal || '';  //riliVal //当前输入的年月
                    riliVal = $.trim(riliVal);
                    var year,month,day=0;
                    if(!riliVal || riliVal==0 || !/\d{4}-\d{1,2}-\d{1,2}/.test(riliVal)) {
                        year =  now.getFullYear();//今年
                        month = now.getMonth()+1;//本月
                        day = now.getDay(); //默认为0 防止每次去掉日期时闪回到今天日期
                        if(strInArray(year, allYears) ==-1) {
                            year = allYears[0];
                        }
                    } else {
                        riliVal = riliVal.replace(/\/|\./g, splitStr);
                        riliVal = riliVal ? riliVal.split(' ')[0] : '';
                        var dateArray = riliVal.split(splitStr);
                        year = dateArray[0];
                        // year = year < 1200 ? 1200 : year;
                        month = dateArray[1];
                        month = month > 12 ? 12 : month;
                        if(!month) month = 1;
                        day = dateArray[2];
                        day = day > 31 ? 31 : day;
                    }
                    //console.log('month:'+ month);
                    //console.log('day:'+ day);
                    return [year, month, day];
                }

                obj._rlMenu = $('<div class="calendar_menu"></div>');
                $('body').append(obj._rlMenu);

                //select:单独的格式化value的括号 更新data时会触发
                obj.formatVal = function (opt) {
                    opt = opt || [];
                    var newData = getOptVal(opt, ['data'], {});
                    var newVal = _onFormatVal(obj, newData,  sourceVal);
                    if ($.isArray(newVal)) newVal = newVal.join(',');
                    riliInput.val(newVal);
                    obj.callRenewRiliBind(newVal);
                };
                //更像绑定的值
                obj.callRenewRiliBind = function(newVal) {
                    if(isUndefined(newVal)) {
                        newVal = obj.value;
                    } else {
                        obj.value = newVal;
                    }
                    var renewBind = obj[objAttrHasKh] == true;
                    if (setBind && renewBind) {
                        var exceptObj = [];
                        exceptObj.push(obj);
                        updateBindObj(setBind, newVal, exceptObj);
                        if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][setBind])) {
                            core.renewObjBindAttr(obj, setBind);
                        }
                    }
                };

                optionDataFrom(obj, options);
                var rlMosHvr = false;
                obj._rlMenu.on({
                    'mouseenter': function () {
                        rlMosHvr = true;
                    },
                    'mouseleave': function () {
                        rlMosHvr = false;
                    },
                })
                riliInput.on({
                    'blur': function () {
                        if(!rlMosHvr) {
                            setTimeout(function () {
                                obj._rlMenu.hide();
                                yearMoneyDaySelect['menu'].hide();
                                yearMoneyDaySelect['son']['menu'].hide();
                            }, 100);
                        } else {
                            //每次选择 要给按钮对焦  这样鼠标点击外部就可以触发关闭下拉层
                            riliInput.focus();
                        }
                    },
                    click: function (e) {
                        var clickObj = $(this);
                        if(clickObj.hasClass('lrXX')) return;//点击clear 无须弹窗
                        //每次点击输入框 重新创建日历
                        var ymd_ = getStrYMD(obj.value);
                        makeDays_(ymd_[0], ymd_[1], ymd_[2]);
                        //恢复下拉框的年月
                        if(ymd_[0]) {
                            yearMoneyDaySelect.value = ymd_[0];
                        }
                        // console.log(yearMoneyDaySelect['son'], ymd_[1]);
                        if(ymd_[1]) {
                            yearMoneyDaySelect['son'].value = ymd_[1];
                        }
                        obj._rlMenu.show();
                    },
                    change: function (e) {
                        var ymd_ = getStrYMD($(this).val());
                        makeDays_(ymd_[0], ymd_[1], ymd_[2]);
                    }
                });
                //创建日历菜单
                function makeRiliMenu() {
                    //构建 月的下拉框
                    var monthData = [];
                    for(var i = 1;i<=12;i++){
                        monthData.push({'value':i,'title':i});
                    }
                    var tableHtml = '<table class="calendar_table" cellspacing="0" cellpadding="0" border="0">' +
                        '<tr class="tr_"><td>' +
                        '<span class="last_month_btn pre_next_btn"> <span class="icon"> </span>  </span>' +
                        '</td>' +
                        '<td colspan="5" class="show_year_month_box"> </td>' +
                        '<td>' +
                        '<span class="next_month_btn pre_next_btn" style="cursor:hand;"> <span class="icon"> </span> </span>' +
                        '</td>' +
                        '</tr>' +
                        '<tr class="week_tr">';
                    //日期选择
                    var weekDays = [textWeek0, textWeek1, textWeek2, textWeek3, textWeek4, textWeek5, textWeek6];
                    for(var i = 0 ; i<weekDays.length; i++){
                        tableHtml+='<td>'+weekDays[i]+'</td>';
                    }
                    tableHtml+="</tr></table>";
                    obj._rlMenu.append($(tableHtml));
                    var currentYear,currentMonth;
                    currentYear = nowYear;
                    currentMonth = now.getMonth();
                    if(riliVal) {
                        var ymd_ = getStrYMD(riliVal);
                        currentYear = ymd_[0];
                        currentMonth = ymd_[1];
                        currentDay = ymd_[2];
                    }
                    //console.log(allYears[0]);
                    if(strInArray(currentYear, allYears) ==-1) {
                        currentYear = allYears[0];
                    }
                    //console.log('currentYear:'+ currentYear);
                    //console.log('currentMonth:'+ currentMonth);
                    yearMoneyDaySelect = global.makeSelect({
                        'value_key': 'value',
                        'title_key': 'title',
                        menu: {
                            width: yearMenuWidth,
                            'data': yearData
                        },
                        'default_text': currentYear,
                        'value': currentYear +'',
                        'size': ymSize,
                        menuOpen: function(o) {
                            console.log('click_year');
                            yearMoneyDaySelect['son']['menu'].hide();
                        },
                        li: {
                            value: '{title}',
                            //修改月份时 要重新载入日期
                            click: function (li_, eve, pubData) {
                                var yearMonth = getCurrentYM();
                                makeDays_(li_.attr('data-value'), yearMonth[1], currentDay);
                            }
                        },
                        son: {
                            menu: {
                                width: monthMenuWidth,
                                'data': monthData
                            },
                            menuOpen: function() {
                                console.log('click_month');
                                yearMoneyDaySelect['menu'].hide();
                            },
                            'value_key': 'value',
                            'title_key': 'title',
                            li: {
                                value: '{title}',
                                width: '25px',
                                //修改月份时 要重新载入日期
                                click: function (li_, eve, pubData) {
                                    var yearMonth = getCurrentYM();
                                    makeDays_(yearMonth[0], li_.attr('data-value'), currentDay);
                                }
                            },
                            'size': ymSize,
                            'default_text': currentMonth,
                            'value': currentMonth+''
                        }
                    });
                    obj._rlMenu.find('.show_year_month_box')
                        .append(yearMoneyDaySelect).append('&nbsp;' + textYear +'&nbsp;')
                        .append(yearMoneyDaySelect['son']).append('&nbsp;' + textMonth +'&nbsp;');
                    obj._rlMenu.find('.tr_').click(function (o) {
                        yearMoneyDaySelect['menu'].hide();
                        yearMoneyDaySelect['son']['menu'].hide();
                    });
                }
                //计算某天是星期几
                function thisWeekDay (year,month,date) {
                    var d = new Date(year,month-1,date);
                    return d.getDay();
                }
                //console.log(yearData);
                makeRiliMenu();
                //获取当前选择的年月日
                function getCurrentYM() {
                    var data_ = [];
                    //console.log('vv:'+ yearMoneyDaySelect.value);
                    data_.push(yearMoneyDaySelect.value);
                    data_.push(yearMoneyDaySelect['son'].value);
                    return data_;
                }
                //构建日历框 并显示
                function makeDays_(thisYear, thisMonth, thisDay) {
                    //每次构建日历 要更新下拉框的年和月
                    yearMoneyDaySelect.value = thisYear;
                    yearMoneyDaySelect['son'].value = thisMonth;
                    //判断是否为闰年
                    function isBissextile(year){
                        var isBis = false;
                        if (0==year%4 && ((year%100!=0) || (year%400==0))) {
                            isBis = true;
                        }
                        return isBis;
                    }
                    //计算某月的总天数，闰年二月为29天
                    function getMonthDays(year_,month_) {
                        var days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month_-1];
                        if((month_==2) && isBissextile(year_)){
                            days++;
                        }
                        return days;
                    }

                    //输出天数
                    var cHtml ="<tr class='day_tr'>";
                    //算出当前年月1号是星期几
                    var thisWeek = thisWeekDay(thisYear,thisMonth,1);
                    if(thisWeek !=7){
                        for (var sw = 0;sw<thisWeek;sw++){
                            cHtml+='<td></td>';
                        }
                    }
                    //开始循环输出当月天数
                    var css_;
                    for (var i = 1; i < getMonthDays(thisYear,thisMonth)+1; i++) {
                        if(thisDay == thisDay && i == thisDay && i == thisDay) {
                            css_ = ' current';
                        } else {
                            css_ = '';
                        }
                        $(this).addClass('current');
                        cHtml+='<td data-value="'+i+'"> <span class="day'+ css_ +'">'+ i +'</span></td>';
                        //星期六换行
                        if(thisWeekDay(thisYear, thisMonth, i)==6 ){
                            cHtml+="</tr>";
                            cHtml+="<tr class='day_tr'>";
                        }
                    }
                    cHtml += '</tr>';
                    cHtml += '</table>';
                    if(obj._rlMenu.find('.day_tr').length>0) obj._rlMenu.find('.day_tr').remove();
                    obj._rlMenu.find('.week_tr').after($(cHtml));
                    //选择日期
                    var dataArray = [];
                    obj._rlMenu.find('.day').off().on('click', function() {
                        dataArray = [];
                        var day_ = $(this).text();
                        $(this).addClass('current');
                        var yearMonth = getCurrentYM();
                        yearMonth.push(day_);
                        var valStr = yearMonth.join(splitStr);
                        riliInput.val(valStr);
                        obj.callRenewRiliBind(valStr);
                        obj._rlMenu.hide();
                        //设置选中时的命令
                        if(onChoseEven) {
                            onChoseEven(valStr, obj, core.livingObj);
                        }
                    });
                    //上一个月
                    obj._rlMenu.find('.last_month_btn').off().on('click', function() {
                        var yearMonth = getCurrentYM();
                        var year_ = yearMonth[0];
                        var month_ = yearMonth[1];
                        if(month_ ==1){
                            year_ = year_-1;
                            month_ = 12;
                        } else {
                            month_ = month_-1;
                        }
                        makeDays_(year_, month_, thisDay);
                    });
                    //下一个月
                    obj._rlMenu.find('.next_month_btn').off().on('click', function() {
                        var yearMonth = getCurrentYM();
                        var year_ = yearMonth[0];
                        var month_ = yearMonth[1];
                        if(month_ ==12){
                            ++year_;
                            month_ = 1;
                        }else{
                            ++month_;
                        }
                        makeDays_(year_, month_, thisDay);
                    });
                    //显示控件
                    var pos = obj.offset();
                    obj._rlMenu.css({'display': 'block', 'left': pos.left, 'top': (pos.top + obj.outerHeight() ) });
                }
            },
            //克隆当前对象 name要重新生成
            cloneSelf: function() {
                var opt = cloneData(obj.sor_opt);
                return global.makeRili(opt, true);
            },
            updates: function(dataName, exceptObj) {//数据被动同步
                var newVal = getObjData($.trim(setBind));
                riliInput.val(newVal);
                obj.value = newVal;
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) {
                    core.renewObjBindAttr(this, dataName);
                }
            }
        });

        optionGetSet(obj, options);
        obj.renew();
        //参数读写绑定 参数可能被外部重置 所以要同步更新参数
        //先设定options参数 下面才可以修改options
        var hasSetData = !isUndefined(options['data']);
        strObj.formatAttr(obj, options, 0, hasSetData);
        objBindVal(obj, options);//数据绑定
        addCloneName(obj);//支持克隆
        return obj;
    };
    return global;
});

