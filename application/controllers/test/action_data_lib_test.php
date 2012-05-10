<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('QR_ACTION_ID', 201);
define('FEEDBACK_ACTION_ID', 202);
define('CHECKIN_ACTION_ID', 203);
class Action_data_lib_test extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
  	$this->load->library('action_data_lib');
  	$this->unit->reset_dbs();
	}

	function __destruct(){
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

	function get_platform_action_test() {
		$expect = array(
					'qr' => array('id' => 201, 'add_method' => 'add_qr_action_data'),
					'feedback' => array('id' => 202, 'add_method' => 'add_feedback_action_data'),
					'checkin' => array('id' => 203, 'add_method' => 'add_checkin_action_data')
				);
		$result = $this->action_data_lib->get_platform_action();
		$this->unit->run($result, $expect, "\$result", $result);
		
		$action_name = 'qr';
		$result = $this->action_data_lib->get_platform_action($action_name);
		$this->unit->run($result, 201, "\$result", $result);
	}

	function add_action_data_test() {
		$qr_action_id = QR_ACTION_ID;
		$this->qr_action_data = array(
			'example_field_named_complete_message' => 'Some string',
			'another_example_field_named_qr_url' => 'someurl',
		);
		$result = $this->action_data_lib->add_action_data($qr_action_id, $this->qr_action_data);
		$this->unit->run($result, 'is_string', "\$result", $result);
		$this->qr_action_data_id = $result;
	}

	function get_action_data_test() {
		$expect = array(
			'action_id' => QR_ACTION_ID,
			'hash' => strrev(sha1($this->qr_action_data_id)),
			'data' => $this->qr_action_data
		);
		$result = $this->action_data_lib->get_action_data($this->qr_action_data_id);
		$this->unit->run(get_mongo_id($result), $this->qr_action_data_id, "\$result", get_mongo_id($result));
		unset($result['_id']);
		$this->unit->run($result, $expect, "\$result", $result);
	}
	
	function get_action_url_test() {
		$result = $this->action_data_lib->get_action_url($this->qr_action_data_id);
		$expect = base_url().'actions/qr?code='.strrev(sha1($this->qr_action_data_id));
		$this->unit->run($result, $expect, "\$result", $result);
	}

	function get_action_data_from_code_test() {
		$expect = array(
			'action_id' => QR_ACTION_ID,
			'hash' => strrev(sha1($this->qr_action_data_id)),
			'data' => $this->qr_action_data
		);
		$_GET['code'] = strrev(sha1($this->qr_action_data_id));
		$result = $this->action_data_lib->get_action_data_from_code();
		$this->unit->run(get_mongo_id($result), $this->qr_action_data_id, "\$result", get_mongo_id($result));
		unset($result['_id']);
		$this->unit->run($result, $expect, "\$result", $result);

		$_GET['code'] = 'blahblah';
		$result = $this->action_data_lib->get_action_data_from_code();
		$this->unit->run($result, FALSE, "\$result", $result);

		unset($_GET['code']);
		$result = $this->action_data_lib->get_action_data_from_code();
		$this->unit->run($result, FALSE, "\$result", $result);
	}

	function add_qr_action_data_test() {
		$form_data = array(
			'done_message' => 'You have completed this rally point!',
			'todo_message' => 'You have to check in',
			'challenge_id' => 'id'
			// 'qr_url' => base_url().'actions/qr/blahblah?somevar=somecodethatuserwillenter',
		);
		$result = $this->action_data_lib->add_qr_action_data($form_data);
		$this->unit->run($result, 'is_string', "\$result", $result);
		$this->another_qr_action_data_id = $result;

		//Test get url
		$result = $this->action_data_lib->get_action_url($this->another_qr_action_data_id);
		$expect = base_url().'actions/qr?code='.strrev(sha1($this->another_qr_action_data_id));
		$this->unit->run($result, $expect, "\$result", $result);

		//Test get data
		$expect = array(
			'action_id' => QR_ACTION_ID,
			'hash' => strrev(sha1($this->another_qr_action_data_id)),
			'data' => $form_data,
      'challenge_id' => 'id'
		);
		$_GET['code'] = strrev(sha1($this->another_qr_action_data_id));
		$result = $this->action_data_lib->get_action_data_from_code();
		$this->unit->run(get_mongo_id($result), $this->another_qr_action_data_id, "\$result", get_mongo_id($result));
		unset($result['_id']);
    
    $this->unit->run($result['action_id'], $expect['action_id'],
     "\$result['action_id']", $result['action_id']);
     
    $this->unit->run($result['hash'], $expect['hash'],
     "\$result['hash']", $result['hash']);
     
    $this->unit->run($result['data']['done_message'], $expect['data']['done_message'],
     "\$result['data']['done_message']", $result['data']['done_message']);
     
    $this->unit->run($result['data']['todo_message'], $expect['data']['todo_message'],
     "\$result['data']['todo_message']", $result['data']['todo_message']);
    
    $this->unit->run($result['data']['challenge_id'], $expect['data']['challenge_id'],
     "\$result['data']['challenge_id']", $result['data']['challenge_id']);
	}
  
  function get_qr_url_test(){
    $result = $this->action_data_lib->get_qr_url();
    $expect = NULL;
    $this->unit->run($result, $expect, '\$url', $result);
    
    $result = $this->action_data_lib->get_qr_url('4f746aca6803fa3365000057');
    $expect = base_url() . 'actions/qr/?code=4f746aca6803fa3365000057';
    $this->unit->run($result, $expect, '\$url', $result);
  }
  
	function add_feedback_action_data_test() {
		$form_data = array(
						'feedback_welcome_message' => 'Dear, Our Customer',
						'feedback_question_message' => 'What do you think about our store?',
						'feedback_vote_message' => 'Please provide your satisfaction score',
						'feedback_thankyou_message' => 'Thank you, please come again',
					);
		
		$result = $this->action_data_lib->add_feedback_action_data($form_data);
		$this->unit->run($result, 'is_string', "\$result", $result);
		$this->another_feedback_action_data_id = $result;

		//Test get url
		$result = $this->action_data_lib->get_action_url($this->another_feedback_action_data_id);
		$expect = base_url().'actions/feedback?code='.strrev(sha1($this->another_feedback_action_data_id));
		$this->unit->run($result, $expect, "\$result", $result);

		//Test get data
		$expect = array(
			'action_id' => FEEDBACK_ACTION_ID,
			'hash' => strrev(sha1($this->another_feedback_action_data_id)),
			'data' => $form_data
		);
		$_GET['code'] = strrev(sha1($this->another_feedback_action_data_id));
		$result = $this->action_data_lib->get_action_data_from_code();
		$this->unit->run(get_mongo_id($result), $this->another_feedback_action_data_id, "\$result", get_mongo_id($result));
		unset($result['_id']);
		$this->unit->run($result, $expect, "\$result", $result);

	}

	function add_checkin_action_data_test() {
		$form_data = array(
						'checkin_facebook_place_id' => '162135693842364',
						'checkin_facebook_place_name' => 'Figabyte HQ.',
						'checkin_min_friend_count' => 3,
						'checkin_welcome_message' => 'Here you are!',
						'checkin_challenge_message' => 'Please check-in here at Figabyte',
						'checkin_thankyou_message' => 'Thank you, for check-in',
					);
		
		$result = $this->action_data_lib->add_checkin_action_data($form_data);
		$this->unit->run($result, 'is_string', "\$result", $result);
		$this->another_checkin_action_data_id = $result;

		//Test get url
		$result = $this->action_data_lib->get_action_url($this->another_checkin_action_data_id);
		$expect = base_url().'actions/checkin?code='.strrev(sha1($this->another_checkin_action_data_id));
		$this->unit->run($result, $expect, "\$result", $result);

		//Test get data
		$expect = array(
			'action_id' => CHECKIN_ACTION_ID,
			'hash' => strrev(sha1($this->another_checkin_action_data_id)),
			'data' => $form_data
		);
		$_GET['code'] = strrev(sha1($this->another_checkin_action_data_id));
		$result = $this->action_data_lib->get_action_data_from_code();
		$this->unit->run(get_mongo_id($result), $this->another_checkin_action_data_id, "\$result", get_mongo_id($result));
		unset($result['_id']);
		$this->unit->run($result, $expect, "\$result", $result);

	}
}
/* End of file action_data_lib_test.php */
/* Location: ./application/controllers/test/action_data_lib_test.php */