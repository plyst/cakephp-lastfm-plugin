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
	 * @return An array containing the requested data
	 */
	public function get($method, $params = null) {
		$url = "$this->baseurl?format=json&api_key=$this->apikey&method=$method";
		if ($params!=null) {
			foreach ($params as $key => $value) {
				$url .= "&$key=$value";
			}
		}
		debug($url);
		return json_decode($this->get_data($url), true);
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