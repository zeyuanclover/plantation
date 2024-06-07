<?php


namespace Plantation\Clover;


class Session
{
    protected $adapter;

    /**
     * @param $instance
     * @return Cookie
     * 实例化函数
     */
    public static function instance($instance){
        return new Session($instance);
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
    public function set($key,$data,$expire=true){
        return $this->adapter->set($key,$data,$expire);
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

    /**
     * @param $key
     * @param $expire
     * @return mixed
     * 设置有效期
     */
    public function expire($key,$expire){
        return $this->adapter->expire($key,$expire);
    }
}