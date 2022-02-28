<?php

namespace EasyWeChat\Kernel\Traits;

trait HttpClientMethods
{
    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function get(string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->request('GET', $url, $options);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function post(string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (!\array_key_exists('body', $options) && !\array_key_exists('json', $options)) {
            $options['body'] = $options;
        }

        return $this->request('POST', $url, $options);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function patch(string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (!\array_key_exists('body', $options) && !\array_key_exists('json', $options)) {
            $options['body'] = $options;
        }

        return $this->request('PATCH', $url, $options);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function put(string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (!\array_key_exists('body', $options) && !\array_key_exists('json', $options)) {
            $options['body'] = $options;
        }

        return $this->request('PUT', $url, $options);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function delete(string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->request('DELETE', $url, $options);
    }
}
