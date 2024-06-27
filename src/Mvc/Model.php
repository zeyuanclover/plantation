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
}