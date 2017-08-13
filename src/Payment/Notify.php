<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment;

use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\Support;
use EasyWeChat\Kernel\Support\AES;
use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Kernel\Support\XML;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Notify.
 *
 * @author overtrue <i@overtrue.me>
 */
class Notify
{
    /**
     * Merchant instance.
     *
     * @var \EasyWeChat\Payment\Merchant
     */
    protected $merchant;

    /**
     * Request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * Payment notify (prependsct from XML).
     *
     * @var Collection
     */
    protected $notify;

    /**
     * Constructor.
     *
     * @param Merchant $merchant
     * @param Request  $request
     */
    public function __construct(Merchant $merchant, Request $request = null)
    {
        $this->merchant = $merchant;
        $this->request = $request ?: Request::createFromGlobals();
    }

    /**
     * Validate the request params.
     *
     * @return bool
     */
    public function isValid()
    {
        $localSign = Support\generate_sign($this->getNotify()->except('sign')->all(), $this->merchant->key, 'md5');

        return $localSign === $this->getNotify()->get('sign');
    }

    /**
     * Decrypt req_info in refund Notify.
     *
     * @return $this
     *
     * @throws Exception
     */
    public function decryptReqInfo()
    {
        if ($reqInfo = $this->getNotify()->get('req_info')) {
            $decrypted = AES::decrypt(
                base64_decode($reqInfo, true),
                md5($this->merchant->key),
                substr(md5($this->merchant->key), 0, 16)
            );

            $this->notify->req_info = $decrypted;

            return $this;
        } else {
            throw new Exception('req_info does not exist.', 400);
        }
    }

    /**
     * Return the notify body from request.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function getNotify()
    {
        if (!empty($this->notify)) {
            return $this->notify;
        }

        try {
            $xml = XML::parse(strval($this->request->getContent()));
        } catch (\Throwable $e) {
            throw new Exception('Invalid request XML: '.$e->getMessage(), 400);
        }

        if (!is_array($xml) || empty($xml)) {
            throw new Exception('Invalid request XML.', 400);
        }

        return $this->notify = new Collection($xml);
    }
}
