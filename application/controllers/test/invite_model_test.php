<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('invite_model', 'invite');
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
		$this->unit->run($result['redirect_url'] == 'https://facebook.com', TRUE, 'get_invite_by_criteria', $result);
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
}
/* End of file invite_model_test.php */
/* Location: ./application/controllers/test/invite_model_test.php */