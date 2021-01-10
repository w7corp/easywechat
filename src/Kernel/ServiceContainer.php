<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Providers\ConfigServiceProvider;
use EasyWeChat\Kernel\Providers\EventDispatcherServiceProvider;
use EasyWeChat\Kernel\Providers\ExtensionServiceProvider;
use EasyWeChat\Kernel\Providers\HttpClientServiceProvider;
use EasyWeChat\Kernel\Providers\LogServiceProvider;
use EasyWeChat\Kernel\Providers\RequestServiceProvider;
use EasyWeChatComposer\Traits\WithAggregator;
use Pimple\Container;

/**
 *
 * @property \EasyWeChat\Kernel\Config                          $config
 * @property \Symfony\Component\HttpFoundation\Request          $request
 * @property \GuzzleHttp\Client                                 $http_client
 * @property \Monolog\Logger                                    $logger
 * @property \Symfony\Component\EventDispatcher\EventDispatcher $events
 */
class ServiceContainer extends Container
{
    use WithAggregator;

    protected ?string  $id;
    protected array $providers = [];
    protected array $defaultConfig = [];
    protected array $userConfig = [];

    public function __construct(array $config = [], array $prepends = [], string $id = null)
    {
        $this->userConfig = $config;

        parent::__construct($prepends);

        $this->registerProviders($this->getProviders());

        $this->id = $id;

        $this->aggregate();

        $this->events->dispatch(new Events\ApplicationInitialized($this));
    }

    public function getId(): string
    {
        return $this->id ?? $this->id = md5(json_encode($this->userConfig));
    }

    public function getConfig(): array
    {
        $base = [
            // http://docs.guzzlephp.org/en/stable/request-options.html
            'http' => [
                'timeout' => 30.0,
                'base_uri' => 'https://api.weixin.qq.com/',
            ],
        ];

        return array_replace_recursive($base, $this->defaultConfig, $this->userConfig);
    }

    public function getProviders(): array
    {
        return array_merge([
            ConfigServiceProvider::class,
            LogServiceProvider::class,
            RequestServiceProvider::class,
            HttpClientServiceProvider::class,
            ExtensionServiceProvider::class,
            EventDispatcherServiceProvider::class,
        ], $this->providers);
    }

    public function rebind(string $id, mixed $value)
    {
        $this->offsetUnset($id);
        $this->offsetSet($id, $value);
    }

    public function __get($id): mixed
    {
        if ($this->shouldDelegate($id)) {
            return $this->delegateTo($id);
        }

        return $this->offsetGet($id);
    }

    public function __set(string $id, mixed $value)
    {
        $this->offsetSet($id, $value);
    }

    public function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            parent::register(new $provider());
        }
    }
}
