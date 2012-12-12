<?php 
include('../_inc/config.inc');
include("sessionHandler.php");
$results = array();
$search_string = trim($_GET['letters']);
if($search_string != ''){
		$search_string = urlencode($search_string);
		$url = SOLR_URL_STRING.'((title:'.$search_string.'*%20OR%20description:'.$search_string.'*%20OR%20id:'.$search_string.'*))&featureClass=P&style=full&&start=0&rows=10&sort=docid%20desc&name_startsWith="'.$search_string.'"';

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
			
		if(ISSET($response) && strlen($response) > 10){
			$results = new SimpleXMLElement($response);
		}
		/*if(count($results) > 0){
			foreach($results['result'] as $key => $val){
				//$inf["ID"]."###".$inf["countryName"]."|";
				
				print_r($val);
				echo $val->arr->str[0]."###".$val->arr->str[0]."|";
				
			
			}*/

		if(count($results) > 0){
			foreach($results as $key =>$val){
				if($key == 'result'){
					$result_found = $val['numFound'];
					$searchResult =  $val->doc;
					if(count($searchResult) > 0){
					foreach($searchResult as $k => $v){
							if(count($searchResult) > 0){
								foreach($searchResult as $sKey => $eVal){
								/* $comments='';
								for($i=0;$i<count($eVal->arr[1]->str);$i++)
								{$no = $i+1;
								$comments .= $no.'.'. $eVal->arr[1]->str[$i].'</br>';
								}*/
								$id[] =   $eVal->long[0];
								$project_id[] =  $eVal->str[1];
									$title[] = $eVal->str[2];
								$urllink[] =  $eVal->arr->str[1];
								$desc[] =  $eVal->str[4];
								$cat[] =  $eVal->str[5];
								/* $comment[] =  (array) $comments ;*/
							/*	if( $eVal->str[5]=='quality'){
								$count_qa=$i++;}
								else{$count_wo=$wo++; }
								*/
								}

							}
						}

					}
				}

			}
		}
		$pageURL = BASE_URL;
		for($i =0 ; $i<count($id); $i++){
		if($cat[$i][0]=='quality'){$link = $pageURL.'/quality/index/edit/?defect_id=';}else{$link = $pageURL.'/workorders/index/edit/?wo_id='; }
		echo $id[$i][0]."###".'<a  href="'.$link.$id[$i][0].'">'.ucfirst($title[$i][0]).'<a/>'."|"; 
		// echo $title[$i][0];
		}
	}
 			


?>

