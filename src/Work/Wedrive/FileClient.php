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
use GuzzleHttp\Cookie\CookieJar;

/**
 * Class FileClient
 *
 * @author lio990527 <lio990527@163.com>
 */
class FileClient extends BaseClient
{
    /**
     * 新建文件/文档
     *
     * @param string $userid    操作者userid
     * @param string $spaceid   空间spaceid
     * @param string $fatherid  父目录fileid, 在根目录时为空间spaceid
     * @param int $fileType     文件类型, 1:文件夹 3:文档(文档) 4:文档(表格) 6:文档(幻灯片)
     * @param string $fileName  文件名字
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93657#%E6%96%B0%E5%BB%BA%E6%96%87%E4%BB%B6%E6%96%87%E6%A1%A3
     */
    public function create($userid, $spaceid, $fatherid, $fileType, $fileName)
    {
        $data = [
            'userid' => $userid,
            'spaceid' => $spaceid,
            'fatherid' => $fatherid,
            'file_type' => $fileType,
            'file_name' => $fileName
        ];

        return $this->httpPostJson('cgi-bin/wedrive/file_create', $data);
    }

    /**
     * 重命名文件
     *
     * @param string $userid    操作人userid
     * @param string $fileid    文件id
     * @param string $name      新文件名
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93657#%E9%87%8D%E5%91%BD%E5%90%8D%E6%96%87%E4%BB%B6
     */
    public function rename($userid, $fileid, $name)
    {
        $data = [
            'userid' => $userid,
            'fileid' => $fileid,
            'new_name' => $name
        ];

        return $this->httpPostJson('cgi-bin/wedrive/file_rename', $data);
    }

    /**
     * 移动文件
     *
     * @param string $userid
     * @param string|array $fileid  文件fileid
     * @param string $fatherid 目标目录的fileid
     * @param boolean $replace 重名时，是否覆盖 false:重名文件进行冲突重命名处理（移动后文件名格式如xxx(1).txt xxx(1).doc等）
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93657#%E7%A7%BB%E5%8A%A8%E6%96%87%E4%BB%B6
     */
    public function move($userid, $fileid, $fatherid, $replace = false)
    {
        $fileid = (array) $fileid;

        return $this->httpPostJson('cgi-bin/wedrive/file_move', compact('userid', 'fatherid', 'replace', 'fileid'));
    }

    /**
     * 删除文件
     *
     * @param string $userid
     * @param string|array $fileid
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93657#%E5%88%A0%E9%99%A4%E6%96%87%E4%BB%B6
     */
    public function delete($userid, $fileid)
    {
        $fileid = (array) $fileid;

        return $this->httpPostJson('cgi-bin/wedrive/file_delete', compact('userid', 'fileid'));
    }

    /**
     * 获取文件列表
     *
     * @param string $userid
     * @param string $spaceid   空间id
     * @param string $fatherid  目录id
     * @param integer $start    查询游标 首次填0, 后续填上一次请求返回的next_start
     * @param integer $limit    拉取条数 最大1000
     * @param integer $sort     列表排序方式 1:名字升序；2:名字降序；3:大小升序；4:大小降序；5:修改时间升序；6:修改时间降序
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93657#%E8%8E%B7%E5%8F%96%E6%96%87%E4%BB%B6%E5%88%97%E8%A1%A8
     */
    public function list($userid, $spaceid, $fatherid = null, $start = 0, $limit = 1000, $sort = 1)
    {
        $data = [
            'userid' => $userid,
            'spaceid' => $spaceid,
            'fatherid' => $fatherid ?: $spaceid,
            'start' => $start,
            'limit' => min($limit, 1000),
            'sort_type' => $sort
        ];

        return $this->httpPostJson('cgi-bin/wedrive/file_list', $data);
    }

    /**
     * 文件信息
     *
     * @param string $userid
     * @param string $fileid
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93657#%E6%96%87%E4%BB%B6%E4%BF%A1%E6%81%AF
     */
    public function info($userid, $fileid)
    {
        return $this->httpPostJson('cgi-bin/wedrive/file_info', compact('userid', 'fileid'));
    }

