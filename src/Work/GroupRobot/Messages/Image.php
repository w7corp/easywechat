<?php

declare(strict_types=1);

namespace EasyWeChat\Work\GroupRobot\Messages;

class Image extends Message
{
    /**
     * @var string
     */
    protected string  $type = 'image';

    /**
     * @var array
     */
    protected array $properties = ['base64', 'md5'];

    /**
     * @param string $base64
     * @param string $md5
     */
    public function __construct(string $base64, string $md5)
    {
        parent::__construct(compact('base64', 'md5'));
    }
}
