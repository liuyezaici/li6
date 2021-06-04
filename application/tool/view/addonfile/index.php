<style>
    #addonFileList {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        right: 0;
    }
    #addonFileList .uploadBtn {
        overflow: hidden;
        width: 34px;
        height: 32px;
        margin: 0 0 0 auto;
    }
    #addonFileList .uploadBtn input {
        background: transparent none no-repeat scroll 0 0;
        background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAHrSURBVFhH7VZtsYNADKwEJFRCJdQBBQVIqAQcPAlIQEIlVEIlIKFvl9kcAe7Kx5v+eDPsTGZKssltjtyV04ED/w55nmewK6x2VsHOonwHWCAry/KnKIoO9k5Y+xUhKHpB8ddkMQp5wKZ+WqPUvwOL31DQuu7wzC2/KBwA3x3xIIa7pdB+oGiGYk8VfVGMQkmA14j/pii590Hv3Dq/yr0I8FuXl8m9DereOqnlXoVJ7r5dQCLfvRXZ3AXy7FV0cm0Du1aBp1wzUFhKnG8Axvnh8K5rBMQrknjEktPMYojzXT9jhRU3AWacidkJClBSv7BZTIBb3HhREQT83IlwMmBxEXQi6C8Vnn3ebKOjx4XodzyzpAhC9QNX7gFwWudUeI8V+7C42ZKIyrj4PewCHvzEV3LP4AvA/LYGUeB8PHbg9LsM3nC0maQCnNZkBwRngsmws3L6blhjzfUL/ny4+aBiD7kWMRUg9yK4hvKGPywUqOVcfWnsEQBeOJr4PQw3H1xg1Z0P3mYB4PpZGX83wGn/ejwFyUE0bBGAuH3M9PzorJBkBBmnlYo57Q3io+n2AjwvYmzMf0XxbokPOgK8LGwnptaK1mMiYI0l75cZQLrBaiSxA3ZH1aO/ZBay2CfjdoPLhb/70XrgwD6cTr/a0ujNl+T23wAAAABJRU5ErkJggg==");
        border: 0 none;
        box-shadow: 0 0 0 0;
        color: transparent;
        padding-left: 100px;
        text-indent: 100px; /* 防止火狐下tab落焦到当前input时会暴露input */
        height: 100%;
        cursor: pointer;
        display: block;
        outline: none;
    }
    #addonFileList ul {
        padding: 0;
        margin: 0;
    }
    #addonFileList  .list ul li {
        list-style-type: none;
        display: inline-block;
        min-width: 100px;
        max-width: 200px;
        width: 15%;
        border: 1px dashed #dedede;
        margin-right: 10px;
        margin-bottom: 10px;
        padding: 8px 5px;
        min-height: 100px;
        overflow: hidden;
        position: relative;
    }
    #addonFileList  .list ul li .num {
        position: absolute;
        right: 0;
        top: 0;
        display: inline-block;
        background-color: #dedede;
        border-radius: 4px;
        padding: 2px 4px;
        color: #aaa;
        font-size: 12px;
    }
    #addonFileList  .list ul li:hover {
        border: 1px solid #dedede;
        background-color: #eef6ff;
    }
    #addonFileList  .list .thumbnail {
        max-width: 80px;
        max-height: 50px;
    }
    #addonFileList  .list .filename {
        width: 100%;
        min-height: 50px;
        resize: none;
        background-color: transparent;
        border: 0;
        outline: none;
    }
    #addonFileList  .list .delBtn {
        position: absolute;
        right: 0;
        bottom: 0;
        color: #ff2222;
        font-size: 12px;
    }
