<?php
namespace Plantation\Clover\Session\Adapter;

// cookie 类
use Plantation\Clover\Safe\Adapter\Certificate;

class File{

    protected $config;

    /**
     * 调用函数
     * @return Cookie
     */
    public static function instance($data){
        return new File($data);
    }

    public function __construct($data){
        $this->config = $data;
    }

    /**
     * 获取cookie
     * @param $key
     * @return mixed|null
     */
    public function get($key){
        $session = $_SESSION[$key]['data'];
        $expire = $_SESSION[$key]['expire'];

        if ($expire==true||$expire>=time()){
            if($session){
                $rsa = new Certificate($this->config['private'],$this->config['public']);
                $val = $rsa->privDecrypt($session);
                return $val;
            }else{
                return null;
            }
        }
        return null;
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

        $rsa = new Certificate($this->config['private'],$this->config['public']);
        $val = $rsa->publicEncrypt($val);

        if ($expire===true){

        }else{
            $expire = time() + $expire;
        }

        $_SESSION[$key]['expire'] = $expire;
        $_SESSION[$key]['data'] = $val;
    }

    /**
     * 删除cookie
     * @param $key
     * @param $arr_cookie_options
     * @return true
     */
    public function remove($key){
        unset($_SESSION[$key]);
    }

    /**
     * 删除cookie
     * @param $key
     * @return bool
     */
    public function delete($key){
        unset($_SESSION[$key]);
    }

    /**
     * 清除所有cookie
     * @param $key
     * @param $val
     * @param $arr_cookie_options
     * @return void
     */
    public function clear(){
        session_destroy();
    }

    /**
     * @param $key
     * @param $expire
     * session 生存时间
     */
    public function expire($key,$expire){
        $_SESSION[$key]['expire'] = $expire;
    }
}