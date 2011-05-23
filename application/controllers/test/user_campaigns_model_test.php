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
		$this->unit->run($result, 'is_array', 'get_campaign_users_by_campaign_id()');
		$this->unit->run($result[0]->user_id,'is_string','user_id');
		$this->unit->run($result[0]->campaign_id,'is_string','campaign_id');
		$this->unit->run($result[0]->user_facebook_id,'is_string','user_facebook_id');
		$this->unit->run($result[0]->user_register_date,'is_string','user_register_date');
		$this->unit->run($result[0]->user_last_seen,'is_string','user_last_seen');
		$this->unit->run(count((array)$result[0]) == 5, 'is_true', 'number of column');
	}
	
}
/* End of file user_campaigns_model_test.php */
/* Location: ./application/controllers/test/user_campaigns_model_test.php */