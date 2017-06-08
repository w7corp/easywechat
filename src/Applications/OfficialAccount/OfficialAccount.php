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

use BadMethodCallException;
use EasyWeChat\Support\Traits\PrefixedContainer;

/*
 * @property \EasyWeChat\Applications\OfficialAccount\Core\AccessToken                   $access_token
 * @property \EasyWeChat\Applications\OfficialAccount\Server\Guard                       $server
 * @property \EasyWeChat\Applications\OfficialAccount\User\User                          $user
 * @property \EasyWeChat\Applications\OfficialAccount\User\Tag                           $user_tag
 * @property \EasyWeChat\Applications\OfficialAccount\User\Group                         $user_group
 * @property \EasyWeChat\Applications\OfficialAccount\Js\Js                              $js
 * @property \Overtrue\Socialite\Providers\WeChatProvider                                $oauth
 * @property \EasyWeChat\Applications\OfficialAccount\Menu\Menu                          $menu
 * @property \EasyWeChat\Applications\OfficialAccount\TemplateMessage\TemplateMessage    $template_message
 * @property \EasyWeChat\Applications\OfficialAccount\Material\Material                  $material
 * @property \EasyWeChat\Applications\OfficialAccount\Material\Temporary                 $material_temporary
 * @property \EasyWeChat\Applications\OfficialAccount\CustomerService\CustomerService    $customer_service
 * @property \EasyWeChat\Applications\OfficialAccount\Url\Url                            $url
 * @property \EasyWeChat\Applications\OfficialAccount\QRCode\QRCode                      $qrcode
 * @property \EasyWeChat\Applications\OfficialAccount\Semantic\Semantic                  $semantic
 * @property \EasyWeChat\Applications\OfficialAccount\Stats\Stats                        $stats
 * @property \EasyWeChat\Applications\OfficialAccount\Payment\Merchant                   $merchant
 * @property \EasyWeChat\Applications\OfficialAccount\Payment\Payment                    $payment
 * @property \EasyWeChat\Applications\OfficialAccount\Payment\LuckyMoney\LuckyMoney      $lucky_money
 * @property \EasyWeChat\Applications\OfficialAccount\Payment\MerchantPay\MerchantPay    $merchant_pay
 * @property \EasyWeChat\Applications\OfficialAccount\Payment\CashCoupon\CashCoupon      $cash_coupon
 * @property \EasyWeChat\Applications\OfficialAccount\Reply\Reply                        $reply
 * @property \EasyWeChat\Applications\OfficialAccount\Broadcast\Broadcast                $broadcast
 * @property \EasyWeChat\Applications\OfficialAccount\Card\Card                          $card
 * @property \EasyWeChat\Applications\OfficialAccount\Device\Device                      $device
 * @property \EasyWeChat\Applications\OfficialAccount\ShakeAround\ShakeAround            $shakearound
 */
class OfficialAccount
{
    use PrefixedContainer;

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (is_callable([$calling = $this->fetch('fundamental_api'), $method])) {
            return call_user_func_array([$calling, $method], $args);
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }
}
