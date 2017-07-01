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
 * Material.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue
 * @see       http://overtrue.me
 */

namespace EasyWeChat\ShakeAround;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;

/**
 * Class Material.
 */
class Material extends AbstractAPI
{
    const API_MATERIAL_ADD = 'https://api.weixin.qq.com/shakearound/material/add';

    /**
     * Upload image material.
     *
     * @param string $path
     * @param string $type
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function uploadImage($path, $type = 'icon')
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }

        $type = strtolower($type);

        return $this->parseJSON('upload', [self::API_MATERIAL_ADD, ['media' => $path], [], ['type' => $type]]);
    }
}
