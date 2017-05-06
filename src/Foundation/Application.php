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
use EasyWeChat\Foundation\Core\Http;
use EasyWeChat\OfficialAccount\Core\AccessToken;
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
 * @property \EasyWeChat\OfficialAccount\Core\AccessToken                   $access_token
 * @property \EasyWeChat\OfficialAccount\Server\Guard                       $server
 * @property \EasyWeChat\OfficialAccount\User\User                          $user
 * @property \EasyWeChat\OfficialAccount\User\Tag                           $user_tag
 * @property \EasyWeChat\OfficialAccount\User\Group                         $user_group
 * @property \EasyWeChat\OfficialAccount\Js\Js                              $js
 * @property \Overtrue\Socialite\Providers\WeChatProvider                   $oauth
 * @property \EasyWeChat\OfficialAccount\Menu\Menu                          $menu
 * @property \EasyWeChat\OfficialAccount\TemplateMessage\TemplateMessage    $template_message
 * @property \EasyWeChat\OfficialAccount\Material\Material                  $material
 * @property \EasyWeChat\OfficialAccount\Material\Temporary                 $material_temporary
 * @property \EasyWeChat\OfficialAccount\CustomerService\CustomerService    $customer_service
 * @property \EasyWeChat\OfficialAccount\Url\Url                            $url
 * @property \EasyWeChat\OfficialAccount\QRCode\QRCode                      $qrcode
 * @property \EasyWeChat\OfficialAccount\Semantic\Semantic                  $semantic
 * @property \EasyWeChat\OfficialAccount\Stats\Stats                        $stats
 * @property \EasyWeChat\OfficialAccount\Payment\Merchant                   $merchant
 * @property \EasyWeChat\OfficialAccount\Payment\Payment                    $payment
 * @property \EasyWeChat\OfficialAccount\Payment\LuckyMoney\LuckyMoney      $lucky_money
 * @property \EasyWeChat\OfficialAccount\Payment\MerchantPay\MerchantPay    $merchant_pay
 * @property \EasyWeChat\OfficialAccount\Payment\CashCoupon\CashCoupon      $cash_coupon
 * @property \EasyWeChat\OfficialAccount\Reply\Reply                        $reply
 * @property \EasyWeChat\OfficialAccount\Broadcast\Broadcast                $broadcast
 * @property \EasyWeChat\OfficialAccount\Card\Card                          $card
 * @property \EasyWeChat\OfficialAccount\Device\Device                      $device
 * @property \EasyWeChat\OfficialAccount\ShakeAround\ShakeAround            $shakearound
 * @property \EasyWeChat\OpenPlatform\OpenPlatform                          $open_platform
 * @property \EasyWeChat\MiniProgram\MiniProgram                            $mini_program
 */
class Application extends Container
{
    /**
     * Service Providers.
     *
     * @var array
     */
    protected $providers = [
        /*
         * OfficialAccount Service Providers...
         */
        \EasyWeChat\OfficialAccount\Server\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\User\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Js\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\OAuth\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Menu\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\TemplateMessage\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Material\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\CustomerService\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Url\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\QRCode\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Semantic\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Stats\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Payment\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\POI\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Reply\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Broadcast\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Card\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\Device\ServiceProvider::class,
        \EasyWeChat\OfficialAccount\ShakeAround\ServiceProvider::class,

        /*
         * OpenPlatform Service Providers...
         */
        \EasyWeChat\OpenPlatform\ServiceProvider::class,
        \EasyWeChat\OpenPlatform\Core\ServiceProvider::class,
        \EasyWeChat\OpenPlatform\Server\ServiceProvider::class,

        /*
         * MiniProgram Service Providers...
         */
        \EasyWeChat\MiniProgram\ServiceProvider::class,
        \EasyWeChat\MiniProgram\Sns\ServiceProvider::class,
        \EasyWeChat\MiniProgram\Stats\ServiceProvider::class,
        \EasyWeChat\MiniProgram\QRCode\ServiceProvider::class,
        \EasyWeChat\MiniProgram\Server\ServiceProvider::class,
        \EasyWeChat\MiniProgram\Material\ServiceProvider::class,
        \EasyWeChat\MiniProgram\CustomerService\ServiceProvider::class,
        \EasyWeChat\MiniProgram\TemplateMessage\ServiceProvider::class,
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

        if ($this['config']['debug']) {
            error_reporting(E_ALL);
        }

        $this->registerProviders();
        $this->registerBase();
        $this->initializeLogger();

        Http::setDefaultOptions($this['config']->get('guzzle', ['timeout' => 5.0]));

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
            $accessToken = new AccessToken(
                $this['config']['app_id'],
                $this['config']['secret']
            );

            $accessToken->setCache($this['cache']);

            return $accessToken;
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
}
