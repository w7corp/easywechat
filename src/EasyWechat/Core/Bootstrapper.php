<?php
/**
 * Bootstrapper.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Core;

use ArrayAccess;
use Closure;
use EasyWeChat\Encryption\Cryptor;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\ServiceProvider;
use EasyWeChat\Core\Exceptions\InvalidConfigException;
use EasyWeChat\Core\Exceptions\UnBoundServiceException;
use IteratorAggregate;

/**
 * Class Bootstrapper
 *
 * @package EasyWeChat\Core
 */
class Bootstrapper implements ArrayAccess, IteratorAggregate
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
     * @var \EasyWeChat\Support\Collection
     */
    protected $config;

    /**
     * Resolved service.
     *
     * @var array
     */
    protected $resolved = array();

    /**
     * Service bindings.
     *
     * @var array
     */
    protected $bindings = array();

    /**
     * Service providers
     *
     * @var array
     */
    protected $providers = array(
        'EasyWeChat\Cache\CacheServiceProvider',
        'EasyWeChat\Server\ServerServiceProvider',
    );

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
     * @param bool   $force
     */
    public function bind($abstract, $concrete, $force = false)
    {
        if ($force && $this->isBound($abstract)) {
            $this->unBind($abstract);
        }

        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Rebind service.
     *
     * @param string $abstract
     * @param mixed  $concrete
     */
    public function reBind($abstract, $concrete)
    {
        $this->unBind($abstract);
        $this->bind($abstract, $concrete);
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
     * Return whether the service is binded.
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
     * Return whether the service is resolved.
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
     * Set providers.
     *
     * @param array $providers
     * @param bool  $cleanBefore
     */
    public function setProviders(array $providers, $cleanBefore = false)
    {
        if ($cleanBefore) {
            $this->providers = array();
        }

        foreach ($providers as $provider) {
            $this->setProvider($provider);
        }
    }

    /**
     * Add a service provider.
     *
     * @param string $provider
     */
    public function setProvider($provider)
    {
        if ($provider instanceof ServiceProvider) {
            $this->providers[] = $provider;
        }
    }

    /**
     * Initial configuration.
     *
     * @param array $config
     *
     * @throws \EasyWeChat\Core\Exception
     */
    protected function registerConfiguration(array $config)
    {
        $required = array('app_id', 'secret', 'token');

        if ($diff = array_diff($required, array_keys($config))) {
            $error = join(',', $diff) . (count($diff) > 1 ? 'are' : 'is') .' required';

            throw new InvalidConfigException('Configuration Missing, ' . $error, 500);
        }

        $this->config = new Collection($config);

        $this->bind('config', $this->config);
    }

    /**
     * Register all Providers
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
        $this->bind('http', function($sdk){
            return new Http();
        });

        $this->bind('access_token', function($sdk){
            return new AccessToken(
                $sdk->config->get('app_id'),
                $sdk->config->get('secret'),
                $sdk['cache'],
                $sdk['http']
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
    protected function get($abstract)
    {
        if (!empty($this->resolved[$abstract])) {
            return $this->resolved[$abstract];
        }

        $service = $this->build($abstract);

        return $this->resolved[$abstract] = $service;
    }

    /**
     * Build service.
     *
     * @param string $abstract
     *
     * @return mixed
     *
     * @throws UnBoundServiceException
     */
    protected function build($abstract)
    {
        if (!$this->isBound($abstract)) {
            throw new UnBoundServiceException("Unknow service '$abstract'", 500);
        }

        $concrete = $this->bindings[$abstract];

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
        $this->bind('cryptor', function($sdk){
            $config = $sdk->config;

            return new Cryptor($config['app_id'], $config['token'], $config['aes_key']);
        });
    }

    /**
     * Register input service.
     */
    protected function registerInput()
    {
        $this->bind('input', function($sdk){
            return new Input($sdk->config->get('token'), $sdk->get('cryptor'));
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
     * @return Iterator
     */
    public function getIterator()
    {
        return new Iterator($this->bindings);
    }


}//end class
