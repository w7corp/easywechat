<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MicroMerchant\Certficates;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Traits\InteractsWithCache;
use EasyWeChat\MicroMerchant\Kernel\BaseClient;
use EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidExtensionException;

/**
 * Class Client.
 *
 * @author   liuml  <liumenglei0211@163.com>
 * @DateTime 2019-05-30  14:19
 */
class Client extends BaseClient
{
    use InteractsWithCache;

    /**
     * get certficates.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidExtensionException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get()
    {
        $certificates = $this->getCache()->get($this->microCertificates);
        if ($certificates && strtotime($certificates['expire_time']) > time()) {
            return $certificates;
        }

        return $this->refresh();
    }

    /**
     * download certficates.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidExtensionException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function download()
    {
        $params = [
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
        ];

        $responseType = $this->app->config->get('response_type');
        $this->app->config->set('response_type', 'array');
        $response = $this->request('risk/getcertficates', $params);
        $this->app->config->set('response_type', $responseType);

        return $this->analytical($response);
    }

    /**
     * analytical certificate.
     *
     * @param $data
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidExtensionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function analytical($data)
    {
        if ('SUCCESS' !== $data['return_code']) {
            throw new InvalidArgumentException(
                sprintf(
                    'Failed to download certificate. return_code_msg: "%s" .',
                    $data['return_code'].'('.$data['return_msg'].')'
                )
            );
        }
        if ('SUCCESS' !== $data['result_code']) {
            throw new InvalidArgumentException(
                sprintf(
                    'Failed to download certificate. result_err_code_des: "%s" .',
                    $data['result_code'].'('.$data['err_code'].'['.$data['err_code_des'].'])'
                )
            );
        }
        $certificates = \GuzzleHttp\json_decode($data['certificates'], JSON_UNESCAPED_UNICODE)['data'][0];
        $ciphertext = $this->decrypt($certificates['encrypt_certificate']);
        unset($certificates['encrypt_certificate']);
        $certificates['certificates'] = $ciphertext;
        $this->getCache()->set($this->microCertificates, $certificates);

        return $certificates;
    }

    /**
     * decrypt ciphertext.
     *
     * @param $encryptCertificate
     *
     * @return string
     *
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidExtensionException
     */
    protected function decrypt($encryptCertificate)
    {
        if (false === extension_loaded('sodium')) {
            throw new InvalidExtensionException('sodium extension is not installed，Reference link https://blog.csdn.net/u010324331/article/details/82153067');
        }

        if (false === sodium_crypto_aead_aes256gcm_is_available()) {
            throw new InvalidExtensionException('aes256gcm is not currently supported');
        }

        // sodium_crypto_aead_aes256gcm_decrypt function needs to open libsodium extension.
        // https://blog.csdn.net/u010324331/article/details/82153067
        return sodium_crypto_aead_aes256gcm_decrypt(
            base64_decode($encryptCertificate['ciphertext'], true),
            $encryptCertificate['associated_data'],
            $encryptCertificate['nonce'],
            $this->app['config']->apiv3_key
        );
    }

    /**
     * refresh certificate.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidExtensionException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function refresh()
    {
        return $this->download();
    }
}
