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

namespace EasyWeChat\Kernel\Messages;

/**
 * Class Card.
 */
class Card extends Message
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
     * Media constructor.
     *
     * @param string $cardId
     */
    public function __construct(string $cardId)
    {
        parent::__construct(['card_id' => $cardId]);
    }
}
