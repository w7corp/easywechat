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

use EasyWeChat\BasicService;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\WeWork\Server\ServiceProvider;

/**
 * Application.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 *
 * @property \EasyWeChat\WeWork\OA\Client                 $oa
 * @property \EasyWeChat\WeWork\Auth\AccessToken          $access_token
 * @property \EasyWeChat\WeWork\Agent\Client              $agent
 * @property \EasyWeChat\WeWork\Department\Client         $department
 * @property \EasyWeChat\WeWork\Media\Client              $media
 * @property \EasyWeChat\WeWork\Menu\Client               $menu
 * @property \EasyWeChat\WeWork\Message\Client            $message
 * @property \EasyWeChat\WeWork\Message\Messenger         $messenger
 * @property \EasyWeChat\WeWork\User\Client               $user
 * @property \EasyWeChat\WeWork\User\TagClient            $tag
 * @property \EasyWeChat\WeWork\Server\ServiceProvider    $server
 * @property \EasyWeChat\BasicService\Jssdk\Client        $jssdk
 * @property \Overtrue\Socialite\Providers\WeWorkProvider $oauth
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
        OAuth\ServiceProvider::class,
        User\ServiceProvider::class,
        Agent\ServiceProvider::class,
        Media\ServiceProvider::class,
        Message\ServiceProvider::class,
        Department\ServiceProvider::class,
        Server\ServiceProvider::class,

        // base services
        BasicService\Jssdk\ServiceProvider::class,
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
