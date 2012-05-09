<?PHP
	include('../_inc/config.inc');  

	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);  
	$userID = $mysql->real_escape_string(@$_REQUEST['user_id']);
	$userTitles = $_REQUEST['userTitle'];
	$userTitlesArray = explode(",",$userTitles);
	if(!empty($userID))
	{
		$update_role = "UPDATE `users` SET `user_title`='".$_REQUEST['userTitle']."',`role`='" . $_REQUEST['userRole']."',`agency`='". $_REQUEST['userVendorName'] ."',`program`='".$_REQUEST['userProgram']."',`active`='". $_REQUEST['userActiveStatus']."',`deleted`='". $_REQUEST['userDeletedStatus']."',`login_status`='". $_REQUEST['userAdminAccess']."',`user_access`='".$_REQUEST['user_access_bit']."' where `id`='" . $userID ."'";
		$mysql->query($update_role);
	}
	if($_REQUEST['isUserTitleChanged']=='Y'){
		$deleteQuery = "DELETE FROM `user_roles` WHERE user_id=".$userID;
		$mysql->query($deleteQuery );
		$insertQuery = "INSERT INTO `user_roles` (`user_id`, `category_subcategory_id`, `flag`, `creation_date`, `active`, `deleted`) VALUES ";
		for($i=0;$i<count($userTitlesArray);$i++){ 
			$pos = strpos($userTitlesArray[$i], "subcat_");
			if($pos === false){
				$insertQuery .= "('{$userID}', ".str_replace("subcat_","",$userTitlesArray[$i]).", 'category','".date("Y-m-d H:i:s")."', '1', '0')"; 
			}else{
				$insertQuery .= "('{$userID}', ".str_replace("subcat_","",$userTitlesArray[$i]).", 'subcategory','".date("Y-m-d H:i:s")."', '1', '0')"; 
			}
			if(count($userTitlesArray)>1 && count($userTitlesArray)-1!=$i){
				$insertQuery .= ","; 
			}   
		}
		$mysql->query($insertQuery );
	}
 
?>