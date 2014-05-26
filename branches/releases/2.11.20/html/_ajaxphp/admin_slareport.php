<?
include('../_inc/config.inc');
include("sessionHandler.php");
//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
global $mysql;
$month = $mysql->real_escape_string($_POST['month']);
$year = $mysql->real_escape_string($_POST['year']);
$to_month = $mysql->real_escape_string($_POST['to_month']);
$to_year = $mysql->real_escape_string($_POST['to_year']);
$to_assign = $mysql->real_escape_string($_POST['assign_to']);
$admin_requested_select = $mysql->real_escape_string($_POST['admin_requested_select']);
$wo_user_list = array();
$wo_status_array = array();
$companyListArr = array();

//print_r($_REQUEST); die;
$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");

$archived_type_arr = array("0" => "FALSE", "1" => "TRUE");

$startDate = date("$year-$month-01");
$lastday = date("t",strtotime($startDate));
$to_startDate = date("$to_year-$to_month-01");
$to_lastday = date("t",strtotime($to_startDate));
if($month==12)
{
	$year++;
	//$dtendDate = strtotime('+ 1 year',strtotime($startDate));
	$endDate = date("$year-01-01");
}else {
	$dtendDate = strtotime('+ 1 month',strtotime($startDate));
	$endDate = date("$year-m-d",$dtendDate);
}

