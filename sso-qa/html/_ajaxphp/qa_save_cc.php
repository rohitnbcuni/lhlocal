<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	
		$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";	
	if(isset($_SESSION['user_id'])) {		
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		//Defining Global mysql connection values
		global $mysql;
		
		$user = $_SESSION['lh_username'];
	    $password = $_SESSION['lh_password'];
		
		$defectId = $mysql->real_escape_string($_GET['defectId']);
		$cc = $mysql->real_escape_string($_GET['cc']);
		$addCC = $mysql->real_escape_string($_GET['addCC']);
		$list = explode(",", $cc);
       
        $listold = array();
		
		
		$ccArray = array();
		if(!empty($defectId))
		{
		
		/*changes for 18088*/
		$project_id=$mysql->real_escape_string($_GET['project_id']);
				
		if(!empty($project_id))
		{
		//LH 18474
		//$wo_query = "SELECT `cclist` FROM `projects` WHERE `id`='$project_id' LIMIT 1";
		$wo_query = "SELECT `qccclist` FROM `projects` WHERE `id`= ? LIMIT 1";
		
		$wo_result = $mysql->sqlprepare($wo_query,array($project_id));
		$wo_row = $wo_result->fetch_assoc();
		//$listold = explode(",", $wo_row[cclist]);
		$listold = explode(",", $wo_row['qccclist']);
		
		}
		
		
		
		$list=array_merge($list,$listold);
		$wo_query = "SELECT * FROM `qa_defects` WHERE `id`= ? LIMIT 1";
		$wo_result = $mysql->sqlprepare($wo_query,array($defectId));
		$wo_row = $wo_result->fetch_assoc();
		$list = explode(",", $cc);
		for($i = 0; $i < sizeof($list); $i++) {
			if(!empty($list[$i]) && !isset($ccArray[$list[$i]])) {
				if($list[$i] != @$_GET['remove'])
				$ccArray[$list[$i]]=true;
			}
		}
		
		$listKeys = array_keys($ccArray);
		$arrayData = "";
		
		for($z = 0; $z < sizeof($listKeys); $z++) {
			$arrayData .= $listKeys[$z] .",";
		}
		
		$update_cc = "UPDATE `qa_defects` SET `cclist`='$arrayData' WHERE `id`= ? ";
		
		$mysql->sqlprepare($update_cc,array($defectId));
		$select_cc = "SELECT `cclist` FROM `qa_defects` WHERE `id`= ? LIMIT 1";
		$result = $mysql->sqlprepare($select_cc,array($defectId));
		$row = $result->fetch_assoc();
		
		$bc_id_query = "SELECT  `body`, `project_id`, `title` FROM `qa_defects` WHERE `id`= ? LIMIT 1";
		
				$bc_id_result = $mysql->sqlprepare($bc_id_query,array($defectId));
				$bc_id_result1=$bc_id_result->fetch_assoc();
				$bc_id_row = $bc_id_result1;
			
		
				$select_project = "SELECT * FROM `projects` WHERE `id`= ? ";
			
				$project_res = $mysql->sqlprepare($select_project,array($bc_id_row['project_id']));
				$project_res1= $project_res->fetch_assoc();
				$project_row = $project_res1;
				$select_company = "SELECT * FROM `companies` WHERE `id`= ? ";
				$company_res = $mysql->sqlprepare($select_company,array($project_row['company']));
				$company_res1 = $company_res->fetch_assoc();
				$company_row = $company_res1;
				
				
		
		if($result->num_rows > 0) {		
			$new_list = explode(",", $row['cclist']);
			$list = "";
			
			$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";	
             //$subject = "Defect: " . $bc_id_row['title'] . " - Lighthouse Work Order Message";		
 $subject = "Defect $defectId:  You have been added to the CC list of this ticket.";
	
				$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");
                $description=($wo_row['body']);
                $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description));
				for($x = 0; $x < sizeof($new_list); $x++) {
				if(!empty($new_list[$x])) {
					$select_cc_user = "SELECT * FROM `users` WHERE `id`= ? LIMIT 1";
					$cc_user_result = @$mysql->sqlprepare($select_cc_user,array($new_list[$x]));
					$cc_user_row = @$cc_user_result->fetch_assoc();
					
					$list .= "<li>"
						."<div class=\"cclist_name\">" .ucfirst($cc_user_row['first_name']) ." " .ucfirst($cc_user_row['last_name']) ."</div>"
						."<button class=\"status cclist_remover\" onClick=\"removeCcUser(" .$new_list[$x] ."); return false;\"><span>remove</span></button>"
					."</li>";
					
					if($new_list[$x] == $addCC ){
						
							$to = $cc_user_row['email'];
					
							$msg =  COMPANY_LABEL.": " . $company_row['name'] . "\r\n"
										."Project: " . $project_row['project_code'] ." - " . $project_row['project_name'] ."\r\n"
										."Link: " .BASE_URL ."/quality/index/edit/?defect_id=" . $defectId  ."\r\n\r\n"
										."Defect <a href ='" .BASE_URL ."/quality/index/edit/?defect_id=" . $defectId  ."' target='_blank'>" . $defectId . "</a> </b> : You have been added to the CC list of this ticket.\r\n\r\n"
										."\t-Description: " . $bc_id_row['body'] ."\r\n"
										.$file_list . "\r\n"
										."..........................................................................";
							if(!empty($to)){ 
								//echo $msg;
								sendEmail($to, $subject, $msg, $headers);
							}
					}	
				}
			}			
			echo $list;
		}
		}



else
		{
			for($i = 0; $i < sizeof($list); $i++) {
				if(!empty($list[$i]) && !isset($ccArray[$list[$i]])) {
					if($list[$i] != @$_GET['remove'])
					$ccArray[$list[$i]]=true;
				}
			}
			
			$listKeys = array_keys($ccArray);
			$arrayData = "";
			$list = "";
			for($z = 0; $z < sizeof($listKeys); $z++) {
				$arrayData .= $listKeys[$z] .",";
			
				if(!empty($listKeys[$z])) {
					$select_cc_user = "SELECT * FROM `users` WHERE `id`= ? LIMIT 1";
					$cc_user_result = @$mysql->sqlprepare($select_cc_user,array($listKeys[$z]));
					$cc_user_row = @$cc_user_result->fetch_assoc();
					
					$list .= "<li>"
						."<div class=\"cclist_name\">" .ucfirst($cc_user_row['first_name']) ." " .ucfirst($cc_user_row['last_name']) ."</div>"
						."<button class=\"status cclist_remover\" onClick=\"removeCcUser(" .$listKeys[$z] ."); return false;\"><span>remove</span></button>"
					."</li>";
				}
			}
			echo $list;
		}
		//echo $row['cclist'];
		
	}



	function sendEmail($to, $subject, $msg, $headers){
				$msg = nl2br($msg);
		mail($to, $subject, $msg, $headers);
	}
?>

