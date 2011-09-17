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
		
		$this->load->model('Session_model','Session');
		
		if(!$this->Session->get_session_id_by_user_id($user_id)){
			echo json_encode(array( 'error' => '300',
									'message' => 'user session error, please login through platform'));
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
		$company_apps = $this->Company_apps->get_company_apps_by_company_id($company_id);
		
		foreach($company_apps as $company_app){
			if($company_app['app_id']==$app_id){
				$this->load->model('Installed_apps_model', 'Installed_apps');
				$app_install_id = $this->Installed_apps->add_installed_app(
											array(
												'company_id' => $company_id,
												'app_id' => $app_id,
												'app_install_status_id' => $this->socialhappen->get_k("app_install_status", "Installed"),
												'app_install_secret_key' => $app_install_secret_key
											));
											
				$this->load->library('audit_lib');
				$this->audit_lib->add_audit(
											$app_id,
											$user_id,
											1, //presently hard coded
											'', 
											'',
											array(
													'app_install_id'=> $app_install_id,
													'company_id' => $company_id
												)
										);
				
				// response
				$response = array(	'status' => 'OK',
									'app_install_id' => $app_install_id,
									'app_install_secret_key' => $app_install_secret_key);
				echo json_encode($response);		
				return;				
			}
			
		}
		echo json_encode(array( 'error' => '300',
										'message' => 'application is not available for company'));
				return;
				
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
		$user_id = $this->input->get('user_id', TRUE);
		
		// check parameter
		if(!($app_id) || !($app_install_id) || !($app_secret_key) || !($app_install_secret_key) || !($page_id) || !($user_id) ){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, page_id, user_id)'));
			return;
		}
		
		$this->load->model('Session_model','Session');
		if(!$this->Session->get_session_id_by_user_id($user_id)){
			echo json_encode(array( 'error' => '300',
									'message' => 'user session error, please login through platform'));
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
				
				$this->load->library('audit_lib');
				$this->audit_lib->add_audit(
											$app_id,
											$user_id,
											2, //presently hard coded
											'', 
											'',
											array(
													'page_id'=> $page_id,
													'app_install_id'=>$app_install_id,
													'company_id' => $company_id
												)
										);
										
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
		
		if($user_id){
			$response = array(	'status' => 'OK',
							'user_id' => $user_id['user_id']);
		} else {
			$response = array(	'error' => '200');
		}

		echo json_encode($response);
	}
	
	/**
	 * Request for platform user's facebook id using user's id 
	 * @author Weerapat P.
	 */				
	function request_user_facebook_id(){
		
		$user_id = $this->input->get('user_id', TRUE);
		
		if(!($user_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: user_id)'));
			return;
		}
		
		$this->load->model('User_model', 'User');
		$user_facebook_id = $this->User->get_user_facebook_id_by_user_id($user_id);
		
		if($user_facebook_id){
			$response = array(	'status' => 'OK',
							'user_facebook_id' => $user_facebook_id);
		} else {
			$response = array(	'error' => '200');
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
		$page_id = $this->Page->get_page_id_by_facebook_page_id($facebook_page_id);
		
		$response = array();
		if($page_id['page_id']!=null){
			$response = array(	'status' => 'OK',
							'page_id' => $page_id);
		}
		
		echo json_encode($response);
	}
	
	/**
	 * Request for facebook page's id using  platform page's id
	 * @author Wachiraph C.
	 */
	function request_facebook_page_id(){
		
		$page_id = $this->input->get('page_id', TRUE);
		
		if(!($page_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: page_id)'));
			return;
		}
		
		$this->load->model('Page_model', 'Page');
		$facebook_page_id = $this->Page->get_facebook_page_id_by_page_id($page_id);
		
		$response = array();
		if($page_id['page_id']!=null){
			$response = array(	'status' => 'OK',
							'facebook_page_id' => $facebook_page_id);
		}
		
		echo json_encode($response);
	}
								
	/**
	 * Request for log 	
	 * @author Wachiraph C. - revise June 2011
	 * @author Manassarn M. - Add increment achievement
	 */		
	function request_log_user(){
		
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		$campaign_id = $this->input->get('campaign_id', TRUE);
		$action = $this->input->get('action', TRUE);
			
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || !($user_id || $user_facebook_id)){
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
		
		if(!$user_id){
			$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
		}
		// if not exist user, create it
		if(!$this->User_apps->check_exist($user_id, $app_install_id)){
			$this->User_apps->add_new($user_id, $app_install_id);
		}
		
		// update user last seen
		$this->User_apps->update_user_last_seen($user_id, $app_install_id);
		$this->User->update_user_last_seen($user_id);
				
		if(!($action)){ //TODO : use sh globals
			$action = 103;
		}
		$this->load->model('installed_apps_model','installed_apps');
		$app = $this->installed_apps->get_app_profile_by_app_install_id($app_install_id);
		$company_id = $app['company_id'];
		$this->load->model('Audit_action_type_model', 'Audit_action_type');
		$audit_auction_type = $this->Audit_action_type->get_audit_action_by_type_id($action);
		$action_text = $audit_auction_type['audit_action_name'];
		$this->load->library('audit_lib');
		//($app_id = NULL, $subject = NULL, $action_id = NULL, $object = NULL, $objecti = NULL, $additional_data = array())
		$result = $this->audit_lib->add_audit(
			$app_id,
			NULL,
			$action,
			NULL, 
			NULL,
			array(
				'app_install_id'=> $app_install_id,
				'company_id' => $company_id,
				'user_id'=> $user_id,
				'page_id' => issetor($app['page_id'])
			)
		);
		if(!$result){
		$response = array(
			'status' => 'ERROR',
			'message' => 'not logged');
			echo json_encode($response);
			return;
		}
	
		$this->load->library('achievement_lib');
		$info = array('action_id'=> $action, 'app_install_id'=>$app_install_id, 'page_id' =>issetor($app['page_id']));
		if($campaign_id){
			$info['campaign_id'] = $campaign_id;
		}
		$result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, 1);
		if($result){
			$response = array(	
				'status' => 'OK',
				'action_text' => $action_text,
				'message' => 'logged, incremented'
			);
		} else {	
			$response = array(	
				'status' => 'ERROR',
				'action_text' => $action_text,
				'message' => 'logged, not inremented'
			);
		}
		
		echo json_encode($response);
	}
	
	/**
	* Admin change app's configuration for audit log
	* Interface to  request_log_user() with 'save config' action
	* @author Wachiraph C.
	*/
	function request_config_log(){
		$_GET['action'] = 4;
		$this->request_log_user();
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
		$company_id = $this->input->get('company_id', TRUE);
		$page_id = $this->input->get('page_id', TRUE);
		//user profile
		$user_first_name = $this->input->get('user_first_name', TRUE);
		$user_last_name = $this->input->get('user_last_name', TRUE);
		$user_email = $this->input->get('user_email', TRUE);
		$user_profile = array(
			'user_facebook_id' => $user_facebook_id,
			'user_first_name' => $user_first_name,
			'user_last_name' => $user_last_name,
			'user_email' => $user_email
		);
		
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
		
		$response = array(	'status' => 'OK');
		
		// register new user
		$this->load->model('User_model', 'User');
		$user_id = $this->User->add_user($user_profile);
		if($user_id){
			$this->load->library('audit_lib');
			$this->audit_lib->add_audit(
										$app_id,
										$user_id,
										102, //presently hard coded
										'', 
										'',
										array(
												'app_install_id'=> $app_install_id,
												'page_id' => $page_id
											)
									);
			$response['user_id'] = $user_id;
			$response['User'] = 'added';
			$response['message'] = 'New user registered';
		
		} else {
			$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
			$this->load->library('audit_lib');
			$this->audit_lib->add_audit(
										$app_id,
										$user_id,
										103, //presently hard coded
										'', 
										'',
										array(
												'app_install_id'=> $app_install_id,
												'company_id' => $company_id
											)
									);
			
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
		$app = $this->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		
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
	function request_platform_navigation(){
		
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
			$this->User->update_user_last_seen($user_id);
			
		}
		
		$this->load->library('fb_library/FB_library',
							array(
							  'appId'  => $this->config->item('facebook_app_id'),
							  'secret' => $this->config->item('facebook_api_secret'),
							  'cookie' => true,
							),
							'FB');
							
		$loginUrl = $this->FB->getLoginUrl(
									array(
										'redirect_uri' => 'http://socialhappen.dyndns.org/socialhappen/signup',	// permission successful target
										'next'=>'http://socialhappen.dyndns.org/signup',
										'req_perms'=>'offline_access,user_photos'
									)
								);
		
		$response = array(	'status' => 'OK',
							'html' => '<input type="button" value="Connect to SH" onclick="window.open(\''.$loginUrl.'\')"/>'
						);

		echo json_encode($response);
	}
	
	/**
	 * Request for campaign list
	 * @author Wachiraph C.
	 */
	function request_campaign_list(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		
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
		
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$company_id = $this->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if(sizeof($company_id)==0){
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid company_id'));
			return;			
		}
		
		$company_id = $company_id['company_id'];
		
		$this->load->model('Campaign_model', 'Campaign');
		$campaigns = $this->Campaign->get_app_campaigns_by_app_install_id($app_install_id);
		
		if(sizeof($campaigns)>0){

			if($campaigns[0]['app_install_id'] != $app_install_id){
				echo json_encode(array( 'error' => '300',
									'message' => 'you have no permission to access this campaign'));
				return;
			}else{
				foreach($campaigns as $campaign){
				$response[] = array(
								'status' => 'OK',
								'campaign_id' => $campaign['campaign_id'],
								'campaign_name' => $campaign['campaign_name'],
								'campaign_status_id' => $campaign['campaign_status_id'],
								'campaign_status_name' => $campaign['campaign_status']
							);
				}
				echo json_encode($response);
				
			}
			
		}else{
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid campaign id'));
			return;		
		}
		
		
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
		$campaign_id = $this->input->get('campaign_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || !($campaign_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, campaign_id)'));
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
								'campaign_status_name' => $campaign['campaign_status'],
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
		
		$this->load->model('Session_model','Session');
		if(!$this->Session->get_session_id_by_user_id($user_id)){
			echo json_encode(array( 'error' => '300',
									'message' => 'user session error, please login through platform'));
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
		
		$this->load->model('Campaign_model','Campaign');
		$campaign_id = 0;
				
		if(!$campaign_status_id)
			$campaign_status_id = $this->socialhappen->get_k('campaign_status',"Active");	// default = active
			
		$campaign_id = $this->Campaign->add_campaign(
									array(
										'app_install_id' => $app_install_id,
										'campaign_name' => $campaign_name,
										'campaign_detail' => $campaign_detail,
										'campaign_start_timestamp' => $campaign_start_timestamp,
										'campaign_end_timestamp' => $campaign_end_timestamp,
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
		
		$this->load->model('Session_model','Session');
		if(!$this->Session->get_session_id_by_user_id($user_id)){
			echo json_encode(array( 'error' => '300',
									'message' => 'user session error, please login through platform'));
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
				
		$this->load->model('Campaign_model','Campaign');
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
												'campaign_end_timestamp' => $campaign_end_timestamp,
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
	
	/**
	 * Request user session on platform using user_-id
	 * @author Wachiraph C. 
	 */
	function request_user_session(){
		$user_id = $this->input->get('user_id', TRUE);
		
		if(!($user_id)){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: user_id'));
			return;
		}
		
		$this->load->model('Session_model','Session');
		$session_id = $this->Session->get_session_id_by_user_id($user_id);
		
		if($session_id){
			$response = array(	'status' => 'OK',
							'session_id' => $session_id);
		}

		echo json_encode($response);
	}
	
	/**
	 * Request user profile
	 * @author Manassarn M.
	 */
	function request_user(){
								
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

		$response = array('status' => 'OK');
		
		// get user
		$this->load->model('User_model', 'User');
		if(!$this->User->check_exist($user_facebook_id)){
			$response['message'] = 'User not found';
		} else {
			$user = $this->User->get_user_profile_by_user_facebook_id($user_facebook_id);
			$response['user_id'] = $user['user_id'];
			$response['user_first_name'] = $user['user_first_name'];
			$response['user_last_name'] = $user['user_last_name'];
			$response['user_email'] = $user['user_email'];
			//TODO: more fields
		}
		echo json_encode($response);
	}
	
	/**
	 * Request page users
	 * @author Manassarn M.
	 */
	function request_page_users(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		$page_id = $this->input->get('page_id', TRUE);
		
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
		
		$this->load->model('installed_apps_model','installed_apps');
		$app = $this->installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if(issetor($app['page_id'])!=$page_id){
			echo json_encode(array( 'error' => '600',
									'message' => 'invalid page_id'));
			return;
		}
		
		$response = array('status' => 'OK');
		
		//Old method
		// $this->load->model('page_user_data_model','page_users');
		// if(!$page_users = $this->page_users->get_page_users_by_page_id($)){
			// $response['message'] = 'User / page not found';
			// $response['page_users'] = array();
		// } else {
			// $response['page_users'] = $page_users;
			//TODO: limit fields
		// }
		// echo json_encode($response);
		
		$users = array(); //id => value
		$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
		$this->load->model('user_apps_model','user_apps');
		foreach($apps as $app){
			$app_install_id = $app['app_install_id'];
			if($app_users = $this->user_apps->get_app_users_by_app_install_id($app_install_id)){
				foreach($app_users as $app_user){
					if(!isset($users[$app_user['user_id']])){
						$users[$app_user['user_id']] = $app_user;
					}
				}
			}
		}
		$response['page_users'] = array_values($users);
		echo json_encode($response);
	}
	
	/**
	 * Request app users
	 * @author Manassarn M.
	 */
	function request_app_users(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		//$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		//$page_id = $this->input->get('page_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key)){
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
		
		$response = array('status' => 'OK');
		
		$this->load->model('user_apps_model','user_apps');
		if(!$app_users = $this->user_apps->get_app_users_by_app_install_id($app_install_id)){
			$response['message'] = 'User / page not found';
			$response['app_users'] = array();
		} else {
			$response['app_users'] = $app_users;
			//TODO: limit fields
		}
		echo json_encode($response);
	}
	
	/**
	 * Request add user point
	 * @author Manassarn M.
	 */
	function request_add_user_point(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		$point = $this->input->get('point', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || !($user_facebook_id) || !$point){
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_facebook_id, point)'));
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
		
		$response = array('status' => 'OK');
		
		$this->load->model('User_model', 'User');
		if(!$user = $this->User->get_user_profile_by_user_facebook_id($user_facebook_id)){
			$response['message'] = 'User not found';
		} else {
			$this->User->update_user($user['user_id'],array('user_point' => $user['user_point'] + (int)$point));
			$response['message'] = 'Point added';
		}
		echo json_encode($response);
		//TODO audit
	}

	function bar(){
		
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
		
		$response = array('status' => 'OK');
		$data = array();
		
		$this->load->model('Installed_apps_model', 'app');
		$app = $this->app->get_app_profile_by_app_install_id($app_install_id);
		if(!isset($app['page_id'])){
			//no page_id, app not found
		}
		
		$this->load->model('User_model', 'User');
		$this->load->model('user_pages_model','user_pages');
		if(!$user = $this->User->get_user_profile_by_user_facebook_id($user_facebook_id)){
			$data['view_as'] = 'guest';
			$data['right_menu'] = array();
		} else if($this->user_pages->is_page_admin($user['user_id'], $app['page_id'])){
			$data['view_as'] = 'admin';
			$data['right_menu'] = array(
				array('location' => '#', 'title' => 'Config this app'),
				array('location' => '#', 'title' => 'View my profile'),
				array('location' => '#', 'title' => 'Go to dashboard'),
				array('location' => '#', 'title' => 'Go to platform')
			);
		} else {
			$data['view_as'] = 'user';
			$data['right_menu'] = array(
				array('location' => '#', 'title' => 'View my profile'),
				array('location' => '#', 'title' => 'Go to Dashboard'),
			);
		}
		
		$data['left_menu'] = array();
		if($app['page_id']){
			$apps = $this->app->get_installed_apps_by_page_id($app['page_id']);
			$this->load->library('app_url');
			foreach($apps as $page_app){
				if($page_app['app_install_id'] != $app_install_id){
					$data['left_menu'][] = 
					array('location' => 
						$this->app_url->translate_url($page_app['app_url'], 
						$page_app['app_install_id']),
								'title' => $page_app['app_name']);
				}
			}
		}
		
		$data['app_icon_url'] = $app['app_image'];
		$data['app_name'] = $app['app_name'];
		
		$data['user_diplay_picture_url'] = $user['user_image'];
		$data['user_display_name'] = $user['user_first_name']. ' ' . $user['user_last_name'];
		
		$response['html'] = $this->load->view('api/app_bar_view', $data, TRUE);
		$response['css'] = base_url() . 'css/api_app_bar.css';
		echo json_encode($response);
	}

	function test_bar_api(){
		$result = json_decode(file_get_contents('http://127.0.0.1/socialhappen/api/bar?app_id=1&app_install_id=1&app_secret_key=ad3d4f609ce1c21261f45d0a09effba4&app_install_secret_key=457f81902f7b768c398543e473c47465&user_facebook_id=755758746'), true);
		echo '<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>';
		echo '<link type="text/css" rel="stylesheet" href="'.$result['css'].'" />';
		echo '<div style="width:800px;margin:0 auto;">'.$result['html'].'</div>';
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
	
	
}

/* End of file api.php */
/* Location: ./application/controllers/api.php */