<?php

spl_autoload_register(function ($class) {
    if (false !== stripos($class, 'EasyWeChat')) {
        var_dump($class);
        require_once __DIR__.'/src/'.str_replace('\\', '/', $class).'.php';
    }
});
