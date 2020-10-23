<?php
//前台模版 文章 文献
use Func\Api;
use Func\Str;
use Func\Users;
use Func\Cache;
use App\Model\Article as Model;
class Article extends Api
{
    public function __construct()
    {
        parent::__construct();
    }


    final function read() {
        $users = new Users();
        $userId = $users->getUserAttrib('userId');
        $sid = $this->getOption('id', 0, 'int');
        if(!is_numeric($sid)) {
            Message::Show('缺少文章sid');
            exit;
        }
        $articleInfo = Model::getArticle($sid, 'a_id,a_title,a_adduid,a_addtime,a_typeid,a_hit,a_typeid,a_content,a_status');
        if(!$articleInfo) {
            Message::Show('文章不存在');
            exit;
        }
        //浏览+1
        if(!Cache::ifExist('li6_article_read',$sid)) {
            $newdata = [];
            $newdata['a_hit'] = $articleInfo['a_hit'] + 1;
            Model::updateArticle($sid , $newdata);
        }
        if(!$articleInfo['a_content']) $articleInfo['a_content'] = '-';
        $articleInfo['a_content'] = Str::tohtml($articleInfo['a_content']);
        if(substr($articleInfo['a_content'], 0, 4) =='[md]') {
            $articleInfo['a_content'] = substr($articleInfo['a_content'], 4);
            //markdown
            include_once(ROOT_PATH.'/include/lib/markdown/Markdown.php');
            include_once(ROOT_PATH.'/include/lib/markdown/MarkdownExtra.php');
            $articleInfo['a_content'] = MarkdownExtra::defaultTransform($articleInfo['a_content']);
        } else {
            //html to markdown
            $savePath = ROOT_PATH .'/include/lib/HTMLToMarkdown/';
            include_once($savePath.'/ElementInterface.php');
            include_once($savePath.'/Element.php');
            include_once($savePath.'/Environment.php');
            include_once($savePath.'/HtmlConverterInterface.php');
            include_once($savePath.'/HtmlConverter.php');
            include_once($savePath.'/ConfigurationAwareInterface.php');
            include_once($savePath.'/Configuration.php');
            include_once($savePath.'/Converter/ConverterInterface.php');
            foreach (glob($savePath . '/Converter/*.php') as $oldFile) {
                require_once $oldFile;//引入文件
            }
            $converter = new HtmlConverter();
            $articleInfo['a_content'] = $converter->convert($articleInfo['a_content']);//旧版html转markdown
        }
        $arr = $articleInfo;
        $arr['author'] = Users::getUserNick($articleInfo['a_adduid']);
        $arr['userId'] = $userId;
        $arr['typeName'] = Model::getTypeName($articleInfo['a_typeid']);
        $arr['header'] = $this -> readTemp('../header.php', $arr);
        $arr['footer'] = $this -> readTemp('../footer.php', $arr);
        return $this->readTemp('', $arr);//设置模板

    }

}
