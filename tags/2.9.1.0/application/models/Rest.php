<?php

/*
$Id$
*/

class Rest{

	var $host;

	var $username;

	var $password;

	var $conn;

	var $cache;

	var $cacheTTL = 0; // cache lifetime of 2 hours

	var $cacheDir = '/tmp/'; // Directory where to put the cache files

	function __construct( $host, $username, $password ){

        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
		$this->connect();

		$this->initCache();

	}
	
	function connect(){
				
		if (!isset($GLOBALS['REST'])){
			$REST = curl_init();

			curl_setopt($REST, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($REST, CURLOPT_POST, 1);
			curl_setopt($REST, CURLOPT_HEADER, false);
			curl_setopt($REST, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
			curl_setopt($REST, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($REST, CURLOPT_USERPWD, $this->username . ":" . $this->password);
			$GLOBALS['REST'] = $REST;
		}

        $this->conn = $GLOBALS['REST'];
	}

	function request( $url, $xml = null ){
		$request_url = $this->host . $url;
		$cache_name = str_replace('/','',$request_url);
		$cache_name = str_replace(':','',$cache_name);
		$cache_name = str_replace('.','',$cache_name);

		if ( $result = $this->cache->load( $cache_name )){
			return $result;
		}

		curl_setopt($this->conn, CURLOPT_URL, $request_url );
		if(ereg("^(https)",$request_url)) curl_setopt($this->conn,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($this->conn, CURLOPT_POSTFIELDS, $xml);
		$response = curl_exec($this->conn);
		if(!$response){ $response = curl_error($this->conn); }
		curl_close($this->conn);

		$return_response = $this->http_error_check( $response );

		$this->cache->save( $return_response, $cache_name );
		return $return_response;
	}

	function initCache(){
		$frontendOptions = array(
			'lifetime' => $this->cacheTTL,
			'automatic_serialization' => true
		);

		$backendOptions = array(
			'cache_dir' => $this->cacheDir
		);

		// getting a Zend_Cache_Core object
		$this->cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
	}

	function http_error_check( $response ){
		// Get HTTP Status code from the response
		$status_code = array();
		preg_match('/\d\d\d/', $response, $status_code);

		// Check the HTTP Status code
		switch( $status_code[0] ) {
			case 200:
				// Success
				break;
			case 503:
				break;
			case 403:
				break;
			case 400:
				// You may want to fall through here and read the specific XML error
				break;
			default:
				//unexpected HTTP status of:' . $status_code[0]);
		}
		
		return $response;
	}

	function simplexml2ISOarray($xml,$attribsAsElements=0) {
		
		if (get_class($xml) == 'SimpleXMLElement') {
			$attributes = $xml->attributes();
			foreach($attributes as $k=>$v) {
				if ($v) $a[$k] = (string) $v;
			}
			$x = $xml;
			$xml = get_object_vars($xml);
		}
		if (is_array($xml)) {
			if (count($xml) == 0) return (string) $x; // for CDATA
			foreach($xml as $key=>$value) {
				$r[$key] = self::simplexml2ISOarray($value,$attribsAsElements);
				if (!is_array($r[$key])) $r[$key] = utf8_decode($r[$key]);
			}
			if (isset($a)) {
				if($attribsAsElements) {
					$r = array_merge($a,$r);
				} else {
					$r['@'] = $a; // Attributes
				}
			}
			return $r;
		}
		return (string) $xml;
	}

			
}

?>
