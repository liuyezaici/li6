<?php

namespace fast;
use think\Db;
use app\admin\model\AuthRule;
use think\exception\ErrorException;


/**
 * 插件配置模块
 */
class Addon
{
    //组件目录
    public static $addonPath = 'addon';//组件存放的目录名字
    public static $addonUriName = 'adppp';//组件在uri引用时的路径
    //组件全路径
    public static function getAddonPathUrl() {
        return APP_PATH . 'admin' .DS .  self::$addonPath;
    }
    //组件class路径
    public static function getAddonClassPathUrl() {
        return  '\\app\\admin\\' .  self::$addonPath;
    }
    //通过路径获取组件信息
    public static function getAddonByPath($path='') {
        $info = Db('authRule')->getbyauthPath($path);
//        echo Db('authRule')->getLastSql();
        return $info;
    }

    //通过路径获取组件名字
    public static function getAddonTitleByPath($path='') {
        return Db('authRule')->getfieldbyauthPath($path, 'title');
    }


    /**
     * 获取插件类的配置数组
     * @param string $name 插件名
     * @return array
     */
    public static function getAddonConfig($name)
    {
        $configFile = self::getAddonPathUrl() . DS . $name .DS .'config.php';
        if (file_exists($configFile))
        {
            $fullConfigArr = include $configFile;
            return $fullConfigArr;
        }
        else
        {
            return [];
        }
    }
    /**
     * 获取插件类的配置的单个属性
     * @param string $name 插件名
     * @return array
     */
    public static function getAddonConfigAttr($name, $attrName='')
    {
        $configInfo = self::getAddonConfig($name);
        return isset($configInfo[$attrName]) ? $configInfo[$attrName] : '';
    }


    //安装组件
    public function installAddon($addonName='') {
        $classPath = Addon::getAddonClassPathUrl() .'\\'. $addonName .'\install\Install';
        $addonClass = new $classPath();
        \app\common\library\Menu::create($addonClass->menu, 0);
        $sql = Addon::getAddonPathUrl() .'/'. $addonName .'/install/install.sql';
        if(is_file($sql)) {
            $this->importsql($sql);
        } else {
            throw new \Exception('not is_file:'. $sql, -1);
        }
    }

    /**
     * 插件启用方法
     */
    public function enable($addonName='')
    {
        $classPath = Addon::getAddonClassPathUrl() .'\\'. $addonName .'\install\Install';
        $addonClass = new $classPath();
        \app\common\library\Menu::enable($addonClass->menu[0]['name']);
    }
    /**
     * 插件禁用方法
     */
    public function disable($addonName='')
    {
        $classPath = Addon::getAddonClassPathUrl() .'\\'. $addonName .'\install\Install';
        $addonClass = new $classPath();
        \app\common\library\Menu::disable($addonClass->menu[0]['name']);
    }
    /**
     * 插件卸载方法
     */
    public function uninstall($addonName='')
    {
        $classPath = Addon::getAddonClassPathUrl() .'\\'. $addonName .'\install\Install';
        $addonClass = new $classPath();
        \app\common\library\Menu::delete($addonClass->menu[0]['name']);
        $uninstallSql = Addon::getAddonPathUrl() .'\\'. $addonName .'\install\uninstall.sql';
        if(is_file($uninstallSql)) $this->importsql($uninstallSql);
        return true;
    }
    /**
     * 导出菜单
     */
    public function exportMenu($addonName='')
    {
        $classPath = Addon::getAddonClassPathUrl() .'\\'. $addonName .'\install\Install';
        $addonClass = new $classPath();
        \app\common\library\Menu::export($addonClass->menu[0]['name']);
    }


