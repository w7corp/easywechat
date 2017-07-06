<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\BaseService;

use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author overtrue <i@overtrue.me>
 *
 * @property \EasyWeChat\Applications\BaseService\Jssdk\Client  $jssdk
 * @property \EasyWeChat\Applications\BaseService\Media\Client  $media
 * @property \EasyWeChat\Applications\BaseService\QrCode\Client $qrcode
 * @property \EasyWeChat\Applications\BaseService\Url\Client    $url
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Jssdk\ServiceProvider::class,
        QrCode\ServiceProvider::class,
        Media\ServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $defaultConfig = [
        // http://docs.guzzlephp.org/en/stable/request-options.html
        'http' => [
            'timeout' => 5.0,
            'base_uri' => 'https://api.weixin.qq.com/',
        ],
    ];
}
