<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Area;
use app\common\model\Version;
use fast\Random;
use think\Config;
use think\Db;

/**
 * 公共接口
 */
class Common extends Api
{

    protected $noNeedLogin = ['init', 'upload','base64upload', 'mulbase64upload','getprovince','getarea'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
		
		//admin插件接口引用时加载api相关
		if($this->request->module() != 'api'){
			// 加载初始化文件
			if (is_file(APP_PATH . 'api/init' . EXT)) {
				include APP_PATH . 'api/init' . EXT;
			} elseif (is_file(RUNTIME_PATH . 'api/init' . EXT)) {
				include RUNTIME_PATH . 'api/init' . EXT;
			} elseif(is_file(CONF_PATH . 'api/config' . CONF_EXT)){
				$config = Config::load(CONF_PATH . 'api/config' . CONF_EXT);
				// 加载应用状态配置
				if ($config['app_status']) {
					Config::load(CONF_PATH . 'api/' . $config['app_status'] . CONF_EXT);
				}
			}
            \think\Lang::load(APP_PATH . 'api/lang' . DS . $this->request->langset() . EXT);
			$this->loadlang('common', 'api');
			// 加载行为扩展文件
			if (is_file(CONF_PATH . 'api/tags' . EXT)) {
				Hook::import(include CONF_PATH . 'api/tags' . EXT);
			}
			//加载公共文件
			$path = APP_PATH . 'api' . DS;
			if (is_file($path . 'common' . EXT)) {
				include $path . 'common' . EXT;
			}
		}
//
//		\think\Log::write('########################');
//		\think\Log::write(input());
//		\think\Log::write($this->request->getInput());
        header("Access-Control-Allow-Origin: * "); //允许跨域
    }

    /**
     * 加载初始化
     * 
     * @param string $version 版本号
     * @param string $lng 经度
     * @param string $lat 纬度
     */
    public function init()
    {
        if ($version = $this->request->request('version'))
        {
            $lng = $this->request->request('lng');
            $lat = $this->request->request('lat');
            $content = [
                'citydata'    => Area::getCityFromLngLat($lng, $lat),
                'versiondata' => Version::check($version),
                'uploaddata'  => Config::get('upload'),
                'coverdata'   => Config::get("cover"),
            ];
            $this->success('', $content);
        }
        else
        {
            $this->error(__('Invalid parameters'));
        }
    }

    /**
     * 上传文件(支持一维数组)
     * 
     * @param
     */
	public function upload(){
		$args = func_get_args();
		$field = isset($args[0]) ? $args[0] : '';
		$returned = isset($args[1]) ? $args[1] : false;

		$file = $this->request->file($field);
        if(empty($file)){
			if($returned)return false;
            $this->error(__('No file upload or server upload limit exceeded'));
        }
		$result = array();
		if(is_array($file)){
			foreach($file as $k => $v){
				if(is_array($v)){
					foreach($v as $kk => $vv){
						$result[$k][$kk] = $this->_uploadSource($vv, $returned);
					}
				}else{
					$result[$k] = $this->_uploadSource($v, $returned);
				}				
			}
		}else{
			$result = $this->_uploadSource($file, $returned);
		}
		return $result;
	}

    /**
     * 上传文件流
     * 
     * @param File $file 文件流
     */
    private function _uploadSource($file = null, $returned = true)
    {
        if (empty($file))
        {
			if($returned)return false;
            $this->error(__('No file upload or server upload limit exceeded'));
        }

        //判断是否已经存在附件
        $sha1 = $file->hash();

        $upload = Config::get('upload');

        preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int) $upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
        $fileInfo = $file->getInfo();
        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        $mimetypeArr = explode(',', $upload['mimetype']);
        $typeArr = explode('/', $fileInfo['type']);
        //验证文件后缀
        if ($upload['mimetype'] !== '*' && !in_array($suffix, $mimetypeArr) && !in_array($fileInfo['type'], $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr))
        {
			if($returned)return __('Uploaded file format is limited');
            $this->error(__('Uploaded file format is limited'));
        }
        $replaceArr = [
            '{year}'     => date("Y"),
            '{mon}'      => date("m"),
            '{day}'      => date("d"),
            '{hour}'     => date("H"),
            '{min}'      => date("i"),
            '{sec}'      => date("s"),
            '{random}'   => Random::alnum(16),
            '{random32}' => Random::alnum(32),
            '{filename}' => $suffix ? substr($fileInfo['name'], 0, strripos($fileInfo['name'], '.')) : $fileInfo['name'],
            '{suffix}'   => $suffix,
            '{.suffix}'  => $suffix ? '.' . $suffix : '',
            '{filemd5}'  => md5_file($fileInfo['tmp_name']),
        ];
        $savekey = $upload['savekey'];
        $savekey = str_replace(array_keys($replaceArr), array_values($replaceArr), $savekey);

