<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Wedrive;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client
 *
 * @author lio990527 <lio990527@163.com>
 */
class Client extends BaseClient
{
    /**
     * 获取盘专业版信息
     *
     * @param string $userid
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/95856#%E8%8E%B7%E5%8F%96%E7%9B%98%E4%B8%93%E4%B8%9A%E7%89%88%E4%BF%A1%E6%81%AF
     */
    public function proInfo($userid)
    {
        return $this->httpPostJson('cgi-bin/wedrive/mng_pro_info', compact('userid'));
    }

    /**
     * 获取盘容量信息
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/95856#%E8%8E%B7%E5%8F%96%E7%9B%98%E5%AE%B9%E9%87%8F%E4%BF%A1%E6%81%AF
     */
    public function capacity()
    {
        return $this->httpPostJson('cgi-bin/wedrive/mng_capacity');
    }
}
