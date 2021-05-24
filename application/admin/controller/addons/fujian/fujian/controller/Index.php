<?php

namespace app\admin\addon\fujian\controller;

use app\admin\addon\fujian\model\Fujian;
use app\admin\addon\fujian\model\Fujian as FujianModel;
use app\common\controller\Backend;
use app\common\model\Users as userModel;
use fast\Random;
use think\Config;
use fast\Addon;

/**
 * 附件管理
 *
 * @icon fa fa-users
 * @remark 所有的组件都独自上传和管理自己的附件
 */
class Index extends Backend
{

    protected $model = null;
    protected $noNeedRight = ['upload']; //上传附件不需要分配权限


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new FujianModel();
//       echo $this->auth->identity;exit;
        $this->view->assign('allStatus', json_encode(userModel::getAdminAllStatusForRadio()));
    }

  
    /**
     * 编辑
     */
    public function edit($id = NULL)
    {
        $row = FujianModel::get(['id' => $id]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                if ($params['password'])
                {
                    $params['salt'] = Random::alnum(); 
                    $params['password'] = userModel::encryptPassword($params['password'], $params['salt']);
                }
                else
                {
                    unset($params['password'], $params['salt']);
                }
                //这里需要针对username和email做唯一验证
                $adminValidate = Addon::validate($this->addonName);
                $adminValidate->rule([
                    'email'    => 'email|unique:admin,email,' . $row->id
                ]);
                $result = $row->validate('v1.fujian.Fujian.edit')->save($params);
                if ($result === false)
                {
                    $this->error($row->getError());
                }

                $this->success();
            }
            $this->error();
        }
        $grouplist = $this->auth->getMyGroupId($row['id']);
        $groupids = [];
        foreach ($grouplist as $k => $v)
        {
            $groupids[] = $v['id'];
        }
        $this->view->assign('allStatus', FujianModel::getFujianAllStatus());
        $this->view->assign("row", $row);
        $this->view->assign("groupids", $groupids);
       print_r($this->view->fetch());
    }

    /**
     * 删除
     */
    public function del($id = "")
    {
        if ($id)
        {
            FujianModel::removeFileImg($id);
            $this->success();
        }
        $this->error();
    }
    /**
     * 删除应用的文件
     */
    public function delAddonFile($id =NULL)
    {

        $addonName = input('addon', '');
        $addonSourceId = input('addon_source_id', 0);
        $fileId = input('fileId', 0);
        FujianModel::removeAddonOneFile($addonName, $addonSourceId, $fileId);
        $this->success('删除成功');
    }

    /**
     * 批量更新
     * @internal
     */
    public function multi($id = "")
    {
        // 管理员禁止批量操作
        $this->error();
    }

    /**
     * 上传文件
     *  上传url /adppp/fujian/index/upload/?addon=Viptc
     */
    public function upload()
    {
        ini_set('post_max_size','20M');
        ini_set('upload_max_filesize','20M');
        $fileTypeLimit = input('filetype', '');//mp3,wma
        $addonName = input('addon', '');
        $isImg = input('isImg', 0, 'intval');
        $addonSourceId = input('addon_source_id', 0);
        $uniqueMethod = input('uniqueMethod', '');//一个sid限制上传一张图的方法
        if(!$addonName) $this->error('no addon_name');
        Config::set('default_return_type', 'json');
        $fileObj = $this->request->file();
        $fileNameArray = array_keys($fileObj);
        if(!$fileNameArray)  return $this->error('获取不到图片的信息,请编辑后再上传');
        $fileName = $fileNameArray[0];
        $file = $this->request->file($fileName);
        if (empty($file))
        {
            return $this->error(__('No file upload or server upload limit exceeded'));
        }
        $addonClass = Addon::getModel($addonName);
        //只允许上传一张图片 deleteOldCover
        if($uniqueMethod) {
            //更新原数据的附件索引
            if(method_exists($addonClass, $uniqueMethod)) {
                $addonClass->$uniqueMethod($addonSourceId);
            } else {
                print_r("{$addonName}{$uniqueMethod}!unique_method_exists");
            }
        }
        $fileInfo = $file->getInfo();
//        print_r($fileInfo);exit;
        //Array
        //(
        //    [name] => 9  心內心外.mp3
        //    [type] => audio/mp3
        //    [tmp_name] => D:\xampp\tmp\php3753.tmp
        //    [error] => 0
        //    [size] => 9773244
        //)
        $fileSize = $fileInfo['size'];
        $fileType = $fileInfo['type'];
        if($fileSize > 20*1024*1024) {
            if(file_exists($fileName)) unlink($fileName);
            return $this->error('文件最大20M');
        }
        if($fileTypeLimit) {
            $geshiArray = explode('/', $fileType);
            $geshi = end($geshiArray);
            if(!in_array($geshi, explode(',', $fileTypeLimit))) {
                return $this->error('文件格式只支持:'.$fileTypeLimit);
            }
            if(strstr($fileTypeLimit, 'mp3')) {
                //获取歌曲时长
                $mp3 = new \fast\Mp3File($fileInfo['tmp_name']);
                $seconds = $mp3->getDurationEstimate();
                $billsec = $mp3->getDuration();
                $fileInfo['songTime'] = $seconds;
            }
        }
        $fileData = FujianModel::saveFile($this->auth->id, $fileInfo, $addonName, $addonName, $addonSourceId, true, '', $isImg);
        if(method_exists($addonClass, 'uploadSuccess')) {
            $addonClass->uploadSuccess($addonSourceId, $fileInfo + $fileData);
        }
        $this->success('上传成功', null, $fileData);
    }


    /**
     * 窗口管理文件 ?addon=Viptc&sid=123
     */
    public function windowFiles()
    {
        $addon = input('addon', '', 'trim');
        $sid = input('sid', 0, 'trim');
        if(!$addon) $this->error('未提交addon');
        if(!$sid) $this->error('未提交sid');
        if ($this->request->isPost())
        {
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            $id = input('id/d');
            $title = input('title/s');
            $result = Fujian::getAddonFiles($addon, $sid, $page, $pageSize, '', $id, $title);
            $list =  $result[0];
            $total = $result[1];
            return json_output($total, $list);
        }
        $this->view->assign('addon', $addon);
        $this->view->assign('sid', $sid);
        print_r($this->view->fetch());
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isPost())
        {
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($id = input('id/d'))  $where['id'] = $id;
            if($title = input('title/s'))  $where['title'] = ['like', '%'. trim($title) .'%'];
//            print_r(json_encode($where));exit;
            if($whereMore) $where = array_merge($where, $whereMore);
            $total = $this->model
                ->where($where)
                ->count();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();
//            echo FujianModel::getlastsql();exit;
            $fileClass = new \fast\File;
            foreach ($list as $k => &$v)
            {
                $v['addon_title'] = Addon::getAddonTitleByPath($v['addon_name']);
                $v['filesize'] = $fileClass->formatBytes($v['filesize']);
            }
            unset($v);
            return json_output($total, $list);
        }
       print_r($this->view->fetch());
    }
}
