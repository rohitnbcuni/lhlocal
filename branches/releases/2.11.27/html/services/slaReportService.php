<?php


class slaReportServices {

	private static $instance;
    private $count = 0;
	public $config;
	//const MAX_UPLOAD_LIMIT = 5;
	
	
 	public static function singleton()
    {
        if (!isset(self::$instance)) {
            //echo 'Creating new instance.';
            $mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
            self::$instance = $mysql;
        }
        return self::$instance;
    }
	/**
	 * Get Report
	 * 
	 * @param Object $date_range
	 * @param Object $format
	 * @return String
	 */
	 
	 public function slaReport($d,$f){
	 
		
		
		$mysql = self::singleton();
		
		$wo_user_list = array();
		$wo_status_array = array();
		$companyListArr = array();
		$startDate = $mysql->real_escape_string($d->startdate);
		$endDate = $mysql->real_escape_string($d->enddate);
		$format = $mysql->real_escape_string($d->format);
		 $qrySLA = "SELECT w.*,p.project_name,p.project_code,p.company FROM `workorders` w,projects p WHERE CASE WHEN draft_date = '0000-00-00 00:00:00' THEN
		`creation_date` >='".$startDate." 00:00:00' AND `creation_date` <= '".$endDate." 59:59:59'   ELSE `draft_date` >='".$startDate." 00:00:00' AND `draft_date` <= '".$endDate." 59:59:59'  END AND p.id=w.project_id ";
	 	$qryResult = $mysql->query($qrySLA);
		
		
		$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");
		$archived_type_arr = array("0" => "FALSE", "1" => "TRUE");

		$final_row = array();
		
		//Get Collect ALL Status type
		$select_wo_status = "SELECT `id`, `name` FROM `lnk_workorder_status_types`";
		$status_result = $mysql->query($select_wo_status);
		if($status_result->num_rows > 0){
			while($status_row = $status_result->fetch_assoc()){
				$wo_status_array[$status_row['id']] = $status_row['name'];
			}
		}
	 	while($workorder = $qryResult->fetch_assoc()){
			$output = array();
		$workorder_id = $workorder['id'];
		$req_type = $this->getCustomTypeName($workorder_id,'REQ_TYPE');
		$site_name_id = $this->getCustomTypeID($workorder_id,'SITE_NAME');
		/*if($req_type ==  'Submit a Request'){
			continue;
		}*/
		//print_r($cat_wo_id);
		/*if(count($cat_wo_ids) > 0){
			if(in_array($site_name_id,$cat_wo_ids) == false){
				continue;
			}
		}*/
		if($req_type == 'Report a Problem'){
		    $req_type_id = $this->getCustomTypeID($workorder_id,'SEVERITY');
		}else{
		    $req_type_id = $this->getCustomTypeID($workorder_id,'REQ_TYPE');
		}
		if($req_type == 'Report a Problem'){
		    $severity = $this->getCustomTypeName($workorder_id,'SEVERITY');
		} else {
		    $severity = 'N/A';
		}
		
		$output[] = $workorder['creation_date'];
		$output[] = $workorder['id'];
		$output[] = $workorder['title'];
		$output[] = $this->getCompanyName($workorder['company']);
		$output[] = $workorder['project_code']." - ".$workorder['project_name'];
		$output[] = $this->getUserName($workorder['requested_by'],$wo_user_list);
		$output[] = $this->getUserName($workorder['assigned_to'],$wo_user_list);
		$output[] = $this->getUserName($workorder['completed_by'],$wo_user_list);
		$output[] = $this->getUserTitle($workorder['assigned_to']);
		
		$output[] = $request_type_arr[$req_type];
		$output[] = $wo_status_array[$workorder['status']];
		$output[] = $this->getCustomTypeName($workorder_id,'SITE_NAME');
		
		$output[] = $this->getCustomTypeName($workorder_id,'INFRA_TYPE');
		$output[] = $severity;
		$output[] = $this->getCustomTypeName($workorder_id,'CRITICAL');
		//$output[] = $this->format_date($workorder['creation_date']);
		$output[] = $this->format_date($workorder['launch_date']);
		$output[] = $this->getAckTimefromAudit($workorder_id);
		$output[] = $this->getFixedDatefromAudit($workorder_id);
		$output[] = $this->format_date($workorder['closed_date']);
		$output[] = $archived_type_arr[$workorder['archived']]; 
		$final_row[] = $output;
		
		
		
		
		
		}
	 
	 return $final_row;
	 
	 }
	 
	 
	private function getCompanyName($company_id)
	{	
		$companyListArr = array();
		$mysql = self::singleton();
		if(!array_key_exists($company_id, $companyListArr)){
			  $select_project_company = "SELECT * FROM `companies` where id ='" .$company_id ."'";
			  $project_company_res = $mysql->query($select_project_company);
			  if($project_company_res->num_rows > 0){
				$row = $project_company_res->fetch_assoc();
				$companyListArr[$row['id']] = $row['name'];
			}
		}
		return $companyListArr[$company_id];
	}
	 
