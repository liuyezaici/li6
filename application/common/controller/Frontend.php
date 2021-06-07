<?php

namespace app\common\controller;

use app\common\library\Auth;
use fast\RDS;
use think\Config;
use think\Controller;
use think\Hook;
use think\Db;
use think\Lang;
use think\Loader;
use think\Session;
use think\Validate;
use app\index\model\txtTool;

/**
 * 前台控制器基类
 */
class Frontend extends Controller
{


    /**
     * 布局模板
     * @var string
     */
    protected $layout = '';
    protected $keys = '';

    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = ['*'];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['*'];

    /**
     * 权限Auth
     * @var Auth
     */
    protected $auth = null;

    public function _initialize()
    {
//        new RDS();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
        $modulename = $this->request->module();
        $controllername = Loader::parseName($this->request->controller());
        $actionname = strtolower($this->request->action());

        // 如果有使用模板布局
        if ($this->layout) {
            $this->view->engine->layout('layout/' . $this->layout);
        }
        $this->auth = Auth::instance();

        $path = str_replace('.', '/', $controllername) . '/' . $actionname;
        // 设置当前请求的URI
        $this->auth->setRequestUri($path);
        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {
            //检测是否登录
            if (!$this->auth->isLogin()) {
                Hook::listen('admin_nologin', $this);
                $url = Session::get('referer');
                $url = $url ? $url : $this->request->url();
                if ($url == '/') {
                    $this->redirect('/index/?user/login', [], 302, ['referer' => $url]);
                    exit;
                }
                $this->error(__('Please login first'), url('/index/?user/login', ['url' => $url]));
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法是否有对应权限
                if (!$this->auth->check($path, $this->auth->id)) {
                    Hook::listen('admin_nopermission', $this);
                    $this->error(__('You have no permission'), '');
                }
            }
        }

        // 语言检测
        $lang = strip_tags($this->request->langset());

        $upload = \app\common\model\Config::upload();

        // 上传信息配置后
        Hook::listen("upload_config_init", $upload);

        // 配置信息
        $config = [
            'upload'         => $upload,
            'modulename'     => $modulename,
            'controllername' => $controllername,
            'actionname'     => $actionname,
            'jsname'         => 'frontend/' . str_replace('.', '/', $controllername),
            'moduleurl'      => rtrim(url("/{$modulename}", '', false), '/'),
            'language'       => $lang
        ];
        $config = array_merge($config, Config::get("view_replace_str"));

        Config::set('upload', array_merge(Config::get('upload'), $upload));

        // 配置信息后
        Hook::listen("config_init", $config);
        // 加载当前控制器语言包
        $this->loadlang($controllername);


        $this->assign('config', $config);
    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function loadlang($name)
    {
        $name =  Loader::parseName($name);
        Lang::load(APP_PATH . $this->request->module() . '/lang/' . $this->request->langset() . '/' . str_replace('.', '/', $name) . '.php');
    }

    /**
     * 渲染配置信息
     * @param mixed $name  键名或数组
     * @param mixed $value 值
     */
    protected function assignconfig($name, $value = '')
    {
        $this->view->config = array_merge($this->view->config ? $this->view->config : [], is_array($name) ? $name : [$name => $value]);
    }

    /**
     * 刷新Token
     */
    protected function token()
    {
        $token = $this->request->param('__token__');

        //验证Token
        if (!Validate::make()->check(['__token__' => $token], ['__token__' => 'require|token'])) {
            $this->error(__('Token verification error'), '', ['__token__' => $this->request->token()]);
        }

        //刷新Token
        $this->request->token();
    }
}
