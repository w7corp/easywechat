<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\BaseService;

use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author overtrue <i@overtrue.me>
 *
 * @property \EasyWeChat\BaseService\Jssdk\Client  $jssdk
 * @property \EasyWeChat\BaseService\Media\Client  $media
 * @property \EasyWeChat\BaseService\QrCode\Client $qrcode
 * @property \EasyWeChat\BaseService\Url\Client    $url
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
        Url\ServiceProvider::class,
    ];
}
