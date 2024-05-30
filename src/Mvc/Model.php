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
    public function __construct($adapter){
        if (!$this->instance){
            $this->instance = $adapter;
        }
    }

    /**
     * @return mixed
     * 模型
     */
    public function model(){
        return $this->instance;
    }
}