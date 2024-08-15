<?php
namespace Plantation\Clover\Mvc;

class Model{

    protected $adapter;
    protected $instance;
    protected $lang;

    /**
     * Model constructor.
     * @param $adapter
     * 构造方法
     */
    public function __construct($adapter=null){
        if (!isset($_SERVER['mysql'])){
            if($adapter){
                $this->instance = $adapter;
                return $_SERVER['mysql'] = $adapter;
            }else{
                return $this->instance = $_SERVER['mysql'];
            }
        }else{
            $this->instance = $_SERVER['mysql'];
            return $_SERVER['mysql'];
        }
    }

    /**
     * @return mixed
     * 模型
     */
    public function model(){
        return $this->instance;
    }

    /**
     * @return mixed
     * db
     */
    public function db(){
        return $this->instance;
    }

    // 访问
    public function __call($name, $arguments)
    {
        if (method_exists($this->instance,$name)){
            if(isset($arguments[0]) && isset($arguments[1])){
                if (isset($arguments[2])){
                    if (isset($arguments[3])){
                        return $this->instance->$name($arguments[0],$arguments[1],$arguments[2],$arguments[3]);
                    }
                    return $this->instance->$name($arguments[0],$arguments[1],$arguments[2]);
                }else{
                    return $this->instance->$name($arguments[0],$arguments[1]);
                }
            }
        }
    }
}