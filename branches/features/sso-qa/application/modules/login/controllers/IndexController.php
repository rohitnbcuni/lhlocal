<?PHP
	
	class Login_IndexController extends LighthouseController  { 
		public $_session;
		public function indexAction() {
			$this->_redirect("login/index/ssologin");
			/*$publickey = "6Lf5B80SAAAAAMjwfX5OBpvylvOA7IhZjcSKW1l9"; // you got this from the signup page
         		$this->view->recaptcha = recaptcha_get_html($publickey);
			$this->view->loginform = "login_form";
			if($_SESSION['captcha_error']){
				$this->view->error = $_SESSION['captcha_error'];
			}
			if(!ISSET($_COOKIE['atm'])){
					setcookie("atm",base64_encode(1),time()+1800,'/','',false,true);
				}else{
					$atm_val = base64_decode($_COOKIE['atm']);
					$atm_val++;
					setcookie("atm",base64_encode($atm_val),time()+1800,'/','',false,true);
					}
			if($atm_val > 3){
				$this->view->loginform = "login_form_captcha";
			}	*/
		}
		public function mobileloginAction(){
			$this->_redirect("login/index/ssologin");
//			$this->indexAction();
			/*echo '<!--==| START: Content |==-->
				<div class="login_box_container" style="width:360px;">
					<div class="login_box">
						<div class="login_box_inner">
							<form action="/login/index/mobilelogin" method="post" name="login_form">';
							
							if(isset($_POST['submit_login'])) {
								echo '<div class="error_row">Incorrect Username or Password!</div>';
							}
							if(is_array($_SESSION["Zend_BC_Auth"]) && array_key_exists("lh_ru", $_SESSION["Zend_BC_Auth"])){
								$url = $_SESSION["Zend_BC_Auth"]["lh_ru"];
								$url = base64_encode(str_replace("/workorders/index/edit", "/workorders/index/mobileedit", $url));
								echo '<input type="hidden" name="url_redirect" value="' . $url . '" />';
							}
							
						echo '<input type="hidden" name="submit_login" value="submit_login" />
								<table width="100%">
									<tr>
										<td width="30%">Login:</td>
										<td width="60%"><input class="login_field" type="text" id="lh_username" name="lh_username" /></td>
									</tr>
									<tr>
										<td width="30%">Password:</td>
										<td width="60%"><input class="login_field" type="password" name="lh_password" /></td>
									</tr>
									<tr>
										<td colspan="2"><input type="checkbox" name="remember" id="remember"/>Remember me next time.</td>
									</tr>
									<tr>
										<td colspan="2"><button onClick="login_form.submit();">OK</button></td>
									</tr>
								</table>
							</form>
						</div>
					</div>
					<div class="login_help_container">
						<div class="forgot_pass"><a href="'.BASECAMP_HOST.'/amnesia/forgot_password">Forgot Password</a></div>
					</div>

				</div>
			<!--==| END: Content |==-->';
			$this->render("index");*/
		}
		
		public function ssologinAction(){
		
			$redirect_url = $this->get_session_value('lighthouse_ru');
			//If company is empty mean user are authenticated from SSO but company is not registered
			//By default we assign only to access workorder
			if(empty($_SESSION['company'])){
				$this->_redirect("workorders/profile");

			}else if((!empty($redirect_url)) && (!empty($row['company']))){
				setcookie("lighthouse_ru", '', time() - 3600, '/');	
				//$this->_redirect("workorders/index/edit/?wo_id=38708");
				header("Location:".$redirect_url);

			}else if(!empty($_SESSION['company'])){
				$this->_redirect("workorders");

			}else{
				$pos = strpos($_SESSION['user_access_bits'],1);
				if( $pos === true){
					if($pos == 0){
						$this->_redirect("resourceplanner/?userid=".$_SESSION['user_id']);
					}else{
						$this->_redirect("workorders");
					}
				
				}
			$this->_redirect("workorders");
			//print_r($row);

			}
		

		
		}
		public function get_session_value($key){
		if(isset($_COOKIE[$key])){
			$cookie_value = $_COOKIE[$key];
			$cookie_value = base64_decode($cookie_value);
			$cookie_value = unserialize($cookie_value);			
		}
		return $cookie_value;
	}
		
		function ssologoutAction(){
						
			include("../simplesamlphp/lib/_autoload.php");
			$auth = new SimpleSAML_Auth_Simple(SAML_SP_ENTITY_ID);
			//$auth->logout();
			$b = BASE_URL."/login/?signout=true";
		    $url = $auth->getLogoutURL($b);
			//print('<a href="' . htmlspecialchars($url) . '">Logout</a>');
			$this->_helper->layout->disableLayout();
		
		}
		
	}
?>
