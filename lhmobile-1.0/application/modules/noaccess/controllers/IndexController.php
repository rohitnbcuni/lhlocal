<?PHP
	class Noaccess_IndexController extends LighthouseController { 
		public function indexAction() {
			echo '<!--==| START: Content |==-->
				<div class="login_box_container">
					<div class="login_box">
						<div class="login_box_inner">
								<center><h3>No Access to this Link</h3></center>
						</div>
					</div>
				</div>
			<!--==| END: Content |==-->';
		}
		/*
		 * Error Action
		 * LH#23703
		 */
		public function errorAction() {	
			$errors = $_SESSION['error_handler'];
			$this->view->assign("message",$errors);
		  
		} 
	}
?>