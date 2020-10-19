<?php
namespace app\admin\addon\emailcode\controller;
use app\common\model\Users;
use fast\Addon;
use fast\Str;
use app\common\controller\Backend;
use app\admin\addon\emailcode\model\Emailcode as EmailcodeModel;


/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new EmailcodeModel();
        $this->view->assign('allValType', json_encode(EmailcodeModel::getValAllTypesForRadio()));
    }


    //添加验证码
    public function add() {
        if ($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['email'])) $this->error('名字不能为空');
            $value = isset($postData['value']) ? $postData['value'] : '';
//            print_r($postData);exit;
            if(is_array($value)) {
                $valueArray = ($value);
                $newVal = [];
                foreach ($valueArray as $keyName => $valArray) {
                    foreach ($valArray as $index => $val_) {
                        $newVal[$index][$keyName] = $val_;
                    }
                }
                sort($newVal);
                $newVal = json_encode($newVal);
                $postData['value'] = $newVal;
            }
            $postData['cuid'] = $this->auth->id;
            $postData['ctime'] = time();
            EmailcodeModel::insert($postData);

            $newSid = EmailcodeModel::getLastInsID();
            //附件更新sid
            if($newSid && ($value && EmailcodeModel::valTypeIsPic($postData['keytype']))) {
                $fujianModel = Addon::getModel('fujian');
                if(!$fujianModel) $this->error('未安装fujian组件');
                $fujianModel->updateSid($newSid, [$value]);
            }
            $this->success();
        }
        return parent::add();
    }

    //修改验证码
    public function edit($id = NULL) {
        $row = EmailcodeModel::get($id);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isPost())
        {
            $args = func_get_args();
            $returned = isset($args[2]) ? $args[2] : false;
            $params = !isset($args[1]) || $args[1] === false || is_null($args[1]) ? $this->request->post("row/a") : $args[1];
            $value = isset($params['value']) ? $params['value'] : '';
//            print_r($value);
            if(is_array($value)) {
                $valueArray = ($value);
                $newVal = [];
                foreach ($valueArray as $keyName => $valArray) {
                    foreach ($valArray as $index => $val_) {
                        $newVal[$index][$keyName] = $val_;
                    }
                }
//                sort($newVal); //不能sort排序 前端已经排序好了
//                print_r($newVal);
//                exit;
                $newVal = json_encode($newVal);
                $params['value'] = $newVal;
            }
            if ($params)
            {
                try
                {
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        if($returned)return $result;
                        $this->success();
                    }
                    else
                    {
                        if($returned)return $row->getError();
                        $this->error($row->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    if($returned)return $e->getMessage();
                    $this->error($e->getMessage());
                }
            }
            if($returned)return __('Parameter %s can not be empty', '');
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $this->view->assign("row", $row);
       print_r($this->view->fetch());
    }
    
    //验证码记录
    public function index()
    {
        if ($this->request->isPost()){
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($id = input('id/d'))  $where['id'] = $id;
            if($email = input('email/s'))  $where['email'] = ['like', '%'. trim($email) .'%'];
            if($typeid = input('typeid/s'))  $where['typeid'] = $typeid;
//            print_r(json_encode($where));exit;
            if($whereMore) $where = array_merge($where, $whereMore);

            $total = EmailcodeModel::where($where)->count();
            $list = EmailcodeModel::where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();

            foreach ($list as $n =>&$v) {
                $v['typenames'] =  EmailcodeTypeModel::getTypeName($v['typeid']);
                unset($v);
            }
            return json_output($total, $list);

        }
       print_r($this->view->fetch());
    }
}
