<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('page_id', 1);
define('app_install_id', 2);
define('campaign_id', 3);
class Reward_model_test extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('reward_model', 'reward');
		$this->unit->reset_mongodb();
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
	
	function start_test(){
		$this->reward->drop_collection();
	}
	
	function create_index_test(){
		$this->reward->create_index();
	}
	
	function add_test(){
		$page_id = page_id;
		$app_install_id = app_install_id;
		$campaign_id = campaign_id;
		$timestamp = time() + 3600;
		$input = compact('page_id','timestamp');
		
		$this->reward_id = $result = $this->reward->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward->count_all();
		$this->unit->run($count, 1, 'count', $count);
		
		$result = $this->reward->add($input); //cannot add duplicate criteria
		$this->unit->run($result, FALSE, "\$result", $result);
		var_dump($result);

		$count = $this->reward->count_all();
		$this->unit->run($count, 1, 'count', $count);

		$input = compact('app_install_id','timestamp');
		$this->reward_id = $result = $this->reward->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward->count_all();
		$this->unit->run($count, 2, 'count', $count);

		$input = compact('campaign_id','timestamp');
		
		$this->reward_id = $result = $this->reward->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward->count_all();
		$this->unit->run($count, 3, 'count', $count);
	}

	function add_fail_test(){
		$timestamp = time() + 3600;
		$input = compact('timestamp');
		
		$result = $this->reward->add($input);
		$this->unit->run($result, FALSE, "\$result", $result);

		$count = $this->reward->count_all();
		$this->unit->run($count, 3, 'count', $count);
	}

	function get_test(){
		$result = $this->reward->get_by_campaign_id(campaign_id);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$result = $this->reward->get_by_app_install_id(app_install_id);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$result = $this->reward->get_by_page_id(page_id);
		$this->unit->run($result, 'is_array', "\$result", $result);
		
		$result = $this->reward->get_by_campaign_id(campaign_id+100);
		$this->unit->run($result, NULL, "\$result", $result);
		$result = $this->reward->get_by_app_install_id(app_install_id+100);
		$this->unit->run($result, NULL, "\$result", $result);
		$result = $this->reward->get_by_page_id(page_id+100);
		$this->unit->run($result, NULL, "\$result", $result);

	}

	function remove_test(){
		$result = $this->reward->remove_by_campaign_id(campaign_id+100); // record to delete
		$this->unit->run($result, FALSE, "\$result", $result);
		$count = $this->reward->count_all();
		$this->unit->run($count, 3, 'count', $count);

		$result = $this->reward->remove_by_campaign_id(campaign_id);
		$this->unit->run($result, TRUE, "\$result", $result);
		$count = $this->reward->count_all();
		$this->unit->run($count, 2, 'count', $count);
		$result = $this->reward->remove_by_app_install_id(app_install_id);
		$this->unit->run($result, TRUE, "\$result", $result);
		$count = $this->reward->count_all();
		$this->unit->run($count, 1, 'count', $count);
		$result = $this->reward->remove_by_page_id(page_id);
		$this->unit->run($result, TRUE, "\$result", $result);
		$count = $this->reward->count_all();
		$this->unit->run($count, 0, 'count', $count);

		$result = $this->reward->remove_by_campaign_id(campaign_id); //already deleted
		$this->unit->run($result, FALSE, "\$result", $result);
		$count = $this->reward->count_all();
		$this->unit->run($count, 0, 'count', $count);
	}
}
/* End of file reward_model_test.php */
/* Location: ./application/controllers/test/reward_model_test.php */