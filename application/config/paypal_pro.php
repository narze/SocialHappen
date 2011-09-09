<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['Sandbox'] = TRUE;
$config['APIVersion'] = '66.0';
$config['APIUsername'] = $config['Sandbox'] ? 'figy_1315552003_biz_api1.figabyte.com' : 'PRODUCTION_USERNAME_GOES_HERE';
$config['APIPassword'] = $config['Sandbox'] ? '1315552082' : 'PRODUCTION_PASSWORD_GOES_HERE';
$config['APISignature'] = $config['Sandbox'] ? 'A7F5dPvwf.EFCLaVItlooCYR6N0GAODd2mXV7lY5PfQrUE4.iwKZUOZc' : 'PRODUCTION_SIGNATURE_GOES_HERE';

/* End of file paypal_pro.php */
/* Location: ./system/application/config/paypal_pro.php */