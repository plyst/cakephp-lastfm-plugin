<?php
/**
 * LastfmComponent.php - The main component file
 *
 * This is a plugin for CakePHP to connect your app with the last.fm API.
 * With this plugin it's possible to access the main API methods.
 *
 * @author Willi Thiel (ni-c@ni-c.de)
 *
 * CakePHP 2.x
 */
class LastfmComponent extends Component {
	
	private $baseurl = 'http://ws.audioscrobbler.com/2.0/';
	
	private $apikey = null;
	
	private $apisecret = null;

	/**
	 * Initializes the last.fm API
	 * 
	 * @param $apikey The API key for last.fm
	 * @param $apisecret The API secret for last.fm
	 * 	 */
	public function init($apikey, $apisecret) {
		$this->apikey = $apikey;
		$this->apisecret = $apisecret;
	}
		
	/**
	 * Request the last.fm api
	 * 
	 * @param $method The method of the API to call
	 * @param $params The API params
	 * @param $signed If the API method should be signed
	 * @return An array containing the requested data
	 */
	public function get($method, $params = null, $signed = false) {
		$url = "$this->baseurl?format=json&api_key=$this->apikey&method=$method";
		if ($params!=null) {
			foreach ($params as $key => $value) {
				$url .= "&$key=$value";
			}
		}
		if ($signed) {
			$orderedParams = array_merge($params, array('api_key' => $this->apikey, 'method' => $method));
			ksort($orderedParams);
			$str = '';
			foreach ($orderedParams as $key => $value) {
				$str .= $key . $value;
			}
			$str .= $this->apisecret;
			$url .= "&api_sig=" .  md5($str);
		}
		return json_decode($this->get_data($url), true);
	}
	
	/**
	 * Send your user to last.fm/api/auth with your API key as a parameter.
	 * 
	 * @param $callback_url You can optionally specify a callback URL that is different to your API Account callback url. Include this as a query param cb. This allows you to have users forward to a specific part of your site after the authorisation process.
	 */
	public function authorize($callback_url = null) {
		$url = "http://www.last.fm/api/auth/?api_key=$this->apikey";
		if ($callback_url!=null) {
			$url .= "&cb=$callback_url";
		}
		header("Location: $url"); 
	    exit();
	}
	
	/**
	 * Fetch a session key for a user.
	 * 
	 * @param $token A 32-character ASCII hexadecimal MD5 hash returned after calling the authorize() method
	 */
	public function getSession($token) {
		return $this->get('auth.getSession', array('token' => $token), true);
	}
	
	/**
	 * Performs a cURL request on the given URL and returns the response.
	 * 
	 * @param $url The URL to perform the cURL request to
	 * @return The response of the given URL
	 */
	private function get_data($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

}