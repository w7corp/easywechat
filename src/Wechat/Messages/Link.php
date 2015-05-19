<?php
/**
 * Part of Overtrue\Wechat
 *
 * @author overtrue <i@overtrue.me>
 */

namespace Overtrue\Wechat\Messages;

/**
 * 链接消息
 *
 * @property string $content
 */
class Link extends BaseMessage
{

    /**
     * 属性
     *
     * @var array
     */
    protected $properties = array(
                             'title',
                             'description',
                             'url',
                            );
}
