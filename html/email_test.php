<?PHP


$to = 'shobhitsingh.bhadauria@nbcuni.com';
$subject = 'Lighthouse test mail please ignore';
$msg = 'Lighthouse test mail please ignore';
$headers = "From: "."LH"."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";

echo "email send start = ".date("Y-m-d : H:i:s")."<br>";
if(mail($to, $subject, $msg, $headers)){
	echo "email send end = ".date("Y-m-d : H:i:s");
} else {
	echo "sending failed";
}




?>