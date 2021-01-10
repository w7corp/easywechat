<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\OfficialAccount;

use EasyWeChat\OfficialAccount\Application as OfficialAccount;
use EasyWeChat\OpenPlatform\Authorizer\Aggregate\AggregateServiceProvider;

/**
 *
 * @property \EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Account\Client     $account
 * @property \EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\MiniProgram\Client $mini_program
 */
class Application extends OfficialAccount
{
    /**
     * @param array $config
     * @param array $prepends
     */
    public function __construct(array $config = [], array $prepends = [])
    {
        parent::__construct($config, $prepends);

        $providers = [
            AggregateServiceProvider::class,
            MiniProgram\ServiceProvider::class,
        ];

        foreach ($providers as $provider) {
            $this->register(new $provider());
        }
    }
}
