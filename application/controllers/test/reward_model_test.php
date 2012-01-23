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
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$input = compact('page_id','start_timestamp','end_timestamp');
		
		$this->reward_id_1 = $result = $this->reward->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward->count_all();
		$this->unit->run($count, 1, 'count', $count);
		
		// $result = $this->reward->add($input); //cannot add duplicate criteria
		// $this->unit->run($result, FALSE, "\$result", $result);

		$count = $this->reward->count_all();
		$this->unit->run($count, 1, 'count', $count);

		$input = compact('app_install_id','start_timestamp','end_timestamp');
		$this->reward_id_2 = $result = $this->reward->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward->count_all();
		$this->unit->run($count, 2, 'count', $count);

		$input = compact('campaign_id','start_timestamp','end_timestamp');
		
		$this->reward_id_3 = $result = $this->reward->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward->count_all();
		$this->unit->run($count, 3, 'count', $count);
	}

	function add_fail_test(){
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$input = compact('start_timestamp','end_timestamp');
		
		$result = $this->reward->add($input);
		$this->unit->run($result, FALSE, "\$result", $result);

		$page_id = page_id + 1;
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 10;
		$input = compact('page_id','start_timestamp','end_timestamp');
		
		$result = $this->reward->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //end before start

		$start_timestamp = time() + 3600;
		$end_timestamp = time() - 1;
		$input = compact('page_id','start_timestamp','end_timestamp');
		
		$result = $this->reward->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //end before time()

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

	function update_test(){
        $input = array(
            'page_id' => 50, //campaign -> page
            'start_timestamp' => time() + 1000,
            'end_timestamp' => time() + 10000,
            );
        $result = $this->reward->update($this->reward_id_3, $input);
        $this->unit->run($result, TRUE, "\$result", $result);
        $result = $this->reward->get_by_page_id(page_id);
        $this->unit->run($result, 'is_array', "\$result", $result);
        $this->unit->run($result['criteria']['page_id'], page_id, "\$result['criteria']['page_id']",$result['criteria']['page_id']);
        $this->unit->run(isset($result['criteria']['campaign_id']), FALSE, "isset(\$result['criteria']['campaign_id']", isset($result['criteria']['campaign_id']));

		$input = array(
			'campaign_id'  => campaign_id, //page -> campaign
			'start_timestamp' => time() + 1000,
			'end_timestamp' => time() + 10000,
			);
		$result = $this->reward->update($this->reward_id_3, $input);
		$this->unit->run($result, TRUE, "\$result", $result);
		$result = $this->reward->get_by_campaign_id(campaign_id);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['criteria']['campaign_id'], campaign_id, "\$result['criteria']['campaign_id']",$result['criteria']['campaign_id']);
        $this->unit->run(isset($result['criteria']['page_id']), FALSE, "isset(\$result['criteria']['page_id']", isset($result['criteria']['page_id']));

        //FAIL

        $input = array(
            'campaign_id' => campaign_id,
            'start_timestamp' => time() + 1000,
            'end_timestamp' => time() + 100,
            );
        $result = $this->reward->update($this->reward_id_3, $input);
        $this->unit->run($result, FALSE, "\$result", $result); //end < start
        
        $input = array(
            'campaign_id' => campaign_id,
            'start_timestamp' => time() - 300,
            'end_timestamp' => time() - 1,
            );
        $result = $this->reward->update($this->reward_id_3, $input);
        $this->unit->run($result, FALSE, "\$result", $result); //end < now
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