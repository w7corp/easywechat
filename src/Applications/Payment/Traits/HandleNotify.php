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
     * @return Response
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

        return new Response(XML::build($response));
    }

    /**
     * Handle native scan notify.
     * https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_4
     * The callback shall return string of prepay_id or throw an exception.
     *
     * @param callable $callback
     *
     * @return Response
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
                'appid' => $this->merchant->app_id,
                'mch_id' => $this->merchant->merchant_id,
                'nonce_str' => uniqid(),
                'prepay_id' => strval($prepayId),
                'result_code' => 'SUCCESS',
            ];
            $response['sign'] = generate_sign($response, $this->merchant->key);
        } catch (\Exception $e) {
            $response = [
                'return_code' => 'SUCCESS',
                'return_msg' => $e->getCode(),
                'result_code' => 'FAIL',
                'err_code_des' => $e->getMessage(),
            ];
        }

        return new Response(XML::build($response));
    }

    /**
     * Return Notify instance.
     *
     * @return \EasyWeChat\Applications\OfficialAccount\Payment\Notify
     */
    public function getNotify()
    {
        return new Notify($this->merchant);
    }
}
