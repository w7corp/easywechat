<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Traits;

use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\Support;
use EasyWeChat\Payment\Notify;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function handleNotify(callable $callback, Request $request = null)
    {
        $notify = $this->getNotify($request);

        if (!$notify->isValid()) {
            throw new Exception('Invalid request payloads.', 400);
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
     * Handle refund notify.
     *
     * @param callable $callback
     * @param Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function handleRefundNotify(callable $callback, Request $request = null)
    {
        // please notice that the 'req_info' has been decrypted here
        $notify = $this->getNotify($request)->decryptReqInfo();

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
     * The callback shall return string of prepay_id or throw an exception.
     *
     * @see https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_4
     *
     * @param callable $callback
     * @param Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function handleScanNotify(callable $callback, Request $request = null)
    {
        $notify = $this->getNotify($request);

        if (!$notify->isValid()) {
            throw new Exception('Invalid request payloads.', 400);
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
     * @param Request  $request
     *
     * @return \EasyWeChat\Payment\Notify
     */
    public function getNotify(Request $request = null): Notify
    {
        return new Notify($this->app['merchant'], $request);
    }
}
