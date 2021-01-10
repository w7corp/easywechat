<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $url
 * @property string $hq_url
 * @property string $title
 * @property string $description
 * @property string $thumb_media_id
 * @property string $format
 */
class Music extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'music';

    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = [
        'title',
        'description',
        'url',
        'hq_url',
        'thumb_media_id',
        'format',
    ];

    /**
     * Aliases of attribute.
     *
     * @var array
     */
    protected array $jsonAliases = [
        'musicurl' => 'url',
        'hqmusicurl' => 'hq_url',
    ];

    public function toXmlArray()
    {
        $music = [
            'Music' => [
                'Title' => $this->get('title'),
                'Description' => $this->get('description'),
                'MusicUrl' => $this->get('url'),
                'HQMusicUrl' => $this->get('hq_url'),
            ],
        ];
        if ($thumbMediaId = $this->get('thumb_media_id')) {
            $music['Music']['ThumbMediaId'] = $thumbMediaId;
        }

        return $music;
    }
}
