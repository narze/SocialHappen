<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_component_model_test extends CI_Controller {
	
	public $sharebutton_data = array(
		// 'campaign_id' => 1,
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
	);
	public $sharebutton_update_data = array(
		// 'campaign_id' => 555, // should be ignored by update
		'facebook_button' => FALSE,
		'twitter_button' => FALSE,
		'criteria' => array(
			'score' => 10,
			'maximum' => 50,
			'cooldown' => 3000
		),
		'message' => array(
			'title' => 'Join the campaign by this link, again',
			'caption' => 'This is caption, again',
			'text' => 'this is long description, again',
			'image' => 'https://localhost/assets/images/blank2.png'
		)
	);
	
	public $invite_data = array(
		// 'campaign_id' => 1,
		'facebook_invite' => TRUE,
		'email_invite' => TRUE,
		'criteria' => array(
			'score' => 1,
			'maximum' => 5,
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
	);
	public $invite_update_data = array(
		// 'campaign_id' => 555, // should be ignored by update
		'facebook_invite' => FALSE,
		'email_invite' => FALSE,
		'criteria' => array(
			'score' => 10,
			'maximum' => 50,
			'cooldown' => 3000,
			'acceptance_score' => array(
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
	
	public $app_component_data = array(
		'campaign_id' => 1,
		'invite' => array(
			'facebook_invite' => TRUE,
			'email_invite' => TRUE,
			'criteria' => array(
				'score' => 1,
				'maximum' => 5,
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
	
	public $app_component_update_data = array(
		'campaign_id' => 1,
		'invite' => array(
			'facebook_invite' => FALSE,
			'email_invite' => FALSE,
			'criteria' => array(
				'score' => 10,
				'maximum' => 50,
				'cooldown' => 3000,
				'acceptance_score' => array(
					'page' => 1000,
					'campaign' => 200
				)
			),
			'message' => array(
				'title' => 'You are invited, again',
				'text' => 'Welcome to the campaign, again',
				'image' => 'https://localhost/assets/images/blank2.png'
			)
		),
		'sharebutton' => array(
			'facebook_button' => FALSE,
			'twitter_button' => FALSE,
			'criteria' => array(
				'score' => 10,
				'maximum' => 50,
				'cooldown' => 3000
			),
			'message' => array(
				'title' => 'Join the campaign by this link, again',
				'caption' => 'This is caption, again',
				'text' => 'this is long description, again',
				'image' => 'https://localhost/assets/images/blank2.png'
			)
		)
	);
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('app_component_model','app_component');
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
		$this->app_component->drop_collection();
	}
	
	function create_index_before_test(){
		$this->app_component->create_index();
	}
	
	//App component
	
	/**
	 * Test add()
	 */
	function add_test(){
		$result = $this->app_component->add($this->app_component_data);
		$this->unit->run($result, TRUE,'Add app_component with full data');
		
		$this->unit->run($this->app_component->count_all(), 1, 'count all app_component');
		
		$app_component_data_2 = $this->app_component_data;
		$app_component_data_2['campaign_id'] = 2;
		unset($app_component_data_2['invite']['facebook_invite']);
		unset($app_component_data_2['sharebutton']['facebook_button']);
		$result = $this->app_component->add($app_component_data_2);
		$this->unit->run($result, TRUE,'Add app_component');
		
		$this->unit->run($this->app_component->count_all(), 2, 'count all app_component');
		
		$app_component_data_3 = $this->app_component_data;
		$app_component_data_3['campaign_id'] = 3;
		unset($app_component_data_3['invite']['email_invite']);
		unset($app_component_data_3['sharebutton']['twitter_button']);
		$result = $this->app_component->add($app_component_data_3);
		$this->unit->run($result, TRUE,'Add app_component');
		
		$this->unit->run($this->app_component->count_all(), 3, 'count all app_component');
		
		$result = $this->app_component->add(array('campaign_id' => 4));
		$this->unit->run($result, TRUE, 'Add app_component'); //Somehow Mongo returns TRUE but document are not added
		
		$this->unit->run($this->app_component->count_all(), 4, 'count all app_component');
	}
	
	function add_fail_test(){
		$result = $this->app_component->add(array('campaign_id' => 4));
		$this->unit->run($result, TRUE, 'Add duplicated app_component'); //Somehow Mongo returns TRUE but document are not added
		
		$this->unit->run($this->app_component->count_all(), 4, 'count all app_component');
	}
	
	//Invite//
	
	function get_invite_by_campaign_id_test(){
		$result = $this->app_component->get_invite_by_campaign_id(1);
		unset($result['_id']);
		$this->unit->run($result, $this->invite_data, 'get invite by campaign_id');
		
		$result = $this->app_component->get_invite_by_campaign_id('1');
		unset($result['_id']);
		$this->unit->run($result, $this->invite_data, 'get invite by string campaign_id');
		
		$result = $this->app_component->get_invite_by_campaign_id(2);
		$this->unit->run($result, 'is_array', 'get invite by campaign_id');
		$this->unit->run($result['facebook_invite'], FALSE, 'facebook_invite is FALSE by default');
		
		$result = $this->app_component->get_invite_by_campaign_id(3);
		$this->unit->run($result, 'is_array', 'get invite by campaign_id');
		$this->unit->run($result['email_invite'], FALSE, 'email_invite is FALSE by default');
		
		$result = $this->app_component->get_invite_by_campaign_id(4);
		$this->unit->run($result === NULL, TRUE, 'get invite by campaign_id');
	}
	
	function get_invite_by_campaign_id_fail_test(){
		$result = $this->app_component->get_invite_by_campaign_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->app_component->get_invite_by_campaign_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->app_component->get_invite_by_campaign_id(NULL);
		$this->unit->run($result, FALSE, 'not found');
	}
	
	function update_invite_by_campaign_id_test(){
		$update_data = $this->invite_update_data;
		$result = $this->app_component->update_invite_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->app_component->get_invite_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated invite');
		
		$update_data = $this->invite_update_data;
		unset($update_data['facebook_invite']);
		unset($update_data['email_invite']);
		$result = $this->app_component->update_invite_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->app_component->get_invite_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated invite');
		$this->unit->run($result['facebook_invite'], FALSE, 'get updated invite, facebook_invite is FALSE by default');
		$this->unit->run($result['email_invite'], FALSE, 'get updated invite, email_invite is FALSE by default');
		
		$update_data = $this->invite_update_data;
		$result = $this->app_component->update_invite_by_campaign_id(4,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 4;
		$get_result = $this->app_component->get_invite_by_campaign_id(4);
		$this->unit->run($result, $update_data, 'get updated invite');
	}
	
	function update_invite_by_campaign_id_fail_test(){
		$result = $this->app_component->update_invite_by_campaign_id(0, $this->invite_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$result = $this->app_component->update_invite_by_campaign_id(NULL, $this->invite_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['score']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No score');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['maximum']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['cooldown']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['acceptance_score']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No acceptance_score');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['acceptance_score']['page']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No page');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['acceptance_score']['campaign']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No campaign');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['message']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No message');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['message']['title']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No title');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['message']['text']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No text');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['message']['image']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No image');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['score'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['maximum'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['cooldown'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['acceptance_score'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty acceptance_score');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['acceptance_score']['page'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty page');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['acceptance_score']['campaign'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty campaign');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['message'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['message']['title'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['message']['text'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['message']['image'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty image');
	}
	
	function invite_data_check_test(){
		//TRUE
		$result = $this->app_component->invite_data_check($this->invite_data);
		$this->unit->run($result, TRUE, 'Check invite');
		
		$invite2 = $this->invite_data;
		unset($invite2['facebook_invite']);
		$invite2['campaign_id'] = 2;
		$result = $this->app_component->invite_data_check($invite2);
		$this->unit->run($result, TRUE, 'Check invite without facebook_invite');
		
		$invite3 = $this->invite_data;
		unset($invite3['email_invite']);
		$invite3['campaign_id'] = '3';
		$result = $this->app_component->invite_data_check($invite3);
		$this->unit->run($result, TRUE, 'Check invite withour email_invite, string campaign id');
	
		// $result = $this->app_component->invite_data_check($this->invite_data);
		// $this->unit->run($result, TRUE, 'Duplicated campaign_id');
		
		// $invite_fail = $this->invite_data;
		// unset($invite_fail['campaign_id']);
		// $result = $this->app_component->invite_data_check($invite_fail);
		// $this->unit->run($result, FALSE, 'No campaign_id');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['score']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No score');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['maximum']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['cooldown']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['acceptance_score']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No acceptance_score');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['acceptance_score']['page']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No page');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['acceptance_score']['campaign']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No campaign');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No message');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']['title']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No title');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']['text']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No text');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']['image']);
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'No image');
		
		// $invite_fail = $this->invite_data;
		// $invite_fail['campaign_id'] = '';
		// $result = $this->app_component->invite_data_check($invite_fail);
		// $this->unit->run($result, FALSE, 'Empty campaign_id');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['score'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['maximum'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['cooldown'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['acceptance_score'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty acceptance_score');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['acceptance_score']['page'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty page');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['acceptance_score']['campaign'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty campaign');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message']['title'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message']['text'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message']['image'] = '';
		$result = $this->app_component->invite_data_check($invite_fail);
		$this->unit->run($result, FALSE, 'Empty image');
	}
	
	//Sharebutton
	
	function get_sharebutton_by_campaign_id_test(){
		$result = $this->app_component->get_sharebutton_by_campaign_id(1);
		unset($result['_id']);
		$this->unit->run($result, $this->sharebutton_data, 'get sharebutton by campaign_id');
		
		$result = $this->app_component->get_sharebutton_by_campaign_id('1');
		unset($result['_id']);
		$this->unit->run($result, $this->sharebutton_data, 'get sharebutton by string campaign_id');
		
		$result = $this->app_component->get_sharebutton_by_campaign_id(2);
		$this->unit->run($result, 'is_array', 'get sharebutton by campaign_id');
		$this->unit->run($result['facebook_button'], FALSE, 'facebook_button is FALSE by default');
		
		$result = $this->app_component->get_sharebutton_by_campaign_id(3);
		$this->unit->run($result, 'is_array', 'get sharebutton by campaign_id');
		$this->unit->run($result['twitter_button'], FALSE, 'twitter_button is FALSE by default');
		
		$result = $this->app_component->get_sharebutton_by_campaign_id(4);
		$this->unit->run($result === NULL, TRUE, 'get sharebutton by campaign_id');
	}
	
	function get_sharebutton_by_campaign_id_fail_test(){
		$result = $this->app_component->get_sharebutton_by_campaign_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->app_component->get_sharebutton_by_campaign_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->app_component->get_sharebutton_by_campaign_id(NULL);
		$this->unit->run($result, FALSE, 'not found');
	}
	
	function update_sharebutton_by_campaign_id_test(){
		$update_data = $this->sharebutton_update_data;
		$result = $this->app_component->update_sharebutton_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->app_component->get_sharebutton_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated sharebutton');
		
		$update_data = $this->sharebutton_update_data;
		unset($update_data['facebook_button']);
		unset($update_data['twitter_button']);
		$result = $this->app_component->update_sharebutton_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->app_component->get_sharebutton_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated sharebutton');
		$this->unit->run($result['facebook_button'], FALSE, 'get updated sharebutton, facebook_button should be set FALSE by default');
		$this->unit->run($result['twitter_button'], FALSE, 'get updated sharebutton, twitter_button should be set FALSE by default');
		
		$update_data = $this->sharebutton_update_data;
		$result = $this->app_component->update_sharebutton_by_campaign_id(4,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 4;
		$get_result = $this->app_component->get_sharebutton_by_campaign_id(4);
		$this->unit->run($result, $update_data, 'get updated sharebutton');
	}
	
	function update_sharebutton_by_campaign_id_fail_test(){
		$result = $this->app_component->update_sharebutton_by_campaign_id(0, $this->sharebutton_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$result = $this->app_component->update_sharebutton_by_campaign_id(NULL, $this->sharebutton_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']);
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']['score']);
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No score');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']['maximum']);
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']['cooldown']);
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']);
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No message');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['title']);
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No title');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['text']);
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No text');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['caption']);
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No caption');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['image']);
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No image');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria'] = '';
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria']['score'] = '';
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria']['maximum'] = '';
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria']['cooldown'] = '';
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message'] = '';
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['title'] = '';
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['text'] = '';
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['image'] = '';
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['caption'] = '';
		$result = $this->app_component->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty caption');
	}
	
	function sharebutton_data_check_test(){
		$result = $this->app_component->sharebutton_data_check($this->sharebutton_data);
		$this->unit->run($result, TRUE, 'Add an sharebutton');
	
		
		$sharebutton2 = $this->sharebutton_data;
		unset($sharebutton2['facebook_button']);
		$sharebutton2['campaign_id'] = 2;
		$result = $this->app_component->sharebutton_data_check($sharebutton2);
		$this->unit->run($result, TRUE, 'Add an sharebutton without facebook_button');
		
		$sharebutton3 = $this->sharebutton_data;
		unset($sharebutton3['twitter_button']);
		$sharebutton3['campaign_id'] = '3';
		$result = $this->app_component->sharebutton_data_check($sharebutton3);
		$this->unit->run($result, TRUE, 'Add an sharebutton withour twitter_button, string campaign id');

		$result = $this->app_component->sharebutton_data_check($this->sharebutton_data);
		$this->unit->run($result, TRUE, 'Duplicated campaign_id');
		
		// $sharebutton_fail = $this->sharebutton_data;
		// unset($sharebutton_fail['campaign_id']);
		// $result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		// $this->unit->run($result, FALSE, 'No campaign_id');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']);
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']['score']);
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No score');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']['maximum']);
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']['cooldown']);
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']);
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No message');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['title']);
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No title');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['caption']);
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No caption');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['text']);
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No text');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['image']);
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No image');
		
		// $sharebutton_fail = $this->sharebutton_data;
		// $sharebutton_fail['campaign_id'] = '';
		// $result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		// $this->unit->run($result, FALSE, 'Empty campaign_id');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria'] = '';
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria']['score'] = '';
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria']['maximum'] = '';
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria']['cooldown'] = '';
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message'] = '';
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['title'] = '';
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['caption'] = '';
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty caption');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['text'] = '';
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['image'] = '';
		$result = $this->app_component->sharebutton_data_check($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty image');
	}
	
	//App_component
	
	function get_by_campaign_id_test(){
		$result = $this->app_component->get_by_campaign_id(1);
		unset($result['_id']);
		$this->unit->run($result, $this->app_component_update_data, 'get by campaign_id');
		
		$result = $this->app_component->get_by_campaign_id('1');
		unset($result['_id']);
		$this->unit->run($result, $this->app_component_update_data, 'get by string campaign_id');
		
		$result = $this->app_component->get_by_campaign_id(4);
		unset($result['_id']);
		$this->unit->run($result['invite'], $this->invite_update_data, 'get invite by campaign_id');
		$this->unit->run($result['sharebutton'], $this->sharebutton_update_data, 'get sharebutton by campaign_id');
		
		$result = $this->app_component->get_by_campaign_id(2);
		$this->unit->run($result, 'is_array', 'get by campaign_id');
		$this->unit->run(isset($result['invite']), TRUE, 'found invite');
		$this->unit->run(isset($result['sharebutton']), TRUE, 'found sharebutton');
		$this->unit->run(isset($result['homepage']), FALSE, 'homepage not found, moved');
	}
	
	function get_by_campaign_id_fail_test(){
		$result = $this->app_component->get_by_campaign_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->app_component->get_by_campaign_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->app_component->get_by_campaign_id(NULL);
		$this->unit->run($result, FALSE, 'not found');
	}
}
/* End of file app_component_model_test.php */
/* Location: ./application/controllers/test/app_component_model_test.php */