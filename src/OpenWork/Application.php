<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenWork\Work\Application as Work;

/**
 * Application.
 *
 * @author xiaomin <keacefull@gmail.com>
 *
 * @property \EasyWeChat\OpenWork\Server\Guard            $server
 * @property \EasyWeChat\OpenWork\Corp\Client             $corp
 * @property \EasyWeChat\OpenWork\Provider\Client         $provider
 * @property \EasyWeChat\OpenWork\SuiteAuth\AccessToken   $suite_access_token
 * @property \EasyWeChat\OpenWork\Auth\AccessToken        $provider_access_token
 * @property \EasyWeChat\OpenWork\SuiteAuth\SuiteTicket   $suite_ticket
 * @property \EasyWeChat\OpenWork\MiniProgram\Client      $mini_program
 * @property \EasyWeChat\OpenWork\Media\Client            $media
 * @property \EasyWeChat\OpenWork\Contact\Client          $contact
 * @property \EasyWeChat\OpenWork\License\Client          $license_order
 * @property \EasyWeChat\OpenWork\License\Account         $license_account
 * @property \EasyWeChat\OpenWork\Device\Client           $device
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Auth\ServiceProvider::class,
        SuiteAuth\ServiceProvider::class,
        Server\ServiceProvider::class,
        Corp\ServiceProvider::class,
        Provider\ServiceProvider::class,
        MiniProgram\ServiceProvider::class,
        Media\ServiceProvider::class,
        Contact\ServiceProvider::class,
        License\ServiceProvider::class,
        Device\ServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $defaultConfig = [
        // http://docs.guzzlephp.org/en/stable/request-options.html
        'http' => [
            'base_uri' => 'https://qyapi.weixin.qq.com/',
        ],
    ];

    /**
     * Creates the miniProgram application.
     *
     * @return \EasyWeChat\Work\MiniProgram\Application
     */
    public function miniProgram(): \EasyWeChat\Work\MiniProgram\Application
    {
        return new \EasyWeChat\Work\MiniProgram\Application($this->getConfig());
    }

    /**
     * @param string $authCorpId    企业 corp_id
     * @param string $permanentCode 企业永久授权码
     *
     * @return Work
     */
    public function work(string $authCorpId, string $permanentCode): Work
    {
        return new Work($authCorpId, $permanentCode, $this);
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this['base']->$method(...$arguments);
    }
}
