<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
		$projectId = $mysql->real_escape_string($_GET['projectId']);

		$cc = $mysql->real_escape_string($_GET['cc']);
		$ccArray = array();
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
			
			$update_cc = "UPDATE `projects` SET `cclist`='$arrayData' WHERE `id`='$projectId'";
			@$mysql->sqlordie($update_cc);

                     $select_cc = "SELECT `cclist` FROM `projects` WHERE `id`='$projectId' LIMIT 1";
			$result = @$mysql->sqlordie($select_cc);
			$row = @$result->fetch_assoc();

			if($result->num_rows > 0) {
				$new_list = explode(",", $row['cclist']);
				$list = "";
				
				for($x = 0; $x < sizeof($new_list); $x++) {
					if(!empty($new_list[$x])) {
						$select_cc_user = "SELECT * FROM `users` WHERE `id`='" .$new_list[$x] ."' LIMIT 1";
						$cc_user_result = @$mysql->sqlordie($select_cc_user);
						$cc_user_row = @$cc_user_result->fetch_assoc();
						
						$list .= '<li class="admincc_listli">'
                                         ."<div class=\"admincclist_name\">" .ucfirst($cc_user_row['first_name']) ." " .ucfirst($cc_user_row['last_name']) ."</div>"
                                               	."<button class=\"status admincclist_remover\" onClick=\"removeCcUser(" .$new_list[$x] .",'".$projectId."'); return false;\"><span>remove</span></button>"
								."</li>";
					}
				}				
				echo $list;				
			}

               
?>