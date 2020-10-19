<!DOCTYPE html>
<html>
<head>
{include file="common/meta" /}
</head>
<base target="_parent" />
<body>
{include file="common/header" /}
{include  file="common/banner" currentBanner='author' /}
{include file="songs/songs_top_singer"  /}
<style>
    .author_list .list_item {
        display: inline-block;
        margin-bottom: 15px;
        float: left;
        width: auto;
        padding: 0 5px;
    }
    .author_list .list_item .input-group {
        padding-top: 5px;
    }
    .author_list .list_item .input-group .btn + .btn {
        border-left: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
</style>
<div class="front_body container">
    {include file="songs/songs_public_top" keyword='' t_='2' /}
    <div class="panel panel-info">
        <div class="panel-heading" style="position: relative;">
            <?=$topTitle?>
        </div>
        <div class="panel-body author_list">
            <ul class="list-group">
                <?php
                foreach($songIds as $v) {
                ?>
                <li class="list_item">
                    <span class="btn btn-default btn-xs">
                        <a href="/juzi/songs/uri/<?=$v['uri']?>" target="_blank" ><?=$v['title']?></a> <?=$v['singer']?>
                    </span>
                </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
</div>
<script>
    var currentZm = '<?=$typeName?>';
    $('#top_phb').find("a[data='"+ currentZm +"']").removeClass('btn-default').addClass('btn-success');
</script>
{include file="common/footer" /}
</body>
</html>