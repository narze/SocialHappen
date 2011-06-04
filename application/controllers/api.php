<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API controller
 * @author Wachiraph C. - revised May 2011
 */
class Api extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		echo json_encode(array('status' => 'OK'));
	}
	
	/**
	 * Request for installation from app
	 * @author Wachiraph C. - revise May 2011
	 */
	function request_install_app(){

		$app_id = $this->input->get('app_id', TRUE); 
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$company_id = $this->input->get('company_id', TRUE);
		$user_id = $this->input->get('user_id', TRUE);

		if(!($app_id) || !($app_secret_key) || !($company_id) || !($user_id) ){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, company_id, user_id)'));
			return;
		}

		//[Deprecated] check pending install app
		/*
		$this->load->model('Installed_apps_model', 'Installed_apps');
		if(!$this->Installed_apps->check_install_app($app_id, $company_id, $user_id)){
			echo json_encode(array( 'error' => '50',
									'message' => 'permission error please install app from platform\'s install page'));
			return;
		}else{
			$this->Installed_apps->delete($app_id, $company_id, $user_facebook_id);
		}*/
		
		// authenticate app with $app_id and $app_secret_key
		if(!($this->_authenticate_app($app_id, $app_secret_key))){
			echo json_encode(array( 'error' => '200',
									'message' => 'invalid app_secret_key'));
			return;
		}
		
		// authenticate user with $company_id and $user_facebook_id
		if(!($this->_authenticate_user($company_id, $user_id))){
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
		
		if(
			$this->Company_apps->add_company_app(array(
					'company_id' => $company_id,
					'app_id' => $app_id
					))
		){
			$this->load->model('Installed_apps_model', 'Installed_apps');
			$app_install_id = $this->Installed_apps->add_installed_app(
										array(
											'company_id' => $company_id,
											'app_id' => $app_id,
											'app_install_status' => TRUE,
											'app_install_secret_key' => $app_install_secret_key
										));
			
			// response
			$response = array(	'status' => 'OK',
								'app_install_id' => $app_install_id,
								'app_install_secret_key' => $app_install_secret_key);
			echo json_encode($response);			
		}else{
			echo json_encode(array( 'error' => '500',
									'message' => 'database error'));
			return;
			
		}
		
		 
	}

	/**
	 * Request for facebook page installaion from app
	 * @author Wachiraph C. - revise May 2011
	 */
	function request_install_page(){
	 						
		$app_id = $this->input->get('app_id', TRUE); 
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$page_id = $this->input->get('page_id', TRUE);
		
		// check parameter
		if(!($app_id) || !($app_install_id) || !($app_secret_key) || !($app_install_secret_key) || !($page_id) ){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, page_id)'));
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
		
		
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$company_id = $this->Installed_apps->get_app_install_by_app_install_id($app_install_id);
		
		if(sizeof($company_id)==0){
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid company_id'));
			return;			
		}
		
		$company_id = $company_id['company_id'];
		
		$this->load->model('Page_model', 'Page');
		
		//[Deprecated] page must be created first
		/*
		// create new company_pages or not?
		if($this->Company_pages->count_all(array('company_id' => $company_id,
											'facebook_page_id' => $facebook_page_id)) == 0){
			$this->Company_pages->add(array('company_id' => $company_id, 
											'facebook_page_id' => $facebook_page_id));
		}
		*/
		
		// check if there is exist page
		$page = $this->Page->get_page_profile_by_page_id($page_id);
		
		if(sizeof($page)!=0){
			// add app to page = new page_apps
			if($page['company_id']==$company_id){
					
				$this->Installed_apps->update_page_id($app_install_id, $page_id);
				
				$response = array(	'status' => 'OK',
							'message' => 'page saved');
			}else{
				$response = array('error' => '500',
									'message' => 'invalid page_id of company');
				
			}
		}else{
			$response = array('error' => '500',
									'message' => 'invalid page_id');
		}
		
		echo json_encode($response);
		
	}

	function _authenticate_app($app_id, $app_secret_key){
		// authenticate app with $app_id and $app_secret_key
		$this->load->model('App_model', 'App');
		$app = $this->App->get_app_by_app_id($app_id);
		if($app != NULL){
			return ($app['app_secret_key']== $app_secret_key);
		}
		return FALSE;
	}
	
	function _authenticate_app_install($app_install_id, $app_install_secret_key){
		// authenticate app with $app_id and $app_secret_key
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$app = $this->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if($app != NULL){
			return ($app['app_install_secret_key'] == $app_install_secret_key);
		}
		return FALSE;
	}
	
	function _authenticate_user($company_id, $user_id){
		// authenticate user with $company_id and $user_id
		$this->load->model('User_companies_model', 'User_companies');
		$company_admin_list_query = $this->User_companies->get_user_companies_by_company_id($company_id, 1000, 0);
		$company_admin_list = array();
		foreach ($company_admin_list_query as $admin) {
			$company_admin_list[] = $admin['user_id'];
		}
		return in_array($user_id, $company_admin_list);
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
	
	/**
	 * Deprecated
	 */
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
			
	/**
	 * Request for platform user's id using user's facebook id
	 * @author Wachiraph C.
	 */				
	function request_user_id(){
		
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		
		if(!($user_facebook_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: user_facebook_id)'));
			return;
		}
		
		$this->load->model('User_model', 'User');
		$user_id = $this->User->get_user_id($user_facebook_id);
		
		if(isset($user_id)){
			$response = array(	'status' => 'OK',
							'user_id' => $user_id['user_id']);
		}

		echo json_encode($response);
	}
	
	/**
	 * Request for platform page's id using facebook page's id
	 * @author Wachiraph C.
	 */
	function request_page_id(){
		
		$facebook_page_id = $this->input->get('facebook_page_id', TRUE);
		
		if(!($facebook_page_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: facebook_page_id)'));
			return;
		}
		
		$this->load->model('Page_model', 'Page');
		$page_id = $this->Page->get_page_id($facebook_page_id);
		
		if(sizeof($page_id)>0){
			$response = array(	'status' => 'OK',
							'page_id' => $page_id['page_id']);
		}

		echo json_encode($response);
	}
					
	/**
	 * Request for log 	
	 * @author Wachiraph C. - revise June 2011
	 */		
	function request_log_user(){
		
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
			
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || !($user_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id)'));
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
		if(!$this->User_apps->check_exist($user_id, $app_install_id)){
			$this->User_apps->add_new($user_id, $app_install_id);
		}
		
		//[Deprecated] wait for external log request
		
		// update user last seen
		$this->User_apps->update_user_last_seen($user_id, $app_install_id);
		$this->User->update_user_last_seen($user_facebook_id);
		
		$response = array(	'status' => 'OK',
						'message' => 'logged');
		
		echo json_encode($response);
		 
	}
							
	/**
	 * First time registration of user to platform
	 * @author Wachiraph C. - revise May 2011
	 */
	function request_register_user(){
								
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || !($user_facebook_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_facebook_id)'));
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
			$user_id = $this->User->add_by_facebook_id($user_facebook_id);
			if($user_id){
				$response['user_id'] = $user_id;
				$response['User'] = 'added ';
			}
		}else{
			$user_id = $this->User->get_user_id($user_facebook_id);
			$user_id = $user_id['user_id'];
		}
		
		// add new user apps
		$this->load->model('User_apps_model', 'User_apps');
		if(!$this->User_apps->check_exist($user_id, $app_install_id)){
			$this->User_apps->add_new($user_id, $app_install_id);
			$response['User_apps'] = 'added';
		}
		
		// update user last seen
		$this->User_apps->update_user_last_seen($user_id, $app_install_id);
		$this->User->update_user_last_seen($user_id);	
		
		echo json_encode($response);
	}
	
	/**
	 * Request for authentication checking
	 * @author Wachiraph C. - revise May 2011
	 */
	function request_authenticate(){
			
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
					
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($user_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, user_id)'));
			return;
		}
		
		// authenticate app with $app_id and $app_secret_key
		if(!($this->_authenticate_app($app_id, $app_secret_key))){
			echo json_encode(array( 'error' => '200',
									'message' => 'invalid app_secret_key'));
			return;
		}
		
		// get company_id
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$app = $this->Installed_apps->get_app_install_by_app_install_id($app_install_id);
		
		if(sizeof($app)!=0){
			$company_id = $app['company_id'];
		}else{
			echo json_encode(array( 'error' => '250',
									'message' => 'invalid app'));
			return;
		}
		
		// authenticate user with $company_id and $user_facebook_id
		if(!($this->_authenticate_user($company_id, $user_id))){
			echo json_encode(array( 'error' => '300',
									'message' => 'you have no permission on this app'));
			return;
		}
		
		echo json_encode(array( 	'status' => 'OK',
									'message' => 'authenticated'));
	}

	/**
	 * Request for log and return platform's layout
	 * @author Wachiraph C. - revise June 2011
	 */	
	function request_footer_navigation(){
		
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
			
			
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key)){
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
		
		if($user_id){
			// log user
			$this->load->model('User_apps_model', 'User_apps');
			$this->load->model('User_model', 'User');
			
			// if not exist user, create it
			if(!$this->User_apps->check_exist($user_id, $app_install_id)){
				$this->User_apps->add_new($user_id, $app_install_id);
			}
			
			//[Deprecated] wait for external log request
			
			// update user last seen
			$this->User_apps->update_user_last_seen($user_id, $app_install_id);
			$this->User->update_user_last_seen($user_facebook_id);
			
		}

		$response = array(	'status' => 'OK',
							'html' => '<a href="http://www.socialhappen.com" title="Social Happen">Social Happen</a>');

		echo json_encode($response);
	}

	/**
	 * Request for campaign information
	 * @author Wachiraph C.
	 */
	function request_campaign_info(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
		$campaign_id = $this->input->get('campaign_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || !($user_id) || !($campaign_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id, campaign_id)'));
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
		
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$company_id = $this->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if(sizeof($company_id)==0){
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid company_id'));
			return;			
		}
		
		$company_id = $company_id['company_id'];
		
		// authenticate user with $company_id and $user_facebook_id
		if(!($this->_authenticate_user($company_id, $user_id))){
			echo json_encode(array( 'error' => '300',
									'message' => 'you have no permission to install app on this company'));
			return;
		}
		
		$this->load->model('Campaign_model', 'Campaign');
		$campaign = $this->Campaign->get_campaign_profile_by_campaign_id($campaign_id);
		
		if(sizeof($campaign)>0){

			if($campaign['app_install_id'] != $app_install_id){
				echo json_encode(array( 'error' => '300',
									'message' => 'you have no permission to access this campaign'));
				return;
			}else{
				
				$response = array(
								'status' => 'OK',
								'campaign_id' => $campaign_id,
								'campaign_name' => $campaign['campaign_name'],
								'campaign_detail' => $campaign['campaign_detail'],
								'campaign_status_id' => $campaign['campaign_status_id'],
								'campaign_status_name' => $campaign['campaign_status_name'],
								'campaign_active_member' => $campaign['campaign_active_member'],
								'campaign_all_member' => $campaign['campaign_all_member'],
								'campaign_start_timestamp' => $campaign['campaign_start_timestamp'],
								'campaign_end_timestamp' => $campaign['campaign_end_timestamp']
							);
				echo json_encode($response);
			}
			
		}else{
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid campaign id'));
			return;		
		}
		
		
	}

	/**
	 * Request for campaign creation
	 * @author Wachiraph C.
	 */
	function request_create_campaign(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
		$campaign_name = $this->input->get('campaign_name', TRUE);
		$campaign_detail = $this->input->get('campaign_detail', TRUE);
		$campaign_start_timestamp = $this->input->get('campaign_start_timestamp', TRUE);
		$campaign_end_timestamp = $this->input->get('campaign_end_timestamp', TRUE);
		$campaign_status_id = $this->input->get('campaign_status_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || !($user_id) || !($campaign_name) || !($campaign_start_timestamp) || !($campaign_end_timestamp)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id, campaign_name, campaign_start_timestamp, campaign_end_timestamp)'));
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
		
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$company_id = $this->Installed_apps->get_app_install_by_app_install_id($app_install_id);
		if(sizeof($company_id)==0){
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid company_id'));
			return;			
		}
		$company_id = $company_id['company_id'];
		
		// authenticate user with $company_id and $user_facebook_id
		if(!($this->_authenticate_user($company_id, $user_id))){
			echo json_encode(array( 'error' => '300',
									'message' => 'you have no permission to install app on this company'));
			return;
		}
		
		$thia->load->model('Campaign_model','Campaign');
		$campaign_id = 0;
				
		if(!$campaign_status_id)
			$campaign_status_id = '2';	// default = active
			
		$campaign_id = $this->Campaign->add_campaign(
									array(
										'campaign_name' => $campaign_name,
										'campaign_detail' => $campaign_detail,
										'campaign_start_timestamp' => $campaign_start_timestamp,
										'campaign_end_time_stamp' => $campaign_end_timestamp,
										'campaign_status_id' => $campaign_status_id
									));
		
		if($campaign_id!=0){
			$response = array(
								'status' => 'OK',
								'campaign_id' => $campaign_id
							);
				echo json_encode($response);
		}else{
			echo json_encode(array( 'error' => '500',
									'message' => 'database error'));
			return;	
		}
		
	}
	
	/**
	 * Request for campaign information update
	 * @author Wachiraph C. 
	 */
	function request_update_campaign(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
		$campaign_id = $this->input->get('campaign_id', TRUE);
		$campaign_name = $this->input->get('campaign_name', TRUE);
		$campaign_detail = $this->input->get('campaign_detail', TRUE);
		$campaign_start_timestamp = $this->input->get('campaign_start_timestamp', TRUE);
		$campaign_end_timestamp = $this->input->get('campaign_end_timestamp', TRUE);
		$campaign_status_id = $this->input->get('campaign_status_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || !($user_id) || !($campaign_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id, campaign_id'));
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
		
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$company_id = $this->Installed_apps->get_app_install_by_app_install_id($app_install_id);
		if(sizeof($company_id)==0){
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid company_id'));
			return;			
		}
		$company_id = $company_id['company_id'];
		
		// authenticate user with $company_id and $user_facebook_id
		if(!($this->_authenticate_user($company_id, $user_id))){
			echo json_encode(array( 'error' => '300',
									'message' => 'you have no permission to install app on this company'));
			return;
		}
				
		$thia->load->model('Campaign_model','Campaign');
		$campaign = $this->Campaign->get_campaign_profile_by_campaign_id($campaign_id);
		
		if(sizeof($campaign)>0){
			
			if($campaign['app_install_id'] != $app_install_id){
				echo json_encode(array( 'error' => '300',
									'message' => 'you have no permission to access this campaign'));
				return;
			}else{
				if(!($campaign_name))
					$campaign_name = $campaign['campaign_name'];
				if(!($campaign_detail))
					$campaign_detail = $campaign['campaign_detail'];
				if(!($campaign_start_timestamp))
					$campaign_start_timestamp = $campaign['campaign_start_timestamp'];
				if(!($campaign_end_timestamp))
					$campaign_end_timestamp = $campaign['campaign_end_timestamp'];
				if(!($campaign_status_id))
					$campaign_status_id = $campaign['campaign_status_id'];
					
				$affected_rows = $this->Campaign->update_campaign_by_id(
											$campaign_id,
											array(
												'campaign_name' => $campaign_name,
												'campaign_detail' => $campaign_detail,
												'campaign_start_timestamp' => $campaign_start_timestamp,
												'campaign_end_time_stamp' => $campaign_end_timestamp,
												'campaign_status_id' => $campaign_status_id
											));
				
				if($affected_rows==1){
					$response = array(
										'status' => 'OK',
										'campaign_id' => $campaign_id,
										'message' => 'update completed'
									);
						echo json_encode($response);
				}else{
					echo json_encode(array( 'error' => '500',
											'message' => 'database error'));
					return;	
				}
			}
		}else{
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid campaign id'));
			return;		
		}
		
	}
	
}

/* End of file api.php */
/* Location: ./application/controllers/api.php */