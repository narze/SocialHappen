<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API controller
 * @author Wachiraph C. - revised May 2011
 */
class Api extends CI_Controller {

	function __construct(){
		header("Access-Control-Allow-Origin: *");
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
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);

		if(!($app_id) || !($app_secret_key) || !($company_id) || (!$user_id && !$user_facebook_id) ){
			log_message('error','Missing parameters (app_id, app_secret_key, company_id, user_id/user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, company_id, user_id/user_facebook_id)'));
			return;
		}
		
		if(!$user_id){
			$this->load->model('user_model','user');
			$user_id = $this->user->get_user_id_by_user_facebook_id($user_facebook_id);
		}
		
		$this->load->model('Session_model','Session');
		if(!$this->Session->get_session_id_by_user_id($user_id)){
			log_message('error',"User #{$user_id} has no session");
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
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate user with $company_id and $user_facebook_id
		if(!$this->_authenticate_user($company_id, $user_id)){
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
											$this->socialhappen->get_k('audit_action','Install App'),
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
		
		log_message('error','This company doesn\'t have this app');
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
		$facebook_page_id = $this->input->get('facebook_page_id', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		
		// check parameter
		if(!($app_id) || !($app_install_id) || !($app_secret_key) || !($app_install_secret_key) || (!$page_id && !$facebook_page_id) || (!$user_id && !$user_facebook_id ) ){
			log_message('error','Missing parameters (app_id, app_secret_key, app_install_id, app_install_secret_key, page_id/facebook_page_id, user_id/user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, page_id/facebook_page_id, user_id/user_facebook_id)'));
			return;
		}
		
		if(!$user_id){
			$this->load->model('user_model','user');
			$user_id = $this->user->get_user_id_by_user_facebook_id($user_facebook_id);
		}
		
		$this->load->model('Session_model','Session');
		if(!$this->Session->get_session_id_by_user_id($user_id)){
			log_message('error',"User #{$user_id} has no session");
			echo json_encode(array( 'error' => '300',
									'message' => 'user session error, please login through platform'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
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
		
		if(!$page_id){
			$page_id = $this->Page->get_page_id_by_facebook_page_id($facebook_page_id);
		}
		
		if($page = $this->Page->get_page_profile_by_page_id($page_id)){
			// add app to page = new page_apps
			if($page['company_id']==$company_id){
					
				$this->Installed_apps->update_page_id($app_install_id, $page_id);
				
				//Update latest installed app install id in page
				$this->Page->update_page_profile_by_page_id($page_id, array('page_app_installed_id' => $app_install_id));
				
			
										
				$response = array(	'status' => 'OK',
							'message' => 'page saved');
			}else{
				log_message('error','company_id mismatch');
				$response = array('error' => '500',
									'message' => 'invalid page_id of company');
				
			}
		}else{
			log_message('error','invalid page_id');
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
			log_message('error','Missing parameters (app_id, app_secret_key, app_install_id, app_install_secret_key)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
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
			log_message('error','Missing parameter (user_facebook_id)');
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
			log_message('error','user_id not found');
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
			log_message('error','Missing parameter (user_id)');
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
			log_message('error','user_facebook_id not found');
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
			log_message('error','Missing parameter (facebook_page_id)');
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
			log_message('error','Missing parameter (page_id)');
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
			
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_id && !$user_facebook_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		// log user
		$this->load->model('User_apps_model', 'User_apps');
		$this->load->model('User_model', 'User');
		
		if(!$user_id){
			$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
		}else if(!$this->User_apps->check_exist($user_id, $app_install_id)){ // if not exist user, create it
			$this->User_apps->add_new($user_id, $app_install_id);
			
			$this->load->library('audit_lib');
			$action_id = $this->socialhappen->get_k('audit_action','User Register App');
			$this->audit_lib->add_audit(
				0,
				$user_id,
				$action_id,
				'', 
				'',
				array(
					'app_id' => $app_id,
					'app_install_id' => $app_install_id,
					'user_id' => $user_id
				)
			);
			
			$this->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>$app_install_id, 'app_id'=>$app_id);
			$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
		}
		
		// update user last seen
		$this->User_apps->update_user_last_seen($user_id, $app_install_id);
		$this->User->update_user_last_seen($user_id);
				
		if(!$action){ //User default action if not specified
			$action = $this->socialhappen->get_k('audit_action', 'User Visit');
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
			$user_id,
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
		log_message('error','add_audit failed');
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
			log_message('error','increment_achievement_stat failed');
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
		$facebook_page_id = $this->input->get('facebook_page_id', TRUE);
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
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_facebook_id)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$response = array(	'status' => 'OK');
		
		if(!$page_id){
			$page_id = $this->Page->get_page_id_by_facebook_page_id($facebook_page_id);
		}
		
		// register new user
		$this->load->model('User_model', 'User');
		$user_id = $this->User->add_user($user_profile);
		if($user_id){
			$action_id = $this->socialhappen->get_k('audit_action','User Register App');
			$this->load->library('audit_lib');
			$this->audit_lib->add_audit(
				$app_id,
				$user_id,
				$action_id,
				'', 
				'',
				array(
						'app_install_id'=> $app_install_id,
						'page_id' => $page_id
					)
			);
			$this->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>$app_install_id, 'page_id' => $page_id);
			$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
			
			$action_id = $this->socialhappen->get_k('audit_action','User Register SocialHappen');
			$this->audit_lib->add_audit(
				0,
				$user_id,
				$action_id,
				'', 
				'',
				array(
					'page_id'=> $page_id,
					'app_install_id' => 0,
					'user_id' => $user_id
				)
			);
			$info = array('action_id'=> $action_id, 'app_install_id'=>0, 'page_id' => $page_id);
			$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
									
			$response['user_id'] = $user_id;
			$response['User'] = 'added';
			$response['message'] = 'New user registered';
		
		} else {
			$action_id = $this->socialhappen->get_k('audit_action','User Visit');
			$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
			$this->load->library('audit_lib');
			$this->audit_lib->add_audit(
				$app_id,
				$user_id,
				$action_id,
				'', 
				'',
				array(
					'app_id' => $app_id,
					'app_install_id'=> $app_install_id,
					'company_id' => $company_id
				)
			);
			
			$this->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>$app_install_id, 'app_id'=>$app_id);
			$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
			
		}
		
		// add new user apps
		$this->load->model('User_apps_model', 'User_apps');
		if(!$this->User_apps->check_exist($user_id, $app_install_id)){
			$this->User_apps->add_new($user_id, $app_install_id);
			$response['User_apps'] = 'added';
			
			$this->load->library('audit_lib');
			$action_id = $this->socialhappen->get_k('audit_action','User Register App');
			$this->audit_lib->add_audit(
				0,
				$user_id,
				$action_id,
				'', 
				'',
				array(
					'app_id' => $app_id,
					'app_install_id' => $app_install_id,
					'user_id' => $user_id
				)
			);
			
			$this->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>$app_install_id, 'app_id'=>$app_id);
			$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
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
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
					
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || (!$user_id && !$user_facebook_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, user_id/user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, user_id/user_facebook_id)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// get company_id
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$app = $this->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		
		if(sizeof($app)!=0){
			$company_id = $app['company_id'];
		}else{
			log_message('error','app not found');
			echo json_encode(array( 'error' => '250',
									'message' => 'invalid app'));
			return;
		}
		
		// authenticate user with $company_id and $user_facebook_id
		if(!$user_id){
			$this->load->model('user_model','user');
			$user_id = $this->user->get_user_id_by_user_facebook_id($user_facebook_id);
		} else if(!$this->_authenticate_user($company_id, $user_id)){
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
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
			
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->load->model('User_model', 'User');
		if($user_id || $user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id)){
			// log user
			$this->load->model('User_apps_model', 'User_apps');
			
			// if not exist user, create it
			if(!$this->User_apps->check_exist($user_id, $app_install_id)){
				$this->User_apps->add_new($user_id, $app_install_id);
				
				$this->load->library('audit_lib');
				$action_id = $this->socialhappen->get_k('audit_action','User Register App');
				$this->audit_lib->add_audit(
					0,
					$user_id,
					$action_id,
					'', 
					'',
					array(
						'app_id' => $app_id,
						'app_install_id' => $app_install_id,
						'user_id' => $user_id
					)
				);
				
				$this->load->library('achievement_lib');
				$info = array('action_id'=> $action_id, 'app_install_id'=>$app_install_id, 'app_id'=>$app_id);
				$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
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
										'redirect_uri' => base_url().'home/signup',	// permission successful target
										'next'=>base_url().'home/signup',
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
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$company_id = $this->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if(sizeof($company_id)==0){
			log_message('error','company not found');
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid company_id'));
			return;			
		}
		
		$company_id = $company_id['company_id'];
		
		$this->load->model('Campaign_model', 'Campaign');
		$campaigns = $this->Campaign->get_app_campaigns_by_app_install_id($app_install_id);
		
		if(sizeof($campaigns)>0){

			if($campaigns[0]['app_install_id'] != $app_install_id){
				log_message('error','app_install_id mismatch');
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
			log_message('error','campaign not found');
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
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, campaign_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, campaign_id)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$company_id = $this->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if(sizeof($company_id)==0){
			log_message('error','company not found');
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid company_id'));
			return;			
		}
		
		$company_id = $company_id['company_id'];
		
		$this->load->model('Campaign_model', 'Campaign');
		$campaign = $this->Campaign->get_campaign_profile_by_campaign_id($campaign_id);
		
		if(sizeof($campaign)>0){

			if($campaign['app_install_id'] != $app_install_id){
				log_message('error','app_install_id mismatch');
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
								'campaign_start_date' => $campaign['campaign_start_date'],
								'campaign_end_date' => $campaign['campaign_end_date']
							);
				echo json_encode($response);
			}
			
		}else{
			log_message('error','campaign not found');
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
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		$campaign_name = $this->input->get('campaign_name', TRUE);
		$campaign_detail = $this->input->get('campaign_detail', TRUE);
		$campaign_start_date = $this->input->get('campaign_start_date', TRUE);
		$campaign_end_date = $this->input->get('campaign_end_date', TRUE);
		$campaign_status_id = $this->input->get('campaign_status_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_id && !$user_facebook_id) || !($campaign_name) || !($campaign_start_date) || !($campaign_end_date)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id, campaign_name, campaign_start_date, campaign_end_date)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id, campaign_name, campaign_start_date, campaign_end_date)'));
			return;
		}
		
		if(!$user_id){
			$this->load->model('user_model','user');
			$user_id = $this->user->get_user_id_by_user_facebook_id($user_facebook_id);
		}
		
		$this->load->model('Session_model','Session');
		if(!$this->Session->get_session_id_by_user_id($user_id)){
			log_message('error',"User #{$user_id} has no session");
			echo json_encode(array( 'error' => '300',
									'message' => 'user session error, please login through platform'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$company_id = $this->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if(sizeof($company_id)==0){
			log_message('error','company not found');
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid company_id'));
			return;			
		}
		$company_id = $company_id['company_id'];
		
		// authenticate user with $company_id and $user_facebook_id
		if(!$this->_authenticate_user($company_id, $user_id)){
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
										'campaign_start_date' => $campaign_start_date,
										'campaign_end_date' => $campaign_end_date,
										'campaign_status_id' => $campaign_status_id
									));
		
		if($campaign_id!=0){
			$response = array(
								'status' => 'OK',
								'campaign_id' => $campaign_id
							);
				echo json_encode($response);
		}else{
			log_message('error','cannot add campaign');
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
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		$campaign_id = $this->input->get('campaign_id', TRUE);
		$campaign_name = $this->input->get('campaign_name', TRUE);
		$campaign_detail = $this->input->get('campaign_detail', TRUE);
		$campaign_start_date = $this->input->get('campaign_start_date', TRUE);
		$campaign_end_date = $this->input->get('campaign_end_date', TRUE);
		$campaign_status_id = $this->input->get('campaign_status_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_id && !$user_facebook_id) || !($campaign_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id, campaign_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id, campaign_id'));
			return;
		}
		
		if(!$user_id){
			$this->load->model('user_model','user');
			$user_id = $this->user->get_user_id_by_user_facebook_id($user_facebook_id);
		}
		
		$this->load->model('Session_model','Session');
		if(!$this->Session->get_session_id_by_user_id($user_id)){
			log_message('error',"User #{$user_id} has no session");
			echo json_encode(array( 'error' => '300',
									'message' => 'user session error, please login through platform'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$company_id = $this->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if(sizeof($company_id)==0){
			log_message('error','company not found');
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid company_id'));
			return;			
		}
		$company_id = $company_id['company_id'];
		
		if(!$this->_authenticate_user($company_id, $user_id)){
			return;
		}
				
		$this->load->model('Campaign_model','Campaign');
		$campaign = $this->Campaign->get_campaign_profile_by_campaign_id($campaign_id);
		
		if(sizeof($campaign)>0){
			
			if($campaign['app_install_id'] != $app_install_id){
				log_message('error','app_install_id mismatch');
				echo json_encode(array( 'error' => '300',
									'message' => 'you have no permission to access this campaign'));
				return;
			}else{
				if(!($campaign_name))
					$campaign_name = $campaign['campaign_name'];
				if(!($campaign_detail))
					$campaign_detail = $campaign['campaign_detail'];
				if(!($campaign_start_date))
					$campaign_start_date = $campaign['campaign_start_date'];
				if(!($campaign_end_date))
					$campaign_end_date = $campaign['campaign_end_date'];
				if(!($campaign_status_id))
					$campaign_status_id = $campaign['campaign_status_id'];
					
				$affected_rows = $this->Campaign->update_campaign_by_id(
											$campaign_id,
											array(
												'campaign_name' => $campaign_name,
												'campaign_detail' => $campaign_detail,
												'campaign_start_date' => $campaign_start_date,
												'campaign_end_date' => $campaign_end_date,
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
					log_message('error','campaign update failed');
					echo json_encode(array( 'error' => '500',
											'message' => 'database error'));
					return;	
				}
			}
		}else{
			log_message('error','campaign not found');
			echo json_encode(array( 'error' => '500',
									'message' => 'invalid campaign id'));
			return;		
		}
		
	}
	
	/**
	 * Request user session on platform using user_id or user_facebook_id
	 * @author Wachiraph C. 
	 */
	function request_user_session(){
		$user_id = $this->input->get('user_id', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		
		if(!$user_id && !$user_facebook_id){
			log_message('error','Missing parameter (user_id/user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: user_id/user_facebook_id'));
			return;
		}
		
		if(!$user_id){
			$this->load->model('user_model','user');
			$user_id = $this->user->get_user_id_by_user_facebook_id($user_facebook_id);
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
		$user_id = $this->input->get('user_id', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);

		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_facebook_id && !$user_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}

		$response = array('status' => 'OK');
		
		// get user
		$this->load->model('User_model', 'User');
		if(!$user_facebook_id){
			$user_facebook_id = $this->User->get_user_facebook_id_by_user_id($user_id);
		}
		
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
		$page_id = $this->input->get('page_id', TRUE);
		$facebook_page_id = $this->input->get('facebook_page_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$page_id && !$facebook_page_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, page_id/facebook_page_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, page_id/facebook_page_id)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->load->model('installed_apps_model','installed_apps');
		$app = $this->installed_apps->get_app_profile_by_app_install_id($app_install_id);
		
		
		if(!$page_id){
			$this->load->model('page_model','Page');
			$page_id = $this->Page->get_page_id_by_facebook_page_id($facebook_page_id);
		}
		
		if(issetor($app['page_id'])!=$page_id){
			log_message('error','page_id mismatch');
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
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
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
	 * DEPRECATED
	 * Request add user point 
	 * @author Manassarn M.
	 */
	function request_add_user_point(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
		$point = $this->input->get('point', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_facebook_id && !$user_id) || !$point){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id, point)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id, point)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$response = array('status' => 'OK');
		
		$user = $user_id ? $this->User->get_user_profile_by_user_id($user_id) : $this->User->get_user_profile_by_user_facebook_id($user_facebook_id);

		if(!$user){
			$response['message'] = 'User not found';
		} else {
			$this->User->update_user($user['user_id'],array('user_point' => $user['user_point'] + (int)$point));
			$response['message'] = 'Point added';
		}
		echo json_encode($response);
		//TODO audit
	}

	/**
	 * Request add limit service
	 * @author Wachiraph C.
	 */
	function request_add_limit_service(){
	
		$user_id = $this->input->get('user_id', TRUE);
		$action_no = $this->input->get('action_no', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$campaign_id = $this->input->get('campaign_id', TRUE);
	
		if(empty($user_id))
			$user_id = NULL;
		else
			$user_id = (int) $user_id;
		
		if(empty($action_no))
			$action_no = NULL;
		else
			$action_no = (int) $action_no;
		
		if(empty($app_install_id))
			$app_install_id = NULL;
		else
			$app_install_id = (int) $app_install_id;
		
		if(empty($campaign_id))
			$campaign_id = NULL;
		else
			$campaign_id = (int) $campaign_id;
	
		$this->load->library('audit_stat_limit_lib');
		$result = $this->audit_stat_limit_lib->add($user_id, 
										$action_no,
										$app_install_id,
										$campaign_id);
		if(!$result) {
			log_message('error','audit_stat_limit_lib failed');
			$response = array('status' => 'Error');
		} else {
			$response = array('status' => 'OK');
		}
		echo json_encode($response);
	}
	
	/**
	 * Request count limit service
	 * @author Wachiraph C.
	 */
	function request_count_limit_service(){
	
		$user_id = $this->input->get('user_id', TRUE);
		$action_no = $this->input->get('action_no', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$campaign_id = $this->input->get('campaign_id', TRUE);
		$back_time_interval = $this->input->get('back_time_interval', TRUE);
		
		if(empty($user_id))
			$user_id = NULL;
		else
			$user_id = (int) $user_id;
		
		if(empty($action_no))
			$action_no = NULL;
		else
			$action_no = (int) $action_no;
		
		if(empty($app_install_id))
			$app_install_id = NULL;
		else
			$app_install_id = (int) $app_install_id;
		
		if(empty($campaign_id))
			$campaign_id = NULL;
		else
			$campaign_id = (int) $campaign_id;
		
		if(empty($back_time_interval))
			$back_time_interval = NULL;
		else
			$back_time_interval = (int) $back_time_interval;
		
		$this->load->library('audit_stat_limit_lib');
		$result = $this->audit_stat_limit_lib->count($user_id,
													$action_no,
													$app_install_id,
													$campaign_id,
													$back_time_interval);
		
		
		$response = array(
						'status' => 'OK',
						'count' => $result 			
						);
		echo json_encode($response);
	}
	
	
	/**
	 * Request app bar
	 * @author Manassarn M.
	 */
	function bar(){
		
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_facebook_id && !$user_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		
		
		$data = array(
			'app_install_id' => $app_install_id,
			'user_id' => $user_id,
			'user_facebook_id' => $user_facebook_id,
		);
		
		$response = array('status' => 'OK');
		$response['html'] = $this->socialhappen->get_bar($data);
		$response['css'] = base_url() . 'assets/css/common/api_app_bar.css';
		echo json_encode($response);
	}

	function test_bar_api(){
		$result = json_decode(file_get_contents('http://127.0.0.1/socialhappen/api/bar?app_id=1&app_install_id=1&app_secret_key=ad3d4f609ce1c21261f45d0a09effba4&app_install_secret_key=457f81902f7b768c398543e473c47465&user_facebook_id=755758746'), true);
		echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>';
		echo '<link type="text/css" rel="stylesheet" href="'.$result['css'].'" />';
		echo '<div style="width:800px;margin:0 auto;">'.$result['html'].'</div>';
	}
	
	/**
	 * Request app get started
	 * @author Weerapat P.
	 */
	function get_started(){
		
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_facebook_id && !$user_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		//get page_id
		$this->load->model('Installed_apps_model', 'app');
		$app = $this->app->get_app_profile_by_app_install_id($app_install_id);
		
		$data = array(
			'app_install_id' => $app_install_id,
			'page_id' => $app['page_id'],
			'user_id' => $user_id,
			'user_facebook_id' => $user_facebook_id,
			'view' => 'app_get_started'
		);
		
		$response = array('status' => 'OK');
		$response['html'] = $this->socialhappen->get_setting_template($data);
		echo json_encode($response);
	}
	
	/**
	 * Request app setting template
	 * @author Weerapat P.
	 */
	function setting_template(){
		
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_facebook_id && !$user_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		//get page_id
		$this->load->model('Installed_apps_model', 'app');
		$app = $this->app->get_app_profile_by_app_install_id($app_install_id);
		
		$data = array(
			'app_install_id' => $app_install_id,
			'page_id' => $app['page_id'],
			'user_id' => $user_id,
			'user_facebook_id' => $user_facebook_id
		);
		
		$response = array('status' => 'OK');
		$response['html'] = $this->socialhappen->get_setting_template($data);
		echo json_encode($response);
	}

  function show_notification(){
    $user_id = $this->input->get('user_id', TRUE);
    $limit = $this->input->get('limit', TRUE);
    
    // @TODO: validate user_id here
    
    $this->load->library('notification_lib');
    $limit = !$limit ? 10 : $limit;
    if(!$user_id){
      $notification_list = array();
    }else{
      $notification_list = $this->notification_lib->lists($user_id, $limit, 0);
      $notification_list = !$notification_list ? array() : $notification_list;
      for ($i = 0; $i < count($notification_list); $i++) { 
        $notification_list[$i]['_id'] = (string) $notification_list[$i]['_id']; 
      }
    }
    echo json_encode(array('notification_list' => $notification_list));
  }

  function read_notification(){
    $user_id = $this->input->get('user_id', TRUE);
    $notification_list = $this->input->get('notification_list', TRUE);
    $notification_list = !$notification_list ? array() :
     json_decode($notification_list);
    
    // @TODO: validate user_id here
    
    if(!$user_id || count($notification_list) == 0){
      echo json_encode(array('result' => 'OK', 'read' => 0));
    }else{
      $this->load->library('notification_lib');
      $result = $this->notification_lib->read($user_id, $notification_list);
      
      echo json_encode(array('result' => $result ? 'OK' : 'FAIL'));
    }
  }
	
	/**
	 * Request add achievement infos
	 * @author Manassarn M.
	 */
	function request_add_achievement_infos(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$achievement_infos = $this->input->get('achievement_infos', TRUE);
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || !($achievement_infos)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, achievement_infos)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, achievement_infos)'));
			return;
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$response = array('status' => 'OK');
		$achievement_infos = json_decode(base64_decode($achievement_infos), TRUE);
		$this->load->library('achievement_lib');
		foreach($achievement_infos as $achievement_info){
			$this->achievement_lib->add_achievement_info(
				$app_id, $app_install_id,
				$achievement_info['info'], $achievement_info['criteria']);
		}
		
		echo json_encode($response);
	}
	
	/**
	 * Request for socialhappen login
	 * @author Manassarn M.
	 */
	function request_login(){
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);

		if(!$user_facebook_id){
			log_message('debug','Missing parameter (user_facebook_id)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: user_facebook_id)'));
			return;
		}
		$this->load->model('User_model', 'User');
		$user_id_check = $this->User->get_user_id($user_facebook_id);
		
		$user_id = $this->socialhappen->login();
		if($user_id && $user_id == $user_id_check){
			$response = array('status' => 'OK', 'user_id' => $user_id);
		} else {
			$response = array('status' => 'ERROR', 'message' => 'User not found');
			$this->socialhappen->logout();
		}
		echo json_encode($response);
	}
	
	/**
	 * Request for facebook tab url
	 * @author Manassarn M.
	 */
	function request_facebook_tab_url(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$facebook_page_id = $this->input->get('facebook_page_id', TRUE); //Optional
		$page_id = $this->input->get('page_id', TRUE); //Optional
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key)');
			echo json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));
			return;
		}
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$response = array();
		if(!$page_id && !$facebook_page_id){ //Request for facebook tab url for app
			$this->load->model('installed_apps_model','installed_app');
			if(!$app = $this->installed_app->get_app_profile_by_app_install_id($app_install_id)){
				$response['message'] = 'App not found';
			} else if (!$facebook_tab_url = $app['facebook_tab_url']){
				$response['message'] = 'Facebook tab url not found in this app';
			} else {
				$response['facebook_tab_url'] = $facebook_tab_url;
			}
		} else { //Request for facebook tab url for page
			$this->load->model('page_model','page');
			if(!$page_id){
				$page_id = $this->page->get_page_id_by_facebook_page_id($facebook_page_id);
			}
			if(!$page = $this->page->get_page_profile_by_page_id($page_id)){
				$response['message'] = 'Page not found';
			} else if(!$facebook_tab_url = $page['facebook_tab_url']){
				$response['message'] = 'Facebook tab url not found in this page';
			} else {
				$response['facebook_tab_url'] = $facebook_tab_url;
			}
		}
		if(isset($response['facebook_tab_url'])){
			$response['status'] = 'OK';
		} else {
			$response['status'] = 'ERROR';
		}
		
		echo json_encode($response);
	}
	
	function test_tab_url_api(){
		$result_page_url = json_decode(file_get_contents('https://127.0.0.1/socialhappen/api/request_facebook_tab_url?app_id=1&app_install_id=1&app_secret_key=ad3d4f609ce1c21261f45d0a09effba4&app_install_secret_key=457f81902f7b768c398543e473c47465&page_id=1'), true);
		$result_app_url = json_decode(file_get_contents('https://127.0.0.1/socialhappen/api/request_facebook_tab_url?app_id=1&app_install_id=1&app_secret_key=ad3d4f609ce1c21261f45d0a09effba4&app_install_secret_key=457f81902f7b768c398543e473c47465'), true);
		$result_error = json_decode(file_get_contents('https://127.0.0.1/socialhappen/api/request_facebook_tab_url?app_id=1&app_install_id=1&app_secret_key=ad3d4f609ce1c21261f45d0a09effba4&app_install_secret_key=457f81902f7b768c398543e473c47465&page_id=100'), true);
		echo "<pre>";
		var_export($result_page_url);
		var_export($result_app_url);
		var_export($result_error);
		echo "</pre>";
	}
	
	function _authenticate_app($app_id, $app_secret_key){
		// authenticate app with $app_id and $app_secret_key
		$this->load->model('App_model', 'App');
		$app = $this->App->get_app_by_app_id($app_id);
		if($app != NULL && $app['app_secret_key']== $app_secret_key){
			return TRUE;
		} else {
			log_message('error','app_secret_key mismatch, app authenticate failed');
			echo json_encode(array( 
				'error' => '200',
				'message' => 'invalid app_secret_key')
			);
			return FALSE;
		}
	}
	
	function _authenticate_app_install($app_install_id, $app_install_secret_key){
		// authenticate app with $app_id and $app_install_secret_key
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$app = $this->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if($app != NULL){
			return ($app['app_install_secret_key'] == $app_install_secret_key);
		} else {
			log_message('error','app_install_secret_key mismatch, app authenticate failed');
			echo json_encode(array( 
				'error' => '500',
				'message' => 'invalid app_install_secret_key')
			);
			return FALSE;
		}
	}
	
	function _authenticate_user($company_id, $user_id){
		// authenticate user with $company_id and $user_id
		$this->load->model('User_companies_model', 'User_companies');
		$company_admin_list_query = $this->User_companies->get_user_companies_by_company_id($company_id, 1000, 0);
		$company_admin_list = array();
		foreach ($company_admin_list_query as $admin) {
			$company_admin_list[] = $admin['user_id'];
		}
		if(in_array($user_id, $company_admin_list)){
			return TRUE;
		} else {
			log_message('error',"User #{$user_id} has no permission in company #{$company_id}");
			echo json_encode(array( 
				'error' => '300',
				'message' => 'you have no permission to install app on this company')
			);
			return FALSE;
		}
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