<?php
/*
 * This file is part of the keacefull/wechat.
 *
 * (c) xiaomin <keacefull@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenWork\Work\Application as Work;

/**
 * Class Application
 * @package EasyWeChat\OpenWork
 *
 * @author Xiaomin<keacefull@gmail.com>
 *
 * //配置文件格式
 * $config = [
 *      'corp_id'         => 'XXX', //服务商的corpid
 *      'secret'          => 'XXX', //服务商的secret，在服务商管理后台可见
 *      'suite_id'        => 'XXX', //应用的suiteID
 *      'suite_secret'    => 'XXX',//应用的Secret
 *      'token'           => 'XXX', //应用的token
 *      'aes_key'         => 'XXX',//应用的aes_key
 *      'reg_template_id' => '注册定制化模板ID',
 * ];
 *
 * @property \EasyWeChat\OpenWork\Server\ServiceProvider $server
 * @property \EasyWeChat\OpenWork\Corp\ServiceProvider $corp
 * @property \EasyWeChat\OpenWork\Provider\ServiceProvider $provider
 *
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
     * @param string $auth_corpid 企业 corp_id
     * @param string $permanent_code 企业永久授权码
     * @return Work
     */
    public function work(string $auth_corpid, string $permanent_code): Work
    {
        return new Work($auth_corpid, $permanent_code, $this);
    }


    /**
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this['base']->$method(...$arguments);
    }
}