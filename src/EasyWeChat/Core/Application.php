<?php

/**
 * Application.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Core;

use EasyWeChat\Container\Container;
use EasyWeChat\Encryption\Cryptor;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\ServiceProvider;
use EasyWeChat\Core\Exceptions\InvalidConfigException;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;

/**
 * Class Application.
 */
class Application extends Container
{
    /**
     * Configuration.
     *
     * <pre>
     * {
     *    app_id:  xxxx,
     *    secret:  xxxx,
     *    token:   xxxx,
     *    aes_key: xxxx,
     * }
     * </pre>
     *
     * @var Collection
     */
    protected $config;

    /**
     * Service providers.
     *
     * @var array
     */
    protected $providers = [
        'EasyWeChat\Cache\CacheServiceProvider',
        'EasyWeChat\Server\ServerServiceProvider',
    ];

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->registerConfiguration($config);
        $this->registerCryptor();
        $this->registerInput();
        $this->registerClientBaseService();
        $this->registerProviders();
    }

    /**
     * Set providers.
     *
     * @param array $providers
     * @param bool  $cleanBefore
     */
    public function setProviders(array $providers, $cleanBefore = false)
    {
        if ($cleanBefore) {
            $this->providers = [];
        }

        foreach ($providers as $provider) {
            $this->setProvider($provider);
        }
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Add a service provider.
     *
     * @param string $provider
     *
     * @throws InvalidArgumentException
     */
    public function setProvider($provider)
    {
        if (!$provider instanceof ServiceProvider) {
            throw new InvalidArgumentException("ServiceProvider must be a subclass of 'EasyWeChat\Support\ServiceProvider'.");
        }

        $this->providers[] = $provider;
    }

    /**
     * Initial configuration.
     *
     * @param array $config
     *
     * @throws InvalidConfigException
     */
    protected function registerConfiguration(array $config)
    {
        $required = ['app_id', 'secret', 'token'];

        if ($diff = array_diff($required, array_keys($config))) {
            $error = implode(',', $diff).(count($diff) > 1 ? 'are' : 'is').' required';

            throw new InvalidConfigException('Configuration Missing, '.$error, 500);
        }

        $this->config = new Collection($config);

        $this->bind('config', $this->config);
    }

    /**
     * Register all Providers.
     */
    protected function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->resolveProvider($provider)->register($this);
        }
    }

    /**
     * Register client base service.
     */
    protected function registerClientBaseService()
    {
        $this->bind('http', function ($app) {
            return new Http(new Client());
        });

        $this->bind('access_token', function ($app) {
            return new AccessToken(
                $app->config->get('app_id'),
                $app->config->get('secret'),
                $app['cache'],
                $app['http']
            );
        });
    }

    /**
     * Get provider instance.
     *
     * @param string $provider The name of provider.
     *
     * @return mixed
     */
    protected function resolveProvider($provider)
    {
        return new $provider($this);
    }

    /**
     * Register encryption service.
     */
    protected function registerCryptor()
    {
        $this->bind('cryptor', function ($app) {
            $config = $app->config;

            return new Cryptor($config['app_id'], $config['token'], $config['aes_key']);
        });
    }

    /**
     * Register input service.
     */
    protected function registerInput()
    {
        $this->bind('input', function ($app) {
            return new Input($app->config->get('token'), $app->get('cryptor'));
        });
    }
}
