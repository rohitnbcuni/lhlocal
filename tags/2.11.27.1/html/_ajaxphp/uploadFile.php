<?PHP
	if(isset($_POST['dirName'])) {
		@session_start();
		include('../_inc/config.inc');
	
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		global $mysql;
		$woIdClean = $mysql->real_escape_string($_POST['workorder_id']);
		$woId = "'" .$woIdClean ."'";
		$dirName = $mysql->real_escape_string($_POST['dirName']);
		
		$cleaned_filename = str_replace(" ", "_", ($_FILES['upload_file']['name']));
		$cleaned_filename = str_replace("'", "_", $cleaned_filename);
		$cleaned_filename = str_replace('"', "_",$cleaned_filename);
		
		//$ext = array('.jpg','.jpeg','.png','.gif','.tiff','.bmp','.html','.txt','.xml','.xls','.xlsx','.pdf','.doc','.docx','.zip','.tar','.flv','.mp4','.JPG','.JPEG','.PNG','.TIFF','.BMP','.HTML','.TXT','.XML','.XLS','.XLSX','.PDF','.DOC','.DOCX');
		$ext = unserialize(ALLOWED_FILE_EXTENSION);

		/*if(!in_array(strrchr($cleaned_filename,'.'),$ext))
		die ("You cannot upload in that format"); 
		
		if (filesize($_FILES['upload_file']['size']) > 10242880)
		
		die("Exdeed");
		*/
		$mimitype_extension = ".".pathinfo($_FILES['upload_file']['name'], PATHINFO_EXTENSION);
		if(!in_array($mimitype_extension,$ext)){
			$cleaned_filename = '';
			die ("You cannot upload in that format"); 
				
		}
		if(!in_array(strrchr($cleaned_filename,'.'),$ext)){
			$cleaned_filename = '';
			die ("You cannot upload in that format"); 
		}	
		
		if ($_FILES['upload_file']['size']> MAX_UPLOAD_FILE_SIZE){
			$cleaned_filename = '';
		
			die("The file size of the attachment is more than uploading limit");
		
		}	
		if(!is_numeric($woIdClean)) {
			$woId = "null";
		} else {
			$dirName = $woIdClean ."/";
		}
		
		if(!empty($cleaned_filename)){
		$select_file = "SELECT * FROM `workorder_files` WHERE `directory`='" .str_replace("/", "", $dirName) ."' AND `file_name`='" .$cleaned_filename ."' LIMIT 1";
		$result = $mysql->sqlordie($select_file);
		
		//if (!copy($_FILES['upload_file']['tmp_name'], WEBPATH .'/files/'.md5($_FILES['upload_file']['name']))) {
		if (!copy($_FILES['upload_file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] .'/files/' .$dirName .$cleaned_filename)) {
//			header ("Location: /workorders/index/edit/?wo_id=".$woIdClean."&fileUpload=0");
			echo "fail";
		} else {
				chmod($_SERVER['DOCUMENT_ROOT'] .'/files/' .$dirName .$cleaned_filename, 0744);
				if($result->num_rows == 1) {
				$row = $result->fetch_assoc();
				$update_row = "UPDATE `workorder_files` SET `workorder_id`='$woId', `upload_date`=NOW() WHERE `id`='" .$row['id'] ."'";
				$mysql->sqlordie($update_row);
			} else {
				$insert_image = "INSERT INTO `workorder_files` "
					."(`workorder_id`,`directory`,`file_name`,`upload_date`,`deleted`) "
					."VALUES "
					."($woId,'" .str_replace("/", "", $dirName) ."','" .$cleaned_filename ."',NOW(),'1')";
				$mysql->sqlordie($insert_image);
				//$entryId = $mysql->insert_id;
			}
			echo "success";
//			header ("Location: /workorders/index/edit/?wo_id=".$woIdClean."&fileUpload=1");
			
			
			/*$update_image = "UPDATE `workorder_files` SET `deleted`='0' WHERE `id`='$entryId'";
			$mysql->query($update_image);
			$mysql->error;*/
		}
		}
	}
	
//	echo '<script type="text/javascript">
//		window.onload = function() {
//			window.parent.updateFileList();
//		}
//	</script>';
?>
