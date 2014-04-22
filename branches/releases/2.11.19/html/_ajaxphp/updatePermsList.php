<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	
	$projectId = $mysql->real_escape_string($_POST['projectId']);
	$html = '';
	$list = Array();
	
	$default = $mysql->sqlordie("SELECT a.`id`,a.`name` FROM `companies` a, `projects` b WHERE b.`id`='$projectId' AND a.`id`=b.`company` LIMIT 1");
	$default_row = $default->fetch_assoc();
	
	if(!empty($projectId)) {
		$get_perms = "SELECT * FROM `user_project_permissions` WHERE `project_id`='$projectId'";
		$res = $mysql->sqlordie($get_perms);
		
		if($res->num_rows > 0) {
			while($row = $res->fetch_assoc()) {
				$get_user = "SELECT * FROM `users` WHERE `id`='" .$row['user_id'] ."' LIMIT 1";
				$res_user = $mysql->sqlordie($get_user);
				$user_row = $res_user->fetch_assoc();
				
				$list[$user_row['company']][$user_row['id']] = true;
			}
		}
		
		$html .= '<li class="static_perm">
				<div class="wo_perms_company">
					' .$default_row['name'] .'
				</div>
				<div class="wo_perms_users">
					All Users
				</div>
				<div style="clear: both;"></div>
		</li>';
		
		$comp_keys = array_keys($list);
		for($i = 0; $i < sizeof($comp_keys); $i++) {
			$get_comp = "SELECT * FROM `companies` WHERE `id`='" .$comp_keys[$i]."' LIMIT 1";
			$res_comp = $mysql->sqlordie($get_comp);
			$comp_row = $res_comp->fetch_assoc();
			
			$html .= '<li class="static_perm">
				<div class="wo_perms_company">
					' .substr(ucfirst($comp_row['name']), 0, 30) .'
				</div>
				<div class="wo_perms_users">
					<select id="edit_' .$i .'" name="edit_' .$i .'[]" multiple="multiple" size="5">
						<option value=""></option>';
				
			
			$get_user_list = "SELECT * FROM `users` WHERE `company`='" .$comp_keys[$i] ."'";
			$res_user_list = $mysql->sqlordie($get_user_list);
			
			while($user_list_row = $res_user_list->fetch_assoc()) {
				if(isset($list[$comp_keys[$i]][$user_list_row['id']])) {
					$selected = " SELECTED ";
				} else {
					$selected = "";
				}
				
				$html .= '<option value="' .$user_list_row['id'] .'" ' .$selected .'>' .ucfirst($user_list_row['first_name']) .' ' .ucfirst($user_list_row['last_name']) .'</option>';
			}
			
			$html .= '</select>
					<button class="remove_perms" name="save" id="company_' .$comp_keys[$i] .'" onClick="deletePerm(\'company_' .$comp_keys[$i] .'\');changeSectionStatusMan(\'sec_11\'); setCompleteness();return false;">&nbsp;</button>	
					</div>
					<div style="clear: both;"></div>
				</li>';
		}
	}

	//$mysql->close();
	echo $html;
?>