<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>LR主页</title>
    <meta name="keywords" content="LR个人主页">
    <meta name="description" content="李大爷个人主页">
    <?=$header?>
    <link href="/resource/front/css/index.css" rel="stylesheet" media="all" />
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="list_data">
                        <dt>
                            <?php
                            if($typeId) echo '<a href="/" target="_self">首页</a> &raquo;';
                            ?> <?=$to_title?>
                            <div id="show_search_form" data-form_class="search_article_form" data-name="keyword" data-value="<?=$keyword?>" data-width="200" data-maxlen="20"
                                 data-class="searchkey" data-submit="&lt;i class=&#34;glyphicon glyphicon-search&#34;&gt;&lt;&#47;i&gt;搜索" data-url="/" data-submit_class="btn"> </div>
                        </dt>
                        <ul>
                            <?php
                            foreach($newArticles as $n=>$v) {
                                ?>
                            <li>
                                <div class="row">
                                    <div class="col-md-2 col-sm-6 col-xs-12"> <span class="cate">『<a  href="/?type=<?=$v['a_typeid']?>" target="_self"> <?=$v['typeName']?> </a>』</span> </div>
                                    <div class="col-md-7 col-sm-6 col-xs-12"> <span class="title">  <a href="/?s=article/read&id=<?=$v['a_id']?>"> <?=$v['a_title']?></a> </span> </div>
                                    <div class="col-md-3 col-sm-12 col-xs-12"> <span class="add_time"><?=$v['a_addtime']?></span> </div>
                                </div>
                            </li>
                            <?php
                            }
                            ?>
                        </ul>
                        <div class="index_page_bar">
                            <?=$articlePages?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="right_bar">
                        <dl>
                            <dt class="line2">关键词</dt>
                            <div class="rightContent">
                               <form action="/" method="get" class="input-group" target="_self">
                                   <input name="keyword" class="form-control" value="<?=$keyword?>" />
                                   <span class="input-group-btn">
                                       <button type="submit" class="btn btn-md btn-default">搜索</button>
                                   </span>
                               </form>
                            </div>
                        </dl>
                        <dl>
                            <dt class="line2">category</dt>
                            <div class="rightContent">
                                <ul class="cateList">
                                    <?php
                                    foreach($allTypes as $n=>$tmpType) {
                                        ?>
                                        <li>
                                            <a href="/?type=<?=$tmpType['t_id']?>" target="_self" class="btn btn-xs"> <?=$tmpType['t_title']?></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </dl>
                        <dl>
                            <dt class="line3">工具</dt>
                            <div class="rightContent">
                                <ul class="toolList">
                                    <li>
                                        <span class="btn btn-md btn-info">
                                            <a href="/tool/code.html" class="badge">特殊符号</a>
                                        </span>
                                        <span class="btn btn-md btn-success">
                                            <a href="/tool/color.html" class="badge">颜色表</a>
                                        </span>
                                        <span class="btn btn-md btn-warning">
                                            <a href="https://cs.li6.cc" class="badge">生活常识</a>
                                        </span>
                                        <span class="btn btn-md btn-primary">
                                            <a href="https://wz123.cc" class="badge">网址123</a>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?=$footer?>