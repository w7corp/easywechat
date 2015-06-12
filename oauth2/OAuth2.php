<?php
namespace MasApi\Oauth2;

/**
 * OAuth2.0
 *
 * @author Phil Sturgeon < @philsturgeon >
 */
class OAuth2 {

	/**
	 * Create a new provider.
	 *
	 *     // Load the Twitter provider
	 *     $provider = $this->oauth2->provider('twitter');
	 *
	 * @param   string $name    provider name
	 * @param   array  $options provider options
	 * @return  OAuth2_Provider
	 */
	public static function provider($name, array $options = NULL)
	{
		$name = ucfirst(strtolower($name));

		include_once 'Provider/Oauth2_Provider_'.$name.'.php';

		$class = "MasApi\\Oauth2\\Provider\\Oauth2_Provider_".$name;
		return new $class($options);
	}

}
