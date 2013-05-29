<?PHP
	
	class Login_IndexController extends LighthouseController  { 
		public function indexAction() {
			$publickey = "6Lf5B80SAAAAAMjwfX5OBpvylvOA7IhZjcSKW1l9"; // you got this from the signup page
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
			}	
		}
		public function mobileloginAction(){
//			$this->indexAction();
			echo '<!--==| START: Content |==-->
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
			$this->render("index");
		}
		
		public funtion ssologinAction(){
			include("/var/www/lighthouse-uxd/dev3/current/simplesamlphp/lib/_autoload.php");

			$auth = new SimpleSAML_Auth_Simple('nbcu-sp');
			if (!$auth->isAuthenticated()) {
				$auth->requireAuth(array(
					'KeepPost' => FALSE,
				));
			}
			if ($auth->isAuthenticated()) {
				  $attributes = $auth->getAttributes();

			print_r($attributes);
			}


		
		
		}
		
	}
?>
