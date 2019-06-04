<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Support;

/*
 * helpers.
 *
 * @author overtrue <i@overtrue.me>
 */

/**
 * Generate a signature.
 *
 * @param  array   $attributes
 * @param  string  $key
 * @param  string  $encryptMethod
 *
 * @return string
 */
function generate_sign(array $attributes, $key, $encryptMethod = 'md5')
{
    ksort($attributes);

    $attributes['key'] = $key;

    return strtoupper(call_user_func_array($encryptMethod, [urldecode(http_build_query($attributes))]));
}

/**
 * Get client ip.
 *
 * @return string
 */
function get_client_ip()
{
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        // for php-cli(phpunit etc.)
        $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
    }

    return filter_var($ip, FILTER_VALIDATE_IP) ? : '127.0.0.1';
}

/**
 * Get current server ip.
 *
 * @return string
 */
function get_server_ip()
{
    if (!empty($_SERVER['SERVER_ADDR'])) {
        $ip = $_SERVER['SERVER_ADDR'];
    } elseif (!empty($_SERVER['SERVER_NAME'])) {
        $ip = gethostbyname($_SERVER['SERVER_NAME']);
    } else {
        // for php-cli(phpunit etc.)
        $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
    }

    return filter_var($ip, FILTER_VALIDATE_IP) ? : '127.0.0.1';
}

/**
 * Return current url.
 *
 * @return string
 */
function current_url()
{
    $protocol = 'http://';

    if ((!empty($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS'])
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http') === 'https'
    ) {
        $protocol = 'https://';
    }

    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Return random string.
 *
 * @param  string  $length
 *
 * @return string
 */
function str_random($length)
{
    return Str::random($length);
}

/**
 * @param  string  $content
 * @param  string  $publicKey
 *
 * @return string
 */
function rsa_public_encrypt($content, $publicKey)
{
    $encrypted = '';
    openssl_public_encrypt($content, $encrypted, openssl_pkey_get_public($publicKey), OPENSSL_PKCS1_OAEP_PADDING);

    return base64_encode($encrypted);
}

/**
 * verify signature
 *
 * @param $data
 * @param $secretKey
 *
 * @return bool
 *
 * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
 */
function verify_signature($data, $secretKey)
{
    if ($data['return_code'] != 'SUCCESS' || $data['result_code'] != 'SUCCESS') {
        return false;
    }

    $sign = $data['sign'];
    strlen($sign) > 32 && $sign_type = 'HMAC-SHA256';
    unset($data['sign']);


    if ('HMAC-SHA256' === ($sign_type ?? 'MD5')) {
        $encryptMethod = function ($str) use ($secretKey) {
            return hash_hmac('sha256', $str, $secretKey);
        };
    } else {
        $encryptMethod = 'md5';
    }

    if (generate_sign($data, $secretKey, $encryptMethod) == $sign) {
        return true;
    }

    throw new \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException('return value signature verification error');
}