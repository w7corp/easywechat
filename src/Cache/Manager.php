<?php

/*
 * This file is part of the EasyWeChat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Manager.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Cache;

use EasyWeChat\Cache\Adapters\AdapterInterface;
use EasyWeChat\Cache\Adapters\FileAdapter;

/**
 * Class Manager.
 *
 * @method get($key, $default)
 * @method set($key, $value, $lifetime = 7200)
 */
class Manager
{
    /**
     * Cache adapter.
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * App id.
     *
     * @var string
     */
    protected $appId;

    /**
     * Constructor.
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        $this->adapter = $adapter;
    }

    /**
     * Set cache adapter.
     *
     * @param AdapterInterface $adapter
     *
     * @return Manager
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Return current cache adapter.
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter ?  $this->adapter : $this->adapter = $this->makeAdapter();
    }

    /**
     * Return cache adapter.
     *
     * @return AdapterInterface
     */
    public function makeAdapter()
    {
        if ($this->adapter) {
            return $this->adapter;
        }

        return $this->makeDefaultAdapter();
    }

    /**
     * Return default cache adapter instance.
     *
     * @return AdapterInterface
     */
    protected function makeDefaultAdapter()
    {
        $adapter = new FileAdapter();

        $adapter->setAppId($this->appId);

        return $adapter;
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->getAdapter(), $method], $args);
    }
}
