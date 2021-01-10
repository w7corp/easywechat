<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

use EasyWeChat\Kernel\Contracts\MediaInterface;
use EasyWeChat\Kernel\Support\Str;

class Media extends Message implements MediaInterface
{
    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = ['media_id'];

    /**
     * @var array
     */
    protected array $required = [
        'media_id',
    ];

    /**
     * @param string $mediaId
     * @param string $type
     * @param array  $attributes
     */
    public function __construct(string $mediaId, $type = null, array $attributes = [])
    {
        parent::__construct(array_merge(['media_id' => $mediaId], $attributes));

        !empty($type) && $this->setType($type);
    }

    /**
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getMediaId(): string
    {
        $this->assertRequiredAttributesExists();

        return $this->get('media_id');
    }

    public function toXmlArray()
    {
        return [
            Str::studly($this->getType()) => [
                'MediaId' => $this->get('media_id'),
            ],
        ];
    }
}
