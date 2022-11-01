<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Message;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * @param string|\EasyWeChat\Kernel\Messages\Message $message
     *
     * @return \EasyWeChat\Work\Message\Messenger
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function message($message)
    {
        return (new Messenger($this))->message($message);
    }

    /**
     * @param array $message
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(array $message)
    {
        return $this->httpPostJson('cgi-bin/message/send', $message);
    }

    /**
     * 更新任务卡片消息状态
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/91579
     *
     * @param array $userids
     * @param int $agentId
     * @param string $taskId
     * @param string $replaceName
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     */
    public function updateTaskcard(array $userids, int $agentId, string $taskId, string $replaceName = '已收到')
    {
        $params = [
            'userids' => $userids,
            'agentid' => $agentId,
            'task_id' => $taskId,
            'replace_name' => $replaceName
        ];

        return $this->httpPostJson('cgi-bin/message/update_taskcard', $params);
    }

    /**
     *  * 更新模版卡片消息状态
     *
     * @see https://developer.work.weixin.qq.com/document/path/94888
     *
     * @param array $userids
     * @param int $agentId
     * @param string $responseCode
     * @param string $replaceName
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateTemplateCard(array $userids, int $agentId, string $responseCode, string $replaceName = '已收到')
    {
        $params = [
            'userids' => $userids,
            'agentid' => $agentId,
            'response_code' => $responseCode,
            'button' => [
                'replace_name' => $replaceName
            ]
        ];

        return $this->httpPostJson('cgi-bin/message/update_template_card', $params);
    }
}
