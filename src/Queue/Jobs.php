<?php


namespace Plantation\Clover\Queue;

class Jobs{

    // 实例
    protected $instance;

    protected $queueName;

    protected $token;

    protected $data;

    // 构造函数
    public function __construct($instance,$queueName,$token,$data){
        $this->instance = $instance;
        $this->queueName = $queueName;
        $this->token = $token;
        $this->data = $data;
    }

    // 删除队列
    function delete(){
        $this->instance->setStatus($this->queueName.$this->token,'cancel');
    }

    // 重新入队列
    function reTry(){
        $this->instance->setStatus($this->queueName.$this->token,'ready');
        $this->instance->reAdd($this->queueName,json_encode($this->data));
    }

    // 获取重试次数
    function getAttemp(){
        return $this->instance->getAttemp($this->queueName.$this->token);
    }

    // 获得token
    function getToken(){
        return $this->token;
    }

    // 设置状态
    public function setStatus($status){
        $this->instance->setStatus($this->queueName.$this->token,$status);
    }

    // 获取状态
    public function getStatus(){
        return $this->instance->getStatus($this->queueName.$this->token);
    }
}