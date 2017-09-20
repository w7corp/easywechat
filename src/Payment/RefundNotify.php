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

use EasyWeChat\Core\Exceptions\FaultException;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\XML;
use Symfony\Component\HttpFoundation\Request;

class RefundNotify
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
     * Payment notify (extract from XML).
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
     * Return the notify body from request.
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws \EasyWeChat\Core\Exceptions\FaultException
     */
    public function getNotify()
    {
        if (!empty($this->notify)) {
            return $this->notify;
        }

        try {
            $xml = XML::parse(strval($this->request->getContent()));
        } catch (\Throwable $t) {
            throw new FaultException('Invalid request XML: '.$t->getMessage(), 400);
        } catch (\Exception $e) {
            throw new FaultException('Invalid request XML: '.$e->getMessage(), 400);
        }

        if (!is_array($xml) || empty($xml)) {
            throw new FaultException('Invalid request XML.', 400);
        }

        $xml['req_info'] = $this->decode($xml['req_info']);

        return $this->notify = new Collection($xml);
    }

    public function decode($reqInfo)
    {
        $ciphertext = base64_decode($reqInfo, true);
        $key = md5($this->merchant->key);
        $decrypted = openssl_decrypt($ciphertext, 'aes-256-ecb', $key, OPENSSL_RAW_DATA);

        return XML::parse($decrypted);
    }
}
