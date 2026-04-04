<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Kernel\Support\PublicKey;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\ResetsResolvedDependencies;
use EasyWeChat\Kernel\Traits\SynchronizesServerRequest;
use EasyWeChat\Pay\Contracts\Validator as ValidatorInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Application implements Contracts\Application
{
    use InteractWithConfig;
    use InteractWithHttpClient;
    use LoggerAwareTrait;
    use ResetsResolvedDependencies;
    use SynchronizesServerRequest;

    protected ?ServerInterface $server = null;

    protected bool $usesCustomServer = false;

    protected ?ValidatorInterface $validator = null;

    protected bool $usesCustomValidator = false;

    protected ?HttpClientInterface $client = null;

    protected bool $usesCustomClient = false;

    protected ?Merchant $merchant = null;

    public function getUtils(): Utils
    {
        return new Utils($this->getMerchant());
    }

    public function getMerchant(): Merchant
    {
        if (! $this->merchant) {
            /** @var array<int|string, string|PublicKey> $platformCerts */
            $platformCerts = $this->config->has('platform_certs') ? (array) $this->config['platform_certs'] : [];

            $this->merchant = new Merchant(
                mchId: $this->getStringConfig('mch_id'),
                privateKey: new PrivateKey($this->getStringConfig('private_key')),
                certificate: new PublicKey($this->getStringConfig('certificate')),
                secretKey: $this->getStringConfig('secret_key'),
                v2SecretKey: $this->getStringConfig('v2_secret_key'),
                platformCerts: $platformCerts,
            );
        }

        return $this->merchant;
    }

    public function getValidator(): ValidatorInterface
    {
        if (! $this->validator) {
            $this->validator = new Validator($this->getMerchant());
            $this->usesCustomValidator = false;
        }

        return $this->validator;
    }

    public function setValidator(ValidatorInterface $validator): static
    {
        $this->validator = $validator;
        $this->usesCustomValidator = true;

        return $this;
    }

    public function getServer(): Server|ServerInterface
    {
        if (! $this->server) {
            $this->server = new Server(
                merchant: $this->getMerchant(),
                request: $this->getRequest(),
            );
            $this->usesCustomServer = false;
        }

        return $this->server;
    }

    public function setServer(ServerInterface $server): static
    {
        $this->server = $server;
        $this->usesCustomServer = true;

        return $this;
    }

    public function setConfig(ConfigInterface $config): static
    {
        $this->config = $config;
        $this->afterConfigUpdated();

        return $this;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function getClient(): Client|HttpClientInterface
    {
        if (! $this->client) {
            /** @var array<string, mixed> $httpOptions */
            $httpOptions = (array) $this->config->get('http', []);

            $this->client = (new Client(
                $this->getMerchant(),
                $this->getHttpClient(),
                $httpOptions
            ))->setPresets($this->config->all());
            $this->usesCustomClient = false;
        }

        return $this->client;
    }

    public function setClient(HttpClientInterface $client): static
    {
        $this->client = $client;
        $this->usesCustomClient = true;

        return $this;
    }

    protected function resetClient(): void
    {
        if ($this->usesCustomClient) {
            return;
        }

        $this->client = null;
    }

    protected function afterHttpClientUpdated(): void
    {
        $this->resetClient();
    }

    protected function afterConfigUpdated(): void
    {
        $this->merchant = null;
        $this->resetHttpClient();
        $this->resetClient();
        $this->resetResolvedDependencies([
            [$this->usesCustomValidator, fn (): mixed => $this->validator = null],
            [$this->usesCustomServer, fn (): mixed => $this->server = null],
        ]);
    }
}
