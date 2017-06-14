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
use EasyWeChat\Applications\OfficialAccount\OpenPlatform\Authorizer\AccessToken as AuthorizerAccessToken;
use EasyWeChat\Config\Repository as Config;
use EasyWeChat\Support\ServiceContainer;

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
 * @property \EasyWeChat\Applications\Application\Payment\Merchant                   $merchant
 * @property \EasyWeChat\Applications\Application\Payment\Payment                    $payment
 * @property \EasyWeChat\Applications\Application\Payment\LuckyMoney\LuckyMoney      $lucky_money
 * @property \EasyWeChat\Applications\Application\Payment\MerchantPay\MerchantPay    $merchant_pay
 * @property \EasyWeChat\Applications\Application\Payment\CashCoupon\CashCoupon      $cash_coupon
 * @property \EasyWeChat\Applications\Application\Reply\Reply                        $reply
 * @property \EasyWeChat\Applications\Application\Broadcast\Broadcast                $broadcast
 * @property \EasyWeChat\Applications\Application\Card\Card                          $card
 * @property \EasyWeChat\Applications\Application\Device\Device                      $device
 * @property \EasyWeChat\Applications\Application\ShakeAround\ShakeAround            $shakearound
 */
class Application extends ServiceContainer
{
    protected $providers = [
        OfficialAccount\Core\ServiceProvider::class,
        OfficialAccount\Server\ServiceProvider::class,
        OfficialAccount\User\ServiceProvider::class,
        OfficialAccount\Js\ServiceProvider::class,
        OfficialAccount\OAuth\ServiceProvider::class,
        OfficialAccount\Menu\ServiceProvider::class,
        OfficialAccount\TemplateMessage\ServiceProvider::class,
        OfficialAccount\Material\ServiceProvider::class,
        OfficialAccount\CustomerService\ServiceProvider::class,
        OfficialAccount\Url\ServiceProvider::class,
        OfficialAccount\QRCode\ServiceProvider::class,
        OfficialAccount\Semantic\ServiceProvider::class,
        OfficialAccount\Stats\ServiceProvider::class,
        OfficialAccount\Payment\ServiceProvider::class,
        OfficialAccount\POI\ServiceProvider::class,
        OfficialAccount\Reply\ServiceProvider::class,
        OfficialAccount\Broadcast\ServiceProvider::class,
        OfficialAccount\Card\ServiceProvider::class,
        OfficialAccount\Device\ServiceProvider::class,
        OfficialAccount\ShakeAround\ServiceProvider::class,
        OfficialAccount\Comment\ServiceProvider::class,
        OfficialAccount\Invoice\ServiceProvider::class,
    ];

    /**
     * Create an instance.
     *
     * @param \EasyWeChat\Config\Repository $config
     * @param string                        $clientId
     * @param string                        $refreshToken
     *
     * @return \EasyWeChat\Applications\OfficialAccount\Application
     */
    public static function createFromOpenPlatform(Config $config, string $clientId, string $refreshToken): Application
    {
        $componentClientId = $config['app_id'];

        $config['app_id'] = $clientId;
        $config['secret'] = null;

        $instance = new self($config);

        /*
         * Override services.
         */
        $instance['oauth'] = function () {
            // todo
        };
        $instance['access_token'] = function () use ($clientId, $componentClientId, $refreshToken) {
            $accessToken = new AuthorizerAccessToken($clientId);
            $accessToken->setComponentClientId($componentClientId)->setRefreshToken($refreshToken);

            return $accessToken;
        };

        return $instance;
    }
}
