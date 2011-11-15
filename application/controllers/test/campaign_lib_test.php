<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_lib_test extends CI_Controller {
	private $dateStr1 = "2011/11/11";
	private $dateStr2 = "2011/11/20";

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
}
/* End of file campaign_lib_test.php */
/* Location: ./application/controllers/test/campaign_lib_test.php */