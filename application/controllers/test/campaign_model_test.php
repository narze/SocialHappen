<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('campaign_model','campaigns');
	}

	function __destruct(){
		echo $this->unit->report();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->campaigns))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	/**
	 * Tests get_page_campaigns_by_page_id()
	 * @author Manassarn M.
	 */
	function get_page_campaigns_by_page_id_test(){
		$result = $this->campaigns->get_page_campaigns_by_page_id(1);
		$this->unit->run($result,'is_array', 'get_page_campaigns_by_page_id()');
		$this->unit->run($result[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['campaign_name'],'is_string','campaign_name');
		$this->unit->run($result[0]['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($result[0]['campaign_status_id'],'is_string','campaign_status_id');
		$this->unit->run($result[0]['campaign_status_name'],'is_string','campaign_status_name');
		$this->unit->run($result[0]['campaign_active_member'],'is_string','campaign_active_member');
		$this->unit->run($result[0]['campaign_all_member'],'is_string','campaign_all_member');
		$this->unit->run($result[0]['campaign_start_timestamp'],'is_string','campaign_start_timestamp');
		$this->unit->run($result[0]['campaign_end_timestamp'],'is_string','campaign_end_timestamp');
		
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_install_status'],'is_string','app_install_status');
		$this->unit->run($result[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run(count($result[0]) == 16,'is_true', 'number of column');
	}

	/**
	 * Tests get_campaign_profile_by_campaign_id()
	 * @author Manassarn M.
	 */
	function get_campaign_profile_by_campaign_id_test(){
		$result = $this->campaigns->get_campaign_profile_by_campaign_id(1);
		$this->unit->run($result,'is_array', 'get_campaign_profile_by_campaign_id()');
		$this->unit->run($result[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['campaign_name'],'is_string','campaign_name');
		$this->unit->run($result[0]['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($result[0]['campaign_status_id'],'is_string','campaign_status_id');
		$this->unit->run($result[0]['campaign_status_name'],'is_string','campaign_status_name');
		$this->unit->run($result[0]['campaign_active_member'],'is_string','campaign_active_member');
		$this->unit->run($result[0]['campaign_all_member'],'is_string','campaign_all_member');
		$this->unit->run($result[0]['campaign_start_timestamp'],'is_string','campaign_start_timestamp');
		$this->unit->run($result[0]['campaign_end_timestamp'],'is_string','campaign_end_timestamp');
		
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_install_status'],'is_string','app_install_status');
		$this->unit->run($result[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run(count($result[0]) == 16,'is_true', 'number of column');	
	}

	/**
	 * Test add_campaign() and remove_campaign()
	 * @author Manassarn M.
	 */
	function add_campaign_and_remove_campaign_test(){
		$campaign = array(
							'app_install_id' => 'test',
							'campaign_name' => 'test',
							'campaign_detail' => 'test',
							'campaign_status_id' => '1',
							'campaign_active_member' => '0',
							'campaign_all_member' => '1',
							'campaign_start_timestamp' => NULL,
							'campaign_end_timestamp' => NULL
						);
		$campaign_id = $this->campaigns->add_campaign($campaign);
		$this->unit->run($campaign_id,'is_int','add_campaign()');
		
		$removed = $this->campaigns->remove_campaign($campaign_id);
		$this->unit->run($removed == 1,'is_true','remove_campaign()');
		
		$removed_again = $this->campaigns->remove_campaign($campaign_id);
		$this->unit->run($removed_again == 0,'is_true','remove_campaign()');
	}
}
/* End of file campaign_model_test.php */
/* Location: ./application/controllers/test/campaign_model_test.php */