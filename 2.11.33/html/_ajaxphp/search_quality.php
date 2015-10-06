<?php
session_start();
if(!(isset($from_action) && $from_action))
include('../_inc/config.inc');
if(!ISSET($_SESSION['user_id'])){
	die("<b>You are not allowed to access these files</b>");
}
$postingList = Array();
//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
//Defining Global mysql connection values
global $mysql;
if($_SESSION['login_status'] == "client") {
  $client_sql = " AND a.`company`='".$_SESSION['company']."'";
  $client_sql = "";
  $employee = "";
} else {
  $client_sql = "";
  //2 - Producer
  //3 - Project Manager
  //echo $_SESSION['resource'];
  if($_SESSION['resource'] == 2 || $_SESSION['resource'] == 3) {
    $employee = "";
  } else {
    $employee = " b.`assigned_to`='" .$_SESSION['user_id'] . "' AND ";
    $employee = "";
  }
}

$id_quality = $mysql->real_escape_string($_POST['id_quality']);

$archive_sql = " AND `archived`='0'";
$project_archive = " AND b.`archived`='0'";
if('1' == $_POST['status']){
  $archive_sql = " AND `archived`='0'";
  $project_archive = " AND b.`archived`='0'";
}else if('0' == $_POST['status']){
  $archive_sql = " AND `archived`='1'";
  $project_archive = " AND b.`archived`='1'";
}

if(ISSET($_POST['clientId']) && $_POST['clientId'] != '-1'){
	$CLIENT_SQL = " AND a.company = '".$mysql->real_escape_string($_POST['clientId'])."'";
}


$WHERE = array();
$WHERE_SQL = '';
if(ISSET($_POST['assignedTo']) && $_POST['assignedTo'] != '-1'){
	$WHERE[] = " assigned_to = ".$mysql->real_escape_string($_POST['assignedTo']);
}
if(ISSET($_POST['statusId']) && $_POST['statusId'] != '-1' && $_POST['statusId'] != '99' ){
	$status = getQualityStatus($mysql->real_escape_string($_POST['statusId']),$mysql);
	$WHERE[] = " status = ".$status;
}
if($_POST['statusId'] == '99'){
	//closed and Hold
	$WHERE[] = "  status NOT IN ('7','8')";
}
if($_POST['statusId'] == '-1'){
	//closed and Hold
	$WHERE[] = "  status IN ('1','2','3','4','5','6','7','8','10')";
}
if(ISSET($_POST['projectId']) && $_POST['projectId'] != '-1'){
	$WHERE[] = " project_id = ".$mysql->real_escape_string($_POST['projectId']);
}
if(ISSET($_POST['severityID']) && $_POST['severityID'] != '-1'){
	$WHERE[] = " severity = ".$mysql->real_escape_string($_POST['severityID']);
}
/*if($_POST['assignedTo'] != '-1'){
	$WHERE[] = " assigned_to = ".$_POST['assignedTo'];
}*/

if(count($WHERE) > 0){
	$WHERE_SQL = implode(" AND ",$WHERE);
}else{
	$WHERE_SQL = "  status NOT IN ('7','8')";
}
$type = '';
if(array_key_exists("report", $_POST)){
  $type = $mysql->real_escape_string($_POST['report']);
}


$project_query = "SELECT DISTINCT a.`id`, a.`project_name`, a.`project_code`, a.`company` FROM `projects` a, `qa_defects` b, `user_project_permissions` c WHERE a.`id`=b.`project_id` AND a.`id`=c.`project_id` AND a.qa_permission ='1' AND c.`user_id`='" .$_SESSION['user_id'] ."'  ".$CLIENT_SQL." ORDER BY a.`company`, a.`project_name` ASC";
//echo "qry".$project_query.'<br>';
$project_result = $mysql->sqlordie($project_query);
$project_result->num_rows;
$i=0;

$wo_status_array = array();
$wo_user_list = array();
$wo_last_comment_array = array();
$qa_last_audit_array = array();
$qa_project_version = array();
$qa_project_iteration = array();
$qa_project_product = array();

