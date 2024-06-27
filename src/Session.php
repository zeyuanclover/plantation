<?php


namespace Plantation\Clover;


use Predis\Command\Redis\SELECT;

class Session
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
        return new Session($adapter);
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
    public function set($name,$data,$expire=true){
        $this->adapter->set($name,$data,$expire);
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
     * 删除
     */
    public function delete($name){
        $this->adapter->delete($name);
    }

    /**
     * @param $key
     * @return mixed
     * 有效期
     */
    public function ttl($key){
        return $this->adapter->ttl($key);
    }
}