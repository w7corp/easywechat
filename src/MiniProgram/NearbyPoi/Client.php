<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\NearbyPoi;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class Client.
 *
 * @author joyeekk <xygao2420@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Add nearby poi.
     *
     * @param string $name
     * @param string $credential
     * @param string $address
     * @param string $proofMaterial
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function add(string $name, string $credential, string $address, string $proofMaterial = null)
    {
        return $this->httpPostJson('wxa/addnearbypoi', [
            'related_name' => $name,
            'related_credential' => $credential,
            'related_address' => $address,
            'related_proof_material' => $proofMaterial,
        ]);
    }

    /**
     * Delete nearby poi.
     *
     * @param string $poiId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete(string $poiId)
    {
        return $this->httpPostJson('wxa/delnearbypoi', [
            'poi_id' => $poiId,
        ]);
    }

    /**
     * Get nearby poi list.
     *
     * @param int $page
     * @param int $pageRows
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function list(int $page, int $pageRows)
    {
        return $this->httpGet('wxa/getnearbypoilist', [
            'page' => $page,
            'page_rows' => $pageRows,
        ]);
    }

    /**
     * Set nearby poi show status.
     *
     * @param string $poiId
     * @param int    $status
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function setVisibility(string $poiId, int $status)
    {
        if (!in_array($status, [0, 1], true)) {
            throw new InvalidArgumentException('status should be 0 or 1.');
        }

        return $this->httpPostJson('wxa/setnearbypoishowstatus', [
            'poi_id' => $poiId,
            'status' => $status,
        ]);
    }
}
