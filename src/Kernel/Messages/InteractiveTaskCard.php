<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Messages;

/**
 * Class InteractiveTaskCard.
 *
 * @description 企业微信 interactive_taskcard 任务卡片消息类型
 *
 * @author      xyj2156
 * @date        2021年5月25日 15:21:03
 *
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $task_id
 * @property array  $btn
 */
class InteractiveTaskCard extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'interactive_taskcard';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'title',
        'description',
        'url',
        'task_id',
        'btn',
    ];
}
