<?php
class commentServices {

	private static $instance;
    private $count = 0;
	public $config;
	
	
 	public static function singleton()
    {
        if (!isset(self::$instance)) {
            //echo 'Creating new instance.';
            $mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
            self::$instance = $mysql;
        }
        return self::$instance;
    }
	/**
	 * Test function
	 * 
	 * @param Object $userInfo
	 * @param Object $Workorder
	 * @return String
	 */
	 public function saveLHComment($userInfo, $Workorder) {
	 	$mysql = self::singleton();
	 	$userName = $mysql->real_escape_string($userInfo->useremail);
	 	$usersSql = "SELECT id FROM `users` WHERE email = '$userName' AND active='1' and deleted ='0' LIMIT 0,1";
	 	$userCheck = $mysql->query($usersSql);
	 	$userResult = $userCheck->fetch_assoc();
	 	
	 	if(count($userResult) > 0){
	 		$wid = $Workorder->wid;
	 		$uid = $userResult['id'];
	 		$comment = $Workorder->comment;
	 		$curDateTime = date("Y-m-d H:i:s");
	 		$update_wo_comment = "INSERT INTO `workorder_comments` (`workorder_id`,`user_id`,`comment`,`date`) "
				."VALUES ('$wid','$uid','$comment','$curDateTime')";
			$mysql->query($update_wo_comment);
			$bc_id_query = "SELECT  `bcid`, `project_id`, `title`, `priority`,`status`,`assigned_to`,`body` FROM `workorders` WHERE `id`='" .$mysql->real_escape_string($wid) ."' LIMIT 1";
			$bc_id_result = $mysql->query($bc_id_query);
			$bc_id_row = $bc_id_result->fetch_assoc();
			$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$wid' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
			$req_type_res = $mysql->query($select_req_type_qry);
			$req_type_row = $req_type_res->fetch_assoc();
			$this->insertWorkorderAudit($wid, '4', $uid,$bc_id_row['assigned_to'],$bc_id_row['status'],$curDateTime );
			return "success";
	 	}else{
	 		return "failed";
	 	}
	    
	    /*return "you passed me ".$a." ".$b;*/
	 }
	 
	private function insertWorkorderAudit($wo_id, $audit_id, $log_user_id,$assign_user_id,$status,$curDateTime )
	{
		
		$mysql = self::singleton();
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."','".$curDateTime."')";
		@$mysql->query($insert_custom_feild);
	}
	
	 
	
}

/*if($_POST['lh_submit']){
		require_once('../_inc/config.inc');
		$c = new commentServices();
		$u = new stdClass();
	    	$w = new stdClass();
	    	$u->useremail = $_POST['lh_email'];
	        $w->wid = $_POST['lh_wid'];
	     	$w->comment = $_POST['lh_wid'];
	     
	     	echo $c->saveLHComment($u,$w);
		}else{
			echo " Bad request";
		}
*/
//echo "test"; 
