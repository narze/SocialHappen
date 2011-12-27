<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		require_once 'MockMe.php';
		use \Mockery as m;
class Invite_component_lib_test extends CI_Controller {

	private $invite_key1 = NULL;
	private $invite_key2 = NULL;
	private $invite_key3 = NULL;

	private $FBPAGEID1 = '135287989899131';
	private $FBPAGEID2 = '116586141725712';
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('invite_component_lib');
		$this->load->library('app_component_lib');
		$this->load->model('user_model');
		$this->load->model('page_model');
		$this->unit->reset_dbs();
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

	function add_invite_component_before_test(){
		//Add app component (campaign_id = 1)
		$campaign_id = 1;
	    $app_id = 1;
	    $app_install_id = 1;
	    $page_id = 1;
	    $app_component_data = array(
	      'campaign_id' => $campaign_id,
	      'invite' => array(
	        'facebook_invite' => TRUE,
	        'email_invite' => TRUE,
	        'criteria' => array(
	          'score' => 1,
	          'maximum' => 7,
	          'cooldown' => 300,
	          'acceptance_score' => array(
	            'page' => 100,
	            'campaign' => 20
	          )
	        ),
	        'message' => array(
	          'title' => 'You are invited',
	          'text' => 'Welcome to the campaign',
	          'image' => 'https://localhost/assets/images/blank.png'
	        )
	      ),
	      'sharebutton' => array(
	        'facebook_button' => TRUE,
	        'twitter_button' => TRUE,
	        'criteria' => array(
	          'score' => 1,
	          'maximum' => 5,
	          'cooldown' => 300
	        ),
	        'message' => array(
	          'title' => 'Join the campaign by this link',
	          'caption' => 'This is caption',
	          'text' => 'this is long description',
	          'image' => 'https://localhost/assets/images/blank.png',
	        )
	      )
	    );

	    $result = $this->app_component_lib->add_campaign($app_component_data);
	    $this->unit->run($result, TRUE,'Add app_component with full data', print_r($result, TRUE));
	}

	function add_invite_test(){
		$campaign_id = 1;
		$app_install_id = 1;
		$facebook_page_id = $this->FBPAGEID1;
		$invite_type = 1;
		$user_facebook_id = '713558190';
		$commasep_target_facebook_ids = '1,2,3';
		$result = $this->invite_component_lib->add_invite($campaign_id, $app_install_id, $facebook_page_id, $invite_type, $user_facebook_id, $commasep_target_facebook_ids);

		$this->unit->run($result, 'is_string', 'add_invite', $result);
		$this->invite_key1 = $result;

		$criteria = array('campaign_id' => '1');
		$result = $this->invite_component_lib->list_invite($criteria);
		$this->unit->run(count($result), 1, 'list_invite', count($result));

		$campaign_id = 1;
		$app_install_id = 1;
		$facebook_page_id = $this->FBPAGEID1;
		$invite_type = 2;
		$user_facebook_id = '713558190';
		$commasep_target_facebook_ids = '5,6,7'; //will not be added
		$result = $this->invite_component_lib->add_invite($campaign_id, $app_install_id, $facebook_page_id, $invite_type, $user_facebook_id, $commasep_target_facebook_ids);
		$this->unit->run($result, 'is_string', 'add_invite', $result);
		$this->invite_key2 = $result;

		$criteria = array('campaign_id' => '1');
		$result = $this->invite_component_lib->list_invite($criteria);
		$this->unit->run(count($result), 2, 'list_invite', count($result));
	}
	
