<?PHP
@session_start();
	define('AJAX_CALL', '0');
	include('../_inc/config.inc');
	//set_include_path(ZENDLIB .':' .APPLIB .':' .APPPATH .':' .WEBPATH .':' .FCKPATH);
	if(!ISSET($_SESSION['user_id'])){
		die("<b>You are not allowed to access these files</b>");
	}
	require_once('fpdf.inc');
	
	//$mysql = new mysqli('localhost', 'generic', 'generic', 'nbc_lighthouse');
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$sections = array();
	$secKeys = array();
	//print_r($_POST);
	$project_id = (int)$mysql->real_escape_string(@$_POST['project_id']);
	$sections = @$_POST['section'];
	$secKeys = array_keys($sections);
	
	$project_query = "SELECT * FROM `projects` WHERE `id`= ? LIMIT 1";
	$project_res = $mysql->sqlprepare($project_query,array($project_id));
	$project_row = $project_res->fetch_assoc();
	
	$pdf=new PDF();	
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$pdf->SetTitle('Project Breakdown');
	$pdf->SetSubject('Project Breakdown');
	$pdf->SetAuthor('NBC');
	$pdf->SetFillColor(84,84,84);
	
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('Arial','B',16);
	$title = $project_row['project_code'] .":" .$project_row['project_name'] ."<br><br>";
	$pdf->WriteHTML($title);
	
	for($i = 0; $i < sizeof($secKeys); $i++) {
		$key = $secKeys[$i];
		
		switch($key) {
			case 1: {
				$section = base64_decode($sections[$key]);
				
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFont('Arial','B',16);
				$pdf->MultiCell(0, 10, 'Project Description', 0, 'c', true);
				$pdf->Ln(5);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B',10);
				$pdf->WriteHTML(($section ."<br><br>"));
				break;
			}
			case 2: {
				$section = $sections[$key];
				$section_keys = array_keys($section);
				
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFont('Arial','B',16);
				$pdf->MultiCell(0, 10, 'User Roles', 0, 'c', true);
				$pdf->Ln(5);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B',10);
				$html = '';
				for($x = 0; $x < sizeof($section_keys); $x++) {
					$select_user = "SELECT * FROM `users` WHERE `id`='" .$mysql->real_escape_string($section[$section_keys[$x]]['user']) ."' LIMIT 1";
					$select_rt = "SELECT * FROM `resource_types` WHERE `id`='" .$mysql->real_escape_string($section[$section_keys[$x]]['resource_type']) ."' LIMIT 1";
					$user_res = @$mysql->sqlordie($select_user);
					$rt_res = @$mysql->sqlordie($select_rt);
					$user_row = @$user_res->fetch_assoc();
					$rt_row = @$rt_res->fetch_assoc();
					
					if(!empty($user_row['first_name'])) {
						$html .= "User: " .@$user_row['first_name'] ." " .@$user_row['last_name'] ."<br />";
						$html .= "Resource Type: " .@$rt_row['name'] ."<br />";
						$html .= "Email: " .@$section[$section_keys[$x]]['email'] ."<br />";
						$html .= "Phone: " .@$section[$section_keys[$x]]['phone'] ."<br /><br />";
					}
				}
				$html .= "<br><br>";
				$pdf->WriteHTML($html);
				
				break;
			}
			case 3: {
				$section = $sections[$key];
				$section_keys = array_keys($section);
				
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFont('Arial','B',16);
				$pdf->MultiCell(0, 10, 'Project Timeline', 0, 'c', true);
				$pdf->Ln(5);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B',10);
				$html = '';
				
				for($x = 0; $x < sizeof($section_keys); $x++) {
					$select_phase = "SELECT * FROM `lnk_project_phase_types` WHERE `id`='" .$mysql->real_escape_string($section[$section_keys[$x]]['phase']) ."' LIMIT 1";
					$phase_res = @$mysql->sqlordie($select_phase);
					$phase_row = $phase_res->fetch_assoc();
					
					if(!empty($section[$section_keys[$x]]['start_date']) || !empty($section[$section_keys[$x]]['projected_date'])) {
						$html .= "Phase: " .$phase_row['name'] ."<br />";
						$html .= "Start Date: " .$section[$section_keys[$x]]['start_date'] ."<br />";
						$html .= "Projected End Date: " .$section[$section_keys[$x]]['projected_date'] ."<br /><br />";
					}
				}
				$html .= "<br><br>";
				$pdf->WriteHTML($html);
				
				break;
			}
			case 4: {
				$section = base64_decode($sections[$key]);
				
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFont('Arial','B',16);
				$pdf->MultiCell(0, 10, 'Project Scope', 1, 'c', true);
				$pdf->Ln(5);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B',10);
				$pdf->WriteHTML(($section ."<br><br>"));
				break;
			}
			case 5: {
				break;
			}
			case 6: {
				$section = $sections[$key];
				$section_keys = @array_keys($section);
				
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFont('Arial','B',16);
				$pdf->MultiCell(0, 10, 'Finance & Budget', 0, 'c', true);
				$pdf->Ln(5);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B',10);
				$html = '';
				$overall_total = 0;
				
				for($x = 0; $x < sizeof($section_keys); $x++) {
					$select_phase = "SELECT * FROM `lnk_project_phase_types` WHERE `id`='" .$mysql->real_escape_string($section[$section_keys[$x]]['phase']) ."' LIMIT 1";
					$phase_res = @$mysql->sqlordie($select_phase);
					$phase_row = $phase_res->fetch_assoc();
					
					if(!empty($section[$section_keys[$x]]['hours']) || !empty($section[$section_keys[$x]]['rate'])) {
						$html .= "Phase: " .$phase_row['name'] ."<br />";
						
						$html .= "Hours: " .$section[$section_keys[$x]]['hours'] ."<br />";
						$html .= "Rate: " .$section[$section_keys[$x]]['rate'] ."<br />";
						$html .= "Total: $" .number_format(($section[$section_keys[$x]]['hours'] * $section[$section_keys[$x]]['rate']), 2, ".", ",") ."<br /><br />";
					}
					$overall_total += $section[$section_keys[$x]]['hours'] * $section[$section_keys[$x]]['rate'];
				}
				$html .= "Overall Total: $" .number_format($overall_total, 2, ".", ",") ."<br /><br />";
				
				$html .= "<br><br>";
				$pdf->WriteHTML($html);
				
				break;
			}
			case 7: {
				$section = base64_decode($sections[$key]);
				
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFont('Arial','B',16);
				$pdf->MultiCell(0, 10, 'Project Deliverables', 1, 'c', true);
				$pdf->Ln(5);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B',10);
				$pdf->WriteHTML(($section ."<br><br>"));
				break;
			}
			case 8: {
				$section = base64_decode($sections[$key]);
				
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFont('Arial','B',16);
				$pdf->MultiCell(0, 10, 'Project Metrics', 1, 'c', true);
				$pdf->Ln(5);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B',10);
				$pdf->WriteHTML(($section ."<br><br>"));
				break;
			}
			case 9: {
				$section = $sections[$key];
				$section_keys = @array_keys($section);
				
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFont('Arial','B',16);
				$pdf->MultiCell(0, 10, 'Project Approvals', 0, 'c', true);
				$pdf->Ln(5);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B',10);
				$html = '';
				
				for($x = 0; $x < sizeof($section_keys); $x++) {
					if(!empty($section[$section_keys[$x]]['user_name'])) {
						if(is_numeric($section[$section_keys[$x]]['phase'])) {
							$select_phase = "SELECT * FROM `lnk_project_phase_types` WHERE `id`='" .$mysql->real_escape_string($section[$section_keys[$x]]['phase']) ."' LIMIT 1";
							$phase_res = @$mysql->sqlordie($select_phase);
							$phase_row = $phase_res->fetch_assoc();
							
							$html .= "Phase: " .$phase_row['name'] ."<br />";
						} else {
							$html .= "Phase: " .$section[$section_keys[$x]]['phase'] ."<br />";
						}
						
						$select_appr_user = "SELECT * FROM `users` WHERE `id`='" .$mysql->real_escape_string($section[$section_keys[$x]]['user_name']) ."' LIMIT 1";
						$appr_user_res = @$mysql->sqlordie($select_appr_user);
						$user_row = $appr_user_res->fetch_assoc();
						//$section[$section_keys[$x]]['user_name']
						
						$html .= "User Name: " .ucfirst($user_row['first_name']) ." " .ucfirst($user_row['last_name']) ."<br />";
						$html .= "Title: " .$section[$section_keys[$x]]['user_title'] ."<br />";
						$html .= "Phone: " .$section[$section_keys[$x]]['user_phone'] ."<br />";
						$html .= "Approved: " .$section[$section_keys[$x]]['approved'] ."<br />";
						$html .= "Approval Date: " .$section[$section_keys[$x]]['approval_date'] ."<br /><br />";
					}
				}
				$html .= "<br><br>";
				$pdf->WriteHTML($html);
				
				break;
			}
			case 10: {
				$section = base64_decode($sections[$key]);
				
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFont('Arial','B',16);
				$pdf->MultiCell(0, 10, 'Business Case', 1, 'c', true);
				$pdf->Ln(5);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B',10);
				$pdf->WriteHTML(($section ."<br><br>"));
				break;
			}
		}
	}
	
	/*$html='Welcome to the html PDF<br><br>
	This is writing html to the pdf.<br>
	It is also using the <i>Italics</i>, <b>Bold</b>, <u>Underline</u>, or <i><b><u>All Formats</u></b></i>.<br>
	Here is a link to <a href="www.jandaco.com">Jandaco</a>.';*/
	
	//$pdf->SetKeywords('test, jandaco, lighhouse, pdf maker, ohya');
	//$pdf->SetTextColor(255,255,255);
	//$pdf->MultiCell(0, 10, 'This is a test for the multi cell pdf formatting I hope it works nicely.', 1, 'c', true);
	//$pdf->Ln(5);
	//$pdf->SetTextColor(0,0,0);
	//left, top, right
	//$pdf->SetMargins(10,0,0);
	//$pdf->WriteHTML($html);
	$pdf->Output();
?>