<?PHP
	/*
		Search classes parent and child
	*/
	class SearchDisplay {
	
		const limit = 2000;
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
				$url = SOLR_URL_STRING.'(("'.urlencode($query).'")'.$string.')&start=0&rows='.self::limit.'&sort=docid%20desc';
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
		public function advanceSearchresult($search_par_all,$search_par_atleastone,$search_par_without,$date_range,$search_fields) {
			$string = '';
			$boolean_string = '';
			//$search_par_all.','.$search_par_atleastone.','.$search_par_without;
			if((!empty($search_par_all)) && (!empty($search_par_atleastone)) && (!empty($search_par_without))){
				$boolean_string = '("'.urlencode($search_par_all).'")%20OR%20("'.urlencode($search_par_atleastone).'")%20NOT%20("'.urlencode($search_par_without).'"))';
			
			}
			if((!empty($search_par_all)) && (empty($search_par_atleastone)) && (empty($search_par_without))){
				$boolean_string = '("'.urlencode($search_par_all).'"))';
			
			}
			if((empty($search_par_all)) && (!empty($search_par_atleastone)) && (empty($search_par_without))){
				$boolean_string = '("'.urlencode($search_par_atleastone).'"))';
			
			}
			if((empty($search_par_all)) && (!empty($search_par_atleastone)) && (!empty($search_par_without))){
				$boolean_string = '("'.urlencode($search_par_atleastone).'")%20NOT%20("'.urlencode($search_par_without).'"))';
			}
			if((!empty($search_par_all)) && (empty($search_par_atleastone)) && (!empty($search_par_without))){
				$boolean_string = '("'.urlencode($search_par_all).'")%20NOT%20("'.urlencode($search_par_without).'"))';
			
			}
			if((!empty($search_par_all)) && (!empty($search_par_atleastone)) && (empty($search_par_without))){
				$boolean_string = '("'.urlencode($search_par_all).'")%20OR%20("'.urlencode($search_par_atleastone).'"))';
			
			}
			//relevancy Search
			//ec2-75-101-162-191.compute-1.amazonaws.com:8080/solr/lighthouse_active/select?q=("Can I get an update on this one")&qf=title description commentTextList&start=0&rows=10&defType=edismax
			
			if((!empty($date_range['startDate'])) AND (!empty($date_range['endDate']))){
				$boolean_string = $boolean_string.'%20AND%20(createdDate:['.urlencode(date("Y-m-d",strtotime($date_range['startDate']))).'T01:00:00Z%20TO%20'.urlencode(date("Y-m-d",strtotime($date_range['endDate']))).'T23:59:59Z]';
				
			}else{
				$boolean_string = "(".$boolean_string;
			}
			if(count($search_fields)){
				$search_fields_str = urlencode(implode(" ",$search_fields));
			}
	
			$url = SOLR_URL_STRING.'('.$boolean_string.')&start=0&rows='.self::limit.'&qf='.$search_fields_str.'&defType=edismax&sort=docid%20desc'; 
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
			$xml = (array)simplexml_load_string($response);
			//print_r($xml);
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
