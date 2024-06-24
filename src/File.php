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
    public function __construct($dirName=null)
    {
        if ($dirName){
            if (!is_dir($dirName)) {
                mkdir($dirName, 0777, true);
            }

            if(substr($dirName,strlen($dirName)-1)==DIRECTORY_SEPARATOR){
                $this->dirName = substr($dirName,0,-1);
            }else{
                $this->dirName = $dirName;
            }
        }
    }

    /**
     * @param $dirName
     * @return File
     * 加载函数
     */
    public static function instance($dirName=null)
    {
       return new File($dirName);
    }

    /**
     * @param $fileName
     * 包含文件
     */
    public function load($fileName)
    {
        $filePath = $this->dirName . DIRECTORY_SEPARATOR . $fileName;
        if (is_file($filePath)) {
            include($filePath);
        } else {
            Message::instance('json')->send([
                'message' => 'Line ' . __LINE__ . '-文件' . __FILE__ . '-无法包含文件，因为这个文件不存在！',
                'code' => 'File-1001',
                'error' => false
            ]);
        }
    }

    /**
     * PHP 非递归实现查询该目录下所有文件
     * @param unknown $dir
     * @return multitype:|multitype:string
     */
    function scanfiles($name=null)
    {
        $dir = $this->dirName . DIRECTORY_SEPARATOR . $name;
        if (!is_dir($dir))
            return array();

        // 兼容各操作系统
        $dir = rtrim(str_replace('\\', '/', $dir), '/') . '/';

        // 栈，默认值为传入的目录
        $dirs = array($dir);

        // 放置所有文件的容器
        $rt = array();
        do {
            // 弹栈
            $dir = array_pop($dirs);

            // 扫描该目录
            $tmp = scandir($dir);

            foreach ($tmp as $f) {
                // 过滤. ..
                if ($f == '.' || $f == '..')
                    continue;

                // 组合当前绝对路径
                $path = $dir . $f;

                // 如果是目录，压栈。
                if (is_dir($path)) {
                    array_push($dirs, $path . '/');
                } else if (is_file($path)) { // 如果是文件，放入容器中
                    $rt [] = $path;
                }
            }
        } while ($dirs); // 直到栈中没有目录
        return $rt;
    }

    /**
     * @param string $dir
     * 删除目录下所有文件
     */
    function deleteDirectory() {
        $dir = $this->dirName . DIRECTORY_SEPARATOR;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->deleteDirectory($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    /**
     * @param $dirPath
     * 删除文件夹下所有文件
     */
    function deleteDir($dirPath=null) {
        if (!is_dir($dirPath)) {
            if(is_dir($this->dirName)){
                $dirPath = $this->dirName;
            }else{
                return false;
            }
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}