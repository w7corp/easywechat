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
 * 模板卡片消息
 * 
 * @link https://work.weixin.qq.com/api/doc/90000/90135/90236#%E6%A8%A1%E6%9D%BF%E5%8D%A1%E7%89%87%E6%B6%88%E6%81%AF
 */
class TemplateCard extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'template_card';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'card_type',
    ];
}
