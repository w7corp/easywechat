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

use EasyWeChat\Support\ServiceContainer;

/**
 * Class MiniProgram.
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
class MiniProgram extends ServiceContainer
{
    protected $providers = [
        \EasyWeChat\Applications\MiniProgram\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\Sns\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\Stats\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\QRCode\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\Server\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\Material\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\CustomerService\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\TemplateMessage\ServiceProvider::class,
    ];
}
