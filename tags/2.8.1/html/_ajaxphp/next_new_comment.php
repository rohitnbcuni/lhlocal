<?php
	session_start();
	include('../_inc/config.inc');
	include('../_ajaxphp/sendEmail.php');
	include('../_ajaxphp/util.php');
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	if(isset($_SESSION['user_id'])) {
		$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		$woId = $mysql->real_escape_string($_POST['wid']);
		if(trim($woId) != ''){
		$last_wid = $mysql->real_escape_string($_POST['last_wid']);
		if($last_wid == ''){
			$select_comments = "SELECT id, user_id FROM `workorder_comments` WHERE `workorder_id`='$woId' order by id LIMIT 0, 1";

		}else{
			$select_comments = "SELECT id, user_id FROM `workorder_comments` WHERE `workorder_id`='$woId' AND id > $last_wid order by id LIMIT 0, 1";
		}
		$comm_result = @$mysql->query($select_comments);
		$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";	
		$comment_id =0;
		if($comm_result->num_rows > 0){
			$comRow = $comm_result->fetch_assoc();
			$select_user = "SELECT CONCAT_WS(' ',first_name,last_name) as fullname FROM `users` WHERE `id`='" .$comRow['user_id'] ."' LIMIT 1";
			$user_result = @$mysql->query($select_user);
			$user_row = $user_result->fetch_assoc();
			echo $comment_id = $comRow["id"]."##".$user_row['fullname']; 
			}
	}
	}
	