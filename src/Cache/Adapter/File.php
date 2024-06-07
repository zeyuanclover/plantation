<?php

namespace Plantation\Clover\Cache\Adapter;

class File
{
    protected static $path = null;
    protected static $instance;
    protected static $obj = null;
    public static function instance($instance,$path){
        $configs['path'] = $path;
        if (isset($configs['path'])){
            self::$path = $configs['path'];
        }else{
            self::$path = ROOT_PATH . 'Run' . DIRECTORY_SEPARATOR .'Cache';
        }
        if (!is_dir(self::$path)){
            mkdir(self::$path,0777,true);
        }
        self::$path = rtrim(self::$path,'/');
        self::$path = rtrim(self::$path,'\\');
        self::$path .= DIRECTORY_SEPARATOR;
        if (!is_dir(self::$path)){
            mkdir(self::$path,0777,true);
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
            'expire'=>time()+$expire,
        ];

        if ($expire==true){
            $data['expire'] = $expire;
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
            if ($expire==true){
                $data['expire'] = $expire;
            }else{
                $data['expire'] = time()+$expire;
            }
            file_put_contents($file,'<?php return '.var_export($data,true).';');
        }
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
}