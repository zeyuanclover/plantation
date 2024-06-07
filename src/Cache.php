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
        return self::$instance = new Cache($adapter);
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
     * @param $name
     * @param $expire
     * 设置有效期
     */
    public function expire($name,$expire){
        $this->adapter->expire($name,$expire);
    }
}