<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Exmail;

use EasyWeChat\Kernel\BaseClient;

/**
 * Exmail Client.
 *
 * @author keller31 <xiaowei.vip@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 创建邮件群组
     * 该接口用于创建新邮件群组，可以指定群组成员，定义群组使用权限范围
     *
     * @link https://developer.work.weixin.qq.com/document/path/95510#%E5%88%9B%E5%BB%BA%E9%82%AE%E4%BB%B6%E7%BE%A4%E7%BB%84
     *
     * @param array $data 参数格式请参考文档
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createGroup(array $data)
    {
        return $this->httpPostJson('cgi-bin/exmail/group/create', $data);
    }

    /**
     * 更新邮件群组
     *
     * @link https://developer.work.weixin.qq.com/document/path/95510#%E6%9B%B4%E6%96%B0%E9%82%AE%E4%BB%B6%E7%BE%A4%E7%BB%84
     *
     * @param array $data 参数格式请参考文档
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateGroup(array $data)
    {
        return $this->httpPostJson('cgi-bin/exmail/group/update', $data);
    }

    /**
     * 删除邮件群组
     *
     * @link https://developer.work.weixin.qq.com/document/path/95510#%E5%88%A0%E9%99%A4%E9%82%AE%E4%BB%B6%E7%BE%A4%E7%BB%84
     *
     * @param string $groupid 邮件群组ID
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteGroup(string $groupid)
    {
        return $this->httpPostJson('cgi-bin/exmail/group/delete', [
            'groupid' => $groupid
        ]);
    }

    /**
     * 获取邮件详情
     *
     * @link https://developer.work.weixin.qq.com/document/path/95510#%E8%8E%B7%E5%8F%96%E9%82%AE%E4%BB%B6%E8%AF%A6%E6%83%85
     *
     * @param string $groupid 邮件群组ID
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGroup(string $groupid)
    {
        return $this->httpGet('cgi-bin/exmail/group/get', [
            'groupid' => $groupid
        ]);
    }

    /**
     * 模糊搜索邮件群组
     *
     * @link https://developer.work.weixin.qq.com/document/path/95510#%E6%A8%A1%E7%B3%8A%E6%90%9C%E7%B4%A2%E9%82%AE%E4%BB%B6%E7%BE%A4%E7%BB%84
     *
     * @param integer $fuzzy    1开启模糊搜索，0获取全部邮件群组
     * @param string $groupid   邮件群组ID，邮箱格式
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchGroup(int $fuzzy, string $groupid = '')
    {
        return $this->httpGet('cgi-bin/exmail/group/search', [
            'fuzzy' => $fuzzy,
            'groupid' => $groupid
        ]);
    }

    /**
     * 创建业务邮箱
     *
     * @link https://developer.work.weixin.qq.com/document/path/95511#%E5%88%9B%E5%BB%BA%E4%B8%9A%E5%8A%A1%E9%82%AE%E7%AE%B1
     *
     * @param array $data   参数格式请参考文档
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createPublicMail(array $data)
    {
        return $this->httpPostJson('cgi-bin/exmail/publicmail/create', $data);
    }

    /**
     * 更新业务邮箱
     *
     * @link https://developer.work.weixin.qq.com/document/path/95511#%E6%9B%B4%E6%96%B0%E4%B8%9A%E5%8A%A1%E9%82%AE%E7%AE%B1
     *
     * @param array $data   参数格式请参考文档
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updatePublicMail(array $data)
    {
        return $this->httpPostJson('cgi-bin/exmail/publicmail/update', $data);
    }

    /**
     * 删除业务邮箱
     *
     * @link https://developer.work.weixin.qq.com/document/path/95511#%E5%88%A0%E9%99%A4%E4%B8%9A%E5%8A%A1%E9%82%AE%E7%AE%B1
     *
     * @param integer $id   业务邮箱ID
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deletePublicMail(int $id)
    {
        return $this->httpPostJson('cgi-bin/exmail/publicmail/update', [
            'id' => $id
        ]);
    }

    /**
     * 获取业务邮箱详情
     *
     * @link https://developer.work.weixin.qq.com/document/path/95511#%E8%8E%B7%E5%8F%96%E4%B8%9A%E5%8A%A1%E9%82%AE%E7%AE%B1%E8%AF%A6%E6%83%85
     *
     * @param array $id_list 业务邮箱ID列表
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPublicMail(array $id_list)
    {
        return $this->httpPostJson('cgi-bin/exmail/publicmail/get', [
            'id_list' => $id_list
        ]);
    }

    /**
     * 模糊搜索业务邮箱
     *
     * @link https://developer.work.weixin.qq.com/document/path/95511#%E6%A8%A1%E7%B3%8A%E6%90%9C%E7%B4%A2%E4%B8%9A%E5%8A%A1%E9%82%AE%E7%AE%B1
     *
     * @param integer $fuzzy    1开启模糊搜索，0获取全部业务邮箱
     * @param string $email     业务邮箱名称或邮箱地址
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchPublicMail(int $fuzzy, string $email = '')
    {
        return $this->httpGet('cgi-bin/exmail/publicmail/search', [
            'fuzzy' => $fuzzy,
            'email' => $email
        ]);
    }

    /**
     * 账户控制 - 禁用/启用邮箱
     *
     * @link https://developer.work.weixin.qq.com/document/path/95512
     *
     * @param string $userid            成员UserID
     * @param string $publicemail_id    业务邮箱ID
     * @param integer $type             1启用，2禁用
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actAccount(string $userid = '', string $publicemail_id = '', int $type)
    {
        return $this->httpPostJson('cgi-bin/exmail/account/act_email', [
            'userid' => $userid,
            'publicemail_id' => $publicemail_id,
            'type' => $type,
        ]);
    }

    /**
     * 获取用户功能属性
     *
     * @link https://developer.work.weixin.qq.com/document/path/95513#%E8%8E%B7%E5%8F%96%E7%94%A8%E6%88%B7%E5%8A%9F%E8%83%BD%E5%B1%9E%E6%80%A7
     *
     * @param string $userid    用户UserID
     * @param array $type       功能设置属性类型 1: 强制启用安全登录 2: IMAP/SMTP服务 3: POP/SMTP服务 4: 是否启用安全登录
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserOption(string $userid, array $type)
    {
        return $this->httpPostJson('cgi-bin/exmail/useroption/get', [
            'userid' => $userid,
            'type' => $type,
        ]);
    }

    /**
     * 更改用户功能属性
     *
     * @link https://developer.work.weixin.qq.com/document/path/95513#%E6%9B%B4%E6%94%B9%E7%94%A8%E6%88%B7%E5%8A%9F%E8%83%BD%E5%B1%9E%E6%80%A7
     *
     * @param string $userid    用户UserID
     * @param array $option     功能设置  例[['type'=>1,'value'=>'0'],['type'=>2,'value'=>'1'],['type'=>3,'value'=>'0]]
     * @param integer $option[]['type]  功能设置属性类型 1: 强制启用安全登录 2: IMAP/SMTP服务 3: POP/SMTP服务 4: 是否启用安全登录
     * @param string $option[]['value] 1表示启用，0表示关闭
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateUserOption(string $userid, array $option)
    {
        return $this->httpPostJson('cgi-bin/exmail/useroption/update', [
            'userid' => $userid,
            'option' => [
                'list' => $option
            ],
        ]);
    }

    /**
     * 获取用户新邮件数
     *
     * @link https://developer.work.weixin.qq.com/document/path/95514#%E8%8E%B7%E5%8F%96%E7%94%A8%E6%88%B7%E6%96%B0%E9%82%AE%E4%BB%B6%E6%95%B0
     *
     * @param string $userid    成员UserID
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNewMailCount(string $userid)
    {
        return $this->httpPostJson('cgi-bin/exmail/mail/get_newcount', [
            'userid' => $userid
        ]);
    }
}
