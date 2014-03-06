<?PHP
	if(isset($_POST['dirName'])) {
		@session_start();
		include('../_inc/config.inc');
		include("sessionHandler.php");
	
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		//Defining Global mysql connection values
	    global $mysql;
		$defectIdClean = $mysql->real_escape_string(@$_POST['defect_id']);
		$defectId = "'" .$defectIdClean ."'";
		$dirName = $mysql->real_escape_string(@$_POST['dirName']);
		
		$cleaned_filename = str_replace(" ", "_", $_FILES['upload_file']['name']);
		$cleaned_filename = str_replace("'", "_", $_FILES['upload_file']['name']);
		

//	 $ext = array('.jpg','.jpeg','.png','.gif','.tiff','.bmp','.html','.txt','.xml','.xls','.xlsx','.pdf','.doc','.docx','.zip','.tar','.flv','.mp4','.JPG','.JPEG','.PNG','.TIFF','.BMP','.HTML','.TXT','.XML','.XLS','.XLSX','.PDF','.DOC','.DOCX');
		$ext = unserialize(ALLOWED_FILE_EXTENSION);	
		/*if(!in_array(strrchr($cleaned_filename,'.'),$ext))
		die ("You cannot upload in that format"); 
		
		if (filesize($_FILES['upload_file']['size']) > 10242880)
		
		die("Exdeed");
                 */
		if(!in_array(strrchr($cleaned_filename,'.'),$ext)){
			die ("You cannot upload in that format"); 
		}	
		
		if ($_FILES['upload_file']['size']> MAX_UPLOAD_FILE_SIZE){
		
			die("The file size of the attachment is more than 10MB");
		
		}
		 
		if(!is_numeric($defectIdClean)) {
			$defectId = "null";
		} else {
			$dirName = $defectIdClean ."/";
		}

		$select_file = "SELECT * FROM `qa_files` WHERE `directory`=  ? AND `file_name`= ? LIMIT 1";
		$result = $mysql->sqlprepare($select_file, array(str_replace("/", "", $dirName), $cleaned_filename ));	

		if (!copy($_FILES['upload_file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] .'/qafiles/' .$dirName .$cleaned_filename)) {
			echo "fail";
		} else {
			chmod($_SERVER['DOCUMENT_ROOT'] .'/files/' .$dirName .$cleaned_filename, 0744);
			if($result->num_rows == 1) {
				$row = $result->fetch_assoc();
				$update_row = "UPDATE `qa_files` SET `defect_id`='$defectId', `upload_date`=NOW() WHERE `id`='" .$row['id'] ."'";
				$mysql->sqlordie($update_row);
			} else {
				$insert_image = "INSERT INTO `qa_files` "
					."(`defect_id`,`directory`,`file_name`,`upload_date`,`deleted`) "
					."VALUES "
					."($defectId,'" .str_replace("/", "", $dirName) ."','" .$cleaned_filename ."',NOW(),'1')";
				$mysql->sqlordie($insert_image);	
			}
			echo "success";
		}
	}	

?>
