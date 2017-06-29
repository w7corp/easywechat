<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\MiniProgram;

use EasyWeChat\Applications\MiniProgram;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 *
 * @property \EasyWeChat\Applications\MiniProgram\Sns\Client $sns
 * @property \EasyWeChat\Applications\MiniProgram\DataCube\Client $data_cube
 * @property \EasyWeChat\Applications\MiniProgram\Server\Guard $server
 * @property \EasyWeChat\Applications\MiniProgram\TemplateMessage\Client $template_message
 * @property \EasyWeChat\Applications\MiniProgram\QRCode\Client $qrcode
 * @property \EasyWeChat\Applications\MiniProgram\Material\TemporaryClient $material_temporary
 * @property \EasyWeChat\Applications\MiniProgram\CustomerService\Client $customer_service
 */
class Application extends ServiceContainer
{
    /**
     * {@inheritdoc}
     */
    protected $providers = [
        MiniProgram\Auth\ServiceProvider::class,
        MiniProgram\CustomerService\ServiceProvider::class,
        MiniProgram\DataCube\ServiceProvider::class,
        MiniProgram\Material\ServiceProvider::class,
        MiniProgram\QRCode\ServiceProvider::class,
        MiniProgram\Sns\ServiceProvider::class,
        MiniProgram\Server\ServiceProvider::class,
        MiniProgram\TemplateMessage\ServiceProvider::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected $defaultConfig = [
        'http' => [
            'timeout' => 5.0,
            'base_uri' => 'https://api.weixin.qq.com/',
        ],
    ];
}
