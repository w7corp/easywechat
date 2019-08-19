<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Http;

use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Kernel\Support\XML;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface;
use Safe\Exceptions\JsonException;

/**
 * Class Response.
 *
 * @author overtrue <i@overtrue.me>
 */
class Response extends GuzzleResponse
{
    /**
     * @return string
     */
    public function getBodyContents()
    {
        $this->getBody()->rewind();
        $contents = $this->getBody()->getContents();
        $this->getBody()->rewind();

        return $contents;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \EasyWeChat\Kernel\Http\Response
     */
    public static function buildFromPsrResponse(ResponseInterface $response)
    {
        return new static(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    /**
     * Build to json.
     *
     * @return string
     *
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     */
    public function toJson()
    {
        return \Safe\json_encode($this->toArray());
    }

    /**
     * Build to array.
     *
     * @return array
     *
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     */
    public function toArray()
    {
        $content = $this->removeControlCharacters($this->getBodyContents());

        if (false !== stripos($this->getHeaderLine('Content-Type'), 'xml') || 0 === stripos($content, '<xml')) {
            return XML::parse($content);
        }

        try {
            return \Safe\json_decode($content, true, 512, JSON_BIGINT_AS_STRING);
        } catch (JsonException $exception) {
            return [];
        }
    }

    /**
     * Get collection data.
     *
     * @return \EasyWeChat\Kernel\Support\Collection
     *
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     */
    public function toCollection()
    {
        return new Collection($this->toArray());
    }

    /**
     * @return object
     *
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     */
    public function toObject()
    {
        return \Safe\json_decode($this->toJson());
    }

    /**
     * @return bool|string
     */
    public function __toString()
    {
        return $this->getBodyContents();
    }

    /**
     * @param string $content
     *
     * @return string
     *
     * @throws \Safe\Exceptions\PcreException
     */
    protected function removeControlCharacters(string $content)
    {
        return \Safe\preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $content);
    }
}
