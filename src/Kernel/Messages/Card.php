<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

class Card extends Message
{
    /**
     * Message type.
     *
     * @var string
     */
    protected string $type = 'wxcard';

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
