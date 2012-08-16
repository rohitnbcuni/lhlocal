<?PHP
	include('../_inc/config.inc');

	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	$feildKEY = $_GET['feildKEY'];	

	$QRY_MASTER_SELECT ="SELECT `field_name`,`field_id` FROM `lnk_custom_fields_value` cfv,`lnk_custom_fields` cf where cfv.`field_key` ='".$feildKEY."' and cfv.field_key = cf.field_key and cf.active='1' and cf.deleted='0' and cfv.active='1' and cfv.deleted='0' order by sort_order";

	$fields_list = $mysql->query($QRY_MASTER_SELECT);
	$selected = "";
	if($fields_list->num_rows > 0) {
		while($row = $fields_list->fetch_assoc()) {
			$html .= '<option value="' .$row['field_id'] .'"' .$selected .'>' 
							.$row['field_name'].'</option>';
		}
	}  
	
	echo $html;
?>