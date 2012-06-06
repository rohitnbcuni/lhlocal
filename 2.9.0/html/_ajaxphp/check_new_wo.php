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
		$select_comments = "SELECT * FROM `workorder_comments` WHERE `workorder_id`='$woId' AND id = $last_wid AND deleted ='0' order by id LIMIT 0, 1";
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
			$comment_delete = '';
			$comment_update_box = '';
			$date_diff = dateDiffComment($comRow['date']);
			if($date_diff['years'] == 0 && $date_diff['days'] == 0 && $date_diff['months'] == 0 && $date_diff['hours'] == 0 && $date_diff['minuts'] <= 15){

				if($comRow['user_id'] == $_SESSION['user_id']){
					$comment_delete = "<div id='edit_pannel_".$comment_id."'> <span id='comment_edit' style='padding-left:10px;' onclick='displayCommentBox(".$comment_id.");'> <img src='/_images/b_edit.png' alt='Edit' title='Edit'></span><span style='padding-left:10px;' id='comment_delete' onclick='deleteComment(".$comment_id.");'><img src='/_images/b_drop.png' alt='Delete' title='Delete'></span></div>";
					$comment_update_box ='<div id="comment_id_li_body_'.$comment_id.'" class="panel" >
							<form name="1" method="post" action="">
							<textarea id="comment_id_li_textarea_'.$comment_id.'"  class ="field_large" >'.htmlentities($cmnt,ENT_NOQUOTES, 'UTF-8').'</textarea><br>
							<div class="new_comment_actions">
							<br/>															
							<button onclick="updateComment('.$comment_id.'); return false;" class="secondary" style="padding-left:190px;">
								<span>Update Comment</span>
							</button>
							</div>

							</form>
							<input type="hidden" class="comment_id_li_comment_id" name="comment_id_li_comment_id[]"  value="'.$comment_id.'">
							<input type="hidden" id="comment_id_li_comment_time_'.$comment_id.'"  name="comment_id_li_comment_time_'.$comment_id.'"  value="'.$comRow['date'].'">
							</div>';
				}
			}
			
			$comment_html .= '<li id="comment_id_li_'.$comment_id.'">
				<img src="'.$user_row['user_img'].'" class="comment_photo" />
				<div class="comment_body">
					<p><strong>' .ucfirst($user_row['first_name']) ." " .ucfirst($user_row['last_name']) .'</strong><br><em>' .$date .' '.$comment_delete.'</em></p>
					<p id="comment_id_li_msg_'.$comment_id.'">' . $text_string .'</p>
					'.$comment_update_box.'
				</div>
			</li>';
		}
		echo $comment_html;
		//echo $comment_html .= '<li style="border-bottom:none;"><div id="new_comment"><input type="hidden" id="last_comment_id" name="last_comment_id" value="'.$comment_id.'"></div></li>';
		}
	}
	
	function dateDiffComment($commentDate){ 
				$dateDiff = array();
				
				

				$diff = abs(strtotime($commentDate) - time()); 

				$years   = floor($diff / (365*60*60*24)); 
				$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
				$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

				$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 

				$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 

				$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
				$dateDiff['years'] = $years;
				$dateDiff['months'] = $months;
				$dateDiff['days'] = $days;	
				$dateDiff['hours'] = $hours;
				$dateDiff['minuts'] = $minuts;
				$dateDiff['seconds'] = $seconds;		
				return $dateDiff;
						
			}