<?php

/**
 * Staff.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Staff;

use EasyWeChat\Core\Http;

/**
 * Class Staff.
 */
class Staff
{
    const API_LISTS = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist';
    const API_ONLINE = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist';
    const API_DELETE = 'https://api.weixin.qq.com/customservice/kfaccount/del';
    const API_UPDATE = 'https://api.weixin.qq.com/customservice/kfaccount/update';
    const API_CREATE = 'https://api.weixin.qq.com/customservice/kfaccount/add';
    const API_MESSAGE_SEND = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';
    const API_AVATAR_UPLOAD = 'http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg';

    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    /**
     * Constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http->setExpectedException(StaffHttpException::class);
    }

    /**
     * List all staffs.
     *
     * @return array
     */
    public function lists()
    {
        $response = $this->http->get(self::API_LISTS);

        return $response['kf_list'];
    }

    /**
     * List all online staffs.
     *
     * @return array
     */
    public function onlines()
    {
        $response = $this->http->get(self::API_ONLINE);

        return $response['kf_online_list'];
    }

    /**
     * Create a staff.
     *
     * @param string $email
     * @param string $nickname
     * @param string $password
     *
     * @return bool
     */
    public function create($email, $nickname, $password)
    {
        $params = [
                   'kf_account' => $email,
                   'nickname' => $nickname,
                   'password' => $password,
                  ];

        return $this->http->json(self::API_CREATE, $params);
    }

    /**
     * Update a staff.
     *
     * @param string $email
     * @param string $nickname
     * @param string $password
     *
     * @return bool
     */
    public function update($email, $nickname, $password)
    {
        $params = [
                   'kf_account' => $email,
                   'nickname' => $nickname,
                   'password' => $password,
                  ];

        return $this->http->json(self::API_UPDATE, $params);
    }

    /**
     * Delete a staff.
     *
     * @param string $email
     *
     * @return bool
     */
    public function delete($email)
    {
        $params = [
                    'kf_account' => $email,
                  ];

        return $this->http->get(self::API_DELETE, $params);
    }

    /**
     * Set staff avatar.
     *
     * @param string $email
     * @param string $path
     *
     * @return bool
     */
    public function avatar($email, $path)
    {
        return $this->http->upload(self::API_AVATAR_UPLOAD, ['media' => $path], ['kf_account' => $email]);
    }
}
