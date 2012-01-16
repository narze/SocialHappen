<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('From','fromUrl');
define('Facebook_user_id', 1);

class Home_ctrl_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('controller/home_ctrl');
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

	function signup_test(){
		$is_registered = TRUE;
		$from = From;
		$facebook_user_id = Facebook_user_id;
		$facebook_user = array(
			'id' => Facebook_user_id,
			'first_name' => 'firstName',
			'last_name' => 'lastName'
		);
		$input = compact('is_registered','from','facebook_user_id','facebook_user');
		$result = $this->home_ctrl->signup($input);

		$this->unit->run($result['success'], TRUE, 'signup test', $result['success']);
		$data = $result['data'];
		$this->unit->run(isset($data['header']), TRUE, 'header');
		$this->unit->run(isset($data['breadcrumb']), TRUE, 'breadcrumb');
		$this->unit->run(isset($data['tutorial']), TRUE, 'tutorial');
		$this->unit->run(isset($data['footer']), TRUE, 'footer');
		$this->unit->run($data['is_registered'], $is_registered, 'is_registered');
		$this->unit->run($data['from'], $from, 'from');
		$this->unit->run($data['user_profile_picture'] != NULL, TRUE, 'user_profile_picture');
		$this->unit->run($data['facebook_user'], $facebook_user, 'facebook_user');
	}

	function signup_form_test(){
		$user_timezone = 'Asia/Bangkok';
		$first_name = 'firstName';
		$last_name = 'lastName';
		$email = 'eMail@gmail.com';
		$facebook_user_id = '123456789';
		$company_name = 'compName';
		$company_detail = 'compDetail';
		$company_image = 'http://compImg.com/img.png';
		$package_id = 1;
		$facebook_access_token = 'samplefacebookaccesstoken';
		$input = compact('user_timezone','first_name','last_name','email','facebook_user_id','company_name','company_detail','company_image','package_id','facebook_access_token');
		$result = $this->home_ctrl->signup_form($input);
		$this->unit->run($result['success'], TRUE, 'signup form', $result['success']);
		$data = $result['data'];
		$user_id = $data['user_id'];
		$company_id = $data['company_id'];
		$this->unit->run(isset($data['redirect_url']), TRUE, 'redirect_url', $data['redirect_url']);
		$this->unit->run($user_id, 'is_int', 'user_id', $user_id);
		$this->unit->run($company_id, 'is_int', 'company_id', $company_id);
		
		$this->load->model('user_model');
		$this->load->model('company_model');
		$user = $this->user_model->get_user_profile_by_user_id($user_id);
		$company = $this->company_model->get_company_profile_by_company_id($company_id);
		$this->unit->run($user['user_first_name'], $first_name, 'first_name', $user['user_first_name']);
		$this->unit->run($user['user_last_name'], $last_name, 'last_name', $user['user_last_name']);
		$this->unit->run($user['user_email'], $email, 'email', $user['user_email']);
		$this->unit->run($user['user_facebook_access_token'], $facebook_access_token, 'facebook_access_token', $user['user_facebook_access_token']);
		$this->unit->run($user['user_facebook_id'], $facebook_user_id, 'facebook_user_id', $user['user_facebook_id']);

		$this->unit->run($company['company_name'], $company_name, 'company_name', $company['company_name']);
		$this->unit->run($company['company_detail'], $company_detail, 'company_detail', $company['company_detail']);
		$this->unit->run($company['company_image'], $company_image, 'company_image', $company['company_image']);

	}
}
/* End of file home_ctrl_test.php */
/* Location: ./application/controllers/test/home_ctrl_test.php */
