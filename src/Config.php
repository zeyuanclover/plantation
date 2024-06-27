<?php


namespace Plantation\Clover;

use Plantation\Clover\File;
use http\Cookie;

class Config
{
    public static $__loadArr;
    private static $instance;
    private $dir;

    /**
     * Config constructor.
     * @param $dir
     * 构造函数
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    /**
     * @param $dir
     * @return Config
     * 实例化
     */
    public static function instance($dir=null)
    {
        return new Config($dir);
    }

    /**
     * 载入某个目录下所有文件
     */

    function scanAll($return = false,$dirPath=null)
    {
        if (!$dirPath){
            $dirPath = $this->dir;
        }

        if (!is_dir($dirPath)) {
            return;
        }

        $dirHandle = opendir($dirPath);
        while (false !== ($file = readdir($dirHandle))) {
            if ($file != '.' && $file != '..') {
                $filePath = $dirPath . '/' . $file;
                if (is_dir($filePath)) {
                    $this->scanAll($return,$filePath);
                } else {
                    $data = include($filePath);

                    $dirArr = [];
                    if ($data && $return == true) {
                        if (isset($data['content'])) {
                            $dir = explode(DIRECTORY_SEPARATOR, $dirPath);
                            $dir = array_filter($dir);

                            if (count($dir) > 0) {
                                if ($dir[count($dir) - 1] != 'Configs' && $dir[count($dir) - 1] != '') {
                                    $dirKey = array_search('Configs', $dir);
                                    foreach ($dir as $key => $val) {
                                        if ($key <= $dirKey) {
                                            unset($dir[$key]);
                                        }
                                    }
                                } else {
                                    $dir = [];
                                }
                            }

                            $dirNameNew = end($dir);
                            $dirNameNew = str_replace('/', DIRECTORY_SEPARATOR, $dirNameNew);
                            $dirNameNew = explode(DIRECTORY_SEPARATOR, $dirNameNew);
                            $fileName = rtrim($file, '.php');
                            $dirNameNew[] = $fileName;
                            $dirNameNew = array_filter($dirNameNew);
                            $dirNameNew = array_keys(array_flip($dirNameNew));

                            self::$__loadArr[implode('.', $dirNameNew)] = $data['content'];
                        }
                    }
                }
            }
        }

        closedir($dirHandle);
        return self::$__loadArr;
    }

    /**
     * 清除数据
     */
    public function clearConfigData(){
        self::$__loadArr = [];
    }

    /**
     * PHP 非递归实现查询该目录下所有文件
     * @param unknown $dir
     * @return multitype:|multitype:string
     */
    function scanFiles($name=null)
    {
        $dir = $this->dir . DIRECTORY_SEPARATOR . $name;
        $absolutePath = $dir;
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
                    $absolutePath = str_replace('\\','/',$absolutePath);
                    $relativePath = str_replace($absolutePath,'',$path);
                    $relativePath = str_replace('.php','',$relativePath);
                    $data = include ($path);
                    $rt [$relativePath] = $data['content'];
                }
            }
        } while ($dirs); // 直到栈中没有目录
        return $rt;
    }

    /**
     * @param $paths
     * @return array|mixed
     * 路径转多维数组
     */
    function pathsToTree($paths) {
        $tree = array();
        foreach ($paths as $path=>$value) {
            $parts = explode('/', $path);
            $current = &$tree;
            $cc = end($parts);
            foreach ($parts as $part) {
                if (!isset($current[$part])) {
                    if ($cc == $part){
                        $current[$part] = $value;
                    }else{
                        $current[$part] = [];
                    }
                }
                $current = &$current[$part];
            }
        }
        return $tree;
    }
}