<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite_model_test extends CI_Controller {
	public $invite_data = array(
		'campaign_id' => 1,
		'facebook_invite' => TRUE,
		'email_invite' => TRUE,
		'criteria' => array(
			'score' => 1,
			'maximum' => 5,
			'cooldown' => 300,
			'acceptance' => array(
				'page' => 100,
				'campaign' => 20
			)
		),
		'message' => array(
			'title' => 'You are invited',
			'text' => 'Welcome to the campaign',
			'image' => 'https://localhost/assets/images/blank.png'
		)
	);
	public $invite_update_data = array(
		'campaign_id' => 555, // should be ignored by update
		'facebook_invite' => FALSE,
		'email_invite' => FALSE,
		'criteria' => array(
			'score' => 10,
			'maximum' => 50,
			'cooldown' => 3000,
			'acceptance' => array(
				'page' => 1000,
				'campaign' => 200
			)
		),
		'message' => array(
			'title' => 'You are invited, again',
			'text' => 'Welcome to the campaign, again',
			'image' => 'https://localhost/assets/images/blank2.png'
		)
	);
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('invite_model','invite');
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
	
	/**
	 * Test add()
	 */
	function add_test(){
		$result = $this->invite->add($this->invite_data);
		$this->unit->run($result, TRUE, 'Add an invite');
		
		$invite2 = $this->invite_data;
		unset($invite2['facebook_invite']);
		$invite2['campaign_id'] = 2;
		$result = $this->invite->add($invite2);
		$this->unit->run($result, TRUE, 'Add an invite without facebook_invite');
		
		$invite3 = $this->invite_data;
		unset($invite3['email_invite']);
		$invite3['campaign_id'] = '3';
		$result = $this->invite->add($invite3);
		$this->unit->run($result, TRUE, 'Add an invite withour email_invite, string campaign id');
		
		$this->unit->run($this->invite->count_all(), 3, 'count all invite');
	}
	/**
	 * Test add failures
	 */
	function failed_add_test(){
		$result = $this->invite->add($this->invite_data);
		$this->unit->run($result, TRUE, 'Duplicated campaign_id');
		
		$this->unit->run($this->invite->count_all(), 3, 'count all invite');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['campaign_id']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['score']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No score');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['maximum']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['cooldown']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['acceptance']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No acceptance');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['acceptance']['page']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No page');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['acceptance']['campaign']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No campaign');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No message');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']['title']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No title');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']['text']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No text');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']['image']);
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'No image');
		
		$invite_fail = $this->invite_data;
		$invite_fail['campaign_id'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty campaign_id');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['score'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['maximum'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['cooldown'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['acceptance'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty acceptance');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['acceptance']['page'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty page');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['acceptance']['campaign'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty campaign');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message']['title'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message']['text'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message']['image'] = '';
		$result = $this->invite->add($invite_fail);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$this->unit->run($this->invite->count_all(), 3, 'count all invite');
	}
	
	function get_by_campaign_id_test(){
		$result = $this->invite->get_by_campaign_id(1);
		unset($result['_id']);
		$this->unit->run($result, $this->invite_data, 'get invite by campaign_id');
		
		$result = $this->invite->get_by_campaign_id('1');
		unset($result['_id']);
		$this->unit->run($result, $this->invite_data, 'get invite by string campaign_id');
		
		$result = $this->invite->get_by_campaign_id(2);
		$this->unit->run($result, 'is_array', 'get invite by campaign_id');
		$this->unit->run($result['facebook_invite'], FALSE, 'facebook_invite is FALSE by default');
		
		$result = $this->invite->get_by_campaign_id(3);
		$this->unit->run($result, 'is_array', 'get invite by campaign_id');
		$this->unit->run($result['email_invite'], FALSE, 'email_invite is FALSE by default');
	}
	
	function get_by_campaign_id_fail_test(){
		$result = $this->invite->get_by_campaign_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->invite->get_by_campaign_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->invite->get_by_campaign_id(NULL);
		$this->unit->run($result, FALSE, 'not found');
	}
	
	function update_by_campaign_id_test(){
		$update_data = $this->invite_update_data;
		$result = $this->invite->update_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->invite->get_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated invite');
		
		$update_data = $this->invite_update_data;
		unset($update_data['facebook_invite']);
		unset($update_data['email_invite']);
		$result = $this->invite->update_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->invite->get_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated invite');
		$this->unit->run($result['facebook_invite'], FALSE, 'get updated invite, facebook_invite is FALSE by default');
		$this->unit->run($result['email_invite'], FALSE, 'get updated invite, email_invite is FALSE by default');
	}
	
	function update_by_campaign_id_fail_test(){
		$result = $this->invite->update_by_campaign_id(0, $this->invite_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$result = $this->invite->update_by_campaign_id(NULL, $this->invite_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['score']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No score');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['maximum']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['cooldown']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['acceptance']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No acceptance');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['acceptance']['page']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No page');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['acceptance']['campaign']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No campaign');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['message']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No message');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['message']['title']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No title');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['message']['text']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No text');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['message']['image']);
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No image');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['score'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['maximum'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['cooldown'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['acceptance'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty acceptance');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['acceptance']['page'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty page');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['acceptance']['campaign'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty campaign');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['message'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['message']['title'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['message']['text'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['message']['image'] = '';
		$result = $this->invite->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty image');
	}
}
/* End of file invite_model_test.php */
/* Location: ./application/controllers/test/invite_model_test.php */