<?php

namespace EasyWeChat\Kernel\HttpClient;

use function array_merge;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Form\File;
use EasyWeChat\Kernel\Form\Form;
use EasyWeChat\Kernel\Support\Str;
use function in_array;
use function is_file;
use function is_string;
use function str_ends_with;
use function str_starts_with;
use function strtoupper;
use function substr;

trait RequestWithPresets
{
    /**
     * @var array<string, string>
     */
    protected array $prependHeaders = [];

    /**
     * @var array<string, mixed>
     */
    protected array $prependParts = [];

    /**
     * @var array<string, mixed>
     */
    protected array $presets = [];

    /**
     * @param  array<string, mixed>  $presets
     */
    public function setPresets(array $presets): static
    {
        $this->presets = $presets;

        return $this;
    }

    public function withHeader(string $key, string $value): static
    {
        $this->prependHeaders[$key] = $value;

        return $this;
    }

    public function withHeaders(array $headers): static
    {
        foreach ($headers as $key => $value) {
            $this->withHeader($key, $value);
        }

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function with(string|array $key, mixed $value = null): static
    {
        if (\is_array($key)) {
            // $client->with(['appid', 'mchid'])
            // $client->with(['appid' => 'wx1234567', 'mchid'])
            foreach ($key as $k => $v) {
                if (\is_int($k) && is_string($v)) {
                    [$k, $v] = [$v, null];
                }

                $this->with($k, $v ?? $this->presets[$k] ?? null);
            }

            return $this;
        }

        $this->prependParts[$key] = $value ?? $this->presets[$key] ?? null;

        return $this;
    }

    /**
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function withFile(string $pathOrContents, string $formName = 'file', string $filename = null): static
    {
        $file = is_file($pathOrContents) ? File::fromPath(
            $pathOrContents,
            $filename
        ) : File::withContents($pathOrContents, $filename);

        /**
         * @var array{headers: array<string, string>, body: string}
         */
        $options = Form::create([$formName => $file])->toOptions();

        $this->withHeaders($options['headers']);

        return $this->withOptions([
            'body' => $options['body'],
        ]);
    }

    /**
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function withFileContents(string $contents, string $formName = 'file', string $filename = null): static
    {
        return $this->withFile($contents, $formName, $filename);
    }

    /**
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function withFiles(array $files): static
    {
        foreach ($files as $key => $value) {
            $this->withFile($value, $key);
        }

        return $this;
    }

    public function mergeThenResetPrepends(array $options, string $method = 'GET'): array
    {
        $name = in_array(strtoupper($method), ['GET', 'HEAD', 'DELETE']) ? 'query' : 'body';

        if (($options['headers']['Content-Type'] ?? $options['headers']['content-type'] ?? null) === 'application/json' || ! empty($options['json'])) {
            $name = 'json';
        }

        if (($options['headers']['Content-Type'] ?? $options['headers']['content-type'] ?? null) === 'text/xml' || ! empty($options['xml'])) {
            $name = 'xml';
        }

        if (! empty($this->prependParts)) {
            $options[$name] = array_merge($this->prependParts, $options[$name] ?? []);
        }

        if (! empty($this->prependHeaders)) {
            $options['headers'] = array_merge($this->prependHeaders, $options['headers'] ?? []);
        }

        $this->prependParts = [];
        $this->prependHeaders = [];

        return $options;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handleMagicWithCall(string $method, mixed $value = null): static
    {
        // $client->withAppid();
        // $client->withAppid('wxf8b4f85f3a794e77');
        // $client->withAppidAs('sub_appid');
        if (! str_starts_with($method, 'with')) {
            throw new InvalidArgumentException(sprintf('The method "%s" is not supported.', $method));
        }

        $key = Str::snakeCase(substr($method, 4));

        // $client->withAppidAs('sub_appid');
        if (str_ends_with($key, '_as')) {
            $key = substr($key, 0, -3);

            [$key, $value] = [is_string($value) ? $value : $key, $this->presets[$key] ?? null];
        }

        return $this->with($key, $value);
    }
}
