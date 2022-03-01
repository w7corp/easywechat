<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;

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

    public function setRequestFromSymfonyRequest(Request $symfonyRequest): static
    {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        $this->request = $psrHttpFactory->createRequest($symfonyRequest);

        return $this;
    }
}
