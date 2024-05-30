<?php


namespace Plantation\Clover;
use Plantation\Clover\Message;

class File
{
    private $dirName;

    private static $instance;

    /**
     * File constructor.
     * @param $dirName
     * 构造函数
     */
    public function __construct($dirName)
    {
        if (!is_dir($dirName)){
            mkdir($dirName,0777,true);
        }

        $this->dirName = $dirName;
    }

    /**
     * @param $dirName
     * @return File
     * 加载函数
     */
    public static function instance($dirName){
        if (!self::instance){
            self::$instance = new File($dirName);
            return self::$instance;
        }else{
            return self::instance;
        }
    }

    /**
     * @param $fileName
     * 包含文件
     */
    public function load($fileName){
        $filePath = $this->dirName.DIRECTORY_SEPARATOR.$fileName;
        if (is_file($filePath)){
            include ($filePath);
        }else{
            Message::instance('json')->send([
                'message'=>'Line '.__LINE__.'-文件'.__FILE__.'-无法包含文件，因为这个文件不存在！',
                'code'=>'File-1001',
                'error'=>false
            ]);
        }
    }
}