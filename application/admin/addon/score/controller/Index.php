<?php
namespace app\admin\addon\score\controller;
use app\common\controller\Backend;
use fast\Addon;
use think\Db;


/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'score';
        $this->model = Addon::getModel($this->addonName);
    }
    public function index()
    {
        if ($this->request->isPost()){
            list($whereMore, $sort, $order) = $this->buildparams();
            $map = [];
            $filter = json_decode($this->request->param('filter'));
            if(isset($filter->uid)) $map['user_id'] = $filter->uid;
            if(isset($filter->username)) {
                $uid = model('users')->getfieldbyusername($filter->username, 'id');
                if($uid) $map['user_id'] = $uid;
            }
            //不能加where条件，因为where里的多余条件无法剔除 如：username=>xxx。
            $total = $this->model->where($map)->count();
            $list = $this->model->where($map)
                ->limit($offset,$limit)
                ->order($sort,$order)
                ->select();
            $userClass = model('users');
            foreach ($list as $k => $v)
            {
                $list[$k]['username'] = $userClass->getUserName($v['user_id']);
            }

            return json_output($total, $list);

        }
       print_r($this->view->fetch());
    }


    //充值积分
    public function trade($ids = NULL) {
        $row = [];
        $row['user_id'] = 0;
        if($ids) {
            $row = $this->model->get($ids);
            if (!$row)  $this->error(__('No Results were found'));
        }
        if ($this->request->isPost())
        {
            $args = func_get_args();
            $returned = isset($args[2]) ? $args[2] : false;

            $params = !isset($args[1]) || $args[1] === false || is_null($args[1]) ? $this->request->post("row/a") : $args[1];
            if ($params)
            {
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        //$name = basename(str_replace('\\', '/', get_class($this->model)));
                        $model = str_replace('\\', '/', get_class($this->model));
                        $temp = explode('/', $model);
                        $name = '';
                        foreach($temp as $v){
                            if($v == 'model')continue;
                            if(preg_match("/^v\d+$/", $v)){
                                $name .= $v . '.';
                            }else{
                                if($name)$name .= $v . '.';
                            }
                        }
                        $name .= basename($model);
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validate($validate);
                    }

                    if(empty($params['score'])) $this->error('积分不能为空');
                    if(!is_numeric($params['score'])) $this->error('积分只能是整数');
                    if(empty($params['user_id'])) $this->error('user_id 不能为空');
                    if(!is_numeric($params['user_id'])) $this->error('user_id 只能是整数:'. $params['user_id']);
                    $uid = $params['user_id'];
                    $admin = $this->model->getAdminId();
                    if($uid == $admin) $this->error('不能给管理员转积分');
                    //转移积分
                    if($params['score'] > 0) {
                        $fromUid = $admin;
                        $toUid = $uid;
                    } else {
                        $fromUid = $uid;
                        $toUid = $admin;
                        $params['score'] = abs($params['score']);//转正数才能进行转移
                    }
                    $result = $this->model->tradeUserScore($fromUid, $toUid, $params['score'], 'score', $this->model->tradeTypeByhand, $params['memo']);
//                    print_r($params);exit;
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
//        print_r($row);exit;
        $username = model('users')->getfieldbyid($row['user_id'], 'username');
        $row['user_name'] = $username;
        $this->view->assign("row", $row);
       print_r($this->view->fetch());
    }

    //积分日志列表
    public function scorelist() {
        $this->model = Db::name('score_log');
        list($whereMore, $sort, $order) = $this->buildparams();
        $total = $this->model->where($where)->count();
        $list = $this->model->where($where)
            ->limit($offset,$limit)
            ->order($sort,$order)
            ->select();
        if ($this->request->isPost()){
            $userClass = model('users');
            foreach ($list as $k => $v)
            {
                $list[$k]['from_username'] = $userClass->getUserName($v['from_uid']);
                $list[$k]['to_username'] = $userClass->getUserName($v['to_uid']);
            }
            return json_output($total, $list);
        }
       print_r($this->view->fetch());
    }

    //积分兑换优惠券
    public function exchangeCoupon() {
        $coupon_id = input('coupon_id');
        if(!$coupon_id) $this->error('No coupon_id');
        $status = Addon::getModel('coupon')->getExchangeScore($coupon_id);
        if(!$status) throw new \Exception('增加优惠券使用次数失败:'. $status,-1);
    }
}
