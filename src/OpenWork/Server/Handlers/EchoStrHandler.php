<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * EchoStrHandler.
 *
 */
class EchoStrHandler implements EventHandlerInterface
{
    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @param ServiceContainer $app
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
        if (!empty($payload['SuiteTicket'])) {
            $this->app['suite_ticket']->setTicket($payload['SuiteTicket']);
        }

        return null;
    }
}
