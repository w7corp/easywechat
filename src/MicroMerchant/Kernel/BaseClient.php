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

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support;
use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\MicroMerchant\Kernel\Exceptions\EncryptException;
use EasyWeChat\Payment\Kernel\BaseClient as PaymentBaseClient;

/**
 * Class BaseClient.
 *
 * @author   liuml  <liumenglei0211@163.com>
 * @DateTime 2019-07-10  12:06
 */
class BaseClient extends PaymentBaseClient
{
    /**
     * @var string
     */
    protected $certificates;

    /**
     * BaseClient constructor.
     *
     * @param \EasyWeChat\MicroMerchant\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

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
     * httpUpload.
     *
     * @param string $url
     * @param array  $files
     * @param array  $form
     * @param array  $query
     * @param bool   $returnResponse
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    public function httpUpload(string $url, array $files = [], array $form = [], array $query = [], $returnResponse = false)
    {
        $multipart = [];

        foreach ($files as $name => $path) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($path, 'r'),
            ];
        }

        $base = [
            'mch_id' => $this->app['config']['mch_id'],
        ];

        $form = array_merge($base, $form);

        $form['sign'] = $this->getSign($form);

        foreach ($form as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }

        $options = [
            'query' => $query,
            'multipart' => $multipart,
            'connect_timeout' => 30,
            'timeout' => 30,
            'read_timeout' => 30,
            'cert' => $this->app['config']->get('cert_path'),
            'ssl_key' => $this->app['config']->get('key_path'),
        ];

        $this->pushMiddleware($this->logMiddleware(), 'log');

        $response = $this->performRequest($url, 'POST', $options);

        $result = $returnResponse ? $response : $this->castResponseToType($response, $this->app->config->get('response_type'));
        // auto verify signature
        if ($returnResponse || 'array' !== ($this->app->config->get('response_type') ?? 'array')) {
            $this->app->verifySignature($this->castResponseToType($response, 'array'));
        } else {
            $this->app->verifySignature($result);
        }

        return $result;
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

        $params = array_merge($base, $this->prepends(), $params);
        $params['sign'] = $this->getSign($params);
        $options = array_merge([
            'body' => Support\XML::build($params),
        ], $options);

        $this->pushMiddleware($this->logMiddleware(), 'log');
        $response = $this->performRequest($endpoint, $method, $options);
        $result = $returnResponse ? $response : $this->castResponseToType($response, $this->app->config->get('response_type'));
        // auto verify signature
        if ($returnResponse || 'array' !== ($this->app->config->get('response_type') ?? 'array')) {
            $this->app->verifySignature($this->castResponseToType($response, 'array'));
        } else {
            $this->app->verifySignature($result);
        }

        return $result;
    }

    /**
     * processing parameters contain fields that require sensitive information encryption.
     *
     * @param array $params
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\EncryptException
     */
    protected function processParams(array $params)
    {
        $serial_no = $this->app['config']->get('serial_no');
        if (null === $serial_no) {
            throw new InvalidArgumentException('config serial_no connot be empty.');
        }

        $params['cert_sn'] = $serial_no;
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
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\EncryptException
     */
    protected function encryptSensitiveInformation(string $string)
    {
        $certificates = $this->app['config']->get('certificate');
        if (null === $certificates) {
            throw new InvalidArgumentException('config certificate connot be empty.');
        }

        $encrypted = '';
        $publicKeyResource = openssl_get_publickey($certificates);
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

    /**
     * getSign.
     *
     * @param $params
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function getSign($params)
    {
        $params = array_filter($params);

        $key = $this->app->getKey();
        if ('HMAC-SHA256' === ($params['sign_type'] ?? 'MD5')) {
            $encryptMethod = function ($str) use ($key) {
                return hash_hmac('sha256', $str, $key);
            };
        } else {
            $encryptMethod = 'md5';
        }

        return Support\generate_sign($params, $key, $encryptMethod);
    }
}