        $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
        $fileName = substr($savekey, strripos($savekey, '/') + 1);
        //
        $splInfo = $file->validate(['size' => $size])->move(ROOT_PATH . $uploadDir, $fileName);
        if ($splInfo)
        {
            $imagewidth = $imageheight = 0;
            if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf']))
            {
                $imgInfo = getimagesize($splInfo->getPathname());
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }
            $params = array(
                'filesize'    => $fileInfo['size'],
                'imagewidth'  => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype'   => $suffix,
                'imageframes' => 0,
                'mimetype'    => $fileInfo['type'],
                'url'         => $uploadDir . $splInfo->getSaveName(),
                'uploadtime'  => time(),
                'storage'     => 'local',
                'sha1'        => $sha1,
            );
            $attachment = model("attachment");
            $attachment->data(array_filter($params));
            $attachment->create();
            \think\Hook::listen("upload_after", $attachment);
			if($returned)return ['url' => $uploadDir . $splInfo->getSaveName()];
            $this->success(__('Upload successful'), [
                'url' => $uploadDir . $splInfo->getSaveName()
            ]);
        }
        else
        {
            // 上传失败获取错误信息
			if($returned)return $file->getError();
            $this->error($file->getError());
        }
    }


    /**
     * 上传文件base64_image
     * 只能上传一张图片
     */
    public function base64upload()
    {
        if ($this->request->isPost()) {
            $postData = $this->request->post();
            $image = trim(!empty($postData['upload']) ? $postData['upload'] : '');
            if (!$image) $this->error('请上传图片');
            //匹配出图片的格式
            if (!$image == base64_encode(base64_decode($image))) $this->error('图片格式错误');
			
			$suffix = 'png';//默认图片格式
            //如果图片带有格式的
            if (strstr($image, ",")) {
                $imageArray = explode(',', $image);
                //获取图片格式
                $img_type_array = explode(';', $imageArray[0]);
                $suffix = explode('/', $img_type_array[0]);
                $suffix = $suffix[1];
                //获取图片的base64
                $image = $imageArray[1];
            }
						
			$upload = Config::get('upload');
			$replaceArr = [
				'{year}'     => date("Y"),
				'{mon}'      => date("m"),
				'{day}'      => date("d"),
				'{hour}'     => date("H"),
				'{min}'      => date("i"),
				'{sec}'      => date("s"),
				'{random}'   => Random::alnum(16),
				'{random32}' => Random::alnum(32),
				'{filename}' => $suffix ? substr(md5($image), 0, strripos(md5($image), '.')) : md5($image),
				'{suffix}'   => $suffix,
				'{.suffix}'  => $suffix ? '.' . $suffix : '',
				'{filemd5}'  => md5($image),
			];
			$savekey = $upload['savekey'];
			$savekey = str_replace(array_keys($replaceArr), array_values($replaceArr), $savekey);
	
			$uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
			$fileName = substr($savekey, strripos($savekey, '/') + 1);
			@mkdir($uploadDir, 0777, true); //文件夹不存在就创建一个文件夹
            $img_url = ROOT_PATH . $uploadDir . $fileName;//图片路径
            $r = file_put_contents($img_url, base64_decode($image));//返回的是字节数

            if (!$r) {
                $this->error('图片生成失败');
            } else {
                $this->success("图片生成成功", ["url" => long_url($uploadDir . $fileName)]);
            }
        }
        $this->error('没有post文件');
    }
    /**
     * 多张base64图片上传
     */
    public function mulbase64upload(){
        if ($this->request->isPost()) {
            $postData = $this->request->post();
            $images = trim(!empty($postData['upload']) ? $postData['upload'] : '');
            if (!$images) $this->error('请上传图片');
            //分割字符串
            $images = explode(',data:', $images);
            $img_list =array();
            foreach ($images as $n => $v){
                $image = 'data:'.$v;
                $img_type = '.png';//默认图片格式
                //如果图片带有格式的
                if (strstr($image, ",")) {
                    $image = explode(',', $image);
                    //获取图片格式
                    $img_type_array = explode(';', $image[0]);
                    $img_type = explode('/', $img_type_array[0]);
                    $img_type = '.' . $img_type[1];
                    //获取图片的base64
                    $image = $image[1];
                }
                //截取域名 app 前面部分
                /*$domain = $_SERVER['SERVER_NAME'];
                $n = preg_match('/(.*\.)?\w+\.\w+$/', $domain, $matches);
                $domain_name = trim($matches[1], ".");
                $domain_array = explode('.', $domain_name);*/
                $path = '/uploads';
                $imageName = "25220_" . date("His", time()) . "_" . rand(100000, 999999) . $img_type;
                $file = ROOT_PATH . $path . "/" . date('Ymd', time());
                if (!file_exists($file)) mkdir($file, 0777, true); //文件夹不存在就创建一个文件夹
                $img_url = $path . "/" . date('Ymd', time()) . '/' . $imageName;//图片路径
                $r = file_put_contents(ROOT_PATH . $img_url, base64_decode($image));//返回的是字节数
                if ($r) {
                    $img_list[]=long_url($img_url);
                }
            }
            if($img_list){
                $this->success("图片生成成功", ['url'=>$img_list]);
            }else{
                $this->error('图片生成失败');
            }
        }
        $this->error('没有post文件');
    }
    //获取省
    public function getprovince() {
        if ($this->request->isPost()) {
            $areaid = input('post.area_id', 0, 'int');
            function __getSons($areaid__)  {
                $sonArea = Db::name('area')->field('s_id,s_name,s_needopen')->where([
                    's_parent_id'=> $areaid__
                ])->select();
                return $sonArea;
            };
            $this->success('success', __getSons($areaid));
        } else {
            echo 'no post';
        }
    }
    //获取市区
    public function getarea() {
        if ($this->request->isPost()) {
            $areaid = input('post.area_id', 0, 'int');
            function __getSons($areaid__)  {
                $sonArea = Db::name('area')->field('s_id,s_name,s_needopen')->where([
                    's_parent_id'=> $areaid__
                ])->select();
//                print_r($sonArea);exit;
                $newCity = array();
                foreach ($sonArea as &$datum) {
                    if($datum['s_needopen']) {
                        $newCity = array_merge($newCity, __getSons($datum['s_id']));
                    }
                }
                if($newCity) $sonArea = $newCity;
                return $sonArea;
            };
            $this->success('success', __getSons($areaid));
        } else {
            echo 'no post';
        }
    }
}