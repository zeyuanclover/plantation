<?php
namespace Plantation\Clover\Mvc;

class Controller{

    protected $config;
    protected $container;
    protected $vars;

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
     * 模板
     */
    public function template($name){
        $config['theme'] = 'default';
        if(isset($this->config['appConfig']['Config.Application']['theme'])){
            $config['theme'] = $this->config['appConfig']['Config.Application']['theme'];
        }

        // 载入模板函数
        $templateFunctionPath = $this->config['appPath'] . 'Template' . DIRECTORY_SEPARATOR . 'Function.php';
        include ($templateFunctionPath);

        $config['path'] = $this->config['appPath'];
        if (is_array($this->vars)){
            extract($this->vars);
        }

        header("Content-type: text/html; charset=utf-8");
        include (new Template($config))->fetch($name);
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