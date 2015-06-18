<?php 
namespace MasApi\Oauth2;
/**
 * OAuth2 Provider
 *
 * @package    CodeIgniter/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 */

 /**
  * Oauth2 SocialAuth for CodeIgniter
  * 修改自 https://github.com/philsturgeon/codeigniter-oauth2
  * 
  * @author     chekun <234267695@qq.com>
  */
use MasApi\Oauth2\Utils\Http;
use MasApi\Oauth2\OAuth2_Exception;
use MasApi\Oauth2\Token\OAuth2_Token_Access;

abstract class OAuth2_Provider
{
	/**
	 * @var  string  provider name
	 */
	public $name;
	
    /**
     * Http对象
     *
     * @var Http
     */
    public $http;

        
        /**
	 * @var  string  provider human name
	 */
	public $human;
        
        /**
	 * @var  string  state key name, for some unfollowing spec kids
	 */
	public $state_key = 'state';
        
        /**
	 * @var  string  error key name, for some unfollowing spec kids
	 */
	public $error_key = 'error';
        
        /**
	 * @var  string  client_id key name, for some unfollowing spec kids
	 */
	public $client_id_key = 'client_id';
        
        /**
	 * @var  string  client_secret key name, for some unfollowing spec kids
	 */
	public $client_secret_key = 'client_secret';
        
        /**
	 * @var  string  redirect_uri key name, for some unfollowing spec kids
	 */
	public $redirect_uri_key = 'redirect_uri';
        
        /**
	 * @var  string  access_token key name, for some unfollowing spec kids
	 */
	public $access_token_key = 'access_token';
        
	/**
	 * @var  string  uid key name name, for some unfollowing spec kids
	 */
	public $uid_key = 'uid';

	/**
	 * @var  string  additional request parameters to be used for remote requests
	 */
	public $callback;

	/**
	 * @var  array  additional request parameters to be used for remote requests
	 */
	protected $params = array();

	/**
	 * @var  string  the method to use when requesting tokens
	 */
	protected $method = 'GET';

	/**
	 * @var  string  default scope (useful if a scope is required for user info)
	 */
	protected $scope;

	/**
	 * @var  string  scope separator, most use "," but some like Google are spaces
	 */
	protected $scope_seperator = ',';

