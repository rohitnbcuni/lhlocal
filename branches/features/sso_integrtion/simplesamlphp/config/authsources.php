<?php


$config = array(
/* This is the name of this authentication source, and will be used to access it later. */
    "nbcu-sp" => array(
        'saml:SP',
        'idp' => 'lighthouse',
        'entityID' => 'lighthouse',
        'RelayState' => 'http://dev3.lighthouse.nbcuots.com',
     //   'privatekey' => 'STAR_ lighthouse.com.key', /* if you have*/
      // 'certificate' => 'STAR_ lighthouse.com.crt', /* if you have*/
        'NameIDPolicy' => NULL
    ),
);


?>
