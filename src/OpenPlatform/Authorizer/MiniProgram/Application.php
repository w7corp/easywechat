<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram;

use EasyWeChat\MiniProgram\Application as MiniProgram;
use EasyWeChat\OpenPlatform\Authorizer\Aggregate\AggregateServiceProvider;

/**
 *
 * @property \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Account\Client  $account
 * @property \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Code\Client     $code
 * @property \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Domain\Client   $domain
 * @property \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Setting\Client  $setting
 * @property \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Tester\Client   $tester
 * @property \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Material\Client $material
 */
class Application extends MiniProgram
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
            Code\ServiceProvider::class,
            Domain\ServiceProvider::class,
            Account\ServiceProvider::class,
            Setting\ServiceProvider::class,
            Tester\ServiceProvider::class,
            Material\ServiceProvider::class,
        ];

        foreach ($providers as $provider) {
            $this->register(new $provider());
        }
    }
}
