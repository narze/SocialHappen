<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('invite_model', 'invite');
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

	function clear_data_before_test(){
		$this->invite->drop_collection();
	}
	
	function create_index_before_test(){
		$this->invite->create_index();
	}
	
	function add_invite_test(){
		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = 1;
		$user_facebook_id = 4;
		$target_facebook_id_list = array(
			'10','11','12','13'
		);
		$invite_key = 'asdfjkl;1';
		$redirect_url = 'https://google.com';
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id
			, $invite_type, $user_facebook_id, $target_facebook_id_list
			, $invite_key, $redirect_url);
		
		$this->unit->run($result, TRUE, 'add_invite', $result);

		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = 2;
		$user_facebook_id = 4;
		$target_facebook_id_list = array(
			'10','11','12','13'
		);
		$invite_key = 'asdfjkl;2';
		$redirect_url = 'https://google.com';
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id
			, $invite_type, $user_facebook_id, $target_facebook_id_list
			, $invite_key, $redirect_url);
		
		$this->unit->run($result, TRUE, 'add_invite', $result);
	}
	
	function add_invite_fail_test(){
		$campaign_id = NULL;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = 1;
		$user_facebook_id = 4;
		$target_facebook_id_list = array(
			'10','11','12','13'
		);
		$invite_key = 'asdfjkl;';
		$redirect_url = 'https://google.com';
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id
			, $invite_type, $user_facebook_id, $target_facebook_id_list
			, $invite_key, $redirect_url);
		
		$this->unit->run($result, FALSE, 'add_invite', $result, 'no campaign id');
		
		$campaign_id = 1;
		$app_install_id = NULL;
		$facebook_page_id = 3;
		$invite_type = 1;
		$user_facebook_id = 4;
		$target_facebook_id_list = array(
			'10','11','12','13'
		);
		$invite_key = 'asdfjkl;';
		$redirect_url = 'https://google.com';
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id
			, $invite_type, $user_facebook_id, $target_facebook_id_list
			, $invite_key, $redirect_url);
		
		$this->unit->run($result, FALSE, 'add_invite', $result, 'no app_install_id');
		
		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = NULL;
		$invite_type = 1;
		$user_facebook_id = 4;
		$target_facebook_id_list = array(
			'10','11','12','13'
		);
		$invite_key = 'asdfjkl;';
		$redirect_url = 'https://google.com';
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id
			, $invite_type, $user_facebook_id, $target_facebook_id_list
			, $invite_key, $redirect_url);
		
		$this->unit->run($result, FALSE, 'add_invite', $result, 'no facebook_page_id');
		
		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = NULL;
		$user_facebook_id = 4;
		$target_facebook_id_list = array(
			'10','11','12','13'
		);
		$invite_key = 'asdfjkl;';
		$redirect_url = 'https://google.com';
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id
			, $invite_type, $user_facebook_id, $target_facebook_id_list
			, $invite_key, $redirect_url);
		
		$this->unit->run($result, FALSE, 'add_invite', $result, 'no invite type');
		
		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = 1;
		$user_facebook_id = NULL;
		$target_facebook_id_list = array(
			'10','11','12','13'
		);
		$invite_key = 'asdfjkl;';
		$redirect_url = 'https://google.com';
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id
			, $invite_type, $user_facebook_id, $target_facebook_id_list
			, $invite_key, $redirect_url);
		
		$this->unit->run($result, FALSE, 'add_invite', $result, 'not inviter facebook id');
		
		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = 1;
		$user_facebook_id = NULL;
		$target_facebook_id_list = array(
			'10','11','12','13'
		);
		$invite_key = 'asdfjkl;';
		$redirect_url = NULL;
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id
			, $invite_type, $user_facebook_id, $target_facebook_id_list
			, $invite_key, $redirect_url);
		
		$this->unit->run($result, FALSE, 'add_invite', $result, 'no redirect url');
		
		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = 1;
		$user_facebook_id = 4;
		$target_facebook_id_list = NULL;
		$invite_key = 'asdfjkl;';
		$redirect_url = 'https://google.com';
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id
			, $invite_type, $user_facebook_id, $target_facebook_id_list
			, $invite_key, $redirect_url);
		
		$this->unit->run($result, FALSE, 'add_invite', $result, 'no target list on private invite');

		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = 2;
		$user_facebook_id = 4;
		$target_facebook_id_list = array(
			'10','11','12','13'
		);
		$invite_key = 'asdfjkl;2';
		$redirect_url = 'https://google.com';
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id
			, $invite_type, $user_facebook_id, $target_facebook_id_list
			, $invite_key, $redirect_url);
		
		$this->unit->run($result, FALSE, 'add_invite', $result, 'duplicated invite_key');
	}

	function get_invite_by_criteria_test(){
		$result = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;1'));
		$this->unit->run($result, 'is_array', 'get_invite_by_criteria', $result);
		$this->unit->run($result['campaign_id'] === 1, TRUE, 'campaign_id', $result['campaign_id']);
		$this->unit->run($result['timestamp'], 'is_int', 'timestamp', $result['timestamp']);

		$result = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;2'));
		$this->unit->run($result, 'is_array', 'get_invite_by_criteria', $result);
		$this->unit->run($result['campaign_id'] === 1, TRUE, 'campaign_id', $result['campaign_id']);
		$this->unit->run($result['target_facebook_id_list'], NULL, 'target_facebook_id_list is null due to public invite mode', $result['target_facebook_id_list']);
	}

	function get_invite_by_criteria_fail_test(){
		$result = $this->invite->get_invite_by_criteria(array('invite_key' => 'willfoundnothing'));
		$this->unit->run($result, FALSE, 'get_invite_by_criteria', $result);
	}

	function update_invite_test(){
		$result = $this->invite->update_invite('asdfjkl;1', array(
			'target_facebook_id_list'=> array(
				'21','22','23'
			),
			'redirect_url' => 'https://facebook.com'
		));
		$this->unit->run($result, TRUE, 'update_invite', $result);

		$result = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;1'));
		$this->unit->run($result['target_facebook_id_list'] == array(
				'21','22','23'
			), TRUE, 'get_invite_by_criteria', $result);
		
	}


	function update_invite_fail_test(){
		$result = $this->invite->update_invite('asdfjkl;1', array('target_facebook_id_list'=> array(
				'21','22','23'
			)));
		$this->unit->run($result, TRUE, 'update_invite', $result);

		$result = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;1'));
		$this->unit->run($result['target_facebook_id_list'] == array(
				'21','22','23'
			), TRUE, 'get_invite_by_criteria', $result);
		$this->unit->run($result['app_install_id'] == 2, TRUE, 'get_invite_by_criteria', $result);
	}

	function list_invites_test(){
		$result = $this->invite->list_invites(array('campaign_id' => 1,'app_install_id' => 2));
		$this->unit->run($result, 'is_array', 'list_invites', $result);
		$this->unit->run(count($result), 2, 'count list_invites result', count($result));
	}

	function push_into_campaign_accepted_test(){
		$invite_key = 'asdfjkl;1';
		$user_facebook_id = 21;
		$result = $this->invite->push_into_campaign_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, TRUE, 'push_into_campaign_accepted');
		$result = $this->invite->push_into_campaign_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'push_into_campaign_accepted, duplicated');

		$invite_key = 'asdfjkl;1';
		$user_facebook_id = 55;
		$result = $this->invite->push_into_campaign_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'push_into_campaign_accepted, 55 is not in list 21,22,23');

		$result = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;1'));
		$this->unit->run(in_array(21, $result['campaign_accepted']), TRUE);
		$this->unit->run(in_array(55, $result['campaign_accepted']), FALSE);

		$invite_key = 'asdfjkl;2';
		$user_facebook_id = 55;
		$result = $this->invite->push_into_campaign_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, TRUE, 'push_into_campaign_accepted, public invite mode');
		$result = $this->invite->push_into_campaign_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'push_into_campaign_accepted, public invite mode, duplicated');

		$result = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;2'));
		$this->unit->run(in_array(55, $result['campaign_accepted']), TRUE);


	}

	function push_into_campaign_accepted_fail_test(){
		$invite_key = '';
		$user_facebook_id = 22;
		$result = $this->invite->push_into_campaign_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'push_into_campaign_accepted withour invite_key');

		$invite_key = 'asdfjkl;1';
		$user_facebook_id = NULL;
		$result = $this->invite->push_into_campaign_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'push_into_campaign_accepted withour user_facebook_id');
	}

	function push_into_page_accepted_test(){
		$invite_key = 'asdfjkl;1';
		$user_facebook_id = 21;
		$result = $this->invite->push_into_page_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, TRUE, 'push_into_page_accepted');
		$result = $this->invite->push_into_page_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'push_into_page_accepted, duplicated');

		$invite_key = 'asdfjkl;1';
		$user_facebook_id = 55;
		$result = $this->invite->push_into_page_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'push_into_page_accepted, 55 is not in list 21,22,23');

		$result = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;1'));
		$this->unit->run(in_array(21, $result['page_accepted']), TRUE);
		$this->unit->run(in_array(55, $result['page_accepted']), FALSE);

		$invite_key = 'asdfjkl;2';
		$user_facebook_id = 55;
		$result = $this->invite->push_into_page_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, TRUE, 'push_into_page_accepted, public invite mode');
		$result = $this->invite->push_into_page_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'push_into_page_accepted, public invite mode, duplicated');

		$result = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;2'));
		$this->unit->run(in_array(55, $result['page_accepted']), TRUE);
	}

	function push_into_page_accepted_fail_test(){
		$invite_key = '';
		$user_facebook_id = 22;
		$result = $this->invite->push_into_page_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'push_into_page_accepted withour invite_key');

		$invite_key = 'asdfjkl;1';
		$user_facebook_id = NULL;
		$result = $this->invite->push_into_page_accepted($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'push_into_page_accepted withour user_facebook_id');
	}

	function increment_invite_count_by_invite_key_test(){
		$invite_key = 'asdfjkl;1';
		$result = $this->invite->increment_invite_count_by_invite_key($invite_key);
		$this->unit->run($result, TRUE, 'increment_invite_count_by_invite_key', $invite_key);
		$invite_key = NULL;
		$result = $this->invite->increment_invite_count_by_invite_key($invite_key);
		$this->unit->run($result, FALSE, 'increment_invite_count_by_invite_key', $invite_key);
		$invite_key = 'youcantfindme';
		$result = $this->invite->increment_invite_count_by_invite_key($invite_key);
		$this->unit->run($result, FALSE, 'increment_invite_count_by_invite_key', $invite_key);
	}

	function add_into_target_facebook_id_list_test(){
		$invite_key = 'asdfjkl;1';
		$facebook_id_array = array(22,23,24,25);
		$expected_target_facebook_id_list = array('21','22','23','24','25');
		$result = $this->invite->add_into_target_facebook_id_list($invite_key, $facebook_id_array);
		$this->unit->run($result, TRUE, 'add_into_target_facebook_id_list', $invite_key. ' : '. print_r($facebook_id_array, TRUE));

		$result = $this->invite->get_invite_by_criteria(array('invite_key' => $invite_key));
		$this->unit->run(count($result['target_facebook_id_list']), count($expected_target_facebook_id_list), 'count target_facebook_id_list', print_r($result['target_facebook_id_list'], TRUE));

		$invite_key = 'asdfjkl;2'; //public invite
		$facebook_id_array = array(23,24,25);
		$expected_target_facebook_id_list = array('23','24','25');
		$result = $this->invite->add_into_target_facebook_id_list($invite_key, $facebook_id_array);
		$this->unit->run($result, TRUE, 'add_into_target_facebook_id_list with public invite', $invite_key);
		$result = $this->invite->get_invite_by_criteria(array('invite_key' => $invite_key));
		$this->unit->run(count($result['target_facebook_id_list']), count($expected_target_facebook_id_list), 'count target_facebook_id_list', print_r($result['target_facebook_id_list'], TRUE));

		$invite_key = 'asdfjkl;1';
		$facebook_id_array = array(21,23,24);
		$expected_target_facebook_id_list = array('21','22','23','24','25');
		$result = $this->invite->add_into_target_facebook_id_list($invite_key, $facebook_id_array);
		$this->unit->run($result, TRUE, 'add_into_target_facebook_id_list : all already in the list', $invite_key. ' : '. print_r($facebook_id_array, TRUE));

		$result = $this->invite->get_invite_by_criteria(array('invite_key' => $invite_key));
		$this->unit->run(count($result['target_facebook_id_list']), count($expected_target_facebook_id_list), 'count target_facebook_id_list');
	}

	function get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list_test(){
		$facebook_page_id = '3';
		$user_facebook_id = 21;
		$result = $this->invite->get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list($facebook_page_id, $user_facebook_id);
		$this->unit->run(count($result) === 1, TRUE, 'get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list_test');
		$this->unit->run($result[0]['invite_key'] === 'asdfjkl;1', TRUE, 'get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list_test');
	}

	function get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list_fail_test(){
		$facebook_page_id = NULL;
		$user_facebook_id = 21;
		$result = $this->invite->get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list($facebook_page_id, $user_facebook_id);
		$this->unit->run($result, FALSE, 'get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list_test : no facebook_page_id');

		$facebook_page_id = 1;
		$user_facebook_id = 21;
		$result = $this->invite->get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list($facebook_page_id, $user_facebook_id);
		$this->unit->run($result, FALSE, 'get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list_test : cannot find page');

		$facebook_page_id = 3;
		$user_facebook_id = '10';
		$result = $this->invite->get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list($facebook_page_id, $user_facebook_id);
		$this->unit->run($result, FALSE, 'get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list_test : no user_facebook_id in the list');
	}

	function push_into_all_page_accepted_test(){
		$invite_keys = array('asdfjkl;2','asdfjkl;1');
		$user_facebook_id = 1234;
		$result = $this->invite->push_into_all_page_accepted($user_facebook_id,$invite_keys);
		$this->unit->run($result, TRUE, 'push_into_all_page_accepted');

		//Test
		$invite = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;1'));
		$this->unit->run(in_array('1234',$invite['page_accepted']), TRUE, 'test push_into_all_page_accepted', print_r($invite['page_accepted'],TRUE));
		$page_accepted_count = count($invite['page_accepted']);

		//Test
		$invite = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;2'));
		$this->unit->run(in_array('1234',$invite['page_accepted']), TRUE, 'test push_into_all_page_accepted', print_r($invite['page_accepted'],TRUE));

		//Push again
		$invite_keys = array('asdfjkl;1');
		$user_facebook_id = 1234;
		$result = $this->invite->push_into_all_page_accepted($user_facebook_id,$invite_keys);
		$this->unit->run($result, TRUE, 'push_into_all_page_accepted');
		$invite = $this->invite->get_invite_by_criteria(array('invite_key' => 'asdfjkl;1'));
		$this->unit->run(count($invite['page_accepted'])===$page_accepted_count, TRUE, 'When pushed again, no more user_facebook_id pushed');
	}

	function push_into_all_page_accepted_fail_test(){
		$invite_keys = array('');
		$user_facebook_id = 1235;
		$result = $this->invite->push_into_all_page_accepted($user_facebook_id,$invite_keys);
		$this->unit->run($result, FALSE, 'push_into_all_page_accepted');

		$invite_keys = array();
		$user_facebook_id = 1236;
		$result = $this->invite->push_into_all_page_accepted($user_facebook_id,$invite_keys);
		$this->unit->run($result, FALSE, 'push_into_all_page_accepted');
	}
}
/* End of file invite_model_test.php */
/* Location: ./application/controllers/test/invite_model_test.php */