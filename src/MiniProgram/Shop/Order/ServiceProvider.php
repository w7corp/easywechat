<?php

namespace EasyWeChat\MiniProgram\Shop\Order;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * 自定义版交易组件及开放接口 - 订单接口
 *
 * @package EasyWeChat\MiniProgram\Shop\Order
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['shop_order'] = function ($app) {
            return new Client($app);
        };
    }
}
