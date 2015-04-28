<?php

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;

class Input extends Bag
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct(array_merge($_GET, $_POST));
    }
}