	private function getSiteWoId($cateGoryStr)
	{
		$mysql = self::singleton();
		$field_id = array();
		//echo "SELECT wcf.workorder_id FROM `site_categories_mapping`  scm INNER JOIN workorder_custom_fields wcf ON (scm.field_id = wcf.field_id) WHERE scm.category_id IN ($cateGoryStr) AND wcf.field_key = 'SITE_NAME'";
		$wo_custom_data = $mysql->query("SELECT scm.field_id FROM `site_categories_mapping`  scm  WHERE scm.category_id IN ($cateGoryStr) ");
		while($row = $wo_custom_data->fetch_assoc()){
			$field_id[] = $row['field_id'];
		}
		return $field_id;

	}
	private function getCatogeryName($site_id)
	{
		$mysql = self::singleton();
		$category_name = "N/A";
		//echo "SELECT s.category_name  FROM `site_categories` INNER JOIN  site_categories_mapping scm ON (s.id = scm.category_id) WHERE scm.field_id =  $site_id";
		$wo_custom_data = $mysql->query("SELECT s.category_name  FROM `site_categories` s INNER JOIN  site_categories_mapping scm ON (s.id = scm.category_id) WHERE scm.field_id =  '$site_id'");
		if($wo_custom_data->num_rows > 0){
			$row = $wo_custom_data->fetch_assoc();
			$category_name = $row['category_name'];
		}
		
		return $category_name;

	 }

	private function getCustomTypeName($workorder_id,$custom_type)
	{
		$mysql = self::singleton();
		$wo_custom_data = $mysql->query("SELECT `workorder_id`,a.`field_key`,a.`field_id`,c.`field_name` FROM `workorder_custom_fields` a,`workorders` b,`lnk_custom_fields_value` c where a.`workorder_id`='".$workorder_id."' and a.`field_key`='".$custom_type."' and b.id = a.workorder_id and a.`field_id`= c.`field_id`");
		$field_name = 'N/A';
		if($wo_custom_data->num_rows > 0){
			$row = $wo_custom_data->fetch_assoc();
			$field_name =  $row['field_name'];
		}
		return $field_name;

	}

	private function getCustomTypeID($workorder_id,$custom_type)
	{
		$mysql = self::singleton();
		$wo_custom_data = $mysql->query("SELECT `workorder_id`,a.`field_key`,a.`field_id`,c.`field_name` FROM `workorder_custom_fields` a,`workorders` b,`lnk_custom_fields_value` c where a.`workorder_id`='".$workorder_id."' and a.`field_key`='".$custom_type."' and b.id = a.workorder_id and a.`field_id`= c.`field_id`");
		$field_name = 'N/A';
		if($wo_custom_data->num_rows > 0){
			$row = $wo_custom_data->fetch_assoc();
			$field_name =  $row['field_id'];
		}
		return $field_name;

	}

	private function getAckTimefromAudit($workorder_id)
	{
		$mysql = self::singleton();
		$wo_audit_data = $mysql->query("select `log_date` from `workorder_audit` wa where wa.workorder_id='".$workorder_id."' AND wa.status = '7' order by log_date limit 1");
		$log_date = 'N/A';
		if($wo_audit_data->num_rows > 0){
			$row = $wo_audit_data->fetch_assoc();
			$log_date =  $row['log_date'];
		}
		return $this->format_date($log_date);

	}

