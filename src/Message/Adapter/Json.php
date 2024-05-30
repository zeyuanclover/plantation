<?php
namespace Plantation\Clover\Message\Adapter;

class Json
{
    /**
     * @param $data
     * 发功json消息
     */
    public function send($data){
        die(json_encode($data));
    }
}