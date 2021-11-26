<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * EchoStrHandler.
 *
 * @author xiaomin <keacefull@gmail.com>
 */
class EchoStrHandler implements EventHandlerInterface
{
    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * EchoStrHandler constructor.
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }

    /**
     * @param mixed $payload
     *
     * @return FinallyResult|null
     */
    public function handle($payload = null)
    {
        if ($decrypted = $this->app['request']->get('echostr')) {
            $str = $this->app['encryptor_corp']->decrypt(
                $decrypted,
                $this->app['request']->get('msg_signature'),
                $this->app['request']->get('nonce'),
                $this->app['request']->get('timestamp')
            );

            return new FinallyResult($str);
        }
        //把SuiteTicket缓存起来
        if (!empty($payload['SuiteTicket']) && !empty($payload['SuiteId']) && $this->app['config']['suite_id'] == $payload['SuiteId']) {
            $this->app['suite_ticket']->setTicket($payload['SuiteTicket']);
        }
    }
}
