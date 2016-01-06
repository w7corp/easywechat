<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Postage.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a939638621 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 *
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Shop;

use Overtrue\Wechat\Shop\Data\TopFee;
use Overtrue\Wechat\Shop\Foundation\Base;
use Overtrue\Wechat\Shop\Foundation\Postage as PostageInterface;
use Overtrue\Wechat\Shop\Foundation\ShopsException;

/**
 * 邮费模板
 *
 * Class Postage
 *
 * @property $topFee
 */
class Postage extends Base implements PostageInterface
{
    const PING_YOU = '10000027';
    const KUAI_DI = '10000028';
    const EMS = '10000029';

    const API_ADD = 'https://api.weixin.qq.com/merchant/express/add';
    const API_DELETE = 'https://api.weixin.qq.com/merchant/express/del';
    const API_UPDATE = 'https://api.weixin.qq.com/merchant/express/update';
    const API_GET_BY_ID = 'https://api.weixin.qq.com/merchant/express/getbyid';
    const API_LISTS = 'https://api.weixin.qq.com/merchant/express/getall';

    /**
     * 添加邮费模板
     *
     * @param $name
     * @param null $topFee
     * @param int  $assumer
     * @param int  $valuation
     *
     * @return int
     *
     * @throws ShopsException
     */
    public function add($name, $topFee, $assumer = 0, $valuation = 0)
    {
        //todo 这个判断不知道为何会触发
//        if (!is_array($topFee) && !is_callable($topFee)) {
//            throw new ShopsException('....');
//        }

        if (is_callable($topFee)) {
            $topFeeData = call_user_func($topFee, new TopFee());

            if (!($topFeeData instanceof TopFee)) {
                throw new ShopsException('请返回 Shop\Data\TopFee class');
            }

            $topFee = $topFeeData->getData();
        }

        $this->response = $this->http->jsonPost(self::API_ADD, array(
            'delivery_template' => array(
                'Name' => $name,
                'Assumer' => $assumer,
                'Valuation' => $valuation,
                'TopFee' => $topFee,
            ),
        ));

        return $this->getResponse();
    }

    /**
     * 删除邮费模板
     *
     * @param $templateId
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function delete($templateId)
    {
        $this->response = $this->http->jsonPost(self::API_DELETE, array('template_id' => $templateId));

        return $this->getResponse();
    }

    /**
     * 修改邮费模板
     *
     * @param $templateId
     * @param $name
     * @param null $topFee
     * @param int  $assumer
     * @param int  $valuation
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function update($templateId, $name, $topFee, $assumer = 0, $valuation = 0)
    {
        if (is_callable($topFee)) {
            $topFee = call_user_func($topFee, new TopFee());

            if (!($topFee instanceof TopFee)) {
                throw new ShopsException('请返回 Shop\Data\TopFee class');
            }

            $topFee = $topFee->topFee;
        }

        //todo 这个判断不知道为何会触发
        //if (!is_array($topFee)) throw new ShopsException('请返回数组');

        $this->response = $this->http->jsonPost(self::API_UPDATE, array(
            'template_id' => $templateId,
            'delivery_template' => array(
                'Name' => $name,
                'Assumer' => $assumer,
                'Valuation' => $valuation,
                'TopFee' => $topFee,
            ),
        ));

        return $this->getResponse();
    }

    /**
     * 获取指定ID的邮费模板
     *
     * @param $templateId
     *
     * @return array
     *
     * @throws ShopsException
     */
    public function getById($templateId)
    {
        $this->response = $this->http->jsonPost(self::API_GET_BY_ID, array('template_id' => $templateId));

        return $this->getResponse();
    }

    /**
     * 获得全部的邮费模板
     *
     * @return array
     *
     * @throws ShopsException
     */
    public function lists()
    {
        $this->response = $this->http->get(self::API_LISTS);

        return $this->getResponse();
    }
}
