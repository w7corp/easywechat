<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\SubscribeComponent;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author joyeekk <xygao2420@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 获取展示的公众号信息.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function get()
    {
        return $this->httpGet('wxa/getshowwxaitem');
    }

    /**
     * 设置展示的公众号.
     *
     * @param string $appid
     * @param int    $flag
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function update(string $appid, int $flag)
    {
        return $this->httpPostJson('wxa/updateshowwxaitem', [
            'appid' => $appid,
            'wxa_subscribe_biz_flag' => $flag,
        ]);
    }

    /**
     * 获取可以用来设置的公众号列表.
     *
     * @param int $page
     * @param int $num
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function getAvailableList(int $page, int $num)
    {
        return $this->httpGet('wxa/getwxamplinkforshow', [
            'page' => $page,
            'num' => $num,
        ]);
    }
}
