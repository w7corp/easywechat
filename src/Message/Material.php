<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Material.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Message;

use EasyWeChat\Support\Attribute;

/**
 * Class Material.
 */
class Material extends Attribute
{
    /**
     * Material type.
     *
     * @var string
     */
    protected $type = 'mpnews';

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
    public function __construct($mediaId, $type = null)
    {
        $this->set('media_id', $mediaId);

        if ($type) {
            $this->type = $type;
        }
    }
}
