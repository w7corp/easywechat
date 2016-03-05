<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Media.php.
 *
 * Part of Overtrue\Wechat.
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

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Arr;
use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Utils\File;
use Overtrue\Wechat\Utils\JSON;

/**
 * 媒体素材.
 *
 * @method string image($path)
 * @method string voice($path)
 * @method string thumb($path)
 */
class Media
{
    const API_TEMPORARY_UPLOAD = 'http://file.api.weixin.qq.com/cgi-bin/media/upload';
    const API_FOREVER_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/material/add_material';
    const API_TEMPORARY_GET = 'https://api.weixin.qq.com/cgi-bin/media/get';
    const API_FOREVER_GET = 'https://api.weixin.qq.com/cgi-bin/material/get_material';
    const API_FOREVER_NEWS_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/material/add_news';
    const API_FOREVER_NEWS_UPDATE = 'https://api.weixin.qq.com/cgi-bin/material/update_news';
    const API_FOREVER_DELETE = 'https://api.weixin.qq.com/cgi-bin/material/del_material';
    const API_FOREVER_COUNT = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount';
    const API_FOREVER_LIST = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material';

    /**
     * 允许上传的类型.
     *
     * @var array
     */
    protected $allowTypes = array(
                             'image',
                             'voice',
                             'video',
                             'thumb',
                             'news',
                            );

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * 是否上传永久素材.
     *
     * @var bool
     */
    protected $forever = false;

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 是否为永久素材.
     *
     * @return Media
     */
    public function forever()
    {
        $this->forever = true;

        return $this;
    }

    /**
     * 上传媒体文件.
     *
     * @param string $type
     * @param string $path
     * @param array  $params
     *
     * @return string
     */
    protected function upload($type, $path, $params = array())
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new Exception("文件不存在或不可读 '$path'");
        }

        if (!in_array($type, $this->allowTypes, true)) {
            throw new Exception("错误的媒体类型 '{$type}'");
        }

        $queries = array('type' => $type);

        $options = array(
                    'files' => array('media' => $path),
                   );

        $url = $this->getUrl($type, $queries);

        $response = $this->http->post($url, $params, $options);

        $this->forever = false;

        if ($type == 'image') {
            return $response;
        }

        $response = Arr::only($response, array('media_id', 'thumb_media_id'));

        return array_pop($response);
    }

    /**
     * 上传视频.
     *
     * 有点不一样。。。
     *
     * @param string $path
     * @param string $title
     * @param string $description
     *
     * @return string
     */
    public function video($path, $title, $description)
    {
        $params = array(
                   'description' => JSON::encode(
                       array(
                        'title' => $title,
                        'introduction' => $description,
                       )
                   ),
                  );

        return $this->upload('video', $path, $params);
    }

    /**
     * 新增图文素材.
     *
     * @param array $articles
     *
     * @return string
     */
    public function news(array $articles)
    {
        $params = array('articles' => $articles);

        $response = $this->http->jsonPost(self::API_FOREVER_NEWS_UPLOAD, $params);

        return $response['media_id'];
    }

    /**
     * 修改图文消息.
     *
     * @param string $mediaId
     * @param array  $article
     * @param int    $index
     *
     * @return bool
     */
    public function updateNews($mediaId, $article, $index = 0)
    {
        $params = array(
                   'media_id' => $mediaId,
                   'index' => $index,
                   'articles' => isset($article['title']) ? $article : (isset($article[$index]) ? $article[$index] : array()),
                  );

        return $this->http->jsonPost(self::API_FOREVER_NEWS_UPDATE, $params);
    }

    /**
     * 删除永久素材.
     *
     * @param string $mediaId
     *
     * @return bool
     */
    public function delete($mediaId)
    {
        return $this->http->jsonPost(self::API_FOREVER_DELETE, array('media_id' => $mediaId));
    }

    /**
     * 图片素材总数.
     *
     * @param string $type
     *
     * @return array|int
     */
    public function stats($type = null)
    {
        $response = $this->http->get(self::API_FOREVER_COUNT);

        $response['voice'] = $response['voice_count'];
        $response['image'] = $response['image_count'];
        $response['video'] = $response['video_count'];
        $response['news'] = $response['news_count'];

        $response = new Bag($response);

        return $type ? $response->get($type) : $response;
    }

    /**
     * 获取永久素材列表.
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
     *         //可能会有多个素材
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
        $params = array(
                   'type' => $type,
                   'offset' => intval($offset),
                   'count' => min(20, $count),
                  );

        return $this->http->jsonPost(self::API_FOREVER_LIST, $params);
    }

    /**
     * 下载媒体文件.
     *
     * @param string $mediaId
     * @param string $filename
     *
     * @return mixed
     */
    public function download($mediaId, $filename = '')
    {
        $params = array('media_id' => $mediaId);

        $method = $this->forever ? 'jsonPost' : 'get';
        $api = $this->forever ? self::API_FOREVER_GET : self::API_TEMPORARY_GET;

        $contents = $this->http->{$method}($api, $params);

        $filename = $filename ? $filename : $mediaId;

        if (!is_array($contents)) {
            $ext = File::getStreamExt($contents);

            file_put_contents($filename.$ext, $contents);

            return $filename.$ext;
        } else {
            return $contents;
        }
    }

    /**
     * 魔术调用.
     *
     * <pre>
     * $media->image($path); // $media->upload('image', $path);
     * </pre>
     *
     * @param string $method
     * @param array  $args
     *
     * @return string
     */
    public function __call($method, $args)
    {
        $args = array(
                 $method,
                 array_shift($args),
                );

        return call_user_func_array(array(__CLASS__, 'upload'), $args);
    }

    /**
     * 获取API.
     *
     * @param string $type
     * @param array  $queries
     *
     * @return string
     */
    protected function getUrl($type, $queries = array())
    {
        if ($type === 'news') {
            $api = self::API_FOREVER_NEWS_UPLOAD;
        } else {
            $api = $this->forever ? self::API_FOREVER_UPLOAD : self::API_TEMPORARY_UPLOAD;
        }

        return $api.'?'.http_build_query($queries);
    }
}
