// 异常()开头
var regErrNullGongshiSet1 = /^\(\s*\)/;
//  纯 (asdasdasd) 或 = (abc) 或 (abc) >
var regErrNullGongshiSet2 = /^\(([a-zA-Z]+)\)$|([>|<|==|!|&|\|])\s*\(([a-zA-Z]+)\)|\(([a-zA-Z]+)\)\s*([>|<|==|!|&|\|])/;
// 异常格式 一个或多个!  {!} {!!}
var regErrNullGongshiSet3 = /^!+$/;
// !&|<>=开头或结尾
var regErrFrontEnd = /^\s*(=|\||&|<|>|!)|(=|\||&|<|>)\s*$/;
//  &&||   中间有空
var regErrBoolenGongshiSetMiddle = /(&&|\|\|)\s*(&&|\|\|)/; //
//  && 或 ||的 左边或右边 直接跟了比较符 如：||> ||< ||! ||=
var regErrNullGongshiSet4 = /([\&\&|\|\|]+)\s*(>|<|=|!)|(>|<|=|!)\s*([\&\&|\|\|]+)/;
// () 的里面直接跟了比较符 ><!=&| ) 注意：)外面是可以跟|&的
var regErrNullGongshiSet5 = /\(\s*([>|<|=|!|&|\|])|([>|<|=|!|&|\|])\s*\)/;
//字母和数字直接跟比较符号 注意 class="" 注意的不能过滤 所以要考虑是字母开头 或 前面跟&&||
var regErrNullGongshiSet6 = /^([a-zA-Z_]+)([a-zA-Z0-9_]*)([>|<|==|!|\||&])| ([\&\&|\|\|]+)\s*([a-zA-Z_]+)([a-zA-Z0-9_]*)\s*([>|<|==|!|\||&])|([>|<|==|!|\||&])\s*([a-zA-Z_]+)([a-zA-Z0-9_]*)\s*([\&\&|\|\|]+)|([>|<|==|!|\||&])\s*([a-zA-Z_]+)([a-zA-Z0-9_]*)\s*([>|<|==|!|\||&])|([>|<|=|!])([a-zA-Z_]+)([a-zA-Z0-9_]*)$/;
//运算符跟空的()
var regErrNullGongshiSet7 = /([>|<|=|!|&|\|])\s*\(\s*\)|\(\s*\)\s*([>|<|=|!|&|\|])/;


function formatErrStr(str_) {
    //获取所有括号里的内容
    var matches = str_.match(/{([^}^{]*?)}/g);// ["{id}", "{name}", "{1+2...}"] //局部变量 {abc} 数据来源于data
    // console.log('matches');
    // console.log(matches);
    if(!matches) { //  =="" 这样的字符串
        // console.log('tmpStr 11111111111:'+ str_);
        // console.log('tmpStr 22222222222:'+ str_);
        // console.log(matches);
        str_ = __replaceErrString(str_, str_);
        // console.log('tmpStr 22222222222:'+ str_);
        return str_;
    }
    function __replaceErrString(str2, tmpStr) {
        var newTmpString;
        //console.log('tmpStr:'+ tmpStr);
        newTmpString = trim(tmpStr, '{');
        newTmpString = trim(newTmpString, '}');
        console.log('replaceErrYunsuan:'+ newTmpString);
        newTmpString = replaceErrYunsuan(newTmpString);
        console.log(' Yunsuan enddddddddd:'+newTmpString);
        newTmpString = replace3yuanYinhao(newTmpString);
        // console.log('new replace3yuanYinhao:'+ newTmpString);
        if(hasYufa(newTmpString)) {
            console.log('tmpStr:'+ tmpStr);
            console.log('hasyufa newTmpString 1:'+ newTmpString);
            newTmpString = runYufa(newTmpString);
            console.log('hasyufa newTmpString 2:'+ newTmpString);
        } else {
            console.log('no hasyufa newTmpString:'+ newTmpString);
        }

        console.log('str2:');
        console.log(str2);
        console.log('tmpStr:');
        console.log(tmpStr);
        if(str2 == tmpStr) { //原文替换
            str2 = newTmpString;
        } else {
            str2 = str2.replace(RegExp(regCodeAddGang(tmpStr),"g"), newTmpString);
        }
        console.log('str2 last:');
        console.log(str2);
        return str2;
    }
    if(matches) matches = uniqueArray(matches);
    console.log('format__ErrStr:'+ str_);
    console.log('matches2:');
    console.log(matches);
    $.each(matches, function (n, tmpStr) {
        str_ = __replaceErrString(str_, tmpStr);
    });
    return str_;
}


