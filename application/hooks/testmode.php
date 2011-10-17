<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TestMode {
	function test_mode(){
		$ci =& get_instance();
		if($ci->config->item('sh_test_mode')){
		  $ci->load->helper('form');
			if(!in_array($_SERVER['REMOTE_ADDR'], array('216.113.188.202','216.113.188.203','216.113.188.204','66.211.170.66','173.0.82.126'))){ //Skip if from paypal
				if(isset($_SERVER['HTTP_USER_AGENT']) //letting calls from file_get_contents() bypass
				&& (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')){ //letting calls from ajax bypass
					if($ci->router->fetch_class() != 'tab'){
						$ci->load->library('session');
						if(!$ci->session->userdata('test_mode')){
							$password = $ci->input->post('test_mode_password');
							if($password != $ci->config->item('sh_test_mode_password')){
								$ci->load->view('test_mode');
							} else {
								$userdata = array(
									'test_mode' => TRUE
								);
								$ci->session->set_userdata($userdata);
							}
						} else if($ci->input->get('test_mode_logout')){
							$this->logout();
						}
					}
				}
			}
		}
	}
	
	function logout(){
		$ci =& get_instance();
		$ci->session->unset_userdata('test_mode');
		$ci->load->helper('form');
		$ci->load->view('test_mode');
	}
}