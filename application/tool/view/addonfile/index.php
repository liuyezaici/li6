<style>
    .addonFileList {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        right: 0;
    }
    .addonFileList ul {
        padding: 0;
        margin: 0;
    }
    .addonFileList  .list ul li {
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
    .addonFileList  .list ul li .num {
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
    .addonFileList  .list ul li:hover {
        border: 1px solid #dedede;
        background-color: #eef6ff;
    }
    .addonFileList  .list .thumbnail {
        max-width: 80px;
        max-height: 50px;
    }
    .addonFileList  .list .filename {
        width: 100%;
        min-height: 50px;
        resize: none;
        background-color: transparent;
        border: 0;
    }
</style>
<link href="/assets/libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" media="all" />
<link rel="stylesheet" href="https://js.li6.cc/assets/libs/lr/lrBox.css" />
<script type="text/javascript" src="/assets/js/require.min.js"></script>
<script src="/assets/libs/jquery/dist/jquery-2.2.1.min.js"></script>
<body>
    <div class="addonFileList">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="well well-sm list">
                    <ul>
                        <?php
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
                                <textarea class="filename"><?=$v['filename']?> </textarea>
                            <?php
                            }?>
                                <div class="input-group-btn">
                                    <a href="javascript: void(0);" target="_self" class="btn btn-xs btn-info insetBtn" data-url="<?=$v['fileurl']?>">插入</a>
                                    <a href="javascript: void(0);" target="_self" class="btn btn-xs btn-warning delBtn" data-id="<?=$v['id']?>">删除</a>
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
            }
        });
        require(['jquery', 'lrBox'], function ($, lrBox) {
            let table_ = '<?=$table?>';
            let fileList = $('.addonFileList');
            fileList.find('.insetBtn').click(function () {
                let url = $(this).attr('data-url');
                if(parent.window.editorInsertUrl) {
                    parent.window.editorInsertUrl(url);
                }
            });
            fileList.find('.delBtn').click(function () {
                let id = $(this).attr('data-id');
                let item = $(this).closest('.item');
                lrBox.msgConfirm('Delete？', 'Yes', 'No', function () {
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
                }, function () {
                    lrBox.removeNewBox();
                });

            });


        });
    </script>

</body>