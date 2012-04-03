<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('QR_ACTION_ID', 201);
define('FEEDBACK_ACTION_ID', 202);

define('ACTIONDATAMONGOID1', 'dummyactiondatamongoid1');
define('ACTIONDATAMONGOID2', 'dummyactiondatamongoid2');
define('CHALLENGEMONGOID1', 'dummychallengemongoid1');
define('CHALLENGEMONGOID2', 'dummychallengemongoid2');
define('USERID1', 1);
define('USERID2', 2);
define('COMPANYID1', 1);
define('COMPANYID2', 2);


class Action_user_data_lib_test extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
	  	$this->load->library('action_user_data_lib');
	  	$this->unit->reset_dbs();
	}

	function __destruct(){
		$this->load->model('action_user_data_model');
		$this->action_user_data_model->delete(array('_id' => new MongoId($this->action_user_data_id1)));
		$this->action_user_data_model->delete(array('_id' => new MongoId($this->action_user_data_id2)));
		$this->unit->report_with_counter();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)/",$method)){
    			$this->$method();
    		}
		}
	}

	function add_action_user_data_test() {
		$company_id = COMPANYID1;
		$this->action_id = QR_ACTION_ID;
		$action_data_id = ACTIONDATAMONGOID1;
		$challenge_id = CHALLENGEMONGOID1;
		$user_id = USERID1;
		$this->user_data1 = 'dummystring';

		$result = $this->action_user_data_lib->add_action_user_data($company_id, $this->action_id, $action_data_id, $challenge_id, $user_id, $this->user_data1);
		$this->unit->run($result, 'is_string', "\$result", $result);
		$this->action_user_data_id1 = $result;

		$company_id = COMPANYID2;
		$this->action_id2 = FEEDBACK_ACTION_ID;
		$action_data_id = ACTIONDATAMONGOID2;
		$challenge_id = CHALLENGEMONGOID2;
		$user_id = USERID2;
		$this->user_data2 = array(
							'field1' => 'dummyfield1',
							'field2' => 'dummyfield2',
						);


		$result = $this->action_user_data_lib->add_action_user_data($company_id, $this->action_id2, $action_data_id, $challenge_id, $user_id, $this->user_data2);
		$this->unit->run($result, 'is_string', "\$result", $result);
		$this->action_user_data_id2 = $result; 

		//fail test
		$result = $this->action_user_data_lib->add_action_user_data($company_id, $this->action_id2, $action_data_id, $challenge_id, $user_id, NULL);
		$this->unit->run($result, 'is_false', "\$result", $result);
	}

	function get_action_user_data_test() {
		$this->expect1 = array(
			'company_id' => COMPANYID1,
			'action_id' => QR_ACTION_ID,
			'action_data_id' => ACTIONDATAMONGOID1,
			'challenge_id' => CHALLENGEMONGOID1,
			'user_id' => USERID1,
			'user_data' => $this->user_data1
		);
		$result = $this->action_user_data_lib->get_action_user_data($this->action_user_data_id1);
		$this->unit->run(get_mongo_id($result), $this->action_user_data_id1, "\$result", get_mongo_id($result));
		unset($result['_id']);
		$this->unit->run($result, $this->expect1, "\$result", $result);

		$this->expect2 = array(
			'company_id' => COMPANYID2,
			'action_id' => FEEDBACK_ACTION_ID,
			'action_data_id' => ACTIONDATAMONGOID2,
			'challenge_id' => CHALLENGEMONGOID2,
			'user_id' => USERID2,
			'user_data' => $this->user_data2
		);
		$result = $this->action_user_data_lib->get_action_user_data($this->action_user_data_id2);
		$this->unit->run(get_mongo_id($result), $this->action_user_data_id2, "\$result", get_mongo_id($result));
		unset($result['_id']);
		$this->unit->run($result, $this->expect2, "\$result", $result);

		//fail test
		$result = $this->action_user_data_lib->get_action_user_data('ultragaaaaayyyyyy');
		$this->unit->run($result, 'is_false', "\$result", $result);
	}

	function get_action_user_data_by_company_test() {
		$result = $this->action_user_data_lib->get_action_user_data_by_company(COMPANYID1);
		
		unset($result['_id']);
		foreach ($result as $action_user_data) {
			if(get_mongo_id($action_user_data) == $this->action_user_data_id1){
				$this->unit->run(get_mongo_id($action_user_data), $this->action_user_data_id1, "same object id", get_mongo_id($action_user_data));
				unset($action_user_data['_id']);
				$this->unit->run($action_user_data, $this->expect1, "same object", $action_user_data);
			}
		}

		$result = $this->action_user_data_lib->get_action_user_data_by_company(COMPANYID2);
		
		unset($result['_id']);
		foreach ($result as $action_user_data) {
			if(get_mongo_id($action_user_data) == $this->action_user_data_id1){
				$this->unit->run(get_mongo_id($action_user_data), $this->action_user_data_id2, "same object id", get_mongo_id($action_user_data));
				unset($action_user_data['_id']);
				$this->unit->run($action_user_data, $this->expect2, "same object", $action_user_data);
			}
		}

		//fail test
		$result = $this->action_user_data_lib->get_action_user_data_by_company('ultragaaaaayyyyyy');
		$this->unit->run(sizeof($result) > 0, 'is_false', "\$result", $result);
	}


	function get_action_user_data_by_action_test() {
		$result = $this->action_user_data_lib->get_action_user_data_by_action($this->action_id);
		
		unset($result['_id']);
		foreach ($result as $action_user_data) {
			if(get_mongo_id($action_user_data) == $this->action_user_data_id1){
				$this->unit->run(get_mongo_id($action_user_data), $this->action_user_data_id1, "same object id", get_mongo_id($action_user_data));
				unset($action_user_data['_id']);
				$this->unit->run($action_user_data, $this->expect1, "same object", $action_user_data);
			}
		}
		
		$result = $this->action_user_data_lib->get_action_user_data_by_action($this->action_id2);
		
		unset($result['_id']);
		foreach ($result as $action_user_data) {
			if(get_mongo_id($action_user_data) == $this->action_user_data_id1){
				$this->unit->run(get_mongo_id($action_user_data), $this->action_user_data_id2, "same object id", get_mongo_id($action_user_data));
				unset($action_user_data['_id']);
				$this->unit->run($action_user_data, $this->expect2, "same object", $action_user_data);
			}
		}

		//fail test
		$result = $this->action_user_data_lib->get_action_user_data_by_action('ultragaaaaayyyyyy');
		$this->unit->run(sizeof($result) > 0, 'is_false', "\$result", $result);
	}

	function get_action_user_data_by_action_data_test() {
		$result = $this->action_user_data_lib->get_action_user_data_by_action_data(ACTIONDATAMONGOID1);
		
		unset($result['_id']);
		foreach ($result as $action_user_data) {
			if(get_mongo_id($action_user_data) == $this->action_user_data_id1){
				$this->unit->run(get_mongo_id($action_user_data), $this->action_user_data_id1, "same object id", get_mongo_id($action_user_data));
				unset($action_user_data['_id']);
				$this->unit->run($action_user_data, $this->expect1, "same object", $action_user_data);
			}
		}
		$result = $this->action_user_data_lib->get_action_user_data_by_action_data(ACTIONDATAMONGOID2);
		
		unset($result['_id']);
		foreach ($result as $action_user_data) {
			if(get_mongo_id($action_user_data) == $this->action_user_data_id1){
				$this->unit->run(get_mongo_id($action_user_data), $this->action_user_data_id2, "same object id", get_mongo_id($action_user_data));
				unset($action_user_data['_id']);
				$this->unit->run($action_user_data, $this->expect2, "same object", $action_user_data);
			}
		}

		//fail test
		$result = $this->action_user_data_lib->get_action_user_data_by_action_data('ultragaaaaayyyyyy');
		$this->unit->run(sizeof($result) > 0, 'is_false', "\$result", $result);
	}

	function get_action_user_data_by_challenge_test() {
		$result = $this->action_user_data_lib->get_action_user_data_by_challenge(CHALLENGEMONGOID1);
		
		unset($result['_id']);
		
		foreach ($result as $action_user_data) {
			if(get_mongo_id($action_user_data) == $this->action_user_data_id1){
				$this->unit->run(get_mongo_id($action_user_data), $this->action_user_data_id1, "same object id", get_mongo_id($action_user_data));
				unset($action_user_data['_id']);
				$this->unit->run($action_user_data, $this->expect1, "same object", $action_user_data);
			}
		}
		$result = $this->action_user_data_lib->get_action_user_data_by_challenge(CHALLENGEMONGOID2);
		
		unset($result['_id']);
		foreach ($result as $action_user_data) {
			if(get_mongo_id($action_user_data) == $this->action_user_data_id1){
				$this->unit->run(get_mongo_id($action_user_data), $this->action_user_data_id2, "same object id", get_mongo_id($action_user_data));
				unset($action_user_data['_id']);
				$this->unit->run($action_user_data, $this->expect2, "same object", $action_user_data);
			}
		}

		//fail test
		$result = $this->action_user_data_lib->get_action_user_data_by_challenge('ultragaaaaayyyyyy');
		$this->unit->run(sizeof($result) > 0, 'is_false', "\$result", $result);
	}

}
/* End of file action_data_lib_test.php */
/* Location: ./application/controllers/test/action_data_lib_test.php */