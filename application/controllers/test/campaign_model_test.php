<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('campaign_model','campaigns');
		$this->unit->reset_mysql();
	}

	function __destruct(){
		$this->unit->report_with_counter();
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
		$this->unit->run($result[0]['campaign_status'] == 'Inactive','is_true', 'campaign_status == "Inactive"');
		$this->unit->run($result[0]['campaign_status_id'] == 1,'is_true', 'campaign_status_id == 1');

		$this->unit->run($result[0]['campaign_start_timestamp'],'is_string','campaign_start_timestamp');
		$this->unit->run($result[0]['campaign_end_timestamp'],'is_string','campaign_end_timestamp');
		
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($result[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($result[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['app_install_secret_key'],'is_string','app_install_secret_key');
	}	
	
	/**
	 * Tests get_page_campaigns_by_page_id_and_campaign_status_id()
	 * @author Manassarn M.
	 */
	function get_page_campaigns_by_page_id_and_campaign_status_id_test(){
		$result = $this->campaigns->get_page_campaigns_by_page_id_and_campaign_status_id(1,1);
		$this->unit->run($result,'is_array', 'get_page_campaigns_by_page_id_and_campaign_status_id()');
		$this->unit->run($result[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['campaign_name'],'is_string','campaign_name');
		$this->unit->run($result[0]['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($result[0]['campaign_status'] == 'Inactive','is_true', 'campaign_status == "Inactive"');
		$this->unit->run($result[0]['campaign_status_id'] == 1,'is_true', 'campaign_status_id == 1');

		$this->unit->run($result[0]['campaign_start_timestamp'],'is_string','campaign_start_timestamp');
		$this->unit->run($result[0]['campaign_end_timestamp'],'is_string','campaign_end_timestamp');
		
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($result[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($result[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['app_install_secret_key'],'is_string','app_install_secret_key');
	}

	/**
	 * Tests get_campaign_profile_by_campaign_id()
	 * @author Manassarn M.
	 */
	function get_campaign_profile_by_campaign_id_test(){
		$result = $this->campaigns->get_campaign_profile_by_campaign_id(1);
		$this->unit->run($result,'is_array', 'get_campaign_profile_by_campaign_id()');
		$this->unit->run($result['campaign_id'],'is_string','campaign_id');
		$this->unit->run($result['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result['campaign_name'],'is_string','campaign_name');
		$this->unit->run($result['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($result['campaign_status'] == 'Inactive','is_true', 'campaign_status == "Inactive"');
		$this->unit->run($result['campaign_status_id'] == 1,'is_true', 'campaign_status_id == 1');
		$this->unit->run($result['campaign_start_timestamp'],'is_string','campaign_start_timestamp');
		$this->unit->run($result['campaign_end_timestamp'],'is_string','campaign_end_timestamp');
		
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['app_id'],'is_string','app_id');
		$this->unit->run($result['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($result['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($result['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result['page_id'],'is_string','page_id');
		$this->unit->run($result['app_install_secret_key'],'is_string','app_install_secret_key');
	}

	/**
	 * Tests get_campaigns_by_app_install_id()
	 * @author Manassarn M.
	 */
	function get_campaigns_by_app_install_id_test(){
		$result = $this->campaigns->get_campaigns_by_app_install_id(1);
		$this->unit->run($result,'is_array', 'get_campaigns_by_app_install_id()');
		$this->unit->run($result[0],'is_array', 'get_campaigns_by_app_install_id()');
		$this->unit->run($result[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['campaign_name'],'is_string','campaign_name');
		$this->unit->run($result[0]['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($result[0]['campaign_status'] == 'Inactive','is_true', 'campaign_status == "Inactive"');
		$this->unit->run($result[0]['campaign_status_id'] == 1,'is_true', 'campaign_status == 1');

		$this->unit->run($result[0]['campaign_start_timestamp'],'is_string','campaign_start_timestamp');
		$this->unit->run($result[0]['campaign_end_timestamp'],'is_string','campaign_end_timestamp');
		
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($result[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($result[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['app_install_secret_key'],'is_string','app_install_secret_key');
	}

	/**
	 * Tests get_app_campaigns_by_app_install_id_and_campaign_status_id()
	 * @author Manassarn M.
	 */
	function get_app_campaigns_by_app_install_id_and_campaign_status_id_test(){
		$result = $this->campaigns->get_app_campaigns_by_app_install_id_and_campaign_status_id(1,1);
		$this->unit->run($result,'is_array', 'get_app_campaigns_by_app_install_id_and_campaign_status_id()');
		$this->unit->run($result[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['campaign_name'],'is_string','campaign_name');
		$this->unit->run($result[0]['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($result[0]['campaign_status'] == 'Inactive','is_true', 'campaign_status == "Inactive"');
		$this->unit->run($result[0]['campaign_status_id'] == 1,'is_true', 'campaign_status == 1');

		$this->unit->run($result[0]['campaign_start_timestamp'],'is_string','campaign_start_timestamp');
		$this->unit->run($result[0]['campaign_end_timestamp'],'is_string','campaign_end_timestamp');
		
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($result[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($result[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['app_install_secret_key'],'is_string','app_install_secret_key');
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
	
	/**
	 * Test count_campaigns_by_page_id()
	 * @author Manassarn M.
	 */
	function count_campaigns_by_page_id_test(){
		$result = $this->campaigns->count_campaigns_by_page_id(1);
		$this->unit->run($result,'is_int', 'count_campaigns_by_page_id()');
	}
	
	/**
	 * Test count_campaigns_by_app_install_id()
	 * @author Manassarn M.
	 */
	function count_campaigns_by_app_install_id_test(){
		$result = $this->campaigns->count_campaigns_by_app_install_id(1);
		$this->unit->run($result,'is_int', 'count_campaigns_by_app_install_id()');
	}

	function get_app_campaigns_by_app_install_id_ordered_test(){
		$campaign1 = array(
							'app_install_id' => 1,
							'campaign_name' => 'test1',
							'campaign_detail' => 'test2',
							'campaign_status_id' => '1',
							'campaign_start_timestamp' => '2011-11-11 11:11:11',
							'campaign_end_timestamp' => '2011-11-11 11:11:11'
						);
		$campaign_id1 = $this->campaigns->add_campaign($campaign1);
		$this->unit->run($campaign_id1,'is_int','add_campaign()');

		$campaign2 = array(
							'app_install_id' => 1,
							'campaign_name' => 'test2',
							'campaign_detail' => 'test2',
							'campaign_status_id' => '1',
							'campaign_start_timestamp' => '2011-11-11 11:11:12',
							'campaign_end_timestamp' => '2011-11-11 11:11:12'
						);
		$campaign_id2 = $this->campaigns->add_campaign($campaign2);
		$this->unit->run($campaign_id2,'is_int','add_campaign()');

		$result = $this->campaigns->get_app_campaigns_by_app_install_id_ordered(1, 'campaign_start_timestamp desc');
		$this->unit->run($result[0]['campaign_name'] === 'test2', TRUE, 'get_app_campaigns_by_app_install_id_ordered' );
		$this->unit->run($result[1]['campaign_name'] === 'test1', TRUE, 'get_app_campaigns_by_app_install_id_ordered' );

		$result = $this->campaigns->get_app_campaigns_by_app_install_id_ordered(1, 'campaign_end_timestamp asc');
		$this->unit->run($result[0]['campaign_name'] === 'test1', TRUE, 'get_app_campaigns_by_app_install_id_ordered' );
		$this->unit->run($result[1]['campaign_name'] === 'test2', TRUE, 'get_app_campaigns_by_app_install_id_ordered' );

	}

	function get_app_campaigns_by_app_install_id_test(){
		
	}

	function update_campaign_by_id_test(){
		
	}

	function count_campaigns_by_page_id_and_campaign_status_id_test(){
		
	}

	function count_campaigns_by_app_install_id_and_campaign_status_id_test(){
		
	}

	function count_campaigns_by_company_id_test(){
		
	}

	function count_campaigns_by_company_id_and_campaign_status_id_test(){
		
	}

	function add_incoming_and_expired_campaign_for_test(){
		date_default_timezone_set('Asia/Bangkok');
		$campaign = array(
			'app_install_id' => 1,
			'campaign_name' => 'incoming',
			'campaign_detail' => 'incoming',
			'campaign_status_id' => '2',
			'campaign_start_timestamp' => date('Y-m-d H:i:s', time()+1),
			'campaign_end_timestamp' => date('Y-m-d H:i:s', time()+2)
		);
		$campaign_id = $this->campaigns->add_campaign($campaign);
		$this->unit->run($campaign_id,'is_int','add_campaign()');
	}

	function get_incoming_campaigns_by_page_id_test(){
		$page_id = 1;
		$result = $this->campaigns->get_incoming_campaigns_by_page_id($page_id); //8
		$this->unit->run($result[0]['campaign_name'], 'incoming', "\$result[0]['campaign_name']", $result[0]['campaign_name']);
	}

	function get_active_campaigns_by_page_id_test(){
		$page_id = 1;
		$result = $this->campaigns->get_active_campaigns_by_page_id($page_id); //2
		$this->unit->run($result[0]['campaign_name'], '2nd campaign', "\$result[0]['campaign_name']", $result[0]['campaign_name']);
		$this->unit->run(count($result), 1, "\count($result)", count($result));
	}

	function get_expired_campaigns_by_page_id_test(){
		$page_id = 1;
		$result = $this->campaigns->get_expired_campaigns_by_page_id($page_id); //4
		$this->unit->run($result[0]['campaign_name'], '3rd campaign', "\$result[0]['campaign_name']", $result[0]['campaign_name']);
	}

	function count_incoming_campaigns_by_page_id_test(){
		$page_id = 1;
		$result = $this->campaigns->count_incoming_campaigns_by_page_id($page_id);
		$this->unit->run($result, 1, "\$result", $result);
	}

	function count_active_campaigns_by_page_id_test(){
		$page_id = 1;
		$result = $this->campaigns->count_active_campaigns_by_page_id($page_id);
		$this->unit->run($result, 1, "\$result", $result);
	}

	function count_expired_campaigns_by_page_id_test(){
		$page_id = 1;
		$result = $this->campaigns->count_expired_campaigns_by_page_id($page_id);
		$this->unit->run($result, 1, "\$result", $result);
	}

	function get_incoming_campaigns_by_app_install_id_test(){
		$app_install_id = 1;
		$result = $this->campaigns->get_incoming_campaigns_by_app_install_id($app_install_id); //1
		$this->unit->run($result[0]['campaign_name'], 'incoming', "\$result[0]['campaign_name']", $result[0]['campaign_name']);
		$this->unit->run(count($result), 1, "\count($result)", count($result));
	}

	function get_active_campaigns_by_app_install_id_test(){
		$app_install_id = 1;
		$result = $this->campaigns->get_active_campaigns_by_app_install_id($app_install_id); //0
		$this->unit->run(count($result), 0, "\count($result)", count($result));
	}

	function get_expired_campaigns_by_app_install_id_test(){
		$app_install_id = 1;
		$result = $this->campaigns->get_expired_campaigns_by_app_install_id($app_install_id); //0
		$this->unit->run(count($result), 0, "\count($result)", count($result));
	}

	function count_incoming_campaigns_by_app_install_id_test(){
		$app_install_id = 1;
		$result = $this->campaigns->count_incoming_campaigns_by_app_install_id($app_install_id);
		$this->unit->run($result, 1, "\$result", $result);
	}

	function count_active_campaigns_by_app_install_id_test(){
		$app_install_id = 1;
		$result = $this->campaigns->count_active_campaigns_by_app_install_id($app_install_id);
		$this->unit->run($result, 0, "\$result", $result);
	}

	function count_expired_campaigns_by_app_install_id_test(){
		$app_install_id = 1;
		$result = $this->campaigns->count_expired_campaigns_by_app_install_id($app_install_id);
		$this->unit->run($result, 0, "\$result", $result);
	}

}
/* End of file campaign_model_test.php */
/* Location: ./application/controllers/test/campaign_model_test.php */