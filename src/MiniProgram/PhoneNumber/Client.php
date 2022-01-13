<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\PhoneNumber;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @package EasyWeChat\MiniProgram\PhoneNumber
 *
 * @author 读心印 <aa24615@qq.com>
 */
class Client extends BaseClient
{
    /**
     * 获取用户手机号.
     *
     * @see https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/phonenumber/phonenumber.getPhoneNumber.html
     *
     * @param string $code
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function getUserPhoneNumber(string $code)
    {
        $params = [
            'code' => $code
        ];

        return $this->httpPostJson('wxa/business/getuserphonenumber', $params);
    }
}
