<?php

namespace app\common\controller;

use app\common\model\Users;
use app\admin\library\Auth;
use think\Config;
use think\Controller;
use think\Hook;
use think\Lang;
use think\Session;

/**
 * 后台控制器基类
 */
class Backend extends Controller
{

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
     * 布局模板
     * @var string
     */
    protected $layout = 'default';

    /**
     * 权限控制类
     * @var Auth
     */
    protected $auth = null;

    /**
     * 快速搜索时执行查找的字段
     */
    protected $searchFields = 'id';

    /**
     * 是否是关联查询
     */
    protected $relationSearch = false;

    /**
     * 是否开启数据限制
     * 支持auth/personal
     * 表示按权限判断/仅限个人
     * 默认为禁用,若启用请务必保证表中存在admin_id字段
     */
    protected $dataLimit = false;
    protected $dataLimitIdent = '';//区分的下级角色
    protected $dataLimitSelf = true;//下级角色是否包含自己
    /**
     * 数据限制字段
     */
    protected $dataLimitField = 'admin_id';

    /**
     * 数据限制开启时自动填充限制字段值
     */
    protected $dataLimitFieldAutoFill = true;

    /**
     * 是否开启Validate验证
     */
    protected $modelValidate = false;

    /**
     * 是否开启模型场景验证
     */
    protected $modelSceneValidate = false;

    /**
     * Multi方法可批量修改的字段
     */
    protected $multiFields = 'status';

    /**
     * 导入文件首行类型
     * 支持comment/name
     * 表示注释或字段名
     */
    protected $importHeadType = 'comment';

	protected $currentAddonName = '';

    /**
     * 引入后台控制器的traits
     */
    use \app\admin\library\traits\Backend;

    public function _initialize()
    {
        $modulename = $this->request->module();
        $controllername = strtolower($this->request->controller());
        $actionname = strtolower($this->request->action());

        $path = str_replace('.', '/', $controllername) . '/' . $actionname;

        // 定义是否AJAX请求
        !defined('IS_AJAX') && define('IS_AJAX', $this->request->isAjax());

        //初始化身份信息
        //$this->auth->id 是用户id
        $this->auth = Auth::instance();

        // 设置当前请求的URI
        $this->auth->setRequestUri($path);
        // token
        $token = $this->request->server('HTTP_TOKEN', $this->request->request('token', \think\Cookie::get('token')));

//        return;
        //未登录 停止执行
        $noLoginStop = function ($showType='html') {
            Hook::listen('admin_nologin', $this);
            $url = Session::get('referer');
            $url = $url ? $url : $this->request->url();
            if($showType == 'html') {
//                print_r('no___login');exit;
                $this->error('请先登录', url('/index/system/login', ['url' => $url]));
            } else {
                output('请先登录吧', [], -100);
            }
        };
        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin))
        {
            //支持token初始化登录
            if($token) {
                $tokenIslogin = $this->auth->init($token);
                if(!$tokenIslogin) {
                    $noLoginStop('json');
                }
            }
            //检测是否登录
            if (!$this->auth->isLogin())
            {
                $noLoginStop('html');
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight))
            {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->checkAuth($path))
                {
                    Hook::listen('admin_nopermission', $this);
                    $this->error('无权访问:'.$path, '');
                }
            }
        }


        // 如果有使用模板布局
        if ($this->layout)
        {
            $this->view->engine->layout('layout/' . $this->layout);
        }

        // 语言检测
        $lang = strip_tags(Lang::detect());


       // $upload = \app\common\model\Config::upload();

        // 上传信息配置后
        //Hook::listen("upload_config_init", $upload);

        $config = [
           // 'upload'         => $upload,
            'modulename'     => $modulename,
            'controllername' => $controllername,
            'actionname'     => $actionname,
            'moduleurl'      => rtrim(url("/{$modulename}", '', false), '/'),
            'language'       => $lang,
            'fastadmin'      => Config::get('fastadmin'),
            'referer'        => Session::get("referer"),
        ];
        $config = array_merge($config, Config::get("view_replace_str"));

        //Config::set('upload', array_merge(Config::get('upload'), $upload));

        // 配置信息后
        Hook::listen("config_init", $config);
        //加载当前控制器语言包
        $this->loadlang($controllername);
        //渲染配置信息
        $this->assign('config', $config);
        //渲染权限对象
        $this->assign('auth', $this->auth);
        //渲染管理员对象
        $this->assign('admin', Users::get($this->auth->id));
        $this->assign('header',   $this->view->fetch(APP_PATH . 'user/view/header.php'));
        $this->assign('footer',   $this->view->fetch(APP_PATH . 'user/view/footer.php'));
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
    /**
     * 生成查询所需要的条件,排序方式
     * @param mixed $searchfields 快速查询的字段
     * @param boolean $relationSearch 是否关联查询
     * @param array $changeAgent 是否转义代理为商家 如:['agent_id', 'seller_id']
     * @return array
     */
    protected function buildparams($changeAgent=false)
    {

        $sort = $this->request->get("sort", "id");
        $order = $this->request->get("order", "DESC");
        $where = [];
        $tableName = '';
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            $where[$tableName . $this->dataLimitField] = [ 'in', $adminIds];
        }
        //如果需要修改代理
        foreach ($where as $k=>$tmpV) {
            if($changeAgent && $tmpV[0] == $changeAgent[0]) {
                unset($where[$k]);
                $agentId = join(',', $tmpV[2]);
                $sellerIds = (new Auth())->getAgentSellerIds($agentId);
                $where[$k][$changeAgent[1]] = ['in', $sellerIds];
            }
        }
        return [$where, $sort, $order];
    }

    /**
     * 获取数据限制的管理员ID
     * 禁用数据限制时返回的是null
     * @return mixed
     */
    protected function getDataLimitAdminIds()
    {
        if (!$this->dataLimit || !$this->dataLimitIdent || !$this->dataLimitField)
        {
            return null;
        }
       //在管理商家时，管理员也只能看管理员，不能看商家 由管理员列表单独加身份
        if ($this->auth->isSuperAdmin())
        {
          return null;
        }
        $adminIds = [];
        if ($this->dataLimit)
        {
            $adminIds = $this->auth->getChildrenAdminIds($this->dataLimitSelf, $this->dataLimitIdent);
            if(!is_array($adminIds))$adminIds = explode(',', $adminIds);
        }
        return $adminIds;
    }

    /**
     * Selectpage的实现方法
     *
     * 当前方法只是一个比较通用的搜索匹配,请按需重载此方法来编写自己的搜索逻辑,$where按自己的需求写即可
     * 这里示例了所有的参数，所以比较复杂，实现上自己实现只需简单的几行即可
     *
     */
    protected function selectpage()
    {
        $adminIds = $this->getDataLimitAdminIds();
        $list = [];
        $total = $this->model->count();
        if ($total > 0)
        {
            if (is_array($adminIds))
            {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->field("password,salt", true)->select();
        }
        //这里一定要返回有list这个字段,total是可选的,如果total<=list的数量,则会隐藏分页按钮
        return json_encode(['code'=>1,'list' => $list]);
    }

	public function model(){
		return $this->model ? : false;
	}

	public function qrcode($url = '', $label = '', $view = true){
		$url = input('url') ? : $url;
		$label = input('label') ? : $label;
		$view = input('view') ? : $view;
		return create_qrcode($url, $label, $view);
	}
}
