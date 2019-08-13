<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\EventDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Dispatcher.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 *
 * @method string|null dispatch(object $event)
 */
class Dispatcher
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    /**
     * Dispatcher constructor.
     */
    public function __construct()
    {
        $this->dispatcher = class_exists(EventDispatcher::class) ? new EventDispatcher() : null;
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     * @param int      $priority  The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        // Makes callable.
        if (is_string($listener)) {
            $listener = new $listener();
        }

        if ($this->dispatcher) {
            $this->dispatcher->addListener($eventName, $listener, $priority);
        }
    }

    /**
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (!$this->dispatcher) {
            return;
        }

        return call_user_func_array([$this->dispatcher, $name], $args);
    }
}
