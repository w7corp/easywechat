<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $video
 * @property string $title
 * @property string $media_id
 * @property string $description
 * @property string $thumb_media_id
 */
class Video extends Media
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'video';

    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = [
        'title',
        'description',
        'media_id',
        'thumb_media_id',
    ];

    /**
     * @param string $mediaId
     * @param array  $attributes
     */
    public function __construct(string $mediaId, array $attributes = [])
    {
        parent::__construct($mediaId, 'video', $attributes);
    }

    public function toXmlArray()
    {
        return [
            'Video' => [
                'MediaId' => $this->get('media_id'),
                'Title' => $this->get('title'),
                'Description' => $this->get('description'),
            ],
        ];
    }
}
