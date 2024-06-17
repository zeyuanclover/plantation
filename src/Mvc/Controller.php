<?php
namespace Plantation\Clover\Mvc;

use DI\Definition\ArrayDefinition;
use Plantation\Clover\File as FileClass;
use Plantation\Clover\Cache\Adapter\File;
use Plantation\Clover\Cache;

class Controller{

    protected $config;
    protected $container;
    protected $vars;
    protected $cacheSwitch;

    /**
     * Controller constructor.
     * @param $data
     * @param $container
     * 构造函数
     */
    public function __construct($data,$container){
        $this->config = $data;
        $this->container = $container;
    }

    /**
     * @param $url
     * @return string
     * url
     */
    public function appUrl($url){
        return $this->config['appUrl'] . $url;
    }

    /**
     * @param $name
     * @return mixed
     * 容器
     */
    public function container($name){
        return $this->container->get($name);
    }

    /**
     * @param $name
     * @param $data
     * 模板注入变量
     */
    public function assign($name,$data){
        $this->vars[$name] = $data;
    }

    /**
     * @param $name
     * @param null $nameAdditional
     * @return false|string
     * 读取解析后的模板html文件
     */
    public function readTemplate($name,$nameAdditional=null){
        // 查找模板名称
        $config['theme'] = 'default';
        if(isset($this->config['appConfig']['Application']['theme'])){
            $config['theme'] = $this->config['appConfig']['Application']['theme'];
        }

        // 载入模板函数
        $templateFunctionPath = $this->config['appPath'] . 'Template' . DIRECTORY_SEPARATOR . 'Function.php';
        include_once ($templateFunctionPath);

        // 分配变量
        $config['path'] = $this->config['appPath'];
        if (is_array($this->vars)){
            extract($this->vars);
        }

        // 遇到有变量的缓存，以此为区分
        $finalName = $name;
        if ($nameAdditional){
            $nameArr = explode('.', $name);
            $finalName = implode('_' . $nameAdditional . '.',$nameArr);
        }

        $content = null;

        ob_start();
        include_once (new Template($config))->fetch($name);
        $content = ob_get_clean();
        return $content;
    }

    /**
     * @param $name
     * @param false $cacheSwitch
     * @param null $nameAdditional
     * 加载模板
     */
    public function template($name,$cacheSwitch=false,$nameAdditional=null){
        // 查找模板名称
        $config['theme'] = 'default';
        if(isset($this->config['appConfig']['Application']['theme'])){
            $config['theme'] = $this->config['appConfig']['Application']['theme'];
        }

        // 载入模板函数
        $templateFunctionPath = $this->config['appPath'] . 'Template' . DIRECTORY_SEPARATOR . 'Function.php';
        include_once($templateFunctionPath);

        // 分配变量
        $config['path'] = $this->config['appPath'];
        if (is_array($this->vars)){
            extract($this->vars);
        }

        // 遇到有变量的缓存，以此为区分
        $finalName = $name;
        if ($nameAdditional){
            $nameArr = explode('.', $name);
            $finalName = implode('_' . $nameAdditional . '.',$nameArr);
        }

        $content = null;

        // 是否开启缓存
        if($_SERVER['env']['Cache']==1&&$cacheSwitch==true){
            $filePath = ROOT_PATH . 'Run' . DIRECTORY_SEPARATOR . 'PageCache' . DIRECTORY_SEPARATOR . $this->config['realAppName'] . DIRECTORY_SEPARATOR . $this->config['controller'] . DIRECTORY_SEPARATOR . $this->config['action'];
            $htmlCache = File::instance('',$filePath)->get($finalName);

            if ($htmlCache){
                echo $htmlCache;
            }else{
                ob_start();
                include (new Template($config))->fetch($name);
                $content = ob_get_clean();
                $this->setPacheCache($finalName,$content);
                echo $content;
            }
        }else{
            include (new Template($config))->fetch($name);
        }
    }

    /**
     * 删除某个模块或某个控制器下所有文件以及文件夹
     * @param string $path
     */
    public function clearTemplates($path=''){
        $filePath = ROOT_PATH . 'Run' . DIRECTORY_SEPARATOR . 'PageCache' . DIRECTORY_SEPARATOR . $this->config['realAppName'];
        if($path){
            $path = str_replace('/',DIRECTORY_SEPARATOR,$path);
            if (strpos($path,DIRECTORY_SEPARATOR)===0){
                $path = substr($path,1);
            }
            $filePath = DIRECTORY_SEPARATOR . $path;
        }
        if (is_dir($filePath)){
            FileClass::instance($filePath)->deleteDirectory();
        }
    }

    /**
     * 页面缓存
     * @param $content
     * @param $name
     */
    public function setPacheCache($name,$content){
        $filePath = ROOT_PATH . 'Run' . DIRECTORY_SEPARATOR . 'PageCache' . DIRECTORY_SEPARATOR . $this->config['realAppName'] . DIRECTORY_SEPARATOR . $this->config['controller'] . DIRECTORY_SEPARATOR . $this->config['action'];
        if (strpos($name,DIRECTORY_SEPARATOR)!==false||strpos($name,'/')!==false){
            $dir = str_replace('/',DIRECTORY_SEPARATOR,$name);
            $dirArr = explode(DIRECTORY_SEPARATOR,$dir);
            if (count($dirArr)>2){
                unset($dirArr[count($dirArr)-1]);
            }
            if (!is_dir($filePath.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$dirArr))){
                mkdir($filePath.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$dirArr),0777,true);
            }
        }
        File::instance('',$filePath)->set($name,$content);
    }

    /**
     * @param $name
     * @param $content
     * @return mixed|null
     * 获取缓存
     */
    public function getPacheCache($name,$content){
        $filePath = ROOT_PATH . 'Run' . DIRECTORY_SEPARATOR . 'PageCache' . DIRECTORY_SEPARATOR . $this->config['realAppName'] . DIRECTORY_SEPARATOR . $this->config['controller'] . DIRECTORY_SEPARATOR . $this->config['action'];
        if (strpos($name,DIRECTORY_SEPARATOR)!==false||strpos($name,'/')!==false){
            $dir = str_replace('/',DIRECTORY_SEPARATOR,$name);
            $dirArr = explode(DIRECTORY_SEPARATOR,$dir);
            if (count($dirArr)>2){
                unset($dirArr[count($dirArr)-1]);
            }
            if (!is_dir($filePath.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$dirArr))){
                mkdir($filePath.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$dirArr),0777,true);
            }
        }
        return File::instance('',$filePath)->get($name);
    }

    /**
     * @param $name
     * @param $var
     * @return mixed
     * 访问未定义对象
     *
     */
    public function __call($name,$var){
        return $this->container->get($name);
    }

}