if($project_result->num_rows > 0) {

  $select_wo_status = "SELECT `id`, `name` FROM `lnk_qa_status_types`";
  $status_result = $mysql->sqlprepare($select_wo_status);
  if($status_result->num_rows > 0){
    while($status_row = $status_result->fetch_assoc()){
      $wo_status_array[$status_row['id']] = $status_row['name'];
    }
  }

  $wo_last_comment = "SELECT wc.`id`,wc.`defect_id`,wc.`user_id`,wc.`comment`,wc.`date` FROM `qa_comments` wc, (select max(id) id from `qa_comments` group by `defect_id` ) tab1,`qa_defects` b where wc.id=tab1.id and b.id = wc.`defect_id` $project_archive";

  $wo_last_comment_result = $mysql->sqlordie($wo_last_comment);

  if($wo_last_comment_result->num_rows > 0){
    while($last_comment_row = $wo_last_comment_result->fetch_assoc()){
      $wo_last_comment_array[$last_comment_row['defect_id']] = $last_comment_row;
    }
  }

    $qa_last_action_sql = "SELECT audit.`defect_id`,audit.`audit_id`,audit.`log_user_id`,audit.`assign_user_id`,audit.`status`,audit.`log_date` FROM `quality_audit` audit, (select max(id) id from `quality_audit` group by `defect_id` ) tab1,`qa_defects` b where audit.id=tab1.id and b.id = audit.`defect_id` $project_archive";

	$qa_last_action_audit = $mysql->sqlordie($qa_last_action_sql);

	if($qa_last_action_audit->num_rows > 0){
		while($qa_last_action_row = $qa_last_action_audit->fetch_assoc()){
		  $log_date_array[$qa_last_action_row['defect_id']] = $qa_last_action_row['log_date'];
			if($qa_last_action_row['audit_id']=='1')
			{
			  $qa_last_audit_array[$qa_last_action_row['defect_id']] = "Created : ".timeElapse($qa_last_action_row['log_date'])." ago";
			}else
			{
				 $qa_last_audit_array[$qa_last_action_row['defect_id']] = "Updated : ".timeElapse($qa_last_action_row['log_date'])." ago";
			}
		}
	}

  $select_project_company = "SELECT id,name FROM `companies`";
  $project_company_res = $mysql->sqlprepare($select_project_company);
  $companyListArr;

  if($project_company_res->num_rows > 0){
    while($row = $project_company_res->fetch_assoc()){
      $companyListArr[$row['id']] = $row['name'];
    }
  }

  $qa_custom_data = $mysql->sqlprepare("SELECT field_key,field_id,field_name FROM `lnk_custom_fields_value` where field_key in ('QA_CATEGORY','QA_SEVERITY','QA_OS','QA_BROWSER','QA_ORIGIN') order by field_key ");
  $custom_feild_arr;
  while($row = $qa_custom_data->fetch_assoc())
  {
    $custom_feild_arr[$row['field_key']][$row['field_id']] = $row['field_name'];
  }

  while($project_row = $project_result->fetch_assoc()) {

    $postingList[$i] = Array();
    $postingList[$i]['project_name'] = $project_row['project_name'];
    $postingList[$i]['project_code'] = $project_row['project_code'];
    $postingList[$i]['project_id'] = $project_row['id'];
    $postingList[$i]['company_name'] = $companyListArr[$project_row['company']];
    $postingList[$i]['client'] = $project_row['company'];
    $postingList[$i]['quality'] = Array();

   $select_project_workorders = "SELECT * FROM `qa_defects` WHERE `project_id`='" .$project_row['id'] ."' AND `id` IN (".$id_quality.")  ORDER BY `title` ";

    $project_workorders_result = $mysql->sqlordie($select_project_workorders);

    if($project_workorders_result->num_rows > 0) {
      while($quality = $project_workorders_result->fetch_assoc()) {



         if(!array_key_exists($quality['version'], $qa_project_version)){
	          $select_qa_version = "SELECT * FROM `qa_project_version` WHERE `id`= ? ";
			  $version_result = $mysql->sqlprepare($select_qa_version,array($quality['version']));
			  $version_row = $version_result->fetch_assoc();
			  $userName = '';
		if(strlen($version_row['version_name']) > 16) {
          $elipsee = "...";
        } else {
          $elipsee = "";
        }
			  if(!empty($version_row['version_name']))
			  {

		          $qa_project_version[$quality['version_break']] = htmlentities(substr($version_row['version_name'], 0, 10).$elipsee,ENT_QUOTES,'UTF-8');
				  $qa_project_version[$quality['version']] = $version_row['version_name'];
			  }
		 }
		 //For qa_project_iteration
		 if(!array_key_exists($quality['iteration'], $qa_project_iteration)){
	          $select_qa_version = "SELECT * FROM `qa_project_iteration` WHERE `id`= ? ";
			  $version_result = $mysql->sqlprepare($select_qa_version,array($quality['iteration']));
			  $version_row = $version_result->fetch_assoc();

			  $userName = '';
			  if(!empty($version_row['iteration_name']))
			  {
		          $qa_project_iteration[$quality['iteration']] = $version_row['iteration_name'];
			  }
		 }
		 //for product
		 if(!array_key_exists($quality['product'], $qa_project_product)){
	          $select_qa_version = "SELECT * FROM `qa_project_product` WHERE `id`= ? ";
			  $version_result = $mysql->sqlprepare($select_qa_version,array($quality['product']));
			  $version_row = $version_result->fetch_assoc();
			  $userName = '';
			  if(!empty($version_row['product_name']))
			  {
		          $qa_project_product[$quality['product']] = $version_row['product_name'];
			  }
		 }
        if(!array_key_exists($quality['detected_by'], $wo_user_list)){
          $select_wo_requested_by = "SELECT * FROM `users` WHERE `id`= ? ";
          $requested_result = $mysql->sqlprepare($select_wo_requested_by,array($quality['detected_by']));
          $requested_row = $requested_result->fetch_assoc();
          $userName = '';
          if(!empty($requested_row['last_name']))
          {
            $userName  = $requested_row['last_name'] .", " .$requested_row['first_name'];
          }
          else
          {
            $userName  = $requested_row['first_name'];
          }
          $wo_user_list[$quality['detected_by']] = $userName;
        }
        if(!array_key_exists($quality['assigned_to'], $wo_user_list)){
          $select_wo_assigned_to = "SELECT * FROM `users` WHERE `id`= ? ";
          $assigned_result = $mysql->sqlprepare($select_wo_assigned_to,array($quality['assigned_to']));
          $assigned_row = $assigned_result->fetch_assoc();
          $userName ='';
          if(!empty($assigned_row['last_name']))
          {
            $userName  = $assigned_row['last_name'] .", " .$assigned_row['first_name'];
          }
          else
          {
            $userName  = $assigned_row['first_name'];
          }

          $wo_user_list[$quality['assigned_to']] = $userName;
        }

        $date_time_part = explode(" ", $quality['creation_date']);
        $date_part = explode("-", $date_time_part[0]);
        $overdue_flag = '0';
        $date_offset = array("0" => "4", "1" => "4", "2" => "4", "3" => "6", "4" => "6", "5" => "6", "6" => "5");
        $current_timestamp = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		// New-1, In Progress-2, Fixed - 3 , Rejected - 4,  Reopened - 5, Need More Info - 6 ,  Hold - 7 , Closed - 8, Feedback Provided - 10
        if($quality['status'] == 1 || $quality['status'] == 5 ) {
          $dlClass = 'new';
        } else if($quality['status'] == 3 || $quality['status'] == 8) {
			  $dlClass = 'complete';
        } else if($quality['status'] == 4) {
			 $dlClass = 'alert';
             //$overdue_flag = '1';
        }
		else if($quality['status'] == 7) {
			 $dlClass = 'onhold';
        }
		else
		{
			 $dlClass = '';
		}
		$requested_by=$wo_user_list[$quality['requested_by']];
        $detectedby = $wo_user_list[$quality['detected_by']];
        $assigned = $wo_user_list[$quality['assigned_to']];

        if(strlen($quality['title']) > 40) {
          $elipse = "...";
        } else {
          $elipse = "";
        }

        $last_comment_row =$wo_last_comment_array[$quality['id']];

        if(!empty($last_comment_row))
        {
          if(!empty($last_comment_row['defect_id']))
          {
            $wo_last_comment  = $last_comment_row['comment'] ;
            $wo_last_comment_user_id  = $last_comment_row['user_id'] ;
            $wo_last_comment_date  = $last_comment_row['date'] ;
          }
          else
          {
            $wo_last_comment  = 'N/A';
            $wo_last_comment_user_id  = 'N/A';
            $wo_last_comment_date  = 'N/A';
            $wo_last_comment_user = 'N/A';
          }

          if(!array_key_exists($wo_last_comment_user_id, $wo_user_list)){
            $select_wo_last_comment = "SELECT * FROM `users` WHERE `id`= ? ";
            $last_comment_result = $mysql->sqlprepare($select_wo_last_comment,array($wo_last_comment_user_id));
            $last_comment_row = $last_comment_result->fetch_assoc();

            $userName ='';
            if(!empty($last_comment_row['last_name']))
            {
              $userName  = $last_comment_row['last_name'] .", " .$last_comment_row['first_name'];
            }
            else
            {
              $userName  = $last_comment_row['first_name'];
            }
            $wo_last_comment_user = $userName;
          }
          else
          {
            $wo_last_comment_user = $wo_user_list[$wo_last_comment_user_id];
          }
        }
        else
        {
          $wo_last_comment  = 'N/A';
          $wo_last_comment_user_id  = 'N/A';
          $wo_last_comment_date  = 'N/A';
          $wo_last_comment_user = 'N/A';
        }
		$qa_last_action = "";
		if(empty($qa_last_audit_array[$quality['id']]))
		{
			$qa_last_action = "N/A";
		}
		else
		{
			$qa_last_action = $qa_last_audit_array[$quality['id']];
			$log_date_val = $log_date_array[$quality['id']];
		}
		if($quality['active']!=0){$active='Yes';}else{$active='No';}
      if($quality['deleted']!=0){$deleted='Yes';}else{$deleted='No';}
      if($quality['archived']!=0){$archived='Yes';}else{$archived='No';}
      if ((!empty($quality['closed_date']))){$closed_date=format_date($quality['closed_date']);}else {$closed_date='No';}
		  $select_qa_browser = "SELECT * FROM `lnk_custom_fields_value` WHERE `field_id`= ? ";
          $assigned_result = $mysql->sqlprepare($select_qa_browser,array($quality['browser']));
          $assigned_row_qa_browser = $assigned_result->fetch_assoc();

          $select_qa_os = "SELECT * FROM `lnk_custom_fields_value` WHERE `field_id`= ? ";
          $assigned_result_os = $mysql->sqlprepare($select_qa_os,array($quality['os']));
          $assigned_row_qa_os = $assigned_result_os->fetch_assoc();

           $select_qa_origin = "SELECT * FROM `lnk_custom_fields_value` WHERE `field_id`= ? ";
          $assigned_result_origin = $mysql->sqlprepare($select_qa_origin,array($quality['origin']));
          $assigned_row_qa_origin = $assigned_result_origin->fetch_assoc();

      array_push($postingList[$i]['quality'],Array('id' => $quality['id'], 'title' => htmlentities(substr($quality['title'], 0, 37).$elipse,ENT_QUOTES,'UTF-8'), 'full_title' => htmlentities($quality['title'],ENT_QUOTES,'UTF-8'), 'status' => $wo_status_array[$quality['status']],'severity' => $custom_feild_arr['QA_SEVERITY'][$quality['severity']],'severity_id' => $quality['severity'],'category' => $custom_feild_arr['QA_CATEGORY'][$quality['category']],'version' => $qa_project_version[$quality['version']],'version_break' => $qa_project_version[$quality['version_break']], 'detected_by' => $detectedby, 'assigned_to' => $assigned,'assigned_to_id' => $quality['assigned_to'],'creation_date' => $quality['creation_date'] ,'open_date' => format_date($quality['creation_date']), 'assigned_date' => format_date($quality['assigned_date']), 'completed' => format_date($quality['completed_date']),'launch_date' => format_date($quality['launch_date']), 'class' => $dlClass, 'overdue_flag' => $overdue_flag,'wo_last_comment'=>nl2br(htmlentities($wo_last_comment,ENT_QUOTES,'UTF-8')),'wo_last_comment_user_id'=>$wo_last_comment_user_id,'wo_last_comment_user'=>$wo_last_comment_user ,'last_log_date'=>$log_date_val,'wo_last_comment_date'=>format_date($wo_last_comment_date),'qa_last_action'=>$qa_last_action ,'qa_requested_by'=>$requested_by, 'example_url'=>$quality['example_url'], 'body'=>preg_replace("/\s+/",' ',preg_replace("/[\r\n]+/", "\n",$quality['body'])),'active'=>$active,'deleted'=>$deleted,'archived'=>$archived,'closed_date'=> $closed_date,'browser'=>$assigned_row_qa_browser['field_name'],'os'=>$assigned_row_qa_os['field_name'],'origin'=>$assigned_row_qa_origin['field_name'],'iteration' =>$qa_project_iteration[$quality['iteration']],'product' =>$qa_project_product[$quality['product']] ));
      }
    }
    $i=$i+1;
  }
}

