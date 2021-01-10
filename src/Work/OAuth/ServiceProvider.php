<?php

declare(strict_types=1);

namespace EasyWeChat\Work\OAuth;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['oauth'] = function ($app) {
            $socialite = (new Manager([
                'wework' => [
                    'client_id' => $app['config']['corp_id'],
                    'client_secret' => null,
                    'corp_id' => $app['config']['corp_id'],
                    'corp_secret' => $app['config']['secret'],
                    'redirect' => $this->prepareCallbackUrl($app),
                ],
            ], $app));

            $scopes = (array) $app['config']->get('oauth.scopes', ['snsapi_base']);

            if (!empty($scopes)) {
                $socialite->scopes($scopes);
            } else {
                $socialite->setAgentId($app['config']['agent_id']);
            }

            return $socialite;
        };
    }

    /**
     * Prepare the OAuth callback url for wechat.
     *
     * @param Container $app
     *
     * @return string
     */
    private function prepareCallbackUrl($app)
    {
        $callback = $app['config']->get('oauth.callback');

        if (0 === stripos($callback, 'http')) {
            return $callback;
        }

        $baseUrl = $app['request']->getSchemeAndHttpHost();

        return $baseUrl.'/'.ltrim($callback, '/');
    }
}
