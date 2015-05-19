<?php
/**
 * Part of Overtrue\Wechat
 *
 * @author overtrue <i@overtrue.me>
 */

namespace Overtrue\Wechat\Messages;

/**
 * 图文项
 */
class NewsItem extends BaseMessage
{

    /**
     * 属性
     *
     * @var array
     */
    protected $properties = array(
                             'title',
                             'description',
                             'pic_url',
                             'url',
                            );
}
