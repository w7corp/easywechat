<?php namespace Overtrue\Wechat\Trait;

use Closure;
use Countable;
use ArrayAccess;
use Serializable;
use ArrayIterator;
use JsonSerializable;
use IteratorAggregate;
use Overtrue\Wechat\Util\Arr;

trait Attributes implements
    ArrayAccess,
    Countable,
    IteratorAggregate,
    Serializable,
    JsonSerializable
{
    protected $attributes = [];

    /**
     * Return all items
     *
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Merge data
     *
     * @param array $data
     *
     * @return array
     */
    public function merge($data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        return $this->all();
    }

    /**
     * Set the item value.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        Arr::set($this->data, $key, $value);
    }

    /**
     * Retrieve item from Bag.
     *
     * @param string   $key
     * @param mixed    $default
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function get($key, $default = null, Closure $callback = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * Remove item form Bag
     *
     * @param string $key
     *
     * @return void
     */
    public function forget($key)
    {
        return Arr::forget($this->data, $key);
    }

    /** {@inhertDoc} */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /** {@inhertDoc} */
    public function serialize()
    {
        return serialize($this->data);
    }

    /** {@inhertDoc} */
    public function unserialize($data)
    {
        return $this->data = unserialize($data);
    }

    /** {@inhertDoc} **/
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /** {@inhertDoc} */
    public function count($mode = COUNT_NORMAL)
    {
        return count($this->data, $mode);
    }

    /**
     * Get a data by key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Assigns a value to the specified data
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Whether or not an data exists by key
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Unsets an data by key
     *
     * @param string $key
     */
    public function __unset($key)
    {
        $this->forget($key);
    }

    /**
     * Assigns a value to the specified offset
     *
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Whether or not an offset exists
     *
     * @param string $offset
     *
     * @access public
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Unsets an offset
     *
     * @param string $offset
     *
     * @return array
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->forget($offset);
        }
    }

    /**
     * Returns the value at specified offset
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->get($offset) : null;

    }

}