<?php

namespace Overtrue\Wechat\Services;

class Auth extends Service
{
    const API_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    public function authorized()
    {
        # code...
    }

    public function url($redirect, $state, $scope)
    {
        # code...
    }

    public function redirect($redirect, $state, $scope)
    {
        # code...
    }

    public function user()
    {
        # code...
    }
}