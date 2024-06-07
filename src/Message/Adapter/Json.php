<?php
namespace Plantation\Clover\Message\Adapter;

class Json
{
    /**
     * @param $data
     * 发功json消息
     */
    public function send($data){
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($data));
    }
}