<?php

namespace EasyWeChat\MiniProgram\Shop\Account;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * 自定义版交易组件及开放接口 - 商家入驻接口
 *
 * @package EasyWeChat\MiniProgram\Shop\Account
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['shop_account'] = function ($app) {
            return new Client($app);
        };
    }
}
