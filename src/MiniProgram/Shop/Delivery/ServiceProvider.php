<?php

namespace EasyWeChat\MiniProgram\Shop\Delivery;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * 自定义版交易组件及开放接口 - 物流接口
 *
 * @package EasyWeChat\MiniProgram\Shop\Delivery
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['shop_delivery'] = function ($app) {
            return new Client($app);
        };
    }
}
