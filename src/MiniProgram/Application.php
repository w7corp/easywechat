<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram;

use EasyWeChat\BasicService;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 *
 * @property \EasyWeChat\MiniProgram\Auth\AccessToken           $access_token
 * @property \EasyWeChat\MiniProgram\DataCube\Client            $data_cube
 * @property \EasyWeChat\MiniProgram\AppCode\Client             $app_code
 * @property \EasyWeChat\MiniProgram\Auth\Client                $auth
 * @property \EasyWeChat\OfficialAccount\Server\Guard           $server
 * @property \EasyWeChat\MiniProgram\Encryptor                  $encryptor
 * @property \EasyWeChat\MiniProgram\TemplateMessage\Client     $template_message
 * @property \EasyWeChat\OfficialAccount\CustomerService\Client $customer_service
 * @property \EasyWeChat\MiniProgram\Plugin\Client              $plugin
 * @property \EasyWeChat\MiniProgram\Plugin\DevClient           $plugin_dev
 * @property \EasyWeChat\MiniProgram\UniformMessage\Client      $uniform_message
 * @property \EasyWeChat\MiniProgram\ActivityMessage\Client     $activity_message
 * @property \EasyWeChat\MiniProgram\Express\Client             $logistics
 * @property \EasyWeChat\MiniProgram\NearbyPoi\Client           $nearby_poi
 * @property \EasyWeChat\MiniProgram\OCR\Client                 $ocr
 * @property \EasyWeChat\MiniProgram\Soter\Client               $soter
 * @property \EasyWeChat\BasicService\Media\Client              $media
 * @property \EasyWeChat\BasicService\ContentSecurity\Client    $content_security
 * @property \EasyWeChat\MiniProgram\Mall\ForwardsMall          $mall
 * @property \EasyWeChat\MiniProgram\SubscribeMessage\Client    $subscribe_message
 * @property \EasyWeChat\MiniProgram\RealtimeLog\Client         $realtime_log
 * @property \EasyWeChat\MiniProgram\Search\Client              $search
 * @property \EasyWeChat\MiniProgram\Live\Client                $live
 * @property \EasyWeChat\MiniProgram\Broadcast\Client           $broadcast
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Auth\ServiceProvider::class,
        DataCube\ServiceProvider::class,
        AppCode\ServiceProvider::class,
        Server\ServiceProvider::class,
        TemplateMessage\ServiceProvider::class,
        CustomerService\ServiceProvider::class,
        UniformMessage\ServiceProvider::class,
        ActivityMessage\ServiceProvider::class,
        OpenData\ServiceProvider::class,
        Plugin\ServiceProvider::class,
        Base\ServiceProvider::class,
        Express\ServiceProvider::class,
        NearbyPoi\ServiceProvider::class,
        OCR\ServiceProvider::class,
        Soter\ServiceProvider::class,
        Mall\ServiceProvider::class,
        SubscribeMessage\ServiceProvider::class,
        RealtimeLog\ServiceProvider::class,
        Search\ServiceProvider::class,
        Live\ServiceProvider::class,
        Broadcast\ServiceProvider::class,
        // Base services
        BasicService\Media\ServiceProvider::class,
        BasicService\ContentSecurity\ServiceProvider::class,
    ];

    /**
     * Handle dynamic calls.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this->base->$method(...$args);
    }
}
