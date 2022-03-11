<?php

namespace EasyWeChat\Kernel\HttpClient;

use EasyWeChat\Kernel\Support\UserAgent;
use EasyWeChat\Kernel\Support\Xml;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestUtil
{
    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    public static function mergeDefaultRetryOptions(array $options): array
    {
        return \array_merge([
            'status_codes' => GenericRetryStrategy::DEFAULT_RETRY_STATUS_CODES,
            'delay' => 1000,
            'max_delay' => 0,
            'max_retries' => 2,
            'multiplier' => 2.0,
            'jitter' => 0.1,
        ], $options);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    public static function formatDefaultOptions(array $options): array
    {
        $defaultOptions = \array_filter(
            array: $options,
            callback: fn ($key) => \array_key_exists($key, HttpClientInterface::OPTIONS_DEFAULTS),
            mode: \ARRAY_FILTER_USE_KEY
        );

        if (!isset($options['headers']['User-Agent']) && !isset($options['headers']['user-agent'])) {
            $defaultOptions['headers']['User-Agent'] = UserAgent::create();
        }

        return $defaultOptions;
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public static function formatBody(array $options): array
    {
        if (isset($options['xml'])) {
            if (is_array($options['xml'])) {
                $options['xml'] = Xml::build($options['xml']);
            }

            if (!\is_string($options['xml'])) {
                throw new \InvalidArgumentException('The type of `xml` must be string or array.');
            }

            if (!isset($options['headers']['Content-Type']) && !isset($options['headers']['content-type'])) {
                $options['headers']['Content-Type'] = [$options['headers'][] = 'Content-Type: text/xml'];
            }

            $options['body'] = $options['xml'];
            unset($options['xml']);
        }

        if (isset($options['json'])) {
            if (is_array($options['json'])) {
                /** XXX: 微信的 JSON 是比较奇葩的，比如菜单不能把中文 encode 为 unicode */
                $options['json'] = \json_encode($options['json'], empty($options['json']) ? \JSON_FORCE_OBJECT : \JSON_UNESCAPED_UNICODE);
            }

            if (!\is_string($options['json'])) {
                throw new \InvalidArgumentException('The type of `json` must be string or array.');
            }

            if (!isset($options['headers']['Content-Type']) && !isset($options['headers']['content-type'])) {
                $options['headers']['Content-Type'] = [$options['headers'][] = 'Content-Type: application/json'];
            }

            $options['body'] = $options['json'];
            unset($options['json']);
        }

        return $options;
    }
}
