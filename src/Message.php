<?php


namespace Plantation\Clover;

use Plantation\Clover\Message\Adapter\Json as JsonMessage;
use Plantation\Clover\Message\Adapter\Str;
use Plantation\Clover\Message\Adapter\Template;

class Message
{
    /*
     * 适配
     */
    private $adapter;

    /**
     * @var Json
     * 适配实例
     */
    private $adapterInstance;

    /**
     * Message constructor.
     * @param $adapter
     * 构造函数
     */
    public function __construct($adapter)
    {
        switch ($adapter){
            case 'json':
                $this->adapterInstance = new JsonMessage();
                break;
            case 'template':
                $this->adapterInstance = new Template();
                break;
            case 'str':
                $this->adapterInstance = new Str();
                break;
        }
    }

    /**
     * @param string $adapter
     * @return Message
     * 实例化
     */
    public static function instance($adapter='json'){
        return new Message($adapter);
    }

    /**
     * @param $data
     * 发送消息
     */
    public function send($data,$die=true){
        $this->adapterInstance->send($data,$die);
    }
}