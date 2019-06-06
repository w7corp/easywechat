<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MicroMerchant\Kernel;

use EasyWeChat\Kernel\Support;
use EasyWeChat\Kernel\Traits\HasHttpRequests;
use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\MicroMerchant\Kernel\Exceptions\EncryptException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;

/**
 * Class BaseClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class BaseClient
{
    use HasHttpRequests {
        request as performRequest;
    }

    /**
     * @var string
     */
    protected $microCertificates;

    /**
     * @var string
     */
    protected $certificates;

    /**
     * @var \EasyWeChat\MicroMerchant\Application
     */
    protected $app;

    /**
     * BaseClient constructor.
     *
     * @param \EasyWeChat\MicroMerchant\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->microCertificates = $this->app['config']->mch_id.'_micro_certificates';

        $this->setHttpClient($this->app['http_client']);
    }

    /**
     * Extra request params.
     *
     * @return array
     */
    protected function prepends()
    {
        return [];
    }

    /**
     * request.
     *
     * @param string $endpoint
     * @param array  $params
     * @param string $method
     * @param array  $options
     * @param bool   $returnResponse
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    protected function request(string $endpoint, array $params = [], $method = 'post', array $options = [], $returnResponse = false)
    {
        $base = [
            'mch_id' => $this->app['config']['mch_id'],
        ];

        $params = array_filter(array_merge($base, $this->prepends(), $params));

        $secretKey = $this->app->getKey();
        if ('HMAC-SHA256' === ($params['sign_type'] ?? 'MD5')) {
            $encryptMethod = function ($str) use ($secretKey) {
                return hash_hmac('sha256', $str, $secretKey);
            };
        } else {
            $encryptMethod = 'md5';
        }
        $params['sign'] = Support\generate_sign($params, $secretKey, $encryptMethod);

        $options = array_merge([
            'body' => Support\XML::build($params),
        ], $options);

        $this->pushMiddleware($this->logMiddleware(), 'log');

        $response = $this->performRequest($endpoint, $method, $options);
        $response = $returnResponse ? $response : $this->castResponseToType($response, $this->app->config->get('response_type'));
        // auto verify signature
        if (!$returnResponse && 'array' === ($this->app->config->get('response_type') ?? 'array')) {
            $this->app->verifySignature($response);
        }

        return $response;
    }

    /**
     * Log the request.
     *
     * @return \Closure
     */
    protected function logMiddleware()
    {
        $formatter = new MessageFormatter($this->app['config']['http.log_template'] ?? MessageFormatter::DEBUG);

        return Middleware::log($this->app['logger'], $formatter);
    }

    /**
     * Make a request and return raw response.
     *
     * @param          $endpoint
     * @param array    $params
     * @param string   $method
     * @param array    $options
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    protected function requestRaw($endpoint, array $params = [], $method = 'post', array $options = [])
    {
        return $this->request($endpoint, $params, $method, $options, true);
    }

    /**
     * Request with SSL.
     *
     * @param          $endpoint
     * @param array    $params
     * @param string   $method
     * @param array    $options
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    protected function safeRequest($endpoint, array $params, $method = 'post', array $options = [])
    {
        $options = array_merge([
            'cert' => $this->app['config']->get('cert_path'),
            'ssl_key' => $this->app['config']->get('key_path'),
        ], $options);

        return $this->request($endpoint, $params, $method, $options);
    }

    /**
     * processing parameters contain fields that require sensitive information encryption.
     *
     * @param array $params
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\EncryptException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidExtensionException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function processParams(array $params)
    {
        $cert = $this->app->getCertficates();
        $this->certificates = $cert['certificates'];
        $params['cert_sn'] = $cert['serial_no'];
        $sensitive_fields = $this->getSensitiveFieldsName();
        foreach ($params as $k => $v) {
            if (in_array($k, $sensitive_fields, true)) {
                $params[$k] = $this->encryptSensitiveInformation($v);
            }
        }

        return $params;
    }

    /**
     * To id card, mobile phone number and other fields sensitive information encryption.
     *
     * @param string $string
     *
     * @return string
     *
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\EncryptException
     */
    protected function encryptSensitiveInformation(string $string)
    {
        $encrypted = '';
        $publicKeyResource = openssl_get_publickey($this->certificates);
        $f = openssl_public_encrypt($string, $encrypted, $publicKeyResource);
        openssl_free_key($publicKeyResource);
        if ($f) {
            return base64_encode($encrypted);
        }
        
        throw new EncryptException('Encryption of sensitive information failed');
    }

    /**
     * get sensitive fields name.
     *
     * @return array
     */
    protected function getSensitiveFieldsName()
    {
        return [
            'id_card_name',
            'id_card_number',
            'account_name',
            'account_number',
            'contact',
            'contact_phone',
            'contact_email',
            'legal_person',
            'mobile_phone',
            'email',
        ];
    }
}
