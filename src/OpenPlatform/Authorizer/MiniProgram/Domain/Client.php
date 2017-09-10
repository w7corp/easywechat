<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Domain;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function get()
    {
        return $this->modify('get');
    }

    /**
     * @param array $request
     * @param array $wsRequest
     * @param array $upload
     * @param array $download
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function set(array $request, array $wsRequest, array $upload, array $download)
    {
        return $this->modify('set', $request, $wsRequest, $upload, $download);
    }

    /**
     * @param array $request
     * @param array $wsRequest
     * @param array $upload
     * @param array $download
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function add(array $request, array $wsRequest, array $upload, array $download)
    {
        return $this->modify('add', $request, $wsRequest, $upload, $download);
    }

    /**
     * @param array $request
     * @param array $wsRequest
     * @param array $upload
     * @param array $download
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function delete(array $request, array $wsRequest, array $upload, array $download)
    {
        return $this->modify('delete', $request, $wsRequest, $upload, $download);
    }

    /**
     * @param string $action
     * @param array  $request
     * @param array  $wsRequest
     * @param array  $upload
     * @param array  $download
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    protected function modify(string $action, array $request = [], array $wsRequest = [], array $upload = [], array $download = [])
    {
        $params = array_filter([
            'action' => $action,
            'requestdomain' => $request,
            'wsrequestdomain' => $wsRequest,
            'uploaddomain' => $upload,
            'downloaddomain' => $download,
        ]);

        return $this->httpPostJson('wxa/modify_domain', $params);
    }
}
