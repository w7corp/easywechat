<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork;

use EasyWeChat\Applications\BaseService\Application as BaseService;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Application.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 *
 * @property \EasyWeChat\Applications\WeWork\OA\Client         $oa
 * @property \EasyWeChat\Applications\WeWork\Auth\AccessToken  $access_token
 * @property \EasyWeChat\Applications\WeWork\Agent\Client      $agent
 * @property \EasyWeChat\Applications\WeWork\Department\Client $department
 * @property \EasyWeChat\Applications\WeWork\Media\Client      $media
 * @property \EasyWeChat\Applications\WeWork\Menu\Client       $menu
 * @property \EasyWeChat\Applications\WeWork\Message\Client    $message
 * @property \EasyWeChat\Applications\WeWork\Message\Messenger $messenger
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        OA\ServiceProvider::class,
        Auth\ServiceProvider::class,
        Menu\ServiceProvider::class,
        User\ServiceProvider::class,
        Agent\ServiceProvider::class,
        Media\ServiceProvider::class,
        Message\ServiceProvider::class,
        Department\ServiceProvider::class,
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

    protected function afterRegistered()
    {
        $this['jssdk'] = function () {
            return (new BaseService($this['config']->toArray()))->jssdk;
        };
    }
}