function format_date($date){
  if(!empty($date)){
    $str_date = strtotime($date);
    return Date('m-d-Y h:i A', $str_date);
  }else
  return $date;
}

function timeElapse($log_date)
{
	$current_date = mktime();
	if(!empty($log_date))
	{
	   $str_date = strtotime($log_date);
	   $diff = $current_date - $str_date;
	   $min = ceil($diff / (60)) ;
		$hrs = '0';
		$days = '0';
		$lapseTxt = "";
		if($min>='60')
		{
			$hrs = $min/60;
		}

		if($hrs>='24')
		{
			$days = $hrs/24;
			$hrs = $hrs%24;
		}
		$lapseTxt = floor($days)." d ".floor($hrs)." h";
	    return  $lapseTxt;
	}
	return "0 d 0 h";
}

if(isset($from_action) && $from_action){
  //		return $postingList;
}else if($type == 'excel'){

  $header = "Id\t Project\t Company\t Title\t  Category\t Example Url\t Description\t Status\t Severity\t Version\t Browser\t OS\t Origin\t Iteration\t Product\t  Requested By\t Detected By\t Assigned To\t Open Date\t Assigned Date\t Completed Date\t Closed Date\t Last Action\t Active\t Deleted\t Archived\n";
  $excel_body = '';

  $clientId = $_POST['clientId'];
  $projectId = $_POST['projectId'];
  $statusId = $_POST['statusId'];
  $assignedTo = $_POST['assignedTo'];
  $rp_severity_filter = $_POST['severityID'];
  
  
  if($rp_severity_filter == 'Show All'){
    $rp_severity_filter = -1;
  }
  foreach($postingList as $project){
    if(($clientId < 0 || $clientId == $project['client']) && ($projectId < 0 || $projectId == $project['project_id'])){
      foreach($project['quality'] as $wo){
         if(($statusId < 0 || ($statusId == 99 && $wo['status'] != 'Closed') || $statusId == $wo['status']) && ($assignedTo < 0 || $assignedTo == $wo['assigned_to_id']) && ($rp_severity_filter < 0 || $rp_severity_filter == $wo['severity'])){

         $excel_body .=  $wo['id'] . "\t " .
          $project['project_code'] . " : " . $project['project_name'] . "\t " .
          $project['company_name'] . "\t " .
		  $wo['full_title'] . "\t " .
          $wo['category'] . "\t " .
          $wo['example_url'] . "\t" .
          $wo['body'] . "\t" .
          $wo['status'] . "\t " .
          $wo['severity'] . "\t " .
          $wo['version'] . "\t " .
          $wo['browser'] . "\t " .
          $wo['os'] . "\t " .
          $wo['origin'] . "\t " .
		  $wo['iteration'] . "\t " .
		  $wo['product'] . "\t " .
     	  $wo['qa_requested_by'] . "\t " .
          $wo['detected_by'] . "\t " .
          $wo['assigned_to'] . "\t " .
          $wo['open_date'] . "\t " .
          $wo['assigned_date'] . "\t " .
          $wo['completed'] . "\t" .
          $wo['closed_date'] . "\t" .
          $wo['qa_last_action'] . "\t".
          $wo['active'] . "\t" .
          $wo['deleted'] . "\t" .
          $wo['archived'] . "\n";
        }
      }
    }
  }

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=qa_report.xls");
	header("Content-Transfer-Encoding: binary ");
	echo $header;
	echo $excel_body;

}else{
  	// p($postingList);
  $jsonSettings = json_encode($postingList);

  // output correct header
  $isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ?
  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
  ($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');

  echo $jsonSettings;
}

function getQualityStatus($name,$mysql){
	//global $mysql;
	$select_wo_status = "SELECT `id` FROM `lnk_qa_status_types` WHERE name = ? ";
  	$status_result = $mysql->sqlprepare($select_wo_status,array($name));
  	if($status_result->num_rows > 0){
    	$status_row = $status_result->fetch_assoc();
      	return $status_row['id'];

  }
}
?>
