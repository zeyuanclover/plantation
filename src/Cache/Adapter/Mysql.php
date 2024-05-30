<?php
namespace Plantation\Clover\Cache\Adapter;

use Monolog\Handler\IFTTTHandler;
use Peach5\Nectarine\Mvc\Cache\Adapter\File;
use Predis\Command\Redis\CONFIG;
use function Peach5\Nectarine\Functions\report_message;
use function Peach5\Nectarine\Functions\searchKeyInArray;
use function Peach5\Nectarine\Functions\getFinalConfig;
use Peach5\Nectarine\Security\Certificate;

// cookie 类
class Redis{

    protected $redis;
    private $path;
    private static $instance;

    /**
     * 调用函数
     * @return Cookie
     */
    public static function instance($instance){
        if (!self::$instance){
            self::$instance = new Redis($instance);
            return self::$instance;
        }else{
            return self::$instance;
        }
    }

    /**
     * Redis constructor.
     * @param $instance
     * 构造函数
     */
    public function __construct($instance){
        $this->redis = $instance;
    }

    /**
     * 获取cookie
     * @param $key
     * @return mixed|null
     */
    public function get($key){
        $cookie = $this->redis->get($key);

        if(isset($cookie)){
            $cert = new Certificate(ROOT_PATH.$this->path['private'],ROOT_PATH.$this->path['public']);
            $val = $cert->privDecrypt($cookie);
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

        $cert = new Certificate(ROOT_PATH.$this->path['private'],ROOT_PATH.$this->path['public']);
        $val = $cert->publicEncrypt($val);
        return $this->redis->set($key,$val);
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
        return $this->redis->del($key);
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
        return $this->redis->del($key);
    }

    /**
     * 清除所有cookie
     * @param $key
     * @param $val
     * @param $arr_cookie_options
     * @return void
     */
    public function clear($key,$val,$arr_cookie_options=[]){
        $val = '';
        $arr_cookie_options['expires'] = time() - 1000;
        foreach ($_COOKIE as $cookieName => $cookieValue) {
            if($cookieName!='PHPSESSID'){
                setcookie($cookieName, '', $arr_cookie_options);
            }
        }

        $this->redis->flushdb();
    }

    /**
     * @param $expire
     * 过期设置
     */
    public function expire($key,$expire){
        return $this->redis->expire($key,$expire);
    }
}