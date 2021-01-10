<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\SuiteAuth;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Traits\InteractsWithCache;
use EasyWeChat\OpenWork\Application;

/**
 * SuiteTicket.
 *
 */
class SuiteTicket
{
    use InteractsWithCache;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $ticket
     *
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setTicket(string $ticket)
    {
        $this->getCache()->set($this->getCacheKey(), $ticket, 1800);

        if (!$this->getCache()->has($this->getCacheKey())) {
            throw new RuntimeException('Failed to cache suite ticket.');
        }

        return $this;
    }

    /**
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getTicket(): string
    {
        if ($cached = $this->getCache()->get($this->getCacheKey())) {
            return $cached;
        }

        throw new RuntimeException('Credential "suite_ticket" does not exist in cache.');
    }

    /**
     * @return string
     */
    protected function getCacheKey(): string
    {
        return 'easywechat.open_work.suite_ticket.'.$this->app['config']['suite_id'];
    }
}
