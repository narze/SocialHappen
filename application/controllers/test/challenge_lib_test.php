<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Challenge_lib_test extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->library('unit_test');
  	  	$this->load->library('challenge_lib');
    	$this->unit->reset_dbs();
	}

	function __destruct() {
		$this->unit->report_with_counter();
	}
	
	function index() {
		$class_methods = get_class_methods($this);
		//echo 'Functions : '.(count(get_class_methods($this->achievement_lib))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)) {
    			$this->$method();
    		}
		}
	}

	function setup_before_test() {
		$this->challenge = array(
			'company_id' => 1,
			'start' => time(),
			'end' => time() + 86400,
			'detail' => array(
				'name' => 'Challenge name',
				'description' => 'Challenge description',
				'image' => 'Challenge image url'
			),
			'criteria' => array(
				array(
					'name' => 'C1',
					'query' => array('page_id' => 1, 'app_id'=>1, 'action_id'=>1),
					'count' => 1
				),
				array(
					'name' => 'C2',
					'query' => array('page_id' => 1, 'app_id'=>2, 'action_id'=>2),
					'count' => 2
				)
			),
		);

		$this->achievement_stat1 = array(
			'action_id' => 1,
			'page_id' => 1,
			'company_id' => 1,
			'app_install_id' => 1
		);

		$this->achievement_stat2 = array(
			'action_id' => 2,
			'page_id' => 1,
			'company_id' => 1,
			'app_install_id' => 2
		);
	}

	function add_test() {
		$result = $this->challenge_lib->add($this->challenge);
		$this->unit->run($result, TRUE, "\$result", $result);
		$this->challenge_id = $result;
	}

	function get_test() {
		$criteria = array('company_id' => '1');
		$result = $this->challenge_lib->get($criteria);
		$this->unit->run(count($result), 1, "\$result", count($result));
		$this->unit->run($result[0], 'is_array', "\$result[0]", $result[0]);
		$this->unit->run($result[0]['detail']['name'], 'Challenge name', "\$result[0]['detail']['name']", $result[0]['detail']['name']);
	}

	function get_one_test() {
		$criteria = array('company_id' => '1');
		$result = $this->challenge_lib->get_one($criteria);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['detail']['name'], 'Challenge name', "\$result['detail']['name']", $result['detail']['name']);
	}

	function update_test() {
		$criteria = array('company_id' => '1');
		$update = array(
			'$set' => array(
				'start' => time() + 86400
			)
		);
		$result = $this->challenge_lib->update($criteria, $update);
		$this->unit->run($result, TRUE, "\$result", $result);

		$update = array(
			'$set' => array(
				'end' => time()
			)
		);
		$result = $this->challenge_lib->update($criteria, $update);echo '<pre>';
		echo '</pre>';
		$this->unit->run($result, FALSE, "\$result", $result); // end < start
	}

	function check_challenge_test() {
		$info = array();
		$company_id = 1;
		$user_id = 1;
		$result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
		$expected_result = array(
			'success' => TRUE, //no error checking challenges
			'completed' => array(),
			'in_progress' => array()
		);
		$this->unit->run($result, $expected_result, "\$result", $result);

		//Unrelated achievement_stat
		$this->load->library('achievement_lib');
		$app_id = 2;
		$user_id = 1;
		$inc_result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id,
			$this->achievement_stat1);
		$this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

		$info = array();
		$company_id = 1;
		$user_id = 1;
		$result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
		$expected_result = array(
			'success' => TRUE, //no error checking challenges
			'completed' => array(),
			'in_progress' => array()
		);
		$this->unit->run($result, $expected_result, "\$result", $result);

		$this->load->library('achievement_lib');
		$app_id = 1;
		$user_id = 1;
		$inc_result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id,
			$this->achievement_stat1);
		$this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

		$result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
		$expected_result = array(
			'success' => TRUE, //no error checking challenges
			'completed' => array(),
			'in_progress' => array($this->challenge_id)
		);
		$this->unit->run($result, $expected_result, "\$result", $result);

		$this->load->library('achievement_lib');
		$app_id = 2;
		$user_id = 1;
		$inc_result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id,
			$this->achievement_stat2);
		$this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

		$result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
		$expected_result = array(
			'success' => TRUE, //no error checking challenges
			'completed' => array(),
			'in_progress' => array($this->challenge_id)
		);
		$this->unit->run($result, $expected_result, "\$result", $result);

		//Count achieved before complete challenge
	  	$count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
		$this->unit->run($count, 0, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));
		
		$this->load->library('achievement_lib');
		$app_id = 2;
		$user_id = 1;
		//Check challenge invoked by increment_achievement_stat already
		$inc_result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id,
			$this->achievement_stat2);
		$this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

		$result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
		$expected_result = array(
			'success' => TRUE, //no error checking challenges
			'completed' => array($this->challenge_id), //get completed challenge id			
			'in_progress' => array()
		);
		$this->unit->run($result, $expected_result, "\$result", $result);

		//Count achieved after complete
	  	$count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
		$this->unit->run($count, 1, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));
		
		$this->load->library('achievement_lib');
		$app_id = 2;
		$user_id = 1;
		$inc_result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id,
			$this->achievement_stat2);
		$this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

		$result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
		$expected_result = array(
			'success' => TRUE, //no error checking challenges
			'completed' => array($this->challenge_id), //get completed challenge id
			'in_progress' => array()
		);
		$this->unit->run($result, $expected_result, "\$result", $result);

		//Count again to check that cannot complete twice
	  	$count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
		$this->unit->run($count, 1, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));
	}

	function remove_test() {
		// $criteria = array('page_id' => '1');
		// $result = $this->challenge_lib->remove($criteria);
		// $this->unit->run($result, TRUE, "\$result", $result);

		// $all_challenge = $this->challenge_lib->get(array());
		// $this->unit->run(count($all_challenge), 0, "count(\$all_challenge)", count($all_challenge));
	}

}