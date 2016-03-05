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
 * TopFee.php.
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

namespace Overtrue\Wechat\Shop\Data;

use Overtrue\Wechat\Shop\Foundation\ShopsException;
use Overtrue\Wechat\Shop\Postage;
use Overtrue\Wechat\Utils\MagicAttributes;

/**
 * Class TopFee.
 *
 * @property $custom
 * @property $normal
 * @property $topFee
 */
class TopFee extends MagicAttributes
{
    /**
     * 设置TopFee.
     *
     * @param string $type
     *
     * @return $this
     *
     * @throws ShopsException
     */
    public function setTopFee($type = Postage::KUAI_DI)
    {
        if (!isset($this->normal) && !isset($this->custom)) {
            throw new ShopsException('normal 和　custom　必须设置');
        }
        if (!isset($this->normal) || !isset($this->custom)) {
            throw new ShopsException('normal 和　custom　必须全部设置');
        }
        $keys = array_keys($this->custom);

        foreach ($keys as $v) {
            if (!is_numeric($v)) {
                throw new ShopsException('数据不合法');
            }
        }

        $this->attributes['topFee'][] = array(
            'Type' => $type,
            'Normal' => $this->normal,
            'Custom' => $this->custom,
        );

        return $this;
    }

    /**
     * 设置 Normal.
     *
     * @param $startStandards
     * @param $startFees
     * @param $addStandards
     * @param $addFees
     *
     * @return $this
     */
    public function setNormal($startStandards, $startFees, $addStandards, $addFees)
    {
        $this->normal = array(
            'StartStandards' => $startStandards,
            'StartFees' => $startFees,
            'AddStandards' => $addStandards,
            'AddFees' => $addFees,
        );

        return $this;
    }

    /**
     * 设置Custom.
     *
     * @param $startStandards
     * @param $startFees
     * @param $addStandards
     * @param $addFees
     * @param $destProvince
     * @param null   $destCity
     * @param string $destCountry
     *
     * @return $this
     *
     * @throws ShopsException
     */
    public function setCustom(
        $startStandards, $startFees, $addStandards, $addFees,
        $destProvince, $destCity = null, $destCountry = '中国'
    ) {
        if (empty($destProvince)) {
            throw new ShopsException('$destProvince不允许为空');
        }

        $this->custom = array();

        //todo 未做反选，排除一个城市，选择其他

        if (empty($destCity)) {

            //todo  $destProvince的判断
            //todo 　加入　全国省直辖市的　简称等
            //todo　加入　某些不平等条约的存在　例如　江浙沪　，你们懂得！！

            $destProvince = is_string($destProvince) ? array($destProvince) : $destProvince;

            $regional = new Regional();

            foreach ($destProvince as $province) {
                $citys = $regional->getCity($province);

                if (empty($citys)) {
                    throw new ShopsException('请传入合法的省份名!!!');
                }

                foreach ($citys[0] as $city) {
                    $this->attributes['custom'][] = array(
                        'StartStandards' => $startStandards,
                        'StartFees' => $startFees,
                        'AddStandards' => $addStandards,
                        'AddFees' => $addFees,
                        'DestCountry' => $destCountry,
                        'DestProvince' => $province,
                        'DestCity' => $city,
                    );
                }
            }

            return $this;
        } else {

            //todo 未做省份的检测
            //todo 未做市检测

            $destCity = is_string($destCity) ? array($destCity) : $destCity;

            if (is_array($destCity)) {
                foreach ($destCity as $city) {
                    $this->attributes['custom'][] = array(
                        'StartStandards' => $startStandards,
                        'StartFees' => $startFees,
                        'AddStandards' => $addStandards,
                        'AddFees' => $addFees,
                        'DestCountry' => $destCountry,
                        'DestProvince' => $destProvince,
                        'DestCity' => $city,
                    );
                }
            }

            return $this;
        }
    }

    public function __get($property)
    {
        return parent::__get($property);
    }

    public function __isset($property)
    {
        return isset($this->attributes[$this->snake($property)]);
    }
}
