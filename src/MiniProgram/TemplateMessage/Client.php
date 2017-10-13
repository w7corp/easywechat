<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\TemplateMessage;

use EasyWeChat\OfficialAccount\TemplateMessage\Client as BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    const API_SEND = 'cgi-bin/message/wxopen/template/send';
    const API_PRIVATE_TEMPLATE_LIST = 'cgi-bin/wxopen/template/list';

    /**
     * {@inheritdoc}.
     */
    protected $message = [
        'touser' => '',
        'template_id' => '',
        'page' => '',
        'form_id' => '',
        'data' => [],
        'emphasis_keyword' => '',
    ];

    /**
     * {@inheritdoc}.
     */
    protected $required = ['touser', 'template_id', 'form_id'];

    /**
     * 4.获取帐号下已存在的模板列表
     *
     * https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=ACCESS_TOKEN
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function getTemplateList($offset, $count)
    {
        return $this->httpPostJson(static::API_PRIVATE_TEMPLATE_LIST, ['offset'=>$offset, 'count'=>$count]);
    }
}
