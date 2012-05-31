<?PHP
	Zend_Loader::loadClass('Zend_Controller_Action');
	header("Location: /login");
	
	class IndexController extends Zend_Controller_Action { 
		public function indexAction() {
			echo '<!--=========== START: COLUMNS ===========-->
				<div class="dashboard_column">
					<!--==| START: Bucket |==-->
					<div class="title_med"><h4>Resource Planner</h4><div class="right_actions"><a href="/resourceplanner/">Edit</a></div></div>
					<div class="dashboard_bucket">
						<div class="dashboard_bucket_inner">
							<div class="dashboard_subtitle"><h5>Time Allocated</h5></div>
							<ul class="dashboard_content">
							<li><strong>Week:</strong>89%</li>
							<li><strong>Month:</strong>50%</li>
							</ul>
						</div>
					</div>
					<!--==| END: Bucket |==-->
					<!--==| START: Bucket |==-->
					<div class="title_med"><h4>Control Tower</h4><div class="right_actions"><a href="/controltower/">Edit</a></div></div>
					<div class="dashboard_bucket">
						<div class="dashboard_bucket_inner">
							<div class="dashboard_subtitle"><h5>Current Projects</h5></div>
							<ul class="dashboard_content">
								<li>NSW001x - Weather Plus Visual Design <img src="_images/sign_critical.gif" class="bang"/></li>
								<li>SCIFI006 - Battlestar Galactica Social Network</li>
								<li>INT001 - SCIFI international</li>
							</ul>
							<div class="dashboard_subtitle"><h5>Approvals</h5></div>
							<ul class="dashboard_content">
								<li>NSW001x - Weather Plus Visual Design
									<ul class="dashboard_content_level2">
										<li><strong>UI</strong><span class="alert">3/18/08</span> <img src="_images/sign_critical.gif" class="bang"/></li>
										<li><strong>Design</strong><span class="alert">4/05/08</span> <img src="_images/sign_critical.gif" class="bang"/></li>
										<li><strong>Dev</strong>5/05/08</li>
									</ul>
								</li>
								<li>SCIFI006 - Battlestar Galactica Social Network
									<ul class="dashboard_content_level2">
										<li><strong>UI</strong>3/18/08</li>
										<li><strong>Design</strong>4/05/08</li>
										<li><strong>Dev</strong>5/05/08</li>
									</ul>
								</li>
								<li>INT001 - SCIFI international
									<ul class="dashboard_content_level2">
										<li><strong>UI</strong>3/18/08</li>
										<li><strong>Design</strong>4/05/08</li>
										<li><strong>Dev</strong>5/05/08</li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
					<!--==| END: Bucket |==-->
				</div>
				<!--=========== COLUMN BREAK ===========-->
				<div class="dashboard_column">
					<!--==| START: Bucket |==-->
					<div class="title_med"><h4>Launch Calendar</h4><div class="right_actions"><a href="/launchcalendar/">Edit</a></div></div>
					<div class="dashboard_bucket">
						<div class="dashboard_bucket_inner">
							<div class="dashboard_subtitle"><h5>Status</h5></div>
							<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam imperdiet, nisl vitae dap ibus vehicula, felis dolor consectetuer nunc, in tempus dolor nisi vitae lacus. Praesent vitae orci. Aliquam non justo. Nam lobortis placerat tellus. Praesent nec mauris sagittis risus vehicula faucibus.</p>
							<div class="dashboard_subtitle"><h5>Communicating</h5></div>
							<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam imperdiet, nisl vitae dap ibus vehicula, felis dolor consectetuer nunc, in tempus dolor nisi vitae lacus. Praesent vitae orci. Aliquam non justo. Nam lobortis placerat tellus. Praesent nec mauris sagittis risus vehicula faucibus.</p>
							<div class="dashboard_subtitle"><h5>Reporting</h5></div>
							<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam imperdiet, nisl vitae dap ibus vehicula, felis dolor consectetuer nunc, in tempus dolor nisi vitae lacus. Praesent vitae orci. Aliquam non justo. Nam lobortis placerat tellus. Praesent nec mauris sagittis risus vehicula faucibus.</p>
							<div class="dashboard_subtitle"><h5>Risk</h5></div>
							<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam imperdiet, nisl vitae dap ibus vehicula, felis dolor consectetuer nunc, in tempus dolor nisi vitae lacus. Praesent vitae orci. Aliquam non justo. Nam lobortis placerat tellus. Praesent nec mauris sagittis risus vehicula faucibus.</p>
						</div>
					</div>
					<!--==| END: Bucket |==-->
				</div>
				<!--=========== END: COLUMNS ===========-->';
		}
	}
?>