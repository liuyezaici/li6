<?php
//路由类
class router {
    public $request = array();
    /*
     * 运行
     * /system/code/a/b/c/d 等于 /?s=system/code&args=a/b/c/d
     * */
    //$request /system/code/a/b/c/d
    public function run($request) {
        $request = trim($request, '/');
        $this->request = $request;
        $req_array = $args = explode('/',$request);//对请求的url转换成数组
        $array = array(
            'm' => isset($req_array[0]) ? $req_array[0] : '',
            'show' => isset($req_array[1]) ? $req_array[1] : '',
            'args' => $req_array,
        );
        return $array;
    }

    /*
     * 获取路由
     * 如果使用了路由功能，那么在$_REQUEST会有提交 router 的参数。
     * 这里的方法就是返回路由的第几段
     * 例：$_REQUEST['router'] = 'system/code/a/b/c/d'
     * $segment = $router->get_segment(); //返回 a;
     * $segment = $router->get_segment(0); //返回 system;
     * $segment = $router->get_segment(1); //返回 code;
     * */
    public function get_segment($index=2){
        $request =  $this->request;
        $req_array = $args = explode('/',$request);
        return isset($req_array[$index]) ? $req_array[$index] : $req_array[0];
    }
    /*
     * 调用路由
     * 仅能调用 do的方法
     * */
    static  function call( $options , $system_do_call_back=true ){

        /*
         * 系统回调需要使用到的类
         * $page->output();
        */
        $options['system_do_call_back'] = $system_do_call_back;
        $model = $options['m'];
        $model_name = "mod_".$model;
        //判断和运行类
        if(class_exists($model_name)) {
            $myModel = new $model_name($options);
        } else {
            $myModel = new mod_page404($options);
        }
        //执行输入
        return $myModel->output();
    }
}