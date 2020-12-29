<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\GroupRobot\Messages;

/**
 * Class Text.
 *
 * @author her-cat <i@her-cat.com>
 */
class Text extends Message
{
    /**
     * @var string
     */
    protected $type = 'text';

    /**
     * @var array
     */
    protected $properties = ['content', 'mentioned_list', 'mentioned_mobile_list'];

    /**
     * Text constructor.
     *
     * @param string|array $userIds
     * @param string|array $mobiles
     */
    public function __construct(string $content, $userIds = [], $mobiles = [])
    {
        parent::__construct([
            'content' => $content,
            'mentioned_list' => (array) $userIds,
            'mentioned_mobile_list' => (array) $mobiles,
        ]);
    }

    /**
     * @param array $userIds
     *
     * @return Text
     */
    public function mention($userIds)
    {
        $this->set('mentioned_list', (array) $userIds);

        return $this;
    }

    /**
     * @param array $mobiles
     *
     * @return Text
     */
    public function mentionByMobile($mobiles)
    {
        $this->set('mentioned_mobile_list', (array) $mobiles);

        return $this;
    }
}
