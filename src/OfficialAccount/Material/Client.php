<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Material;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Messages\Article;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * Allow media type.
     *
     * @var array
     */
    protected $allowTypes = ['image', 'voice', 'video', 'thumb', 'news_image'];

    /**
     * Upload image.
     *
     * @param string $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function uploadImage($path)
    {
        return $this->uploadMedia('image', $path);
    }

    /**
     * Upload voice.
     *
     * @param string $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function uploadVoice($path)
    {
        return $this->uploadMedia('voice', $path);
    }

    /**
     * Upload thumb.
     *
     * @param string $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function uploadThumb($path)
    {
        return $this->uploadMedia('thumb', $path);
    }

    /**
     * Upload video.
     *
     * @param string $path
     * @param string $title
     * @param string $description
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function uploadVideo($path, $title, $description)
    {
        $params = [
            'description' => json_encode(
                [
                    'title' => $title,
                    'introduction' => $description,
                ], JSON_UNESCAPED_UNICODE),
        ];

        return $this->uploadMedia('video', $path, $params);
    }

    /**
     * Upload articles.
     *
     * @param array|Article $articles
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function uploadArticle($articles)
    {
        if (!empty($articles['title']) || $articles instanceof Article) {
            $articles = [$articles];
        }

        $params = ['articles' => array_map(function ($article) {
            if ($article instanceof Article) {
                return $article->only([
                    'title', 'thumb_media_id', 'author', 'digest',
                    'show_cover_pic', 'content', 'content_source_url',
                    ]);
            }

            return $article;
        }, $articles)];

        return $this->httpPostJson('cgi-bin/material/add_news', $params);
    }

    /**
     * Update article.
     *
     * @param string $mediaId
     * @param array  $article
     * @param int    $index
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function updateArticle($mediaId, $article, $index = 0)
    {
        $params = [
            'media_id' => $mediaId,
            'index' => $index,
            'articles' => isset($article['title']) ? $article : (isset($article[$index]) ? $article[$index] : []),
        ];

        return $this->httpPostJson('cgi-bin/material/update_news', $params);
    }

    /**
     * Upload image for article.
     *
     * @param string $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function uploadArticleImage($path)
    {
        return $this->uploadMedia('news_image', $path);
    }

    /**
     * Fetch material.
     *
     * @param string $mediaId
     *
     * @return mixed
     */
    public function get($mediaId)
    {
        $response = $this->httpGet('cgi-bin/material/get_material', ['media_id' => $mediaId]);

        foreach ($response->getHeader('Content-Type') as $mime) {
            if (preg_match('/(image|video|audio)/i', $mime)) {
                return $response->getBody();
            }
        }

        $json = $this->getHttp()->parseJSON($response);

        // XXX: 微信开发这帮混蛋，尼玛文件二进制输出不带header，简直日了!!!
        if (!$json) {
            return $response->getBody();
        }

        $this->checkAndThrow($json);

        return $json;
    }

    /**
     * Delete material by media ID.
     *
     * @param string $mediaId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete($mediaId)
    {
        return $this->httpPostJson('cgi-bin/material/del_material', ['media_id' => $mediaId]);
    }

    /**
     * List materials.
     *
     * example:
     *
     * {
     *   "total_count": TOTAL_COUNT,
     *   "item_count": ITEM_COUNT,
     *   "item": [{
     *             "media_id": MEDIA_ID,
     *             "name": NAME,
     *             "update_time": UPDATE_TIME
     *         },
     *         // more...
     *   ]
     * }
     *
     * @param string $type
     * @param int    $offset
     * @param int    $count
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function lists($type, $offset = 0, $count = 20)
    {
        $params = [
            'type' => $type,
            'offset' => intval($offset),
            'count' => min(20, $count),
        ];

        return $this->httpPostJson('cgi-bin/material/batchget_material', $params);
    }

    /**
     * Get stats of materials.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function stats()
    {
        return $this->httpGet('cgi-bin/material/get_materialcount');
    }

    /**
     * Upload material.
     *
     * @param string $type
     * @param string $path
     * @param array  $form
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws InvalidArgumentException
     */
    protected function uploadMedia($type, $path, array $form = [])
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }

        $form['type'] = $type;

        return $this->httpUpload($this->getApiByType($type), ['media' => $path], $form);
    }

    /**
     * Get API by type.
     *
     * @param string $type
     *
     * @return string
     */
    public function getApiByType($type)
    {
        switch ($type) {
            case 'news_image':
                return 'cgi-bin/media/uploadimg';
            default:
                return 'cgi-bin/material/add_material';
        }
    }
}
