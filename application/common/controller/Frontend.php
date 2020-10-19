<?php

namespace app\common\controller;

use app\admin\library\Auth;
use think\Config;
use think\Controller;
use think\Hook;
use think\Lang;

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

    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];

    /**
     * 权限Auth
     * @var Auth 
     */
    protected $auth = null;

    public function _initialize()
    {
        //移除HTML标签
        $this->request->filter('strip_tags');
        $modulename = $this->request->module();
        $controllername = strtolower($this->request->controller());
        $actionname = strtolower($this->request->action());

        // 如果有使用模板布局
        if ($this->layout)
        {
            $this->view->engine->layout('layout/' . $this->layout);
        }
        $this->auth = Auth::instance();

        $modulename = $this->request->module();
        $controllername = strtolower($this->request->controller());
        $actionname = strtolower($this->request->action());

        // token
        $token = $this->request->server('HTTP_TOKEN', $this->request->request('token', \think\Cookie::get('token')));

        $path = str_replace('.', '/', $controllername) . '/' . $actionname;
        // 设置当前请求的URI
        $this->auth->setRequestUri($path);
        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin))
        {
            //初始化
            if($token) $this->auth->init($token);
            //检测是否登录
            if (!$this->auth->isLogin())
            {
                $this->error('请先登录', '/');
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight))
            {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->checkAuth($path))
                {
                    $this->error(__('You have no permission'));
                }
            }
        }
        else
        {
            // 如果有传递token才验证是否登录状态
            if($token) $this->auth->init($token);
        }

        $this->view->assign('user', $this->auth->getToken());

        // 语言检测
        $lang = strip_tags(Lang::detect());

        $site = Config::get("site");


        // 配置信息
		$jsname = file_path(APP_PATH . $this->request->module(), $controllername, '.js', 'assets/js');		
		if(!$jsname){
			$jsname = 'frontend/' . str_replace('.', '/', $controllername);
		}else{
			$jsname = substr('../../'.APP_DIR.'/'.file_url($jsname), 0, -3);
		}
        $config = [
            'site'           => array_intersect_key($site, array_flip(['name', 'cdnurl', 'version', 'timezone', 'languages'])),
            'modulename'     => $modulename,
            'controllername' => $controllername,
            'actionname'     => $actionname,
            'jsname'         => $jsname,
            'moduleurl'      => rtrim(url("/{$modulename}", '', false), '/'),
            'language'       => $lang
        ];
        $config = array_merge($config, Config::get("view_replace_str"));


        // 配置信息后
        Hook::listen("config_init", $config);
        // 加载当前控制器语言包
        $this->loadlang($controllername);
        $this->assign('site', $site);
        $this->assign('config', $config);
    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function loadlang($name)
    {
		$file = file_path(APP_PATH . $this->request->module(), $name, '.php', 'lang/'.Lang::detect());	
		if($file)Lang::load($file);
		/*$temp = explode('.', str_replace(array('/', '\\'), '.', $name));
		$temp1 = array_pop($temp);		
		$temp0 = implode('/', $temp);
		$file = APP_PATH . $this->request->module() . '/' . $temp0 . '/lang/' . Lang::detect() . '/' . $temp1 . '.php';
		if(!is_file($file))$file = APP_PATH . $this->request->module() . '/lang/' . Lang::detect() . '/' . str_replace('.', '/', $name) . '.php';
        Lang::load($file);*/
    }

    /**
     * 渲染配置信息
     * @param mixed $name 键名或数组
     * @param mixed $value 值 
     */
    protected function assignconfig($name, $value = '')
    {
        $this->view->config = array_merge($this->view->config ? $this->view->config : [], is_array($name) ? $name : [$name => $value]);
    }

	public function qrcode($url = '', $label = '', $view = true){
		$url = input('url') ? : $url;
		$label = input('label') ? : $label;
		$view = input('view') ? : $view;
		return create_qrcode($url, $label, $view);
	}
}
