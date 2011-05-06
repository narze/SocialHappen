<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		echo json_encode(array('status' => 'OK'));
	}
	
	function request_install_app($app_id = NULL, $app_secret_key = NULL
								, $company_id = NULL, $user_facebook_id = NULL){

		if(!isset($app_id) || !isset($app_secret_key) || !isset($company_id) || !isset($user_facebook_id) ){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, company_id, user_facebook_id)'));
			return;
		}
		
		// check pending install app
		$this->load->model('Install_app_model', 'Install_app');
		if(!$this->Install_app->check_install_app($app_id, $company_id, $user_facebook_id)){
			echo json_encode(array( 'error' => '50',
									'message' => 'permission error please install app from platform\'s install page'));
			return;
		}else{
			$this->Install_app->delete($app_id, $company_id, $user_facebook_id);
		}
		
		// authenticate app with $app_id and $app_secret_key
		if(!($this->_authenticate_app($app_id, $app_secret_key))){
			echo json_encode(array( 'error' => '200',
									'message' => 'invalid app_secret_key'));
			return;
		}
		
		// authenticate user with $company_id and $user_facebook_id
		if(!($this->_authenticate_user($company_id, $user_facebook_id))){
			echo json_encode(array( 'error' => '300',
									'message' => 'you have no permission to install app on this company'));
			return;
		}
		
		// generate app_install_secret_key for app
		$app_install_secret_key = $this->_generate_app_install_secret_key($company_id, $app_id);
				
		// add new company_apps record based on $conpany_id and $app_id ,
		// return $app_install_id
		$app_install_id = 0;
		$this->load->model('Company_apps_model', 'Company_apps');
		
		$app_install_id = $this->Company_apps->add(array(
					'company_id' => $company_id,
					'app_id' => $app_id,
					'app_install_available' => TRUE,
					'app_install_secret_key' => $app_install_secret_key
					//'app_install_fanpage_id' => ''
					));
		
		// response
		$response = array(	'status' => 'OK',
							'app_install_id' => $app_install_id,
							'app_install_secret_key' => $app_install_secret_key);
		
		echo json_encode($response);
	}

	function request_install_page($app_id = NULL, $app_secret_key = NULL, $app_install_id = NULL,
	 					$app_install_secret_key = NULL, $facebook_page_id = NULL){
		// check parameter
		if(!isset($app_id) || !isset($app_install_id) || !isset($app_secret_key) || !isset($app_install_secret_key) || !isset($facebook_page_id) ){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, facebook_page_id)'));
			return;
		}
		
		// authenticate app with $app_id and $app_secret_key
		if(!($this->_authenticate_app($app_id, $app_secret_key))){
			echo json_encode(array( 'error' => '200',
									'message' => 'invalid app_secret_key'));
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!($this->_authenticate_app_install($app_install_id, $app_install_secret_key))){
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid app_install_secret_key'));
			return;
		}
		
		/* not in use na ja
		// do something
		$this->load->Model('Company_apps_model', 'Company_apps');
		$this->Company_apps->update(array('facebook_page_id' => $facebook_page_id),
									 array('app_install_id' => $app_install_id));
		*/
		$this->load->model('Company_apps_model', 'Company_apps');
		$company_id = $this->Company_apps->get_app_install_by_app_install_id($app_install_id);
		$company_id = $company_id->company_id;
		
		$this->load->model('Page_apps_model', 'Pages_apps');
		$this->load->model('Company_pages_model', 'Company_pages');
		
		// create new company_pages or not?
		if($this->Company_pages->count_all(array('company_id' => $company_id,
											'facebook_page_id' => $facebook_page_id)) == 0){
			$this->Company_pages->add(array('company_id' => $company_id, 
											'facebook_page_id' => $facebook_page_id));
		}
		
		// check if there is exist page
		if($this->Pages_apps->count_all(array('facebook_page_id' => $facebook_page_id,
												'app_install_id' => $app_install_id)) == 0){
			// add app to page = new page_apps
			$this->Pages_apps->add(array('facebook_page_id' => $facebook_page_id, 
											'app_install_id' => $app_install_id));
		}
		
		$response = array(	'status' => 'OK',
							'message' => 'page saved');
		
		echo json_encode($response);
	}

	function _authenticate_app($app_id, $app_secret_key){
		// authenticate app with $app_id and $app_secret_key
		$this->load->model('App_model', 'App');
		$app = $this->App->get_app($app_id);
		if($app != NULL){
			$app = $app[0];
			return ($app->app_secret_key == $app_secret_key);
		}
		return FALSE;
	}
	
	function _authenticate_app_install($app_install_id, $app_install_secret_key){
		// authenticate app with $app_id and $app_secret_key
		$this->load->model('Company_apps_model', 'Company_apps');
		$app = $this->Company_apps->get_app_install_by_app_install_id($app_install_id);
		if($app != NULL){
			return ($app->app_install_secret_key == $app_install_secret_key);
		}
		return FALSE;
	}
	
	function _authenticate_user($company_id, $user_facebook_id){
		// authenticate user with $company_id and $user_facebook_id
		$this->load->model('User_companies_model', 'User_companies');
		$company_admin_list_query = $this->User_companies->get_user_admin_companies_list_by_company($company_id, 1000, 0);
		$company_admin_list = array();
		foreach ($company_admin_list_query as $admin) {
			$company_admin_list[] = $admin->user_facebook_id;
		}
		
		return in_array($user_facebook_id, $company_admin_list);
	}

	function _generate_app_install_secret_key($company_id, $app_id){
		return md5($this->_generate_random_string());
	}
	
	/**
	 * generate random string !
	 */
	function _generate_random_string() {
	    $length = 10;
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	    $string = '';    
	
	    for ($p = 0; $p < $length; $p++) {
	        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
	    }

    	return $string;
	}
	
	function request_log_app($app_id = NULL, $app_secret_key = NULL
							, $app_install_id = NULL, $app_install_secret_key = NULL){
		if(!isset($app_id) || !isset($app_secret_key) || !isset($app_install_id) || !isset($app_install_secret_key)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));
			return;
		}
		
		// authenticate app with $app_id and $app_secret_key
		if(!($this->_authenticate_app($app_id, $app_secret_key))){
			echo json_encode(array( 'error' => '200',
									'message' => 'invalid app_secret_key'));
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!($this->_authenticate_app_install($app_install_id, $app_install_secret_key))){
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid app_install_secret_key'));
			return;
		}
		
		$response = array(	'status' => 'OK',
							'message' => 'logged');
		
		echo json_encode($response);
	}
							
	function request_log_user($app_id = NULL, $app_secret_key = NULL
							, $app_install_id = NULL, $app_install_secret_key = NULL, $user_facebook_id = NULL){
		if(!isset($app_id) || !isset($app_secret_key) || !isset($app_install_id) || !isset($app_install_secret_key) || !isset($user_facebook_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));
			return;
		}
		
		// authenticate app with $app_id and $app_secret_key
		if(!($this->_authenticate_app($app_id, $app_secret_key))){
			echo json_encode(array( 'error' => '200',
									'message' => 'invalid app_secret_key'));
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!($this->_authenticate_app_install($app_install_id, $app_install_secret_key))){
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid app_install_secret_key'));
			return;
		}
		
		// log user
		$this->load->model('User_apps_model', 'User_apps');
		$this->load->model('User_model', 'User');
		
		// if not exist user, create it
		if(!$this->User_apps->check_exist($user_facebook_id, $app_install_id)){
			/*
			$response = array(	'error' => '600',
							'status' => 'ERROR',
							'message' => 'non exist user, register new user first');
			echo json_encode($response);
			return;
			*/
			$this->User->add_by_facebook_id($user_facebook_id);
			if(!$this->User_apps->check_exist($user_facebook_id, $app_install_id)){
				$this->User_apps->add_new($user_facebook_id, $app_install_id);
			}
			
			// update user last seen
			$this->User_apps->update_user_last_seen($user_facebook_id, $app_install_id);
			$this->User->update_user_last_seen($user_facebook_id);
			
			$response = array(	'status' => 'OK',
							'message' => 'logged');
		}else{
			// update user last seen
			$this->User_apps->update_user_last_seen($user_facebook_id, $app_install_id);
			$this->User->update_user_last_seen($user_facebook_id);
			$response = array(	'status' => 'OK',
							'message' => 'logged');
		}
		
		echo json_encode($response);
	}
							
	function request_register_user($app_id = NULL, $app_secret_key = NULL
							, $app_install_id = NULL, $app_install_secret_key = NULL, $user_facebook_id = NULL){
		if(!isset($app_id) || !isset($app_secret_key) || !isset($app_install_id) || !isset($app_install_secret_key) || !isset($user_facebook_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));
			return;
		}
		
		// authenticate app with $app_id and $app_secret_key
		if(!($this->_authenticate_app($app_id, $app_secret_key))){
			echo json_encode(array( 'error' => '200',
									'message' => 'invalid app_secret_key'));
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!($this->_authenticate_app_install($app_install_id, $app_install_secret_key))){
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid app_install_secret_key'));
			return;
		}
		
		$response = array(	'status' => 'OK',
							'message' => 'registered');
		
		// register new user
		$this->load->model('User_model', 'User');
		if(!$this->User->check_exist($user_facebook_id)){
			$this->User->add_by_facebook_id($user_facebook_id);
			$response['User'] = 'added ';
		}
		
		// add new user apps
		$this->load->model('User_apps_model', 'User_apps');
		if(!$this->User_apps->check_exist($user_facebook_id, $app_install_id)){
			$this->User_apps->add_new($user_facebook_id, $app_install_id);
			$response['User_apps'] = 'added';
		}
		
		// update user last seen
		$this->User_apps->update_user_last_seen($user_facebook_id, $app_install_id);
		$this->User->update_user_last_seen($user_facebook_id);	
		
		echo json_encode($response);
	}
	
	function request_authenticate($app_id = NULL, $app_secret_key = NULL, $app_install_id = NULL, $user_facebook_id = NULL){		
		if(!isset($app_id) || !isset($app_secret_key) || !isset($app_install_id) || !isset($user_facebook_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, user_facebook_id)'));
			return;
		}
		
		// authenticate app with $app_id and $app_secret_key
		if(!($this->_authenticate_app($app_id, $app_secret_key))){
			echo json_encode(array( 'error' => '200',
									'message' => 'invalid app_secret_key'));
			return;
		}
		
		// get $company_id
		$this->load->model('Company_apps_model', 'Company_apps');
		$app = $this->Company_apps->get_app_install_by_app_install_id($app_install_id);
		if($app != NULL){
			$company_id = $app->company_id;
		}else{
			echo json_encode(array( 'error' => '250',
									'message' => 'invalid app'));
			return;
		}
		
		// authenticate user with $company_id and $user_facebook_id
		if(!($this->_authenticate_user($company_id, $user_facebook_id))){
			echo json_encode(array( 'error' => '300',
									'message' => 'you have no permission on this app'));
			return;
		}
		
		echo json_encode(array( 	'status' => 'OK',
									'message' => 'authenticated'));
	}

	function request_footer_navigation($app_id = NULL, $app_secret_key = NULL
							, $app_install_id = NULL, $app_install_secret_key = NULL, $user_facebook_id = NULL){
		if(!isset($app_id) || !isset($app_secret_key) || !isset($app_install_id) || !isset($app_install_secret_key)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));
			return;
		}
		
		// authenticate app with $app_id and $app_secret_key
		if(!($this->_authenticate_app($app_id, $app_secret_key))){
			echo json_encode(array( 'error' => '200',
									'message' => 'invalid app_secret_key'));
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!($this->_authenticate_app_install($app_install_id, $app_install_secret_key))){
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid app_install_secret_key'));
			return;
		}
		
		if(isset($user_facebook_id)){
			// log user
			$this->load->model('User_apps_model', 'User_apps');
			$this->load->model('User_model', 'User');
			
			// if not exist user, create it
			if(!$this->User_apps->check_exist($user_facebook_id, $app_install_id)){
				/*
				$response = array(	'error' => '600',
								'status' => 'ERROR',
								'message' => 'non exist user, register new user first');
				echo json_encode($response);
				return;
				*/
				$this->User->add_by_facebook_id($user_facebook_id);
				if(!$this->User_apps->check_exist($user_facebook_id, $app_install_id)){
					$this->User_apps->add_new($user_facebook_id, $app_install_id);
				}
				// update user last seen
				$this->User_apps->update_user_last_seen($user_facebook_id, $app_install_id);
				$this->User->update_user_last_seen($user_facebook_id);
				
				$response = array(	'status' => 'OK',
								'message' => 'logged');
			}else{
				// update user last seen
				$this->User_apps->update_user_last_seen($user_facebook_id, $app_install_id);
				$this->User->update_user_last_seen($user_facebook_id);
			}
		}
		$response = array(	'status' => 'OK',
							'html' => '<a href="http://www.socialhappen.com" title="Social Happen">Social Happen</a>');

		echo json_encode($response);
	}
}

/* End of file api.php */
/* Location: ./application/controllers/api.php */