<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Security;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author lujunyi <lujunyi@shopex.cn>
 */
class Client extends BaseClient
{
    /**
     * 获取隐私接口列表.
     */
    public function get()
    {
        return $this->httpGet('wxa/security/get_privacy_interface');
    }

    /**
     * 申请隐私接口
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function set(array $params)
    {
        return $this->httpPostJson('wxa/security/apply_privacy_interface', $params);
    }
}
