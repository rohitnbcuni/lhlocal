<?php
	
	include "cron.config.php";
	
	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	global $mysql;

try {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, CURLOPT_PROXY, "http://64.210.197.20:80");
	//curl_setopt($ch, CURLOPT_PROXYPORT, 80);
	curl_setopt($ch, CURLOPT_URL, RALLY_PROJECT_URL);    // get the url contents
	
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: text/xml;charset=utf-8'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERPWD, RALLY_DEFECT_USERNAME.':'.RALLY_DEFECT_PASSWORD);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	
	$data = curl_exec($ch); // execute curl request
	$info = curl_getinfo($ch);
	curl_close($ch);
	if($info['http_code'] != 200){
		throw new Exception("Invalid URL",0,$e);
	
	}
}
catch(Exception $e){
    echo $e->getMessage();
}

$rally_workspaces = simplexml_load_string($data);
if(count($rally_workspaces) >0){
	foreach($rally_workspaces as $rally_key => $rally_values){
		if(ISSET($rally_values->Workspace)){
		
		foreach($rally_values->Workspace as $workspace_key => $workspace_value){
			$insr_workspace = "REPLACE INTO rally_projects SET project_id = ".$workspace_value->ObjectID." ,project_name = '".$workspace_value->Name."', workspace_id = 0, active = '1' ,deleted ='0'";
			$mysql->sqlordie($insr_workspace);
			if(ISSET($workspace_value->Projects)){
				
				foreach($workspace_value->Projects as $project_key => $project_values){
					if(ISSET($project_values->Project)){
						foreach($project_values->Project as $pkey => $pValues){
							
							//$proj_res = $mysql->sqlordie($select_proj);
							$insr_project = "REPLACE INTO rally_projects SET project_id = ".$pValues->ObjectID." ,project_name = '".$pValues->Name."', workspace_id = '".$workspace_value->ObjectID."', active = '1' ,deleted ='0'";
							$mysql->sqlordie($insr_project);
							/*echo "WOrk SPace:: ".$workspace_value->ObjectID;
							echo "<br/>";
							echo "WOrk SPace Name:: ".$workspace_value->Name;
							echo "<br/>";
							echo "WOrk Project ID:: ".($pValues->ObjectID);
							echo "<br/>";
							echo "WOrk Project Name:: ".();*/
						
						
						}
					
					
					
					}
					

				
				}
			}	
		
		}
	
	}
	}

}

//print "<pre>";
//print_r($xml);



?>
