<?php
namespace Plantation\Clover\Mvc;

use DI\Definition\Resolver\ParameterResolver;
use Plantation\Clover\Cache;
use Plantation\Clover\Message;
use Plantation\Clover\Request;
use Plantation\Clover\Config;

use Plantation\Clover\Cache\Adapter\File;
use Plantation\Clover\Cache\Adapter\FileCertificateEncryption;

class Core{
    public function run(){

        /**
         * 确认应用，找到具体应用目录，检查环境
         */
        $requestUri = Request::instance()->getUrl();
        $parseUrlArr = parse_url($requestUri);

        /**
         * 默认app名称
         */
        $appName = 'home';

        /**
         * url数组
         */
        $urlArr = [];

        /**
         * 查找app名称
         */
        $appUrl = '/';

        $url = $parseUrlArr['path'];
        if ($url!='/'){
            $urlArr = explode('/',$url);
            $appName = $urlArr['1'];
            $appUrl = '/'.$urlArr['1'];

            // 防止两个下划线
            if ($urlArr['1']==''){
                $appName = 'home';
                $appUrl = '/';
            }
        }

        $urlArr = null;
        $appName = ucfirst($appName);

        /**
         * env 载入
         */
        $env = [];
        $envPath = ROOT_PATH . 'env.ini';
        if (is_file($envPath)){
            $env = parse_ini_file($envPath);
        }else{
            Message::instance('json')->send([
                'message'=>'Line ['.__LINE__.'] -文件'.__FILE__.'-请配置env.ini！',
                'code'=>'File-1001',
                'error'=>false
            ]);
        }

        /**
         * 报错信息
         */
        if ($env['Debug']==true){
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }else{
            error_reporting(0);
        }

        /**
         * 时区设置
         */
        if ($env['TimeZone']){
            date_default_timezone_set($env['TimeZone']);
        }

        /**
         * 验证app配置里面是否有
         */
        if ($env['Cache']==true){
            $commonConfigPath = ROOT_PATH .'Application' . DIRECTORY_SEPARATOR . 'Config';
            $commonConfigCachePath = ROOT_PATH .'Run' . DIRECTORY_SEPARATOR . 'Cache';

            $commonConfigCache = Cache::instance( File::instance('',$commonConfigCachePath))->get('CommonConfig');

            if ($commonConfigCache){
                $commonConfig = $commonConfigCache;
                $commonConfigCache = null;
            }else{
                $commonConfig = Config::instance( $commonConfigPath)->scanAll(true);
                Config::instance()->clearConfigData();
                Cache::instance( File::instance('',$commonConfigCachePath))->set('CommonConfig',$commonConfig);
            }

            /**
             * 销毁变量
             */
            $commonConfigPath = null;
            $appConfigPath = null;

            if(!isset($commonConfig['Config.Application'])||!isset($commonConfig['Config.Application']['app'])){
                Message::instance('json')->send([
                    'message'=>'Line ['.__LINE__.'] -文件'.__FILE__.'-请配置Application.php！',
                    'code'=>'File-1001',
                    'error'=>false
                ]);
            }

            $appCommonConfig = $commonConfig['Config.Application'];
            $realAppName = null;
            if (isset($appCommonConfig['app'][$appUrl]['name'])){
                $realAppName = $appCommonConfig['app'][$appUrl]['name'];
            }else{
                $realAppName = $appCommonConfig['appDefault'];
            }

            /**
             * app 专用配置
             */
            $appConfigCachePath = ROOT_PATH .'Run' . DIRECTORY_SEPARATOR . 'Cache' .DIRECTORY_SEPARATOR. 'Application';
            $appConfigCache = Cache::instance( File::instance('',$appConfigCachePath))->get('AppConfig');

            if ($appConfigCache){
                $appConfig = $appConfigCache;
            }else{
                $appConfigPath = ROOT_PATH .'Application' . DIRECTORY_SEPARATOR . 'Src' . DIRECTORY_SEPARATOR .  $appName . DIRECTORY_SEPARATOR . 'Config';
                $appConfig = Config::instance( $appConfigPath)->scanAll(true, $appConfigPath);
                Config::instance()->clearConfigData();
                Cache::instance( File::instance('',$appConfigCachePath))->set('AppConfig',$appConfig);
            }
        }else{
            $commonConfigPath = ROOT_PATH .'Application' . DIRECTORY_SEPARATOR . 'Config';
            $commonConfig = Config::instance( $commonConfigPath)->scanAll(true);
            Config::instance()->clearConfigData();

            /**
             * 销毁变量
             */
            $commonConfigPath = null;
            $appConfigPath = null;

            if(!isset($commonConfig['Config.Application'])||!isset($commonConfig['Config.Application']['app'])){
                Message::instance('json')->send([
                    'message'=>'Line '.__LINE__.'-文件'.__FILE__.'-请配置Application.php！',
                    'code'=>'File-1001',
                    'error'=>false
                ]);
            }

            $appCommonConfig = $commonConfig['Config.Application'];

            $realAppName = null;
            if (isset($appCommonConfig['app'][$appUrl]['name'])){
                $realAppName = $appCommonConfig['app'][$appUrl]['name'];
            }else{
                $realAppName = $appCommonConfig['appDefault'];
            }

            /**
             * app 专用配置
             */
            $appConfigPath = ROOT_PATH .'Application' . DIRECTORY_SEPARATOR . 'Src' . DIRECTORY_SEPARATOR .  $appName . DIRECTORY_SEPARATOR . 'Config';
            $appConfig = Config::instance( $appConfigPath)->scanAll(true, $appConfigPath);
            Config::instance()->clearConfigData();
        }

        /**
         * 载入容器
         */
        $appCotainerPath = ROOT_PATH .'Application' . DIRECTORY_SEPARATOR . 'Src' . DIRECTORY_SEPARATOR .  $appName . DIRECTORY_SEPARATOR . 'Container' .DIRECTORY_SEPARATOR . 'Container.php';

        $container = null;
        if (is_file($appCotainerPath)){
            $container = include($appCotainerPath);
        }

        /**
         * 路由
         */
        $appPath = ROOT_PATH . 'Application' . DIRECTORY_SEPARATOR . 'Src' . DIRECTORY_SEPARATOR . ucfirst($realAppName) . DIRECTORY_SEPARATOR;

        $appRoutePath = $appPath . 'Route' . DIRECTORY_SEPARATOR .'Web.php';
        if(!is_file($appRoutePath)){
            Message::instance('json')->send([
                'message'=>'Line '.__LINE__.'-文件'.__FILE__.'-请配置Route.php！',
                'code'=>'File-1001',
                'error'=>false
            ]);
        }else{
            $dispatcher = include $appRoutePath;
        }

        define('APP_PATH',$appPath);

        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                $path404 = $appPath . 'Template' . DIRECTORY_SEPARATOR . '404.html';
                include $path404;
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                // ... 405 Method Not Allowed
                $path405 = $appPath . 'Template' . DIRECTORY_SEPARATOR . '405.html';
                include $path405;
                break;
            case \FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                if(strpos($handler,'@')==false){
                    $controller = $handler;
                    $action = 'index';
                }else{
                    $name = explode('@',$handler);
                    $controller = $name[0];
                    $action = $name[1];
                }

                if($controller['0']!='\\') {
                    $controller = str_replace('Controller', '', $controller);
                }

                $map = [
                    'appPath'=>$appPath,
                    'realAppName'=>$realAppName,
                    'commonConfig' => $commonConfig,
                    'appConfig'=>$appConfig,
                    'controller'=>$controller.'Controller',
                    'action'=>$action,
                    'currentPage'=>$parseUrlArr['path'],
                    'currentUri'=>$uri,
                ];

                // 配置
                $_SERVER['appConfig'] = $appConfig;
                $_SERVER['env'] = $env;

                // 前置操作
                $appRouteBeforePath = $appPath . 'Route' . DIRECTORY_SEPARATOR .'Before.php';
                if(is_file($appRouteBeforePath)){
                    include($appRouteBeforePath);
                }

                // 定位控制器
                if($controller['0']=='\\'){
                    $app = $controller;
                }else{
                    $app = '\\Application\\'.$appName.'\\Controller\\'.ucfirst($controller).'Controller';
                }

                if(!class_exists($app)){
                    Message::instance('json')->send([
                        'message'=>'Line ['.__LINE__.'] -文件'.__FILE__.'-类'.$app.'不存在！',
                        'code'=>'File-1001',
                        'error'=>false
                    ]);
                }

                // 初始化控制器
                $instance = new $app($map,$container);

                // 方法是否存在
                if(!method_exists($instance,$action)){
                    Message::instance('json')->send([
                        'message'=>'Line ['.__LINE__.'] -文件'.__FILE__.'-类'.$app.'方法'.$action.'不存在！',
                        'code'=>'File-1001',
                        'error'=>false
                    ]);
                }

                // 载入functions
                $directory = ROOT_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'plantation' . DIRECTORY_SEPARATOR . 'clover' . DIRECTORY_SEPARATOR  . 'src' .  DIRECTORY_SEPARATOR . 'Functions'; // 替换为你的目录路径

                // 读取目录内容
                $files = scandir($directory);

                // 过滤掉当前目录(.)和上级目录(..)
                $files = array_diff($files, array('.', '..'));

                // 打印文件列表
                foreach ($files as $file) {
                    include $directory . DIRECTORY_SEPARATOR . $file;
                }

                $instance->$action($vars);

                // 后置操作
                $appRouteAfterPath = $appPath . 'Route' . DIRECTORY_SEPARATOR .'After.php';
                if(is_file($appRouteAfterPath)){
                    include($appRouteAfterPath);
                }
                break;
        }
    }
}