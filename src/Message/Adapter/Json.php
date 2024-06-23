<?php
namespace Plantation\Clover\Message\Adapter;

class Json
{
    /**
     * @param $data
     * 发功json消息
     */
    public function send($data,$die=true){
        header('Content-Type: application/json; charset=utf-8');
        if ($die){
            die(json_encode($data));
        }else{
            echo json_encode($data);
        }
    }
}