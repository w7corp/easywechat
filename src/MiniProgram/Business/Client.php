<?php

/*
 * This file is part of the overtrue/wechat.
 *
 */

namespace EasyWeChat\MiniProgram\Business;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class Client.
 *
 * @author wangdongzhao <elim051@163.com>
 */
class Client extends BaseClient
{
    /**
     * Business register
     * @param string $accountName
     * @param string $nickname
     * @param string $iconMediaId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function register(string $accountName, string $nickname, string $iconMediaId)
    {
        $params = [
            'account_name' => $accountName,
            'nickname' => $nickname,
            'icon_media_id' => $iconMediaId,
        ];

        return $this->httpPostJson('cgi-bin/business/register', $params);
    }

    /**
     * Get business
     * @param int $businessId
     * @param string $accountName
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBusiness(int $businessId = 0, string $accountName = '')
    {
        if (empty($businessId) && empty($accountName)) {
            throw new InvalidArgumentException('Missing parameter.');
        }
        if ($businessId) {
            $params = [
                'business_id' => $businessId,
            ];
        } else {
            $params = [
                'account_name' => $accountName,
            ];
        }

        return $this->httpPostJson('cgi-bin/business/get', $params);
    }

    /**
     * Get business list
     * @param int $offset
     * @param int $count
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(int $offset = 0, int $count = 10)
    {
        $params = [
            'offset' => $offset,
            'count' => $count,
        ];

        return $this->httpPostJson('cgi-bin/business/list', $params);
    }

    /**
     * Update business.
     * @param int $businessId
     * @param string $nickname
     * @param string $iconMediaId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(int $businessId, string $nickname = '', string $iconMediaId = '')
    {
        $params = [
            'business_id' => $businessId,
            'nickname' => $nickname,
            'icon_media_id' => $iconMediaId,
        ];

        return $this->httpPostJson('cgi-bin/business/update', $params);
    }

    /**
     * Get message builder.
     *
     * @param \EasyWeChat\Kernel\Messages\Message|string $message
     *
     * @return \EasyWeChat\MiniProgram\Business\Messenger
     */
    public function message($message)
    {
        $messageBuilder = new Messenger($this);

        return $messageBuilder->message($message);
    }

    /**
     * Send a message.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(array $message)
    {
        return $this->httpPostJson('cgi-bin/message/custom/business/send', $message);
    }

    /**
     * Typing status.
     * @param int $businessId
     * @param string $toUser openid
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function typing(int $businessId, string $toUser)
    {
        $params = [
            'business_id' => $businessId,
            'touser' => $toUser,
            'command' => 'Typing',
        ];

        return $this->httpPostJson('cgi-bin/business/typing', $params);
    }
}