    /**
     * 上传文件
     *
     * @param string $userid
     * @param string $spaceid       空间id
     * @param string $fatherid      目录id
     * @param string $fileName      文件名
     * @param string $fileContent   文件内容
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93657#%E4%B8%8A%E4%BC%A0%E6%96%87%E4%BB%B6
     */
    public function upload($userid, $spaceid, $fatherid, $fileName, $fileContent)
    {
        $data = [
            'userid' => $userid,
            'spaceid' => $spaceid,
            'fatherid' => $fatherid,
            'file_name' => $fileName,
            'file_base64_content' => base64_encode($fileContent)
        ];

        return $this->httpPostJson('cgi-bin/wedrive/file_upload', $data);
    }

    /**
     * 文件下载
     *
     * @param string $userid
     * @param string $fileid
     * @param boolean $download 是否直接下载
     *
     * @return \EasyWeChat\Kernel\Http\StreamResponse|\Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93657#%E4%B8%8B%E8%BD%BD%E6%96%87%E4%BB%B6
     */
    public function download($userid, $fileid, $download = false)
    {
        $response = $this->httpPostJson('cgi-bin/wedrive/file_download', compact('userid', 'fileid'));

        if (!$download || 0 <> $response['errcode']) {
            return $response;
        }

        return $this->getHttpClient()->request('GET', $response['download_url'], [
            'cookies' => CookieJar::fromArray([
                $response['cookie_name'] => $response['cookie_value'],
            ], parse_url($response['download_url'])['host'] ?? 'qq.com'),
        ]);
    }

    /**
     * 获取分享链接
     *
     * @param string $userid
     * @param string $fileid
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93658#%E8%8E%B7%E5%8F%96%E5%88%86%E4%BA%AB%E9%93%BE%E6%8E%A5
     */
    public function share($userid, $fileid)
    {
        return $this->httpPostJson('cgi-bin/wedrive/file_share', compact('userid', 'fileid'));
    }

    /**
     * 分享设置
     *
     * @param string $userid
     * @param string $fileid
     * @param int $authScope    权限范围：1:指定人 2:企业内 3:企业外
     * @param int $auth         权限信息 权限信息
     * 普通文档： 1:仅浏览（可下载) 4:仅预览（仅专业版企业可设置）；如果不填充此字段为保持原有状态
     * 微文档： 1:仅浏览（可下载） 2:可编辑；如果不填充此字段为保持原有状态
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93658#%E5%88%86%E4%BA%AB%E8%AE%BE%E7%BD%AE
     */
    public function setting($userid, $fileid, $authScope, $auth = null)
    {
        $data = [
            'userid' => $userid,
            'fileid' => $fileid,
            'auth_scope' => $authScope,
            'auth' => $auth
        ];

        return $this->httpPostJson('cgi-bin/wedrive/file_setting', array_filter($data));
    }

    /**
     * 新增成员
     *
     * @param string $userid
     * @param string $fileid
     * @param array $authInfo 添加成员的信息
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93658#%E6%96%B0%E5%A2%9E%E6%88%90%E5%91%98
     */
    public function aclAdd($userid, $fileid, $authInfo)
    {
        $data = [
            'userid' => $userid,
            'fileid' => $fileid,
            'auth_info' => Arr::isAssoc($authInfo) ? [$authInfo] : $authInfo
        ];

        return $this->httpPostJson('cgi-bin/wedrive/file_acl_add', $data);
    }

    /**
     * 删除成员
     *
     * @param string $userid
     * @param string $fileid
     * @param array $authInfo 删除成员的信息
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @see https://developer.work.weixin.qq.com/document/path/93658#%E5%88%A0%E9%99%A4%E6%88%90%E5%91%98
     */
    public function aclDel($userid, $fileid, $authInfo)
    {
        $data = [
            'userid' => $userid,
            'fileid' => $fileid,
            'auth_info' => Arr::isAssoc($authInfo) ? [$authInfo] : $authInfo
        ];

        return $this->httpPostJson('cgi-bin/wedrive/file_acl_del', $data);
    }
}
