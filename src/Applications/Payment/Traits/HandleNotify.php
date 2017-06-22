<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment\Traits;

use EasyWeChat\Applications\Payment\Notify;
use EasyWeChat\Exceptions\FaultException;
use EasyWeChat\Support;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait HandleNotify.
 *
 * @author overtrue <i@overtrue.me>
 */
trait HandleNotify
{
    /**
     * Handle payment notify.
     *
     * @param callable $callback
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \EasyWeChat\Exceptions\FaultException
     */
    public function handleNotify(callable $callback)
    {
        $notify = $this->getNotify();

        if (!$notify->isValid()) {
            throw new FaultException('Invalid request payloads.', 400);
        }

        $notify = $notify->getNotify();
        $successful = $notify->get('result_code') === 'SUCCESS';

        $handleResult = call_user_func_array($callback, [$notify, $successful]);

        if (is_bool($handleResult) && $handleResult) {
            $response = [
                'return_code' => 'SUCCESS',
                'return_msg' => 'OK',
            ];
        } else {
            $response = [
                'return_code' => 'FAIL',
                'return_msg' => $handleResult,
            ];
        }

        return new Response(Support\XML::build($response));
    }

    /**
     * Handle native scan notify.
     * https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_4
     * The callback shall return string of prepay_id or throw an exception.
     *
     * @param callable $callback
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \EasyWeChat\Exceptions\FaultException
     */
    public function handleScanNotify(callable $callback)
    {
        $notify = $this->getNotify();

        if (!$notify->isValid()) {
            throw new FaultException('Invalid request payloads.', 400);
        }

        $notify = $notify->getNotify();

        try {
            $prepayId = call_user_func_array($callback, [$notify->get('product_id'), $notify->get('openid'), $notify]);
            $response = [
                'return_code' => 'SUCCESS',
                'appid' => $this->app['merchant']->app_id,
                'mch_id' => $this->app['merchant']->merchant_id,
                'nonce_str' => uniqid(),
                'prepay_id' => strval($prepayId),
                'result_code' => 'SUCCESS',
            ];
            $response['sign'] = Support\generate_sign($response, $this->app['merchant']->key);
        } catch (Exception $exception) {
            $response = [
                'return_code' => 'SUCCESS',
                'return_msg' => $exception->getCode(),
                'result_code' => 'FAIL',
                'err_code_des' => $exception->getMessage(),
            ];
        }

        return new Response(Support\XML::build($response));
    }

    /**
     * Return Notify instance.
     *
     * @return \EasyWeChat\Applications\Payment\Notify
     */
    public function getNotify()
    {
        return new Notify($this->app['merchant']);
    }
}
