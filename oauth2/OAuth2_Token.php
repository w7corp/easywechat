<?php
namespace MasApi\Oauth2;
/**
 * OAuth2 Token
 *
 * @package    OAuth2
 * @category   Token
 * @author     Phil Sturgeon
 * @copyright  (c) 2011 HappyNinjas Ltd
 */

abstract class OAuth2_Token {

	/**
	 * Create a new token object.
	 *
	 *     $token = OAuth2_Token::factory($name);
	 *
	 * @param   string  $name     token type
	 * @param   array   $options  token options
	 * @return  OAuth2_Token
	 */
	public static function factory($name = 'access', array $options = null)
	{
		$name = ucfirst(strtolower($name));
		include_once 'Token/'.$name.'.php';

		$class = 'OAuth2_Token_'.$name;

		return new $class($options);
	}

	/**
	 * Return the value of any protected class variable.
	 *
	 *     // Get the token secret
	 *     $secret = $token->secret;
	 *
	 * @param   string  $key  variable name
	 * @return  mixed
	 */
	public function __get($key)
	{
		return $this->$key;
	}

	/**
	 * Return a boolean if the property is set
	 *
	 *     // Get the token secret
	 *     if ($token->secret) exit('YAY SECRET');
	 *
	 * @param   string  $key  variable name
	 * @return  bool
	 */
	public function __isset($key)
	{
		return isset($this->$key);
	}

} // End Token
