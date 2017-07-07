<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Http;

use EasyWeChat\Kernel\Support\File;

/**
 * Class StreamResponse.
 *
 * @author overtrue <i@overtrue.me>
 */
class StreamResponse extends Response
{
    /**
     * @param string $directory
     * @param string $filename
     *
     * @return bool|int
     */
    public function save(string $directory, string $filename = '')
    {
        $this->getBody()->rewind();

        $directory = rtrim($directory, '/');

        if (!is_writable($directory)) {
            mkdir($directory, 0755, true);
        }

        $contents = $this->getBody()->getContents();

        if (empty($filename)) {
            $filename = md5($contents);
        }

        if (empty(pathinfo($filename, PATHINFO_EXTENSION))) {
            $filename .= File::getStreamExt($this->getBody());
        }

        file_put_contents($directory.'/'.$filename, $contents);

        return $filename;
    }

    /**
     * @param string $directory
     * @param string $filename
     *
     * @return bool|int
     */
    public function saveAs(string $directory, string $filename)
    {
        return $this->save($directory, $filename);
    }
}
