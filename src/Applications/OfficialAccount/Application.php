<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OfficialAccount;

use EasyWeChat\Applications\OfficialAccount;
use EasyWeChat\Kernel\ServiceContainer;

/*
 * Class Application.
 *
 * @property \EasyWeChat\Applications\OfficialAccount\Auth\AccessToken                   $access_token
 * @property \EasyWeChat\Applications\OfficialAccount\Server\Guard                       $server
 * @property \EasyWeChat\Applications\OfficialAccount\User\UserClient                    $user
 * @property \EasyWeChat\Applications\OfficialAccount\User\TagClient                     $user_tag
 * @property \EasyWeChat\Applications\OfficialAccount\User\GroupClient                   $user_group
 * @property \Overtrue\Socialite\Providers\WeChatProvider                                $oauth
 * @property \EasyWeChat\Applications\OfficialAccount\Menu\Client                        $menu
 * @property \EasyWeChat\Applications\OfficialAccount\TemplateMessage\Client             $template_message
 * @property \EasyWeChat\Applications\OfficialAccount\MaterialClient\MaterialClient      $material
 * @property \EasyWeChat\Applications\OfficialAccount\MaterialClient\TemporaryClient     $material_temporary
 * @property \EasyWeChat\Applications\OfficialAccount\CustomerService\Client             $customer_service
 * @property \EasyWeChat\Applications\OfficialAccount\Url\Client                         $url
 * @property \EasyWeChat\Applications\OfficialAccount\QRCode\Client                      $qrcode
 * @property \EasyWeChat\Applications\OfficialAccount\Semantic\Client                    $semantic
 * @property \EasyWeChat\Applications\OfficialAccount\DataCube\Client                    $stats
 * @property \EasyWeChat\Applications\OfficialAccount\Reply\Client                       $reply
 * @property \EasyWeChat\Applications\OfficialAccount\Broadcasting\Client                $broadcast
 * @property \EasyWeChat\Applications\OfficialAccount\Card\Client                        $card
 * @property \EasyWeChat\Applications\OfficialAccount\Device\Client                      $device
 * @property \EasyWeChat\Applications\OfficialAccount\ShakeAround\Client                 $shsake_around
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        OfficialAccount\Auth\ServiceProvider::class,
        OfficialAccount\Server\ServiceProvider::class,
        OfficialAccount\User\ServiceProvider::class,
        OfficialAccount\Jssdk\ServiceProvider::class,
        OfficialAccount\OAuth\ServiceProvider::class,
        OfficialAccount\Menu\ServiceProvider::class,
        OfficialAccount\TemplateMessage\ServiceProvider::class,
        OfficialAccount\Material\ServiceProvider::class,
        OfficialAccount\CustomerService\ServiceProvider::class,
        OfficialAccount\Url\ServiceProvider::class,
        OfficialAccount\QRCode\ServiceProvider::class,
        OfficialAccount\Semantic\ServiceProvider::class,
        OfficialAccount\DataCube\ServiceProvider::class,
        OfficialAccount\POI\ServiceProvider::class,
        OfficialAccount\AutoReply\ServiceProvider::class,
        OfficialAccount\Broadcasting\ServiceProvider::class,
        OfficialAccount\Card\ServiceProvider::class,
        OfficialAccount\Device\ServiceProvider::class,
        OfficialAccount\ShakeAround\ServiceProvider::class,
        OfficialAccount\Comment\ServiceProvider::class,
        OfficialAccount\Invoice\ServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $defaultConfig = [
        'http' => [
            'timeout' => 5.0,
            'base_uri' => 'https://api.weixin.qq.com/',
        ],
    ];
}
