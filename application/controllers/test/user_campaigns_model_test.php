<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_campaigns_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('user_campaigns_model','user_campaigns');
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
	 * Tests get_campaign_users_by_campaign_id()
	 * @author Manassarn M.
	 */
	function get_campaign_users_by_campaign_id_test(){
		$result = $this->user_campaigns->get_campaign_users_by_campaign_id(1);
		$this->unit->run($result,'is_array', 'get_campaign_users_by_campaign_id()');
		$this->unit->run($result[0]['user_id'],'is_string','user_id');
		$this->unit->run($result[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($result[0]['user_first_name'],'is_string','user_first_name');
		$this->unit->run($result[0]['user_last_name'],'is_string','user_last_name');
		$this->unit->run($result[0]['user_email'],'is_string','user_email');
		$this->unit->run($result[0]['user_image'],'is_string','user_image');
		$this->unit->run($result[0]['user_facebook_id'],'is_string','user_facebook_id');
		$this->unit->run($result[0]['user_register_date'],'is_string','user_register_date');
		$this->unit->run($result[0]['user_last_seen'],'is_string','user_last_seen');
		$this->unit->run(count($result[0]) == 9,'is_true', 'number of column');
	}
	
	/**
	 * Tests get_user_campaigns_by_user_id()
	 * @author Manassarn M.
	 */
	function get_user_campaigns_by_user_id_test(){
		$result = $this->user_campaigns->get_user_campaigns_by_user_id(1);
		$this->unit->run($result,'is_array', 'get_user_campaigns_by_user_id()');
		$this->unit->run($result[0]['user_id'],'is_string','user_id');
		$this->unit->run($result[0]['campaign_id'],'is_string','campaign_id');		
		$this->unit->run($result[0]['user_first_name'],'is_string','user_first_name');
		$this->unit->run($result[0]['user_last_name'],'is_string','user_last_name');
		$this->unit->run($result[0]['user_email'],'is_string','user_email');
		$this->unit->run($result[0]['user_image'],'is_string','user_image');
		$this->unit->run($result[0]['user_facebook_id'],'is_string','user_facebook_id');
		$this->unit->run($result[0]['user_register_date'],'is_string','user_register_date');
		$this->unit->run($result[0]['user_last_seen'],'is_string','user_last_seen');
		$this->unit->run(count($result[0]) == 9,'is_true', 'number of column');
	}
	
	/**
	 * Test add_user_campaign() and remove_user_campaign()
	 * @author Manassarn M.
	 */
	function add_user_campaign_and_remove_user_campaign_test(){
		$user_id = $campaign_id = 50;
		$user_campaign = array(
							'user_id' => $user_id,
							'campaign_id' => $campaign_id
						);
		$add_result = $this->user_campaigns->add_user_campaign($user_campaign);
		$this->unit->run($add_result,'is_true','add_user_campaign()');
		
		$removed = $this->user_campaigns->remove_user_campaign($user_id, $campaign_id);
		$this->unit->run($removed == 1,'is_true','remove_user_campaign()');
		
		$removed_again = $this->user_campaigns->remove_user_campaign($user_id, $campaign_id);
		$this->unit->run($removed_again == 0,'is_true','remove_user_campaign()');
	}
}
/* End of file user_campaigns_model_test.php */
/* Location: ./application/controllers/test/user_campaigns_model_test.php */