<?PHP
	include('../_inc/config.inc');  
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);  
	$OP = $mysql->real_escape_string(@$_REQUEST['OP']);	
	$field_id = $mysql->real_escape_string(@$_REQUEST['fieldid']);
	$field_name = $mysql->real_escape_string(@$_REQUEST['fieldname']);
	$customname = $mysql->real_escape_string(@$_REQUEST['custom_name']);

	$fieldDeleteStatus = $mysql->real_escape_string(@$_REQUEST['fieldDeleteStatus']);
	$fieldActiveStatus = $mysql->real_escape_string(@$_REQUEST['fieldActiveStatus']);

	if($OP == 'ADD')
	{

		$maxOrderIdSql = "SELECT MAX(sort_order)+1 AS sortId FROM `lnk_custom_fields_value` WHERE field_key = '".$customname."'";
		$maxOrderId = $mysql->sqlordie($maxOrderIdSql);
		$maxOrderId_row = $maxOrderId->fetch_assoc();
      	$insert_site = "INSERT INTO `lnk_custom_fields_value` (`field_name`, `field_key` ,`sort_order`, `active`, `deleted`) VALUES ('".$field_name."', '".$customname."', '". $maxOrderId_row['sortId']."', '".$fieldActiveStatus."', '".$fieldDeleteStatus."')";
		$mysql->sqlordie($insert_site);
	}
	else if($OP == 'UPDATE')
	{
		$update_site = "UPDATE `lnk_custom_fields_value` SET `field_name`='".$field_name."', `active`='". $fieldActiveStatus."', `deleted`='". $fieldDeleteStatus."' WHERE `field_id`='" . $field_id ."'";
		$mysql->sqlordie($update_site);
	}
	echo $OP;
?>
