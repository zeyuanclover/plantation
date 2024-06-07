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
        if (!self::$instance) {
            self::$instance = new Config($dir);
            return self::$instance;
        } else {
            return self::$instance;
        }
    }

    /**
     * @param $fileName
     * 载入单个文件
     */
    public function loadSingle($fileName)
    {
        $content = File::instance($this->dir)->load($fileName);
        if (isset($content['content'])) {
            return $content['content'];
        } else {
            return null;
        }
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
     * @param $fileName
     * @param $data
     * 设置缓存
     */
    public function set($dir,$fileName,$data){
        Cache::instance('redis',$dir)->set();
    }
}