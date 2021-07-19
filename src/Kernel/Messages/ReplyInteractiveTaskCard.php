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
 * Class ReplyInteractiveTaskCard
 *
 * @property array{'replace_name':string} $properties
 *
 * @description 专门为回复 InteractiveTaskCard 类型任务卡片消息而创建的类型
 * @author      xyj2156
 *
 * @package     App\Extend\EnterpriseApplication\BusinessWX\Message
 */
class ReplyInteractiveTaskCard extends Message
{
    /**
     * Message Type
     *
     * @var string
     */
    protected $type = 'update_taskcard';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'replace_name',
    ];

    /**
     * ReplyInteractiveTaskCard constructor.
     *
     * @param string $replace_name
     */
    public function __construct(string $replace_name = '')
    {
        parent::__construct(compact('replace_name'));
    }

    public function toXmlArray()
    {
        return [
            'TaskCard' => [
                'ReplaceName' => $this->get('replace_name'),
            ],
        ];
    }
}
