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
 * @property \EasyWeChat\Applications\Application\Core\AccessToken                   $access_token
 * @property \EasyWeChat\Applications\Application\Server\Guard                       $server
 * @property \EasyWeChat\Applications\Application\User\User                          $user
 * @property \EasyWeChat\Applications\Application\User\Tag                           $user_tag
 * @property \EasyWeChat\Applications\Application\User\Group                         $user_group
 * @property \EasyWeChat\Applications\Application\Js\Js                              $js
 * @property \Overtrue\Socialite\Providers\WeChatProvider                                $oauth
 * @property \EasyWeChat\Applications\Application\Menu\Menu                          $menu
 * @property \EasyWeChat\Applications\Application\TemplateMessage\TemplateMessage    $template_message
 * @property \EasyWeChat\Applications\Application\Material\Material                  $material
 * @property \EasyWeChat\Applications\Application\Material\Temporary                 $material_temporary
 * @property \EasyWeChat\Applications\Application\CustomerService\CustomerService    $customer_service
 * @property \EasyWeChat\Applications\Application\Url\Url                            $url
 * @property \EasyWeChat\Applications\Application\QRCode\QRCode                      $qrcode
 * @property \EasyWeChat\Applications\Application\Semantic\Semantic                  $semantic
 * @property \EasyWeChat\Applications\Application\Stats\Stats                        $stats
 * @property \EasyWeChat\Applications\Application\Reply\Reply                        $reply
 * @property \EasyWeChat\Applications\Application\Broadcast\Broadcast                $broadcast
 * @property \EasyWeChat\Applications\Application\Card\Card                          $card
 * @property \EasyWeChat\Applications\Application\Device\Device                      $device
 * @property \EasyWeChat\Applications\Application\ShakeAround\ShakeAround            $shakearound
 */
class Application extends ServiceContainer
{
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
        OfficialAccount\Broadcast\ServiceProvider::class,
        OfficialAccount\Card\ServiceProvider::class,
        OfficialAccount\Device\ServiceProvider::class,
        OfficialAccount\ShakeAround\ServiceProvider::class,
        OfficialAccount\Comment\ServiceProvider::class,
        OfficialAccount\Invoice\ServiceProvider::class,
    ];

    protected $defaultConfig = [
        'http' => [
            'timeout' => 5.0,
            'base_uri' => 'https://api.weixin.qq.com/',
        ],
    ];
}
