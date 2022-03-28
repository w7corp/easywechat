<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Draft;

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
     * Add new articles to the draft.
     * @param array $articles
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(array $articles)
    {
        return $this->httpPostJson('cgi-bin/draft/add', $articles);
    }

    /**
     * Get article from the draft.
     * @param string $mediaId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $mediaId)
    {
        return $this->httpPostJson('cgi-bin/draft/get', ['media_id' => $mediaId]);
    }

    /**
     * Update article
     * @param string $mediaId
     * @param int $index
     * @param mixed $article
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $mediaId, int $index, $article)
    {
        $params = [
            'media_id' => $mediaId,
            'index' => $index,
            'articles' => $article
        ];
        return $this->httpPostJson('cgi-bin/draft/update', $params);
    }

    /**
     * Get draft total count
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function count()
    {
        return $this->httpPostJson('cgi-bin/draft/count');
    }

    /**
     * Batch get articles from the draft.
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
        return $this->httpPostJson('cgi-bin/draft/batchget', $params);
    }

    /**
     * Delete article.
     * @param string $mediaId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $mediaId)
    {
        return $this->httpPostJson('cgi-bin/draft/delete', ['media_id' => $mediaId]);
    }
}
