<?php 

/**
  * Oauth2 SocialAuth for CodeIgniter
  * 棱镜 Provider
  * 
  * @author     chekun <234267695@qq.com>
  */
namespace MasApi\Oauth2\Provider;

use MasApi\Oauth2\Utils\Curl;
use MasApi\Oauth2\Token\OAuth2_Token_Access;
use MasApi\Oauth2\OAuth2_Exception;
use MasApi\Oauth2\OAuth2_Provider;

class Oauth2_Provider_Prism extends Oauth2_Provider {


    const API_URL = 'https://prism-dev.masengine.com/app/index.php/';

    public $curl;

    public $name = 'prism';

    public $human = '棱镜';

    public $uid_key = 'appid';

    public $client_id_key = 'client_id';

    public $client_secret_key = 'client_secret';

    protected $scope ='base';

    public $method = 'POST';

    public function __construct($options)
    {
        parent::__construct($options);
        $this->curl = new Curl();
    }

    public function scope_min()
    {
         $this->scope = 'snsapi_base';
    }
    public function url_authorize()
    {
        return static::API_URL.'oauth/AuthCode_Controller/authorize';
    }

    public function url_access_token()
    {
        return static::API_URL.'oauth/AuthCode_Controller/accessToken';
    }

    public function get_user_info(OAuth2_Token_Access $token)
    {
        $url = static::API_URL . 'Papi/OauthUserInfo';
        
        $params = array(
            'access_token' => $token->access_token,
            'lang' => 'zh_CN'
        );
        $user = json_decode($this->curl->ssl(false)->simple_post($url,$params),true);
        if (array_key_exists("errcode", $user)) {
            throw new OAuth2_Exception((array) $user);
        }
        return $user;
    }
}
