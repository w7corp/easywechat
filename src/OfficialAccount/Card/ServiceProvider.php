<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Card;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author overtrue <i@overtrue.me>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['card'] = function ($app) {
            return new Card($app);
        };

        $app['card.client'] = function ($app) {
            return new Client($app);
        };

        $app['card.coin'] = function ($app) {
            return new CoinClient($app);
        };

        $app['card.sub_merchant'] = function ($app) {
            return new SubMerchantClient($app);
        };

        $app['card.code'] = function ($app) {
            return new CodeClient($app);
        };

        $app['card.movie_ticket'] = function ($app) {
            return new MovieTicketClient($app);
        };

        $app['card.member_card'] = function ($app) {
            return new MemberCardClient($app);
        };

        $app['card.general_card'] = function ($app) {
            return new GeneralCardClient($app);
        };

        $app['card.boarding_pass'] = function ($app) {
            return new BoardingPassClient($app);
        };

        $app['card.meeting_ticket'] = function ($app) {
            return new MeetingTicketClient($app);
        };

        $app['card.jssdk'] = function ($app) {
            return new JssdkClient($app);
        };

        $app['card.gift_card'] = function ($app) {
            return new GiftCardClient($app);
        };

        $app['card.gift_card_order'] = function ($app) {
            return new GiftCardOrderClient($app);
        };

        $app['card.gift_card_page'] = function ($app) {
            return new GiftCardPageClient($app);
        };

        $app['card.invoice'] = function ($app) {
            return new InvoiceClient($app);
        };
    }
}
