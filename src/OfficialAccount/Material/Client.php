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
use EasyWeChat\Kernel\Http\StreamResponse;
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadImage(string $path)
    {
        return $this->upload('image', $path);
    }

    /**
     * Upload voice.
     *
     * @param string $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadVoice(string $path)
    {
        return $this->upload('voice', $path);
    }

    /**
     * Upload thumb.
     *
     * @param string $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadThumb(string $path)
    {
        return $this->upload('thumb', $path);
    }

    /**
     * Upload video.
     *
     * @param string $path
     * @param string $title
     * @param string $description
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadVideo(string $path, string $title, string $description)
    {
        $params = [
            'description' => json_encode(
                [
                    'title' => $title,
                    'introduction' => $description,
                ],
                JSON_UNESCAPED_UNICODE
            ),
        ];

        return $this->upload('video', $path, $params);
    }

    /**
     * Upload articles.
     *
     * @param array|Article $articles
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadArticle($articles)
    {
        if ($articles instanceof Article || !empty($articles['title'])) {
            $articles = [$articles];
        }

        $params = ['articles' => array_map(function ($article) {
            if ($article instanceof Article) {
                return $article->transformForJsonRequestWithoutType();
            }

            return $article;
        }, $articles)];

        return $this->httpPostJson('cgi-bin/material/add_news', $params);
    }

    /**
     * Update article.
     *
     * @param string        $mediaId
     * @param array|Article $article
     * @param int           $index
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateArticle(string $mediaId, $article, int $index = 0)
    {
        if ($article instanceof Article) {
            $article = $article->transformForJsonRequestWithoutType();
        }

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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function uploadArticleImage(string $path)
    {
        return $this->upload('news_image', $path);
    }

    /**
     * Fetch material.
     *
     * @param string $mediaId
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $mediaId)
    {
        $response = $this->requestRaw('cgi-bin/material/get_material', 'POST', ['json' => ['media_id' => $mediaId]]);

        if (false !== stripos($response->getHeaderLine('Content-disposition'), 'attachment')) {
            return StreamResponse::buildFromPsrResponse($response);
        }

        return $this->castResponseToType($response, $this->app['config']->get('response_type'));
    }

    /**
     * Delete material by media ID.
     *
     * @param string $mediaId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $mediaId)
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(string $type, int $offset = 0, int $count = 20)
    {
        $params = [
            'type' => $type,
            'offset' => $offset,
            'count' => $count,
        ];

        return $this->httpPostJson('cgi-bin/material/batchget_material', $params);
    }

    /**
     * Get stats of materials.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
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
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload(string $type, string $path, array $form = [])
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException(sprintf('File does not exist, or the file is unreadable: "%s"', $path));
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
    public function getApiByType(string $type)
    {
        switch ($type) {
            case 'news_image':
                return 'cgi-bin/media/uploadimg';
            default:
                return 'cgi-bin/material/add_material';
        }
    }
}
