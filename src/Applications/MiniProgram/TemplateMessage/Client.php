<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\MiniProgram\TemplateMessage;

use EasyWeChat\Applications\OfficialAccount\TemplateMessage\Client as BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
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
    protected $defaults = [
        'touser' => '',
        'template_id' => '',
        'form_id' => '',
        'data' => [],
    ];

    /**
     * {@inheritdoc}.
     */
    protected $required = ['touser', 'template_id', 'form_id'];

    /**
     * Send a template message.
     *
     * @param $data
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Exceptions\InvalidArgumentException
     */
    public function send($data = [])
    {
        $params = $this->formatMessage($data);

        $this->restoreMessage();

        return $this->httpPostJson('cgi-bin/message/wxopen/template/send', $params);
    }
}
