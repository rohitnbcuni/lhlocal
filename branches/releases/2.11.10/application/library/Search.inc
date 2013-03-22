<?PHP
	/*
		Search classes parent and child
	*/
	class SearchDisplay {
		public function Searchresult($query,$search_par, $type='') {
			if($search_par[0]=='All'){
				$string='';
			}
			elseif($search_par[0]=='Defect'){
					$string='%20AND%20categories:quality';
				}
			elseif($search_par[0]=='WorkO'){
					$string='%20AND%20categories:workorder';
				}
			$query = htmlspecialchars($query);
			//$url = SOLR_URL_STRING.urlencode($query).$string.'&start=0&rows=200000&sort=docid%20desc';
			if($type == ''){
				$url = SOLR_URL_STRING.'(("'.urlencode($query).'")'.$string.')&start=0&rows=2000&sort=docid%20desc';
			}else if($type == 'dym'){
				$search_string = urlencode($query);
				define("SOLR_URL_SPELLCHECKER_STRING",str_replace("select?q=","spellIndex?wt=json&spellcheck=true&spellcheck.q=",SOLR_URL_STRING));
				$url = SOLR_URL_SPELLCHECKER_STRING.$search_string;
				
			}
			$ch = curl_init();
			$request='<request>'.$request.'</request>';
			curl_setopt($ch, CURLOPT_URL, $url);
			if(defined('HTTP_PROXY') && HTTP_PROXY!='')
			{
					curl_setopt($ch,CURLOPT_PROXY,HTTP_PROXY);
					curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL,true);
					curl_setopt($ch,CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
			}
			//Uncomment if you want to run on Local system
			//curl_setopt($ch, CURLOPT_PROXY, "http://64.210.197.20:80");
			//curl_setopt($ch, CURLOPT_PROXYPORT, 80);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $request);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_USERPWD,BC_USER . ":" . BC_PASSWORD);
			if(preg_match('/^(http)/',$url)) curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
			$response = curl_exec($ch);
			curl_close($ch);
			if($type == ''){
				$xml = (array)simplexml_load_string($response);
			}else if($type == 'dym'){
				$response = json_decode($response,true);
				if(ISSET($response['spellcheck']['suggestions'])){
				$spellCheck = $response['spellcheck']['suggestions'];
				foreach($spellCheck as $spellCheck_key => $spellCheck_value){
					if(is_array($spellCheck_value)){
						if(ISSET($spellCheck_value[0])){
							if($spellCheck_value[0] == 'collationQuery'){
								if(ISSET($spellCheck_value[1])){
									$xml = $spellCheck_value[1];
								}
							
							}
						}
					}			
				}
				}
			
			}
			
			return $xml;
		}
		
		
		static public function insertSearchLog($data){
			try{
				$db = Zend_Registry::get('db');
				$db->insert("search_log", $data);
			}catch(Exception $e){
				
				//echo "update_event_audit".$e->getMessage();
			}
		
		
		}
		
		
		
		
	}

?>
