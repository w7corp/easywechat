<?php

declare(strict_types=1);

namespace EasyWeChat\Work\GroupRobot\Messages;

class Markdown extends Message
{
    /**
     * @var string
     */
    protected string  $type = 'markdown';

    /**
     * @var array
     */
    protected array $properties = ['content'];

    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        parent::__construct(compact('content'));
    }
}
