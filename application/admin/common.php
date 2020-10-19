<?php

use app\common\model\Category;
use fast\Form;
use fast\Tree;
use think\Db;

if (!function_exists('build_select')) {

    /**
     * 生成下拉列表
     * @param string $name
     * @param mixed $options
     * @param mixed $selected
     * @param mixed $attr
     * @return string
     */
    function build_select($name, $options, $selected = [], $attr = [])
    {
        $options = is_array($options) ? $options : explode(',', $options);
        $selected = is_array($selected) ? $selected : explode(',', $selected);
        return Form::select($name, $options, $selected, $attr);
    }
}

if (!function_exists('build_radios')) {

    /**
     * 生成单选按钮组
     * @param string $name
     * @param array $list
     * @param mixed $selected
     * @return string
     */
    function build_radios($name, $list = [], $selected = null)
    {
        $html = [];
        $selected = is_null($selected) ? key($list) : $selected;
        $selected = is_array($selected) ? $selected : explode(',', $selected);
        foreach ($list as $k => $v) {
            $html[] = sprintf(Form::label("{$name}-{$k}", "%s {$v}"), Form::radio($name, $k, in_array($k, $selected), ['id' => "{$name}-{$k}"]));
        }
        return '<div class="radio">' . implode(' ', $html) . '</div>';
    }
}

if (!function_exists('build_checkboxs')) {

    /**
     * 生成复选按钮组
     * @param string $name
     * @param array $list
     * @param mixed $selected
     * @return string
     */
    function build_checkboxs($name, $list = [], $selected = null)
    {
        $html = [];
        $selected = is_null($selected) ? [] : $selected;
        $selected = is_array($selected) ? $selected : explode(',', $selected);
        foreach ($list as $k => $v) {
            $html[] = sprintf(Form::label("{$name}-{$k}", "%s {$v}"), Form::checkbox($name, $k, in_array($k, $selected), ['id' => "{$name}-{$k}"]));
        }
        return '<div class="checkbox">' . implode(' ', $html) . '</div>';
    }
}


if (!function_exists('build_category_select')) {

    /**
     * 生成分类下拉列表框
     * @param string $name
     * @param string $type
     * @param mixed $selected
     * @param array $attr
     * @return string
     */
    function build_category_select($name, $type, $selected = null, $attr = [], $header = [])
    {
        $tree = Tree::instance();
        $tree->init(Category::getCategoryArray($type), 'pid');
        $categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $categorydata = $header ? $header : [];
        foreach ($categorylist as $k => $v) {
            $categorydata[$v['id']] = $v['name'];
        }
        $attr = array_merge(['id' => "c-{$name}", 'class' => 'form-control selectpicker'], $attr);
        return build_select($name, $categorydata, $selected, $attr);
    }
}

if (!function_exists('build_toolbar')) {

    /**
     * 生成表格操作按钮栏
     * @param array $btns 按钮组
     * @param array $attr 按钮属性值
     * @return string
     */
    function build_toolbar($btns = NULL)
    {
        $auth = \app\admin\library\Auth::instance();
        $controller = str_replace('.', '/', strtolower(think\Request::instance()->controller()));
        $btns = is_array($btns) ? $btns : explode(',', $btns);
        $index = array_search('delete', $btns);
        if ($index !== FALSE) {
            $btns[$index] = 'del';
        }
        $html = [];
        foreach ($btns as $k => $datum) {
            $title_ = isset($datum['title']) ? $datum['title'] : '';
            $powerName = isset($datum['power']) ? $datum['power'] : '';
            $class_ = isset($datum['class']) ? $datum['class'] : 'btn';
            $icon = isset($datum['icon']) ? '<i class="' . $datum['icon'] . '"></i>' : '';
            $click = isset($datum['click']) ? 'onclick="' . $datum['click'] . '"' : '';
            $href_ = isset($datum['href']) ? $datum['href'] : 'javascript: void(0)';
            $target_ = isset($datum['target']) ? $datum['target'] : '_self';
            //如果未定义或没有权限
            $controller = \fast\Str::trimStr($controller, '/index');
            $powerName = trim($powerName, '/');
//            echo "{$controller}/{$powerName}\n";
            if ($powerName && ! $powerName !== 'refresh' && !$auth->checkAuth("{$controller}/{$powerName}")) {
                continue;
            }
            $html[] = '<a href="' . $href_ . '" class="' . $class_ . '" target="'. $target_ .'" '. $click .' >' . $icon . ' ' . $title_ . '</a>';
        }
        return implode(' ', $html);
    }
}

if (!function_exists('build_heading')) {

    /**
     * 生成页面Heading
     *
     * @param string $path 指定的path
     * @return string
     */
    function build_heading($path = NULL, $container = TRUE)
    {
        $title = $content = '';
        if (is_null($path)) {
            $action = request()->action();
            $controller = str_replace('.', '/', request()->controller());
            //大写控制器替换成_ 如 goodsType =>  goods_type
            $path = strtolower(preg_replace("/([a-zA-Z0-9]+)([A-Z])/", "$1_$2", $controller) . ($action && $action != 'index' ? '/' . $action : ''));
        }
        // 根据当前的URI自动匹配父节点的标题和备注
        $data = Db('authRule')
            ->where('auth_path', $path)
            ->whereOr('auth_path', $path.'/index')
            ->field('title,remark,pid')->find();
        if ($data) {
            $title = __($data['title']);
            $content = __($data['remark']);
            $pid = $data['pid'];
        }
        $parentHtml = '';
        if($pid) {
            $parentRule =  Db::name('authRule')->where('id', $pid)->field('title')->find();
            if($parentRule) {
                $parentHtml = '<b>'.$parentRule['title'] .'</b> &raquo;';
            }
        }
        if (!$content && !$title) return '';  //要有标题或备注才显示子标题
        $result = '<div class="panel-lead">'. $parentHtml .'<b>' . $title . '</b> <br /> ' . $content . '</div>';
        if ($container) {
            $result = '<div class="panel-heading">' . $result . '</div>';
        }
        return $result;
    }
}
