<?PHP
	
	
	class Workorders_ProfileController extends LighthouseController { 
		public function indexAction() {
			include('Admin.inc');
			$usersInfo = AdminDisplay::fetchUserbyID($_SESSION['user_id']);
			$this->view->assign("userInfo",$usersInfo[0]);
			
		
		}
		
		function imageupdateAction(){
			//upto 2 MB
			define ("MAX_SIZE","2097152");
			if ($this->_request->isPost()){
				include('SSOLogin.inc');
				
				$user_id = $_SESSION['user_id'];
				//$user_id = $mysql->real_escape_string($user_id);
				$dirName = "users-image/";
				
				$cleaned_filename = str_replace(" ", "_", urldecode($_FILES['image_upload']['name']));
				$cleaned_filename = str_replace("'", "_", strtolower($cleaned_filename));
				$cleaned_filename = $user_id."-".time().$cleaned_filename;
				
				$ext = array('.jpg','.jpeg','.png','.gif');
			
				if(!in_array(strrchr($cleaned_filename,'.'),$ext)){
					die ("You cannot upload in that format"); 
				}	
				if ($_FILES['image_upload']['size']> MAX_SIZE){
				
					die("The file size of the attachment is more than uploading limit");
				
				}	
				

				if (!copy($_FILES['image_upload']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] .'/files/' .$dirName .$cleaned_filename)) {
					echo "fail";
				} else {
					$file_full_path = $_SERVER['DOCUMENT_ROOT'] .'/files/' .$dirName .$cleaned_filename;
					chmod($file_full_path, 0655);
					$sso_profile = new SSOLogin();
					//GD is not on server
					$thumb_image_name = $this->smart_resize_image($file_full_path,56,56,$file_full_path);
					
					$userData = '/files/'.$dirName .$cleaned_filename;
					$sso_profile->updateUserImage($userData,$user_id);
					echo "success";
				}
	
			}
		$this->_helper->layout->disableLayout();
			
		}
		
		
		function companyupdateAction(){
			$db = Zend_Registry::get('db');
			
			if ($this->_request->isPost()){
				$where[] = 'id =  '.$_SESSION['user_id'];
				$user_company = $this->_request->getParam('companyId');
				$phone = trim($this->_request->getParam('phone'));
				if($phone != ''){
					$dataArray = array('company' => $user_company, "phone_office" => $phone);
				}else{
					$dataArray = array('company' => $user_company);
				}
				
				$db->update("users", $dataArray, $where);
				
				$this->_helper->layout->disableLayout();
			}
		
		}


	

			
		
	function smart_resize_image($file, $width = 0,
										  $height = 0,
										  $proportional = false,
										  $output = 'file',
										  $delete_original = true,
										  $use_linux_commands = false ) {

		if ( $height <= 0 && $width <= 0 ) return false;

		# Setting defaults and meta
		$info = getimagesize($file);
		$image = '';
		$final_width = 0;
		$final_height = 0;
		list($width_old, $height_old) = $info;

		# Calculating proportionality
		if ($proportional) {
			if ($width == 0) $factor = $height/$height_old;
			elseif ($height == 0) $factor = $width/$width_old;
			else $factor = min( $width / $width_old, $height / $height_old );

			$final_width = round( $width_old * $factor );
			$final_height = round( $height_old * $factor );
		}
		else {
			$final_width = ( $width <= 0 ) ? $width_old : $width;
			$final_height = ( $height <= 0 ) ? $height_old : $height;
		}

		# Loading image to memory according to type
		switch ( $info[2] ) {
			case IMAGETYPE_GIF: $image = imagecreatefromgif($file); break;
			case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($file); break;
			case IMAGETYPE_PNG: $image = imagecreatefrompng($file); break;
			default: return false;
		}


		# This is the resizing/resampling/transparency-preserving magic
		$image_resized = imagecreatetruecolor( $final_width, $final_height );
		if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
			$transparency = imagecolortransparent($image);

			if ($transparency >= 0) {
				$transparent_color = imagecolorsforindex($image, $trnprt_indx);
				$transparency = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				imagefill($image_resized, 0, 0, $transparency);
				imagecolortransparent($image_resized, $transparency);
				}
			elseif ($info[2] == IMAGETYPE_PNG) {
				imagealphablending($image_resized, false);
				$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
				imagefill($image_resized, 0, 0, $color);
				imagesavealpha($image_resized, true);
			}
		}
		imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);

		# Taking care of original, if needed
		if ( $delete_original ) {
			if ( $use_linux_commands ) exec('rm '.$file);
			else @unlink($file);
		}

		# Preparing a method of providing result
		switch ( strtolower($output) ) {
			case 'browser':
			$mime = image_type_to_mime_type($info[2]);
			header("Content-type: $mime");
			$output = NULL;
			break;
			case 'file':
			$output = $file;
			break;
			case 'return':
			return $image_resized;
			break;
			default:
			break;
		}

		# Writing image according to type to the output destination
		switch ( $info[2] ) {
			case IMAGETYPE_GIF: imagegif($image_resized, $output); break;
			case IMAGETYPE_JPEG: imagejpeg($image_resized, $output); break;
			case IMAGETYPE_PNG: imagepng($image_resized, $output); break;
			default: return false;
		}

		return true;
	}
		
		
		
		
	}
?>		