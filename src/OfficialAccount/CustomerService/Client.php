<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\CustomerService;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * List all staffs.
     *
     * @return mixed
     */
    public function list()
    {
        return $this->httpGet('cgi-bin/customservice/getkflist');
    }

    /**
     * List all online staffs.
     *
     * @return mixed
     */
    public function online()
    {
        return $this->httpGet('cgi-bin/customservice/getonlinekflist');
    }

    /**
     * Create a staff.
     *
     * @param string $account
     * @param string $nickname
     *
     * @return mixed
     */
    public function create(string $account, string $nickname)
    {
        $params = [
            'kf_account' => $account,
            'nickname' => $nickname,
        ];

        return $this->httpPostJson('customservice/kfaccount/add', $params);
    }

    /**
     * Update a staff.
     *
     * @param string $account
     * @param string $nickname
     *
     * @return mixed
     */
    public function update(string $account, string $nickname)
    {
        $params = [
            'kf_account' => $account,
            'nickname' => $nickname,
        ];

        return $this->httpPostJson('customservice/kfaccount/update', $params);
    }

    /**
     * Delete a staff.
     *
     * @param string $account
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete(string $account)
    {
        return $this->httpPostJson('customservice/kfaccount/del', [], ['kf_account' => $account]);
    }

    /**
     * Invite a staff.
     *
     * @param string $account
     * @param string $wechatId
     *
     * @return mixed
     */
    public function invite(string $account, string $wechatId)
    {
        $params = [
            'kf_account' => $account,
            'invite_wx' => $wechatId,
        ];

        return $this->httpPostJson('customservice/kfaccount/inviteworker', $params);
    }

    /**
     * Set staff avatar.
     *
     * @param string $account
     * @param string $path
     *
     * @return mixed
     */
    public function setAvatar(string $account, string $path)
    {
        return $this->httpUpload('customservice/kfaccount/uploadheadimg', ['media' => $path], [], ['kf_account' => $account]);
    }

    /**
     * Get message builder.
     *
     * @param \EasyWeChat\Kernel\Messages\Message|string $message
     *
     * @return \EasyWeChat\OfficialAccount\CustomerService\Messenger
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function message($message)
    {
        $messageBuilder = new Messenger($this);

        return $messageBuilder->message($message);
    }

    /**
     * Send a message.
     *
     * @param string|array $message
     *
     * @return mixed
     */
    public function send(array $message)
    {
        return $this->httpPostJson('cgi-bin/message/custom/send', $message);
    }

    /**
     * Get messages history.
     *
     * @param int $startTime
     * @param int $endTime
     * @param int $msgId
     * @param int $number
     *
     * @return mixed
     */
    public function messages($startTime, $endTime, int $msgId = 1, int $number = 10000)
    {
        $params = [
            'starttime' => is_numeric($startTime) ? $startTime : strtotime($startTime),
            'endtime' => is_numeric($endTime) ? $endTime : strtotime($endTime),
            'msgid' => $msgId,
            'number' => $number,
        ];

        return $this->httpPostJson('customservice/msgrecord/getmsglist', $params);
    }
}
