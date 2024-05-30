<?php

namespace Plantation\Clover\Cache\Adapter;

use Plantation\Clover\Safe\Adapter\Certificate;

class FileCertificateEncryption
{
    protected static $path = null;
    protected static $obj = null;
    private static $cePath;
    public static function instance($configs=[]){
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

        self::$cePath = $configs['perm'];
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
                        $cert = new Certificate(ROOT_PATH.self::$cePath['private'],ROOT_PATH.self::$cePath['public']);
                        $val = json_decode($cert->privDecrypt($cache['content']),true);
                        if (isset($val['data'])){
                            return $val['data'];
                        }
                        return null;
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
        $cert = new Certificate(ROOT_PATH.self::$cePath['private'],ROOT_PATH.self::$cePath['public']);

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
}