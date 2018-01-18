<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Application.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Foundation;

use Doctrine\Common\Cache\Cache as CacheInterface;
use Doctrine\Common\Cache\FilesystemCache;
use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Http;
use EasyWeChat\Support\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application.
 *
 * @property \EasyWeChat\Core\AccessToken                    $access_token
 * @property \EasyWeChat\Server\Guard                        $server
 * @property \EasyWeChat\User\User                           $user
 * @property \EasyWeChat\User\Tag                            $user_tag
 * @property \EasyWeChat\User\Group                          $user_group
 * @property \EasyWeChat\Js\Js                               $js
 * @property \Overtrue\Socialite\Providers\WeChatProvider    $oauth
 * @property \EasyWeChat\Menu\Menu                           $menu
 * @property \EasyWeChat\Notice\Notice                       $notice
 * @property \EasyWeChat\Material\Material                   $material
 * @property \EasyWeChat\Material\Temporary                  $material_temporary
 * @property \EasyWeChat\Staff\Staff                         $staff
 * @property \EasyWeChat\Url\Url                             $url
 * @property \EasyWeChat\QRCode\QRCode                       $qrcode
 * @property \EasyWeChat\Semantic\Semantic                   $semantic
 * @property \EasyWeChat\Stats\Stats                         $stats
 * @property \EasyWeChat\Payment\Merchant                    $merchant
 * @property \EasyWeChat\Payment\Payment                     $payment
 * @property \EasyWeChat\Payment\LuckyMoney\LuckyMoney       $lucky_money
 * @property \EasyWeChat\Payment\MerchantPay\MerchantPay     $merchant_pay
 * @property \EasyWeChat\Payment\CashCoupon\CashCoupon       $cash_coupon
 * @property \EasyWeChat\Reply\Reply                         $reply
 * @property \EasyWeChat\Broadcast\Broadcast                 $broadcast
 * @property \EasyWeChat\Card\Card                           $card
 * @property \EasyWeChat\Device\Device                       $device
 * @property \EasyWeChat\Comment\Comment                     $comment
 * @property \EasyWeChat\ShakeAround\ShakeAround             $shakearound
 * @property \EasyWeChat\OpenPlatform\OpenPlatform           $open_platform
 * @property \EasyWeChat\MiniProgram\MiniProgram             $mini_program
 *
 * @method \EasyWeChat\Support\Collection clearQuota()
 * @method \EasyWeChat\Support\Collection getCallbackIp()
 */
class Application extends Container
{
    /**
     * Service Providers.
     *
     * @var array
     */
    protected $providers = [
        ServiceProviders\FundamentalServiceProvider::class,
        ServiceProviders\ServerServiceProvider::class,
        ServiceProviders\UserServiceProvider::class,
        ServiceProviders\JsServiceProvider::class,
        ServiceProviders\OAuthServiceProvider::class,
        ServiceProviders\MenuServiceProvider::class,
        ServiceProviders\NoticeServiceProvider::class,
        ServiceProviders\MaterialServiceProvider::class,
        ServiceProviders\StaffServiceProvider::class,
        ServiceProviders\UrlServiceProvider::class,
        ServiceProviders\QRCodeServiceProvider::class,
        ServiceProviders\SemanticServiceProvider::class,
        ServiceProviders\StatsServiceProvider::class,
        ServiceProviders\PaymentServiceProvider::class,
        ServiceProviders\POIServiceProvider::class,
        ServiceProviders\ReplyServiceProvider::class,
        ServiceProviders\BroadcastServiceProvider::class,
        ServiceProviders\CardServiceProvider::class,
        ServiceProviders\DeviceServiceProvider::class,
        ServiceProviders\ShakeAroundServiceProvider::class,
        ServiceProviders\OpenPlatformServiceProvider::class,
        ServiceProviders\MiniProgramServiceProvider::class,
        ServiceProviders\CommentServiceProvider::class,
    ];

    /**
     * Application constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        parent::__construct();

        $this['config'] = function () use ($config) {
            return new Config($config);
        };

        $this->registerProviders();
        $this->registerBase();
        $this->initializeLogger();

        Http::setDefaultOptions($this['config']->get('guzzle', ['timeout' => 5.0]));

        AbstractAPI::maxRetries($this['config']->get('max_retries', 2));

        $this->logConfiguration($config);
    }

    /**
     * Log configuration.
     *
     * @param array $config
     */
    public function logConfiguration($config)
    {
        $config = new Config($config);

        $keys = ['app_id', 'secret', 'open_platform.app_id', 'open_platform.secret', 'mini_program.app_id', 'mini_program.secret'];
        foreach ($keys as $key) {
            !$config->has($key) || $config[$key] = '***'.substr($config[$key], -5);
        }

        Log::debug('Current config:', $config->toArray());
    }

    /**
     * Add a provider.
     *
     * @param string $provider
     *
     * @return Application
     */
    public function addProvider($provider)
    {
        array_push($this->providers, $provider);

        return $this;
    }

    /**
     * Set providers.
     *
     * @param array $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = [];

        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

    /**
     * Register providers.
     */
    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    /**
     * Register basic providers.
     */
    private function registerBase()
    {
        $this['request'] = function () {
            return Request::createFromGlobals();
        };

        if (!empty($this['config']['cache']) && $this['config']['cache'] instanceof CacheInterface) {
            $this['cache'] = $this['config']['cache'];
        } else {
            $this['cache'] = function () {
                return new FilesystemCache(sys_get_temp_dir());
            };
        }

        $this['access_token'] = function () {
            return new AccessToken(
                $this['config']['app_id'],
                $this['config']['secret'],
                $this['cache']
            );
        };
    }

    /**
     * Initialize logger.
     */
    private function initializeLogger()
    {
        if (Log::hasLogger()) {
            return;
        }

        $logger = new Logger('easywechat');

        if (!$this['config']['debug'] || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($this['config']['log.handler'] instanceof HandlerInterface) {
            $logger->pushHandler($this['config']['log.handler']);
        } elseif ($logFile = $this['config']['log.file']) {
            $logger->pushHandler(new StreamHandler(
                $logFile,
                $this['config']->get('log.level', Logger::WARNING),
                true,
                $this['config']->get('log.permission', null))
            );
        }

        Log::setLogger($logger);
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        if (is_callable([$this['fundamental.api'], $method])) {
            return call_user_func_array([$this['fundamental.api'], $method], $args);
        }

        throw new \Exception("Call to undefined method {$method}()");
    }
}
