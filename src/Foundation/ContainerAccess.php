<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * ContainerAccess.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2017
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Foundation;

use Pimple\Container;

/**
 * Class ContainerAccess.
 */
abstract class ContainerAccess
{
    /**
     * Container prefix.
     *
     * @var string
     */
    protected $containerPrefix;

    /**
     * Container.
     *
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * MiniProgram constructor.
     *
     * @param \Pimple\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Gets a parameter or an object from pimple container.
     *
     * @param string $key The unique identifier for the parameter or object
     *
     * @return mixed The value of the parameter or an object
     *
     * @throws \InvalidArgumentException if the identifier is not defined
     */
    public function __get($key)
    {
        return $this->container->offsetGet($this->containerPrefix.$key);
    }
}
