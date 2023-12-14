<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Wedrive;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Support\Arr;

/**
 * Class SpaceClient
 *
 * @author lio990527 <lio990527@163.com>
 */
class SpaceClient extends BaseClient
{
    /**
     * 新建空间
     *
     * @param string $userid        操作者userid
     * @param string $spaceName     空间名称
     * @param array $authInfo       空间成员权限信息
     * @param integer $spaceSubType 空间类型, 0:普通 1:相册
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93656#%E6%96%B0%E5%BB%BA%E7%A9%BA%E9%97%B4
     */
    public function create($userid, $spaceName, $authInfo = [], $spaceSubType = 0)
    {
        $data = [
            'userid' => $userid,
            'space_name' => $spaceName,
            'auth_info' => Arr::isAssoc($authInfo) ? [$authInfo] : $authInfo,
            'space_sub_type' => $spaceSubType,
        ];

        return $this->httpPostJson('cgi-bin/wedrive/space_create', $data);
    }

    /**
     * 重命名空间
     *
     * @param string $userid
     * @param string $spaceid
     * @param string $spaceName 重命名后的空间名
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93656#%E9%87%8D%E5%91%BD%E5%90%8D%E7%A9%BA%E9%97%B4
     */
    public function rename($userid, $spaceid, $spaceName)
    {
        $data = [
            'userid' => $userid,
            'spaceid' => $spaceid,
            'space_name' => $spaceName
        ];

        return $this->httpPostJson('cgi-bin/wedrive/space_rename', $data);
    }

    /**
     * 解散空间
     *
     * @param string $userid
     * @param string $spaceid
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93656#%E8%A7%A3%E6%95%A3%E7%A9%BA%E9%97%B4
     */
    public function dismiss($userid, $spaceid)
    {
        return $this->httpPostJson('cgi-bin/wedrive/space_dismiss', compact('userid', 'spaceid'));
    }

    /**
     * 获取空间/相册信息
     *
     * @param string $userid
     * @param string $spaceid
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93656#%E8%8E%B7%E5%8F%96%E7%A9%BA%E9%97%B4%E7%9B%B8%E5%86%8C%E4%BF%A1%E6%81%AF
     */
    public function info($userid, $spaceid)
    {
        return $this->httpPostJson('cgi-bin/wedrive/space_info', compact('userid', 'spaceid'));
    }

    /**
     * 获取邀请链接
     *
     * @param string $userid
     * @param string $spaceid
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93656#%E8%8E%B7%E5%8F%96%E9%82%80%E8%AF%B7%E9%93%BE%E6%8E%A5
     */
    public function share($userid, $spaceid)
    {
        return $this->httpPostJson('cgi-bin/wedrive/space_share', compact('userid', 'spaceid'));
    }

    /**
     * 安全设置
     *
     * @param string $userid
     * @param string $spaceid
     * @param array  $settings 设置
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93656#%E5%AE%89%E5%85%A8%E8%AE%BE%E7%BD%AE
     */
    public function setting($userid, $spaceid, $settings)
    {
        $data = array_merge(compact('userid', 'spaceid'), $settings);

        return $this->httpPostJson('cgi-bin/wedrive/space_setting', $data);
    }

    /**
     * 添加成员/部门
     *
     * @param string $userid
     * @param string $spaceid
     * @param array $authInfo  被添加的空间成员信息
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93656#%E6%B7%BB%E5%8A%A0%E6%88%90%E5%91%98%E9%83%A8%E9%97%A8
     */
    public function aclAdd($userid, $spaceid, $authInfo)
    {
        $data = [
            'userid' => $userid,
            'spaceid' => $spaceid,
            'auth_info' => Arr::isAssoc($authInfo) ? [$authInfo] : $authInfo
        ];

        return $this->httpPostJson('cgi-bin/wedrive/space_acl_add', $data);
    }

    /**
     * 移除成员/部门
     *
     * @param string $userid
     * @param string $spaceid
     * @param array $authInfo  被移除的空间成员信息
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93656#%E6%B7%BB%E5%8A%A0%E6%88%90%E5%91%98%E9%83%A8%E9%97%A8
     */
    public function aclDel($userid, $spaceid, $authInfo)
    {
        $data = [
            'userid' => $userid,
            'spaceid' => $spaceid,
            'auth_info' => Arr::isAssoc($authInfo) ? [$authInfo] : $authInfo
        ];

        return $this->httpPostJson('cgi-bin/wedrive/space_acl_del', $data);
    }
}
