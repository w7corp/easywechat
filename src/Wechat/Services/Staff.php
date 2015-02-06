<?php

namespace Overtrue\Wechat\Services;

class Staff extends Service
{
    const API_GET           = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist';
    const API_AVATAR_UPLOAD = 'http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg';
    const API_DELETE        = 'https://api.weixin.qq.com/customservice/kfaccount/del';
    const API_UPDATE        = 'https://api.weixin.qq.com/customservice/kfaccount/update';
    const API_CREATE        = 'https://api.weixin.qq.com/customservice/kfaccount/add';


    /**
     * 获取所有的客服
     *
     * @return array
     */
    public function all()
    {
        # code...
    }

    /**
     * 添加客服账号
     *
     * @param string $email
     * @param string $nickname
     * @param string $password
     *
     * @return array
     */
    public function create($email, $nickname, $password)
    {
        $params = array(
                   "kf_account" => $email,
                   "nickname"   => $nickname,
                   "password"   => $password,
                  );

        return $this->postRequest(self::API_CREATE, $params);
    }

    /**
     * 修改客服账号
     *
     * @param string $email
     * @param string $nickname
     * @param string $password
     *
     * @return array
     */
    public function update($email, $nickname, $password)
    {
        $params = array(
                   "kf_account" => $email,
                   "nickname"   => $nickname,
                   "password"   => $password,
                  );

        return $this->postRequest(self::API_UPDATE, $params);
    }

    /**
     * 删除客服账号
     *
     * @param string $email
     * @param string $nickname
     * @param string $password
     *
     * @return array
     */
    public function delete($email, $nickname, $password)
    {
        $params = array(
                   "kf_account" => $email,
                   "nickname"   => $nickname,
                   "password"   => $password,
                  );

        return $this->postRequest(self::API_UPDATE, $params);
    }

    public function avatar()
    {
        # code...
    }

    public function send()
    {
        # code...
    }
}