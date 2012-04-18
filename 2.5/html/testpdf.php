<?PHP
	include('_inc/config.inc');
	set_include_path(ZENDLIB .':' .APPLIB .':' .APPPATH .':' .WEBPATH .':' .FCKPATH);
	require_once('fpdf.inc');
	
	$html='Welcome to the html PDF<br><br>
This is writing html to the pdf.<br>
It is also using the <i>Italics</i>, <b>Bold</b>, <u>Underline</u>, or <i><b><u>All Formats</u></b></i>.<br>
Here is a link to <a href="www.jandaco.com">Jandaco</a>.';
	
	$pdf=new PDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$pdf->SetTitle('Test PDF Title');
	$pdf->SetSubject('Test PDF Subject');
	$pdf->SetAuthor('Justin Ishoy');
	$pdf->SetKeywords('test, jandaco, lighhouse, pdf maker, ohya');
	$pdf->SetTextColor(255,255,255);
	$pdf->MultiCell(0, 10, 'This is a test for the multi cell pdf formatting I hope it works nicely.', 1, 'c', true);
	$pdf->Ln(5);
	$pdf->SetTextColor(0,0,0);
	//left, top, right
	//$pdf->SetMargins(10,0,0);
	$pdf->WriteHTML($html);
	
	$pdf->Output();
?>