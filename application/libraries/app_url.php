<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * App_url Class
 *
 * translate url patttern to real url
 *
 * @package        	SH
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	hybridknight
 */
class App_url
{
	private $_ci;                // CodeIgniter instance
	
	function __construct(){
		$this->_ci =& get_instance();		
    }
	
	function translate_url($app_url, $app_install_id){
		return str_replace('{app_install_id}', $app_install_id, $app_url);
	}
	
	function translate_install_url($app_install_url, $company_id, $user_facebook_id){
		$pattern = array('{company_id}', '{user_facebook_id}');
		$replace_with = array($company_id, $user_facebook_id);
		return str_replace($pattern, $replace_with, $app_install_url);
	}
	
	function translate_config_url($app_config_url, $app_install_id, $user_id, $app_install_secret_key){
		$pattern = array('{app_install_id}', '{user_id}', '{app_install_secret_key}');
		$replace_with = array($app_install_id, $user_id, $app_install_secret_key);
		return str_replace($pattern, $replace_with, $app_config_url);
	}
}
// END App_url Class

/* End of file App_url.php */
/* Location: ./application/libraries/App_url.php */
	