<?php

declare(strict_types=1);

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

define('TEST_ROOT', __DIR__);
define('STUBS_ROOT', __DIR__.'/fixtures');

$_SERVER['HTTP_HOST'] = 'localhost';

include __DIR__.'/../vendor/autoload.php';
