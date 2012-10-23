<?PHP
	/*
		Search classes parent and child
	*/
	class SearchDisplay {
		public function Searchresult($query,$search_par) {
			if($search_par[0]=='All'){
				$string='';
			}
			elseif($search_par[0]=='Defect'){
					$string='%20AND%20categories:quality';
				}
			elseif($search_par[0]=='WorkO'){
					$string='%20AND%20categories:workorder';
				}
			//$url = SOLR_URL_STRING.urlencode($query).$string.'&start=0&rows=200000&sort=docid%20desc';
			$url = SOLR_URL_STRING.'(('.urlencode($query).')'.$string.')&start=0&rows=20000&sort=docid%20desc';
			$ch = curl_init();
			$request='<request>'.$request.'</request>';
			curl_setopt($ch, CURLOPT_URL, $url);
			if(defined('HTTP_PROXY') && HTTP_PROXY!='')
			{
					curl_setopt($ch,CURLOPT_PROXY,HTTP_PROXY);
					curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL,true);
					curl_setopt($ch,CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
			}
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
			$xml = (array)simplexml_load_string($response);
			return $xml;
		}
		
	}

?>
