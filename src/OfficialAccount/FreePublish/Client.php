<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\FreePublish;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Messages\Article;

/**
 * Class Client.
 *
 * @author wangdongzhao <elim051@163.com>
 */
class Client extends BaseClient
{
    /**
     * Get publish status.
     * @param string $publishId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $publishId)
    {
        return $this->httpPostJson('cgi-bin/freepublish/get', ['publish_id' => $publishId]);
    }

    /**
     * Submit article.
     * @param string $mediaId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function submit(string $mediaId)
    {
        return $this->httpPostJson('cgi-bin/freepublish/submit', ['media_id' => $mediaId]);
    }

    /**
     * Get article.
     * @param string $articleId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getArticle(string $articleId)
    {
        return $this->httpPostJson('cgi-bin/freepublish/getarticle', ['article_id' => $articleId]);
    }

    /**
     * Batch get articles.
     * @param int $offset
     * @param int $count
     * @param int $noContent
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function batchGet(int $offset = 0, int $count = 20, int $noContent = 0)
    {
        $params = [
            'offset' => $offset,
            'count' => $count,
            'no_content' => $noContent
        ];
        return $this->httpPostJson('cgi-bin/freepublish/batchget', $params);
    }

    /**
     * Delete article
     * @param string $articleId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $articleId)
    {
        return $this->httpPostJson('cgi-bin/freepublish/delete', ['article_id' => $articleId]);
    }
}
