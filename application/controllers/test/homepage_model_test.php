<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Homepage_model_test extends CI_Controller {
	public $homepage_data = array(
		'campaign_id' => 1,
		'enable' => TRUE,
		'image' => 'https://localhost/assets/images/blank.png',
		'message' => 'You are not this page\'s fan, please like this page first'
	);
	public $homepage_update_data = array(
		'campaign_id' => 555, // should be ignored by update
		'enable' => FALSE,
		'image' => 'https://localhost/assets/images/blank.png',
		'message' => 'You are not this page\'s fan, please like this page first, again'
	);
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('homepage_model','homepage');
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
	
	function clear_data_before_test(){
		$this->homepage->drop_collection();
	}
	
	function create_index_before_test(){
		$this->homepage->create_index();
	}
	
	/**
	 * Test add()
	 */
	function add_test(){
		$result = $this->homepage->add($this->homepage_data);
		$this->unit->run($result, TRUE, 'Add an homepage');
		
		$this->unit->run($this->homepage->count_all(), 1, 'count all homepage');
		
		$homepage2 = $this->homepage_data;
		unset($homepage2['enable']);
		$homepage2['campaign_id'] = 2;
		$result = $this->homepage->add($homepage2);
		$this->unit->run($result, TRUE, 'Add an homepage without setting enable');
		
		$this->unit->run($this->homepage->count_all(), 2, 'count all homepage');
	}
	
	/**
	 * Test add failures
	 */
	function failed_add_test(){
		$result = $this->homepage->add($this->homepage_data);
		$this->unit->run($result, TRUE, 'Duplicated campaign_id');
		
		$this->unit->run($this->homepage->count_all(), 2, 'count all homepage');
		
		$homepage_fail = $this->homepage_data;
		unset($homepage_fail['image']);
		$result = $this->homepage->add($homepage_fail);
		$this->unit->run($result, FALSE, 'No image');
		
		$homepage_fail = $this->homepage_data;
		unset($homepage_fail['message']);
		$result = $this->homepage->add($homepage_fail);
		$this->unit->run($result, FALSE, 'No message');
		
		$homepage_fail = $this->homepage_data;
		$homepage_fail['image'] = '';
		$result = $this->homepage->add($homepage_fail);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$homepage_fail = $this->homepage_data;
		$homepage_fail['message'] = '';
		$result = $this->homepage->add($homepage_fail);
		$this->unit->run($result, FALSE, 'Empty message');
		
	}
	
	function get_by_campaign_id_test(){
		$result = $this->homepage->get_by_campaign_id(1);
		unset($result['_id']);
		$this->unit->run($result, $this->homepage_data, 'get homepage by campaign_id');
		
		$result = $this->homepage->get_by_campaign_id('1');
		unset($result['_id']);
		$this->unit->run($result, $this->homepage_data, 'get homepage by string campaign_id');
		
		$result = $this->homepage->get_by_campaign_id(2);
		$this->unit->run($result, 'is_array', 'get homepage by campaign_id');
		$this->unit->run($result['enable'], FALSE, 'enable is FALSE by default');
	}
	
	function get_by_campaign_id_fail_test(){
		$result = $this->homepage->get_by_campaign_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->homepage->get_by_campaign_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->homepage->get_by_campaign_id(NULL);
		$this->unit->run($result, FALSE, 'not found');
	}
	
	function update_by_campaign_id_test(){
		$update_data = $this->homepage_update_data;
		$result = $this->homepage->update_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->homepage->get_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated homepage');
		
		$update_data = $this->homepage_update_data;
		unset($update_data['enable']);
		$result = $this->homepage->update_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->homepage->get_by_campaign_id(1);
		$this->unit->run($result['enable'], FALSE, 'get updated homepage, enable is FALSE if not set on update');
	}
	
	function update_by_campaign_id_fail_test(){
		$result = $this->homepage->update_by_campaign_id(0, $this->homepage_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$result = $this->homepage->update_by_campaign_id(NULL, $this->homepage_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$fail_update_data = $this->homepage_update_data;
		unset($fail_update_data['image']);
		$result = $this->homepage->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No image');
		
		$fail_update_data = $this->homepage_update_data;
		unset($fail_update_data['message']);
		$result = $this->homepage->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No message');
		
		$fail_update_data = $this->homepage_update_data;
		$fail_update_data['image'] = '';
		$result = $this->homepage->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$fail_update_data = $this->homepage_update_data;
		$fail_update_data['message'] = '';
		$result = $this->homepage->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty message');
	}
}
/* End of file homepage_model_test.php */
/* Location: ./application/controllers/test/homepage_model_test.php */