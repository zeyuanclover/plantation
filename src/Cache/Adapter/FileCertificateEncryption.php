<?php

namespace Plantation\Clover\Cache\Adapter;

use Plantation\Clover\Safe\Adapter\Certificate;

class FileCertificateEncryption
{
    protected static $path = null;
    protected static $obj = null;
    private static $cePath;
    public static function instance($path,$perm){
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

        self::$cePath = $perm;
        return self::$obj = new FileCertificateEncryption();
    }

    // 获取缓存
    public function get($name){
        $file = self::$path.$name.'.php';
        if(is_file($file)){
            $cache = include ($file);
            if(isset($cache['content'])){
                if(isset($cache['expire'])){
                    if ($cache['expire']==true||$cache['expire']>time()){
                        $cert = new Certificate(self::$cePath['private'],self::$cePath['public']);
                        $val = json_decode($cert->privDecrypt($cache['content']),true);
                        return $val;
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
        $cert = new Certificate(self::$cePath['private'],self::$cePath['public']);

        $data = [
            'content'=>'',
            'expire'=>time()+$expire,
        ];

        if ($expire==true){
            $data['expire'] = $expire;
        }

        $val = $cert->publicEncrypt(json_encode(['data'=>$value]));
        $data['content'] = $val;

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

    /**
     * @param $name
     * @return mixed|null
     * 获取未解密的数据
     */
    public function getNotDecrypted($name){
        $file = self::$path.$name.'.php';
        if(is_file($file)){
            $cache = include ($file);
            if(isset($cache['content'])){
                if(isset($cache['expire'])){
                    if ($cache['expire']==true||$cache['expire']>time()){
                        if (isset($cache['content'])){
                            return $cache['content'];
                        }else{
                            return null;
                        }
                    }
                }
            }
            return null;
        }else{
            return null;
        }
    }

    /**
     * @param $val
     * @return mixed
     * 获得解密数据
     */
    public function getDecrypted($val){
        $cert = new Certificate(self::$cePath['private'],self::$cePath['public']);
        return json_decode($cert->privDecrypt($val),true);
    }
}