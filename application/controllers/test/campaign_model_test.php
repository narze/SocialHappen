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
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	/**
	 * Tests get campaigns by page_id
	 * @author Manassarn M.
	 */
	function get_campaigns_by_page_id_test(){
		$result = $this->campaigns->get_campaigns_by_page_id(1);
		$this->unit->run($result, 'is_array', 'get_campaigns_by_page_id');
		$this->unit->run($result[0]->campaign_id,'is_string','campaign_id');
		$this->unit->run($result[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($result[0]->campaign_name,'is_string','campaign_name');
		$this->unit->run($result[0]->campaign_detail,'is_string','campaign_detail');
		$this->unit->run($result[0]->campaign_status_id,'is_string','campaign_status_id');
		$this->unit->run($result[0]->campaign_status_name,'is_string','campaign_status_name');
		$this->unit->run($result[0]->campaign_active_member,'is_string','campaign_active_member');
		$this->unit->run($result[0]->campaign_all_member,'is_string','campaign_all_member');
		$this->unit->run($result[0]->campaign_start_timestamp,'is_string','campaign_start_timestamp');
		$this->unit->run($result[0]->campaign_end_timestamp,'is_string','campaign_end_timestamp');
		
		$this->unit->run($result[0]->company_id,'is_string','company_id');
		$this->unit->run($result[0]->app_id,'is_string','app_id');
		$this->unit->run($result[0]->app_install_status,'is_string','app_install_status');
		$this->unit->run($result[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($result[0]->page_id,'is_string','page_id');
		$this->unit->run($result[0]->app_install_secret_key,'is_string','app_install_secret_key');
		$this->unit->run(count((array)$result[0]) == 16, 'is_true', 'number of column');
	}

	/**
	 * Tests get campaign profile by campaign_id
	 * @author Manassarn M.
	 */
	function get_campaign_profile_by_id_test(){
		$result = $this->campaigns->get_campaign_profile_by_id(1);
		$this->unit->run($result, 'is_array', 'get_campaign_profile_by_id()');
		$this->unit->run($result[0]->campaign_id,'is_string','campaign_id');
		$this->unit->run($result[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($result[0]->campaign_name,'is_string','campaign_name');
		$this->unit->run($result[0]->campaign_detail,'is_string','campaign_detail');
		$this->unit->run($result[0]->campaign_status_id,'is_string','campaign_status_id');
		$this->unit->run($result[0]->campaign_status_name,'is_string','campaign_status_name');
		$this->unit->run($result[0]->campaign_active_member,'is_string','campaign_active_member');
		$this->unit->run($result[0]->campaign_all_member,'is_string','campaign_all_member');
		$this->unit->run($result[0]->campaign_start_timestamp,'is_string','campaign_start_timestamp');
		$this->unit->run($result[0]->campaign_end_timestamp,'is_string','campaign_end_timestamp');
		
		$this->unit->run($result[0]->company_id,'is_string','company_id');
		$this->unit->run($result[0]->app_id,'is_string','app_id');
		$this->unit->run($result[0]->app_install_status,'is_string','app_install_status');
		$this->unit->run($result[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($result[0]->page_id,'is_string','page_id');
		$this->unit->run($result[0]->app_install_secret_key,'is_string','app_install_secret_key');
		$this->unit->run(count((array)$result[0]) == 16, 'is_true', 'number of column');	
	}

}
/* End of file campaign_model_test.php */
/* Location: ./application/controllers/test/campaign_model_test.php */