<?php

namespace EasyWeChat\Pay\V3;

/**
 * @property-read Native native
 */
class Api extends BaseProvideV3
{
    public function __get(string $name)
    {
        $apis = ['native'];
        if (in_array($name, $apis)) {
            $class = 'EasyWeChat\\Pay\V3\\'.ucwords($name);
            return new $class($this->merchant, $this->client);
        }
    }
}
