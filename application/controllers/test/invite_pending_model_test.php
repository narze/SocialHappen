<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite_pending_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('invite_pending_model', 'invite_pending');
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
		$this->invite_pending->drop_collection();
	}
	
	function create_index_before_test(){
		$this->invite_pending->create_index();
	}
	
	function add_test(){
		$user_facebook_id = '123';
		$invite_key = 'abc';
		$campaign_id = 1;
		$facebook_page_id = 1;
		$result = $this->invite_pending->add($user_facebook_id, $campaign_id, $facebook_page_id, $invite_key);
		$this->unit->run($result, 'is_string', 'add', $result);

		$user_facebook_id = '456';
		$invite_key = 'def';
		$campaign_id = '1';
		$facebook_page_id = 1;
		$result = $this->invite_pending->add($user_facebook_id, $campaign_id, $facebook_page_id, $invite_key);
		$this->unit->run($result, 'is_string', 'add with dup invite_key and campaign_id', $result);

		$user_facebook_id = 123;
		$invite_key = 'ghi';
		$campaign_id = 2;
		$facebook_page_id = '1';
		$result = $this->invite_pending->add($user_facebook_id, $campaign_id, $facebook_page_id, $invite_key);
		$this->unit->run($result, 'is_string', 'add with dup user_facebook_id and facebook_page_id', $result);
	}
	
	function add_fail_test(){
		$user_facebook_id = 123;
		$invite_key = 'def';
		$campaign_id = '1';
		$facebook_page_id = 1;
		$result = $this->invite_pending->add($user_facebook_id, $campaign_id, $facebook_page_id, $invite_key);
		$this->unit->run($result, FALSE, 'add with dup user_facebook_id and campaign_id', $result);

		$user_facebook_id = NULL;
		$invite_key = 'def';
		$campaign_id = '1';
		$facebook_page_id = 1;
		$result = $this->invite_pending->add($user_facebook_id, $campaign_id, $facebook_page_id, $invite_key);
		$this->unit->run($result, FALSE, 'add without user_facebook_id', $result);

		$user_facebook_id = 234;
		$invite_key = NULL;
		$campaign_id = '1';
		$facebook_page_id = 1;
		$result = $this->invite_pending->add($user_facebook_id, $campaign_id, $facebook_page_id, $invite_key);
		$this->unit->run($result, FALSE, 'add without invite_key', $result);

		$user_facebook_id = 789;
		$invite_key = 'def';
		$campaign_id = '';
		$facebook_page_id = 1;
		$result = $this->invite_pending->add($user_facebook_id, $campaign_id, $facebook_page_id, $invite_key);
		$this->unit->run($result, FALSE, 'add without campaign_id', $result);

		$user_facebook_id = 789;
		$invite_key = 'def';
		$campaign_id = 1;
		$facebook_page_id = 0;
		$result = $this->invite_pending->add($user_facebook_id, $campaign_id, $facebook_page_id, $invite_key);
		$this->unit->run($result, FALSE, 'add without facebook_page_id', $result);

		$user_facebook_id = 789;
		$invite_key = '';
		$campaign_id = '1';
		$facebook_page_id = 1;
		$result = $this->invite_pending->add($user_facebook_id, $campaign_id, $facebook_page_id, $invite_key);
		$this->unit->run($result, FALSE, 'add blank invite_key', $result);
	}

	function get_invite_key_by_user_facebook_id_and_campaign_id_test(){
		$user_facebook_id = 123;
		$campaign_id = '1';
		$result = $this->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
		$this->unit->run($result, 'abc', 'get_invite_key_by_user_facebook_id_and_campaign_id', $result);

		$user_facebook_id = '456';
		$campaign_id = 1;
		$result = $this->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
		$this->unit->run($result, 'def', 'get_invite_key_by_user_facebook_id_and_campaign_id', $result);
	}

	function get_invite_key_by_user_facebook_id_and_campaign_id_fail_test(){
		$user_facebook_id = 345;
		$campaign_id = '1';
		$result = $this->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
		$this->unit->run($result === NULL, TRUE, 'get_invite_key_by_user_facebook_id_and_campaign_id not found', $result);

		$user_facebook_id = 345;
		$campaign_id = '';
		$result = $this->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
		$this->unit->run($result === FALSE, TRUE, 'get_invite_key_by_user_facebook_id_and_campaign_id without campaign_id', $result);
	}

	function get_by_user_facebook_id_and_campaign_id_test(){
		$user_facebook_id = 123;
		$campaign_id = '1';
		$result = $this->invite_pending->get_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
		$this->unit->run($result['invite_key'], 'abc', 'get_by_user_facebook_id_and_campaign_id_test', $result['invite_key']);
		$this->unit->run($result['facebook_page_id'], 1, 'get_by_user_facebook_id_and_campaign_id_test', $result['facebook_page_id']);
		$user_facebook_id = '456';
		$campaign_id = 1;
		$result = $this->invite_pending->get_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
		$this->unit->run($result['invite_key'], 'def', 'get_by_user_facebook_id_and_campaign_id_test', $result['invite_key']);
		$this->unit->run($result['facebook_page_id'], 1, 'get_by_user_facebook_id_and_campaign_id_test', $result['facebook_page_id']);
	}

	function get_by_user_facebook_id_and_campaign_id_fail_test(){
		$user_facebook_id = 345;
		$campaign_id = '1';
		$result = $this->invite_pending->get_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
		$this->unit->run($result === FALSE, TRUE, 'get_by_user_facebook_id_and_campaign_id_test not found', $result);
	}

	function get_by_user_facebook_id_and_facebook_page_id_test(){
		$user_facebook_id = 123;
		$facebook_page_id = '1';
		$result = $this->invite_pending->get_by_user_facebook_id_and_facebook_page_id($user_facebook_id, $facebook_page_id);
		$this->unit->run(count($result) === 2, TRUE, 'get_by_user_facebook_id_and_facebook_page_id', count($result));
		$this->unit->run($result[0]['invite_key'] === 'abc', TRUE, 'found invite_key abc');
		$this->unit->run($result[1]['invite_key'] === 'ghi', TRUE, 'found invite_key ghi');
	}

	function get_by_user_facebook_id_and_facebook_page_id_fail_test(){
		$user_facebook_id = 555;
		$facebook_page_id = 1;
		$result = $this->invite_pending->get_by_user_facebook_id_and_facebook_page_id($user_facebook_id, $facebook_page_id);
		$this->unit->run(count($result) === 0, TRUE, 'get_by_user_facebook_id_and_facebook_page_id', count($result));
	}

	function remove_by_user_facebook_id_and_campaign_id_test(){
		$user_facebook_id = '123';
		$campaign_id = '1';
		$result = $this->invite_pending->remove_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
		$this->unit->run($result === TRUE, TRUE, 'remove_by_user_facebook_id_and_campaign_id', $result);

		$user_facebook_id = 123;
		$campaign_id = 1;
		$result = $this->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
		$this->unit->run($result === NULL, TRUE, 'get_invite_key_by_user_facebook_id_and_campaign_id', $result);

		$user_facebook_id = 999;
		$campaign_id = 1;
		$result = $this->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
		$this->unit->run($result === NULL, TRUE, 'get_invite_key_by_user_facebook_id_and_campaign_id', $result);
	}
}
/* End of file invite_pending_model_test.php */
/* Location: ./application/controllers/test/invite_pending_model_test.php */