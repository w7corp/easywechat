<?php
/**
 * Created by PhpStorm.
 * User: caikeal
 * Date: 2018/6/12
 * Time: 下午4:35
 */

namespace EasyWeChat\Work\MiniProgram;


use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author Caikeal <caikeal@qq.com>
 *
 * @property \EasyWeChat\Work\MiniProgram\Auth\Client $auth
 */
class Application extends ServiceContainer
{
    /**
     * Application constructor.
     *
     * @param array $config
     * @param array $prepends
     */
    public function __construct(array $config = [], array $prepends = [])
    {
        parent::__construct($config, $prepends);

        $providers = [
            Auth\ServiceProvider::class,
        ];

        foreach ($providers as $provider) {
            $this->register(new $provider());
        }
    }
}