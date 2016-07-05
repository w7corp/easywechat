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
 * @link      https://github.com/overtrue/wechat
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Foundation;

use Doctrine\Common\Cache\Cache as CacheInterface;
use Doctrine\Common\Cache\FilesystemCache;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Http;
use EasyWeChat\Support\Log;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application.
 *
 * @property \EasyWeChat\Server\Guard                    $server
 * @property \EasyWeChat\User\User                       $user
 * @property \EasyWeChat\User\Tag                        $user_tag
 * @property \EasyWeChat\User\Group                      $user_group
 * @property \EasyWeChat\Js\Js                           $js
 * @property \Overtrue\Socialite\SocialiteManager        $oauth
 * @property \EasyWeChat\Menu\Menu                       $menu
 * @property \EasyWeChat\Notice\Notice                   $notice
 * @property \EasyWeChat\Material\Material               $material
 * @property \EasyWeChat\Material\Temporary              $material_temporary
 * @property \EasyWeChat\Staff\Staff                     $staff
 * @property \EasyWeChat\Url\Url                         $url
 * @property \EasyWeChat\QRCode\QRCode                   $qrcode
 * @property \EasyWeChat\Semantic\Semantic               $semantic
 * @property \EasyWeChat\Stats\Stats                     $stats
 * @property \EasyWeChat\Payment\Merchant                $merchant
 * @property \EasyWeChat\Payment\Payment                 $payment
 * @property \EasyWeChat\Payment\LuckyMoney\LuckyMoney   $lucky_money
 * @property \EasyWeChat\Payment\MerchantPay\MerchantPay $merchant_pay
 * @property \EasyWeChat\Reply\Reply                     $reply
 * @property \EasyWeChat\Broadcast\Broadcast             $broadcast
 */
class Application extends Container
{
    /**
     * Service Providers.
     *
     * @var array
     */
    protected $providers = [
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

        foreach (['app_id', 'secret'] as $key) {
            !isset($config[$key]) || $config[$key] = '***'.substr($config[$key], -5);
        }

        Log::debug('Current config:', $config);
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
        } elseif ($logFile = $this['config']['log.file']) {
            $logger->pushHandler(new StreamHandler($logFile, $this['config']->get('log.level', Logger::WARNING)));
        }

        Log::setLogger($logger);
    }
}
