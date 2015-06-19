<?php

spl_autoload_register(function ($class) {
    if (false !== stripos($class, 'MasApi\Wechat')) {
        require_once __DIR__.'/wechat/src/'.str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 6)).'.php';
    }elseif(false !== stripos($class, 'MasApi\OAuth2')) {
    	require_once __DIR__.'/oauth2/'.str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 13)).'.php';
    }
});