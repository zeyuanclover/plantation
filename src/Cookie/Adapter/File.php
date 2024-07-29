<?php
namespace Plantation\Clover\Cookie\Adapter;

// cookie 类
use Plantation\Clover\Safe\Adapter\Certificate;

class File{

    protected $config;

    /**
     * 调用函数
     * @return Cookie
     */
    public static function instance($config){
        return new File($config);
    }

    /**
     * File constructor.
     * @param $config
     * 构造函数
     */
    public function __construct($config){
        $this->config = $config;
    }

    /**
     * 获取cookie
     * @param $key
     * @return mixed|null
     */
    public function get($key){
        if(isset($_COOKIE[$key])){
            $cookie = $_COOKIE[$key];
            $rsa = new Certificate($this->config['private'],$this->config['public']);
            $val = $rsa->privDecrypt($cookie);
            return $val;
        }else{
            return null;
        }
    }

    /**
     * @param $key
     * 获取未解密数据
     */
    public function getNotDecrypted($key){
        if(isset($_COOKIE[$key])){
            return $_COOKIE[$key];
        }else{
            return null;
        }
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
        $cookie = $val;
        $rsa = new Certificate($this->config['private'],$this->config['public']);
        $val = $rsa->privDecrypt($cookie);
        return $val;
    }

    /**
     * 设置cookie
     * @param $key
     * @param $val
     * @param $arr_cookie_options
     * @return bool
     */
    public function set($key, $value, $expire=true, $path='/', $domain=null){
        if (is_array($value)){
            $value = json_encode($value);
        }

        if ($expire===true){
            $expire = time() + (3600 * 24 * 90);
        }

        $rsa = new Certificate($this->config['private'],$this->config['public']);
        $val = $rsa->publicEncrypt($value);
        return setcookie($key, $val, $expire,$path,$domain);
    }

    /**
     * 删除cookie
     * @param $key
     * @param $arr_cookie_options
     * @return true
     */
    public function remove($key,$path, $domain){
        $cookieExpire = time() - 1000;
        $val = '';
        return setcookie($key, $val, $cookieExpire,$path, $domain);
    }

    /**
     * 删除cookie
     * @param $key
     * @return bool
     */
    public function delete($key,$path, $domain){
        $cookieExpire = time() - 1000;
        $val = '';
        return setcookie($key, $val, $cookieExpire,$path, $domain);
    }

    /**
     * 清除所有cookie
     * @param $key
     * @param $val
     * @param $arr_cookie_options
     * @return void
     */
    public function clear(){
        foreach (($_COOKIE) as $key => $value) {
            setcookie($key, '', time() - 3600);
        }
    }
}