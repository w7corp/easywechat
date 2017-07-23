<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\WeWork;

use EasyWeChat\BaseService;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Application.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 *
 * @property \EasyWeChat\WeWork\OA\Client         $oa
 * @property \EasyWeChat\WeWork\Auth\AccessToken  $access_token
 * @property \EasyWeChat\WeWork\Agent\Client      $agent
 * @property \EasyWeChat\WeWork\Department\Client $department
 * @property \EasyWeChat\WeWork\Media\Client      $media
 * @property \EasyWeChat\WeWork\Menu\Client       $menu
 * @property \EasyWeChat\WeWork\Message\Client    $message
 * @property \EasyWeChat\WeWork\Message\Messenger $messenger
 * @property \EasyWeChat\BaseService\Jssdk\Client $jssdk
 *
 * @method mixed getCallbackIp()
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        OA\ServiceProvider::class,
        Auth\ServiceProvider::class,
        Base\ServiceProvider::class,
        Menu\ServiceProvider::class,
        User\ServiceProvider::class,
        Agent\ServiceProvider::class,
        Media\ServiceProvider::class,
        Message\ServiceProvider::class,
        Department\ServiceProvider::class,

        // base services
        BaseService\Jssdk\ServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $defaultConfig = [
        // http://docs.guzzlephp.org/en/stable/request-options.html
        'http' => [
            'base_uri' => 'https://qyapi.weixin.qq.com/cgi-bin/',
        ],
    ];

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this['base']->$method(...$arguments);
    }
}
