<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Socialhappen_test extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('socialhappen');
    	$this->unit->reset_dbs();
	}

	function __destruct(){
		$this->unit->report_with_counter();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	 
	function get_k_test(){

	}

	function get_v_test(){

	}

	function map_one_v_test(){

	}

	function map_v_test(){

	}

	function get_default_url_test(){

	}

	function check_logged_in_test(){

	}

	function is_logged_in_test(){

	}

	function get_user_test(){

	}

	function get_user_id_test(){

	}

	function get_user_companies_test(){

	}

	function get_header_test(){

	}

	function get_header_lightbox_test(){

	}

	function get_footer_test(){

	}

	function get_footer_lightbox_test(){

	}

	function login_test(){

	}

	function logout_test(){

	}

	function upload_image_test(){

	}

	function replace_image_test(){

	}

	function resize_image_test(){

	}

	function ajax_check_test(){

	}

	function _has_company_roles_test(){

	}

	function check_admin_test(){

	}

	function in_each_array_test(){

	}

	function check_user_test(){

	}

	function check_package_by_user_id_and_mode_test(){

	}

	function check_package_over_the_limit_by_user_id_test(){

	}

	function get_bar_test(){

	}

	function get_setting_template_test(){

	}

	function get_tab_url_by_app_install_id_test(){

	}

	function is_developer_test(){

	}

	function is_developer_or_features_enabled_test(){

	}

	function is_developer_or_member_under_limit_test(){

	}

}
/* End of file socialhappen_test.php */
/* Location: ./application/controllers/test/socialhappen_test.php */