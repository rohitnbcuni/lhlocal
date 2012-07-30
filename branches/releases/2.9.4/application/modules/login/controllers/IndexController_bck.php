<?PHP
	
	class Login_IndexController extends LighthouseController  { 
		public function indexAction() {
			echo '<!--==| START: Content |==-->
				<div class="login_box_container">
					<div class="login_box">
						<div class="login_box_inner">
							<form action="/login/" method="post" name="login_form">';
							
							if(isset($_POST['submit_login'])) {
								echo '<div class="error_row">Wrong Email Address or Password!</div>';
							}
							
						echo '<input type="hidden" name="submit_login" value="submit_login" />
							<div class="form_row"><label class="login_label">User Name:</label><input class="login_field" type="text" id="lh_username" name="lh_username" /></div>
							<div class="form_row"><label class="login_label">Password:</label><input class="login_field" type="password" id="lh_password" name="lh_password" /></div>
							<div class="form_row_offset"><input type="checkbox" name="remember" id="remember"/><label for="remember">Remember me next time.</label></div>
							<!--<input type="submit" style="position: absolute; z-index: -999; border: 0; width: 1; height: 1px; ">-->
							<div class="form_row_buttons"><button class="secondary" onClick="loginSubmit(); return false;"><span class="login_button">OK</span></button></div>
							</form>
						</div>
					</div>
					<div class="login_help_container">
						<div class="forgot_pass"><a href="'.BASECAMP_HOST.'/amnesia/forgot_password">Forgot Password</a></div>
						<div style="color:#1F96F9;text-decoration:none;float:right;">'.LIGHTHOUSE_VERSION.'</div>  
						<!--<div class="login_help"><a href="">Help</a></div>-->
					</div>
				</div>
			<!--==| END: Content |==-->';
		}
		public function mobileloginAction(){
//			$this->indexAction();
			echo '<!--==| START: Content |==-->
				<div class="login_box_container" style="width:360px;">
					<div class="login_box">
						<div class="login_box_inner">
							<form action="/login/index/mobilelogin" method="post" name="login_form">';
							
							if(isset($_POST['submit_login'])) {
								echo '<div class="error_row">Wrong Email Address or Password!</div>';
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
	}
?>