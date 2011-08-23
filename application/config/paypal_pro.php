<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['Sandbox'] = TRUE;
$config['APIVersion'] = '66.0';
$config['APIUsername'] = $config['Sandbox'] ? 'SANDBOX_USERNAME_GOES_HERE' : 'PRODUCTION_USERNAME_GOES_HERE';
$config['APIPassword'] = $config['Sandbox'] ? 'SANDBOX_PASSWORD_GOES_HERE' : 'PRODUCTION_PASSWORD_GOES_HERE';
$config['APISignature'] = $config['Sandbox'] ? 'SANDBOX_SIGNATURE_GOES_HERE' : 'PRODUCTION_SIGNATURE_GOES_HERE';

/* End of file paypal_pro.php */
/* Location: ./system/application/config/paypal_pro.php */