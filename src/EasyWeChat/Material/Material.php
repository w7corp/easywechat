<?php

/**
 * Material.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Material;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Http;

/**
 * Class Material.
 */
class Material
{
    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    /**
     * Allow media type.
     *
     * @var array
     */
    protected $allowTypes = ['image', 'voice', 'video', 'thumb', 'news_image'];

    const API_GET = 'https://api.weixin.qq.com/cgi-bin/material/get_material';
    const API_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/material/add_material';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/material/del_material';
    const API_STATS = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount';
    const API_LISTS = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material';
    const API_NEWS_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/material/add_news';
    const API_NEWS_UPDATE = 'https://api.weixin.qq.com/cgi-bin/material/update_news';
    const API_NEWS_IMAGE_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg';

    /**
     * Constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http->setExpectedException('EasyWeChat\Material\MaterialHttpException');
    }

    /**
     * Upload image.
     *
     * @param string $path
     *
     * @return string
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
     * @return string
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
     * @return string
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
     * @return string
     */
    public function uploadVideo($path, $title, $description)
    {
        $params = [
            'description' => json_encode(
                [
                    'title' => $title,
                    'introduction' => $description,
                ]
            ),
        ];

        return $this->uploadMedia('video', $path, $params);
    }

    /**
     * Upload articles.
     *
     * @param array $articles
     *
     * @return string
     */
    public function uploadArticle(array $articles)
    {
        $params = ['articles' => $articles];

        $response = $this->http->json(self::API_NEWS_UPLOAD, $params);

        return $response['media_id'];
    }

    /**
     * Upload image for article.
     *
     * @param string $path
     *
     * @return string
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
        return $this->http->json(self::API_GET, ['media_id' => $mediaId]);
    }

    /**
     * Delete material by media ID.
     *
     * @param string $mediaId
     *
     * @return bool
     */
    public function delete($mediaId)
    {
        return $this->http->json(self::API_DELETE, ['media_id' => $mediaId]);
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
     * @return array
     */
    public function lists($type, $offset = 0, $count = 20)
    {
        $params = [
            'type' => $type,
            'offset' => intval($offset),
            'count' => min(20, $count),
        ];

        return $this->http->json(self::API_LISTS, $params);
    }

    /**
     * Get stats of materials.
     *
     * @return array
     */
    public function stats()
    {
        return $this->http->get(self::API_STATS);
    }

    /**
     * Update article.
     *
     * @param string $mediaId
     * @param array  $article
     * @param int    $index
     *
     * @return bool
     */
    public function updateArticle($mediaId, $article, $index = 0)
    {
        $params = [
            'media_id' => $mediaId,
            'index' => $index,
            'articles' => isset($article['title']) ? $article : (isset($article[$index]) ? $article[$index] : []),
        ];

        return $this->http->json(self::API_NEWS_UPDATE, $params);
    }

    /**
     * Upload material.
     *
     * @param string $type
     * @param string $path
     * @param array  $form
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function uploadMedia($type, $path, array $form = [])
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }

        $form = array_merge($form, ['type' => $type]);

        return $this->http->upload($this->getApi($type), ['media' => $path], $form);
    }

    /**
     * 获取API.
     *
     * @param string $type
     *
     * @return string
     */
    protected function getApi($type)
    {
        switch ($type) {
            case 'news_image':
                $api = self::API_NEWS_IMAGE_UPLOAD;
                break;
            default:
                $api = self::API_UPLOAD;
        }

        return $api;
    }
}
