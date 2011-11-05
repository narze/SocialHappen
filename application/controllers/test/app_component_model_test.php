<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_component_model_test extends CI_Controller {
	public $homepage_data = array(
		'campaign_id' => 1,
		'enable' => TRUE,
		'image' => 'https://localhost/assets/images/blank.png',
		'message' => 'You are not this page\'s fan, please like this page first'
	);
	public $homepage_update_data = array(
		'campaign_id' => 555, // should be ignored by update
		'enable' => FALSE,
		'image' => 'https://localhost/assets/images/blank.png',
		'message' => 'You are not this page\'s fan, please like this page first, again'
	);
	
	public $sharebutton_data = array(
		'campaign_id' => 1,
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
		'campaign_id' => 555, // should be ignored by update
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
	
	//Homepage
	
	/**
	 * Test add_homepage()
	 */
	function add_homepage_test(){
		$result = $this->app_component->add_homepage($this->add_homepage);
		$this->unit->run($result, TRUE, 'Add an homepage');
		
		$this->unit->run($this->app_component->count_all(), 1, 'count all homepage');
		
		$homepage2 = $this->homepage_data;
		unset($homepage2['enable']);
		$homepage2['campaign_id'] = 2;
		$result = $this->app_component->add_homepage($homepage2);
		$this->unit->run($result, TRUE, 'Add an homepage without setting enable');
		
		$this->unit->run($this->app_component->count_all(), 2, 'count all app_component');
	}
	
	/**
	 * Test add failures
	 */
	function failed_add_homepage_test(){
		$result = $this->app_component->add_homepage($this->homepage_data);
		$this->unit->run($result, TRUE, 'Duplicated campaign_id');
		
		$this->unit->run($this->app_component->count_all(), 2, 'count all app_component');
		
		$homepage_fail = $this->homepage_data;
		unset($homepage_fail['image']);
		$result = $this->app_component->add_homepage($homepage_fail);
		$this->unit->run($result, FALSE, 'No image');
		
		$homepage_fail = $this->homepage_data;
		unset($homepage_fail['message']);
		$result = $this->app_component->add_homepage($homepage_fail);
		$this->unit->run($result, FALSE, 'No message');
		
		$homepage_fail = $this->homepage_data;
		$homepage_fail['image'] = '';
		$result = $this->app_component->add_homepage($homepage_fail);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$homepage_fail = $this->homepage_data;
		$homepage_fail['message'] = '';
		$result = $this->app_component->add_homepage($homepage_fail);
		$this->unit->run($result, FALSE, 'Empty message');
		
	}
	
	function get_homepage_by_campaign_id_test(){
		$result = $this->app_component->get_homepage_by_campaign_id(1);
		unset($result['_id']);
		$this->unit->run($result, $this->homepage_data, 'get homepage by campaign_id');
		
		$result = $this->app_component->get_homepage_by_campaign_id('1');
		unset($result['_id']);
		$this->unit->run($result, $this->homepage_data, 'get homepage by string campaign_id');
		
		$result = $this->app_component->get_homepage_by_campaign_id(2);
		$this->unit->run($result, 'is_array', 'get homepage by campaign_id');
		$this->unit->run($result['enable'], FALSE, 'enable is FALSE by default');
	}
	
	function get_homepage_by_campaign_id_fail_test(){
		$result = $this->app_component->get_homepage_by_campaign_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->app_component->get_homepage_by_campaign_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->app_component->get_homepage_by_campaign_id(NULL);
		$this->unit->run($result, FALSE, 'not found');
	}
	
	function update_homepage_by_campaign_id_test(){
		$update_data = $this->homepage_update_data;
		$result = $this->app_component->update_homepage_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->app_component->get_homepage_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated homepage');
		
		$update_data = $this->homepage_update_data;
		unset($update_data['enable']);
		$result = $this->app_component->update_homepage_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->app_component->get_homepage_by_campaign_id(1);
		$this->unit->run($result['enable'], FALSE, 'get updated homepage, enable is FALSE if not set on update');
	}
	
	function update_homepage_by_campaign_id_fail_test(){
		$result = $this->app_component->update_homepage_by_campaign_id(0, $this->homepage_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$result = $this->app_component->update_homepage_by_campaign_id(NULL, $this->homepage_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$fail_update_data = $this->homepage_update_data;
		unset($fail_update_data['image']);
		$result = $this->app_component->update_homepage_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No image');
		
		$fail_update_data = $this->homepage_update_data;
		unset($fail_update_data['message']);
		$result = $this->app_component->update_homepage_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No message');
		
		$fail_update_data = $this->homepage_update_data;
		$fail_update_data['image'] = '';
		$result = $this->app_component->update_homepage_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$fail_update_data = $this->homepage_update_data;
		$fail_update_data['message'] = '';
		$result = $this->app_component->update_homepage_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty message');
	}
	
	//Invite//
	/**
	 * Test add_invite()
	 */
	function add_invite_test(){
		$result = $this->app_component->add_invite($this->invite_data);
		$this->unit->run($result, TRUE, 'Add an invite');
		
		$invite2 = $this->invite_data;
		unset($invite2['facebook_invite']);
		$invite2['campaign_id'] = 2;
		$result = $this->app_component->add_invite($invite2);
		$this->unit->run($result, TRUE, 'Add an invite without facebook_invite');
		
		$invite3 = $this->invite_data;
		unset($invite3['email_invite']);
		$invite3['campaign_id'] = '3';
		$result = $this->app_component->add_invite($invite3);
		$this->unit->run($result, TRUE, 'Add an invite withour email_invite, string campaign id');
		
		$this->unit->run($this->app_component->count_all(), 3, 'count all invite');
	}
	/**
	 * Test add_invite failures
	 */
	function failed_add_invite_test(){
		$result = $this->app_component->add_invite($this->invite_data);
		$this->unit->run($result, TRUE, 'Duplicated campaign_id');
		
		$this->unit->run($this->app_component->count_all(), 3, 'count all invite');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['campaign_id']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['score']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No score');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['maximum']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['cooldown']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['acceptance']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No acceptance');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['acceptance']['page']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No page');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['criteria']['acceptance']['campaign']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No campaign');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No message');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']['title']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No title');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']['text']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No text');
		
		$invite_fail = $this->invite_data;
		unset($invite_fail['message']['image']);
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'No image');
		
		$invite_fail = $this->invite_data;
		$invite_fail['campaign_id'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty campaign_id');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['score'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['maximum'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['cooldown'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['acceptance'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty acceptance');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['acceptance']['page'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty page');
		
		$invite_fail = $this->invite_data;
		$invite_fail['criteria']['acceptance']['campaign'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty campaign');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message']['title'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message']['text'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$invite_fail = $this->invite_data;
		$invite_fail['message']['image'] = '';
		$result = $this->app_component->add_invite($invite_fail);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$this->unit->run($this->app_component->count_all(), 3, 'count all invite');
	}
	
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
		unset($fail_update_data['criteria']['acceptance']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No acceptance');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['acceptance']['page']);
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No page');
		
		$fail_update_data = $this->invite_update_data;
		unset($fail_update_data['criteria']['acceptance']['campaign']);
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
		$fail_update_data['criteria']['acceptance'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty acceptance');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['acceptance']['page'] = '';
		$result = $this->app_component->update_invite_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty page');
		
		$fail_update_data = $this->invite_update_data;
		$fail_update_data['criteria']['acceptance']['campaign'] = '';
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
	
	//Sharebutton
	
	/**
	 * Test add_sharebutton()
	 */
	function add_sharebutton_test(){
		$result = $this->sharebutton->add_sharebutton($this->sharebutton_data);
		$this->unit->run($result, TRUE, 'Add an sharebutton');
		
		$this->unit->run($this->sharebutton->count_all(), 1, 'count all sharebutton');
		
		$sharebutton2 = $this->sharebutton_data;
		unset($sharebutton2['facebook_button']);
		$sharebutton2['campaign_id'] = 2;
		$result = $this->sharebutton->add_sharebutton($sharebutton2);
		$this->unit->run($result, TRUE, 'Add an sharebutton without facebook_button');
		
		$this->unit->run($this->sharebutton->count_all(), 2, 'count all sharebutton');
		
		$sharebutton3 = $this->sharebutton_data;
		unset($sharebutton3['twitter_button']);
		$sharebutton3['campaign_id'] = '3';
		$result = $this->sharebutton->add_sharebutton($sharebutton3);
		$this->unit->run($result, TRUE, 'Add an sharebutton withour twitter_button, string campaign id');
		
		$this->unit->run($this->sharebutton->count_all(), 3, 'count all sharebutton');
	}
	
	/**
	 * Test add_sharebutton failures
	 */
	function failed_add_sharebutton_test(){
		$result = $this->sharebutton->add_sharebutton($this->sharebutton_data);
		$this->unit->run($result, TRUE, 'Duplicated campaign_id');
		
		$this->unit->run($this->sharebutton->count_all(), 3, 'count all sharebutton');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['campaign_id']);
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']);
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']['score']);
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No score');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']['maximum']);
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']['cooldown']);
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']);
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No message');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['title']);
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No title');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['caption']);
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No caption');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['text']);
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No text');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['image']);
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No image');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['campaign_id'] = '';
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty campaign_id');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria'] = '';
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria']['score'] = '';
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria']['maximum'] = '';
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria']['cooldown'] = '';
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message'] = '';
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['title'] = '';
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['caption'] = '';
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty caption');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['text'] = '';
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['image'] = '';
		$result = $this->sharebutton->add_sharebutton($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty image');
	}
	
	function get_sharebutton_by_campaign_id_test(){
		$result = $this->sharebutton->get_sharebutton_by_campaign_id(1);
		unset($result['_id']);
		$this->unit->run($result, $this->sharebutton_data, 'get sharebutton by campaign_id');
		
		$result = $this->sharebutton->get_sharebutton_by_campaign_id('1');
		unset($result['_id']);
		$this->unit->run($result, $this->sharebutton_data, 'get sharebutton by string campaign_id');
		
		$result = $this->sharebutton->get_sharebutton_by_campaign_id(2);
		$this->unit->run($result, 'is_array', 'get sharebutton by campaign_id');
		$this->unit->run($result['facebook_button'], FALSE, 'facebook_button is FALSE by default');
		
		$result = $this->sharebutton->get_sharebutton_by_campaign_id(3);
		$this->unit->run($result, 'is_array', 'get sharebutton by campaign_id');
		$this->unit->run($result['twitter_button'], FALSE, 'twitter_button is FALSE by default');
	}
	
	function get_sharebutton_by_campaign_id_fail_test(){
		$result = $this->sharebutton->get_sharebutton_by_campaign_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->sharebutton->get_sharebutton_by_campaign_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->sharebutton->get_sharebutton_by_campaign_id(NULL);
		$this->unit->run($result, FALSE, 'not found');
	}
	
	function update_sharebutton_by_campaign_id_test(){
		$update_data = $this->sharebutton_update_data;
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->sharebutton->get_sharebutton_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated sharebutton');
		
		$update_data = $this->sharebutton_update_data;
		unset($update_data['facebook_button']);
		unset($update_data['twitter_button']);
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->sharebutton->get_sharebutton_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated sharebutton');
		$this->unit->run($result['facebook_button'], FALSE, 'get updated sharebutton, facebook_button should be set FALSE by default');
		$this->unit->run($result['twitter_button'], FALSE, 'get updated sharebutton, twitter_button should be set FALSE by default');
	}
	
	function update_sharebutton_by_campaign_id_fail_test(){
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(0, $this->sharebutton_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(NULL, $this->sharebutton_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']);
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']['score']);
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No score');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']['maximum']);
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']['cooldown']);
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']);
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No message');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['title']);
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No title');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['text']);
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No text');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['caption']);
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No caption');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['image']);
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No image');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria'] = '';
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria']['score'] = '';
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria']['maximum'] = '';
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria']['cooldown'] = '';
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message'] = '';
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['title'] = '';
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['text'] = '';
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['image'] = '';
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['caption'] = '';
		$result = $this->sharebutton->update_sharebutton_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty caption');
	}
	
	//App_component
	
	function get_by_campaign_id_test(){
		$result = $this->app_component->get_by_campaign_id(1);
		unset($result['_id']);
		$this->unit->run($result, $this->app_component_data, 'get homepage by campaign_id');
		
		$result = $this->app_component->get_by_campaign_id('1');
		unset($result['_id']);
		$this->unit->run($result, $this->app_component_data, 'get homepage by string campaign_id');
		
		$result = $this->app_component->get_by_campaign_id(2);
		$this->unit->run($result, 'is_array', 'get homepage by campaign_id');
		$this->unit->run(isset($result['homepage']), TRUE, 'found homepage');
		$this->unit->run(isset($result['invite']), TRUE, 'found invite');
		$this->unit->run(isset($result['sharebutton']), TRUE, 'found sharebutton');
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