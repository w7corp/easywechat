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
 * Card.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Message;

/**
 * Class Card.
 */
class Card extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'wxcard';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = ['card_id'];

    /**
     * Material constructor.
     *
     * @param string $mediaId
     * @param string $type
     */
    public function __construct($cardId)
    {
        $this->set('card_id', $cardId);
    }
}
