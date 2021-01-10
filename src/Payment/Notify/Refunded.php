<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Notify;

use Closure;
use EasyWeChat\Kernel\Support\XML;

class Refunded extends Handler
{
    protected $check = false;

    /**
     * @param \Closure $closure
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function handle(Closure $closure)
    {
        $this->strict(
            \call_user_func($closure, $this->getMessage(), $this->reqInfo(), [$this, 'fail'])
        );

        return $this->toResponse();
    }

    /**
     * Decrypt the `req_info` from request message.
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function reqInfo()
    {
        return XML::parse($this->decryptMessage('req_info'));
    }
}
