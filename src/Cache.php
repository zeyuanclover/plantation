<?php


namespace Plantation\Clover;


class Cache
{
    private static $instance;
    private $dir;
    private $adapter;

    /**
     * Config constructor.
     * @param $dir
     * 构造函数
     */
    public function __construct($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param $dir
     * @return Config
     * 实例化
     */
    public static function instance($adapter)
    {
        return new Cache($adapter);
    }

    /**
     * @param $name
     * @return mixed
     * 设置
     */
    public function get($name){
        return $this->adapter->get($name);
    }

    /**
     * @param $name
     * @param $data
     * 设置
     */
    public function set($name,$data,$expire=0){
        $this->adapter->set($name,$data,$expire);
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
     * @param $name
     * @param $expire
     * 设置有效期
     */
    public function expire($name,$expire){
        $this->adapter->expire($name,$expire);
    }

    /**
     * @param $name
     * @return mixed
     * 获得剩余时间
     */
    public function ttl($name){
        return $this->adapter->ttl($name);
    }

    /**
     * @param $name
     * 删除
     */
    public function delete($name){
        $this->adapter->delete($name);
    }
}