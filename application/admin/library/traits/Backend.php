<?php

namespace app\admin\library\traits;

trait Backend
{

    /**
     * 查看
     */
    public function index()
    {
        $args = func_get_args();
        $model = isset($args[0]) ? $args[0] : '';
        $buildparams = isset($args[1]) ? $args[1] : false;
        $returned = isset($args[2]) ? $args[2] : false;
        if(!$model) $model = $this->model;
        $page = input('page', 1, 'int');
        $pageSize = input('page_size', 10, 'int');
        if(!$buildparams) $buildparams = $this->buildparams();
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isPost())
        {

            list($where, $sort, $order) = $buildparams;
            $total = $model
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $model
                    ->where($where)
                    ->order($sort, $order)
                    ->page($page, $pageSize)
                    ->select();

			$list = collection($list)->toArray();
			if($returned) return json_output($total, $list);
            return json($result);
        }
       print_r($this->view->fetch());
    }


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost())
        {
			$args = func_get_args();
			$returned = isset($args[1]) ? $args[1] : false;

            $params = !isset($args[0]) || $args[0] === false || is_null($args[0]) ? $this->request->post("row/a") : $args[0];
            if ($params)
            {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill)
                {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
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
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->isUpdate(false)->allowField(true)->save($params);
                    if ($result !== false)
                    {
						if($returned)return $this->model->id;
                        $this->success();
                    }
                    else
                    {
						if($returned)return $this->model->getError();
                        $this->error($this->model->getError());
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
       print_r($this->view->fetch());
    }

    /**
     * 编辑
     */
    public function edit($id = NULL)
    {
        $row = $this->model->get($id);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
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
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false)
                    {
						if($returned)return $row->id;
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

    //批量修改
    public function multi_edit($id = NULL) {
        $row = $this->model->all(['id'=> ['in', $id]]);
        if (!$row)
            $this->error(__('No Results were found'));
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
                        $row[0]->validate($validate);
                    }
					$results = array();
                    foreach ($row as $v) {
                        $result = $v->allowField(true)->save($params);
                        if ($result !== false)
                        {
                            $results[] = $v->id;
                        }
                        else
                        {
                            if($returned)return $v->getError();
                            $this->error($v->getError());
                        }
                    }
					if($returned)return $results;
                    $this->success();

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
        $this->view->assign("row", $row[0]);
       print_r($this->view->fetch());
    }

    /**
     * 获取信息
     */
    public function get($id = NULL)
    {
        $row = $this->model->get(['id' => $id]);
        if (!$row) $this->error(__('No Results were found'));
        return $this->result($row);
    }
    /**
     * 删除
     */
    public function del($id = "")
    {
        $returned = '';
        if ($id)
        {
			$args = func_get_args();
			$returned = isset($args[1]) ? $args[1] : false;

            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds))
            {
                $count = $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
			$okList = [];
            $list = $this->model->where($pk, 'in', $id)->select();
            $count = 0;
            foreach ($list as $k => $v)
            {
				$result = $v->delete();
				if($result){
					$okList[] = $v;
                	$count += $result;
				}
            }
            if ($count)
            {
				if($returned)return $okList;
                $this->success();
            }
            else
            {
				if($returned)return __('No rows were deleted');
                $this->error(__('No rows were deleted'));
            }
        }
		if($returned)return __('Parameter %s can not be empty', 'ids');
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 真实删除
     */
    public function destroy($id = "")
    {
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            $count = $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        if ($id)
        {
            $this->model->where($pk, 'in', $id);
        }
        $count = 0;
        $list = $this->model->onlyTrashed()->select();
        foreach ($list as $k => $v)
        {
            $count += $v->delete(true);
        }
        if ($count)
        {
            $this->success();
        }
        else
        {
            $this->error(__('No rows were deleted'));
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 还原
     */
    public function restore($id = "")
    {
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        if ($id)
        {
            $this->model->where($pk, 'in', $id);
        }
        $count = $this->model->restore('1=1');
        if ($count)
        {
            $this->success();
        }
        $this->error(__('No rows were updated'));
    }

    /**
     * 批量更新
     */
    public function multi($id = "")
    {
        $id = $id ? $id : $this->request->param("ids");
        if ($id)
        {
            if ($this->request->has('params'))
            {
                parse_str($this->request->post("params"), $values);
                $values = array_intersect_key($values, array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
                if ($values)
                {
                    $adminIds = $this->getDataLimitAdminIds();
                    if (is_array($adminIds))
                    {
                        $this->model->where($this->dataLimitField, 'in', $adminIds);
                    }
                    $this->model->where($this->model->getPk(), 'in', $id);
                    $count = $this->model->allowField(true)->isUpdate(true)->save($values);
                    if ($count)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error(__('No rows were updated'));
                    }
                }
                else
                {
                    $this->error(__('You have no permission'));
                }
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 导入
     */
    protected function import()
    {
        $file = $this->request->request('file');
        if (!$file)
        {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath))
        {
            $this->error(__('No results were found'));
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath))
        {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath))
            {
                $PHPReader = new \PHPExcel_Reader_CSV();
                if (!$PHPReader->canRead($filePath))
                {
                    $this->error(__('Unknown data format'));
                }
            }
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
        foreach ($list as $k => $v)
        {
            if ($importHeadType == 'comment')
            {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            }
            else
            {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        $PHPExcel = $PHPReader->load($filePath); //加载文件
        $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
        $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
        $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
        $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);
        for ($currentRow = 1; $currentRow <= 1; $currentRow++)
        {
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++)
            {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $fields[] = $val;
            }
        }
        $insert = [];
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++)
        {
            $values = [];
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++)
            {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $values[] = is_null($val) ? '' : $val;
            }
            $row = [];
            $temp = array_combine($fields, $values);
            foreach ($temp as $k => $v)
            {
                if (isset($fieldArr[$k]) && $k !== '')
                {
                    $row[$fieldArr[$k]] = $v;
                }
            }
            if ($row)
            {
                $insert[] = $row;
            }
        }
        if (!$insert)
        {
            $this->error(__('No rows were updated'));
        }
        try
        {
            $this->model->saveAll($insert);
        }
        catch (\think\exception\PDOException $exception)
        {
            $this->error($exception->getMessage());
        }

        $this->success();
    }

}
