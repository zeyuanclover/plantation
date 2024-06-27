<?php
namespace Plantation\Clover\Cache\Adapter;

use Plantation\Clover\Safe\Adapter\Certificate;

// cookie 类
class RedisCertificateEncription{

    protected $redis;
    private $config;
    private static $instance;

    /**
     * 调用函数
     * @return Cookie
     */
    public static function instance($instance,$config){
        new RedisCertificateEncription($instance,$config);
    }

    /**
     * Redis constructor.
     * @param $instance
     * 构造函数
     */
    public function __construct($instance,$config){
        $this->redis = $instance;
        $this->config = $config;
    }

    /**
     * 获取cookie
     * @param $key
     * @return mixed|null
     */
    public function get($key){
        $value = $this->redis->get($key);

        if(isset($value)){
            $cert = new Certificate($this->config['private'],$this->config['public']);
            return $cert->privDecrypt($value);
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
    public function set($key,$val,$expire=true){
        if (is_array($val)){
            $val = json_encode($val);
        }

        $cert = new Certificate($this->config['private'],$this->config['public']);
        $val = $cert->publicEncrypt($val);
        if ($expire!==true){
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

    /**
     * @param $key
     * @return mixed
     * 有效期
     */
    public function ttl($key){
        return $this->redis->ttl($key);
    }

    /**
     * @param $key
     * 获取未解密数据
     */
    public function getNotDecrypted($key){
        return $this->redis->get($key);
    }

    /**
     * @param $val
     * @return null
     * 获取解密的数据
     */
    public function getDecrypted($val){
        if (!$val){
            return null;
        }
        $rsa = new Certificate($this->config['private'],$this->config['public']);
        return $rsa->privDecrypt($val);
    }
}