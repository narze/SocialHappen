<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Test class for external API
* baware of chaining effects
* @author Wachiraph C.
*/

/*set these customized parameters first*/
define('APP_ID','1');
define('COMPANY_ID','1');
define('APP_SECRET_KEY','11111111111111111111111111111111');
define('USER_ID','3');
define('USER_FACEBOOK_ID','631885465');
define('FACEBOOK_PAGE_ID','4321');
define('PAGE_ID','1');
	
class Api_test extends CI_Controller {

	private $app_install_id;
	private $app_install_secret_key;
	private $page_id;
	private $campaign_id;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
	}
	
	function __destruct(){
		echo $this->unit->report();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	/**
	 * DEPRECATED : api does not have index()
	 * Tests output data
	 * @author Wachiraph C.
	 * @author Manassarn M.
	 */
	function index_test(){
		// ob_start();
		// require(__DIR__.'/../api.php');
		// $api = new Api();
		
		// $data = $app->index(1);
		// ob_end_clean();
		// $this->unit->run($data,'is_array','$data');
		// $this->unit->run($data['app_install_id'], 'is_int', '$app_install_id');
		// $this->unit->run(count($data) == 1, 'is_true', 'number of passed variables');
		
		// $data = $app->index();
		// ob_end_clean();
		// $this->unit->run($data,'is_null','$data');
		// $this->unit->run($data['app_install_id'], 'is_null', '$app_install_id');
		// $this->unit->run(count($data) == 0, 'is_true', 'number of passed variables');
	}
	
	/**
	 * Tests request_platform_navigation()
	 * @author Wachiraph C.
	 */
	function request_user_session_test(){
		$app = array(	
						'user_id' => USER_ID
					);
					
		$content = $this->curl->simple_get(base_url().'api/request_user_session', $app);
		
		$content = json_decode($content, TRUE);
		$this->unit->run($content, 'is_array', 'request_user_session()');
		$this->unit->run(@$content['status']=='OK','is_true','user session established');
	}
	
	/**
	 * Tests request_install_app()
	 * @author Wachiraph C.
	 */
	function request_install_app_test(){
		$app = array(
						'app_secret_key' => APP_SECRET_KEY,
						'company_id' => COMPANY_ID,
						'app_id' => APP_ID,
						'user_id' => USER_ID
					);
					
		$content = $this->curl->simple_get(base_url().'api/request_install_app', $app);
		$content = json_decode($content, TRUE);
		
		$this->unit->run($content, 'is_array', 'request_install_app()');
		$this->unit->run(@$content['app_install_id'],'is_int','app_install_id');
		$this->unit->run(@$content['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->app_install_id = @$content['app_install_id'];
		$this->app_install_secret_key = @$content['app_install_secret_key'];
		
	}
	
	/**
	 * Tests request_install_page()
	 * @author Wachiraph C.
	 */
	function request_install_page_test(){
		$app = array(
						'app_secret_key' => APP_SECRET_KEY,
						'app_id' => APP_ID,
						'app_install_id' => $this->app_install_id,
						'app_install_secret_key' => $this->app_install_secret_key,
						'page_id' => PAGE_ID
					);
					
		$content = $this->curl->simple_get(base_url().'api/request_install_page', $app);
		$content = json_decode($content, TRUE);
		
		$this->unit->run($content, 'is_array', 'request_install_page()');
		$this->unit->run(@$content['status']=='OK','is_true','page installed');
	}
	
	/**
	 * Tests request_user_id()
	 * @author Wachiraph C.
	 */
	function request_user_id_test(){
		$app = array(
						'user_facebook_id' => USER_FACEBOOK_ID
					);
		
		$content = $this->curl->simple_get(base_url().'api/request_user_id', $app);
		$content = json_decode($content, TRUE);
		
		$this->unit->run($content, 'is_array', 'request_user_id()');
		$this->unit->run(@$content['user_id'],'is_string','user id');
	}
	
	/**
	 * Tests request_page_id()
	 * @author Wachiraph C.
	 */
	function request_page_id_test(){
		$app = array(
						'facebook_page_id' => FACEBOOK_PAGE_ID
					);
					
		$content = $this->curl->simple_get(base_url().'api/request_page_id', $app);
		$content = json_decode($content, TRUE);
		
		$this->unit->run($content, 'is_array', 'request_page_id()');
		$this->unit->run(@$content['page_id'],'is_string','page id');
	}
	
	/**
	 * Tests request_register_user()
	 * @author Wachiraph C.
	 */
	function request_register_user_test(){
		$app = array(
						'app_id' => APP_ID,
						'app_secret_key' => APP_SECRET_KEY,
						'app_install_id' => $this->app_install_id,
						'app_install_secret_key' => $this->app_install_secret_key,
						'user_facebook_id' => rand(1,100000000),
					);
					
		$content = $this->curl->simple_get(base_url().'api/request_register_user', $app);
		
		$content = json_decode($content, TRUE);
		$this->unit->run($content, 'is_array', 'request_register_user()');
		$this->unit->run(@$content['user_id'],'is_int','user registered');
		$this->unit->run(@$content['User_apps']=='added','is_true','user_apps added');
	}
	
	/**
	 * Tests request_log_user()
	 * @author Wachiraph C.
	 */
	function request_log_user_test(){
		$app = array(
						'app_id' => APP_ID,
						'app_secret_key' => APP_SECRET_KEY,
						'app_install_id' => $this->app_install_id,
						'app_install_secret_key' => $this->app_install_secret_key,
						'user_id' => USER_ID,
						'action' => '1'
					);
					
		$content = $this->curl->simple_get(base_url().'api/request_log_user', $app);
		
		$content = json_decode($content, TRUE);
		$this->unit->run($content, 'is_array', 'request_log_user()');
		$this->unit->run(@$content['action_text']=='view','is_true','action text');
		$this->unit->run(@$content['status']=='OK','is_true','user logged');
	}
	
	/**
	 * Tests request_log_user()
	 * @author Wachiraph C.
	 */
	function request_config_log_test(){
		$app = array(
						'app_id' => APP_ID,
						'app_secret_key' => APP_SECRET_KEY,
						'app_install_id' => $this->app_install_id,
						'app_install_secret_key' => $this->app_install_secret_key,
						'user_id' => USER_ID
					);
					
		$content = $this->curl->simple_get(base_url().'api/request_config_log', $app);
		
		$content = json_decode($content, TRUE);
		$this->unit->run($content, 'is_array', 'request_config_log()');
		$this->unit->run(@$content['action_text']=='save config','is_true','action text');
		$this->unit->run(@$content['status']=='OK','is_true','saving config logged');
	}
	
	/**
	 * Tests request_authenticate()
	 * @author Wachiraph C.
	 */
	function request_authenticate_test(){
		$app = array(
						'app_id' => APP_ID,
						'app_secret_key' => APP_SECRET_KEY,
						'app_install_id' => $this->app_install_id,
						'user_id' => USER_ID,
					);
					
		$content = $this->curl->simple_get(base_url().'api/request_authenticate', $app);
		
		$content = json_decode($content, TRUE);
		$this->unit->run($content, 'is_array', 'request_authenticate()');
		$this->unit->run(@$content['status']=='OK','is_true','user is authenticated');
	}
	
	/**
	 * Tests request_platform_navigation()
	 * @author Wachiraph C.
	 */
	function request_platform_navigation_test(){
		$app = array(
						'app_id' => APP_ID,
						'app_secret_key' => APP_SECRET_KEY,
						'app_install_id' => $this->app_install_id,
						'app_install_secret_key' => $this->app_install_secret_key,
						'user_id' => USER_ID,
					);
					
		$content = $this->curl->simple_get(base_url().'api/request_platform_navigation', $app);
		
		$content = json_decode($content, TRUE);
		$this->unit->run($content, 'is_array', 'request_platform_navigation()');
		$this->unit->run(@$content['status']=='OK','is_true','platform navigation is loaded');
	}
	
	/**
	 * Tests request_create_campaign()
	 * @author Wachiraph C.
	 */
	function request_create_campaign_test(){
		$app = array(	
						'app_id' => APP_ID,
						'app_secret_key' => APP_SECRET_KEY,
						'app_install_id' => $this->app_install_id,
						'app_install_secret_key' => $this->app_install_secret_key,
						'user_id' => USER_ID,
						'campaign_name' => 'Test Campaign',
						'campaign_start_timestamp' => '2011-05-19 00:00:00',
						'campaign_end_timestamp' => '2012-05-19 00:00:00',
						'campaign_detail' => 'Test Campaign Detail',
						'campaign_status_id' => 1
					);
				
		$content = $this->curl->simple_get(base_url().'api/request_create_campaign', $app);
		
		$content = json_decode($content, TRUE);
		$this->unit->run($content, 'is_array', 'request_create_campaign()');
		$this->unit->run(@$content['campaign_id'],'is_int','campaign_id');
		$this->campaign_id = @$content['campaign_id'];
	}
	
	/**
	 * Tests request_campaign_list()
	 * @author Wachiraph C.
	 */
	function request_campaign_list_test(){
		$app = array(	
						'app_id' => APP_ID,
						'app_secret_key' => APP_SECRET_KEY,
						'app_install_id' => $this->app_install_id,
						'app_install_secret_key' => $this->app_install_secret_key
					);
				
		$content = $this->curl->simple_get(base_url().'api/request_campaign_list', $app);
		$content = json_decode($content, TRUE);
		
		$this->unit->run($content, 'is_array', 'request_campaign_list()');
		$this->unit->run(@$content[0]['campaign_id']==$this->campaign_id,'is_true','last campaign_id in list');
		
	}
	
	/**
	 * Tests request_campaign_info()
	 * @author Wachiraph C.
	 */
	function request_campaign_info_test(){
		$app = array(	
						'app_id' => APP_ID,
						'app_secret_key' => APP_SECRET_KEY,
						'app_install_id' => $this->app_install_id,
						'app_install_secret_key' => $this->app_install_secret_key,
						'campaign_id' => $this->campaign_id
					);
				
		$content = $this->curl->simple_get(base_url().'api/request_campaign_info', $app);
		$content = json_decode($content, TRUE);
		
		$this->unit->run($content, 'is_array', 'request_campaign_list()');
		$this->unit->run(@$content['campaign_id'],'is_string','campaign_id');
		$this->unit->run(@$content['campaign_id']==$this->campaign_id,'is_true','last campaign_id');
	}
	
	/**
	 * Tests request_update_campaign()
	 * @author Wachiraph C.
	 */
	function request_update_campaign_test(){
		$app = array(	
						'app_id' => APP_ID,
						'app_secret_key' => APP_SECRET_KEY,
						'app_install_id' => $this->app_install_id,
						'app_install_secret_key' => $this->app_install_secret_key,
						'user_id' => USER_ID,
						'campaign_id' => $this->campaign_id,
						'campaign_name' => 'Changed campaign name'
					);
				
		$content = $this->curl->simple_get(base_url().'api/request_update_campaign', $app);
		$content = json_decode($content, TRUE);
		
		$this->unit->run($content, 'is_array', 'request_update_campaign()');
		$this->unit->run(@$content['campaign_id'],'is_string','campaign_id');
	}
	
}

/* End of file api_test.php */
/* Location: ./application/controllers/test/api_test.php */
