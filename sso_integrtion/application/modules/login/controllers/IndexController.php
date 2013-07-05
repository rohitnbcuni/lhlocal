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
			include_once("../simplesamlphp/lib/_autoload.php");
			$this->_session = new Zend_Session_Namespace('Zend_BC_Auth');
			$this->_session->setExpirationSeconds(365 * 24 * 60 * 60);
			

			$auth = new SimpleSAML_Auth_Simple('nbcu-sp');
			if (!$auth->isAuthenticated()) {
				$auth->requireAuth(array(
					'KeepPost' => FALSE,
				));
			}
			if ($auth->isAuthenticated()) {
				include("SSOLogin.inc");
				$attributes = array();
				$attributes = $auth->getAttributes();
				if(count($attributes) > 0){
					$row = array();
					$sso_obj = new SSOLogin();
					$row = $sso_obj->checkUser($attributes);
					//If User have SSO id but not Active user in LH application
					print_r($row); 
					die;
					if((!empty($row)) && (count($row) > 0)){
						
						$this->_session->lh_username = $row['user_name'];
						$_SESSION['lh_username'] = $row['user_name'];
						//$this->_session->lh_password = $_POST['lh_password'];
						//$_SESSION['lh_password'] = $_POST['lh_password'];
						$this->_session->user_id = $row['id'];
						$_SESSION['user_id'] = $row['id'];
						$this->_session->first = $row['first_name'];
						$_SESSION['first'] = $row['first_name'];
						$this->_session->last = $row['last_name'];
						$_SESSION['last'] = $row['last_name'];
						$this->_session->login_status = $row['login_status'];
						$_SESSION['login_status'] = $row['login_status'];
						$this->_session->role = $row['role'];
						$_SESSION['role'] = $row['role'];
						$this->_session->resource = $row['resource'];
						$_SESSION['resource'] = $row['resource'];
						$this->_session->company = $row['company'];
						$_SESSION['company'] = $row['company'];
						$this->_session->user_access_bits = $row['user_access'];
						$_SESSION['user_access_bits'] = $row['user_access'];

						$user_session['lh_username'] = $row['user_name'];
						//$user_session['lh_password'] = $_POST['lh_password'];
						$user_session['user_id'] = $row['id'];
						$user_session['first'] = $row['first_name'];
						$user_session['last'] = $row['last_name'];
						$user_session['login_status'] = $row['login_status'];
						$user_session['role'] = $row['role'];
						$user_session['resource'] = $row['resource'];
						$user_session['company'] = $row['company'];
						$user_session['user_access_bits'] = $row['user_access'];

						if($row['login_status'] != "admin"){
							if($row['company'] == "2" || $row['company'] == "136" || $row['company'] == "141") {
								$login_status = "employee";
							} else {
								$login_status = "client";
							}
							$_SESSION['login_status'] = $login_status;
							$user_session['login_status'] = $login_status;
						}
						$_SESSION['loggedin'] = true;
						$user_session['loggedin'] = true;
						$this->set_session($user_session, "lh_user");
						$this->_session->loggedin = true;
						$redirect_url = $this->get_session_value('lighthouse_ru');
						//If company is empty mean user are authenticated from SSO but company is not registered
						//By default we assign only to access workorder
						if(empty($row['company'])){
							$this->_redirect("workorders");
						
						}else if((!empty($redirect_url)) && (!empty($row['company']))){
							setcookie("lighthouse_ru", '', time() - 3600, '/');	
							//$this->_redirect("workorders/index/edit/?wo_id=38708");
							header("Location:".$redirect_url);
							
						}else{
							//print_r($row);
							$this->_redirect("resourceplanner/?userid=".$_SESSION['user_id']);
						}
					}else{
						echo "Invalid Username and PAssword";
					
					}
				 
				}
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
			$auth = new SimpleSAML_Auth_Simple('nbcu-sp');
			//$auth->logout();
			$b = BASE_URL."/login/?signout=true";
		    $url = $auth->getLogoutURL($b);
			//print('<a href="' . htmlspecialchars($url) . '">Logout</a>');
			$this->_helper->layout->disableLayout();
		
		}
		
	}
?>
