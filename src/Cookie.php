<?php


namespace Plantation\Clover;


class Cookie
{
    protected $adapter;

    /**
     * @param $instance
     * @return Cookie
     * 实例化函数
     */
    public static function instance($instance){
        return new Cookie($instance);
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
    public function set($key, $value, $expire=true, $path='/', $domain=null){
        return $this->adapter->set($key, $value, $expire, $path, $domain);
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
     * @param $val
     * @return mixed
     * 获得解密的数据
     */
    public function getDecrypted($val){
        return $this->adapter->getDecrypted($val);
    }

    /**
     * @param $key
     * 获取未解密的数据
     */
    public function getNotDecrypted($key){
        return $this->adapter->getNotDecrypted($key);
    }

    /**
     * @param $key
     * @return mixed
     * 删除cookie
     */
    public function delete($key,$path='/', $domain=null){
        return $this->adapter->delete($key,$path, $domain);
    }
}