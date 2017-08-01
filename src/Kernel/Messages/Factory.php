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
 * Class Factory.
 *
 * @author overtrue <i@overtrue.me>
 */
class Factory
{
    /**
     * @param array $attributes
     */
    public static function createFromMedia($attributes = [])
    {
        //TODO: 从素材转换为消息对象
    }

    /**
     * @param array $attributes
     */
    public static function createFromUserMessage($attributes = [])
    {
        //TODO: 从请求消息解析为消息对象
    }
}
