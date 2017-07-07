<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Invoice;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Get invoice link.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function getUserTitleUrl(array $data)
    {
        return $this->httpPostJson('card/invoice/biz/getusertitleurl', $data);
    }

    /**
     * Gets the qrcode link.
     *
     * @param string $attach
     *
     * @return mixed
     */
    public function getSelectTitleUrl(string $attach)
    {
        return $this->httpPostJson('card/invoice/biz/getselecttitleurl', compact('attach'));
    }

    /**
     * Merchants scan the user's qrcode to get the invoice information.
     *
     * @param string $text
     *
     * @return mixed
     */
    public function scanTitle($text)
    {
        return $this->httpPostJson('card/invoice/biz/scantitle', ['scan_text' => $text]);
    }
}
