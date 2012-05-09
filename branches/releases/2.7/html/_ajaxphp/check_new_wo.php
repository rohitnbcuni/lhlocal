<?php
	session_start();
	include('../_inc/config.inc');
	include('../_ajaxphp/sendEmail.php');
	include('../_ajaxphp/util.php');
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	if(isset($_SESSION['user_id'])) {
		$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		$woId = $mysql->real_escape_string($_POST['wid']);
		$last_wid = $mysql->real_escape_string($_POST['last_wid']);
		$select_comments = "SELECT * FROM `workorder_comments` WHERE `workorder_id`='$woId' AND id = $last_wid order by id LIMIT 0, 1";
		$comm_result = @$mysql->query($select_comments);
		$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";	
		$comment_id =0;
		if($comm_result->num_rows > 0){
		while($comRow = $comm_result->fetch_assoc()) {
			$comment_id = $comRow["id"]; 
			$select_user = "SELECT * FROM `users` WHERE `id`='" .$comRow['user_id'] ."' LIMIT 1";
			$user_result = @$mysql->query($select_user);
			$user_row = $user_result->fetch_assoc();
			
			$date_time_split = explode(" ", $comRow['date']);
			$date_split = explode("-", $date_time_split[0]);
			$time_split = explode(":", $date_time_split[1]);
			$date = date("D M j \a\t g:i a", mktime($time_split[0],$time_split[1],$time_split[2],$date_split[1],$date_split[2],$date_split[0]));
			$cmnt = $comRow['comment'];
			//$text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",str_replace('&#129;','&#153;',htmlentities($cmnt)));
			/**
			 * Ticket No 16857,19352
			 * Special Character display 
			 * @var test Comment type
			 */
			 $text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",str_replace('&#129;','&#153;',htmlentities($cmnt,ENT_NOQUOTES, 'UTF-8')));
			//$text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",str_replace('&#129;','&#153;',html_entity_decode($cmnt,ENT_QUOTES,'ISO-8859-1')));
			$text_string=nl2br($text_string);
			$comment_html .= '<li id="comment_id_li_'.$comment_id.'">
				<img src="'.$user_row['user_img'].'" class="comment_photo" />
				<div class="comment_body">
					<p><strong>' .ucfirst($user_row['first_name']) ." " .ucfirst($user_row['last_name']) .'</strong><br><em>' .$date .'</em></p>
					<p>' . $text_string .'</p>
				</div>
			</li>';
		}
		echo $comment_html;
		//echo $comment_html .= '<li style="border-bottom:none;"><div id="new_comment"><input type="hidden" id="last_comment_id" name="last_comment_id" value="'.$comment_id.'"></div></li>';
		}
	}