</style>
<link href="/assets/libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" media="all" />
<link rel="stylesheet" href="https://js.li6.cc/assets/libs/lr/lrBox.css" />
<script type="text/javascript" src="/assets/js/require.min.js"></script>
<script src="/assets/libs/jquery/dist/jquery-2.2.1.min.js"></script>
<body>
    <div id="addonFileList">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="text-right">
                    <div class="uploadBtn">
                        <input type="file" multiple="multiple" name="fileInput" class="btn btn-xs btn-info" />
                    </div>
                </div>
                <div class="well well-sm list">
                    <ul>
                        <?php
                        if(!$list) {
                            echo '<li>没有附件</li>';
                        }
                        foreach ($list as $v) {
                            ?>
                            <li class="item" title="<?=$v['filename']?>&#13;Size:<?=$v['filesize']?> &#13;<?=$v['addtime']?>">
                                <span class="num"><?=$v['id']?></span>
                            <?php
                            if(in_array($v['geshi'], ['png', 'gif', 'jpeg', 'webp', 'jpg', 'bmp'])) {
                            ?>
                                <img src="<?=$v['fileurl']?>" class="thumbnail" />
                            <?php
                            } else {
                            ?>
                                <textarea class="filename" readonly><?=$v['filename']?> </textarea>
                            <?php
                            }?>
                                <div class="input-group-btn">
                                    <a href="javascript: void(0);" target="_self" class="btn btn-xs btn-info insetBtn" data-url="<?=$v['fileurl']?>">插入</a>
                                    <a href="javascript: void(0);" target="_self" class="btn btn-xs btn-warning copyBtn" data-url="<?=$v['fileurl']?>">复制</a>
                                    <a href="javascript: void(0);" target="_self" class="delBtn glyphicon glyphicon-remove" data-id="<?=$v['id']?>"></a>
                                </div>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="well well-sm ">
            <?=$menu;?>
        </div>
    </div>
    <script>


        require.config({
            paths: {
                jquery: '/assets/libs/jquery/dist/jquery-2.2.1.min',
                lrBox: 'https://js.li6.cc/assets/libs/lr/box.ver/lrBox.1.1',
                'front': '/assets/index/front',
            }
        });
        require(['jquery', 'lrBox', 'front'], function ($, lrBox, front) {
            let table_ = '<?=$table?>';
            let sid_ = '<?=$sid?>';
            let addonKey = '<?=$addonKey?>';
            let addonVal = '<?=$addonVal?>';
            let sidKey = '<?=$sidKey?>';
            let fileList = $('#addonFileList');

            // 获取文件二进制数据
            function getFileBlob(file, cb) {
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function (e) {
                    var base64Data = this.result;
                    let file_size = e.total;
                    let file_mine = base64Data.match(/^data\:([^;]+);/)[1];
                    if (typeof cb === "function") {
                        cb.call(this, this.result, file_size, file_mine);
                    }
                };
            }
            fileList.find('.uploadBtn input').on('change', function (e) {
                e.stopPropagation();
                let inputName = $(this).attr('name');
                var fileNum = e.target.files.length;
                $.each(e.target.files, function (n, file_) {
                    let fileName = file_.name;
                    getFileBlob(file_, function (base64, file_size, file_mine) {
                        let blobFile = front.base64toBlob(base64);
                        let postOpt = {
                            'postData': {
                                sid: sid_,
                                filename: fileName,
                                table: table_,
                            },
                            'url': '/tool/Addonfile/upload',
                        };
                        if(sidKey)  postOpt['postData']['sidKey'] = sidKey;
                        if(addonKey)  postOpt['postData']['addonKey'] = addonKey;
                        if(addonVal)  postOpt['postData']['addonVal'] = addonVal;
                        postOpt['postData'][inputName] = blobFile;
                        postOpt['successKey'] = 'code';
                        postOpt['successVal'] = '1';
                        postOpt['successFunc'] = function (res) {
                            lrBox.msgTsf(res.msg);
                            if( fileNum == n+1) {
                                window.location.reload();
                            }
                        };
                        front.postAndDone(postOpt);
                    });
                });
            });
            fileList.find('.insetBtn').click(function () {
                let url = $(this).attr('data-url');
                if(parent.window.editorInsertUrl) {
                    parent.window.editorInsertUrl(url);
                }
            });
            fileList.find('.copyBtn').click(function () {
                let url = $(this).attr('data-url');
                front.copyText(url);
                lrBox.msgTisf('success');
            });
            fileList.find('.delBtn').click(function () {
                let id = $(this).attr('data-id');
                let item = $(this).closest('.item');
                // lrBox.msgConfirm('Delete？', 'Yes', 'No', function () {
                //
                // }, function () {
                //     lrBox.removeNewBox();
                // });
                $.post('/tool/addonfile/del/id/'+id,
                    {
                        table: table_,
                    },
                    function (res) {
                        lrBox.removeNewBox();
                        lrBox.msgTisf(res.msg);
                        if(res.code == 1) {
                            item.remove();
                        }
                    },
                    'json');
            });

        });
    </script>

<!-- 引入方式 <iframe style="width: 100%; height: 600px;border: 0;overflow:hidden;" src="/tool/AddonFile?addonKey=addon&addonVal=article&sidKey=sid&sidVal=123&page=5&page_size=24&table=lr_article_fujian"></iframe>  -->
</body>