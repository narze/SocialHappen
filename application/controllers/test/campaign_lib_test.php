<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_lib_test extends CI_Controller {
	private $dateStr1 = "2011/11/11";
	private $dateStr2 = "2011/11/20";
	private $dateStr3 = "2011/11/05";
	private $dateStr4 = "2011/11/06";
	private $dateStr5 = "2011/11/15";
	private $dateStr6 = "2011/11/16";
	private $dateStr7 = "2011/11/25";
	private $dateStr8 = "2011/11/26";
	private $dateStr9 = "2011/11/30";
	private $dateStr10 = "2011/11/14";
	private $dateStr11 = "2011/11/17";

	private $campaigns = array(
		array('campaign_id' => 1, 'campaign_start_date' => '2011/11/06', 'campaign_end_date' => '2011/11/15'),
		array('campaign_id' => 2, 'campaign_start_date' => '2011/11/16', 'campaign_end_date' => '2011/11/25'),
	);

	private $campaigns_api_data1 = array( // found current campaign : 2
		array('campaign_id' => 1, 'campaign_start_date' => '2011/11/06', 'campaign_end_date' => '2011/11/14', 'campaign_end_message' => 'end campaign 1'),
		array('campaign_id' => 2, 'campaign_start_date' => '2011/11/15', 'campaign_end_date' => '2033/11/25', 'campaign_end_message' => 'end campaign 2')
	);

	private $campaigns_api_data2 = array( // not found current campaign, have last campaign : 1
		array('campaign_id' => 0, 'campaign_start_date' => '2011/10/06', 'campaign_end_date' => '2011/11/05', 'campaign_end_message' => 'end campaign 0'),
		array('campaign_id' => 1, 'campaign_start_date' => '2011/11/06', 'campaign_end_date' => '2011/11/14', 'campaign_end_message' => 'end campaign 1'),
		array('campaign_id' => 3, 'campaign_start_date' => '2011/09/15', 'campaign_end_date' => '2011/10/04', 'campaign_end_message' => 'end campaign 3'),
		array('campaign_id' => 2, 'campaign_start_date' => '2031/11/15', 'campaign_end_date' => '2031/11/25', 'campaign_end_message' => 'end campaign 2')
	);

	private $campaigns_api_data3 = array( // not found current campaign, no last campaign
		array('campaign_id' => 1, 'campaign_start_date' => '2031/11/06', 'campaign_end_date' => '2031/11/14', 'campaign_end_message' => 'end campaign 1'),
		array('campaign_id' => 2, 'campaign_start_date' => '2031/11/15', 'campaign_end_date' => '2031/11/25', 'campaign_end_message' => 'end campaign 2')
	);

	private $api_request_campaign_expected1 = array(
		'in_campaign' => TRUE,
		'campaign_id' => 2,
		'campaign_end_message' => NULL
	);
	private $api_request_campaign_expected2 = array(
		'in_campaign' => FALSE,
		'campaign_id' => NULL,
		'campaign_end_message' => 'end campaign 1'
	);
	private $api_request_campaign_expected3 = array(
		'in_campaign' => FALSE,
		'campaign_id' => NULL,
		'campaign_end_message' => 'No campaign created'
	);
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('campaign_lib');
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
	
	function validate_date_range_test(){
		$result = $this->campaign_lib->validate_date_range($this->dateStr1, $this->dateStr2);
		$this->unit->run($result, TRUE, '2011/11/11 - 2011/11/20');

		$result = $this->campaign_lib->validate_date_range($this->dateStr1, $this->dateStr1);
		$this->unit->run($result, TRUE, '2011/11/11 - 2011/11/11');
		
		$result = $this->campaign_lib->validate_date_range($this->dateStr2, $this->dateStr1);
		$this->unit->run($result, FALSE, '2011/11/20 - 2011/11/11');
	}
	
	function validate_date_range_error_test(){
		$result = $this->campaign_lib->validate_date_range('', $this->dateStr2);
		$this->unit->run($result, FALSE, '"" - 2011/11/20');

		$result = $this->campaign_lib->validate_date_range($this->dateStr1, '');
		$this->unit->run($result, FALSE, '2011/11/11 - ""');

		$result = $this->campaign_lib->validate_date_range($this->dateStr2, 0);
		$this->unit->run($result, FALSE, '2011/11/11 - 0');

		$result = $this->campaign_lib->validate_date_range($this->dateStr2, NULL);
		$this->unit->run($result, FALSE, '2011/11/11 - NULL');

		$result = $this->campaign_lib->validate_date_range($this->dateStr2, FALSE);
		$this->unit->run($result, FALSE, '2011/11/11 - FALSE');

		$result = $this->campaign_lib->validate_date_range($this->dateStr2, TRUE);
		$this->unit->run($result, FALSE, '2011/11/11 - TRUE');
	}

	function validate_date_range_with_campaigns_test(){
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr1, $this->dateStr2, $this->campaigns);
		$this->unit->run($result, FALSE, $this->dateStr1.'-'.$this->dateStr2);
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr3, $this->dateStr4, $this->campaigns);
		$this->unit->run($result, FALSE, $this->dateStr3.'-'.$this->dateStr4);
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr4, $this->dateStr5, $this->campaigns);
		$this->unit->run($result, FALSE, $this->dateStr4.'-'.$this->dateStr5);
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr5, $this->dateStr6, $this->campaigns);
		$this->unit->run($result, FALSE, $this->dateStr5.'-'.$this->dateStr6);
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr6, $this->dateStr7, $this->campaigns);
		$this->unit->run($result, FALSE, $this->dateStr6.'-'.$this->dateStr7);
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr7, $this->dateStr8, $this->campaigns);
		$this->unit->run($result, FALSE, $this->dateStr7.'-'.$this->dateStr8);
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr8, $this->dateStr9, $this->campaigns);
		$this->unit->run($result, TRUE, $this->dateStr8.'-'.$this->dateStr9);
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr10, $this->dateStr11, $this->campaigns);
		$this->unit->run($result, FALSE, $this->dateStr10.'-'.$this->dateStr11);

		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr1, $this->dateStr2, array());
		$this->unit->run($result, TRUE, 'First campaign, no other campaign found');
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr1, $this->dateStr2, NULL);
		$this->unit->run($result, TRUE, 'First campaign, no other campaign found');
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr1, $this->dateStr2, FALSE);
		$this->unit->run($result, TRUE, 'First campaign, no other campaign found');
	}

	function validate_date_range_with_campaigns_fail_test(){
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr1, FALSE, $this->campaigns);
		$this->unit->run($result, FALSE, 'error');
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr1, '', $this->campaigns);
		$this->unit->run($result, FALSE, 'error');
		$result = $this->campaign_lib->validate_date_range_with_campaigns(TRUE, $this->dateStr2, $this->campaigns);
		$this->unit->run($result, FALSE, 'error');
		$result = $this->campaign_lib->validate_date_range_with_campaigns($this->dateStr1, $this->dateStr2, TRUE);
		$this->unit->run($result, FALSE, 'error');
	}

	function api_request_current_campaign_in_campaigns_test(){
		$expected = $this->api_request_campaign_expected1;
		$result = $this->campaign_lib->api_request_current_campaign_in_campaigns($this->campaigns_api_data1);
		$this->unit->run($result, $expected);
		
		$expected = $this->api_request_campaign_expected2;
		$result = $this->campaign_lib->api_request_current_campaign_in_campaigns($this->campaigns_api_data2);
		
		$this->unit->run($result, $expected);
		
		$expected = $this->api_request_campaign_expected3;
		$result = $this->campaign_lib->api_request_current_campaign_in_campaigns($this->campaigns_api_data3);
		
		$this->unit->run($result, $expected);
		
		$expected = $this->api_request_campaign_expected3;
		$result = $this->campaign_lib->api_request_current_campaign_in_campaigns(array());
		
		$this->unit->run($result, $expected);
	}

	function api_request_current_campaign_in_campaigns_fail_test(){
		$result = $this->campaign_lib->api_request_current_campaign_in_campaigns(NULL);
		$this->unit->run($result, FALSE);

		$result = $this->campaign_lib->api_request_current_campaign_in_campaigns(FALSE);
		$this->unit->run($result, FALSE);

		$result = $this->campaign_lib->api_request_current_campaign_in_campaigns('string');
		$this->unit->run($result, FALSE);

	}
}
/* End of file campaign_lib_test.php */
/* Location: ./application/controllers/test/campaign_lib_test.php */