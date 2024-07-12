<?php
namespace Plantation\Clover\Mvc;

class Model{

    protected $adapter;
    protected $instance;

    /**
     * Model constructor.
     * @param $adapter
     * 构造方法
     */
    public function __construct($adapter=null){
        if (!$this->instance){
            if(!$adapter){
                if (isset($_SERVER['mysqlMaster'])){
                    $this->instance = $_SERVER['mysqlMaster'];
                }else{
                    $this->instance = $adapter;
                }
            }else{
                $this->instance = $adapter;
            }
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