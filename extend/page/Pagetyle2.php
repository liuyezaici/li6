<?php
namespace page;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangyajun <448901948@qq.com>
// +----------------------------------------------------------------------
use think\Paginator;
class Pagetyle2 extends Paginator
{
    //首页
    protected function home() {
        if ($this->currentPage() > 1) {
            return "<a href='" . $this->url(1) . "' title='首页' target=\"_parent\">首页</a>";
        } else {
            return "";
        }
    }
    //上一页
    protected function prev() {
        if ($this->currentPage() > 1) {
            return "<a href='" . $this->url($this->currentPage - 1) . "' title='上一页' target=\"_parent\">上一页</a>";
        } else {
            return "";
        }
    }
    //下一页
    protected function next() {
        if ($this->hasMore) {
            return "<a href='" . $this->url($this->currentPage + 1) . "' title='下一页' target=\"_parent\">下一页</a>";
        } else {
            return"";
        }
    }
    //尾页
    protected function last() {
        if ($this->hasMore) {
            return "<a href='" . $this->url($this->lastPage) . "' title='尾页' target=\"_parent\">尾页</a>";
        } else {
            return "";
        }
    }
    //统计信息
    protected function info(){
        return "<p class='pageRemark'>共<b>" . $this->lastPage .
            "</b>页<b>" . $this->total . "</b>条数据</p>";
    }
    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {
        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];
        $side   = 3;
        $window = $side * 2;
        if ($this->lastPage < $window + 6) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= $window) {
            $block['first'] = $this->getUrlRange(1, $window + 2);
            $block['last']  = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        } elseif ($this->currentPage > ($this->lastPage - $window)) {
            $block['first'] = $this->getUrlRange(1, 2);
            $block['last']  = $this->getUrlRange($this->lastPage - ($window + 2), $this->lastPage);
        } else {
            $block['first']  = $this->getUrlRange(1, 2);
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
            $block['last']   = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        }
        $html = '';
        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }
        if (is_array($block['slider'])) {
            $html .= $this->getUrlLinks($block['slider']);
        }
        if (is_array($block['last'])) {
            $html .= $this->getUrlLinks($block['last']);
        }
        return $html;
    }
    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '%s<div class="pagination">%s %s %s</div>',
                    $this->css(),
                    $this->prev(),
                    $this->getLinks(),
                    $this->next()
                );
            } else {
                return sprintf(
                    '%s<div class="pagination">%s %s %s %s %s %s</div>',
                    $this->css(),
                    $this->home(),
                    $this->prev(),
                    $this->getLinks(),
                    $this->next(),
                    $this->last(),
                    $this->info()
                );
            }
        }
    }
    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return '<a href="' . htmlentities($url) . '" title="第'. $page .'页"  target="_parent">' . $page . '</a>';
    }
    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<p class="pageEllipsis">' . $text . '</p>';
    }
    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<a href="" class="cur" target="_parent">' . $text . '</a>';
    }
    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }
    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';
        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }
        return $html;
    }
    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper($page);
        }
        return $this->getAvailablePageWrapper($url, $page);
    }
    /**
     * 分页样式
     */
    protected function css(){
        return '  <style type="text/css">
            .pagination p{
                margin:0;
                cursor:pointer
            }
            .pagination {
                height: 40px;
                padding: 10px 0px;
                margin: 0 -10px;
            }
            .pagination a {
                display:block;
                float:left;
                margin-right: 8px;
                padding: 0px 5px;
                height: 22px;
                border: 1px solid #dedede;
                border-radius: 5px;
                text-decoration:none;
                color:#808080;
                font-size:12px;
                line-height: 22px;
                margin-bottom: 10px;
            }
            .pagination a:hover{
                color: #666;
                background: #eee;
            }
            .pagination a.cur{
                border:none;
                background:#077ee3;
                color:#fff;
            }
            .pagination p{
                float:left;
                padding: 2px 5px;
                font-size:12px;
                height:24px;
                line-height:22px;
                color:#bbb;
                background:#fcfcfc;
                margin-right:5px;
                border: 0;
            }
            .pagination p.pageRemark{
                border-style:none;
                background:none;
                margin-right:0px;
                padding:4px 0px;
                color:#666;
            }
            .pagination p.pageRemark b{
                color:#777;
            }
            .pagination p.pageEllipsis{
                border-style:none;
                background:none;
                padding:4px 0px;
                color:#808080;
            }
            .dates li {font-size: 14px;margin:20px 0}
            .dates li span{float:right}
        </style>';
    }
}