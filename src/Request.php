<?php
namespace Plantation\Clover;

use http\Header;

class Request
{

    /**
     * 构造函数
     * Request constructor.
     * @param $config
     */
    public function __construct(){
    }

    /**
     * @param array $config
     * @return Request
     * 加载函数
     */
    public static function instance(){
        return new Request();
    }

    /**
     * @param $url
     * @return mixed|string|null
     * 获取子域名
     */
    function getSubdomain() {
        $url = $this->getUrl();
        $parsedUrl = parse_url($url);
        $hostParts = explode('.', $parsedUrl['host']);
        if (count($hostParts) < 3) {
            return null; // 不是子域名
        }
        return $hostParts[0]; // 子域名是第一部分
    }

    /**
     * @return mixed|string
     * 获取用户真实ip
     */
    function getUserRealIP() {
        $headers = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );

        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && filter_var($_SERVER[$header], FILTER_VALIDATE_IP)) {
                return $_SERVER[$header];
            }
        }

        return 'UNKNOWN';
    }

    /**
     * @return bool
     * 是否https
     */
    function isHttps() {
        if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')) {
            return true;
        } elseif (isset($_SERVER['REQUEST_SCHEME']) && strtolower($_SERVER['REQUEST_SCHEME']) == 'https') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_CLIENT_SCHEME']) && strtolower($_SERVER['HTTP_X_CLIENT_SCHEME']) == 'https') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param null $name
     * @return array
     * 获取get方法提交参数
     */
    public function get($name=null){

        // 返回所有
        if (!$name){
            return $_GET;
        }

        // 返回某一个
        if (isset($_GET[$name])){
            return $_GET[$name];
        }else{
            return null;
        }
    }

    /**
     * @param string $name
     * @return mixed|null
     * input
     */
    function input($name=''){
        // 获取请求的原始数据体
        $input = file_get_contents('php://input');
        // 解析原始数据体
        parse_str($input, $parsedData);
        if(!$name){
            return $parsedData;
        }else{
            if(isset($parsedData[$name])){
                return $parsedData[$name];
            }else{
                return null;
            }
        }
    }

    /**
     * @param null $name
     * @return array|mixed|null
     * request
     */
    public function request($name=null){

        // 返回所有
        if (!$name){
            return $_REQUEST;
        }

        // 返回某一个
        if (isset($_REQUEST[$name])){
            return $_REQUEST[$name];
        }else{
            return null;
        }
    }

    /**
     * @param null $name
     * @return array|mixed|null
     * POST 参数
     */
    public function post($name=null){

        // 返回所有
        if (!$name){
            return $_POST;
        }

        // 返回某一个
        if (isset($_POST[$name])){
            return $_POST[$name];
        }else{
            return null;
        }
    }

    /**
     * @param $url
     * @return string
     * 获取域名
     */
    public function getDomain($url){
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';

        // 获取主机名(包括域名和端口)
        $host = $_SERVER['HTTP_HOST'];

        $uri = '';
        if ($url){
            $uri = $url;
        }

        // 构建完整的URL
        return $protocol . '://' . $host . $uri;
    }

    /**
     * @return string
     * 获得完整url
     */
    public function getUrl($url=null){
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';

        // 获取主机名(包括域名和端口)
        $host = $_SERVER['HTTP_HOST'];

        if ($url){
            $uri = $url;
        }else{
            // 获取资源路径
            $uri = $_SERVER['REQUEST_URI'];
        }

        // 构建完整的URL
        return $protocol . '://' . $host . $uri;
    }

    /**
     * @param $url
     * 跳转url
     */
    public function redirectx($url){
        // 是否有目录符
        if (strpos($url,'/')===false){
            $url = '/' . $url;
        }

        // 是否有域名
        if (strpos($url,':')===false){
            $url = $this->getUrl($url);
        }

        // 跳转
        Header('Location:'.$url);
    }

    /**
     * @return bool
     * 是否get请求
     */
    function isGet(){
        return $this->getRequestMethod('GET');
    }

    /**
     * @return bool
     * 是否post请求
     */
    function isPost(){
        return $this->getRequestMethod('POST');
    }

    /**
     * @return bool
     * 是否put请求
     */
    function isPut(){
        return $this->getRequestMethod('PUT');
    }

    /**
     * @return bool
     * 是否delete请求
     */
    function isDelete(){
        return $this->getRequestMethod('DELETE');
    }

    /**
     * @return bool
     * 是否head请求
     */
    function isHead(){
        return $this->getRequestMethod('HEAD');
    }

    /**
     * @return bool
     * 是否optins请求
     */
    function isOptions(){
        return $this->getRequestMethod('OPTIONS');
    }

    /**
     * @param $method
     * @return bool
     * 工作函数
     */
    function getRequestMethod($method){
        if($_SERVER['REQUEST_METHOD']==$method){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $name
     * @return mixed|null
     * server work
     */
    function serverWork($name){
        if ($name){
            if(isset($_SERVER[$name])){
                return $_SERVER[$name];
            }
        }else{
            return null;
        }
    }

    /**
     * 当前正在执行脚本的文件名，与 document root相关。
     */
    function getServerPhpSelf(){
        return $this->serverWork('PHP_SELF');
    }

    /**
     * 传递给该脚本的参数
     */
    function getServerArgv(){
        return $this->serverWork('argv');
    }

    /**
     * 包含传递给程序的命令行参数的个数（如果运行在命令行模式）。
     */
    function getServerArgc(){
        return $this->serverWork('argc');
    }

    /**
     * 服务器使用的 CGI 规范的版本。例如，“CGI/1.1”。
     */
    function getServerGatewayInterface(){
        return $this->serverWork('GATEWAY_INTERFACE');
    }

    /**
     * 当前运行脚本所在服务器主机的名称。
     */
    function getServerServerName(){
        return $this->serverWork('SERVER_NAME');
    }

    /**
     * 服务器标识的字串，在响应请求时的头部中给出。
     */
    function getServerServerSoftware(){
        return $this->serverWork('SERVER_SOFTWARE');
    }

    /**
     * 请求页面时通信协议的名称和版本。例如，“HTTP/1.0”。
     */
    function getServerServerProtocol(){
        return $this->serverWork('SERVER_PROTOCOL');
    }

    /**
     * 访问页面时的请求方法。例如：“GET”、“HEAD”，“POST”，“PUT”
     */
    function getServerRequestMethod(){
        return $this->serverWork('REQUEST_METHOD');
    }

    /**
     * 查询(query)的字符串。
     */
    function getServerQueryString(){
        return $this->serverWork('QUERY_STRING');
    }

    /**
     * 当前运行脚本所在的文档根目录。在服务器配置文件中定义。
     */
    function getServerDocumentRoot(){
        return $this->serverWork('DOCUMENT_ROOT');
    }

    /**
     * 当前请求的 Accept: 头部的内容
     */
    function getServerHttpAccept(){
        return $this->serverWork('HTTP_ACCEPT');
    }

    /**
     * 当前请求的 Accept-Charset: 头部的内容。例如：“iso-8859-1,*,utf-8”。
     */
    function getServerHttpAcceptCharset(){
        return $this->serverWork('HTTP_ACCEPT_CHARSET');
    }

    /**
     * 当前请求的 Accept-Encoding: 头部的内容。例如：“gzip”。
     */
    function getServerHttpAcceptEncoding(){
        return $this->serverWork('HTTP_ACCEPT_ENCODING');
    }

    /**
     * 当前请求的 Accept-Language: 头部的内容。例如：“en”。
     */
    function getServerHttpAcceptLanguage(){
        return $this->serverWork('HTTP_ACCEPT_LANGUAGE');
    }

    /**
     * 当前请求的 Connection: 头部的内容。例如：“Keep-Alive”。
     */
    function getServerHttpConnection(){
        return $this->serverWork('HTTP_CONNECTION');
    }

    /**
     * 当前请求的 Host: 头部的内容。
     */
    function getServerHttpHost(){
        return $this->serverWork('HTTP_HOST');
    }

    /**
     * 链接到当前页面的前一页面的 URL 地址。
     */
    function getServerHttpReferer(){
        return $this->serverWork('HTTP_REFERER');
    }

    /**
     * 当前请求的 User_Agent: 头部的内容。
     */
    function getServerHttpUserAgent(){
        return $this->serverWork('HTTP_USER_AGENT');
    }

    /**
     * 如果通过https访问,则被设为一个非空的值(on)，否则返回off
     */
    function getServerHttps(){
        return $this->serverWork('HTTPS');
    }

    /**
     * 正在浏览当前页面用户的 IP 地址。
     */
    function getServerRemoteAddr(){
        return $this->serverWork('REMOTE_ADDR');
    }

    /**
     * 正在浏览当前页面用户的主机名。
     */
    function getServerRemoteHost(){
        return $this->serverWork('REMOTE_HOST');
    }

    /**
     * 用户连接到服务器时所使用的端口。
     */
    function getServerRemotePort(){
        return $this->serverWork('REMOTE_PORT');
    }

    /**
     * 当前执行脚本的绝对路径名。
     */
    function getServerScriptFilename(){
        return $this->serverWork('SCRIPT_FILENAME');
    }

    /**
     * 管理员信息
     */
    function getServerServerAdmin(){
        return $this->serverWork('SERVER_ADMIN');
    }

    /**
     * 服务器所使用的端口
     */
    function getServerServerPort(){
        return $this->serverWork('SERVER_PORT');
    }

    /**
     * 包含服务器版本和虚拟主机名的字符串。
     */
    function getServerServerSignature(){
        return $this->serverWork('SERVER_SIGNATURE');
    }

    /**
     * 当前脚本所在文件系统（不是文档根目录）的基本路径。
     */
    function getServerPathTranslated(){
        return $this->serverWork('PATH_TRANSLATED');
    }

    /**
     * 包含当前脚本的路径。这在页面需要指向自己时非常有用。
     */
    function getServerScriptName(){
        return $this->serverWork('SCRIPT_NAME');
    }

    /**
     * 访问此页面所需的 URI。例如，“/index.html”。
     */
    function getServerRequestUri(){
        return $this->serverWork('REQUEST_URI');
    }

    /**
     * 当 PHP 运行在 Apache 模块方式下，并且正在使用 HTTP 认证功能，这个变量便是用户输入的用户名。
     */
    function getServerPhpAuthUser(){
        return $this->serverWork('PHP_AUTH_USER');
    }

    /**
     * 当 PHP 运行在 Apache 模块方式下，并且正在使用 HTTP 认证功能，这个变量便是用户输入的密码。
     */
    function getServerPhpAuthPw(){
        return $this->serverWork('PHP_AUTH_PW');
    }

    /**
     * 当 PHP 运行在 Apache 模块方式下，并且正在使用 HTTP 认证功能，这个变量便是认证的类型。
     */
    function getServerAuthType(){
        return $this->serverWork('AUTH_TYPE');
    }

    /**
     * 透过代理服务器取得客户端的真实 IP 地址
     */
    function getServerHttpXFowardedFor(){
        return $this->serverWork('HTTP_X_FORWARDED_FOR');
    }

    /**
     * 代理服务器IP
     */
    function getServerHttpVia(){
        return $this->serverWork('HTTP_VIA');
    }

    /**
     * 客户端IP
     */
    function getServerHttpClientIp(){
        return $this->serverWork('HTTP_CLIENT_IP');
    }
}