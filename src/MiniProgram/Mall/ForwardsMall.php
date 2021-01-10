<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Mall;

/**
 *
 * @property \EasyWeChat\MiniProgram\Mall\OrderClient   $order
 * @property \EasyWeChat\MiniProgram\Mall\CartClient    $cart
 * @property \EasyWeChat\MiniProgram\Mall\ProductClient $product
 * @property \EasyWeChat\MiniProgram\Mall\MediaClient   $media
 */
class ForwardsMall
{
    /**
     * @var \EasyWeChat\Kernel\ServiceContainer
     */
    protected $app;

    /**
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->app["mall.{$property}"];
    }
}
