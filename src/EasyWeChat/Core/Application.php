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

use ArrayAccess;
use ArrayIterator;
use Closure;
use EasyWeChat\Encryption\Cryptor;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\ServiceProvider;
use EasyWeChat\Core\Exceptions\InvalidConfigException;
use EasyWeChat\Core\Exceptions\UnboundServiceException;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use IteratorAggregate;

/**
 * Class Bootstrapper.
 */
class Application implements ArrayAccess, IteratorAggregate
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
     * Resolved service.
     *
     * @var array
     */
    protected $resolved = [];

    /**
     * Service bindings.
     *
     * @var array
     */
    protected $bindings = [];

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
     * Bind a service.
     *
     * @param string $abstract
     * @param mixed  $concrete
     * @param bool   $share
     * @param bool   $force
     */
    public function bind($abstract, $concrete, $share = false, $force = false)
    {
        if (!$this->isBound($abstract) || $force) {
            $this->bindings[$abstract] = [
                                            'concrete' => $concrete,
                                            'share' => $share,
                                         ];
        }
    }

    /**
     * Bind a singleton service.
     *
     * @param string  $abstract
     * @param Closure $concrete
     */
    public function singleton($abstract, $concrete)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Unbind a service.
     *
     * @param string $abstract
     */
    public function unBind($abstract)
    {
        if (!$this->isBound($abstract)) {
            return;
        }

        if ($this->isResolved($abstract)) {
            unset($this->resolved[$abstract]);
        }

        unset($this->bindings[$abstract]);
    }

    /**
     * Return Whether the service is binded.
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function isBound($abstract)
    {
        return isset($this->bindings[$abstract]);
    }

    /**
     * Return Whether the service is resolved.
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function isResolved($abstract)
    {
        return isset($this->resolved[$abstract]);
    }

    /**
     * Whether the abstract is shared.
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function isShared($abstract)
    {
        return $this->isBound($abstract) && $this->bindings[$abstract]['share'];
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
     * Return all resolved instances.
     *
     * @return array
     */
    public function getResolved()
    {
        return $this->resolved;
    }

    /**
     * Return all bindings.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Return all registed providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
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
            return new Http();
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
     * Return service instance.
     *
     * @param string $abstract
     *
     * @return mixed
     */
    public function get($abstract)
    {
        if ($this->isResolved($abstract)) {
            return $this->resolved[$abstract];
        }

        $service = $this->build($abstract);

        return $this->isShared($abstract) ? $this->resolved[$abstract] = $service : $service;
    }

    /**
     * Build service.
     *
     * @param string $abstract
     *
     * @return mixed
     *
     * @throws UnboundServiceException
     */
    protected function build($abstract)
    {
        if (!$this->isBound($abstract)) {
            throw new UnboundServiceException("Unknow service '$abstract'", 500);
        }

        $concrete = $this->bindings[$abstract]['concrete'];

        if ($concrete instanceof Closure) {
            $concrete = $concrete($this);
        }

        return $concrete;
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

    /**
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->get($property);
    }

    public function __set($abstract, $concrete)
    {
        $this->reBind($abstract, $concrete);
    }

    public function offsetExists($offset)
    {
        return $this->isBound($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        if ($this->isBound($offset)) {
            $this->reBind($offset, $value);
        } else {
            return $this->bind($offset, $value);
        }
    }

    public function offsetUnset($offset)
    {
        return $this->unbind($offset);
    }

    /**
     * Return Iterator.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->bindings);
    }
}
