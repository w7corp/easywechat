<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\Kernel\Server\BaseServer;
use EasyWeChat\Kernel\Server\Handlers\ServerValidationHandler;
use EasyWeChat\Kernel\ServiceContainer;

class Server extends BaseServer
{
    // 消息类型
    public const TEXT = 'text';
    public const IMAGE = 'image';
    public const VOICE = 'voice';
    public const VIDEO = 'video';
    public const SHORT_VIDEO = 'shortvideo';
    public const LOCATION = 'location';
    public const LINK = 'link';
    public const DEVICE_EVENT = 'device_event';
    public const DEVICE_TEXT = 'device_text';
    public const EVENT = 'event';
    public const DEVICE_FILE = 'file';
    public const DEVICE_MINIPROGRAM_PAGE = 'miniprogrampage';

    // 事件类型
    public const SUBSCRIBE_EVENT = 'subscribe';
    public const UNSUBSCRIBE_EVENT = 'unsubscribe';
    public const SCAN_EVENT = 'SCAN';
    public const LOCATION_EVENT = 'LOCATION';
    public const CLICK_EVENT = 'CLICK';
    public const VIEW_EVENT = 'VIEW';

    /**
     * Server constructor.
     *
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app);

        $this->withoutHandler(ServerValidationHandler::class);
    }

    /**
     * @return bool
     */
    protected function shouldReturnRawResponse(): bool
    {
        return !is_null($this->app['request']->get('echostr'));
    }
}
