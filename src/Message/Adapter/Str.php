<?php
namespace Plantation\Clover\Message\Adapter;

class Str
{
    /**
     * @param $data
     * 发功json消息
     */
    public function send($data){
        echo $data;
    }
}