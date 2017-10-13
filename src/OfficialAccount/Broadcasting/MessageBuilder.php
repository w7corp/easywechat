<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Broadcasting;

use EasyWeChat\Kernel\Contracts\MessageInterface;
use EasyWeChat\Kernel\Exceptions\RuntimeException;

/**
 * Class MessageBuilder.
 *
 * @author overtrue <i@overtrue.me>
 */
class MessageBuilder
{
    /**
     * @var mixed
     */
    protected $to;

    /**
     * @var \EasyWeChat\Kernel\Contracts\MessageInterface
     */
    protected $message;

    /**
     * Set message.
     *
     * @param \EasyWeChat\Kernel\Contracts\MessageInterface $message
     *
     * @return $this
     */
    public function message(MessageInterface $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set target user or group.
     *
     * @param mixed $to
     *
     * @return $this
     */
    public function to($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Build message.
     *
     * @param array $prepends
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function build(array $prepends = []): array
    {
        if (empty($this->message)) {
            throw new RuntimeException('No message content to send.');
        }

        $content = $this->message->transformForJsonRequest();

        if (empty($prepends)) {
            $prepends = $this->buildGroup($this->to);
        }

        $message = array_merge($prepends, $content);

        return $message;
    }

    /**
     * Build preview message.
     *
     * @param string $by
     *
     * @return array
     */
    public function buildForPreview(string $by): array
    {
        return $this->build([$by => $this->to]);
    }

    /**
     * Build group.
     *
     * @param mixed $group
     *
     * @return array
     */
    protected function buildGroup($group): array
    {
        if (is_null($group)) {
            $group = [
                'filter' => [
                    'is_to_all' => true,
                ],
            ];
        } elseif (is_array($group)) {
            $group = [
                'touser' => $group,
            ];
        } else {
            $group = [
                'filter' => [
                    'is_to_all' => false,
                    'group_id' => $group,
                ],
            ];
        }

        return $group;
    }
}
