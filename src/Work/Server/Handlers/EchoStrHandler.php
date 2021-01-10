<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\Kernel\ServiceContainer;

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
            $str = $this->app['encryptor']->decrypt(
                $decrypted,
                $this->app['request']->get('msg_signature'),
                $this->app['request']->get('nonce'),
                $this->app['request']->get('timestamp')
            );

            return new FinallyResult($str);
        }

        return null;
    }
}
