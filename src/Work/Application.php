<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Work\MiniProgram\Application as MiniProgram;

/**
 * Application.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 *
 * @property \EasyWeChat\Work\OA\Client                        $oa
 * @property \EasyWeChat\Work\Auth\AccessToken                 $access_token
 * @property \EasyWeChat\Work\Agent\Client                     $agent
 * @property \EasyWeChat\Work\Department\Client                $department
 * @property \EasyWeChat\Work\Media\Client                     $media
 * @property \EasyWeChat\Work\Menu\Client                      $menu
 * @property \EasyWeChat\Work\Message\Client                   $message
 * @property \EasyWeChat\Work\Message\Messenger                $messenger
 * @property \EasyWeChat\Work\User\Client                      $user
 * @property \EasyWeChat\Work\User\TagClient                   $tag
 * @property \EasyWeChat\Work\Server\Guard                     $server
 * @property \EasyWeChat\Work\Jssdk\Client                     $jssdk
 * @property \Overtrue\Socialite\Providers\WeWorkProvider      $oauth
 * @property \EasyWeChat\Work\Invoice\Client                   $invoice
 * @property \EasyWeChat\Work\Chat\Client                      $chat
 * @property \EasyWeChat\Work\ExternalContact\Client           $external_contact
 * @property \EasyWeChat\Work\ExternalContact\ContactWayClient $contact_way
 * @property \EasyWeChat\Work\ExternalContact\StatisticsClient $external_contact_statistics
 * @property \EasyWeChat\Work\ExternalContact\MessageClient    $external_contact_message
 * @property \EasyWeChat\Work\GroupRobot\Client                $group_robot
 * @property \EasyWeChat\Work\GroupRobot\Messenger             $group_robot_messenger
 * @property \EasyWeChat\Work\Calendar\Client                  $calendar
 * @property \EasyWeChat\Work\Schedule\Client                  $schedule
 * @property \EasyWeChat\Work\MsgAudit\Client                  $msg_audit
 * @property \EasyWeChat\Work\ExternalContact\SchoolClient     $school
 *
 * @method mixed getCallbackIp()
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        OA\ServiceProvider::class,
        Auth\ServiceProvider::class,
        Base\ServiceProvider::class,
        Menu\ServiceProvider::class,
        OAuth\ServiceProvider::class,
        User\ServiceProvider::class,
        Agent\ServiceProvider::class,
        Media\ServiceProvider::class,
        Message\ServiceProvider::class,
        Department\ServiceProvider::class,
        Server\ServiceProvider::class,
        Jssdk\ServiceProvider::class,
        Invoice\ServiceProvider::class,
        Chat\ServiceProvider::class,
        ExternalContact\ServiceProvider::class,
        GroupRobot\ServiceProvider::class,
        Calendar\ServiceProvider::class,
        Schedule\ServiceProvider::class,
        MsgAudit\ServiceProvider::class,
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
     */
    public function miniProgram(): MiniProgram
    {
        return new MiniProgram($this->getConfig());
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
