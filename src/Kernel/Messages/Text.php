<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $content
 */
class Text extends Message
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'text';

    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = ['content'];

    /**
     * @param string $content
     */
    public function __construct(string $content = '')
    {
        parent::__construct(compact('content'));
    }

    /**
     * @return array
     */
    public function toXmlArray()
    {
        return [
            'Content' => $this->get('content'),
        ];
    }
}
