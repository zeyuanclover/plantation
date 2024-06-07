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
        $cookie = $_COOKIE[$key];

        if(isset($cookie)){
            $rsa = new Certificate($this->config['private'],$this->config['public']);
            $val = $rsa->privDecrypt($cookie);
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
    public function set($key,$val,$arr_cookie_options=null){
        if (is_array($val)){
            $val = json_encode($val);
        }

        if(!$arr_cookie_options){
            $arr_cookie_options = array (
                'expires' => time() + 60*60*24*365,
                'path' => '/',
                'domain' => '', // leading dot for compatibility or use subdomain
                'secure' => false,     // or false
                'httponly' => false,    // or false
                'samesite' => 'Strict' // None || Lax  || Strict
            );
        }

        $rsa = new Certificate($this->config['private'],$this->config['public']);
        $val = $rsa->publicEncrypt($val);
        return setcookie($key, $val, $arr_cookie_options);
    }

    /**
     * 删除cookie
     * @param $key
     * @param $arr_cookie_options
     * @return true
     */
    public function remove($key,$arr_cookie_options=[]){
        $arr_cookie_options['expires'] = time() - 1000;
        $val = '';
        unset($_COOKIE[$key]);
        return setcookie($key, $val, $arr_cookie_options);
    }

    /**
     * 删除cookie
     * @param $key
     * @return bool
     */
    public function delete($key){
        $arr_cookie_options['expires'] = time() - 1000;
        $val = '';
        unset($_COOKIE[$key]);
        return setcookie($key, $val, $arr_cookie_options);
    }

    /**
     * 清除所有cookie
     * @param $key
     * @param $val
     * @param $arr_cookie_options
     * @return void
     */
    public function clear(){
        $val = '';
        $arr_cookie_options['expires'] = time() - 1000;
        foreach ($_COOKIE as $cookieName => $cookieValue) {
            if($cookieName!='PHPSESSID'){
                setcookie($cookieName, '', $arr_cookie_options);
            }
        }
    }
}