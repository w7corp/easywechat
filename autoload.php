<?php

spl_autoload_register(function($class){
    if (false !== stripos($class, 'Overtrue\Wechat')) {
        require_once __DIR__ ."/src/". str_replace('\\', '/', substr($class, 8)) . ".php";
    }
});