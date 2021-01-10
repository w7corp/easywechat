<?php

declare(strict_types=1);

namespace EasyWeChat\BasicService;

use EasyWeChat\Kernel\ServiceContainer;

/**
 * @property \EasyWeChat\BasicService\Jssdk\Client $jssdk
 * @property \EasyWeChat\BasicService\Media\Client $media
 * @property \EasyWeChat\BasicService\QrCode\Client $qrcode
 * @property \EasyWeChat\BasicService\Url\Client $url
 * @property \EasyWeChat\BasicService\ContentSecurity\Client $content_security
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected array $providers = [
        Jssdk\ServiceProvider::class,
        QrCode\ServiceProvider::class,
        Media\ServiceProvider::class,
        Url\ServiceProvider::class,
        ContentSecurity\ServiceProvider::class,
    ];
}
