<?php


namespace Plantation\Clover;


class Cookie
{
    protected static $instance;
    protected $adapter;

    /**
     * @param $instance
     * @return Cookie
     * 实例化函数
     */
    public static function instance($instance){
        if (!self::$instance){
            self::$instance = new Cookie($instance);
            return self::$instance;
        }else{
            return self::$instance;
        }
    }

    /**
     * Cookie constructor.
     * @param $adpater
     * 构造函数
     */
    public function __construct($adpater){
        $this->adapter = $adpater;
    }

    /**
     * @param $key
     * @param $data
     * @param null $config
     * @return mixed
     * 设置cookie
     */
    public function set($key,$data,$config=null){
        return $this->adapter->set($key,$data,$config);
    }

    /**
     * @param $key
     * @return mixed
     * 获取cookie
     */
    public function get($key){
        return $this->adapter->get($key);
    }

    /**
     * @param $key
     * @return mixed
     * 删除cookie
     */
    public function delete($key){
        return $this->adapter->delete($key);
    }
}