<?php
function url($url){
    return \Plantation\Clover\Request::instance()->getUrl($url);
}