<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\BasicService;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author overtrue <i@overtrue.me>
 *
 * @property \EasyWeChat\BasicService\Media\Client               $media
 * @property \EasyWeChat\BasicService\Url\Client                 $url
 * @property \EasyWeChat\BasicService\QrCode\Client              $qrcode
 * @property \EasyWeChat\BasicService\Jssdk\Client               $jssdk
 * @property \EasyWeChat\OfficialAccount\Auth\AccessToken        $access_token
 * @property \EasyWeChat\OfficialAccount\Server\Guard            $server
 * @property \EasyWeChat\OfficialAccount\User\UserClient         $user
 * @property \EasyWeChat\OfficialAccount\User\TagClient          $user_tag
 * @property \EasyWeChat\OfficialAccount\Menu\Client             $menu
 * @property \EasyWeChat\OfficialAccount\TemplateMessage\Client  $template_message
 * @property \EasyWeChat\OfficialAccount\Material\Client         $material
 * @property \EasyWeChat\OfficialAccount\CustomerService\Client  $customer_service
 * @property \EasyWeChat\OfficialAccount\Semantic\Client         $semantic
 * @property \EasyWeChat\OfficialAccount\DataCube\Client         $data_cube
 * @property \EasyWeChat\OfficialAccount\AutoReply\Client        $auto_reply
 * @property \EasyWeChat\OfficialAccount\Broadcasting\Client     $broadcasting
 * @property \EasyWeChat\OfficialAccount\Card\Card               $card
 * @property \EasyWeChat\OfficialAccount\Device\Client           $device
 * @property \EasyWeChat\OfficialAccount\ShakeAround\ShakeAround $shake_around
 * @property \EasyWeChat\OfficialAccount\POI\Client              $poi
 * @property \EasyWeChat\OfficialAccount\Store\Client            $store
 * @property \EasyWeChat\OfficialAccount\Base\Client             $base
 * @property \Overtrue\Socialite\Providers\WeChatProvider        $oauth
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
        Store\ServiceProvider::class,
        Comment\ServiceProvider::class,
        Base\ServiceProvider::class,
        // Base services
        BasicService\QrCode\ServiceProvider::class,
        BasicService\Media\ServiceProvider::class,
        BasicService\Url\ServiceProvider::class,
        BasicService\Jssdk\ServiceProvider::class,
    ];
}
