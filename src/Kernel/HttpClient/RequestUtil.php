<?php

namespace EasyWeChat\Kernel\HttpClient;

use const ARRAY_FILTER_USE_KEY;
use function array_key_exists;
use EasyWeChat\Kernel\Support\UserAgent;
use EasyWeChat\Kernel\Support\Xml;
use function in_array;
use InvalidArgumentException;
use function is_array;
use function is_string;
use JetBrains\PhpStorm\ArrayShape;
use function json_encode;
use const JSON_FORCE_OBJECT;
use const JSON_UNESCAPED_UNICODE;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestUtil
{
    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    #[ArrayShape([
        'status_codes' => 'array',
        'delay' => 'int',
        'max_delay' => 'int',
        'max_retries' => 'int',
        'multiplier' => 'float',
        'jitter' => 'float',
    ])]
    public static function mergeDefaultRetryOptions(array $options): array
    {
        return \array_merge([
            'status_codes' => GenericRetryStrategy::DEFAULT_RETRY_STATUS_CODES,
            'delay' => 1000,
            'max_delay' => 0,
            'max_retries' => 3,
            'multiplier' => 2.0,
            'jitter' => 0.1,
        ], $options);
    }

    /**
     * @param  array<string, array|mixed>  $options
     * @return array<string, array|mixed>
     */
    public static function formatDefaultOptions(array $options): array
    {
        $defaultOptions = \array_filter(
            array: $options,
            callback: fn ($key) => array_key_exists($key, HttpClientInterface::OPTIONS_DEFAULTS),
            mode: ARRAY_FILTER_USE_KEY
        );

        /** @phpstan-ignore-next-line */
        if (! isset($options['headers']['User-Agent']) && ! isset($options['headers']['user-agent'])) {
            /** @phpstan-ignore-next-line */
            $defaultOptions['headers']['User-Agent'] = UserAgent::create();
        }

        return $defaultOptions;
    }

    public static function formatOptions(array $options, string $method): array
    {
        if (array_key_exists('query', $options) && is_array($options['query']) && empty($options['query'])) {
            return $options;
        }

        if (array_key_exists('body', $options)
            || array_key_exists('json', $options)
            || array_key_exists('xml', $options)
        ) {
            return $options;
        }

        $name = in_array($method, ['GET', 'HEAD', 'DELETE']) ? 'query' : 'body';

        if (($options['headers']['Content-Type'] ?? $options['headers']['content-type'] ?? null) === 'application/json') {
            $name = 'json';
        }

        foreach ($options as $key => $value) {
            if (! array_key_exists($key, HttpClientInterface::OPTIONS_DEFAULTS)) {
                $options[$name][trim($key, '"')] = $value;
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * @param  array<string, array<string,mixed>|mixed>  $options
     * @return array<string, array|mixed>
     */
    public static function formatBody(array $options): array
    {
        if (isset($options['xml'])) {
            if (is_array($options['xml'])) {
                $options['xml'] = Xml::build($options['xml']);
            }

            if (! is_string($options['xml'])) {
                throw new InvalidArgumentException('The type of `xml` must be string or array.');
            }

            /** @phpstan-ignore-next-line */
            if (! isset($options['headers']['Content-Type']) && ! isset($options['headers']['content-type'])) {
                /** @phpstan-ignore-next-line */
                $options['headers']['Content-Type'] = [$options['headers'][] = 'Content-Type: text/xml'];
            }

            $options['body'] = $options['xml'];
            unset($options['xml']);
        }

        if (isset($options['json'])) {
            if (is_array($options['json'])) {
                /** XXX: 微信的 JSON 是比较奇葩的，比如菜单不能把中文 encode 为 unicode */
                $options['json'] = json_encode(
                    $options['json'],
                    empty($options['json']) ? JSON_FORCE_OBJECT : JSON_UNESCAPED_UNICODE
                );
            }

            if (! is_string($options['json'])) {
                throw new InvalidArgumentException('The type of `json` must be string or array.');
            }

            /** @phpstan-ignore-next-line */
            if (! isset($options['headers']['Content-Type']) && ! isset($options['headers']['content-type'])) {
                /** @phpstan-ignore-next-line */
                $options['headers']['Content-Type'] = [$options['headers'][] = 'Content-Type: application/json'];
            }

            $options['body'] = $options['json'];
            unset($options['json']);
        }

        return $options;
    }

    public static function createDefaultServerRequest(): ServerRequestInterface
    {
        $psr17Factory = new Psr17Factory();

        $creator = new ServerRequestCreator(
            serverRequestFactory: $psr17Factory,
            uriFactory: $psr17Factory,
            uploadedFileFactory: $psr17Factory,
            streamFactory: $psr17Factory
        );

        return $creator->fromGlobals();
    }
}
