<?php
$host = 'RKFMLVEM03.e2k.ad.ge.com:143';
$user = 'lighthouse.comments@nbcuni.com';
$password = 'GE210User';
$mailbox = "{$host}INBOX";
$mbx = imap_open($mailbox , $user , $password);

$check = imap_check($mbx);
$overviews = imap_fetch_overview($mbox,"1:{$check->Nmsgs}");
?> <table> <tr> <td>From</td> <td>Date</td> <td>Subject</td> </tr>
 <?php foreach($overviews as $overview) { ?> <tr> <td><?php echo $overview->from; ?></td>
  <td><?php echo $overview->date; ?></td> <td><a href="open.php?id=<?php echo $overview->uid; ?>">
  <?php echo $overview->subject; ?></a></td> </tr> <?php } ?> </table> 
