<?php
	session_start();
	include('../_inc/config.inc');
	include('../_ajaxphp/sendEmail.php');
	include('../_ajaxphp/util.php');
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	if(isset($_SESSION['user_id'])) {
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		global $mysql;
		$woId = $mysql->real_escape_string($_POST['wid']);
		if(trim($woId) != ''){
		$last_wid = $mysql->real_escape_string($_POST['last_wid']);
		if($last_wid == ''){
			$select_comments = "SELECT id, user_id FROM `workorder_comments` WHERE `workorder_id`='$woId' AND `active` = '1' AND `deleted` = '0' order by date desc LIMIT 0, 1";

		}else{
			$select_comments = "SELECT id, user_id FROM `workorder_comments` WHERE `workorder_id`='$woId' AND `active` = '1' AND `deleted` = '0' AND id > $last_wid  AND date > (SELECT max(date) FROM  `workorder_comments` WHERE `workorder_id`='$woId' AND id = $last_wid AND `active` = '1' AND `deleted` = '0') order by date desc LIMIT 0, 1";
		}
		//echo $select_comments;
		$comm_result = @$mysql->sqlordie($select_comments);
		$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";	
		$comment_id =0;
		if($comm_result->num_rows > 0){
			$comRow = $comm_result->fetch_assoc();
			$select_user = "SELECT CONCAT_WS(' ',first_name,last_name) as fullname FROM `users` WHERE `id`='" .$comRow['user_id'] ."' LIMIT 1";
			$user_result = @$mysql->sqlordie($select_user);
			$user_row = $user_result->fetch_assoc();
			echo $comment_id = $comRow["id"]."##".$user_row['fullname'].'##'.date("Y-m-d H:i:s"); 
			}else{
			echo $comment_id = ' '."##".' '.'##'.date("Y-m-d H:i:s");
			}
	}
	}
	
