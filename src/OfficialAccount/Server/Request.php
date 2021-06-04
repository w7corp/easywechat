<?php

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\OfficialAccount\Contracts\Message as MessageInterface;
use EasyWeChat\OfficialAccount\Contracts\Request as RequestInterface;

class Request extends \Nyholm\Psr7\Request implements RequestInterface
{
    protected array $query;

    public function __construct(string $method, $uri, array $headers = [], $body = null, string $version = '1.1')
    {
        parent::__construct($method, $uri, $headers, $body, $version);

        \parse_str($this->getUri()->getQuery(), $this->query);
    }

    public function isSafeMode(): bool
    {
        return isset($this->query['echostr']) && isset($this->query['signature']);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public function validate(string $token): bool
    {
        if (
            $this->query['signature'] !== $this->signature(
                [
                    $token,
                    $this->query['timestamp'],
                    $this->query['nonce'],
                ]
            )
        ) {
            throw new BadRequestException('Invalid request signature.', 400);
        }

        return true;
    }

    public function getMessage(): MessageInterface
    {
        $content = $this->getBody()->getContents();

        $attributes = [];
        // todo: 解密并获取消息内容返回消息实体
        return new Message($attributes);
    }

    protected static function parse(string $content): ?array
    {
        if (0 === stripos($content, '<')) {
            return XML::parse($content);
        }

        // Handle JSON format.
        $dataSet = json_decode($content, true);

        if (
            JSON_ERROR_NONE === json_last_error()
            &&
            $content
        ) {
            $content = $dataSet;
        }

        return $content ?? null;
    }

    public static function signature(array $params): string
    {
        sort($params, SORT_STRING);

        return sha1(implode($params));
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function decrypt(string $ciphertext): string
    {
        return
            $this->encryptor->decrypt(
                $ciphertext,
                $this->get('msg_signature'),
                $this->get('nonce'),
                $this->get('timestamp')
            );
    }

    public function isValidation(): bool
    {
        // TODO: Implement isValidation() method.
    }
}
