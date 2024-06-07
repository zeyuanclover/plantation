<?php
namespace Plantation\Clover\Cache\Adapter;

use Plantation\Clover\Safe\Adapter\Certificate;

// cookie 类
class RedisCertificateEncription{

    protected $redis;
    private $path;
    private static $instance;

    /**
     * 调用函数
     * @return Cookie
     */
    public static function instance($instance,$path){
        new RedisCertificateEncription($instance,$path);
    }

    /**
     * Redis constructor.
     * @param $instance
     * 构造函数
     */
    public function __construct($instance,$path){
        $this->redis = $instance;
        $this->path = $path;
    }

    /**
     * 获取cookie
     * @param $key
     * @return mixed|null
     */
    public function get($key){
        $value = $this->redis->get($key);

        if(isset($value)){
            $cert = new Certificate(ROOT_PATH.$this->path['private'],ROOT_PATH.$this->path['public']);
            $val = $cert->privDecrypt($value);
            return $val;
        }else{
            return null;
        }
    }

    /**
     * 设置cookie
     * @param $key
     * @param $val
     * @param $arr_cookie_options
     * @return bool
     */
    public function set($key,$val,$expire=0){
        if (is_array($val)){
            $val = json_encode($val);
        }

        $cert = new Certificate(ROOT_PATH.$this->path['private'],ROOT_PATH.$this->path['public']);
        $val = $cert->publicEncrypt($val);
        if ($expire>0){
            $this->redis->set($key,$val);
            return $this->redis->expire($key,$expire);
        }
        return $this->redis->set($key,$val);
    }

    /**
     * 删除cookie
     * @param $key
     * @param $arr_cookie_options
     * @return true
     */
    public function remove($key){
        return $this->redis->del($key);
    }

    /**
     * 删除cookie
     * @param $key
     * @return bool
     */
    public function delete($key){
        return $this->redis->del($key);
    }

    /**
     * 清除所有cookie
     * @param $key
     * @param $val
     * @param $arr_cookie_options
     * @return void
     */
    public function clear(){
        return $this->redis->flushdb();
    }

    /**
     * @param $expire
     * 过期设置
     */
    public function expire($key,$expire){
        return $this->redis->expire($key,$expire);
    }
}