	private function getFixedDatefromAudit($workorder_id)
	{	$mysql = self::singleton();
		$wo_latest_reopen_date = $mysql->query("select max(log_date) as log_date from workorder_audit where workorder_id='".$workorder_id."' AND status = 12");
		$log_date_string = '';
		if($wo_latest_reopen_date->num_rows > 0){
			$row = $wo_latest_reopen_date->fetch_assoc();
			if(!empty($row['log_date'])){	
				$log_date_string = "log_date >= '".$row['log_date']."' AND ";
			}
		}
		$wo_audit_data = $mysql->query("select `log_date` from `workorder_audit` wa where wa.workorder_id='".$workorder_id."' AND $log_date_string wa.status = '3' order by log_date asc limit 1");
		$log_date = 'N/A';
		if($wo_audit_data->num_rows > 0){
			$row = $wo_audit_data->fetch_assoc();
			$log_date =  $row['log_date'];
		}
		return $this->format_date($log_date);

	}
	private function format_date($date){

		if(!empty($date) and $date!='N/A')
		{
			$str_date = strtotime($date);
			return Date('Y-m-d H:i A', $str_date);
		}
		return 'N/A';
	}
	
	private function getUserName($user_id,$wo_user_list)
	{
		$mysql = self::singleton();
		if(!array_key_exists($user_id, $wo_user_list)){
		$select_wo_user = "SELECT * FROM `users` WHERE `id`='" .$user_id ."'";
		$select_wo_user_result = $mysql->query($select_wo_user);
		$select_wo_user_row = $select_wo_user_result->fetch_assoc();
		$userName = '';
		if(!empty($select_wo_user_row['last_name']))
		{
			$userName = $select_wo_user_row['first_name']." " .$select_wo_user_row['last_name'];
		}
		else
		{
			$userName  = $select_wo_user_row['first_name'];
		}

		$wo_user_list[$user_id] = $userName;
		}
		return  $wo_user_list[$user_id];
	}

	private function getUserTitle($user_id){
		$mysql = self::singleton();
		$select_user_title = "SELECT a.name from lnk_user_subtitles a,user_roles b where a.id = b.category_subcategory_id and b.user_id = '$user_id'";
		$user_title_result = $mysql->query($select_user_title);
		$user_title = '';
		while($row = $user_title_result->fetch_assoc()){
			$user_title.= $row['name'].",";
		}
		$user_title = substr($user_title,0,-1);
		return $user_title;
	}

	public function  dawnloadfile($output,$format){
		
		if($format == 'csv'){
			$filename = "slareport_".time().".csv";
			header('Content-Type: text/csv');
			header('Content-Disposition: attachement; filename="'.$filename.'";');
			header('Cache-Control: max-age=0');
			$header_csv  = array(
						"Opened",
						"Ticket_No"
                        ,"Brief_Description"
                        ,"Company"
                        ,"Project"
                        ,"Requested_BY"
                        ,"Assigned_TO"
                        ,"Request_Completed_By"
                        ,"User_Category"
                        ,"REQUEST_TYPE"
                        ,"Status"					
                        ,"SITE_NAME"
                        ,"INFRASTRUCTURE_TYPE"
                        ,"SEVERITY"
                        ,"CRITICAL"					
                        ,"Estimated_Completion_Date"					
                        ,"Acknowledgement_Time"
                        ,"Fixed"					
                        ,"Closed"
                        ,"Archived");
		
		 // open raw memory as file so no temp files needed, you might run out of memory though
		$f = fopen('php://output', 'w');
		fputcsv($f, $header_csv);
		//p($output);
		// loop over the input array
		foreach ($output as $line) { 
			// generate csv lines from the inner arrays
			fputcsv($f, $line); 
		}
		// rewrind the "file" with the csv lines
		fclose($f);
		// tell the browser it's going to be a csv file

			
		}else if($format == 'json'){
			
			//p($output);
			echo json_encode($output);
		
		
		
		}
		
		
	
	
	
	
	
	}


	
		
	}
		
		/*if($_POST['lh_submit']){*/
		 	
			define("SALT",'lighthouse');
			require_once('../_inc/config.inc');
			$c = new slaReportServices();
			$u = new stdClass();
	    	/*$u->startdate = "2014-01-03";
			$u->enddate = "2014-02-03";
			$format = "csv";*/
	    	$u->startdate = $_POST['lh_startdate'];
	        $u->enddate = $_POST['lh_enddate'];
			$format = $_POST['lh_format'];
	     	$lh_token = $_POST['lh_token'];
			
			$lh_salt = md5(SALT);
			if($lh_salt == $lh_token){
			
				$o = $c->slaReport($u,$format);
				if(count($o) > 0){
					$c->dawnloadfile($o,$format);
				}else{
					echo "No Record Found";
				
				}
			
			}else{
				echo "Invalid Authentication";
			
			
			}
			
			
			
	     	

				
		

?>
 