    /**
     * 导入SQL
     *
     * @param   string    $name   插件名称
     * @param   string    $file   sql文件名
     * @return  boolean
     */
    public function importsql($sqlFile = 'install.sql')
    {
        $lines = file($sqlFile);
        $templine = '';
        foreach ($lines as $line)
        {
            if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*')
                continue;

            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';')
            {
                $templine = str_ireplace('__PREFIX__', config('database.prefix'), $templine);
                $templine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $templine);
                try
                {
                    Db::getPdo()->exec($templine);
                }
                catch (\PDOException $e)
                {
                    //$e->getMessage();
                }
                $templine = '';
            }
        }
        return true;
    }
    //解析外部uri为插件的路由
    // $path /admin/addonpath/addonname
    public static function uriToRouter($path='') {
        $depr = '/';
        if(strpos($path, '\\') !== false)$depr = '\\';
        if(strpos($path, '.') !== false)$depr = '.';
        if(strpos($path, self::$addonUriName .$depr) !== false){
            $pathArray = explode($depr, $path);
            $pathArray[0] = 'admin/'. self::$addonPath;
            return implode($depr, $pathArray);
        }
        return $path;
    }


   //通过插件名字 获取其model
    //$addonName vv/third
    //$otherModel 组件中的其他模块
    //文件 model/Xxx.php 必须存在
    public static function getModel($addonName='', $otherModel='')
    {
        //支持.拼接组件名
        if(strstr($addonName, '.') && !$otherModel) {
            $array_ = explode('.', $addonName);
            $addonName = $array_[0];
            $otherModel = $array_[1];
        }
        $ruleInfo = AuthRule::getfieldbyauthPath($addonName, 'id');
        if(!$ruleInfo) return null;//未注册此组件
//        print_r('n:'.$addonName.':'. $ruleInfo).'|';
        //写完整的路径以兼容在user控制器里调取admin中的插件
        $addonName = strtolower($addonName);
        $modelPath = self::getAddonClassPathUrl() ."\\". $addonName ."\model\\". ucfirst($addonName);
        if(empty($otherModel)){
            $model = model($modelPath, 'model', false, true);
			if(!$model)$model = model($modelPath, 'model', false, true);
        }else{
            $otherModel = strtolower($otherModel);
            $modelPath = self::getAddonClassPathUrl() ."\\". $addonName ."\model\\". ucfirst($otherModel);
            $model = model($modelPath, 'model', false, true);
            if(!$model)$model = model($modelPath, 'model', false, true);
        }
        if(!$model) return $modelPath;
		return $model;
    }

   //通过插件名字 获取其model
    //$addonName vv/third
    //$otherLibrary 组件中的其他模块
    //文件 model/Xxx.php 必须存在
    public static function getLibrary($addonName='', $otherLibrary='')
    {
        //写完整的路径以兼容在user控制器里调取admin中的插件
        if(empty($otherLibrary)){
            $library = model(self::getAddonClassPathUrl() ."\\". $addonName ."\library\\". ucfirst($addonName), 'library', false, true);
			if(!$library)$library = model(self::getAddonClassPathUrl() ."\\". $addonName ."\library\\". $addonName, 'library', false, true);
//            return model($ver. '.'. $addonName .'.'. $addonName);
        }else{
            $library = model(self::getAddonClassPathUrl() ."\\". $addonName ."\library\\". $otherLibrary, 'library', false, true);
            if(!$library)$library = model(self::getAddonClassPathUrl() ."\\". $addonName ."\library\\". ucfirst($otherLibrary), 'library', false, true);
//            return model(self::$addonPath. '.'. $addonName .'.'. $otherLibrary);
        }
		return $library;
    }
		
	//addonName插件名
	//lang名 index 或 api.index
	//\fast\Addon::getLang('news', 'index');
	//\fast\Addon::getLang('news', 'api.index');
	public static function getLang($addonName='',$langName='index'){
		$file = file_path(APP_PATH . 'admin', self::$addonPath.'.'.$addonName.'.'.$langName, '.php', 'lang/'.\think\Lang::detect());
		if($file)return include $file;
		return [];
	}
	


    //组件的校验方法
    public static function validate($addonName='') {
        if(!$addonName) return false;
        $modelUrl = self::$addonPath .".". $addonName .".". ucfirst($addonName);
        return \think\Loader::validate($modelUrl);
    }
}
