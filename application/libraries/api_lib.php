<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API Library
 * 
 * @author Wachiraphan C.
 */
class Api_Lib {

	function __construct() {
        $this->CI =& get_instance();
    }

	/**
	 * Request for app installation
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param company_id int id of a company
	 *	@param user_id int SH id of a user
	 *	@param user_facebook_id string facebook id of an user
	 *
	 *	@return array of app_install_id and app_install_secret_key
	 *	@author Wachiraphan C.
	 *
	 */
	function request_install_app($app_id = NULL, $app_secret_key = NULL, 
		$company_id = NULL, $user_id = NULL, $user_facebook_id = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($company_id) || (!$user_id && !$user_facebook_id) ){
			log_message('error','Missing parameters (app_id, app_secret_key, company_id, user_id/user_facebook_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, company_id, user_id/user_facebook_id)'));
		}
		
		if(!$user_id){
			$this->load->model('user_model','user');
			$user_id = $this->user->get_user_id_by_user_facebook_id($user_facebook_id);
		}
				
		$this->CI->load->model('Session_model','Session');
		if(!$this->CI->Session->get_session_id_by_user_id($user_id)){
			log_message('error',"User #{$user_id} has no session");
			
			return (array( 'error' => '300',
									'message' => 'user session error, please login through platform'));
			
		}		
		
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
		$this->CI->load->model('Company_apps_model', 'Company_apps');
		$company_apps = $this->CI->Company_apps->get_company_apps_by_company_id($company_id);
		
		foreach($company_apps as $company_app){
			if($company_app['app_id']==$app_id){
				$this->CI->load->model('Installed_apps_model', 'Installed_apps');
				$app_install_id = $this->CI->Installed_apps->add_installed_app(
											array(
												'company_id' => $company_id,
												'app_id' => $app_id,
												'app_install_status_id' => $this->CI->socialhappen->get_k("app_install_status", "Installed"),
												'app_install_secret_key' => $app_install_secret_key
											));
											
				$this->CI->load->library('audit_lib');
				$this->CI->audit_lib->add_audit(
											$app_id,
											$user_id,
											$this->CI->socialhappen->get_k('audit_action','Install App'),
											'', 
											'',
											array(
													'app_install_id'=> $app_install_id,
													'company_id' => $company_id
												)
										);
				
				//Add first 10-year campaign
				$this->CI->load->model('campaign_model','campaign');
				date_default_timezone_set('UTC');
				$campaign = array(
					'app_install_id' => $app_install_id,
					'campaign_name' => 'Campaign',
					'campaign_start_timestamp' => date("y-m-d H:i:s"),
					'campaign_end_timestamp' => date("y-m-d H:i:s", strtotime('+10 years')),
					'campaign_end_message' => 'Campaign Ended');
				$campaign_id = $this->CI->campaign->add_campaign($campaign);
				
				$this->CI->load->library('app_component_lib');
				$default_app_component = array(
			 		'campaign_id' => $campaign_id,
			 		'invite' => array(
						'facebook_invite' => TRUE,
						'email_invite' => TRUE,
						'criteria' => array(
							'score' => 1,
							'maximum' => 5,
							'cooldown' => 4,
							'acceptance_score' => array(
								'page' => 10,
								'campaign' => 3
							)
						),
						'message' => array(
							'title' => 'Invite title',
							'text' => 'Invite text',
							'image' => 'https://localhost/assets/images/blank.png'
						)
			 		),
			 		'sharebutton' => array(
						'facebook_button' => TRUE,
						'twitter_button' => TRUE,
						'criteria' => array(
							'score' => 1,
							'maximum' => 5,
							'cooldown' => 4
						),
						'message' => array(
							'title' => 'Share title',
							'text' => 'Share text',
							'caption' => 'Share caption',
							'image' => 'https://localhost/assets/images/blank.png',
						)
					)
				);
				$this->CI->app_component_lib->add_campaign($default_app_component);
				//End : Add first 10-year campaign

				// response
				$response = array(	'status' => 'OK',
									'app_install_id' => $app_install_id,
									'app_install_secret_key' => $app_install_secret_key ,
									'campaign_id' => $campaign_id);
				return ($response);	
			}
			
		}
		
		log_message('error','This company doesn\'t have this app');
		return (array( 'error' => '300',
										'message' => 'application is not available for company'));
			
	}

	/**
	 * Request for page installation
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param page_id int SH id of a page
	 *	@param facebook_page_is string facebook id of a page
	 *	@param user_id int SH id of a user
	 *	@param user_facebook_id string facebook id of a user
	 *
	 *	@return status ok if install successfully
	 *	@author Wachiraphan C.
	 *
	 */
	function request_install_page($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL, $page_id = NULL,
		$facebook_page_id = NULL, $user_id = NULL, $user_facebook_id = NULL){
	
		// check parameter
		if(!($app_id) || !($app_install_id) || !($app_secret_key) || !($app_install_secret_key) || (!$page_id && !$facebook_page_id) || (!$user_id && !$user_facebook_id ) ){
			log_message('error','Missing parameters (app_id, app_secret_key, app_install_id, app_install_secret_key, page_id/facebook_page_id, user_id/user_facebook_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, page_id/facebook_page_id, user_id/user_facebook_id)'));
		}
		
		if(!$user_id){
			$this->CI->load->model('user_model','user');
			$user_id = $this->CI->user->get_user_id_by_user_facebook_id($user_facebook_id);
		}
		
		$this->CI->load->model('Session_model','Session');
		if(!$this->CI->Session->get_session_id_by_user_id($user_id)){
			
			log_message('error',"User #{$user_id} has no session");
			return (array( 'error' => '300',
									'message' => 'user session error, please login through platform'));
		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->CI->load->model('Installed_apps_model', 'Installed_apps');
		$company_id = $this->CI->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		
		if(sizeof($company_id)==0){
			return (array( 'error' => '500',
									'message' => 'invalid company_id'));
						
		}
		
		$company_id = $company_id['company_id'];
		
		$this->CI->load->model('Page_model', 'Page');
		
		if(!$page_id){
			$page_id = $this->CI->Page->get_page_id_by_facebook_page_id($facebook_page_id);
		}
		
		if($page = $this->CI->Page->get_page_profile_by_page_id($page_id)){
			// add app to page = new page_apps
			if($page['company_id']==$company_id){
					
				$this->CI->Installed_apps->update_page_id($app_install_id, $page_id);
				
				//Update latest installed app install id in page
				$this->CI->Page->update_page_profile_by_page_id($page_id, array('page_app_installed_id' => $app_install_id));
				
			
										
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
		
		return ($response);
		
	}
	
	/**
	 * Request for user id
	 *
	 * @param user_facebook_id string id of an app
	 *
	 * @return user_id
	 * @author Wachiraphan C.
	 *
	 */
	function request_user_id($user_facebook_id = NULL){
		if(!($user_facebook_id)){
			log_message('error','Missing parameter (user_facebook_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: user_facebook_id)'));
		}
		
		$this->CI->load->model('User_model', 'User');
		$user_id = $this->CI->User->get_user_id($user_facebook_id);
		
		if($user_id){
			$response = array(	'status' => 'OK',
							'user_id' => $user_id['user_id']);
		} else {
			log_message('error','user_id not found');
			$response = array(	'error' => '200');
		}

		return ($response);
	}

	/**
	 * Request for user facebook id
	 *
	 *	@param user_id
	 *
	 *	@return user_facebook_id
	 *	@author Wachiraphan C.
	 *
	 */
	function request_user_facebook_id($user_id = NULL){
		if(!($user_id)){
			log_message('error','Missing parameter (user_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: user_id)'));
		}
		
		$this->CI->load->model('User_model', 'User');
		$user_facebook_id = $this->CI->User->get_user_facebook_id_by_user_id($user_id);
		
		if($user_facebook_id){
			$response = array(	'status' => 'OK',
							'user_facebook_id' => $user_facebook_id);
		} else {
			log_message('error','user_facebook_id not found');
			$response = array(	'error' => '200');
		}

		return ($response);
	}

	/**
	 * Request for facebook_page_id
	 *
	 *	@param page_id
	 *
	 *	@return facebook_page_id
	 *	@author Wachiraphan C.
	 *
	 */
	function request_facebook_page_id($page_id = NULL){
	
		if(!($page_id)){
			log_message('error','Missing parameter (page_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: page_id)'));

		}
		
		$this->CI->load->model('Page_model', 'Page');
		$facebook_page_id = $this->CI->Page->get_facebook_page_id_by_page_id($page_id);
		
		$response = array();
		if($page_id['page_id']!=null){
			$response = array(	'status' => 'OK',
							'facebook_page_id' => $facebook_page_id);
		}
		
		return ($response);
	}
	
	/**
	 * Request for SH page id
	 *
	 *	@param facebook_page_id
	 *
	 *	@return SH page id
	 *	@author  Wachiraphan C.
	 *
	 */
	function request_page_id($facebook_page_id = NULL){
		
		if(!($facebook_page_id)){
			log_message('error','Missing parameter (facebook_page_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: facebook_page_id)'));

		}
		
		$this->CI->load->model('Page_model', 'Page');
		$page_id = $this->CI->Page->get_page_id_by_facebook_page_id($facebook_page_id);
		
		$response = array();
		if($page_id['page_id']!=null){
			$response = array(	'status' => 'OK',
							'page_id' => $page_id);
		}
		
		return ($response);
		
	}

	/**
	 * Request for log user data
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *
	 *	@return log status
	 *	@author Wachiraphac C.
	 *
	 */
	function request_log_user($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL, $user_id = NULL,
		$user_facebook_id = NULL, $campaign_id = NULL, $action = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_id && !$user_facebook_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)'));
		}
				
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		// log user
		$this->CI->load->model('User_apps_model', 'User_apps');
		$this->CI->load->model('User_model', 'User');
		
		if(!$user_id){
			$user_id = $this->CI->User->get_user_id_by_user_facebook_id($user_facebook_id);
		}else if(!$this->CI->User_apps->check_exist($user_id, $app_install_id)){ // if not exist user, create it
			$this->CI->User_apps->add_new($user_id, $app_install_id);
			
			$this->CI->load->library('audit_lib');
			$action_id = $this->CI->socialhappen->get_k('audit_action','User Register App');
			$this->CI->audit_lib->add_audit(
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
			
			$this->CI->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>$app_install_id, 'app_id'=>$app_id);
			$stat_increment_result = $this->CI->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
		}
		
		// update user last seen
		$this->CI->User_apps->update_user_last_seen($user_id, $app_install_id);
		$this->CI->User->update_user_last_seen($user_id);
				
		if(!$action){ //User default action if not specified
			$action = $this->CI->socialhappen->get_k('audit_action', 'User Visit');
		}
		$this->CI->load->model('installed_apps_model','installed_apps');
		$app = $this->CI->installed_apps->get_app_profile_by_app_install_id($app_install_id);
		$company_id = $app['company_id'];
		$this->CI->load->model('Audit_action_type_model', 'Audit_action_type');
		$audit_auction_type = $this->CI->Audit_action_type->get_audit_action_by_type_id($action);
		$action_text = $audit_auction_type['audit_action_name'];
		$this->CI->load->library('audit_lib');
		
		$result = $this->CI->audit_lib->add_audit(
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
			return ($response);
		
		}
	
		$this->CI->load->library('achievement_lib');
		$info = array('action_id'=> $action, 'app_install_id'=>$app_install_id, 'page_id' =>issetor($app['page_id']));
		if($campaign_id){
			$info['campaign_id'] = $campaign_id;
		}
		$result = $this->CI->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, 1);
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
		
		return ($response);
		
	}

	/**
	 * Request for user authentication
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param user_id int SH id of a user
	 *	@param user_facebook_id string facebook id of a user
	 *
	 *	@return status OK if user exists
	 *	@author Wachiraphan C.
	 *
	 */
	function request_authenticate($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL, $user_id = NULL,
		$user_facebook_id = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || (!$user_id && !$user_facebook_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, user_id/user_facebook_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, user_id/user_facebook_id)'));

		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// get company_id
		$this->CI->load->model('Installed_apps_model', 'Installed_apps');
		$app = $this->CI->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		
		if(sizeof($app)!=0){
			$company_id = $app['company_id'];
		}else{
			log_message('error','app not found');
			return (array( 'error' => '250',
									'message' => 'invalid app'));

		}
		
		// authenticate user with $company_id and $user_facebook_id
		if(!$user_id){
			$this->CI->load->model('user_model','user');
			$user_id = $this->CI->user->get_user_id_by_user_facebook_id($user_facebook_id);
		} else if(!$this->_authenticate_user($company_id, $user_id)){
			return;
		}
		
		return (array( 	'status' => 'OK',
									'message' => 'authenticated'));
	}

	/**
	 * Request for current user session status
	 *
	 *	@param user_id int SH id of a user
	 *	@param user_facebook_id string facebook id of a user
	 *
	 *	@return status and session id
	 *	@author  Wachiraphan C.
	 *
	 */
	function request_user_session($user_id = NULL, $user_facebook_id = NULL){
		
		if(!$user_id && !$user_facebook_id){
			log_message('error','Missing parameter (user_id/user_facebook_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: user_id/user_facebook_id'));

		}
		
		if(!$user_id){
			$this->CI->load->model('user_model','user');
			$user_id = $this->CI->user->get_user_id_by_user_facebook_id($user_facebook_id);
		}
		
		$this->CI->load->model('Session_model','Session');
		$session_id = $this->CI->Session->get_session_id_by_user_id($user_id);
		
		if($session_id){
			$response = array(	'status' => 'OK',
							'session_id' => $session_id);
		}

		return ($response);
	}
	
	/**
	 * Request for user information
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param user_id int SH id of a user
	 *	@param user_facebook_id string facebook id of a user
	 *
	 *	@return array of user data
	 *	@author Manassarn M.
	 *
	 */
	function request_user($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL, $user_id = NULL,
		$user_facebook_id = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_facebook_id && !$user_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)'));
			
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
		$this->CI->load->model('User_model', 'User');
		if(!$user_facebook_id){
			$user_facebook_id = $this->CI->User->get_user_facebook_id_by_user_id($user_id);
		}
		
		if(!$this->CI->User->check_exist($user_facebook_id)){
			
			$response['message'] = 'User not found';
		} else {
			$user = $this->CI->User->get_user_profile_by_user_facebook_id($user_facebook_id);
			$response['user_id'] = $user['user_id'];
			$response['user_first_name'] = $user['user_first_name'];
			$response['user_last_name'] = $user['user_last_name'];
			$response['user_email'] = $user['user_email'];
			//TODO: more fields
		}
		
		return ($response);
	}

	/**
	 * Request for users of a page
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *
	 *	@return array of users data
	 *	@author Manassarn M.
	 *
	 */
	function request_page_users($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL, $page_id = NULL,
		$facebook_page_id = NULL){
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$page_id && !$facebook_page_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, page_id/facebook_page_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, page_id/facebook_page_id)'));

		}
				
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->CI->load->model('installed_apps_model','installed_apps');
		$app = $this->CI->installed_apps->get_app_profile_by_app_install_id($app_install_id);
		
		
		if(!$page_id){
			$this->CI->load->model('page_model','Page');
			$page_id = $this->CI->Page->get_page_id_by_facebook_page_id($facebook_page_id);
		}
		
		if(issetor($app['page_id'])!=$page_id){
			log_message('error','page_id mismatch');
			return (array( 'error' => '600',
									'message' => 'invalid page_id'));

		}
		
		$response = array('status' => 'OK');
		
		$users = array(); //id => value
		$apps = $this->CI->installed_apps->get_installed_apps_by_page_id($page_id);
		$this->CI->load->model('user_apps_model','user_apps');
		foreach($apps as $app){
			$app_install_id = $app['app_install_id'];
			if($app_users = $this->CI->user_apps->get_app_users_by_app_install_id($app_install_id)){
				foreach($app_users as $app_user){
					if(!isset($users[$app_user['user_id']])){
						$users[$app_user['user_id']] = $app_user;
					}
				}
			}
		}
		$response['page_users'] = array_values($users);
		return ($response);
		
	}

	/**
	 * Request for application's user
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *
	 *	@return array of app users
	 *	@author Manassarn M.  
	 *
	 */
	function request_app_users($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));

		}
		
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		// authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$response = array('status' => 'OK');
		
		$this->CI->load->model('user_apps_model','user_apps');
		if(!$app_users = $this->CI->user_apps->get_app_users_by_app_install_id($app_install_id)){
			$response['message'] = 'User / page not found';
			$response['app_users'] = array();
		} else {
			$response['app_users'] = $app_users;
		}
		return ($response);

	}
	
	/**
	 * Request to add new limit to service list
	 *
	 *	@param user_id int id of a user
	 *	@param action_no int any key number to be added
	 *	@param app_install_id int install id of an app
	 *	@param campaign_id string campaign id to be added
	 *
	 *	@return status OK if sucessfully added
	 *	@author Wachiraphan C.
	 *
	 */
	function request_add_limit_service($user_id = NULL, $action_no = NULL, 
		$app_install_id = NULL, $campaign_id = NULL){
			
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
	
		$this->CI->load->library('audit_stat_limit_lib');
		$result = $this->CI->audit_stat_limit_lib->add($user_id, 
										$action_no,
										$app_install_id,
										$campaign_id);
		if(!$result) {
			log_message('error','audit_stat_limit_lib failed');
			$response = array('status' => 'Error');
		} else {
			$response = array('status' => 'OK');
		}
		return ($response);
		
	}

	/**
	 * Request for counting of limit in service list
	 *
	 *	@param user_id int id of a user
	 *	@param action_no int any key number to be added
	 *	@param app_install_id int install id of an app
	 *	@param campaign_id string campaign id to be added
	 *	@param back_time_interval int period of time
	 *
	 *	@return number of times that limit has been added to service list
	 *	@author Wachiraphan C.
	 *
	 */
	function request_count_limit_service($user_id = NULL, $action_no = NULL, 
		$app_install_id = NULL, $campaign_id = NULL, $back_time_interval = NULL){
			
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
		
		$this->CI->load->library('audit_stat_limit_lib');
		$result = $this->CI->audit_stat_limit_lib->count($user_id,
													$action_no,
													$app_install_id,
													$campaign_id,
													$back_time_interval);
		
		
		$response = array(
						'status' => 'OK',
						'count' => $result 			
						);
		return ($response);
		
	}

	/**
	 * Request for application navigation bar
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param user_facebook_id string facebook id of a user
	 *	@param user_id int SH id of a user
	 *
	 *	@return array of bar's assets
	 *	@author Manassarn M. 
	 *
	 */
	function bar($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL,
		$user_facebook_id = NULL, $user_id = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key)'));

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
		$response['html'] = $this->CI->socialhappen->get_bar($data);
		$response['css'] = base_url() . 'assets/css/common/api_app_bar.css';
		
		return ($response);
	}

	/**
	 * Request for app get started
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param user_facebook_id string facebook id of a user
	 *	@param user_id int SH id of a user
	 *
	 *	@return get started's assets
	 *	@author Weerapat P.
	 *
	 */
	function get_started($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL,
		$user_facebook_id = NULL, $user_id = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_facebook_id && !$user_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)');
			echo (array( 'error' => '100',
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
		$this->CI->load->model('Installed_apps_model', 'app');
		$app = $this->CI->app->get_app_profile_by_app_install_id($app_install_id);
		
		$data = array(
			'app_install_id' => $app_install_id,
			'page_id' => $app['page_id'],
			'user_id' => $user_id,
			'user_facebook_id' => $user_facebook_id,
			'view' => 'app_get_started'
		);
		
		$response = array('status' => 'OK');
		$response['html'] = $this->CI->socialhappen->get_setting_template($data);
		return ($response);
		
	}

	/**
	 * Request for app setting template
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param user_facebook_id string facebook id of a user
	 *	@param user_id int SH id of a user
	 
	 *	@return setting template's assets
	 *	@author Weerapat P.
	 *
	 */
	function setting_template($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL,
		$user_facebook_id = NULL, $user_id = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$user_facebook_id && !$user_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_id/user_facebook_id)');
			echo (array( 'error' => '100',
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
		$this->CI->load->model('Installed_apps_model', 'app');
		$app = $this->CI->app->get_app_profile_by_app_install_id($app_install_id);
		
		$data = array(
			'app_install_id' => $app_install_id,
			'page_id' => $app['page_id'],
			'user_id' => $user_id,
			'user_facebook_id' => $user_facebook_id
		);
		
		$response = array('status' => 'OK');
		$response['html'] = $this->CI->socialhappen->get_setting_template($data);
		return ($response);
	}

	/**
	 * Request for recent notifications 
	 *
	 *	@param user_id int SH id of a user
	 *	@param limit int amount of items
	 *
	 *	@return list of notifications 
	 *	@author  
	 *
	 */
	function show_notification($user_id = NULL, $limit = NULL){
		
		// @TODO: validate user_id here
		
		$this->CI->load->library('notification_lib');
		$limit = !$limit ? 10 : $limit;
		if(!$user_id){
		  $notification_list = array();
		}else{
		  $notification_list = $this->CI->notification_lib->lists($user_id, $limit, 0);
		  $notification_list = !$notification_list ? array() : $notification_list;
		  for ($i = 0; $i < count($notification_list); $i++) { 
			$notification_list[$i]['_id'] = (string) $notification_list[$i]['_id']; 
		  }
		}
		return (array('notification_list' => $notification_list));
		
	}
	
	/**
	 * Request for recent notifications 
	 *
	 *	@param user_id int SH id of a user
	 *	@param notification_list
	 *
	 *	@return ok if notifications are presented
	 *	@author  
	 *
	 */
	function read_notification($user_id = NULL, $notification_list = NULL){
	
		$notification_list = !$notification_list ? array() :
		 json_decode($notification_list);
		
		// @TODO: validate user_id here
		
		if(!$user_id || count($notification_list) == 0){
		  return (array('result' => 'OK', 'read' => 0));
		}else{
		  $this->CI->load->library('notification_lib');
		  $result = $this->CI->notification_lib->read($user_id, $notification_list);
		  
		  return (array('result' => $result ? 'OK' : 'FAIL'));
		}
		
	}

	/**
	 * Request for adding achievement info
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@achievement_infos string base64 encoded of achievement info
	 *
	 *	@return ok if sucessfully added
	 *	@author  Manassarn M.
	 *	@author Weerapat P. - Add page_id in achievement_info
	 *
	 */
	function request_add_achievement_infos($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL, $achievement_infos = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || !($achievement_infos)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, achievement_infos)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, achievement_infos)'));
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

		//get page_id
		$this->CI->load->model('page_model');
		$page = $this->CI->page_model->get_page_profile_by_app_install_id($app_install_id);

		$this->CI->load->library('achievement_lib');
		foreach($achievement_infos as $achievement_info)
		{
			//Add page_id
			if(isset($page['page_id'])) {
				$achievement_info['info']['page_id'] = $page['page_id'];
			}

			$this->CI->achievement_lib->add_achievement_info(
				$app_id, $app_install_id,
				$achievement_info['info'], $achievement_info['criteria']);
		}
		
		return ($response);
	}

	/**
	 * Request for SH login
	 *
	 *	@param user_facebook_id string facebook id of a user
	 *
	 *	@return status ok if logged in through facebook
	 *	@author  Manassarn M.
	 *
	 */
	function request_login($user_facebook_id = NULL){
	
		if(!$user_facebook_id){
			log_message('debug','Missing parameter (user_facebook_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: user_facebook_id)'));

		}
		$this->CI->load->model('User_model', 'User');
		$user = $this->CI->User->get_user_profile_by_user_facebook_id($user_facebook_id);
		$user_id_check = $user['user_id'];
		if(issetor($user['user_facebook_access_token'])){
			$access_token = $user['user_facebook_access_token'];
			$this->CI->FB->setAccessToken($access_token);
			$user_id = $this->CI->socialhappen->login();
			if($user_id && $user_id == $user_id_check){
				$response = array('status' => 'OK', 'user_id' => $user_id);
			} else {
				$response = array('status' => 'ERROR', 'message' => 'User not found');
				$this->CI->socialhappen->logout();
			}
		} else {
			$response = array('status' => 'ERROR', 'message' => 'Not connected with facebook');
		}
		return ($response);
		
	}

	/**
	 * Request for facebook tab url
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param facebook_page_id string facebook id of a page
	 *	@param page_id int SH id of a page
	 *
	 *	@return facebook tab url
	 *	@author  Manassarn M.
	 *
	 */
	function request_facebook_tab_url($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL,
		$facebook_page_id = NULL, $page_id = NULL){
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key)');
			return (array( 'error' => '100',
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
			$this->CI->load->model('installed_apps_model','installed_app');
			if(!$app = $this->CI->installed_app->get_app_profile_by_app_install_id($app_install_id)){
				$response['message'] = 'App not found';
			} else if (!$facebook_tab_url = $app['facebook_tab_url']){
				$response['message'] = 'Facebook tab url not found in this app';
			} else {
				$response['facebook_tab_url'] = $facebook_tab_url;
			}
		} else { //Request for facebook tab url for page
			$this->CI->load->model('page_model','page');
			if(!$page_id){
				$page_id = $this->CI->page->get_page_id_by_facebook_page_id($facebook_page_id);
			}
			if(!$page = $this->CI->page->get_page_profile_by_page_id($page_id)){
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
		
		return ($response);
		
	}
	
	/**
	 * Request for creation of invite
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param user_facebook_id
	 *	@param target_facebook_id
	 *	@param campaign_id
	 *	@param invite_type int 1 for private, 2 for public 
	 *	@param facebook_page_id
	 *
	 *	@return invite id (if success) 
	 *	@author  Wachiraphan C.
	 *
	 */
	function request_create_invite($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL,
		$user_facebook_id = NULL, $target_facebook_id = NULL,
		$campaign_id = NULL, $invite_type = NULL, $facebook_page_id = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) ||
			!($user_facebook_id) || !($campaign_id) || !($facebook_page_id)
		){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, user_facebook_id, campaign_id, invite_type, facebook_page_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, user_facebook_id, campaign_id, invite_type, facebook_page_id)'));

		}
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		//authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->CI->load->library('invite_component_lib');
		
		$response = array();
		
		$add_invite_result = $this->CI->invite_component_lib->add_invite($campaign_id,$app_install_id,$facebook_page_id,
												$invite_type,$user_facebook_id,$target_facebook_id);
		$invite_key = issetor($add_invite_result['data']['invite_key']);
		
		if($invite_key){				
			$response['invite_key'] = $invite_key;
			$response['status'] = 'OK';
		} else {
			$response['status'] = 'ERROR';
			$error_message = issetor($add_invite_result['error']);
			if($error_message)
				$response['message'] = $error_message;
		}
		
		return ($response);
		
	}
	
	/**
	 * Request for accept an invite
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param invite_key
	 *	@param target_facebook_id
	 *
	 *	@return status ok if success 
	 *	@author  Wachiraphan C.
	 *
	 */
	function request_accept_invite($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL,
		$invite_key = NULL, $target_facebook_id = NULL){
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) ||
			!($invite_key) || !($target_facebook_id) 
		){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, invite_key, target_facebook_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, invite_key, target_facebook_id)'));
			
		}
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		//authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->CI->load->library('invite_component_lib');
		
		$response = array();
		
		$accept_result = $this->CI->invite_component_lib->reserve_invite($invite_key, $target_facebook_id);
			
		if(isset($accept_result['error'])){
			$response['status'] = 'ERROR';
			$response['message'] = $accept_result['error'];
		} else {
			$response['status'] = 'OK';
		}
		
		return ($response);
		
		
	}

	/**
	 * Request for list of invite
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param campaign_id
	 *	@param facebook_page_id
	 *	@param user_facebook_id
	 *	
	 *	@return array of invites by criteria
	 *	@author  Wachiraphan C.
	 *
	 */
	function request_invite_list($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL,
		$campaign_id = NULL, $facebook_page_id = NULL,
		$user_facebook_id = NULL){
		
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) ||
			!($facebook_page_id) || !($user_facebook_id) 
		){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key, facebook_page_id, user_facebook_id)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, facebook_page_id, user_facebook_id)'));

		}
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){

		}
		
		//authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->CI->load->library('invite_component_lib');
		
		$response = array();
		
		$criteria['app_install_id'] = (int) $app_install_id;
		$criteria['user_facebook_id'] = $user_facebook_id;
		$criteria['facebook_page_id'] = $facebook_page_id;
		
		if($campaign_id){
			$criteria['campaign_id'] = (int) $campaign_id;
		}
		
		$invites = $this->CI->invite_component_lib->list_invite($criteria);
		
		if(isset($invites)){
			$response['invite_list'] = $invites;
			$response['status'] = 'OK';
		} else {
			$response['status'] = 'ERROR';
		}
		
		return ($response);
		
	}
	
	/**
	 * Request for current campaign
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *
	 *	@return array of last campaign
	 *	@author  Wachiraph C.
	 *
	 */
	function request_current_campaign($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, facebook_page_id, user_facebook_id)'));

		}
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		//authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
		
		$this->CI->load->library('campaign_lib');
		
		$response = array();
		
		$campaign = $this->CI->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
		
		if(isset($campaign['campaign_id'])){
		
			$response['status'] = 'OK';
			$response = $response + $campaign;
			
		} else {
			$response['status'] = 'ERROR';
		}
		
		return ($response);
		
	}

	/**
	 * Request for user classes
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *	@param page_id
	 *	@param facebook_page_id
	 *
	 *	@return array of user class in a page
	 *	@author  Manassarm M.
	 *
	 */
	function request_user_classes($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL, $page_id = NULL, $facebook_page_id = NULL){
		if(!($app_id) || !($app_secret_key) || !($app_install_id) || !($app_install_secret_key) || (!$page_id && !$facebook_page_id)){
			log_message('error','Missing parameter (app_id, app_secret_key, app_install_id, app_install_secret_key)');
			return (array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, app_install_id, app_install_secret_key, facebook_page_id)'));

		}
		
		if(!$this->_authenticate_app($app_id, $app_secret_key)){
			return;
		}
		
		//authenticate app install with $app_install_id and $app_install_secret_key
		if(!$this->_authenticate_app_install($app_install_id, $app_install_secret_key)){
			return;
		}
	
		if(!$page_id){
			$this->CI->load->model('Page_model','Page');
			$page_id = $this->CI->Page->get_page_id_by_facebook_page_id($facebook_page_id);
		}

		$this->CI->load->model('app_component_page_model');
		$user_classes = $this->CI->app_component_page_model->get_classes_by_page_id($page_id);
		
			
		if($user_classes){
			//sort like a boss
			$sorted_user_classes = array();
			
			usort($user_classes, 
					function ($a, $b){
							if($a['invite_accepted'] > $b['invite_accepted'])
								return 1;
							else if($a['invite_accepted'] < $b['invite_accepted'])
								return -1;
								
							return 0;
					}
				);
			
			$response['status'] = 'OK';
			$response['data'] = $user_classes;
			
		} else {
			$response['status'] = 'ERROR';
		}
		return $response;
	}
	
	
		private	
	
	function _generate_app_install_secret_key($company_id, $app_id){
		return md5($this->_generate_random_string());
	}
	
	function _authenticate_app($app_id, $app_secret_key){
		// authenticate app with $app_id and $app_secret_key
		$this->CI->load->model('App_model', 'App');
		$app = $this->CI->App->get_app_by_app_id($app_id);
		if($app != NULL && $app['app_secret_key']== $app_secret_key){
			return TRUE;
		} else {
			log_message('error','app_secret_key mismatch, app authenticate failed');
			/* return (array( 
				'error' => '200',
				'message' => 'invalid app_secret_key')
			); */
			return FALSE;
		}
	}
	
	function _authenticate_app_install($app_install_id, $app_install_secret_key){
		// authenticate app with $app_id and $app_install_secret_key
		$this->CI->load->model('Installed_apps_model', 'Installed_apps');
		$app = $this->CI->Installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if($app != NULL){
			return ($app['app_install_secret_key'] == $app_install_secret_key);
		} else {
			log_message('error','app_install_secret_key mismatch, app authenticate failed');
			/* return (array( 
				'error' => '500',
				'message' => 'invalid app_install_secret_key')
			); */
			return FALSE;
		}
	}
	
	function _authenticate_user($company_id, $user_id){
		// authenticate user with $company_id and $user_id
		$this->CI->load->model('User_companies_model', 'User_companies');
		$company_admin_list_query = $this->CI->User_companies->get_user_companies_by_company_id($company_id, 1000, 0);
		$company_admin_list = array();
		
		foreach ($company_admin_list_query as $admin) {
			$company_admin_list[] = $admin['user_id'];
		}
		if(in_array($user_id, $company_admin_list)){
			return TRUE;
		} else {
			log_message('error',"User #{$user_id} has no permission in company #{$company_id}");
			/* return (array( 
				'error' => '300',
				'message' => 'you have no permission to install app on this company')
			); */
			return FALSE;
		}
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
/* End of file api_lib.php */
/* Location: ./application/libraries/api_lib.php */