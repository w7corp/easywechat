<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Code;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * @param int    $templateId
     * @param string $extJson
     * @param string $version
     * @param string $description
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function commit(int $templateId, string $extJson, string $version, string $description)
    {
        return $this->httpPostJson('wxa/commit', [
            'template_id' => $templateId,
            'ext_json' => $extJson,
            'user_version' => $version,
            'user_desc' => $description,
        ]);
    }

    /**
     * @param string|null $path
     *
     * @return \EasyWeChat\Kernel\Http\Response
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getQrCode(string $path = null)
    {
        return $this->requestRaw('wxa/get_qrcode', 'GET', [
            'query' => ['path' => $path],
        ]);
    }

    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getCategory()
    {
        return $this->httpGet('wxa/get_category');
    }

    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getPage()
    {
        return $this->httpGet('wxa/get_page');
    }

    /**
     * @param array $data
     * @param string|null $feedbackInfo
     * @param string|null $feedbackStuff
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function submitAudit(array $data, string $feedbackInfo = null, string $feedbackStuff = null)
    {
        if (isset($data['item_list'])) {
            return $this->httpPostJson('wxa/submit_audit', $data);
        }

        return $this->httpPostJson('wxa/submit_audit', [
            'item_list' => $data,
            'feedback_info' => $feedbackInfo,
            'feedback_stuff' => $feedbackStuff,
        ]);
    }

    /**
     * @param int $auditId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAuditStatus(int $auditId)
    {
        return $this->httpPostJson('wxa/get_auditstatus', [
            'auditid' => $auditId,
        ]);
    }

    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getLatestAuditStatus()
    {
        return $this->httpGet('wxa/get_latest_auditstatus');
    }

    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function release()
    {
        return $this->httpPostJson('wxa/release');
    }

    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function withdrawAudit()
    {
        return $this->httpGet('wxa/undocodeaudit');
    }

    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function rollbackRelease()
    {
        return $this->httpGet('wxa/revertcoderelease');
    }

    /**
     * @param string $action
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function changeVisitStatus(string $action)
    {
        return $this->httpPostJson('wxa/change_visitstatus', [
            'action' => $action,
        ]);
    }

    /**
     * 分阶段发布.
     *
     * @param int $grayPercentage
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function grayRelease(int $grayPercentage)
    {
        return $this->httpPostJson('wxa/grayrelease', [
            'gray_percentage' => $grayPercentage,
        ]);
    }

    /**
     * 取消分阶段发布.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function revertGrayRelease()
    {
        return $this->httpGet('wxa/revertgrayrelease');
    }

    /**
     * 查询当前分阶段发布详情.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getGrayRelease()
    {
        return $this->httpGet('wxa/getgrayreleaseplan');
    }

    /**
     * 查询当前设置的最低基础库版本及各版本用户占比.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSupportVersion()
    {
        return $this->httpPostJson('cgi-bin/wxopen/getweappsupportversion');
    }

    /**
     * 设置最低基础库版本.
     *
     * @param string $version
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setSupportVersion(string $version)
    {
        return $this->httpPostJson('cgi-bin/wxopen/setweappsupportversion', [
            'version' => $version,
        ]);
    }

    /**
     * 查询服务商的当月提审限额（quota）和加急次数.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function queryQuota()
    {
        return $this->httpGet('wxa/queryquota');
    }

    /**
     * 加急审核申请.
     *
     * @param int $auditId 审核单ID
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function speedupAudit(int $auditId)
    {
        return $this->httpPostJson('wxa/speedupaudit', [
            'auditid' => $auditId,
        ]);
    }
}