function hasErrRunGongshi(str) {
    console.log('hasErr RunGongshi:'+ str);
    //去掉<div> <br/> 等dom 放置误判为对比函数
    var noJTStr = str.replace(/<\s*([a-zA-Z]+)([^>^&^|]*)>/g, '');
    noJTStr = noJTStr.replace(/<\/([a-zA-Z]+)>/g, '');
    // console.log('noJTStr:'+ noJTStr);
    if(regErrFrontEnd.test(noJTStr)) {
        return 'front';
    }
    if(regErrNullGongshiSet1.test(noJTStr)) return 1;
    if(regErrNullGongshiSet2.test(noJTStr)) return 2;
    if(regErrNullGongshiSet3.test(noJTStr)) return 3;
    if(regErrBoolenGongshiSetMiddle.test(noJTStr)) return 'middle';
    if(regErrNullGongshiSet4.test(noJTStr)) return 4;
    if(regErrNullGongshiSet5.test(noJTStr)) return 5;
    var test6 = noJTStr; //6支持true=的格式 所以要把true=改为1=
    test6 = test6.replace(/^\s*true\s*([>|<|==|!|\||&])/g, '1$1'); // true &&或 true ||  替换为1
    test6 = test6.replace(/^\s*false\s*([>|<|==|!|\||&])/g, '0$1'); // false &&或 false ||  替换为1
    test6 = test6.replace(/([\&\&|\|\|]+)\s*true\s*([>|<|==|!|\||&])/g, '$1 1'); // true &&或 true ||  替换为1
    test6 = test6.replace(/([\&\&|\|\|]+)\s*false\s*([>|<|==|!|\||&])/g, '$1 0'); // false &&或 false ||  替换为1
    test6 = test6.replace(/([>|<|==|!|\||&])\s*true\s*([\&\&|\|\|]+)/g, '$1 1 $2'); // == true &&  替换为 ==1 &&
    test6 = test6.replace(/([>|<|==|!|\||&])\s*false\s*([\&\&|\|\|]+)/g, '$1 1 $2'); //  ==  false   替换为 ==0&&
    test6 = test6.replace(/([>|<|=|!])\s*true\s*$/g, '$1 1'); // true &&或 true ||  替换为1
    test6 = test6.replace(/([>|<|=|!])\s*false\s*$/g, '$1 0 '); // false &&或 false ||  替换为1
    if(regErrNullGongshiSet6.test(test6)) return 6;
    if(regErrNullGongshiSet7.test(noJTStr)) return 7;
    return false;
}


