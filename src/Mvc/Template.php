<?php
namespace Plantation\Clover\Mvc;

use Plantation\Clover\Message;

class Template{

    private $path;

    /**
     * Template constructor.
     * @param $data
     * 构造函数
     */
    public function __construct($data)
    {
       $path = $data['path'] . 'Template' . DIRECTORY_SEPARATOR . $data['theme'];
        if(!is_dir($path)){
            Message::instance('json')->send([
                'message'=>'Line ['.__LINE__.'] -文件'.__FILE__.':模板文件夹 '.$path.'不存在！',
                'code'=>'File-1001',
                'error'=>false
            ]);
        }
        $this->path = $path;
    }

    /**
     * @param $name
     * @return string
     * 查找模板文件位置
     */
    public function fetch($name){
        $path = $this->path . DIRECTORY_SEPARATOR . $name;
        if(!is_file($path)){
            Message::instance('json')->send([
                'message'=>'Line ['.__LINE__.'] -文件'.__FILE__.':模板文件 '.$path.'不存在！',
                'code'=>'File-1001',
                'error'=>false
            ]);
        }
        return $path;
    }
}