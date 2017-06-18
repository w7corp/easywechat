<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Messages;

/**
 * Class Material.
 */
class Material extends Message
{
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = ['media_id'];

    /**
     * Material constructor.
     *
     * @param string $mediaId
     * @param string $type
     */
    public function __construct($type, $mediaId)
    {
        $this->set('media_id', $mediaId);
        $this->setType($type);
    }
}
