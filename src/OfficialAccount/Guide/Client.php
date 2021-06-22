<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Guide;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Support\Collection;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client.
 *
 * @author MillsGuo <millsguo@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 添加顾问
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $guideHeadImgUrl
     * @param string $guideNickname
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function createAdviser($guideAccount = '', $guideOpenid = '', $guideHeadImgUrl = '', $guideNickname = '')
    {
        $params = $this->selectAccountAndOpenid(array(), $guideAccount, $guideOpenid);
        if (!empty($guideHeadImgUrl)) {
            $params['guide_headimgurl'] = $guideHeadImgUrl;
        }
        if (!empty($guideNickname)) {
            $params['guide_nickname'] = $guideNickname;
        }

        return $this->httpPostJson('cgi-bin/guide/addguideacct', $params);
    }

    /**
     * 获取顾问信息
     * @param string $guideAccount
     * @param string $guideOpenid
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getAdviser($guideAccount = '', $guideOpenid = '')
    {
        $params = $this->selectAccountAndOpenid(array(), $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/getguideacct', $params);
    }

    /**
     * 修改顾问的昵称或头像
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $guideHeadImgUrl
     * @param string $guideNickname
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function updateAdviser($guideAccount = '', $guideOpenid = '', $guideHeadImgUrl = '', $guideNickname = '')
    {
        $params = $this->selectAccountAndOpenid(array(), $guideAccount, $guideOpenid);
        if (!empty($guideHeadImgUrl)) {
            $params['guide_headimgurl'] = $guideHeadImgUrl;
        }
        if (!empty($guideNickname)) {
            $params['guide_nickname'] = $guideNickname;
        }

        return $this->httpPostJson('cgi-bin/guide/updateguideacct', $params);
    }

    /**
     * 删除顾问
     * @param string $guideAccount
     * @param string $guideOpenid
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function deleteAdviser($guideAccount = '', $guideOpenid = '')
    {
        $params = $this->selectAccountAndOpenid(array(), $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/delguideacct', $params);
    }

    /**
     * 获取服务号顾问列表
     *
     * @return mixed
     *
     * @throws InvalidConfigException
     */
    public function getAdvisers($count, $page)
    {
        $params = [
            'page' => $page,
            'num' => $count
        ];

        return $this->httpPostJson('cgi-bin/guide/getguideacctlist', $params);
    }

    /**
     * 生成顾问二维码
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $qrCodeInfo
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function createQrCode($guideAccount = '', $guideOpenid = '', $qrCodeInfo = '')
    {
        $params = $this->selectAccountAndOpenid(array(), $guideAccount, $guideOpenid);
        if (!empty($qrCodeInfo)) {
            $params['qrcode_info'] = $qrCodeInfo;
        }

        return $this->httpPostJson('cgi-bin/guide/guidecreateqrcode', $params);
    }

    /**
     * 获取顾问聊天记录
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $openid
     * @param int $beginTime
     * @param int $endTime
     * @param int $page
     * @param int $count
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getBuyerChatRecords($guideAccount = '', $guideOpenid = '', $openid = '', $beginTime = 0, $endTime = 0, $page = 1, $count = 100)
    {
        $params = [
            'page' => $page,
            'num' => $count
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);
        if (!empty($openid)) {
            $params['openid'] = $openid;
        }
        if (!empty($beginTime)) {
            $params['begin_time'] = $beginTime;
        }
        if (!empty($endTime)) {
            $params['end_time'] = $endTime;
        }

        return $this->httpPostJson('cgi-bin/guide/getguidebuyerchatrecord', $params);
    }

    /**
     * 设置快捷回复与关注自动回复
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param bool $isDelete
     * @param array $fastReplyListArray
     * @param array $guideAutoReply
     * @param array $guideAutoReplyPlus
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function setConfig($guideAccount = '', $guideOpenid = '', $isDelete = false, $fastReplyListArray = array(), $guideAutoReply = array(), $guideAutoReplyPlus = array())
    {
        $params = [
            'is_delete' => $isDelete
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);
        if (!empty($fastReplyListArray)) {
            $params['guide_fast_reply_list'] = $fastReplyListArray;
        }
        if (!empty($guideAutoReply)) {
            $params['guide_auto_reply'] = $guideAutoReply;
        }
        if (!empty($guideAutoReplyPlus)) {
            $params['guide_auto_reply_plus'] = $guideAutoReplyPlus;
        }

        return $this->httpPostJson('cgi-bin/guide/setguideconfig', $params);
    }

    /**
     * 获取快捷回复与关注自动回复
     * @param string $guideAccount
     * @param string $guideOpenid
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getConfig($guideAccount = '', $guideOpenid = '')
    {
        try {
            $params = $this->selectAccountAndOpenid(array(), $guideAccount, $guideOpenid);
        } catch (InvalidConfigException $e) {
            $params = array();
        }

        return $this->httpPostJson('cgi-bin/guide/getguideconfig', $params);
    }

    /**
     * 设置离线自动回复与敏感词
     * @param bool $isDelete
     * @param array $blackKeyword
     * @param array $guideAutoReply
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function setAdviserConfig(bool $isDelete, array $blackKeyword = [], array $guideAutoReply = [])
    {
        $params = [
            'is_delete' => $isDelete
        ];
        if (!empty($blackKeyword)) {
            $params['black_keyword'] = $blackKeyword;
        }
        if (!empty($guideAutoReply)) {
            $params['guide_auto_reply'] = $guideAutoReply;
        }

        return $this->httpPostJson('cgi-bin/guide/setguideacctconfig', $params);
    }

    /**
     * 获取离线自动回复与敏感词
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getAdviserConfig()
    {
        return $this->httpPostJson('cgi-bin/guide/getguideacctconfig', array());
    }

    /**
     * 允许微信用户复制小程序页面路径
     * @param string $wxaAppid 小程序APPID
     * @param string $wxUsername 微信用户的微信号
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function allowCopyMiniAppPath(string $wxaAppid, string $wxUsername)
    {
        $params = [
            'wxa_appid' => $wxaAppid,
            'wx_username' => $wxUsername
        ];

        return $this->httpPostJson('cgi-bin/guide/pushshowwxapathmenu', $params);
    }

    /**
     * 传入微信号或OPENID二选一
     * @param array $params
     * @param string $guideAccount
     * @param string $guideOpenid
     * @return array
     * @throws InvalidConfigException
     */
    protected function selectAccountAndOpenid($params, $guideAccount = '', $guideOpenid = '')
    {
        if (!is_array($params)) {
            throw new InvalidConfigException("传入配置参数必须为数组");
        }
        if (!empty($guideOpenid)) {
            $params['guide_openid'] = $guideOpenid;
        } elseif (!empty($guideAccount)) {
            $params['guide_account'] = $guideAccount;
        } else {
            throw new InvalidConfigException("微信号和OPENID不能同时为空");
        }

        return $params;
    }

    /**
     * 新建顾问分组
     * @param string $groupName
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function createGroup(string $groupName)
    {
        $params = [
            'group_name' => $groupName
        ];

        return $this->httpPostJson('cgi-bin/guide/newguidegroup', $params);
    }

    /**
     * 获取顾问分组列表
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getGuideGroups()
    {
        return $this->httpPostJson('cgi-bin/guide/getguidegrouplist', array());
    }

    /**
     * 获取指定顾问分组信息
     * @param int $groupId
     * @param int $page
     * @param int $num
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getGroups(int $groupId, int $page, int $num)
    {
        $params = [
            'group_id' => $groupId,
            'page' => $page,
            'num' => $num
        ];

        return $this->httpPostJson('cgi-bin/guide/getgroupinfo', $params);
    }

    /**
     * 分组内添加顾问
     * @param int $groupId
     * @param string $guideAccount
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function addGroupGuide(int $groupId, string $guideAccount)
    {
        $params = [
            'group_id' => $groupId,
            'gruide_account' => $guideAccount
        ];

        return $this->httpPostJson('cgi-bin/guide/addguide2guidegroup', $params);
    }

    /**
     * 分组内删除顾问
     * @param int $groupId
     * @param string $guideAccount
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function deleteGroupGuide(int $groupId, string $guideAccount)
    {
        $params = [
            'group_id' => $groupId,
            'guide_account' => $guideAccount
        ];

        return $this->httpPostJson('cgi-bin/guide/delguide2guidegroup', $params);
    }

    /**
     * 获取顾问所在分组
     * @param string $guideAccount
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getGuideGroup(string $guideAccount)
    {
        $params = [
            'guide_account' => $guideAccount
        ];

        return $this->httpPostJson('cgi-bin/guide/getgroupbyguide', $params);
    }

    /**
     * 删除指定顾问分组
     * @param int $groupId
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function deleteGroup(int $groupId)
    {
        $params = [
            'group_id' => $groupId
        ];

        return $this->httpPostJson('cgi-bin/guide/delguidegroup', $params);
    }

    /**
     * 为顾问分配客户
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param array $buyerList
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function createBuyerRelation(string $guideAccount, string $guideOpenid, array $buyerList)
    {
        $params = [
            'buyer_list' => $buyerList
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/addguidebuyerrelation', $params);
    }

    /**
     * 为顾问移除客户
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param array $openidList
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function deleteBuyerRelation(string $guideAccount, string $guideOpenid, array $openidList)
    {
        $params = [
            'openid_list' => $openidList
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/delguidebuyerrelation', $params);
    }

    /**
     * 获取顾问的客户列表
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param int $page
     * @param int $num
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getBuyerRelations(string $guideAccount, string $guideOpenid, int $page, int $num)
    {
        $params = [
            'page' => $page,
            'num' => $num
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/getguidebuyerrelationlist', $params);
    }

    /**
     * 为客户更换顾问
     * @param string $oldGuideTarget
     * @param string $newGuideTarget
     * @param array $openidList
     * @param bool $useTargetOpenid true使用OPENID，false使用微信号
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function rebindBuyerGuide(string $oldGuideTarget, string $newGuideTarget, array $openidList, bool $useTargetOpenid = true)
    {
        $params = [
            'openid_list' => $openidList
        ];
        if ($useTargetOpenid) {
            $params['old_guide_openid'] = $oldGuideTarget;
            $params['new_guide_openid'] = $newGuideTarget;
        } else {
            $params['old_guide_account'] = $oldGuideTarget;
            $params['new_guide_account'] = $newGuideTarget;
        }

        return $this->httpPostJson('cgi-bin/guide/rebindguideacctforbuyer', $params);
    }

    /**
     * 修改客户昵称
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $openid
     * @param string $nickname
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function updateBuyerRelation(string $guideAccount, string $guideOpenid, string $openid, string $nickname)
    {
        $params = [
            'openid' => $openid,
            'buyer_nickname' => $nickname
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/updateguidebuyerrelation', $params);
    }

    /**
     * 查询客户所属顾问
     * @param string $openid
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getBuyerRelation(string $openid)
    {
        $params = [
            'openid' => $openid
        ];

        return $this->httpPostJson('cgi-bin/guide/getguidebuyerrelationbybuyer', $params);
    }

    /**
     * 查询指定顾问和客户的关系
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $openid
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getBuyerRelationByGuide(string $guideAccount, string $guideOpenid, string $openid)
    {
        $params = [
            'openid' => $openid
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/getguidebuyerrelation', $params);
    }

    /**
     * 新建可查询的标签类型
     * @param string $tagName
     * @param array $tagValues
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function newTagOption(string $tagName, array $tagValues)
    {
        $params = [
            'tag_name' => $tagName,
            'tag_values' => $tagValues
        ];

        return $this->httpPostJson('cgi-bin/guide/newguidetagoption', $params);
    }

    /**
     * 删除指定标签类型
     * @param string $tagName
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function deleteTagOption(string $tagName)
    {
        $params = [
            'tag_name' => $tagName
        ];

        return $this->httpPostJson('cgi-bin/guide/delguidetagoption', $params);
    }

    /**
     * 为标签添加可选值
     * @param string $tagName
     * @param array $tagValues
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function createTagOption(string $tagName, array $tagValues)
    {
        $params = [
            'tag_name' => $tagName,
            'tag_values' => $tagValues
        ];

        return $this->httpPostJson('cgi-bin/guide/addguidetagoption', $params);
    }

    /**
     * 获取标签和可选值
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getTagOption()
    {
        return $this->httpPostJson('cgi-bin/guide/getguidetagoption', array());
    }

    /**
     * 为客户设置标签
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param array $openidList
     * @param string $tagValue
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function setBuyersTag(string $guideAccount, string $guideOpenid, array $openidList, string $tagValue)
    {
        $params = [
            'tag_value' => $tagValue,
            'openid_list' => $openidList
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/addguidebuyertag', $params);
    }

    /**
     * 查询客户标签
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $openid
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getBuyerTags(string $guideAccount, string $guideOpenid, string $openid)
    {
        $params = [
            'openid' => $openid
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/getguidebuyertag', $params);
    }

    /**
     * 根据标签值筛选粉丝
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param int $pushCount
     * @param array $tagValues
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getBuyerByTag(string $guideAccount, string $guideOpenid, int $pushCount = 0, array $tagValues = array())
    {
        $params = $this->selectAccountAndOpenid(array(), $guideAccount, $guideOpenid);
        if ($pushCount > 0) {
            $params['push_count'] = $pushCount;
        }
        if (count($tagValues) > 0) {
            $params['tag_values'] = $tagValues;
        }

        return $this->httpPostJson('cgi-bin/guide/queryguidebuyerbytag', $params);
    }

    /**
     * 删除客户标签
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $tagValue
     * @param array $openidList
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function deleteBuyerTag(string $guideAccount, string $guideOpenid, string $tagValue, array $openidList)
    {
        $params = [
            'tag_value' => $tagValue
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);
        if (count($openidList) > 0) {
            $params['openid_list'] = $openidList;
        }

        return $this->httpPostJson('cgi-bin/guide/delguidebuyertag', $params);
    }

    /**
     * 设置自定义客户信息
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $openid
     * @param array $displayTagList
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function setBuyerDisplayTags(string $guideAccount, string $guideOpenid, string $openid, array $displayTagList)
    {
        $params = [
            'openid' => $openid,
            'display_tag_list' => $displayTagList
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/addguidebuyerdisplaytag', $params);
    }

    /**
     * 获取自定义客户信息
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $openid
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getBuyerDisplayTags(string $guideAccount, string $guideOpenid, string $openid)
    {
        $params = [
            'openid' => $openid
        ];
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/getguidebuyerdisplaytag', $params);
    }

    /**
     * 添加小程序卡片素材
     * @param string $mediaId
     * @param string $title
     * @param string $path
     * @param string $appid
     * @param int $type
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function createCardMaterial(string $mediaId, string $title, string $path, string $appid, int $type = 0)
    {
        $params = [
            'media_id' => $mediaId,
            'type' => $type,
            'title' => $title,
            'path' => $path,
            'appid' => $appid
        ];

        return $this->httpPostJson('cgi-bin/guide/setguidecardmaterial', $params);
    }

    /**
     * 查询小程序卡片素材
     * @param int $type
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getCardMaterial(int $type = 0)
    {
        $params = [
            'type' => $type
        ];

        return $this->httpPostJson('cgi-bin/guide/getguidecardmaterial', $params);
    }

    /**
     * 删除小程序卡片素材
     * @param string $title
     * @param string $path
     * @param string $appid
     * @param int $type
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function deleteCardMaterial(string $title, string $path, string $appid, int $type = 0)
    {
        $params = [
            'type' => $type,
            'title' => $title,
            'path' => $path,
            'appid' => $appid
        ];

        return $this->httpPostJson('cgi-bin/guide/delguidecardmaterial', $params);
    }

    /**
     * 添加图片素材
     * @param string $mediaId
     * @param int $type
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function createImageMaterial(string $mediaId, int $type = 0)
    {
        $params = [
            'media_id' => $mediaId,
            'type' => $type
        ];

        return $this->httpPostJson('cgi-bin/guide/setguideimagematerial', $params);
    }

    /**
     * 查询图片素材
     * @param int $type
     * @param int $start
     * @param int $num
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getImageMaterial(int $type, int $start, int $num)
    {
        $params = [
            'type' => $type,
            'start' => $start,
            'num' => $num
        ];

        return $this->httpPostJson('cgi-bin/guide/getguideimagematerial', $params);
    }

    /**
     * 删除图片素材
     * @param int $type
     * @param string $picUrl
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function deleteImageMaterial(int $type, string $picUrl)
    {
        $params = [
            'type' => $type,
            'picurl' => $picUrl
        ];

        return $this->httpPostJson('cgi-bin/guide/delguideimagematerial', $params);
    }

    /**
     * 添加文字素材
     * @param int $type
     * @param string $word
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function createWordMaterial(int $type, string $word)
    {
        $params = [
            'type' => $type,
            'word' => $word
        ];

        return $this->httpPostJson('cgi-bin/guide/setguidewordmaterial', $params);
    }

    /**
     * 查询文字素材
     * @param int $type
     * @param int $start
     * @param int $num
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getWordMaterial(int $type, int $start, int $num)
    {
        $params = [
            'type' => $type,
            'start' => $start,
            'num' => $num
        ];

        return $this->httpPostJson('cgi-bin/guide/getguidewordmaterial', $params);
    }

    /**
     * 删除文字素材
     * @param int $type
     * @param string $word
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function deleteWordMaterial(int $type, string $word)
    {
        $params = [
            'type' => $type,
            'word' => $word
        ];

        return $this->httpPostJson('cgi-bin/guide/delguidewordmaterial', $params);
    }

    /**
     * 添加群发任务，为指定顾问添加群发任务
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param string $taskName
     * @param string $taskRemark
     * @param int $pushTime
     * @param array $openidArray
     * @param array $materialArray
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function createMasSendJob(string $guideAccount, string $guideOpenid, string $taskName, string $taskRemark, int $pushTime, array $openidArray, array $materialArray)
    {
        $params = [
            'task_name' => $taskName,
            'push_time' => $pushTime,
            'openid' => $openidArray,
            'material' => $materialArray
        ];
        if (!empty($taskRemark)) {
            $params['task_remark'] = $taskRemark;
        }
        $params = $this->selectAccountAndOpenid($params, $guideAccount, $guideOpenid);

        return $this->httpPostJson('cgi-bin/guide/addguidemassendjob', $params);
    }

    /**
     * 获取群发任务列表
     * @param string $guideAccount
     * @param string $guideOpenid
     * @param array $taskStatus
     * @param int $offset
     * @param int $limit
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getMasSendJobs(string $guideAccount, string $guideOpenid, array $taskStatus = [], int $offset = 0, int $limit = 50)
    {
        $params = $this->selectAccountAndOpenid(array(), $guideAccount, $guideOpenid);
        if (!empty($taskStatus)) {
            $params['task_status'] = $taskStatus;
        }
        if ($offset > 0) {
            $params['offset'] = $offset;
        }
        if ($limit != 50) {
            $params['limit'] = $limit;
        }

        return $this->httpPostJson('cgi-bin/guide/getguidemassendjoblist', $params);
    }

    /**
     * 获取指定群发任务信息
     * @param string $taskId
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getMasSendJob(string $taskId)
    {
        $params = [
            'task_id' => $taskId
        ];

        return $this->httpPostJson('cgi-bin/guide/getguidemassendjob', $params);
    }

    /**
     * 修改群发任务
     * @param string $taskId
     * @param string $taskName
     * @param string $taskRemark
     * @param int $pushTime
     * @param array $openidArray
     * @param array $materialArray
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function updateMasSendJob(string $taskId, string $taskName, string $taskRemark, int $pushTime, array $openidArray, array $materialArray)
    {
        $params = [
            'task_id' => $taskId
        ];
        if (!empty($taskName)) {
            $params['task_name'] = $taskName;
        }
        if (!empty($taskRemark)) {
            $params['task_remark'] = $taskRemark;
        }
        if (!empty($pushTime)) {
            $params['push_time'] = $pushTime;
        }
        if (!empty($openidArray)) {
            $params['openid'] = $openidArray;
        }
        if (!empty($materialArray)) {
            $params['material'] = $materialArray;
        }

        return $this->httpPostJson('cgi-bin/guide/updateguidemassendjob', $params);
    }

    /**
     * 取消群发任务
     * @param string $taskId
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function cancelMasSendJob(string $taskId)
    {
        $params = [
            'task_id' => $taskId
        ];

        return $this->httpPostJson('cgi-bin/guide/cancelguidemassendjob', $params);
    }
}
