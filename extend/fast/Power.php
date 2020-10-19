<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614 <weibo.com/luofei614>
// +----------------------------------------------------------------------
// | 修改者: anuo (本权限类在原3.2.3的基础上修改过来的)
// +----------------------------------------------------------------------

namespace fast;

use think\Db;
use think\Config;
use think\Session;
use think\Request;
use app\admin\model\AuthRule;

/**
 * 权限认证类
 * 功能特性：
 * 1，是对规则进行认证，不是对节点进行认证。用户可以把节点当作规则名称实现对节点进行认证。
 *      $Power=new Power();  $power->checkPower('规则名称','用户id')
 * 2，可以同时对多条规则进行认证，并设置多条规则的关系（or或者and）
 *      $Power=new Power();  $power->checkPower('规则1,规则2','用户id','and')
 *      第三个参数为and时表示，用户需要同时具有规则1和规则2的权限。 当第三个参数为or时，表示用户值需要具备其中一个条件即可。默认为or
 * 4，支持规则表达式。
 * 表示用户的分数在5-100之间时这条规则才会通过。
 */
class Power
{

    /**
     * @var object 对象实例
     */
    protected static $instance;
    protected $rules = [];

    /**
     * 当前请求实例
     * @var Request
     */
    protected $request;
    //默认配置
    protected $config = [
        'auth_on'           => 1, // 权限开关
        'auth_type'         => 'anytime', // 认证方式，anytime为实时认证；when_login为登录认证。
        'auth_group'        => 'auth_group', // 用户组数据表名
        'auth_rule'         => 'auth_rule', // 权限规则表
        'auth_user'         => 'user', // 用户信息表
    ];

    /**
     * 类架构函数
     * Power constructor.
     */
    public function __construct()
    {
        // 判断是否需要验证权限
        if ($auth = Config::get('auth'))
        {
            $this->config = array_merge($this->config, $auth);
        }
        // 初始化request
        $this->request = Request::instance();
    }

    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return Power
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance))
        {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * 检查权限
     * @param       $authPath   string|array    需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param       $uid    int             认证用户的id
     * @param       string  $relation       如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * @param       string  $mode           执行验证的模式,可分为url,normal
     * @return bool               通过验证返回true;失败返回false
     */
    public function checkPower($authPath, $uid, $relation = 'or', $mode = 'url')
    {
        if (!$this->config['auth_on'])
        {
            return true;
        }
        // 获取用户需要验证的所有有效规则列表
        $rulelist = $this->getRuleList($uid);
        if (in_array('*', $rulelist)) return true;

        if (is_string($authPath))
        {
            $authPath = strtolower($authPath);
            if (strpos($authPath, ',') !== false)
            {
                $authPath = explode(',', $authPath);
            }
            else
            {
                $authPath = [$authPath];
            }
        }
		$authPath[] = str_ireplace('/index/index', '/index', $authPath[0]);
        $authPath = array_unique($authPath);
//        print_r($rulelist);exit;

        $list = []; //保存验证通过的规则名
        if ('url' == $mode)
        {
            $REQUEST = unserialize(strtolower(serialize($this->request->param())));
        }
        foreach ($rulelist as $rule)
        {
            $query = preg_replace('/^.+\?/U', '', $rule);
            if ('url' == $mode && $query != $rule)
            {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $rule = preg_replace('/\?.*$/U', '', $rule);
                if (in_array($rule, $authPath) && $intersect == $param)
                {
                    //如果节点相符且url参数满足
                    $list[] = $rule;
                }
            }
            else
            {
                if (in_array($rule, $authPath))
                {
                    $list[] = $rule;
                }
            }
        }
        if ('or' == $relation && !empty($list))
        {
            return true;
        }
        $diff = array_diff($authPath, $list);
        if ('and' == $relation && empty($diff))
        {
            return true;
        }

        return false;
    }

    /**
     * 根据用户id获取用户组,返回值为数组
     * @param  $uid int     用户id
     * @return array       用户所属的用户组 array(
     *              array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
     *              ...)
     */
    public function getMyGroupId($uid)
    {
        // 执行查询
        return db('users')->where('id', $uid)->value('groupid');
    }

    /**
     * 获得权限规则列表
     * @param integer $uid 用户id
     * @return array
     */
    public function getRuleList($uid)
    {
        static $_rulelist = []; //保存用户验证通过的权限列表
        if (isset($_rulelist[$uid]))
        {
            return $_rulelist[$uid];
        }
        if ('when_login' == $this->config['auth_type'] && Session::has('_rule_list_' . $uid))
        {
            return Session::get('_rule_list_' . $uid);
        }

        // 读取用户规则节点
        $ids = $this->getRuleIds($uid);
//        print_r($uid);
//        print_r($ids);
//        exit;
        if (empty($ids))
        {
            $_rulelist[$uid] = [];
            return [];
        }

        // 筛选条件
        $modelRule = new AuthRule();
        $where = [];
        if ($ids != '*')
        {
            $where['id'] = ['in', $ids];
        }
        //读取用户组所有权限规则
        $this->rules = $modelRule->where($where)->field('id,pid,icon,auth_path,title,ismenu')->select();
//        print_r($modelRule->getLastSql());exit;
//        print_r(json_encode($this->rules));exit;
        //循环规则，判断结果。
        $rulelist = []; //
        if ($ids == '*')
        {
            $rulelist[] = "*";
        }
        foreach ($this->rules as $rule)
        {
            //只要存在就记录
            $rulelist[$rule['id']] = strtolower($rule['auth_path']);
        }
        $_rulelist[$uid] = $rulelist;
        //登录验证则需要保存规则列表
        if ('when_login' == $this->config['auth_type'])
        {
            //规则列表结果保存到session
            Session::set('_rule_list_' . $uid, $rulelist);
        }
        return array_unique($rulelist);
    }

    //获取我的所有权限id
    public function getRuleIds($uid=0)
    {
        $groupId = $this->getMyGroupId($uid);
//        print_r('$groupId:'.$groupId);
//        exit;
        // 执行查询
       $powerIds = Db('authGroup')->where('id', $groupId)->value('rules');
       return $powerIds;
    }
    //获取我的所有权限
    public function getMyRules($uid=0)
    {
        $modelRule = new AuthRule();
        $powerIds = $this->getRuleIds($uid);
        if($powerIds == '*') {
            return  $modelRule->select();
        } else {
            return  $modelRule->where([
                'id'=> ['in', $powerIds]
            ])->select();
        }

    }



}
