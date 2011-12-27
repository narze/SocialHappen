<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API controller
 * @author Wachiraph C. - revised Dec 2011
 */
class Api extends CI_Controller {

	function __construct(){
		header("Access-Control-Allow-Origin: *");
		parent::__construct();
		
		$this->load->library('api_lib');
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

		$response = $this->api_lib->request_install_app($app_id, $app_secret_key, $company_id, $user_id, $user_facebook_id);
		
		$this->_print_api_result($response);

				
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
		
		$response = $this->api_lib->request_install_page($app_id, $app_secret_key, $app_install_id, $app_install_secret_key,
															$page_id, $facebook_page_id, $user_id, $user_facebook_id);
		
		$this->_print_api_result($response);
	}

	/**
	 * Request for platform user's id using user's facebook id
	 * @author Wachiraph C.
	 */				
	function request_user_id(){
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		
		$response = $this->api_lib->request_user_id($user_facebook_id);
		
		$this->_print_api_result($response);
	}
	
	/**
	 * Request for platform user's facebook id using user's id 
	 * @author Weerapat P.
	 */				
	function request_user_facebook_id(){
		$user_id = $this->input->get('user_id', TRUE);
		
		$response = $this->api_lib->request_user_facebook_id($user_id);
		
		$this->_print_api_result($response);
	}
	
	/**
	 * Request for platform page's id using facebook page's id
	 * @author Wachiraph C.
	 */
	function request_page_id(){
		
		$facebook_page_id = $this->input->get('facebook_page_id', TRUE);
		
		$response = $this->api_lib->request_page_id($facebook_page_id);
		
		$this->_print_api_result($response);
	}
	
	/**
	 * Request for facebook page's id using  platform page's id
	 * @author Wachiraph C.
	 */
	function request_facebook_page_id(){
		$page_id = $this->input->get('page_id', TRUE);
		
		$response = $this->api_lib->request_facebook_page_id($page_id);
		
		$this->_print_api_result($response);
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
			
		$response = $this->api_lib->request_log_user($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, 
														$user_id, $user_facebook_id, $campaign_id, $action);
		
		$this->_print_api_result($response);
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
					
		$response = $this->api_lib->request_authenticate($app_id, $app_secret_key, $app_install_id, $user_id, $user_facebook_id);
		
		$this->_print_api_result($response);
	}

	/**
	 * Request user session on platform using user_id or user_facebook_id
	 * @author Wachiraph C. 
	 */
	function request_user_session(){
		$user_id = $this->input->get('user_id', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		
		$response = $this->api_lib->request_user_session($user_id, $user_facebook_id);
		
		$this->_print_api_result($response);
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

		$response = $this->api_lib->request_user($app_id, $app_secret_key, $app_install_id, $app_install_secret_key,
														$user_id, $user_facebook_id);
		
		$this->_print_api_result($response);
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
		
		$response = $this->api_lib->request_page_users($app_id, $app_secret_key, $app_install_id, $app_install_secret_key,
														$page_id, $facebook_page_id);
		
		$this->_print_api_result($response);
	}
	
	/**
	 * Request app users
	 * @author Manassarn M.
	 */
	function request_app_users(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_keyapp_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		//$user_facebook_id = $this->input->get('user_facebook_id', TRUE);
		//$page_id = $this->input->get('page_id', TRUE);
		
		$response = $this->api_lib->request_app_users($app_id, $app_secret_key, $app_install_id, $app_install_secret_key);
		
		$this->_print_api_result($response);
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
	
		$response = $this->api_lib->request_add_limit_service($user_id, $action_no, $app_install_id, $campaign_id);
		
		$this->_print_api_result($response);
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
		
		$response = $this->api_lib->request_count_limit_service($user_id, $action_no, $app_install_id, $app_install_id, $campaign_id, $back_time_interval);
		
		$this->_print_api_result($response);
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
		
		$response = $this->api_lib->bar($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, $user_facebook_id, $user_id);
		
		$this->_print_api_result($response);
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
		
		$response = $this->api_lib->get_started($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, $user_facebook_id, $user_id);
		
		$this->_print_api_result($response);
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
		
		$response = $this->api_lib->setting_template($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, $user_facebook_id, $user_id);
		
		$this->_print_api_result($response);
	}

  function show_notification(){
    $user_id = $this->input->get('user_id', TRUE);
    $limit = $this->input->get('limit', TRUE);
    
    $response = $this->api_lib->show_notification($user_id, $limit);
		
	$this->_print_api_result($response);
  }

  function read_notification(){
    $user_id = $this->input->get('user_id', TRUE);
    $notification_list = $this->input->get('notification_list', TRUE);
    
	$response = $this->api_lib->read_notification($user_id, $notification_list);
		
	$this->_print_api_result($response);
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
		
		$response = $this->api_lib->request_add_achievement_infos($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, $achievement_infos);
		
		$this->_print_api_result($response);
	}
	
	/**
	 * Request for socialhappen login
	 * @author Manassarn M.
	 */
	function request_login(){
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE);

		$response = $this->api_lib->request_login($user_facebook_id);
		
		$this->_print_api_result($response);
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
		
		$response = $this->api_lib->request_facebook_tab_url($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, $facebook_page_id, $page_id);
		
		$this->_print_api_result($response);
	}
	
