<?php

$config = array(
/* This is the name of this authentication source, and will be used to access it later. */
    "nbcu-sp" => array(
        'saml:SP',
	 'RelayState' => 'http://dev3.lighthouse.nbcuots.com',
        'idp' => 'nbcufssstg',
        'entityID' => 'lighthouse',
     //   'privatekey' => 'STAR_ lighthouse.com.key', /* if you have*/
      // 'certificate' => 'STAR_ lighthouse.com.crt', /* if you have*/
        'NameIDPolicy' => NULL,
	'ReturnTo' => 'http://dev3.lighthouse.nbcuots.com'
    ),
);


?>
