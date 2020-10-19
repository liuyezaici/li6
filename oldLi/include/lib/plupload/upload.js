
var accessid = '',
accesskey = '',
host = '',
policyBase64 = '',
signature = '',
callbackbody = '',
filename = '',
key = '',
expire = 0,
g_object_name = '';
g_object_name_type = 'name_random'
filename_spilt_code = '_lr_rl_r_is_rad_'
now = timestamp = Date.parse(new Date()) / 1000;

var allow_geshi = 'jpg,jpeg,gif,png,bmp,avi,rm,rmvb,vob,mkv,dat,mpg,mpeg,asf,mp4,torrent,m4v,m4r,webm,ogv,wmv,3gp,mp3,wav,fla,flac,js,css,htm,html,xml,ape,swf,flv,f4v,aac,acc,rar,zip,xls,xlsx,txt,doc,pdf,psd,tif,tiff,crw,mdb';


function send_article_fujian_request(aid)
{
    aid = aid||0;
    var xmlhttp = null;
    if (window.XMLHttpRequest)
    {
        xmlhttp=new XMLHttpRequest();
    }
    else if (window.ActiveXObject)
    {
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

    if (xmlhttp!=null)
    {
        serverUrl = '/include/lib/plupload/get_article_fujian_accessid.php?aid='+aid;
        xmlhttp.open( "GET", serverUrl, false );
        xmlhttp.send( null );
        return xmlhttp.responseText
    }
    else
    {
        alert("Your browser does not support XMLHTTP.");
    }
}


function get_article_fujian_signature(aid)
{
    aid = aid||'';
    body = send_article_fujian_request(aid)
    var obj = eval ("(" + body + ")");
    host = obj['host']
    policyBase64 = obj['policy']
    accessid = obj['accessid']
    signature = obj['signature']
    expire = parseInt(obj['expire'])
    callbackbody = obj['callback']
    key = obj['dir']
    return true;
}


function random_string(len) {
    len = len || 32;
    var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
    var maxPos = chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function get_suffix(filename) {
    pos = filename.lastIndexOf('.')
    suffix = ''
    if (pos != -1) {
        suffix = filename.substring(pos)
    }
    return suffix;
}

function calculate_object_name(filename)
{
    if (g_object_name_type == 'local_name')
    {
        g_object_name += "${filename}"
    }
    else if (g_object_name_type == 'random_name')
    {
        suffix = get_suffix(filename)
        g_object_name = key + random_string(10) + suffix
    }
    else if (g_object_name_type == 'name_random')
    {
        g_object_name += "${filename}" + filename_spilt_code + random_string(10) + suffix
    }
    return ''
}

function set_article_fujian_upload_param(up, filename, aid, ret)
{
    aid = aid||'';
    if (ret == false)
    {
        ret = get_article_fujian_signature(aid)
    }
    g_object_name = key;
    if (filename != '') { suffix = get_suffix(filename)
        calculate_object_name(filename)
    }
    new_multipart_params = {
        'key' : g_object_name,
        'policy': policyBase64,
        'OSSAccessKeyId': accessid,
        'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
        'callback' : callbackbody,
        'signature': signature,
    };
    up.setOption({
        'url': host,
        'multipart_params': new_multipart_params
    });
    up.start();
}
//创建上传对象 上传文章的附件
function makeUploadArticleFujianEven(aid, container, ossHint, selectBtn, uploadBtn) {
    var successNum = 0;
    var uploadNum = 0;
    var uploader = new plupload.Uploader({
        runtimes : 'html5,flash,silverlight,html4',
        browse_button : selectBtn,
        //multi_selection: false,//禁止多选
        container: document.getElementById(container),
        flash_swf_url : 'lib/plupload-2.1.2/js/Moxie.swf',
        silverlight_xap_url : 'lib/plupload-2.1.2/js/Moxie.xap',
        url : 'http://oss.aliyuncs.com',

        filters: {
            mime_types : [ //只允许上传图片和zip文件
                { title : "Image files", extensions : 'jpg,jpeg,gif,png,bmp' }
            ],
            max_file_size : '100000mb', //最大只能上传10mb的文件
            prevent_duplicates : true //不允许选取重复文件
        },

        init: {
            PostInit: function() {
                $('#'+ossHint).html('');
                $('#'+uploadBtn).off().on('click', function() {
                    set_article_fujian_upload_param(uploader, '', aid, false);
                    return false;
                });
            },
            FilesAdded: function(up, files) {
                var tmpHtml = '';
                plupload.each(files, function(file) {
                    tmpHtml += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ')<b></b>'
                        +'<div class="progress"><div class="progress-bar" style="width: 0%"></div></div>'
                        +'</div>';
                    uploadNum ++;
                });
                $('#'+ossHint).html(tmpHtml);
            },
            BeforeUpload: function(up, file) {
                set_article_fujian_upload_param(up, file.name, aid, true);
            },

            UploadProgress: function(up, file) {
                var d = document.getElementById(file.id);
                if(!d || isUndefined(d) ||! isUndefined) {
                    console.log(d);
                    return;
                }
                d.getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                var prog = d.getElementsByTagName('div')[0];
                var progBar = prog.getElementsByTagName('div')[0]
                progBar.style.width= 2*file.percent+'px';
                progBar.setAttribute('aria-valuenow', file.percent);
            },

            FileUploaded: function(up, file, info) {
                if (info.status == 200)
                {
                    successNum ++;
                    if(successNum == uploadNum) { //全部上传成功，关闭当前窗口
                        hideNewBox();
                        msgTis('上传成功');
                        successNum = 0;
                        uploadNum = 0;
                        reLoadThisShareFujian();
                    }
                }
                else if (info.status == 203)
                {
                    msg('上传成功 ,但是回调服务器失败:'+info.response);
                    successNum = 0;
                    uploadNum = 0;
                }
                else
                {
                    msg(info.response);
                }
            },

            Error: function(up, err) {
                successNum = 0;
                uploadNum = 0;
                if (err.code == -600) {
                    msg('文件太大了');
                }
                else if (err.code == -601) {
                    msg('文件后缀不对');
                }
                else if (err.code == -602) {
                    msg('这个文件已经上传过一遍了');
                }
                else
                {
                    msg(err.response);
                }
            }
        }
    });
    uploader.init();
}



