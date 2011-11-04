<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sharebutton_model_test extends CI_Controller {
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
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('app_component/sharebutton_model','sharebutton');
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
		$this->sharebutton->drop_collection();
	}
	
	function create_index_before_test(){
		$this->sharebutton->create_index();
	}
	
	/**
	 * Test add()
	 */
	function add_test(){
		$result = $this->sharebutton->add($this->sharebutton_data);
		$this->unit->run($result, TRUE, 'Add an sharebutton');
		
		$this->unit->run($this->sharebutton->count_all(), 1, 'count all sharebutton');
		
		$sharebutton2 = $this->sharebutton_data;
		unset($sharebutton2['facebook_button']);
		$sharebutton2['campaign_id'] = 2;
		$result = $this->sharebutton->add($sharebutton2);
		$this->unit->run($result, TRUE, 'Add an sharebutton without facebook_button');
		
		$this->unit->run($this->sharebutton->count_all(), 2, 'count all sharebutton');
		
		$sharebutton3 = $this->sharebutton_data;
		unset($sharebutton3['twitter_button']);
		$sharebutton3['campaign_id'] = '3';
		$result = $this->sharebutton->add($sharebutton3);
		$this->unit->run($result, TRUE, 'Add an sharebutton withour twitter_button, string campaign id');
		
		$this->unit->run($this->sharebutton->count_all(), 3, 'count all sharebutton');
	}
	
	/**
	 * Test add failures
	 */
	function failed_add_test(){
		$result = $this->sharebutton->add($this->sharebutton_data);
		$this->unit->run($result, TRUE, 'Duplicated campaign_id');
		
		$this->unit->run($this->sharebutton->count_all(), 3, 'count all sharebutton');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['campaign_id']);
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']);
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']['score']);
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No score');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']['maximum']);
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['criteria']['cooldown']);
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']);
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No message');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['title']);
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No title');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['caption']);
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No caption');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['text']);
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No text');
		
		$sharebutton_fail = $this->sharebutton_data;
		unset($sharebutton_fail['message']['image']);
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'No image');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['campaign_id'] = '';
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty campaign_id');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria'] = '';
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria']['score'] = '';
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria']['maximum'] = '';
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['criteria']['cooldown'] = '';
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message'] = '';
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['title'] = '';
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['caption'] = '';
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty caption');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['text'] = '';
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$sharebutton_fail = $this->sharebutton_data;
		$sharebutton_fail['message']['image'] = '';
		$result = $this->sharebutton->add($sharebutton_fail);
		$this->unit->run($result, FALSE, 'Empty image');
	}
	
	function get_by_campaign_id_test(){
		$result = $this->sharebutton->get_by_campaign_id(1);
		unset($result['_id']);
		$this->unit->run($result, $this->sharebutton_data, 'get sharebutton by campaign_id');
		
		$result = $this->sharebutton->get_by_campaign_id('1');
		unset($result['_id']);
		$this->unit->run($result, $this->sharebutton_data, 'get sharebutton by string campaign_id');
		
		$result = $this->sharebutton->get_by_campaign_id(2);
		$this->unit->run($result, 'is_array', 'get sharebutton by campaign_id');
		$this->unit->run($result['facebook_button'], FALSE, 'facebook_button is FALSE by default');
		
		$result = $this->sharebutton->get_by_campaign_id(3);
		$this->unit->run($result, 'is_array', 'get sharebutton by campaign_id');
		$this->unit->run($result['twitter_button'], FALSE, 'twitter_button is FALSE by default');
	}
	
	function get_by_campaign_id_fail_test(){
		$result = $this->sharebutton->get_by_campaign_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->sharebutton->get_by_campaign_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->sharebutton->get_by_campaign_id(NULL);
		$this->unit->run($result, FALSE, 'not found');
	}
	
	function update_by_campaign_id_test(){
		$update_data = $this->sharebutton_update_data;
		$result = $this->sharebutton->update_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->sharebutton->get_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated sharebutton');
		
		$update_data = $this->sharebutton_update_data;
		unset($update_data['facebook_button']);
		unset($update_data['twitter_button']);
		$result = $this->sharebutton->update_by_campaign_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by campaign_id');
		
		$update_data['campaign_id'] = 1;
		$get_result = $this->sharebutton->get_by_campaign_id(1);
		$this->unit->run($result, $update_data, 'get updated sharebutton');
		$this->unit->run($result['facebook_button'], FALSE, 'get updated sharebutton, facebook_button should be set FALSE by default');
		$this->unit->run($result['twitter_button'], FALSE, 'get updated sharebutton, twitter_button should be set FALSE by default');
	}
	
	function update_by_campaign_id_fail_test(){
		$result = $this->sharebutton->update_by_campaign_id(0, $this->sharebutton_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$result = $this->sharebutton->update_by_campaign_id(NULL, $this->sharebutton_update_data);
		$this->unit->run($result, FALSE, 'No campaign_id');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']);
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No criteria');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']['score']);
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No score');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']['maximum']);
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No maximum');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['criteria']['cooldown']);
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No cooldown');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']);
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No message');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['title']);
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No title');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['text']);
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No text');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['caption']);
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No caption');
		
		$fail_update_data = $this->sharebutton_update_data;
		unset($fail_update_data['message']['image']);
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No image');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria'] = '';
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty criteria');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria']['score'] = '';
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty score');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria']['maximum'] = '';
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty maximum');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['criteria']['cooldown'] = '';
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty cooldown');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message'] = '';
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty message');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['title'] = '';
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty title');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['text'] = '';
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty text');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['image'] = '';
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$fail_update_data = $this->sharebutton_update_data;
		$fail_update_data['message']['caption'] = '';
		$result = $this->sharebutton->update_by_campaign_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty caption');
	}
}
/* End of file sharebutton_model_test.php */
/* Location: ./application/controllers/test/sharebutton_model_test.php */