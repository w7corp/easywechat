<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\Auth;

use EasyWeChat\Kernel\AccessToken as BaseAccessToken;
use EasyWeChat\OpenPlatform\Application;
use Pimple\Container;

class AccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected string $requestMethod = 'POST';

    /**
     * @var string
     */
    protected string $queryName = 'access_token';

    /**
     * @var string
     */
    protected string $tokenKey = 'authorizer_access_token';

    /**
     * AccessToken constructor.
     *
     * @param \Pimple\Container                    $app
     * @param \EasyWeChat\OpenPlatform\Application $component
     */
    public function __construct(
        Container $app,
        public Application $component
    ) {
        parent::__construct($app);
    }

    /**
     * @return array
     */
    protected function getCredentials(): array
    {
        return [
            'component_appid' => $this->component['config']['app_id'],
            'authorizer_appid' => $this->app['config']['app_id'],
            'authorizer_refresh_token' => $this->app['config']['refresh_token'],
        ];
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        $query = http_build_query([
            'component_access_token' => $this->component['access_token']?->getToken()['component_access_token'],
        ]);

        return 'cgi-bin/component/api_authorizer_token?'.$query;
    }
}
