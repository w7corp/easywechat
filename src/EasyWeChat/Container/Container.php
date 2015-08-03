<?php

/**
 * Container.php.
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

namespace EasyWeChat\Container;

use ArrayAccess;
use ArrayIterator;
use Closure;
use EasyWeChat\Core\Exceptions\UnboundServiceException;
use IteratorAggregate;

/**
 * Class Container.
 */
class Container implements ArrayAccess, IteratorAggregate
{
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
        $this->bind($abstract, $concrete, false);
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
            return;
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