	function add_duplicate_invite_test(){
		$campaign_id = 1;
		$app_install_id = 1;
		$facebook_page_id = $this->FBPAGEID1;
		$invite_type = 1;
		$user_facebook_id = '713558190';
		$commasep_target_facebook_ids = '5,6,7'; // add three first target_ids to list
		$result = $this->invite_component_lib->add_invite($campaign_id, $app_install_id, $facebook_page_id, $invite_type, $user_facebook_id, $commasep_target_facebook_ids);
		$this->unit->run($result, 'is_string', 'add_duplicate_invite, with same key', $result);
		$this->invite_key1 = $result;

		$criteria = array('campaign_id' => '1');
		$result = $this->invite_component_lib->list_invite($criteria);
		$this->unit->run(count($result), 2, 'list_invite', count($result));

		$campaign_id = 1;
		$app_install_id = 1;
		$facebook_page_id = $this->FBPAGEID1;
		$invite_type = 1;
		$user_facebook_id = '713558190';
		$commasep_target_facebook_ids = '5,6,7,9'; // add only 9 to list
		$result = $this->invite_component_lib->add_invite($campaign_id, $app_install_id, $facebook_page_id, $invite_type, $user_facebook_id, $commasep_target_facebook_ids);
		$this->unit->run($result, $this->invite_key1, 'add_duplicate_invite, with same key', $result);

		$criteria = array('campaign_id' => '1');
		$result = $this->invite_component_lib->list_invite($criteria);
		$this->unit->run(count($result), 2, 'list_invite', count($result));

		$campaign_id = 1;
		$app_install_id = 1;
		$facebook_page_id = $this->FBPAGEID1;
		$invite_type = 2;
		$user_facebook_id = '713558190';
		$commasep_target_facebook_ids = '5,6,7,8,9'; //will not be added
		$result = $this->invite_component_lib->add_invite($campaign_id, $app_install_id, $facebook_page_id, $invite_type, $user_facebook_id, $commasep_target_facebook_ids);
		$this->unit->run($result, $this->invite_key2, 'add_invite', $result);

		$criteria = array('campaign_id' => '1');
		$result = $this->invite_component_lib->list_invite($criteria);
		$this->unit->run(count($result), 2, 'list_invite', count($result));
	}

	function list_invite_test(){
		$criteria = array('campaign_id' => '1');
		$result = $this->invite_component_lib->list_invite($criteria);
		$this->unit->run($result, 'is_array', 'list_invite', print_r($result, TRUE));
		$this->unit->run(count($result), 2, 'list_invite', count($result));

		$criteria = array('user_facebook_id' => '713558190');
		$result = $this->invite_component_lib->list_invite($criteria);
		$this->unit->run($result, 'is_array', 'list_invite', print_r($result, TRUE));
		$this->unit->run(count($result), 2, 'list_invite', count($result));
	}

	function get_invite_by_invite_key_test(){
		$invite_key = $this->invite_key1;
		$result = $this->invite_component_lib->get_invite_by_invite_key($invite_key);
		$this->unit->run($result, 'is_array', 'get_invite_by_invite_key', print_r($result, TRUE));
		$this->unit->run($result['facebook_page_id'] === $this->FBPAGEID1, TRUE, 'get_invite_by_invite_key', $result['facebook_page_id']);
		$this->unit->run($result['target_facebook_id_list'] === array('1','2','3','5','6','7','9'), TRUE, 'get_invite_by_invite_key', $result['target_facebook_id_list']);
	}

	function _extract_target_id_test(){
		
	}

	function _generate_invite_key_test(){
		
	}
	
	function generate_redirect_url_test(){ //DEPRECATED
		$facebook_tab_url = 'http://www.facebook.com/pages/SHBeta/135287989899131?sk=app_253512681338518';
		$invite_key = 'rand0mh4shk3y';
		$expected_url = 'http://www.facebook.com/pages/SHBeta/135287989899131?sk=app_253512681338518&app_data=';
		$result = $this->invite_component_lib->generate_redirect_url($facebook_tab_url, $invite_key);
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

		$redirect_url = $this->invite_component_lib->generate_redirect_url($facebook_tab_url, $invite_key);
		$app_data_string = substr($redirect_url, strlen('app_data=') + strpos($redirect_url,'app_data='));

		$result = $this->invite_component_lib->parse_invite_key_from_app_data($app_data_string);
		$this->unit->run($result, $invite_key, 'parse_invite_key_from_app_data', $app_data_string.' --> '.$result);
	}

