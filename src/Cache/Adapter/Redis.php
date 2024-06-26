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
        return new Redis($instance);
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
        return $this->redis->get($key);
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
        $this->redis->flushdb();
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
     * 获取未解密数据
     */
    public function getNotDecrypted($key){
       return $this->get($key);
    }

    /**
     * @param $val
     * @return null
     * 获取解密的数据
     */
    public function getDecrypted($val){
        return $val;
    }
}