	/**
	 * Request for creation of new invite
	 * @author Wachiraphan C.
	 */
	function request_create_invite(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE); 
		$target_facebook_id = $this->input->get('target_facebook_id', TRUE); 
		$campaign_id = $this->input->get('campaign_id', TRUE); 
		$invite_type = $this->input->get('invite_type', TRUE); //Optional
		$facebook_page_id = $this->input->get('facebook_page_id', TRUE); //Optional
		
		$response = $this->api_lib->request_create_invite($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, 
															$user_facebook_id, $target_facebook_id, $campaign_id, $invite_type, $facebook_page_id);
		
		$this->_print_api_result($response);
	}
	
	/**
	 * Request for acception of an invite
	 * @author Wachiraphan C.
	 */
	function request_accept_invite(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$invite_key = $this->input->get('invite_key', TRUE); 
		$target_facebook_id = $this->input->get('target_facebook_id', TRUE); 
		
		$response = $this->api_lib->request_accept_invite($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, $invite_key, $target_facebook_id);
		
		$this->_print_api_result($response);
		
	}
	
	/**
	 * Request for list of invites
	 * @author Wachiraphan C.
	 */
	function request_invite_list(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$campaign_id = $this->input->get('campaign_id', TRUE); //optional
		$facebook_page_id = $this->input->get('facebook_page_id', TRUE); 
		$user_facebook_id = $this->input->get('user_facebook_id', TRUE); 
		
		$response = $this->api_lib->request_invite_list($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, $campaign_id, $facebook_page_id, $user_facebook_id);
		
		$this->_print_api_result($response);
		
	}
	
	/**
	 * Request for current campaign of app_install_id
	 * @author Wachiraphan C.
	 */
	function request_current_campaign(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		
		$response = $this->api_lib->request_current_campaign($app_id, $app_secret_key, $app_install_id, $app_install_secret_key);
		
		$this->_print_api_result($response);
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

	/**
	 * Request page user classes
	 * @author Manassarn M.
	 */
	function request_user_classes(){
		$app_id = $this->input->get('app_id', TRUE);
		$app_secret_key = $this->input->get('app_secret_key', TRUE);
		$app_install_id = $this->input->get('app_install_id', TRUE);
		$app_install_secret_key = $this->input->get('app_install_secret_key', TRUE);
		$page_id = $this->input->get('page_id', TRUE);
		$facebook_page_id = $this->input->get('facebook_page_id', TRUE);

		$response = $this->api_lib->request_user_classes($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, $page_id, $facebook_page_id);

		$this->_print_api_result($response);
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
	
	function _print_api_result($result){
		if(issetor($result)){
			echo json_encode($result);
		}else{
			echo json_encode(
								array(
										'status' => 'ERROR',
										'message' => 'API error, please check your parameters and try again'
									)
							);
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

/* End of file api.php */
/* Location: ./application/controllers/api.php */