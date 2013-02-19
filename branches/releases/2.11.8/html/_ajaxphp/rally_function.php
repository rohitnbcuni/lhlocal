<?php


function setNewRallyDefect($lhprojectId, $defect_id, $data){
		//$XML_POST_URL = RALLY_WEB_SERVICE_URL."/defect/create";
		
		
		global $mysql;
		$severity_array = array();
		$severity_array[93] = 'Crash/Data Loss';
		$severity_array[94] = 'Major Problem';
		$severity_array[95] = 'Minor Problem';
		$severity_array[96] = 'Cosmetic';
			
		$severity_value = $severity_array[$data['severity']];
		$status_array = array();
		$status_array[1] =  'Submitted';
		$status_array[2] =  'Open';
		$status_array[4] =  'Open';
		$status_array[5] =  'Open';
		$status_array[6] =  'Open';
		$status_array[7] =  'Open';
		$status_array[10] =  'Fixed';
		$status_array[8] =  'Closed';
		$status_array[3] =  'Fixed';
		
		$status_value = $status_array[$data['status']];
		
		$sql = "SELECT * FROM rally_lh_project_mapping WHERE lh_project_id = '".$lhprojectId."' LIMIT 1";
		$result = $mysql->sqlordie($sql);
		//If LH project was mapped with Rally Project
		if($result->num_rows > 0){
			$mapping_data = $result->fetch_assoc();
			$rally_project_id = $mapping_data['rally_project_id'];
			//If Defect were not reported to Rally :-Mean New Defect
			$sql = "SELECT * FROM qa_rally_defects WHERE defect_id = '".$defect_id."' LIMIT 1";
			$result2 = $mysql->sqlordie($sql);
			if($result2->num_rows == 0){
				$type = 'create';
				$XML_POST_URL = RALLY_WEB_SERVICE_URL.'/defect/create';
				$prepare_defect_xml = '<Defect>
									<Description> '.$data['desc'].'</Description> 
									<Name>'.$data['title'].' </Name> 
									<Priority>None</Priority>
									<ReleaseNote>false</ReleaseNote> 
									<Severity>'.$severity_value.'</Severity> 
									<State>'.$status_value.'</State>
									<Owner ref="'.RALLY_WEB_SERVICE_URL.'/user/'.RALLY_LH_USER_ID.'"/>
									<Project ref="'.RALLY_WEB_SERVICE_URL.'/project/10151940218" />
									<SubmittedBy ref="'.RALLY_WEB_SERVICE_URL.'/user/'.RALLY_LH_USER_ID.'"/>
									<LighthouseID>'.$defect_id.'</LighthouseID>
									<LighthouseIDWebLink><LinkID>'.$defect_id.'</LinkID><DisplayString/></LighthouseIDWebLink>
									</Defect>';	
				
			
			}else{
				$type = 'update';
				$rally_info = $result2->fetch_assoc();
				$rally_defect_id = $rally_info['rally_id'];
				//This defect has already reported . Just need to Update
				$XML_POST_URL = RALLY_WEB_SERVICE_URL.'/defect/'.$rally_defect_id;
				$prepare_defect_xml = '<Defect>
										<Description> '.$data['desc'].'</Description> 
										<Name>'.$data['title'].' </Name> 
										<Priority>None</Priority>
										<ReleaseNote>false</ReleaseNote> 
										<Severity>'.$severity_value.'</Severity> 
										<State>'.$status_value.'</State>
										
										</Defect>';	
									
			}
			//print $prepare_defect_xml; die;
			sendRallyCurl($XML_POST_URL,$prepare_defect_xml,$defect_id, $type);
		
		
		}
		
	
	}
	
	
	function sendRallyCurl($XML_POST_URL,$prepare_defect_xml,$lh_defect_id,$type){
		global $mysql;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $XML_POST_URL);
		curl_setopt($ch, CURLOPT_USERPWD, RALLY_DEFECT_USERNAME.':'.RALLY_DEFECT_PASSWORD);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_PROXY, "http://64.210.197.20:80");
		//curl_setopt($ch, CURLOPT_PROXYPORT, 80);
		curl_setopt($ch, CURLOPT_TIMEOUT, 4);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $prepare_defect_xml);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml;charset=utf-8'));
		$rally_xml = curl_exec($ch);
		//print_r($prepare_defect_xml);

		/**
		 * Check for errors
		 */
		if ( curl_errno($ch) ) {
			$result = 'cURL ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
			echo "ERROR! " . $result;
		} else {
			$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
			switch($returnCode){
				case 200:

					break;
				default:
					$result = 'HTTP ERROR -> ' . $returnCode;
					break;
			}
		}
		curl_close($ch);
		if(strlen($rally_xml) > 10){
			$xml_parser = xml_parser_create();
			xml_parse($xml_parser, $rally_xml);
			$returnXML = new SimpleXMLElement($rally_xml);
			if(count($returnXML->Errors) == 0){
			
			
			}
			//print_r($returnXML);
			if(ISSET($returnXML->Object)){
				/*
				 [rallyAPIMajor] => 1
				[rallyAPIMinor] => 40
				[ref] => https://rally1.rallydev.com/slm/webservice/1.40/defect/10547483638
				[refObjectName] => project details numbers
				[type] => Defect
				*/
				if($type == 'create'){
					$arr = $returnXML->Object->attributes();
					
					if(ISSET($arr['ref'])){
						$rally_new_defect_id = str_replace(RALLY_WEB_SERVICE_URL."/defect/","",$arr['ref']); 	
						
						$sql = "INSERT INTO qa_rally_defects SET defect_id = '".$lh_defect_id."', rally_id = '".$rally_new_defect_id."' , created = '".date("Y-m-d h:i:s")."'";
						$result2 = $mysql->sqlordie($sql);
					}
				
				}
			}
		}
		
	
	}






?>