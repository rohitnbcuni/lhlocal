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
					if (function_exists('imagecreate')) {
						$thumb_image_name = $this->resizeImage($file_full_path,56,56,$file_full_path);
					}
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


	

		function resizeImage($filename, $newwidth, $newheight,$thumbfile_name){
			list($width, $height) = getimagesize($filename);
			if($width > $height && $newheight < $height){
				$newheight = $height / ($width / $newwidth);
			} else if ($width < $height && $newwidth < $width) {
				$newwidth = $width / ($height / $newheight);    
			} else {
				$newwidth = $width;
				$newheight = $height;
			}
			$thumb = imagecreatetruecolor($newwidth, $newheight);
			$extension = $this->getExtension($filename);
			if($extension=="jpg" || $extension=="jpeg" ){
				$source = imagecreatefromjpeg($filename);
				
				imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
				
			}
			if($extension=="png" ){
				$source = imagecreatefrompng($filename);
				
				imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
				
			}
			if($extension=="gif" ){
				$source = imagecreatefromgif($filename);
				
				imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
				
			}
			imagejpeg($thumb, $thumbfile_name, 100);
		}
		
		function getExtension($str) {

			 $i = strrpos($str,".");
			 if (!$i) { return ""; } 
			 $l = strlen($str) - $i;
			 $ext = substr($str,$i+1,$l);
			 return $ext;
			}
			
		
		
		
		
		
		
	}
?>		