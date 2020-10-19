<?php
/**
 * 分页系统
 */
namespace Func;

class Divpage
{
    private $baseurl = '';//基本链接
    private $menustyle = 1; //分页样式
    private $tableName = '';
    private $pagenow = 1;//当前页数
    private $pagesize = 10;//页面大小
    private $pagemenu = '';//菜单样式
    private $pagedata = array();
    private $fields = '*';//字段
    private $pageinfo = '';//当前页面的相关信息
    private $menu_script = 'ago';//设置分页样式
    private $indexField = 'id';//排序字段
    private $orderBy = 'desc';//排序字段
    private $whereSql = '1';//查询条件


    public function __construct( $tableName, $baseurl = '', $fields = '*', $pagenow = 1, $pagesize = 0, $menustyle = 1,
                                 $scriptFunc='ago', $indexField='id', $orderBy='DESC', $whereSql='1'){
        $pagenow = !is_numeric($pagenow)  ? 1 :$pagenow;
        $pagenow = $pagenow < 1 ?1 :$pagenow;
        $this->tableName = $tableName;
        $this->pagesize = ($pagesize == 0)?10:$pagesize;//单页数量
        $this->pagenow = $pagenow;//现在的页面 从第几页开始计数
        $this->baseurl = $baseurl;//翻页地址
        $this->fields = $fields;//字段
        $this->menustyle = $menustyle;//分页样式
        $this->menu_script = $scriptFunc;//分页样式
        $this->indexField = $indexField;
        $this->orderBy = $orderBy;
        $this->whereSql = $whereSql;
    }

    //得到分页数据（优化分页核心Sql）
    /*
     * $version 版本
     * 1为旧版本
     * 2为新版本
     * */
    public function getDivPage(){
        //排序字段
        $this->pagedata = DbBase::getPageData($this->fields , $this->tableName, $this->pagenow, $this->pagesize, $this->indexField, $this->orderBy, $this->whereSql);//返回数据
//        print_r( $this->pagedata);
//        exit;
        //得到页数据
        $count = DbBase::getValue("SELECT count(*) FROM {$this->tableName} WHERE {$this->whereSql}");//返回记录的总行数
//        print_r($count);
//        exit;
        //得到导航菜单
        $lastpage = ceil( $count / $this->pagesize );//向上舍入为整数  将行数划断
        $lastpage = $lastpage == 0 ? 1:$lastpage;
        $this->pageinfo = array(
            "msg" => "您要查找的记录",
            "total" => $count,//总记录数
            "pagenow" => $this->pagenow,//当前页的值
            "fpage" => 0 < $this->pagenow - 1 ? $this->pagenow - 1 : 1,//上一页
            "npage" => $lastpage < $this->pagenow + 1 ? $lastpage : $this->pagenow + 1,//下一页
            "lastpage" => $lastpage,//最后一页
            "pagecount" => $lastpage,//页面数量
            "pagesize" => $this->pagesize,//页面容量（当前页面显示的记录数）
        );
        $this->setMenuStyle();//设置分页代码
    }

    //分页导航
    public function getMenu(){
        return $this->pagemenu;
    }

    //得到页面数据
    public function getPage(){
        return $this->pagedata;
    }

    //得到数据总数
    public function getTotal(){
        return $this->pageinfo['total'];
    }

    public function getPageInfo(){
        return $this->pageinfo;
    }

