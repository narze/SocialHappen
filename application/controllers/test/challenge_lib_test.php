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
			'page_id' => 1,
			'start' => time(),
			'end' => time() + 86400,
			'detail' => array(
				'name' => 'Challenge name',
				'description' => 'Challenge description',
				'image' => 'Challenge image url'
			),
			'criteria' => array(
				array('name' => 'C1', 'query' => 'app.1.action.1', 'count' => 10),
				array('name' => 'C2', 'query' => 'app.2.action.2', 'count' => 5),
			),
		);
	}

	function add_test() {
		$result = $this->challenge_lib->add($this->challenge);
		$this->unit->run($result, TRUE, "\$result", $result);
		$this->challenge_id = $result;
	}

	function get_test() {
		$criteria = array('page_id' => '1');
		$result = $this->challenge_lib->get($criteria);
		$this->unit->run(count($result), 1, "\$result", count($result));
		$this->unit->run($result[0], 'is_array', "\$result[0]", $result[0]);
		$this->unit->run($result[0]['detail']['name'], 'Challenge name', "\$result[0]['detail']['name']", $result[0]['detail']['name']);
	}

	function get_one_test() {
		$criteria = array('page_id' => '1');
		$result = $this->challenge_lib->get_one($criteria);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['detail']['name'], 'Challenge name', "\$result['detail']['name']", $result['detail']['name']);
	}

	function update_test() {
		$criteria = array('page_id' => '1');
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

	function remove_test() {
		$criteria = array('page_id' => '1');
		$result = $this->challenge_lib->remove($criteria);
		$this->unit->run($result, TRUE, "\$result", $result);

		$all_challenge = $this->challenge_lib->get(array());
		$this->unit->run(count($all_challenge), 0, "count(\$all_challenge)", count($all_challenge));
	}
}