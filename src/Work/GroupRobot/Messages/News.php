<?php

declare(strict_types=1);

namespace EasyWeChat\Work\GroupRobot\Messages;

class News extends Message
{
    /**
     * @var string
     */
    protected string  $type = 'news';

    /**
     * @var array
     */
    protected array $properties = ['items'];

    /**
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(compact('items'));
    }

    /**
     * @param array $data
     * @param array $aliases
     *
     * @return array
     */
    public function propertiesToArray(array $data, array $aliases = []): array
    {
        return ['articles' => array_map(function ($item) {
            if ($item instanceof NewsItem) {
                return $item->toJsonArray();
            }
        }, $this->get('items'))];
    }
}
