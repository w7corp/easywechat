<?php
/**
 * TestBase.php
 *
 * Part of Overtrue\Wechat\Test
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a9396 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Test;


use Overtrue\Wechat\Shop\Config;
use Overtrue\Wechat\Shop\AccessToken;
use Overtrue\Wechat\Http;
use Symfony\Component\Yaml\Yaml;

class TestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var accessToken
     */
    protected $accessToken;
    /**
     * @var HTTP
     */
    protected $http;

    protected function setUp()
    {
        $config = __DIR__.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'Config.yml';
        $cert = __DIR__.DIRECTORY_SEPARATOR.'Cert'.DIRECTORY_SEPARATOR;

        //yml::载入配置
        $data = Yaml::parse(file_get_contents($config));

        $this->config = new Config($data['appId'],$data['appSecret'],$data['debug']);
        $this->config->setMessageConfig($data['token'],$data['encodingAESKey']);
        $this->config->setPayConfig($data['mchId'],$data['mchId']);

        $data['clientCert'] = $cert.'apiclient_cert.pem';
        $data['clientKey'] = $cert.'apiclient_key.pem';

        file_put_contents($config,Yaml::dump($data));

        $data = Yaml::parse(file_get_contents($config));

        $this->config->setPEMConfig($data['clientCert'],$data['clientKey']);

        $this->accessToken = new AccessToken($this->config);

        $this->http = new Http($this->accessToken);
    }
}