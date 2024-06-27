<?php
namespace Plantation\Clover\Functions;

function url($url){
    return \Plantation\Clover\Request::instance()->getUrl($url);
}