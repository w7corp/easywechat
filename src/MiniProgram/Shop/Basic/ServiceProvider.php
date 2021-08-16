<?php

namespace EasyWeChat\MiniProgram\Shop\Basic;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * 自定义版交易组件及开放接口 - 接入商品前必需接口
 *
 * @package EasyWeChat\MiniProgram\Shop\Basic
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['shop_basic'] = function ($app) {
            return new Client($app);
        };
    }
}