	function reserve_invite_test(){
		$invite_key = $this->invite_key1;
		$user_facebook_id = 1;
		$result = $this->invite_component_lib->reserve_invite($invite_key, $user_facebook_id);
		$this->unit->run($result, TRUE, 'reserve_invite', $invite_key.' '.$user_facebook_id);

		$result = $this->invite_component_lib->reserve_invite($invite_key, $user_facebook_id);
		$this->unit->run($result, TRUE, 'reserve_invite again', $invite_key.' '.$user_facebook_id);

		$invite_key = $this->invite_key2;
		$user_facebook_id = '1';
		$result_status = TRUE;
		$result = $this->invite_component_lib->reserve_invite($invite_key, $user_facebook_id);
		
		$result_status = TRUE;
		if(isset($result['error']))
			$result_status = FALSE;
		$this->unit->run($result_status, FALSE, 'reserve_invite, already accepted on another invite_key (same campaign)', $invite_key.' '.$user_facebook_id);

		$invite_key = $this->invite_key2;
		$user_facebook_id = '3';
		$result = $this->invite_component_lib->reserve_invite($invite_key, $user_facebook_id);
		$this->unit->run($result, TRUE, 'reserve_invite', $invite_key.' '.$user_facebook_id);

		//user_facebook_id added in target_facebook_id_list (public invite)
		$invite = $this->invite_component_lib->get_invite_by_invite_key($this->invite_key2);
		$this->unit->run(count($invite['target_facebook_id_list']),1 ,'count target_facebook_id_list', count($invite['target_facebook_id_list']));
		$this->unit->run(in_array($user_facebook_id,$invite['target_facebook_id_list']), TRUE,'target_facebook_id_list', print_r($invite['target_facebook_id_list'],TRUE));
	}

	function accept_invite_page_level_test(){
		$invite_key = $this->invite_key1;
		$target_facebook_id = 1;
		$result = $this->invite_component_lib->accept_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'accept_invite_page_level', $result);

