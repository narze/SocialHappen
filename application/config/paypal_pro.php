<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['Sandbox'] = TRUE;
$config['APIVersion'] = '66.0';
$config['APIUsername'] = $config['Sandbox'] ? 'figy_1314089794_biz_api1.gmail.com' : 'PRODUCTION_USERNAME_GOES_HERE';
$config['APIPassword'] = $config['Sandbox'] ? '1314089846' : 'PRODUCTION_PASSWORD_GOES_HERE';
$config['APISignature'] = $config['Sandbox'] ? 'A3WhQeCZ0By9hfbGJJPDneGDP2lBA66R9MhbzxlTps.U-Qjoh4ECl0gK' : 'PRODUCTION_SIGNATURE_GOES_HERE';

/* End of file paypal_pro.php */
/* Location: ./system/application/config/paypal_pro.php */