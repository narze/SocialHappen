<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite_component_lib_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('invite_component_lib', NULL, 'invite');
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

	function add_invite_test(){
		
	}

	function accept_invite_test(){
		
	}

	function list_invite_test(){
		
	}

	function get_invite_by_invite_key_test(){
		
	}

	function _extract_target_id_test(){
		
	}

	function _generate_invite_key_test(){
		
	}
	
	function generate_redirect_url_test(){
		$facebook_tab_url = 'http://www.facebook.com/pages/SHBeta/135287989899131?sk=app_253512681338518';
		$invite_key = 'rand0mh4shk3y';
		$expected_url = 'http://www.facebook.com/pages/SHBeta/135287989899131?sk=app_253512681338518&app_data=';
		$result = $this->invite->generate_redirect_url($facebook_tab_url, $invite_key);
		$in_pattern = strpos($result, $expected_url) === 0;
		$this->unit->run($in_pattern, TRUE, 'generate_redirect_url', $result);

		$app_data = substr($result, strlen('app_data=') + strpos($result,'app_data='));
		//$app_data = ripped after '?app_data=' from $result
		$app_data = json_decode(urldecode($app_data), TRUE);
		$app_data = $app_data['sh_invite_key'];
		$this->unit->run($invite_key === $app_data, TRUE, 'generate_redirect_url', $app_data);
	}

	function parse_invite_key_from_app_data_test(){
		$facebook_tab_url = 'http://www.facebook.com/pages/SHBeta/135287989899131?sk=app_253512681338518';
		$invite_key = 'rand0mh4shk3y';

		$redirect_url = $this->invite->generate_redirect_url($facebook_tab_url, $invite_key);
		$app_data_string = substr($redirect_url, strlen('app_data=') + strpos($redirect_url,'app_data='));

		$result = $this->invite->parse_invite_key_from_app_data($app_data_string);
		$this->unit->run($result, $invite_key, 'parse_invite_key_from_app_data', $app_data_string.' --> '.$result);
	}
}
/* End of file invite_component_lib_test.php */
/* Location: ./application/controllers/test/invite_component_lib_test.php */