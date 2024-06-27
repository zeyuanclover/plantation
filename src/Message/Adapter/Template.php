<?php
namespace Plantation\Clover\Message\Adapter;

class Template
{
    /**
     * @param $data
     * 发功json消息
     */
    public function send($data){
        if (isset($data['path'])){
            if (is_file($data['path'])){
                include ($data['path']);
            }
        }
    }
}