<?php

namespace EasyWeChat\Kernel\HttpClient;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as ResponseInterfaceAlias;

trait HttpClientMethods
{
    /**
     * @param  array<string, mixed>  $options
     *
     * @throws TransportExceptionInterface
     */
    public function get(string $url, array $options = []): Response|ResponseInterfaceAlias
    {
        return $this->request('GET', $url, RequestUtil::formatOptions($options, 'GET'));
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws TransportExceptionInterface
     */
    public function post(string $url, array $options = []): Response|ResponseInterfaceAlias
    {
        return $this->request('POST', $url, RequestUtil::formatOptions($options, 'POST'));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function postJson(string $url, array $data = [], array $options = []): Response|ResponseInterfaceAlias
    {
        $options['headers']['Content-Type'] = 'application/json';

        $options['json'] = $data;

        return $this->request('POST', $url, RequestUtil::formatOptions($options, 'POST'));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function postXml(string $url, array $data = [], array $options = []): Response|ResponseInterfaceAlias
    {
        $options['headers']['Content-Type'] = 'text/xml';

        if (array_key_exists('xml', $data)) {
            $data = $data['xml'];
        }

        $options['xml'] = $data;

        return $this->request('POST', $url, RequestUtil::formatOptions($options, 'POST'));
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws TransportExceptionInterface
     */
    public function patch(string $url, array $options = []): Response|ResponseInterfaceAlias
    {
        return $this->request('PATCH', $url, RequestUtil::formatOptions($options, 'PATCH'));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function patchJson(string $url, array $options = []): Response|ResponseInterfaceAlias
    {
        $options['headers']['Content-Type'] = 'application/json';

        return $this->request('PATCH', $url, RequestUtil::formatOptions($options, 'PATCH'));
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws TransportExceptionInterface
     */
    public function put(string $url, array $options = []): Response|ResponseInterfaceAlias
    {
        return $this->request('PUT', $url, RequestUtil::formatOptions($options, 'PUT'));
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws TransportExceptionInterface
     */
    public function delete(string $url, array $options = []): Response|ResponseInterfaceAlias
    {
        return $this->request('DELETE', $url, RequestUtil::formatOptions($options, 'DELETE'));
    }
}
