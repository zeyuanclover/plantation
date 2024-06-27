<?php

namespace Plantation\Clover\Cache\Adapter;

use Plantation\Clover\Safe\Adapter\Certificate;

class File
{
    protected static $path = null;
    protected static $instance;
    protected static $obj = null;
    public static function instance($path){
        if (!is_dir($path)){
            mkdir($path,0777,true);
        }

        $path = str_replace('/',DIRECTORY_SEPARATOR,$path);
        if (substr($path,-1)!==DIRECTORY_SEPARATOR){
            $path .= DIRECTORY_SEPARATOR;
        }

        if(is_dir($path)){
            self::$path = $path;
        }
        return new File();
    }

    // 获取缓存
    public function get($name){
        $file = self::$path.$name.'.php';
        if(is_file($file)){
            $cache = include ($file);
            if(isset($cache['content'])){
                if(isset($cache['expire'])){
                    if ($cache['expire']==true||$cache['expire']>time()){
                        return $cache['content'];
                    }
                }
            }
            return null;
        }else{
            return null;
        }
    }

    // 设置缓存
    public function set($name,$value,$expire=true){
        $file = self::$path.$name.'.php';
        $data = [
            'content'=>$value,
            'expire'=>$expire,
        ];

        if ($expire!==true){
            $data['expire'] = $expire + time();
        }

        file_put_contents($file,'<?php return '.var_export($data,true).';');
    }

    /**
     * @param $name
     * @param $expire
     * 设置有效期
     */
    public function expire($name,$expire){
        $file = self::$path.$name.'.php';
        if(is_file($file)) {
            $data = include($file);
            $data['expire'] = $expire +time();
            file_put_contents($file,'<?php return '.var_export($data,true).';');
        }
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

    /**
     * @param $name
     * 删除文件
     */
    public function delete($name)
    {
        $file = self::$path . $name . '.php';
        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * @param $key
     * @return int|mixed
     * 获取过期时间
     */
    public function ttl($key){
        $file = self::$path.$name.'.php';
        if(is_file($file)){
            $cache = include ($file);
            if(isset($cache['content'])){
                if(isset($cache['expire'])){
                   return $cache['expire']-time();
                }
            }
            return 0;
        }else{
            return 0;
        }
    }
}