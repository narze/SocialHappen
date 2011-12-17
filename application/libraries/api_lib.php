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
	 *	@return array of 
	 *	@author Wachiraphan C.
	 *
	 */
	function request_install_app($app_id = NULL, $app_secret_key = NULL, 
		$company_id = NULL, $user_id = NULL, $user_facebook_id = NULL){
	
		if(!($app_id) || !($app_secret_key) || !($company_id) || (!$user_id && !$user_facebook_id) ){
			log_message('error','Missing parameters (app_id, app_secret_key, company_id, user_id/user_facebook_id)');
			return json_encode(array( 'error' => '100',
									'message' => 'invalid parameter, some are missing (need: app_id, app_secret_key, company_id, user_id/user_facebook_id)'));
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
				
				//Add first 10-year campaign
				$this->load->model('campaign_model','campaign');
				date_default_timezone_set('UTC');
				$campaign = array(
					'app_install_id' => $app_install_id,
					'campaign_name' => 'Campaign',
					'campaign_start_timestamp' => date("y-m-d H:i:s"),
					'campaign_end_timestamp' => date("y-m-d H:i:s", strtotime('+10 years')),
					'campaign_end_message' => 'Campaign Ended');
				$campaign_id = $this->campaign->add_campaign($campaign);
				//End : Add first 10-year campaign

				// response
				$response = array(	'status' => 'OK',
									'app_install_id' => $app_install_id,
									'app_install_secret_key' => $app_install_secret_key ,
									'campaign_id' => $campaign_id);
				echo json_encode($response);		
				return;				
			}
			
		}
		
		log_message('error','This company doesn\'t have this app');
		echo json_encode(array( 'error' => '300',
										'message' => 'application is not available for company'));
				return;
		
		return array();
	}

		/**
	 * Request for 
	 *
	 *	@param app_id int id of an app
	 *	@param app_secret_key string secret key of app
	 *	@param app_install_id int install id of an app
	 *	@param app_install_secret_key string install secret key of an app
	 *
	 *	@return array of 
	 *	@author  
	 *
	 */
	function request_($app_id = NULL, $app_secret_key = NULL, 
		$app_install_id = NULL, $app_install_secret_key = NULL){
	
		
		return array();
	}

	
}
/* End of file api_lib.php */
/* Location: ./application/libraries/api_lib.php */