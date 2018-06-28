<?php

namespace EasyWeChat\OpenWork\SuiteAuth;

use EasyWeChat\Kernel\Traits\InteractsWithCache;
use EasyWeChat\OpenWork\Application;
use EasyWeChat\Kernel\Exceptions\RuntimeException;

class SuiteTicket
{
    use InteractsWithCache;

    /**
     * @var Application
     */
    protected $app;

    /**
     * SuiteTicket constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $ticket
     * @return $this
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setTicket(string $ticket)
    {
        $this->getCache()->set($this->getCacheKey(), $ticket, 600);

        return $this;
    }

    /**
     * @return string
     * @throws RuntimeException
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
        return 'easywechat.open_work.suite_ticket.' . $this->app['config']['corp_id'];
    }

}