		$invite_key = $this->invite_key2;
		$target_facebook_id = 3;
		$result = $this->invite_component_lib->accept_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'accept_invite_page_level', $result);
	}

	function accept_invite_page_level_fail_test(){
		$invite_key = $this->invite_key1;
		$target_facebook_id = 3;
		$result = $this->invite_component_lib->accept_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_invite_page_level, already accept same page in another invite key', $result);

		$invite_key = $this->invite_key2;
		$target_facebook_id = 3;
		$result = $this->invite_component_lib->accept_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_invite_page_level, already accept same page in same invite key', $result);
	}

	function accept_all_invite_page_level_level_test(){

		//Add another invite with another campaign id in the same page
		$campaign_id = 4;
		$app_install_id = 1;
		$facebook_page_id = $this->FBPAGEID1;
		$invite_type = 1;
		$user_facebook_id = '713558190';
		$commasep_target_facebook_ids = '1,2,3';
		$result = $this->invite_component_lib->add_invite($campaign_id, $app_install_id, $facebook_page_id, $invite_type, $user_facebook_id, $commasep_target_facebook_ids);
		$this->unit->run($result, 'is_string', 'add_invite', $result);
		$this->invite_key3 = $result;

		//Check invite 1
		$result = $this->invite_component_lib->get_invite_by_invite_key($this->invite_key1);
		$this->unit->run(in_array(2,$result['page_accepted']), FALSE, 'check invite 1');
		//Check invite 3
		$result = $this->invite_component_lib->get_invite_by_invite_key($this->invite_key3);
		$this->unit->run(in_array(2,$result['page_accepted']), FALSE, 'check invite 3');

		//Reserve invite 3
		$invite_key = $this->invite_key3;
		$target_facebook_id = 2;
		$result = $this->invite_component_lib->reserve_invite($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'reserve_invite', $invite_key.' '.$target_facebook_id);

		$criteria = array('campaign_id' => '4');
		$result = $this->invite_component_lib->list_invite($criteria);
		$this->unit->run(count($result), 1, 'list_invite', count($result));
		
		//Mock inside _give_page_score_to_all_inviters to return TRUE
		// $this->page_model = m::mock('page_model');
		// $this->page_model->shouldReceive('get_page_id_by_facebook_page_id')->andReturn('3');
		// $this->user_model = m::mock('user_model');
		// $this->user_model->shouldReceive('get_user_id_by_user_facebook_id')->andReturn(1);
		// $this->audit_lib = m::mock('audit_lib');
		// $this->audit_lib->shouldReceive('add_audit')->andReturn(TRUE);
		// $this->audit_lib->shouldReceive('get_audit_action')->andReturn(array(
		// 		'app_id' => 0,
		// 		'action_id' => 114,
		// 		'description' => 'Invitee Accept Page Invite',
		// 		'stat_app' => FALSE,
		// 		'stat_page' => TRUE,
		// 		'stat_campaign' => FALSE,
		// 		'format_string' => 'User {user:user_id} accepted page invite',
		// 		'score' => 1
		// 	));
		// $this->achievement_lib = m::mock('achievement_lib');
		// $this->achievement_lib->shouldReceive('increment_achievement_stat')->andReturn(TRUE);

		//When accept invite 3, will automatically accept invite 1 (same facebook_page_id), whether having pending or not
		$invite_key = $this->invite_key3;
		$target_facebook_id = 2;
		$result = $this->invite_component_lib->accept_all_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'accept_all_invite_page_level', $result);
		//Check invite 1 again
		$result = $this->invite_component_lib->get_invite_by_invite_key($this->invite_key1);
		$this->unit->run(in_array(2,$result['page_accepted']), TRUE, 'check invite 1');
		//Check invite 3 again
		$result = $this->invite_component_lib->get_invite_by_invite_key($this->invite_key3);
		$this->unit->run(in_array(2,$result['page_accepted']), TRUE, 'check invite 3');

		//Accept again : fail
		$result = $this->invite_component_lib->accept_all_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_all_invite_page_level', $result);

		//But can still reserve invite 1
		$invite_key = $this->invite_key1;
		$target_facebook_id = 2;
		$result = $this->invite_component_lib->reserve_invite($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'reserve_invite', $invite_key.' '.$target_facebook_id);

		//Accept invite 1 should fail
		$invite_key = $this->invite_key1;
		$target_facebook_id = 2;
		$result = $this->invite_component_lib->accept_all_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_all_invite_page_level', $result);

		// m::close();
	}

	function accept_all_invite_page_level_level_fail_test(){
		$invite_key = $this->invite_key1;
		$target_facebook_id = 1;
		$result = $this->invite_component_lib->accept_all_invite_page_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_all_invite_page_level : already accept', $result);
	}

	function accept_invite_campaign_level_test(){
		//Count campaign score : before giving score
		$app_id = 0; //platform
		$user_id = 1; //713558190
		$this->load->model('achievement_stat_model','achievement_stat');
		$user_stat = $this->achievement_stat->get($app_id, $user_id);
		$this->unit->run(isset($user_stat['action']['115']), FALSE, 'check action 115');

		$invite_key = $this->invite_key1;
		$target_facebook_id = 1;

		$invite = $this->invite_model->get_invite_by_criteria(array('invite_key'=>$invite_key));
		$this->unit->run($invite['invite_count'] === 0, TRUE, 'invite_count', $invite['invite_count']);

		$result = $this->invite_pending_model->get_invite_key_by_user_facebook_id_and_campaign_id($target_facebook_id, 1);
		$this->unit->run($result, TRUE, 'found in pending invite', $result);
		$result = $this->invite_component_lib->accept_invite_campaign_level($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'accept_invite_campaign_level', $result);
		$result = $this->invite_pending_model->get_invite_key_by_user_facebook_id_and_campaign_id($target_facebook_id, 1);
		$this->unit->run($result, FALSE, 'not found in pending invite', $result);

		$invite = $this->invite_model->get_invite_by_criteria(array('invite_key'=>$invite_key));
		$this->unit->run($invite['invite_count'] === 1, TRUE, 'invite_count', $invite['invite_count']);

		//Count campaign score : after giving score
		$app_id = 0; //platform
		$user_id = 1; //713558190
		$this->load->model('achievement_stat_model','achievement_stat');
		$user_stat = $this->achievement_stat->get($app_id, $user_id);
		$this->unit->run(isset($user_stat['action']['115']), TRUE, 'check action 115');
		$this->unit->run(isset($user_stat['action']['115']['count']), 1, 'check action 115');

		$invite_key = $this->invite_key2;
		$target_facebook_id = 3;

		$invite = $this->invite_model->get_invite_by_criteria(array('invite_key'=>$invite_key));
		$this->unit->run($invite['invite_count'] === 0, TRUE, 'invite_count', $invite['invite_count']);

		$result = $this->invite_pending_model->get_invite_key_by_user_facebook_id_and_campaign_id($target_facebook_id, 1);
		$this->unit->run($result, TRUE, 'found in pending invite', $result);
		$result = $this->invite_component_lib->accept_invite_campaign_level($invite_key, $target_facebook_id);
		$this->unit->run($result, TRUE, 'accept_invite_campaign_level', $result);
		$result = $this->invite_pending_model->get_invite_key_by_user_facebook_id_and_campaign_id($target_facebook_id, 1);
		$this->unit->run($result, FALSE, 'not found in pending invite', $result);

		$invite = $this->invite_model->get_invite_by_criteria(array('invite_key'=>$invite_key));
		$this->unit->run($invite['invite_count'] === 1, TRUE, 'invite_count', $invite['invite_count']);
	}

	function accept_invite_campaign_level_fail_test(){
		$invite_key = $this->invite_key1;
		$target_facebook_id = 3;
		$result = $this->invite_component_lib->accept_invite_campaign_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_invite_campaign_level, already accept same campaign in another invite key', $result);

		$invite_key = $this->invite_key2;
		$target_facebook_id = 3;
		$result = $this->invite_component_lib->accept_invite_campaign_level($invite_key, $target_facebook_id);
		$this->unit->run($result, FALSE, 'accept_invite_campaign_level, already accept same campaign in same invite key', $result);
	}

	function reserve_invite_after_registered_campaign_test(){
		$invite_key = $this->invite_key2; //campaign_id 1
		$user_facebook_id = '713558190'; //user 1 is already in campaign 1
		$result = $this->invite_component_lib->reserve_invite($invite_key, $user_facebook_id);
		$result_status = TRUE;
		if(isset($result['error']))
			$result_status = FALSE;
			
		$this->unit->run($result_status, FALSE, 'reserve_invite : failed (user already in this campaign)', $invite_key.' - '.$user_facebook_id);
	}

	function _give_page_score_to_all_inviters_test(){
		$user_facebook_id = '713558190';
		$facebook_page_id = $this->FBPAGEID1;
		$this->load->model('audit_model');
		$this->load->model('achievement_stat_page_model');
		$audit_count_before = count($this->audit_model->list_audit());
		$this->load->model('user_model');
		$user_id = $this->user_model->get_user_id_by_user_facebook_id($user_facebook_id);
		$this->load->model('page_model');
		$page_id = $this->page_model->get_page_id_by_facebook_page_id($facebook_page_id);
		$stat_before = $this->achievement_stat_page_model->list_stat(array(
			'user_id' => (int) $user_id,
			'page_id' => (int) $page_id
		));

		$this->unit->run($stat_before_count = $stat_before[0]['action'][114]['count'], 'is_int','count $stat_before', $stat_before[0]['action'][114]['count']);

		$facebook_page_id = $this->FBPAGEID1;
		$inviters = array(713558190, '637741627', 631885465, 713558190, '713558190'); //713558190 should be given one time only
		$result = $this->invite_component_lib->_give_page_score_to_all_inviters($facebook_page_id, $inviters);
		$this->unit->run($result, TRUE, '_give_page_score_to_all_inviters', $result);

		$stat_after = $this->achievement_stat_page_model->list_stat(array(
			'user_id' => (int) $user_id,
			'page_id' => (int) $page_id
		));
		$this->unit->run($stat_after[0]['action'][114]['count'], $stat_before_count + 1, 'count $stat_after idempotent test', $stat_after[0]['action'][114]['count']);
	}

	function _give_page_score_to_all_inviters_fail_test(){
		//no facebook_page_id
		$facebook_page_id = '';
		$inviters = array('a','b','c');
		$result = $this->invite_component_lib->_give_page_score_to_all_inviters($facebook_page_id, $inviters);
		$this->unit->run($result, FALSE, '_give_page_score_to_all_inviters', $result);
		
		//no inviters
		$facebook_page_id = $this->FBPAGEID2;
		$inviters = NULL;
		$result = $this->invite_component_lib->_give_page_score_to_all_inviters($facebook_page_id, $inviters);
		$this->unit->run($result, FALSE, '_give_page_score_to_all_inviters', $result);

		//cannot find page_id
		// $this->page_model = m::mock('page_model');
		// $this->page_model->shouldReceive('get_page_id_by_facebook_page_id')->with(1234)->once()->andReturn(FALSE);
		// $this->user_model->shouldReceive('get_user_id_by_user_facebook_id')->times(3)->andReturn(1,2,3);
		// $this->audit_lib = m::mock('audit_lib');
		// $this->audit_lib->shouldReceive('add_audit')->times(3)->andReturn(TRUE, TRUE, TRUE);
		// $this->achievement_lib = m::mock('achievement_lib');
		// $this->achievement_lib->shouldReceive('increment_achievement_stat')->times(3)->andReturn(TRUE, TRUE, TRUE);
		$facebook_page_id = 1234; //you can't find me
		$inviters = array('a','b','c');
		$result = $this->invite_component_lib->_give_page_score_to_all_inviters($facebook_page_id, $inviters);
		$this->unit->run($result, FALSE, '_give_page_score_to_all_inviters', $result);

		//cannot find a user_id from inviter_facebook_id
		// $this->page_model = m::mock('page_model');
		// $this->page_model->shouldReceive('get_page_id_by_facebook_page_id')->with(1)->once()->andReturn(1234);
		// $this->user_model = m::mock('user_model');
		// $this->user_model->shouldReceive('get_user_id_by_user_facebook_id')->times(3)->andReturn(1,2,FALSE);
		// $this->audit_lib = m::mock('audit_lib');
		// $this->audit_lib->shouldReceive('add_audit')->times(3)->andReturn(TRUE, TRUE, TRUE);
		// $this->achievement_lib = m::mock('achievement_lib');
		// $this->achievement_lib->shouldReceive('increment_achievement_stat')->times(3)->andReturn(TRUE, TRUE, TRUE);
		$facebook_page_id = $this->FBPAGEID2;
		$inviters = array('a','b','c');
		$result = $this->invite_component_lib->_give_page_score_to_all_inviters($facebook_page_id, $inviters);
		$this->unit->run($result, FALSE, '_give_page_score_to_all_inviters', $result);

		//cannot add_audit once
		// $this->page_model = m::mock('page_model');
		// $this->page_model->shouldReceive('get_page_id_by_facebook_page_id')->with(1)->once()->andReturn('3');
		// $this->user_model = m::mock('user_model');
		// $this->user_model->shouldReceive('get_user_id_by_user_facebook_id')->times(3)->andReturn(1,2,3);
		// $this->audit_lib = m::mock('audit_lib');
		// $this->audit_lib->shouldReceive('add_audit')->times(3)->andReturn(TRUE, FALSE, TRUE);
		// $this->achievement_lib = m::mock('achievement_lib');
		// $this->achievement_lib->shouldReceive('increment_achievement_stat')->times(3)->andReturn(TRUE, TRUE, TRUE);

		$facebook_page_id = 1;
		$inviters = array('a','b','c');
		$result = $this->invite_component_lib->_give_page_score_to_all_inviters($facebook_page_id, $inviters);
		$this->unit->run($result, FALSE, '_give_page_score_to_all_inviters', $result);

		//cannot increment_achievement_stat once

		// $this->page_model = m::mock('page_model');
		// $this->page_model->shouldReceive('get_page_id_by_facebook_page_id')->with(1)->once()->andReturn('3');
		// $this->user_model = m::mock('user_model');
		// $this->user_model->shouldReceive('get_user_id_by_user_facebook_id')->times(3)->andReturn(1,2,3);
		// $this->audit_lib = m::mock('audit_lib');
		// $this->audit_lib->shouldReceive('add_audit')->times(3)->andReturn(TRUE, TRUE, TRUE);
		// $this->achievement_lib = m::mock('achievement_lib');
		// $this->achievement_lib->shouldReceive('increment_achievement_stat')->times(3)->andReturn(TRUE, TRUE, FALSE);

		$facebook_page_id = 1;
		$inviters = array('a','b','c');
		$result = $this->invite_component_lib->_give_page_score_to_all_inviters($facebook_page_id, $inviters);
		$this->unit->run($result, FALSE, '_give_page_score_to_all_inviters', $result);
	}

	function _give_campaign_score_to_inviter_test(){
		//Count campaign score : before giving score
		$app_id = 0; //platform
		$user_id = 1; //713558190
		$this->load->model('achievement_stat_model','achievement_stat');
		$user_stat = $this->achievement_stat->get($app_id, $user_id);
		$this->unit->run(isset($user_stat['action']['115']['count']), 1, 'check action 115');

 		$invite_key = $this->invite_key1;
		$invite = $this->invite_component_lib->get_invite_by_invite_key($invite_key);
		$result = $this->invite_component_lib->_give_campaign_score_to_inviter($invite);
		$this->unit->run($result, TRUE, '_give_campaign_score_to_inviter');

		//Count campaign score : after giving score
		$app_id = 0; //platform
		$user_id = 1; //713558190
		$this->load->model('achievement_stat_model','achievement_stat');
		$user_stat = $this->achievement_stat->get($app_id, $user_id);
		$this->unit->run(isset($user_stat['action']['115']['count']), 2, 'check action 115');
	}


	function _give_campaign_score_to_inviter_fail_test(){
		$invite_key = $this->invite_key3; //campaign_id 4
		$invite = $this->invite_component_lib->get_invite_by_invite_key($invite_key);
		$result = $this->invite_component_lib->_give_campaign_score_to_inviter($invite);
		$this->unit->run($result, FALSE, '_give_campaign_score_to_inviter : no app_component with campaign_id = 4');

	}

	function _increment_invite_limit_test(){
		$user_id = 1;
		$app_install_id = 1;
		$campaign_id = 1;
		$input = compact('user_id', 'app_install_id', 'campaign_id');

		// 5 invite out of 7 : from add_invite above
		$result = $this->invite_component_lib->_increment_invite_limit($input);
		$this->unit->run($result, TRUE, '_increment_invite_limit', $result);
		// 6 invite out of 7 : can invite for one more time
	}

	function _check_invite_limit_test(){
		//cooldown
		$invite_key = $this->invite_key1;
		$user_id = 1;
		$app_install_id = 1;
		$campaign_id = 1;
		$action_no = $this->socialhappen->get_k('audit_action', 'User Invite');

		$input = compact('user_id', 'app_install_id', 'campaign_id');
		$result = $this->invite_component_lib->_check_invite_limit($input);
		$this->unit->run($result, FALSE, '_check_invite_limit', $result);

		$this->load->model('audit_stats_model', 'stats');

		$data = array(
			'user_id' => $user_id,
			'action_no' => $action_no,
			'app_install_id' => $app_install_id,
			'campaign_id' => $campaign_id,
			'timestamp' => time()-301*60
		);
		$result = $this->stats->add_stat($data); //out of cooldown time
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));

		$result = $this->invite_component_lib->_check_invite_limit($input);
		$this->unit->run($result, FALSE, '_check_invite_limit', $result);

		$data = array(
			'user_id' => $user_id,
			'action_no' => $action_no,
			'app_install_id' => $app_install_id,
			'campaign_id' => $campaign_id,
			'timestamp' => time()-301*60
		);
		$result = $this->stats->add_stat($data); //out of cooldown time
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));

		$result = $this->invite_component_lib->_check_invite_limit($input);
		$this->unit->run($result, FALSE, '_check_invite_limit', $result);

		$result = $this->invite_component_lib->_increment_invite_limit($input); //in cooldown time, no invite left (7 of 7)
		$this->unit->run($result, TRUE, '_increment_invite_limit', $result);

		$result = $this->invite_component_lib->_check_invite_limit($input);
		$this->unit->run($result, TRUE, '_check_invite_limit (reached limit)', $result);

	}

	function try_invite_after_reached_limit_test(){
		$campaign_id = 1;
		$app_install_id = 1;
		$facebook_page_id = $this->FBPAGEID1;
		$invite_type = 1;
		$user_facebook_id = '713558190';
		$commasep_target_facebook_ids = '1,2,3,10,11';
		$result = $this->invite_component_lib->add_invite($campaign_id, $app_install_id, $facebook_page_id, $invite_type, $user_facebook_id, $commasep_target_facebook_ids);

		$this->unit->run($result, FALSE, 'add_invite', $result);
	}
}
/* End of file invite_component_lib_test.php */
/* Location: ./application/controllers/test/invite_component_lib_test.php */