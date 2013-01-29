<?php 
include('../_inc/config.inc');
include("sessionHandler.php");
$results = array();
//$search_str = trim(str_replace('"','&quot;',$_GET['letters']));
$search_str = htmlspecialchars($_GET['letters']);
if($search_str != ''){
	$output = '';
	//echo getAdavanceSearch($search_str);
	$output = getGroupBySearch($search_str);
	if(trim($output) == ''){
		$output = getAdavanceSearch($search_str);

	}
	echo $output;
		
	}
	
function getGroupBySearch($search_str){
		define("_LIMIT_",10);
		$result_str = '';
		$search_string = urlencode($search_str);
		//$search_string = '"'.urlencode($search_str).'"';
		$search_par = trim($_GET['filters']);
		if($search_par=='All'){
			$string='';
		}
		elseif($search_par=='Defect'){
			$string='%20AND%20categories:quality';
		}
		elseif($search_par=='WorkO'){
			$string='%20AND%20categories:workorder';
		}		
		$int_id = (int) $search_str;
		$doc_id = (!empty($int_id))?"%20OR%20docid:".$int_id:'';
		$url = SOLR_URL_STRING."*:*&facet=true&rows=0&facet.prefix=$search_string&facet.field=title_facet&facet.limit="._LIMIT_."&wt=json";
	
		$response = createCurlReuest($url);
		
		if(ISSET($response)){
			$xml = json_decode($response); 
			$jsonOutput = $xml->facet_counts->facet_fields->title_facet;
			if(count($jsonOutput) > 0){
				foreach($jsonOutput as $key => $val){
					if($key%2 == 0){
						$result_str .= ($key+1)."###<a  href='javascript:void(0);'>".ucfirst($val)."|"; 
					}
				}
			
			}
		}
		return $result_str;
	}	


function getAdavanceSearch($search_str){
			$result_str = '';
			//$search_string = $search_str;
			$search_string = '"'.urlencode($search_str).'"';
			$search_par = trim($_GET['filters']);
			if($search_par=='All'){
				$string='';
			}
			elseif($search_par=='Defect'){
				$string='%20AND%20categories:quality';
			}
			elseif($search_par=='WorkO'){
				$string='%20AND%20categories:workorder';
			}		
			$int_id = (int) $search_str;
			$doc_id = (!empty($int_id))?"%20OR%20docid:".$int_id:'';
			
			$url = SOLR_URL_STRING.'((title:'.$search_string.'%20OR%20description:'.$search_string.$doc_id.')'.$string.')&featureClass=P&style=full&start=0&rows=10&sort=docid%20desc&name_startsWith='.$search_string;

			$response = createCurlReuest($url);
				
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
			for($i =1 ; $i<count($id); $i++){
				if($cat[$i][0]=='quality'){
					$link = $pageURL.'/quality/index/edit/?defect_id=';
				}else{
					$link = $pageURL.'/workorders/index/edit/?wo_id='; 
				}
			//$result_str .= $id[$i][0]."###".'<a  href="'.$link.$id[$i][0].'">'.ucfirst($title[$i][0]).'<a/>'."|";
			  $result_str .= $id[$i][0]."###".'<a  href="javascript:void(0);">'.ucfirst($title[$i][0]).'<a/>'."|";
			// echo $title[$i][0];
			}
			return $result_str;
		}

		function createCurlReuest($url){
			try
			{
				$ch = curl_init();
				$request='<request>'.$request.'</request>';
				curl_setopt($ch, CURLOPT_URL, $url);
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
				if($response == false){
					//throw new Exception('Bad Request');
				
				}
				curl_close($ch);
				return $response;
			}
			catch( Exception $e)
			{
				$strResponse = "";
				$strErrorCode = $e->getCode();
				$strErrorMessage = $e->getMessage();
				
			} 
		}
 			


?>