//替换非法的运算字符
// >= 12 要加0 在前面
//  == "aaa"  替换为 "" == "aaa"
// && 1>2 或 || 1<2 或 &&||替换为 Boolean("")
//解析各种奇葩语法解析
function replaceErrYunsuan(str) {
    str = str || '';
    // console.log('replace ErrYunsuan:'+ str);
    //校验错误码
    function checkAndGoTo(str_, code) {
        code +='';
        // console.log('call checkAndGoTo code:'+ code);
        switch (code){
            case '1':
                return mendString1(str_);
                break;
            case '2':
                return mendString2(str_);
                break;
            case '3':
                return mendString3(str_);
                break;
            case '4':
                return mendString4(str_);
                break;
            case '5':
                return mendString5(str_);
                break;
            case '6':
                return mendString6(str_);
                break;
            case '7':
                return mendString7(str_);
                break;
            case 'front'://=开头或=结尾
                // console.log('call mendStringFrontEnd:');
                return mendStringFrontEnd(str_);
                break;
            case 'middle'://||跟了&&
                return mendStringMiddle(str_);
                break;
            default: //没有异常 返回原字符串
                return str_;
        }
    }
    //校验数据1 //()开头
    function mendString1(str_) {
        // console.log('hasErrGs1111:');
        var matches = str_.match(RegExp(regErrNullGongshiSet1, 'g'));
        // console.log('matches:');
        // console.log(matches);
        var matchesLen = matches[0].length;
        str_ = '("")'+ str_.substr(matchesLen);
        // console.log('new str:'+ str_);
        var hasErrGs = hasErrRunGongshi(str_);
        // console.log('hasErrGs:'+ hasErrGs);
        return checkAndGoTo(str_, hasErrGs);
    }
    //校验数据2 // 纯 (asdasdasd) 或 = (abc) 或 (abc) >
    function mendString2(str_) {
        var matches = str_.match(RegExp(regErrNullGongshiSet2, 'g'));
        // console.log('hasErrGs2');
        // console.log('str:'+ str_);
        // console.log('matches:');
        $.each(matches, function (n, match_) {
            var tmpNewMatch = match_.replace(/\(([a-zA-Z]+)([a-zA-Z0-9_]*)\)/, '("$1")');
            str_ = str_.replace(RegExp(regCodeAddGang(match_), 'g'), tmpNewMatch);
            // console.log('new str:'+ str_);
        });
        var hasErrGs = hasErrRunGongshi(str_);
        return checkAndGoTo(str_, hasErrGs);
    }
    //校验数据3 //{!} {!!}
    function mendString3(str_) {
        var matchGth = str_.match(RegExp('!', 'g')); //统计感叹号个数
        // console.log('mendString3:' + str_);
        // console.log(matchGth);
        // return;
        var gthLen = matchGth.length;
        var newArray = [];
        for(var i_ = 0; i_<gthLen; i_++) {
            newArray.push('Boolean("1")');
        }
        str_ = newArray.join('&&'); //多个!转换为多个布尔值
        var hasErrGs = hasErrRunGongshi(str_);
        return checkAndGoTo(str_, hasErrGs);
    }
    //校验数据Front  =!|&><开头 或结尾
    function mendStringFrontEnd(str_) {
        // console.log('mendStringFrontEnd:'+ str_);
        if(/^\s*(=|\||&|!|<|>)/.test(str_)) {
            str_ =  '""' + str_;
        }
        if(/(=|\||&|!|<|>)\s*$/.test(str_)) {
            str_ +=  '""';
        }
        var hasErrGs = hasErrRunGongshi(str_);
        return checkAndGoTo(str_, hasErrGs);
    }
    //&&||
    function mendStringMiddle(str_) {
        // console.log('regErrBoolenGongshiSetMiddle:'+ str);
        str = str.replace(/\|\|\s*&&/g, "||false&&");
        str = str.replace(/&&\s*\|\|/g, "&&false||");
        var hasErrGs = hasErrRunGongshi(str);
        return checkAndGoTo(str_, hasErrGs);
    }

    //校验数据4 //  && 或 ||的 左边或右边 直接跟了运算符 如：||> ||< ||! ||=
    function mendString4(str_) {
        var matches = str_.match(RegExp(regErrNullGongshiSet4, 'g'));
        // console.log('emptyA ddYnhao str:' + str_);
        // console.log(matches);
        // return;
        $.each(matches, function(n, match) {
            var newMatch;
            if(/\s/.test(match)) {//包含空格
                newMatch = match.replace(' ', '""');
                // console.log('newMatch empty' );
            } else  {//&&!== 或 !==|| 这种紧挨的语法
                if(strInArray(match.substr(0, 2), ['&&', '||']) !=-1) {
                    // console.log('newMatch 51');
                    newMatch = match.substr(0, 2) + '""' + match.substr(2);
                } else {
                    // console.log('newMatch 52');
                    newMatch = match.substr(0, match.length-2) + '""' + match.substr(-2);
                }
            }
            // console.log('newMatch:'+ newMatch );
            if(newMatch) {
                // console.log('old replace:'+ str_ );
                str_ = str_.replace(RegExp(regCodeAddGang(match), 'g'), newMatch);
                // console.log('new replace:'+ str_ );
            } else {
                // console.log('not_set_newMatch:'+ str_);
            }
        });
        var hasErrGs = hasErrRunGongshi(str_);
        // console.log('hasErrGs:'+ hasErrGs);
        return checkAndGoTo(str_, hasErrGs);
    }
    //校验数据5 //  () 的里面或外面直接跟了运算符 ><!=&|
    function mendString5(str_) {
        var matches = str_.match(RegExp(regErrNullGongshiSet5, 'g'));
        $.each(matches, function(n, match) {
            // console.log('emptyAd dYnhao str:'+ match );
            var newMatch;
            if(/(\(|\))\s*([>|<|=|!|&|\|])/.test(match)) {//左边是括号
                newMatch = '(""' + match.substr(1);
            } else if(/([>|<|=|!|&|\|])\s*(\(|\))/.test(match)) {//右边是括号
                newMatch = match.substr(0, match.length-1) + '"")';
            }
            // console.log('inKuohaoAddYnhaonewMatch:'+ newMatch );
            if(newMatch) {
                // console.log('old replace:'+ str_ );
                str_ = str_.replace(RegExp(regCodeAddGang(match), 'g'), newMatch);
                // console.log('new replace:'+ str_ );
            }
        });
        var hasErrGs = hasErrRunGongshi(str_);
        return checkAndGoTo(str_, hasErrGs);
    }
    //校验数据6
    //字母和数字直接跟比较符号 注意 class="" 注意的不能过滤 所以要考虑是字母开头 或 前面跟&&||
    //lz: abv1>  或  || assa==  或  =assa&& 或 && >0
    function mendString6(str_) {
        var matches = str_.match(RegExp(regErrNullGongshiSet6, 'g'));
        // console.log('mendString6');
        // console.log(matches);
        $.each(matches, function(n, match) {
            var newMatch,newMatch2;
            //以字母开头 abc12== 或 ==abc123结尾
            var fromReg = /^([a-zA-Z0-9_]*)|([a-zA-Z_]+)([a-zA-Z0-9_]*)$/;
            if(fromReg.test(match)) {
                newMatch = match.replace(fromReg, '"$1"');
                // console.log('str_6 match:'+ match);
                // console.log('str_6 111:'+ str_);
                str_ = str_.replace(RegExp(regCodeAddGang(match), 'g'), newMatch);
                // console.log('str_6 222:'+ str_);
            } else {
                // console.log('match6:'+ match);
                newMatch = match.replace(/([a-zA-Z0-9_]*)/g, '"$1"');
                str_ = str_.replace(RegExp(regCodeAddGang(match), 'g'), newMatch);
                // console.log('inKuohaoAddYnhaonewMatch:'+ str_ );
            }

        });
        return str_;
        var hasErrGs = hasErrRunGongshi(str_);
        return checkAndGoTo(str_, hasErrGs);
    }
    //运算符跟空的()
    function mendString7(str_) {
        var matches = str_.match(RegExp(regErrNullGongshiSet7, 'g'));
        console.log('regErrNullGongshiSet7:'+str_ );
        console.log(matches);
        $.each(matches, function(n, match) {
            console.log('emptyAdd Ynhao str:'+ match );
            var newMatch;
            if(match.substr(0, 1) != '(') {// = () 或 >()
                newMatch = match.substr(0, 1)+ '("")';
            } else {//() > 或 () <
                newMatch = '("")'+ match.substr(match.length-1);
            }
            if(newMatch) {
                console.log('old replace:'+ str_ );
                str_ = str_.replace(RegExp(regCodeAddGang(match), 'g'), newMatch);
                console.log('new replace:'+ str_ );
            }
        });
        var hasErrGs = hasErrRunGongshi(str_);
        return checkAndGoTo(str_, hasErrGs);
    }
    var hasErrGs = hasErrRunGongshi(str);
    if(!hasErrGs) return str;
    // console.log('hasErrGs:'+ hasErrGs);
    return checkAndGoTo(str, hasErrGs);
}



