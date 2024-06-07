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
class Mysql{

    private $path;
    private $table;
    private $instance;

    /**
     * 调用函数
     * @return Cookie
     */
    public static function instance($instance,$table){
        return new Mysql($instance,$table);
    }

    /**
     * Redis constructor.
     * @param $instance
     * 构造函数
     */
    public function __construct($instance,$table){
        $this->instance = $instance;
        $this->table = $table;
    }

    /**
     * 获取cookie
     * @param $key
     * @return mixed|null
     */
    public function get($key){
        $data = $this->instance->where('Name',$key)->getOne($this->table);
        if ($data){
            if ($data['Expire']==1 || $data['Expire'] > time()){
                return $data['Value'];
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
    public function set($key,$value,$expire=true){
        if (is_array($value)){
            $value = json_encode($value);
        }

        if ($expire === true){

        }else{
            if ($expire>0){
                $expire = time() + $expire;
            }
        }

        $hasData = $this->instance->where('Name',$key)->getValue($this->table,'ID');
        if(!$hasData){
            return $this->instance->insert($this->table,['Value'=>$value,'Name'=>$key,'Expire'=>$expire,'CreateAt'=>time()]);
        }else{
            return $this->instance->where('Name',$key)->update($this->table,['Value'=>$value,'Expire'=>$expire,'UpdateAt'=>time()]);
        }
    }

    /**
     * 删除cookie
     * @param $key
     * @param $arr_cookie_options
     * @return true
     */
    public function remove($key){
        return $this->instance->where('Name',$key)->delete($this->table);
    }

    /**
     * 删除cookie
     * @param $key
     * @return bool
     */
    public function delete($key){
        return $this->instance->where('Name',$key)->delete($this->table);
    }

    /**
     * 清除所有cookie
     * @param $key
     * @param $val
     * @param $arr_cookie_options
     * @return void
     */
    public function clear(){
        return $this->instance->rawQueryOne('TRUNCATE hlm_'.$this->table);
    }

    /**
     * @param $expire
     * 过期设置
     */
    public function expire($key,$expire){
        if ($expire === true){

        }else{
            if ($expire>0){
                $expire = time() + $expire;
            }
        }

        $hasData = $this->instance->where('Name',$key)->getValue($this->table,'ID');
        if(!$hasData){
            return false;
        }else{
            return $this->instance->where('Name',$key)->update($this->table,['Expire'=>$expire,'UpdateAt'=>time()]);
        }
    }
}