    //分割数字为单页样式
    protected function splitNums($num=0) {
        $numHtmlAr = [];
        for($i=0;$i<strlen($num);$i++)
        {
            $tmpNum = substr($num,$i,1);
            $numHtmlAr[] = '<span class="page_'.$tmpNum.'">'.$tmpNum.'</span>';
        }
        return join('', $numHtmlAr);
    }
    //设置样式导航条
    public function setMenuStyle() {
        switch ( $this->menustyle)
        {
            case 1 :
                if($this->pageinfo['pagecount'] <=1) return '';
                //计算分页导航
                $t = $this->pageinfo['pagenow']-5;
                if($t<1) $t = 1;
                $pagelist = "<div class='new_pages'>";
                $pagelist .= "<a href='javascript:void(0);' target='_self'>共". $this->pageinfo['total'] ."条记录</a><a href='javascript:void(0);' target='_self'>第". $this->pageinfo['pagenow'] ."/". $this->pageinfo['pagecount'] ."页</a>";
                if($this->pageinfo['pagenow'] !=1) {
                    $pagelist .= "<a href=\"". str_replace('{%u}', 1,$this->baseurl ) ."\" target=\"_self\">首页</a>";
                }
                for ($i=$t;$i < min(($t + 10),$this->pageinfo['lastpage']+1);$i++){
                    if ($i == $this->pageinfo['pagenow']){
                        $pagelist .= "<a class=\"current\" href=\"". str_replace('{%u}', $i,$this->baseurl )."\" target=\"_self\">$i</a>";
                    }else{
                        $pagelist .= "<a href=\"". str_replace('{%u}', $i,$this->baseurl ) ."\" target=\"_self\">{$i}</a>";
                    }
                }
                if ($i < $this->pageinfo['lastpage']){
                    $pagelist .= "<a class=\"ellipsis\">…</a> <a href=\"". str_replace('{%u}', $this->pageinfo['lastpage'], $this->baseurl ) ."\" target=\"_self\">".$this->pageinfo['lastpage']."</a>";
                }

                if($this->pageinfo['pagenow']!=$this->pageinfo['pagecount']) {
                    $pagelist .= "<a href=\"". str_replace('{%u}', $this->pageinfo['lastpage'],$this->baseurl ) ."\" target=\"_self\">尾页</a>";
                }
                $pagelist .= "</div> ";
                $this->pagemenu = $pagelist;
                break;
            case 2 : //前端列表专用分页
                $t = $this->pageinfo['pagenow']-5;
                if($t<1) $t = 1;
                $pagelist = "<div class='new_pages'>";
                if($this->pageinfo['pagecount'] <=1) return '';
                for ($i=$t;$i < min(($t + 10), $this->pageinfo['lastpage']+1);$i++){
                    if ($i == $this->pageinfo['pagenow']){
                        $pagelist .= "<span class='item current page_{$i}'> <a class=\"page_\" href=\"". str_replace('{%u}', $i,$this->baseurl )."\" target=\"_self\">$i</a> </span>";
                    } else {
                        $pagelist .= "<span class='item'><a class='page_' href=\"". str_replace('{%u}', $i,$this->baseurl ) ."\" target=\"_self\">{$i}</a> </span>";
                    }
                }
                if($this->pageinfo['pagenow'] != $this->pageinfo['pagecount']) {
                    $pagelist .= "<span class='item'> <a href=\"". str_replace('{%u}', $this->pageinfo['lastpage'],$this->baseurl ) ."\" target=\"_self\" class='page_'>尾页</a></span>";
                }
                $pagelist .= "<span class='item'> 到第<input type=\"text\" maxlength=\"3\" class='enter_page' value='". $this->pageinfo['pagenow'] ."'>页 <span class='btn btn-xs btn-default' onclick=\"gotopage($(this).prev().val());\">确定</span>";
                $pagelist .= "</div> ";
                $this->pagemenu = $pagelist;
                break;
            case 'index' :
                if($this->pageinfo['pagecount'] <=1) return '';
                //计算分页导航
                $t = $this->pageinfo['pagenow']-5;
                if($t<1) $t = 1;
                $pagelist = "<a href='javascript:void(0);' target='_self'>共". $this->splitNums($this->pageinfo['total']) ."</a>";
                if($t > 1) {
                    $pagelist .= "<a href=\"". str_replace('{%u}', 1,$this->baseurl ) ."\" target=\"_self\">". $this->splitNums(1) ."..</a>";
                }
                for ($i=$t;$i < min(($t + 10),$this->pageinfo['lastpage']+1);$i++){
                    if ($i == $this->pageinfo['pagenow']){
                        $pagelist .= "<a class=\"current\" href=\"". str_replace('{%u}', $i,$this->baseurl )."\" target=\"_self\">". $this->splitNums($i) ."</a>";
                    }else{
                        $pagelist .= "<a href=\"". str_replace('{%u}', $i,$this->baseurl ) ."\" target=\"_self\">". $this->splitNums($i) ."</a>";
                    }
                }
                if ($i < $this->pageinfo['lastpage']){
                    $pagelist .= "<a href=\"". str_replace('{%u}', $this->pageinfo['lastpage'], $this->baseurl ) ."\" target=\"_self\">..". $this->splitNums($this->pageinfo['lastpage'])."</a>";
                }

                $this->pagemenu = $pagelist;
                break;
            case 21 : // 前端列表专用分页 url跳转，只显示页码 -支持wap端
                $t = $this->pageinfo['pagenow']-5;
                if($t<1) $t = 1;
                $pagelist = "<div class='new_pages'>";
                if($this->pageinfo['pagecount'] <=1) return '';
                for ($i=$t;$i < min(($t + 5), $this->pageinfo['lastpage']+1);$i++){
                    if ($i == $this->pageinfo['pagenow']){
                        $pagelist .= "<a class=\"btn btn-xs active\" href=\"". str_replace('{%u}', $i,$this->baseurl )."\" target=\"_self\">$i</a>";
                    }else{
                        $pagelist .= "<a href=\"". str_replace('{%u}', $i,$this->baseurl ) ."\" target=\"_self\" class='btn btn-xs btn-default'>{$i}</a>";
                    }
                }
                $pagelist .= "</div> ";
                $this->pagemenu = $pagelist;
                break;
            case 9 ://ajax翻页
                if($this->pageinfo['pagecount'] <=1) return '';
                $t = $this->pageinfo['pagenow']-5;
                if($t<1) $t = 1;
                $pagelist = "<div class='new_pages'>";
                $pagelist .= "<span class='item'> ". $this->pageinfo['total'] ."条记录</span> ";
                for ($i=$t;$i < min(($t + 10),$this->pageinfo['lastpage']+1);$i++){
                    if ($i == $this->pageinfo['pagenow']){
                        $pagelist .= "<span class='item current'> <a class=\"page_\" href=\"javascript:".$this->menu_script."(". $i .");\" target=\"_self\">$i</a> </span>";
                    }else{
                        $pagelist .= "<span class='item'>  <a class='page_' href=\"javascript:".$this->menu_script."(". $i .");\" target=\"_self\">{$i}</a> </span>";
                    }
                }
                if($this->pageinfo['pagenow'] != $this->pageinfo['pagecount']) {
                    $pagelist .= "<span class='item'> <a class='page_' href=\"javascript:".$this->menu_script."(". $this->pageinfo['lastpage'] .");\" target=\"_self\">尾页</a>  </span>";
                }
                $pagelist .= "<span class='item'> <input type=\"text\" maxlength=\"10\" class='enter_page' value='". $this->pageinfo['pagenow'] ."'> 
                <span class='btn btn-xs btn-default' onclick=\"".$this->menu_script."($(this).prev().val());\">前往</span>";
                $pagelist .= "</div> ";
                $this->pagemenu = $pagelist;
            break;
            case 91 ://ajax翻页 只显示页码
                if($this->pageinfo['pagecount'] <=1) return '';
                $t = $this->pageinfo['pagenow']-5;
                if($t<1) $t = 1;
                $pagelist = "<div class='new_pages'>";
                $pagelist .= "<span class='item'> ". $this->pageinfo['total'] ."条记录</span>";
                for ($i=$t;$i < min(($t + 10),$this->pageinfo['lastpage']+1);$i++){
                    if ($i == $this->pageinfo['pagenow']){
                        $pagelist .= "<span class='item current'> <a class=\"page_\" href=\"javascript:".$this->menu_script."(". $i .");\" target=\"_self\">$i</a></span>";
                    }else{
                        $pagelist .= "<span class='item'> <a class='page_' href=\"javascript:".$this->menu_script."(". $i .");\" target=\"_self\">{$i}</a></span>";
                    }
                }
                
                $pagelist .= "</div> ";
                $this->pagemenu = $pagelist;
                break;
            default :
                return '';
        }
    }
}
