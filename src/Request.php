<?php
namespace Plantation\Clover;

class Request
{

    /**
     * 构造函数
     * Request constructor.
     * @param $config
     */
    public function __construct($config){

    }

    /**
     * @param array $config
     * @return Request
     * 加载函数
     */
    public static function instance($config=[]){
        return new Request($config);
    }

    /**
     * @return string
     * 获得完整url
     */
    public function getUrl(){
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';

        // 获取主机名(包括域名和端口)
        $host = $_SERVER['HTTP_HOST'];

        // 获取资源路径
        $uri = $_SERVER['REQUEST_URI'];

        // 构建完整的URL
        return $protocol . '://' . $host . $uri;
    }
}