if($to_month==12)
{
	$to_year++;
	//$dtendDate = strtotime('+ 1 year',strtotime($startDate));
	$to_endDate = date("$to_year-01-01");
}else {
	$dtendDate = strtotime('+ 1 month',strtotime($to_startDate));
	
	$to_endDate = date("$to_year-m-d",$dtendDate);
}
	$admin_requested_select_sql = '';
	if($admin_requested_select != '' || $admin_requested_select != NULL){
		$admin_requested_select_sql = " AND w.requested_by IN (".$admin_requested_select.")";
	
	
	
	}

	if($to_month =='' &&  $to_year==''  && $to_assign =='' && $month!=''  && $year!=''){
	//LH#27424
	//$qry_sla_report_per_month = "SELECT w.*,p.project_name,p.project_code,p.company FROM `workorders` w,projects p WHERE `creation_date` >='".$startDate."' AND `creation_date` < '".$endDate."' AND p.id=w.project_id";
	$qry_sla_report_per_month = "SELECT w.*,p.project_name,p.project_code,p.company FROM `workorders` w,projects p WHERE CASE WHEN draft_date = '0000-00-00 00:00:00' THEN `creation_date` >='".$startDate."' AND `creation_date` < '".$endDate."' ELSE `draft_date` >='".$startDate."' AND `draft_date` < '".$endDate."' END AND p.id=w.project_id ".$admin_requested_select_sql;

	}
	elseif($to_month =='' &&  $to_year==''  && $to_assign!='' && $month!=''  && $year!=''){
	$qry_sla_report_per_month = "SELECT w.*,p.project_name,p.project_code,p.company FROM `workorders` w,projects p WHERE CASE WHEN draft_date = '0000-00-00 00:00:00' THEN `creation_date` >='".$startDate."' AND `creation_date` < '".$endDate."' and  assigned_to='".$to_assign."' ELSE `draft_date` >='".$startDate."' AND `draft_date` < '".$endDate."'  and  assigned_to='".$to_assign."' END AND p.id=w.project_id ".$admin_requested_select_sql;
	}
	elseif($to_month !='' &&  $to_year!=''  && $to_assign=='' && $month!=''  && $year!=''){
	$qry_sla_report_per_month = "SELECT w.*,p.project_name,p.project_code,p.company FROM `workorders` w,projects p WHERE CASE WHEN draft_date = '0000-00-00 00:00:00' THEN `creation_date` >='".$startDate."' and `creation_date` < '".$to_endDate."'  ELSE `draft_date` >='".$startDate."' AND `draft_date` < '".$to_endDate."' END AND p.id=w.project_id ".$admin_requested_select_sql;
	}
	else{

	$qry_sla_report_per_month = "SELECT w.*,p.project_name,p.project_code,p.company FROM `workorders` w,projects p WHERE CASE WHEN draft_date = '0000-00-00 00:00:00' THEN `creation_date` >='".$startDate."' AND `creation_date` < '".$to_endDate."'  and  assigned_to='".$to_assign."' ELSE `draft_date` >='".$startDate."' AND `draft_date` < '".$to_endDate."' and assigned_to='".$to_assign."' END AND p.id=w.project_id ".$admin_requested_select_sql;
	}
	//echo $qry_sla_report_per_month;
	$sla_report_result = $mysql->sqlordie($qry_sla_report_per_month);

 if($sla_report_result->num_rows > 0) {

	   $select_wo_status = "SELECT `id`, `name` FROM `lnk_workorder_status_types`";
	   $status_result = $mysql->sqlordie($select_wo_status);
	  if($status_result->num_rows > 0){
		while($status_row = $status_result->fetch_assoc()){
		  $wo_status_array[$status_row['id']] = $status_row['name'];
		}
	  }
	  header("Pragma: public");
	  header("Expires: 0");
	  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	  header("Cache-Control: private",false);
	  header("Content-Type: application/octet-stream");
	  header("Content-Disposition: attachment;filename=SLA_Report.xls"); 
	  header("Content-Transfer-Encoding: binary");

	   echo "<table border=1>
				<tr>
					<td><b>Ticket No</b></td>
					<td width=100px><b>Brief Description</b></td>
					<td width=100px><b>Company</b></td>
					<td width=100px><b>Project</b></td>
					<td width=100px><b>Requested BY</b></td>
					<td width=100px><b>Assigned TO</b></td>
					<td width=100px><b>Request Completed By</b></td>
					<td width=100px><b>User Category</b></td>
					<td width=100px><b>REQUEST TYPE</b></td>
					<td width=100px><b>Status</b></td>					
					<td width=100px><b>SITE NAME</b></td>
					<td width=100px><b>INFRASTRUCTURE TYPE</b></td>
					<td width=100px><b>SEVERITY</b></td>
					<td width=100px><b>CRITICAL</b></td>					
					<td width=100px><b>Opened</b></td>
					<td width=100px><b>Estimated Completion Date</b></td>					
					<td width=100px><b>Acknowledgement Time</b></td>
					<td width=100px><b>Fixed</b></td>					
					<td width=100px><b>Closed</b></td>
					<td width=100px><b>Archived</b></td>
				</tr>";

	   while($workorder = $sla_report_result->fetch_assoc()) {
			$workorder_id = $workorder['id'];
		$req_type = getCustomTypeName($workorder_id,'REQ_TYPE',$mysql);
		if($req_type == 'Report a Problem'){
			$severity = getCustomTypeName($workorder_id,'SEVERITY',$mysql);
		} else {
			$severity = 'N/A';
		}
		echo "<tr>
		        <td>".$workorder['id']."</td>
		    	<td>".$workorder['title']."</td>
				<td>".getCompanyName($workorder['company'],$companyListArr,$mysql)."</td>
				<td>".$workorder['project_code']." - ".$workorder['project_name']."</td>
				<td>".getUserName($workorder['requested_by'],$wo_user_list,$mysql)."</td>
				<td>".getUserName($workorder['assigned_to'],$wo_user_list,$mysql)."</td>
				<td>".getUserName($workorder['completed_by'],$wo_user_list,$mysql)."</td>
				<td>".getUserTitle($workorder['assigned_to'],$mysql)."</td>
				
				<td>".$request_type_arr[$req_type]."</td>
				<td>".$wo_status_array[$workorder['status']]."</td>
				<td>".getCustomTypeName($workorder_id,'SITE_NAME',$mysql)."</td>
				<td>".getCustomTypeName($workorder_id,'INFRA_TYPE',$mysql)."</td>
				<td>".$severity."</td>
				<td>".getCustomTypeName($workorder_id,'CRITICAL',$mysql)."</td>
				<td>".format_date($workorder['creation_date'])."</td>
				<td>".format_date($workorder['launch_date'])."</td>
				<td>".getAckTimefromAudit($workorder_id,$mysql)."</td>
				<td>".getFixedDatefromAudit($workorder_id,$mysql)."</td>
				<td>".format_date($workorder['closed_date'])."</td>
				<td>".$archived_type_arr[$workorder['archived']]."</td> 
			  </tr>"; 
	   }
	   echo "</table>";
 }else{
	echo "<table border='1'>
				<tr>
					<td><b>Ticket No</b></td>
					<td width=100px><b>Brief Description</b></td>
					<td width=100px><b>Company</b></td>
					<td width=100px><b>Project</b></td>
					<td width=100px><b>Requested BY</b></td>
					<td width=100px><b>Assigned TO</b></td>
					<td width=100px><b>Request Completed By</b></td>
					<td width=100px><b>User Category</b></td>
					<td width=100px><b>REQUEST TYPE</b></td>
					<td width=100px><b>Status</b></td>					
					<td width=100px><b>SITE NAME</b></td>
					<td width=100px><b>INFRASTRUCTURE TYPE</b></td>
					<td width=100px><b>SEVERITY</b></td>
					<td width=100px><b>CRITICAL</b></td>					
					<td width=100px><b>Opened</b></td>
					<td width=100px><b>Estimated Completion Date</b></td>					
					<td width=100px><b>Acknowledgement Time</b></td>
					<td width=100px><b>Fixed</b></td>					
					<td width=100px><b>Closed</b></td>
					<td width=100px><b>Archived</b></td>
				</tr>";
	echo "<tr><td rowspan='20'>No Record Found</td></tr>";
	  header("Pragma: public");
	  header("Expires: 0");
	  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	  header("Cache-Control: private",false);
	  header("Content-Type: application/octet-stream");
	  header("Content-Disposition: attachment;filename=SLA_Report.xls"); 
	  header("Content-Transfer-Encoding: binary");
}

 function getUserName($user_id,$wo_user_list,$mysql)
 {
	  if(!array_key_exists($user_id, $wo_user_list)){
		  $select_wo_user = "SELECT * FROM `users` WHERE `id`='" .$user_id ."'";
		  $select_wo_user_result = $mysql->sqlordie($select_wo_user);
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

 function getUserTitle($user_id,$mysql){
	$select_user_title = "SELECT a.name from lnk_user_subtitles a,user_roles b where a.id = b.category_subcategory_id and b.user_id = '$user_id'";
	$user_title_result = $mysql->sqlordie($select_user_title);
	$user_title = '';
	while($row = $user_title_result->fetch_assoc()){
		$user_title.= $row['name'].",";
	}
	$user_title = substr($user_title,0,-1);
	return $user_title;
 }

 function format_date($date){

	if(!empty($date) and $date!='N/A')
	{
		$str_date = strtotime($date);
		return Date('Y-m-d h:i A', $str_date);
	}
	return 'N/A';
}
function getCompanyName($company_id,$companyListArr,$mysql)
 {
	  if(!array_key_exists($company_id, $companyListArr)){
		  $select_project_company = "SELECT * FROM `companies` where id ='" .$company_id ."'";
		  $project_company_res = $mysql->sqlordie($select_project_company);
		  if($project_company_res->num_rows > 0){
			  $row = $project_company_res->fetch_assoc();
			  $companyListArr[$row['id']] = $row['name'];
		  }
	  }
	return $companyListArr[$company_id];
 }

function getCustomTypeName($workorder_id,$custom_type,$mysql)
{
  $wo_custom_data = $mysql->sqlordie("SELECT `workorder_id`,a.`field_key`,a.`field_id`,c.`field_name` FROM `workorder_custom_fields` a,`workorders` b,`lnk_custom_fields_value` c where a.`workorder_id`='".$workorder_id."' and a.`field_key`='".$custom_type."' and b.id = a.workorder_id and a.`field_id`= c.`field_id`");
  $field_name = 'N/A';
	  if($wo_custom_data->num_rows > 0){
		  $row = $wo_custom_data->fetch_assoc();
		 $field_name =  $row['field_name'];
	  }
   return $field_name;

}

function getAckTimefromAudit($workorder_id,$mysql)
{	
  $wo_audit_data = $mysql->sqlordie("select `log_date` from `workorder_audit` wa where wa.workorder_id='".$workorder_id."' AND wa.status = '7' order by log_date limit 1");
  $log_date = 'N/A';
  if($wo_audit_data->num_rows > 0){
	  $row = $wo_audit_data->fetch_assoc();
	  $log_date =  $row['log_date'];
  }
   return format_date($log_date);

}

function getFixedDatefromAudit($workorder_id,$mysql)
{	
  $wo_latest_reopen_date = $mysql->sqlordie("select max(log_date) as log_date from workorder_audit where workorder_id='".$workorder_id."' AND status = 12");
  $log_date_string = '';
  if($wo_latest_reopen_date->num_rows > 0){
	$row = $wo_latest_reopen_date->fetch_assoc();
	if(!empty($row['log_date'])){	
		$log_date_string = "log_date >= '".$row['log_date']."' AND ";
	}
  }
  $wo_audit_data = $mysql->sqlordie("select `log_date` from `workorder_audit` wa where wa.workorder_id='".$workorder_id."' AND $log_date_string wa.status = '3' order by log_date asc limit 1");
  $log_date = 'N/A';
  if($wo_audit_data->num_rows > 0){
	  $row = $wo_audit_data->fetch_assoc();
	  $log_date =  $row['log_date'];
  }
   return format_date($log_date);

}

?>
