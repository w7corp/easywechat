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
use EasyWeChat\Support\ServiceContainer;

/**
 * Class Application.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 *
 * @property \EasyWeChat\Applications\MiniProgram\Sns\Sns $sns
 * @property \EasyWeChat\Applications\MiniProgram\Stats\Stats $stats
 * @property \EasyWeChat\Applications\MiniProgram\Server\Guard $server
 * @property \EasyWeChat\Applications\MiniProgram\TemplateMessage\TemplateMessage $template_message
 * @property \EasyWeChat\Applications\MiniProgram\QRCode\QRCode $qrcode
 * @property \EasyWeChat\Applications\MiniProgram\Material\Temporary $material_temporary
 * @property \EasyWeChat\Applications\MiniProgram\CustomerService\CustomerService $customer_service
 */
class Application extends ServiceContainer
{
    protected $providers = [
        MiniProgram\Auth\ServiceProvider::class,
        MiniProgram\Sns\ServiceProvider::class,
        MiniProgram\Stats\ServiceProvider::class,
        MiniProgram\QRCode\ServiceProvider::class,
        MiniProgram\Server\ServiceProvider::class,
        MiniProgram\Material\ServiceProvider::class,
        MiniProgram\CustomerService\ServiceProvider::class,
        MiniProgram\TemplateMessage\ServiceProvider::class,
    ];
}
