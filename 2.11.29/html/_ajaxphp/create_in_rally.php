<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$woId = $mysql->real_escape_string(@$_POST['woId']);
	$projectId = $mysql->real_escape_string(@$_POST['projectId']);
	$priorityId = $mysql->real_escape_string(@$_POST['priorityId']);
	$woTitle = $mysql->real_escape_string(@$_POST['woTitle']);
	$woDesc = $mysql->real_escape_string(@$_POST['woDesc']);
	$rallyType = $mysql->real_escape_string(@$_POST['rallyType']);
	$rallyProject = $mysql->real_escape_string(@$_POST['rallyProject']);

	$workorderPriority = "select * from `lnk_workorder_priority_types` where id='".$priorityId."'";
	$priority_result = $mysql->sqlordie($workorderPriority);
	$priority_row = $priority_result->fetch_assoc();

	$projectDetails = "select * from projects where id='".$rallyProject."'";
	$project_result = $mysql->sqlordie($projectDetails);
	$project_row = $project_result->fetch_assoc();
	
	$ProjectName = $project_row['project_name']; 
	$priority = $priority_row['name'];

	// Search for special characters and replace with a space
	for($i=0; $i<strlen($woDesc); $i++) 
	{
		$c=ord($woDesc[$i]);
		if ($c < 128)
		$tmpDesc[$i] = $woDesc[$i];
		else
		$tmpDesc[$i] = ' ';
	}
	$woDesc = implode($tmpDesc);
	$curlResult = "";
	
	if($rallyType == 'defect') //if category is Defect then create a defect in rally application
	{
		$rallyXML='';
		$rallyXML.='<?xml version="1.0" encoding="UTF-8" ?> ';
		$rallyXML.="<Defect>";
		$rallyXML.="<Name>".$woTitle."</Name>";
		$rallyXML.="<Description>" . $woDesc . "</Description>";
		$rallyXML.="<Priority>" . $priority . "</Priority>";
		$rallyXML.="<State>New</State>";
		$rallyXML.="<LighthouseID>".$woId."</LighthouseID>";
		$rallyXML.="<LighthouseProject>" . $ProjectName . "</LighthouseProject>";
		$rallyXML.="</Defect>";
		//LH
		/*if (strlen($rallyXML) == 0)
		{
			fwrite($err, "Error convertiing issue: " . $woId . " to XML. \n");
			fclose($err);
			return;
		}
		$rootPath = '/tmp/Defect_'.$woId.'.xml';
		$a = fopen($rootPath, "w"); 
		fwrite($a, $rallyXML);
		fclose($a);  
		if (file_exists($rootPath))
		{
			$xmlObj = simplexml_load_file($rootPath);
			$command = "curl -u 'eventum@nbcuni.com:3v3ntum1' -k -x 64.210.197.20:80 -T " . $rootPath . " https://us1.rallydev.com:443/slm/webservice/1.08/defect/create";
			$tempString = exec($command,$curlResult);
			unlink($rootPath); 
		} else 
		{
			// There is a problem with the file, so log the id to the error log so it can be moved manually
			$errMsg = 'Error with reading or writing to tmp file for issue: ' . $woId . "\n";
			fwrite($err, $errMsg);
			fclose($err);
		}*/
		
	}
	if($rallyType == 'enhancement') //if category is enhancement then create a user story in rally application
	{
		$rallyXML='';
		$rallyXML.='<?xml version="1.0" encoding="UTF-8" ?> ';
		$rallyXML.="<HierarchicalRequirement>";
		$rallyXML.="<Name>".$woTitle."</Name>";
		$rallyXML.="<Description>" . $woDesc . "</Description>";
		$rallyXML.="<LighthouseID>".$woId."</LighthouseID>";
		$rallyXML.="<LighthouseProject>" . $ProjectName . "</LighthouseProject>";
		$rallyXML.="<Owner>aaron.goldsmid@nbcuni.com</Owner>";
		$rallyXML.="</HierarchicalRequirement>";
		
		/*if (strlen($rallyXML) == 0)
		{
			fwrite($err, "Error convertiing issue: " . $woId . " to XML. \n");
			fclose($err);
			return;
		}
		$rootPath = '/tmp/Defect_'.$woId.'.xml';
		$a = fopen($rootPath, "w"); 
		fwrite($a, $rallyXML);
		fclose($a);  
		if (file_exists($rootPath))
		{
			$xmlObj = simplexml_load_file($rootPath);
			$command = "curl -u 'eventum@nbcuni.com:3v3ntum1' -k -x 64.210.197.20:80 -T " . $rootPath . " https://us1.rallydev.com:443/slm/webservice/1.08/hierarchicalrequirement/create";
			$tempString = exec($command,$curlResult);
			unlink($rootPath); 
		} else 
		{
			// There is a problem with the file, so log the id to the error log so it can be moved manually
			$errMsg = 'Error with reading or writing to tmp file for issue: ' . $woId . "\n";
			fwrite($err, $errMsg);
			fclose($err);
		}*/
		
	}
	$curlResultString = implode("",$curlResult);
	try 
	{
		$parseXml = new SimpleXMLElement($curlResultString);  //ok, we have xml, now try to parse it
		if(empty($parseXml->Errors)){
			$update_wo_query = "update workorders set rally_flag='1' where id='".$woId."'";
			@$mysql->sqlordie($update_wo_query);
		}
	} 
	catch (Exception $e)
	{
		// handle the error
	}
	
	
?>