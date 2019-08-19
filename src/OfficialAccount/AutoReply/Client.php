<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\AutoReply;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * Get current auto reply settings.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     * @throws \Safe\Exceptions\StringsException
     */
    public function current()
    {
        return $this->httpGet('cgi-bin/get_current_autoreply_info');
    }
}
