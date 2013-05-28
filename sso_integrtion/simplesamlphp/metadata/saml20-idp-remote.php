<?php
/**
 * SAML 2.0 remote IdP metadata for simpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://rnd.feide.no/content/idp-remote-metadata-reference
 */

/*
 * Guest IdP. allows users to sign up and register. Great for testing!
 */



$metadata['nbcufssstg'] = array(
        'name' => array('en' => 'Non-Prod lighthouse'),
        'SingleSignOnService' => 'https://fss.external.stg.nbcuni.com/fss/idp/SSO.saml2',
        'SingleLogoutService' => 'https://ssologin.external.stg.nbcuni.com/ssologin/logoff.jsp?=http://dev3.lighthouse.nbcuots.com',
        // Artifact Resolution Services URL => 'https://fss.external.nbcuni.com/fss/idp/ARS.ssaml2',
        // Attribute Query Services URL => 'https://fss.external.nbcuni.com/fss/idp/attrsvc.ssaml2',
        'certificate' => 'nbcustage.crt',
);



