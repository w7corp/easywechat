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
 * Guard.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Server\Guard as ServerGuard;
use EasyWeChat\Support\Collection;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Guard extends ServerGuard
{

    const EVENT_AUTHORIZED              = 'authorized';
    const EVENT_UNAUTHORIZED            = 'unauthorized';
    const EVENT_UPDATE_AUTHORIZED       = 'updateauthorized';
    const EVENT_COMPONENT_VERIFY_TICKET = 'component_verify_ticket';

    /**
     * Container in the scope of the open platform authorization.
     *
     * @var Container
     */
    protected $container;

    /**
     * Guard constructor.
     *
     * @param string $token
     * @param Request $request
     */
    public function __construct($token, Request $request = null)
    {
        parent::__construct($token, $request);
    }

    /**
     * Sets the container for use of event handlers.
     *
     * @param Container $container
     *
     * @see getHandler()
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function serve()
    {
        $this->handleMessage($this->getMessage());

        return new Response('success');
    }

    /**
     * Return for laravel-wechat.
     *
     * @return array
     */
    public function listServe()
    {
        $message = $this->getMessage();
        $this->handleMessage($message);

        $message = new Collection($message);

        return [
            $message->get('InfoType'), $message,
        ];
    }

    /**
     * Listen for wechat push event.
     *
     * @param callable|null $callback
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function listen($callback = null)
    {
        if ($callback) {
            $this->setMessageHandler($callback);
        }

        return $this->serve();
    }

    /**
     * @inheritdoc
     */
    protected function handleMessage($message)
    {
        $message = new Collection($message);
        $handler = $this->getHandler($message->get('InfoType'));

        $result = $handler->handle($message);

        // To be compatible with previous version: merges the auth result while
        // keeping the original message.
        $message->merge($result);
        $message = new Collection($message);

        if ($customHandler = $this->getMessageHandler()) {
            $customHandler($message);
        }

        return $result;
    }

    /**
     * Gets the handler by the info type..
     *
     * @param $type
     *
     * @return EventHandlers\EventHandler
     * @throws InvalidArgumentException
     */
    protected function getHandler($type)
    {
        $handler = $this->container->offsetGet("open_platform_handle_{$type}");

        if (! $handler) {
            throw new InvalidArgumentException("EventHandler \"$type\" does not exists.");
        }

        return $handler;
    }
}
