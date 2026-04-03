<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;

trait SynchronizesServerRequest
{
    use InteractWithServerRequest {
        setRequest as private assignRequest;
        setRequestFromSymfonyRequest as private assignSymfonyRequest;
    }

    public function setRequest(ServerRequestInterface $request): static
    {
        $this->assignRequest($request);
        $this->synchronizeResolvedServerRequest($request);

        return $this;
    }

    public function setRequestFromSymfonyRequest(Request $symfonyRequest): static
    {
        $this->assignSymfonyRequest($symfonyRequest);

        if ($this->request) {
            $this->synchronizeResolvedServerRequest($this->request);
        }

        return $this;
    }

    protected function synchronizeResolvedServerRequest(ServerRequestInterface $request): void
    {
        if (! isset($this->server) || ! method_exists($this->server, 'setRequest')) {
            return;
        }

        $this->server->setRequest($request);
    }
}
