<?php 

/**
  * Oauth2 SocialAuth for CodeIgniter
  * 棱镜 Provider
  * 
  * @author     chekun <234267695@qq.com>
  */
namespace MasApi\Oauth2\Provider;

use MasApi\Oauth2\Utils\Http;
use MasApi\Oauth2\Token\OAuth2_Token_Access;
use MasApi\Oauth2\OAuth2_Exception;
use MasApi\Oauth2\OAuth2_Token;
use MasApi\Oauth2\OAuth2_Provider;

class Oauth2_Provider_Prism extends Oauth2_Provider {

    const API_URL = 'http://prism-dev.masengine.com/app/index.php/';
    //const API_URL = '10.1.1.198/prism/app/index.php/';

    public $name = 'prism';

    public $human = '棱镜';

    public $uid_key = 'appid';

    public $client_id_key = 'client_id';

    public $client_secret_key = 'client_secret';

    protected $scope ='base';

    public $method = 'POST';
    
    public function scope_min()
    {
         $this->scope = 'snsapi_base';
    }
    public function url_authorize()
    {
        return 'http://prism-dev.masengine.com/app/index.php/oauth/AuthCode_Controller/authorize';
        //return 'oauth/AuthCode_Controller/authorize';
    }

    public function url_access_token()
    {
        return 'http://prism-dev.masengine.com/app/index.php/oauth/AuthCode_Controller/accessToken';
        //return 'oauth/AuthCode_Controller/accessToken';
    }

    public function get_user_info(OAuth2_Token_Access $token)
    {
  //       $ci =& get_instance();
		// $ci->load->library('curl');
        $opts = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(array(
                    'access_token' => $token->access_token,
                    'lang' => 'zh_CN'
                ))
            )
        );

        $_default_opts = stream_context_get_params(stream_context_get_default());
        $context = stream_context_create(array_merge_recursive($_default_opts['options'], $opts));
        $url = static::API_URL . 'oauth/Api_Controller/oauthUserInfo';
        
        //$user = json_decode(file_get_contents($url, false, $context),true);
        //$user = json_decode(file_get_contents($url));
        $params = array(
            'access_token' => $token->access_token,
            'lang' => 'zh_CN'
        );
        // $user = json_decode($ci->curl->simple_post($url,$params),true);
        $user = json_decode(Http::post($url,$params),true);
        if (array_key_exists("errcode", $user)) {
            throw new OAuth2_Exception((array) $user);
        }
        return $user;
        //return array(
            //'via' => 'weixin',
            //'screen_name' => $user->nickname,
            //'name' => $user->nickname,
            //'location' => $user->province,
            //'description' => '',
            //'image' => $user->headimgurl,
            //'access_token' => $token->access_token,
            //'expire_at' => $token->expires,
            //'refresh_token' => $token->refresh_token
        //);
    }
}
