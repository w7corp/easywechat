<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $task_id
 * @property array  $btn
 */
class TaskCard extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'taskcard';

    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = [
        'title',
        'description',
        'url',
        'task_id',
        'btn',
    ];
}
