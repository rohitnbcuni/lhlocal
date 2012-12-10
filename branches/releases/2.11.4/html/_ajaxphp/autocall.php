<?php 
 $results = array();
$SOLR_URL_STRING = "http://ec2-75-101-162-191.compute-1.amazonaws.com:8080/solr/lighthouse_active/select?q=";
$url = $SOLR_URL_STRING.urlencode($_REQUEST['letters']).'&featureClass=P&style=full&maxRows=10&name_startsWith='.$_REQUEST[name_startsWith];

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

$results = new SimpleXMLElement($response);
		  $results = (array)simplexml_load_string($response);
			print "<pre>";
			//var_dump($results['result']);
		
if(count($results) > 0){
	foreach($results['result'] as $key => $val){
		//$inf["ID"]."###".$inf["countryName"]."|";
		
		//print_r($val);
		echo $val->arr->str[0]."###".$val->arr->str[0]."|";
		
	
	}


} 			


?>

