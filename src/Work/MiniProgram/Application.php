<?php

declare(strict_types=1);

namespace EasyWeChat\Work\MiniProgram;

use EasyWeChat\MiniProgram\Application as MiniProgram;
use EasyWeChat\Work\Auth\AccessToken;
use EasyWeChat\Work\MiniProgram\Auth\Client;

/**
 *
 * @property \EasyWeChat\Work\MiniProgram\Auth\Client $auth
 */
class Application extends MiniProgram
{
    /**
     * @param array $config
     * @param array $prepends
     */
    public function __construct(array $config = [], array $prepends = [])
    {
        parent::__construct($config, $prepends + [
            'access_token' => function ($app) {
                return new AccessToken($app);
            },
            'auth' => function ($app) {
                return new Client($app);
            },
        ]);
    }
}
