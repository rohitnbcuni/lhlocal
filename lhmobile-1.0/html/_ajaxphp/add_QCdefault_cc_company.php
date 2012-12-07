<?PHP
	include('../_inc/config.inc');
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	include("sessionHandler.php");
	$projectId = $mysql->real_escape_string($_GET['projectId']);
	$cc = $mysql->real_escape_string($_GET['cc']);

	if(!empty($cc))
	{
		$ccArray = array();
		$list = explode(",", $cc);

		$select_company = "SELECT `company` FROM `projects` WHERE `id`='$projectId' LIMIT 1";
		$company_result = @$mysql->sqlordie($select_company);
		$company_row = @$company_result->fetch_assoc();
		$company = $company_row['company']; 

		if(!empty($company))
		{
			$select_company_qry = "SELECT * FROM `companies` WHERE `id`='$company'";
			$select_company_result = @$mysql->sqlordie($select_company_qry);
			 if($select_company_result->num_rows > 0)
			 {
			 	$select_company_row = @$select_company_result->fetch_assoc();
				$company_cc_list = explode(",",$select_company_row['cclist']);
				// Load all the existing default CC users of the projects into the list
				/*for($i = 0; $i < sizeof($company_cc_list); $i++) {
					$ccArray[$company_cc_list[$i]]=true;
				}*/

				// Load the new users that needs to be added to all the projects in the company
				for($i = 0; $i < sizeof($list); $i++) {
					$ccArray[$list[$i]]=true;
				}


				$listKeys = array_keys($ccArray);
				$arrayData = "";
			
				for($z = 0; $z < sizeof($listKeys); $z++) {
					if(!empty($listKeys[$z]))
					{
						$arrayData .= $listKeys[$z] .",";
					}
				}
				
				$update_cc = "UPDATE `companies` SET `qccclist`='$arrayData' WHERE `id`='$company'";
				@$mysql->sqlordie($update_cc);
			 }
		}		 
	}
?>