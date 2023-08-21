<?php

namespace EasyWeChat\Kernel\Contracts;

interface RefreshableJsApiTicket extends JsApiTicket
{
    public function refreshTicket(): string;
}