	/**
	 * Overloads default class properties from the options.
	 *
	 * Any of the provider options can be set here, such as app_id or secret.
	 *
	 * @param   array $options provider options
	 * @throws  Exception if a required option is not provided
	 */
	public function __construct(array $options = array())
	{
		$this->http = new Http();
		if ( ! $this->name)
		{
			// Attempt to guess the name from the class name
			$this->name = strtolower(substr(get_class($this), strlen('OAuth2_Provider_')));
		}

		if (empty($options['id']))
		{
			throw new OAuth2_Exception(array('code' => '403', 'message' => 'Required option not provided: id'));
		}

		$this->client_id = $options['id'];
		
		isset($options['callback']) and $this->callback = $options['callback'];
		isset($options['secret']) and $this->client_secret = $options['secret'];
		isset($options['scope']) and $this->scope = $options['scope'];

		// $this->redirect_uri = site_url(get_instance()->uri->uri_string());
		if (strpos($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"],'?')) {
			$this->redirect_uri = mb_strcut("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"],0,strpos("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"],'?'));
		}else{
			$this->redirect_uri = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		}
	}

	/**
	 * Return the value of any protected class variable.
	 *
	 *     // Get the provider signature
	 *     $signature = $provider->signature;
	 *
	 * @param   string $key variable name
	 * @return  mixed
	 */
	public function __get($key)
	{
		return $this->$key;
	}

	/**
	 * Returns the authorization URL for the provider.
	 *
	 *     $url = $provider->url_authorize();
	 *
	 * @return  string
	 */
	abstract public function url_authorize();

	/**
	 * Returns the access token endpoint for the provider.
	 *
	 *     $url = $provider->url_access_token();
	 *
	 * @return  string
	 */
	abstract public function url_access_token();

	/**
	 * @param OAuth2_Token_Access $token
	 * @return array basic user info
	 */
	abstract public function get_user_info(OAuth2_Token_Access $token);

	/*
	* Get an authorization code from Provider Service.  Redirects to Provider Authorization Page, which this redirects back to the app using the redirect address you've set.
	*/	
	public function authorize($options = array())
	{
		$state = md5(uniqid(rand(), true));
		// get_instance()->session->set_userdata('state', $state);
		$_SESSION['state'] = $state;
		$params = array(
			$this->client_id_key 		=> $this->client_id,
			$this->redirect_uri_key 	=> isset($options[$this->redirect_uri_key]) ? $options[$this->redirect_uri_key] : $this->redirect_uri,
			$this->state_key 		=> $state,
			'scope'				=> is_array($this->scope) ? implode($this->scope_seperator, $this->scope) : $this->scope,
			'response_type' 	=> 'code',
			'approval_prompt'   => 'force' // - google force-recheck
		);
		
		$params = array_merge($params, $this->params);
		
		// redirect($this->url_authorize().'?'.http_build_query($params));
		header('Location: '.$this->url_authorize().'?'.http_build_query($params));
	}

	/*
	* Get access to the API
	*
	* @param	string	The access code
	* @return	object	Success or failure along with the response details
	*/	
	public function access($code, $options = array())
	{
  //       $ci =& get_instance();
		// $ci->load->library('curl');
      	//check we csrf first
        // if (isset($_GET[$this->state_key]) AND $_GET[$this->state_key] != get_instance()->session->userdata('state'))
        if (isset($_GET[$this->state_key]) AND $_GET[$this->state_key] != $_SESSION['state'])
        {
        	throw new OAuth2_Exception(array('code' => '403', 'message' => 'The state does not match. Maybe you are a victim of CSRF.'));
        }
		$params = array(
			$this->client_id_key 	=> $this->client_id,
			$this->client_secret_key => $this->client_secret,
			'grant_type' 	=> isset($options['grant_type']) ? $options['grant_type'] : 'authorization_code',
		);
		
		$params = array_merge($params, $this->params);
		switch ($params['grant_type'])
		{
			case 'authorization_code':
				$params['code'] = $code;
				$params[$this->redirect_uri_key] = isset($options[$this->redirect_uri_key]) ? $options[$this->redirect_uri_key] : $this->redirect_uri;
			break;

			case 'refresh_token':
				$params['refresh_token'] = $code;
			break;
		}

		$response = null;
        $url = $this->url_access_token();
        //echo 'method........'.$this->method;
        //echo "<hr>";
		switch ($this->method)
		{
			case 'GET':
				$url .= '?'.http_build_query($params);
        //echo 'url'.$url;
        //echo "<hr>";
				// $response = file_get_contents($url);
                // $response = $ci->curl->ssl(false)->simple_get($url);
                $response = $this->http->get($url);
				$return = $this->parse_response($response);

			break;

			case 'POST':
				// $opts = array(
				// 	'http' => array(
				// 		'method'  => 'POST',
				// 		'header'  => 'Content-type: application/x-www-form-urlencoded',
				// 		'content' => http_build_query($params),
				// 	)
				// );

				// $_default_opts = stream_context_get_params(stream_context_get_default());
				// $context = stream_context_create(array_merge_recursive($_default_opts['options'], $opts));
				// $response = file_get_contents($url, false, $context);
        //echo 'url'.$url;
        //echo "<hr>";
                // $response = $ci->curl->ssl(false)->simple_post($url,$params);
                $response = $this->http->post($url,$params);
                $return = $this->parse_response($response['data']);
			break;

			default:
				throw new OutOfBoundsException("Method '{$this->method}' must be either GET or POST");
		}
        //echo "<pre>";
        //print_r($params);
        //echo "<hr>";
        //print_r($return);
        //exit;
		if ( ! empty($return[$this->error_key]) OR ! isset($return['access_token']))
		{
			throw new OAuth2_Exception($return);
		}
                
		$return['uid_key'] = $this->uid_key;
        $return['access_token_key'] = $this->access_token_key;
		switch ($params['grant_type'])
		{
			case 'authorization_code':
				return OAuth2_Token::factory('access', $return);
			break;

			case 'refresh_token':
				return OAuth2_Token::factory('refresh', $return);
			break;
		}
		
	}
        
    protected function parse_response($response = '')
    {
    	if (strpos($response, "callback") !== false)
        {
        	$lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $return = json_decode($response, true);
        }
        elseif (strpos($response, "&") !== false)
        {
            parse_str($response, $return);
                                
        }
        else
        {
            $return = json_decode($response, true);
        }
        return $return;
    }

}
