<?php
namespace Plantation\Clover\Functions;

/**
 * @param string $namespace
 * @return string
 * 创建唯一id
 */
function createGuid($namespace = '') {
    static $guid = '';

    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['PHP_SELF'];
    $data .= $_SERVER['REMOTE_PORT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $data .= generateGuid($namespace);
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    if($namespace){
        $prefix = $namespace .
            round(microtime(true)*1000 ).
            '-';
    }else{
        $prefix = '';
    }

    $guid = '' .
        $prefix .
        substr($hash, 0, 8) .
        '-' .
        substr($hash, 8, 4) .
        '-' .
        substr($hash, 12, 4) .
        '-' .
        substr($hash, 16, 4) .
        '-' .
        substr($hash, 20, 12) .
        '';
    return $guid;
}

function createGuidOrder($namespace = '') {
    static $guid = '';

    $uid = uniqid("", true);
    $data = $namespace;
    $data .= generateGuid($namespace);
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    if($namespace){
        $prefix = $namespace .
            round(microtime(true)*1000 ).
            '-';
    }else{
        $prefix = '';
    }

    $guid = '' .
        $prefix .
        substr($hash, 0, 8) .
        '-' .
        substr($hash, 8, 4) .
        '-' .
        substr($hash, 12, 4) .
        '-' .
        substr($hash, 16, 4) .
        '-' .
        substr($hash, 20, 12) .
        '';
    return $guid;
}


/**
 * @param $prefix
 * @return string
 * 生成唯一id
 */
function generateGuid($prefix=''){
    //假设一个机器id
    $machineId = mt_rand(100000,999999);

    //41bit timestamp(毫秒)
    $time = floor(microtime(true) * 1000);

    //0bit 未使用
    $suffix = 0;

    //datacenterId  添加数据的时间
    $base = decbin(pow(2,40) - 1 + $time);

    //workerId  机器ID
    $machineid = decbin(pow(2,9) - 1 + $machineId);

    //毫秒类的计数
    $random = mt_rand(1, pow(2,11)-1);

    $random = decbin(pow(2,11)-1 + $random);
    //拼装所有数据
    $base64 = $suffix.$base.$machineid.$random;
    //将二进制转换int
    $base64 = bindec($base64);

    $id = sprintf('%.0f', $base64);

    return $prefix.$id;
}