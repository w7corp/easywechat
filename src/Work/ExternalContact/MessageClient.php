<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\ExternalContact;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class MessageClient.
 *
 * @author milkmeowo <milkmeowo@gmail.com>
 */
class MessageClient extends BaseClient
{
    /**
     * Required attributes.
     *
     * @var array
     */
    protected $required = ['content', 'title', 'url', 'pic_media_id', 'appid', 'page'];

    protected $textMessage = [
        'content' => '',
    ];

    protected $imageMessage = [

    ];

    protected $linkMessage = [
        'title' => '',
        'picurl' => '',
        'desc' => '',
        'url' => '',
    ];

    protected $miniprogramMessage = [
        'title' => '',
        'pic_media_id' => '',
        'appid' => '',
        'page' => '',
    ];

    /**
     * 添加企业群发消息模板
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91560
     *
     * @param array $msg
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function submit(array $msg)
    {
        $params = $this->formatMessage($msg);

        return $this->httpPostJson('cgi-bin/externalcontact/add_msg_template', $params);
    }

    /**
     * 获取企业群发消息发送结果.
     *
     * @see https://developer.work.weixin.qq.com/document/16251
     *
     * @param string      $msgId  群发消息的id，通过{@see MessageClient::submit() 添加企业群发消息模板}接口返回
     * @param int         $limit  返回的最大记录数，整型，最大值10000，默认值10000
     * @param string|null $cursor 用于分页查询的游标，字符串类型，由上一次调用返回，首次调用可不填
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection SpellCheckingInspection
     */
    public function get(string $msgId, int $limit = 10000, ?string $cursor = null)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/get_group_msg_result', [
            'msgid' => $msgId,
            'limit' => $limit,
            'cursor' => $cursor
        ]);
    }

    /**
     * 获取群发记录列表.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93338#%E8%8E%B7%E5%8F%96%E7%BE%A4%E5%8F%91%E8%AE%B0%E5%BD%95%E5%88%97%E8%A1%A8
     *
     * @param string $chatType 群发任务的类型，默认为single，表示发送给客户，group表示发送给客户群
     * @param int $startTime 群发任务记录开始时间
     * @param int $endTime 群发任务记录结束时间
     * @param string|null $creator 群发任务创建人企业账号id
     * @param int|null $filterType 创建人类型。0：企业发表 1：个人发表 2：所有，包括个人创建以及企业创建，默认情况下为所有类型
     * @param int|null $limit 返回的最大记录数，整型，最大值100，默认值50，超过最大值时取默认值
     * @param string|null $cursor 用于分页查询的游标，字符串类型，由上一次调用返回，首次调用可不填
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGroupmsgListV2(string $chatType, int $startTime, int $endTime, ?string $creator = null, ?int $filterType = null, ?int $limit = null, ?string $cursor = null)
    {
        $data = [
            'chat_type' => $chatType,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'creator' => $creator,
            'filter_type' => $filterType,
            'limit' => $limit,
            'cursor' => $cursor,
        ];
        $writableData = array_filter($data, function (string $key) use ($data) {
            return !is_null($data[$key]);
        }, ARRAY_FILTER_USE_KEY);
        return $this->httpPostJson('cgi-bin/externalcontact/get_groupmsg_list_v2', $writableData);
    }

    /**
     * 获取群发成员发送任务列表.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93338#%E8%8E%B7%E5%8F%96%E7%BE%A4%E5%8F%91%E6%88%90%E5%91%98%E5%8F%91%E9%80%81%E4%BB%BB%E5%8A%A1%E5%88%97%E8%A1%A8
     *
     * @param string $msgId 群发消息的id，通过获取群发记录列表接口返回
     * @param int|null $limit 返回的最大记录数，整型，最大值1000，默认值500，超过最大值时取默认值
     * @param string|null $cursor 用于分页查询的游标，字符串类型，由上一次调用返回，首次调用可不填
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGroupmsgTask(string $msgId, ?int $limit = null, ?string $cursor = null)
    {
        $data = [
            'msgid' => $msgId,
            'limit' => $limit,
            'cursor' => $cursor,
        ];
        $writableData = array_filter($data, function (string $key) use ($data) {
            return !is_null($data[$key]);
        }, ARRAY_FILTER_USE_KEY);
        return $this->httpPostJson('cgi-bin/externalcontact/get_groupmsg_task', $writableData);
    }

    /**
     * 获取企业群发成员执行结果.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93338#%E8%8E%B7%E5%8F%96%E4%BC%81%E4%B8%9A%E7%BE%A4%E5%8F%91%E6%88%90%E5%91%98%E6%89%A7%E8%A1%8C%E7%BB%93%E6%9E%9C
     *
     * @param string $msgId 群发消息的id，通过获取群发记录列表接口返回
     * @param string $userid 发送成员userid，通过获取群发成员发送任务列表接口返回
     * @param int|null $limit 返回的最大记录数，整型，最大值1000，默认值500，超过最大值时取默认值
     * @param string|null $cursor 用于分页查询的游标，字符串类型，由上一次调用返回，首次调用可不填
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGroupmsgSendResult(string $msgId, string $userid, ?int $limit = null, ?string $cursor = null)
    {
        $data = [
            'msgid' => $msgId,
            'userid' => $userid,
            'limit' => $limit,
            'cursor' => $cursor,
        ];
        $writableData = array_filter($data, function (string $key) use ($data) {
            return !is_null($data[$key]);
        }, ARRAY_FILTER_USE_KEY);
        return $this->httpPostJson('cgi-bin/externalcontact/get_groupmsg_send_result', $writableData);
    }

    /**
     * 发送新客户欢迎语.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91688
     *
     * @param string $welcomeCode
     * @param array $msg
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendWelcome(string $welcomeCode, array $msg)
    {
        $formattedMsg = $this->formatMessage($msg);

        $params = array_merge($formattedMsg, [
            'welcome_code' => $welcomeCode,
        ]);

        return $this->httpPostJson('cgi-bin/externalcontact/send_welcome_msg', $params);
    }

    /**
     * @param array $data
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function formatMessage(array $data = [])
    {
        $params = $data;

        if (!empty($params['text'])) {
            $params['text'] = $this->formatFields($params['text'], $this->textMessage);
        }

        if (!empty($params['image'])) {
            $params['image'] = $this->formatFields($params['image'], $this->imageMessage);
        }

        if (!empty($params['link'])) {
            $params['link'] = $this->formatFields($params['link'], $this->linkMessage);
        }

        if (!empty($params['miniprogram'])) {
            $params['miniprogram'] = $this->formatFields($params['miniprogram'], $this->miniprogramMessage);
        }

        return $params;
    }

    /**
     * @param array $data
     * @param array $default
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function formatFields(array $data = [], array $default = [])
    {
        $params = array_merge($default, $data);
        foreach ($params as $key => $value) {
            if (in_array($key, $this->required, true) && empty($value) && empty($default[$key])) {
                throw new InvalidArgumentException(sprintf('Attribute "%s" can not be empty!', $key));
            }

            $params[$key] = empty($value) ? $default[$key] : $value;
        }

        return $params;
    }
}
