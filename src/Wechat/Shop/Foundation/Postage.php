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

namespace Overtrue\Wechat\Shop\Foundation;

/**
 * 邮费接口.
 *
 * Interface Postage
 */
interface Postage
{
    /**
     * 添加运费模板
     *
     * @param $name
     * @param $assumer
     * @param $valuation
     * @param $topFee
     *
     * @return int
     */
    public function add($name, $topFee, $assumer = 0, $valuation = 0);

    /**
     * 删除邮费模板
     *
     * @param $templateId
     *
     * @return bool
     */
    public function delete($templateId);

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
     */
    public function update($templateId, $name, $topFee, $assumer = 0, $valuation = 0);

    /**
     * @param $templateId
     *
     * @return array
     */
    public function getById($templateId);

    /**
     * 获得全部的邮费模板
     *
     * @return mixed
     */
    public function lists();
}