//在格式化字符串之前检测格式
//如： abc== 或 ==asad 或 {aa}>2
function remendStrBeforeFormat(str_) {
    var reg1 = /{([^\{\}]+)}/;
    str_ = yinhaogTH(str_);//所有\\"都要保护起来 防止被解析

    // console.log('str_1 :'+ str_);
    str_ = changeYinhaoIn(str_);
    console.log('str_ :'+ str_);
    //&和|只匹配一个就好 因为可能前面true&&abc>2 后面又跟abc
    var jkhMatchs = str_.match(RegExp(reg1, 'g')); //找到所有的尖括号 进行语法校验和补充""
    if(!hasData(jkhMatchs)) {
        return  str_;
    }
    //替换单层{}变量
    function fixed1CengJkh() {
        jkhMatchs = uniqueArray(jkhMatchs);
        console.log(jkhMatchs);
        console.log('修正尖括号里的语法错误 开始：'+ str_);
        //{aa} ===2 不应该加引号了 ,只给 abc == 加引号 "abc" ==
        var regMiddle = /([a-zA-Z]+)([a-zA-Z_0-9]*)\s*([\||&|=|!|>|<|+|\?|:])|([\||&|=|!|>|<|+|\?|:])\s*([a-zA-Z]+)([a-zA-Z_0-9]*)/;
        //纠正单个尖括号里的语法错误
        function refixedOne(jkhStr) {
            if(/{([a-zA-Z_0-9]+)}/.test(jkhStr)) {
                return ; //纯英文数字不需要替换错误了
            }
            // console.log('has err string');
            if(regMiddle.test(jkhStr) ) {
                // console.log('has err string');
                // console.log(jkhStr);
                var errMatches = jkhStr.match(RegExp(regMiddle, 'g'));
                // console.log('mendString6');
                // console.log(errMatches);
                $.each(errMatches, function(n, matchT) {
                    var newMatch;
                    var match__new = null;
                    //以&或|开头  |{a}或 &a
                    var fromReg = /^([\||&|=|!|>|<|+|\?|:]+)(.+)/;
                    //以{xxx}在中间 "aaaa"||{xxx}
                    var backReg = /{*([^\|&><\!\=]*)}*\s*([\||&|>|<|=|!|+|\?|:]+)/;
                    if(fromReg.test(matchT)) {//  abc==
                        // console.log('test fromReg errrrr:');
                        // console.log(matchT);
                        //||false转为 ||0
                        var backFalseReg = /^([\||&|=|!|>|<|+|\?|:])\s*(false)/;
                        var macFalse = matchT.match(backFalseReg);
                        if(macFalse) {
                            // console.log('matchT:'+ matchT);
                            // console.log(macFalse[1]);
                            // console.log(macFalse[2]);
                            match__new = jkhStr.replace(RegExp(regCodeAddGang(matchT)), macFalse[1]+'0');
                            // console.log('match__new:'+ match__new);
                            // console.log('match__:'+ match__);
                            // console.log('new  str_:'+ str_);
                        } else {
                            // console.log('matchT:'+ matchT);
                            newMatch = matchT.replace(RegExp(fromReg), function (matchTmp0, matchTmp1, matchTmp2) {
                                // console.log(matchTmp1);
                                // console.log(matchTmp2);
                                return matchTmp1 +' "' + $.trim(matchTmp2) +'"';
                            });
                            // console.log('newMatch:'+ newMatch);
                            // console.log('jkhStr:'+ jkhStr);
                            match__new = jkhStr.replace(RegExp(regCodeAddGang(matchT), 'g'), newMatch);
                            // console.log('match__new:'+ match__new);
                            // console.log('str_:'+ str_);
                        }
                    } else if(backReg.test(matchT)) { //abc&&
                        // console.log('backReg');
                        // console.log(matchT);
                        var backReg2 = /{{([^\|\&\>\<\}\&\=\!\+\?\:]+)}}/; //前面不能带^ 因为可能会有3个{  如 {{{abc}}&& aa}
                        var backReg3 = /{([^\|\&\>\<\}\&\=\!\+\?\:]+)}/; //前面不能带^ 因为可能会有2个{  如 {{abc}&& aa}
                        var backReg4 = /([^\|\&\>\<\}\&\=\!\+\?\:]+)/g; //前面不能带^ 因为可能会有2个{  如 {abc&& aa}
                        if(backReg2.test(matchT)) {
                            // console.log('backReg2');
                            // console.log(matchT);
                            var macStr = matchT.match(backReg2);
                            macStr = macStr[0];
                            match__new = jkhStr.replace(RegExp(regCodeAddGang(macStr), 'g'), '"'+ macStr +'"');
                        } else if(backReg3.test(matchT)) {
                            // console.log('backReg3');
                            // console.log(matchT);
                            var macStr = matchT.match(backReg3);
                            macStr = macStr[0];
                            match__new = jkhStr.replace(RegExp(regCodeAddGang(macStr), 'g'), '"'+ macStr +'"');
                        } else if(backReg4.test(matchT)) {
                            // console.log('backReg4');
                            // console.log(matchT);
                            //false&&转为 0&&
                            var backFlaseReg = /^\s*(false)\s*([\||&|=|!|>|<|+])/;
                            var macFalse = matchT.match(backFlaseReg);
                            if(macFalse) {
                                // console.log(macFalse);
                                match__new = jkhStr.replace(RegExp(regCodeAddGang(matchT)), '0'+ macFalse[2]);
                                // console.log('str_:'+ str_);
                            } else {
                                // console.log('matchT:'+ matchT);
                                var newStr = matchT.replace(RegExp(backReg4, 'g'), function (tmpMatch) {
                                    return '"'+ $.trim(tmpMatch) +'"';
                                });
                                // console.log('newStr1:'+ newStr);
                                match__new = jkhStr.replace(RegExp(regCodeAddGang(matchT), 'g'),  newStr);
                                // console.log('str_:'+ str_);
                                // console.log('match__:'+ match__);
                                // console.log('newStr2:'+ match__new);
                                // console.log('newStr1:'+ str_);
                            }
                        }
                        // console.log(macStr);
                    }

                    if(match__new) {
                        console.log('match__new:'+ match__new);
                        str_ = str_.replace(RegExp(regCodeAddGang(jkhStr), 'g'), match__new);
                        if(regMiddle.test(match__new)){
                            refixedOne(match__new);  //继续纠正单个尖括号里的语法错误
                        }
                    }
                });
            }
        }
        jkhMatchs.forEach(function (match__) {
            console.log('match__'+ match__);
            refixedOne(match__);  //纠正单个尖括号里的语法错误
            // console.log('jkhStr'+ jkhStr);
        });
    };
    fixed1CengJkh();
    var mulJhkReg = /{([^\{]*){/;

    console.log('修正尖括号里的语法错误 结束：: '+ str_);
    return str_;
}

//str = remendStrBeforeFormat(str);


//解密引号
//{12 === "item" ?"%22a%3D%20item2%22":"%22b%22"}  解密为：{12 === item ?"a= item2":"b"}
function changeYinhaoBack(str_) {
    console.log('changeYinhaoBack:'+ str_);
    if(isObj(str_)) return str_;
    if(isBoolean(str_)) return str_;
    if(isNumber(str_)) return str_;
    str_ = str_.replace(/'([^']*)'/g, function (match_) {
        match_ = trim(match_, "'");
        return "'"+ (match_ ? decodeNewHtml(this_.urlDecodeLR(match_)) : "") +"'";
    });
    str_ = str_.replace(/"([^"]*)"/g, function (match_) {
        match_ = trim(match_, '"');
        match_ = '"'+ (match_ ? decodeNewHtml(this_.urlDecodeLR(match_)) : '') +'"';
        // console.log('str_:'+ str_);
        // console.log(match_);
        return match_;
    });
    return str_;
}