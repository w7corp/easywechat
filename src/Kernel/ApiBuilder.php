<?php

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Support\Str;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiBuilder
{
    public function __construct(protected HttpClientInterface $client, protected string $uri = '/')
    {
    }

    public function append(string $segment): ApiBuilder
    {
        $segment = Str::kebab($segment);

        $uri = \sprintf('/%s/%s', \trim($this->uri, '/'), \trim($segment, '/'));

        return new ApiBuilder($this->client, $uri);
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function get(array $options = [])
    {
        return $this->client->request('GET', $this->getUri(), $options);
    }

    public function post(array $options = [])
    {
        return $this->client->request('POST', $this->getUri(), $options);
    }

    public function patch(array $options = [])
    {
        return $this->client->request('PATCH', $this->getUri(), $options);
    }

    public function put(array $options = [])
    {
        return $this->client->request('PUT', $this->getUri(), $options);
    }

    public function delete(array $options = [])
    {
        return $this->client->request('DELETE', $this->getUri(), $options);
    }

    public function __get($name)
    {
        return $this->append($name);
    }

    public function __call(string $name, array $arguments)
    {
        return \call_user_func_array([$this->client, $name], $arguments);
    }
}
