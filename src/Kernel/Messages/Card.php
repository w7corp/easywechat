<?php

declare(strict_types=1);





/**
 * Card.<?php

declare(strict_types=1);

.
 *
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Kernel\Messages;

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
    protected array $properties = ['card_id'];

    /**
     * @param string $cardId
     */
    public function __construct(string $cardId)
    {
        parent::__construct(['card_id' => $cardId]);
    }
}
