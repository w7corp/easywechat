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

use EasyWeChat\Applications\BaseService;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author overtrue <i@overtrue.me>
 *
 * @property \EasyWeChat\Applications\BaseService\Media\Client               $media
 * @property \EasyWeChat\Applications\BaseService\Url\Client                 $url
 * @property \EasyWeChat\Applications\BaseService\QrCode\Client              $qrcode
 * @property \EasyWeChat\Applications\BaseService\Jssdk\Client               $jssdk
 * @property \EasyWeChat\Applications\OfficialAccount\Auth\AccessToken       $access_token
 * @property \EasyWeChat\Applications\OfficialAccount\Server\Guard           $server
 * @property \EasyWeChat\Applications\OfficialAccount\User\UserClient        $user
 * @property \EasyWeChat\Applications\OfficialAccount\User\TagClient         $user_tag
 * @property \EasyWeChat\Applications\OfficialAccount\User\GroupClient       $user_group
 * @property \Overtrue\Socialite\Providers\WeChatProvider                    $oauth
 * @property \EasyWeChat\Applications\OfficialAccount\Menu\Client            $menu
 * @property \EasyWeChat\Applications\OfficialAccount\TemplateMessage\Client $template_message
 * @property \EasyWeChat\Applications\OfficialAccount\Material\Client        $material
 * @property \EasyWeChat\Applications\OfficialAccount\CustomerService\Client $customer_service
 * @property \EasyWeChat\Applications\OfficialAccount\Semantic\Client        $semantic
 * @property \EasyWeChat\Applications\OfficialAccount\DataCube\Client        $stats
 * @property \EasyWeChat\Applications\OfficialAccount\AutoReply\Client       $auto_reply
 * @property \EasyWeChat\Applications\OfficialAccount\Broadcasting\Client    $broadcasting
 * @property \EasyWeChat\Applications\OfficialAccount\Card\Client            $card
 * @property \EasyWeChat\Applications\OfficialAccount\Device\Client          $device
 * @property \EasyWeChat\Applications\OfficialAccount\ShakeAround\Client     $shake_around
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Auth\ServiceProvider::class,
        Server\ServiceProvider::class,
        User\ServiceProvider::class,
        OAuth\ServiceProvider::class,
        Menu\ServiceProvider::class,
        TemplateMessage\ServiceProvider::class,
        Material\ServiceProvider::class,
        CustomerService\ServiceProvider::class,
        Semantic\ServiceProvider::class,
        DataCube\ServiceProvider::class,
        POI\ServiceProvider::class,
        AutoReply\ServiceProvider::class,
        Broadcasting\ServiceProvider::class,
        Card\ServiceProvider::class,
        Device\ServiceProvider::class,
        ShakeAround\ServiceProvider::class,
        Comment\ServiceProvider::class,
        Invoice\ServiceProvider::class,
        // Base services
        BaseService\QrCode\ServiceProvider::class,
        BaseService\Media\ServiceProvider::class,
        BaseService\Url\ServiceProvider::class,
        BaseService\Jssdk\ServiceProvider::class,
    ];
}
