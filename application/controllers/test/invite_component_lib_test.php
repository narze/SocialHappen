<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite_component_lib_test extends CI_Controller {

	private $invite_key1 = NULL;
	private $invite_key2 = NULL;
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('invite_component_lib', NULL, 'invite');
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

	function clear_model_befote_test(){
		$this->load->model('invite_model');
		$this->invite_model->drop_collection();
		$this->load->model('invite_pending_model');
		$this->invite_pending_model->drop_collection();
	}

	function add_invite_test(){
		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = 1;
		$user_facebook_id = 4;
		$commasep_target_facebook_ids = '1,2,3';
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id, $invite_type, $user_facebook_id, $commasep_target_facebook_ids);
		$this->unit->run($result, 'is_string', 'add_invite', $result);
		$this->invite_key1 = $result;

		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = 2;
		$user_facebook_id = 4;
		$commasep_target_facebook_ids = '5,6,7'; //will not be added
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id, $invite_type, $user_facebook_id, $commasep_target_facebook_ids);
		$this->unit->run($result, 'is_string', 'add_invite', $result);
		$this->invite_key2 = $result;
	}
	
	function add_duplicate_invite_test(){
		$campaign_id = 1;
		$app_install_id = 2;
		$facebook_page_id = 3;
		$invite_type = 1;
		$user_facebook_id = 4;
		$commasep_target_facebook_ids = '5,6,7'; // add three first target_ids to list
		$result = $this->invite->add_invite($campaign_id, $app_install_id, $facebook_page_id, $invite_type, $user_facebook_id, $commasep_target_facebook_ids);
		$this->unit->run($result, 'is_string', 'add_duplicate_invite, with same key', $result);
		$this->invite_key1 = $result;

	}
	
	function accept_invite_test(){
		
	}

	function list_invite_test(){
		$criteria = array('campaign_id' => '1');
		$result = $this->invite->list_invite($criteria);
		$this->unit->run($result, 'is_array', 'list_invite', print_r($result, TRUE));
		$this->unit->run(count($result), 2, 'list_invite', count($result));

		$criteria = array('user_facebook_id' => "4");
		$result = $this->invite->list_invite($criteria);
		$this->unit->run($result, 'is_array', 'list_invite', print_r($result, TRUE));
		$this->unit->run(count($result), 2, 'list_invite', count($result));
	}

	function get_invite_by_invite_key_test(){
		$invite_key = $this->invite_key1;
		$result = $this->invite->get_invite_by_invite_key($invite_key);
		$this->unit->run($result, 'is_array', 'get_invite_by_invite_key', print_r($result, TRUE));
		$this->unit->run($result['facebook_page_id'] === '3', TRUE, 'get_invite_by_invite_key', $result['facebook_page_id']);
		$this->unit->run($result['target_facebook_id_list'] === array('1','2','3','5','6','7'), TRUE, 'get_invite_by_invite_key', $result['target_facebook_id_list']);
	}

	function _extract_target_id_test(){
		
	}

	function _generate_invite_key_test(){
		
	}
	
	function generate_redirect_url_test(){ //DEPRECATED
		$facebook_tab_url = 'http://www.facebook.com/pages/SHBeta/135287989899131?sk=app_253512681338518';
		$invite_key = 'rand0mh4shk3y';
		$expected_url = 'http://www.facebook.com/pages/SHBeta/135287989899131?sk=app_253512681338518&app_data=';
		$result = $this->invite->generate_redirect_url($facebook_tab_url, $invite_key);
		$in_pattern = strpos($result, $expected_url) === 0;
		$this->unit->run($in_pattern, TRUE, 'generate_redirect_url', $result);

		$app_data = substr($result, strlen('app_data=') + strpos($result,'app_data='));
		//$app_data = ripped after '?app_data=' from $result
		$app_data = json_decode(urldecode($app_data), TRUE);
		$app_data = $app_data['sh_invite_key'];
		$this->unit->run($invite_key === $app_data, TRUE, 'generate_redirect_url', $app_data);
	}

	function parse_invite_key_from_app_data_test(){ //DEPRECATED
		$facebook_tab_url = 'http://www.facebook.com/pages/SHBeta/135287989899131?sk=app_253512681338518';
		$invite_key = 'rand0mh4shk3y';

		$redirect_url = $this->invite->generate_redirect_url($facebook_tab_url, $invite_key);
		$app_data_string = substr($redirect_url, strlen('app_data=') + strpos($redirect_url,'app_data='));

		$result = $this->invite->parse_invite_key_from_app_data($app_data_string);
		$this->unit->run($result, $invite_key, 'parse_invite_key_from_app_data', $app_data_string.' --> '.$result);
	}

	function reserve_invite_test(){
		$invite_key = $this->invite_key1;
		$user_facebook_id = 1;
		$result = $this->invite->reserve_invite($invite_key, $user_facebook_id);
		$this->unit->run($result, TRUE, 'reserve_invite', $invite_key.' '.$user_facebook_id);

		$result = $this->invite->reserve_invite($invite_key, $user_facebook_id);
		$this->unit->run($result, TRUE, 'reserve_invite again', $invite_key.' '.$user_facebook_id);
	
		$invite_key = $this->invite_key2;
		$user_facebook_id = '1';
		$result = $this->invite->reserve_invite($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'reserve_invite, already accepted on another invite_key (same campaign)', $invite_key.' '.$user_facebook_id);

		$invite_key = $this->invite_key2;
		$user_facebook_id = '3';
		$result = $this->invite->reserve_invite($invite_key, $user_facebook_id);
		$this->unit->run($result, TRUE, 'reserve_invite', $invite_key.' '.$user_facebook_id);
	}

	function accept_invite_page_level_test(){
		$invite_key = $this->invite_key1;
		$target_facebook_id = 1;
		$result = $this->invite->accept_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'accept_invite_page_level', $result);

		$invite_key = $this->invite_key2;
		$target_facebook_id = 3;
		$result = $this->invite->accept_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'accept_invite_page_level', $result);
	}

	function accept_invite_page_level_fail_test(){
		$invite_key = $this->invite_key1;
		$target_facebook_id = 3;
		$result = $this->invite->accept_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_invite_page_level, already accept same page in another invite key', $result);

		$invite_key = $this->invite_key2;
		$target_facebook_id = 3;
		$result = $this->invite->accept_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_invite_page_level, already accept same page in same invite key', $result);
	}

	function accept_invite_campaign_level_test(){
		$invite_key = $this->invite_key1;
		$target_facebook_id = 1;

		$invite = $this->invite_model->get_invite_by_criteria(array('invite_key'=>$invite_key));
		$this->unit->run($invite['invite_count'] === 0, TRUE, 'invite_count', $invite['invite_count']);

		$result = $this->invite_pending_model->get_invite_key_by_user_facebook_id_and_campaign_id($target_facebook_id, 1);
		$this->unit->run($result, TRUE, 'found in pending invite', $result);
		$result = $this->invite->accept_invite_campaign_level($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'accept_invite_campaign_level', $result);
		$result = $this->invite_pending_model->get_invite_key_by_user_facebook_id_and_campaign_id($target_facebook_id, 1);
		$this->unit->run($result, FALSE, 'not found in pending invite', $result);

		$invite = $this->invite_model->get_invite_by_criteria(array('invite_key'=>$invite_key));
		$this->unit->run($invite['invite_count'] === 1, TRUE, 'invite_count', $invite['invite_count']);

		$invite_key = $this->invite_key2;
		$target_facebook_id = 3;
		$invite = $this->invite_model->get_invite_by_criteria(array('invite_key'=>$invite_key));
		$this->unit->run($invite['invite_count'] === 0, TRUE, 'invite_count', $invite['invite_count']);

		$result = $this->invite_pending_model->get_invite_key_by_user_facebook_id_and_campaign_id($target_facebook_id, 1);
		$this->unit->run($result, TRUE, 'found in pending invite', $result);
		$result = $this->invite->accept_invite_campaign_level($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'accept_invite_campaign_level', $result);
		$result = $this->invite_pending_model->get_invite_key_by_user_facebook_id_and_campaign_id($target_facebook_id, 1);
		$this->unit->run($result, FALSE, 'not found in pending invite', $result);

		$invite = $this->invite_model->get_invite_by_criteria(array('invite_key'=>$invite_key));
		$this->unit->run($invite['invite_count'] === 1, TRUE, 'invite_count', $invite['invite_count']);
	}

	function accept_invite_campaign_level_fail_test(){
		$invite_key = $this->invite_key1;
		$target_facebook_id = 3;
		$result = $this->invite->accept_invite_campaign_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_invite_campaign_level, already accept same campaign in another invite key', $result);

		$invite_key = $this->invite_key2;
		$target_facebook_id = 3;
		$result = $this->invite->accept_invite_campaign_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_invite_campaign_level, already accept same campaign in same invite key', $result);
	}

	function reserve_invite_after_registered_campaign_test(){
		$invite_key = $this->invite_key2; //campaign_id 1
		$user_facebook_id = '713558190'; //user 1 is already in campaign 1
		$result = $this->invite->reserve_invite($invite_key, $user_facebook_id);
		$this->unit->run($result, FALSE, 'reserve_invite : failed (user already in this campaign)', $invite_key.' - '.$user_facebook_id);
	}

}
/* End of file invite_component_lib_test.php */
/* Location: ./application/controllers/test/invite_component_lib_test.php */