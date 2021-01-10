<?php

declare(strict_types=1);

namespace EasyWeChat\Work\ExternalContact;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['external_contact'] = function ($app) {
            return new Client($app);
        };

        $app['contact_way'] = function ($app) {
            return new ContactWayClient($app);
        };

        $app['external_contact_statistics'] = function ($app) {
            return new StatisticsClient($app);
        };

        $app['external_contact_message'] = function ($app) {
            return new MessageClient($app);
        };
    }
}
