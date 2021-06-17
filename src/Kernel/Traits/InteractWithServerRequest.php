<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use Psr\Http\Message\ServerRequestInterface;

trait InteractWithServerRequest
{
    protected ?ServerRequestInterface $request = null;

    public function getRequest(): ServerRequestInterface
    {
        if (!$this->request) {
            $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

            $creator = new \Nyholm\Psr7Server\ServerRequestCreator(
                serverRequestFactory: $psr17Factory,
                uriFactory: $psr17Factory,
                uploadedFileFactory: $psr17Factory,
                streamFactory: $psr17Factory
            );

            $this->request = $creator->fromGlobals();
        }

        return $this->request;
    }

    public function setRequest(ServerRequestInterface $request): static
    {
        $this->request = $request;

        return $this;
    }
}
