<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\WeWork;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\Arr;

/**
 * Class AgentFactory.
 *
 * @property \EasyWeChat\WeWork\Agent\Client $agent
 *
 * @author overtrue <i@overtrue.me>
 */
class AgentFactory
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $prepends = [];

    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * AgentFactory constructor.
     *
     * @param array $config
     * @param array $prepends
     */
    public function __construct(array $config, array $prepends = [])
    {
        $this->config = $config;
        $this->prepends = $prepends;
    }

    /**
     * @param string $name
     *
     * @return Application
     */
    public function agent(string $name)
    {
        return $this->make($name);
    }

    /**
     * @param string $name
     *
     * @return Application
     *
     * @throws InvalidArgumentException
     */
    public function make(string $name)
    {
        if (!isset($this->config['agents'][$name])) {
            throw new InvalidArgumentException(sprintf('No agent named "%s".', $name));
        }

        if (!isset($this->resolved[$name])) {
            $config = array_merge(Arr::except($this->config, 'agents'), $this->config['agents'][$name]);
            $this->resolved[$name] = new Application($config, $this->prepends);
        }

        return $this->resolved[$name];
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        $default = Arr::get($this->config, 'default_agent', key($this->config['agents']));

        return $this->make($default)->$property;
    }
}
