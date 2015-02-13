<?php

namespace Overtrue\Wechat\Services;

use Exception;
use Overtrue\Wechat\Wechat;
use Overtrue\Wechat\Messages\BaseMessage;

class Staff
{
    /**
     * 消息
     *
     * @var \Overtrue\Wechat\Messages\BaseMessage;
     */
    protected $message;

    /**
     * 指定消息发送客服账号
     *
     * @var string
     */
    protected $by;

    /**
     * 请求的headers
     *
     * @var array
     */
    protected $headers = array('content-type:application/json');

    const API_GET           = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist';
    const API_ONLINE        = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist';
    const API_DELETE        = 'https://api.weixin.qq.com/customservice/kfaccount/del';
    const API_UPDATE        = 'https://api.weixin.qq.com/customservice/kfaccount/update';
    const API_CREATE        = 'https://api.weixin.qq.com/customservice/kfaccount/add';
    const API_MESSAGE_SEND  = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';
    const API_AVATAR_UPLOAD = 'http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg';


    /**
     * 获取所有的客服
     *
     * @return array
     */
    public function all()
    {
        $response = Wechat::request('GET', self::API_GET);

        return $response['kf_list'];
    }

    /**
     * 获取所有在线的
     *
     * @return array
     */
    public function allOnline()
    {
        $response = Wechat::request('GET', self::API_GET);

        return $response['kf_online_list'];
    }

    /**
     * 添加客服账号
     *
     * @param string $email
     * @param string $nickname
     * @param string $password
     *
     * @return boolean
     */
    public function create($email, $nickname, $password)
    {
        $params = array(
                   "kf_account" => $email,
                   "nickname"   => $nickname,
                   "password"   => $password,
                  );

        return Wechat::request('POST', self::API_CREATE, $params);
    }

    /**
     * 修改客服账号
     *
     * @param string $email
     * @param string $nickname
     * @param string $password
     *
     * @return boolean
     */
    public function update($email, $nickname, $password)
    {
        $params = array(
                   "kf_account" => $email,
                   "nickname"   => $nickname,
                   "password"   => $password,
                  );

        return Wechat::request('POST', self::API_UPDATE, $params);
    }

    /**
     * 删除客服账号
     *
     * @param string $email
     * @param string $nickname
     * @param string $password
     *
     * @return boolean
     */
    public function delete($email, $nickname, $password)
    {
        $params = array(
                   "kf_account" => $email,
                   "nickname"   => $nickname,
                   "password"   => $password,
                  );

        return Wechat::request('POST', self::API_UPDATE, $params);
    }

    /**
     * 上传头像
     *
     * @param string $email
     * @param string $path
     *
     * @return boolean
     */
    public function avatar($email, $path)
    {
        $options = array(
                    'headers' => array('content-type:application/json'),
                    'files'   => array(
                                  'media' => $path,
                                 ),
                   );

        $url = self::API_AVATAR_UPLOAD . "?kf_account={$email}";

        return Wechat::request('POST', $url, array(), $options);
    }

    /**
     * 准备消息
     *
     * @param \Overtrue\Wechat\Messages\BaseMessage $message
     *
     * @return Staff
     */
    public function send($message)
    {
        is_string($message) && $message = Message::make('text')->with('content', $message);

        if (!$message instanceof BaseMessage) {
            throw new Exception("消息必须继承自 'Overtrue\Wechat\Services\BaseMessage'");
        }

        $this->message = $message;

        return $this;
    }

    /**
     * 指定客服
     *
     * @param string $account
     *
     * @return Staff
     */
    public function by($account)
    {
        $this->message->staff = $account;

        return $this;
    }

    /**
     * 发送消息
     *
     * @param string $openId
     *
     * @return boolean
     */
    public function to($openId)
    {
        if (empty($this->message)) {
            throw new Exception("未设置要发送的消息");
        }

        $this->message->to = $openId;

        Wechat::request('POST', self::API_MESSAGE_SEND, $this->message->buildForStaff());

        return true;
    }

    /**
     * 发送给所有人
     *
     * @return boolean
     */
    public function toAll()
    {
        if (empty($this->message)) {
            throw new Exception("未设置要发送的消息");
        }

        return true;
    }

    /**
     * 发送给组
     *
     * @return boolean
     */
    public function toGroup()
    {
        if (empty($this->message)) {
            throw new Exception("未设置要发送的消息");
        }

        return true;
    }

    /**
     * 发送给多人
     *
     * @return boolean
     */
    public function toThem(array $openIds)
    {
        if (empty($this->message)) {
            throw new Exception("未设置要发送的消息");
        }

        return true;
    }
}