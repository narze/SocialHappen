<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite_ctrl_test extends CI_Controller {
	private $campaign_id_1 = 5;
	private $app_install_id_1 = 1;
	private $campaign_id_2 = 6;
	private $app_install_id_2 = 2;
	private $campaign_id_3 = 7;
	private $app_install_id_3 = 3;
	private $page_id_1 = 1;
	private $facebook_page_id_1 = '116586141725712';
	private $invite_type_private = 1;
	private $target_facebook_id_commasep_1 = '637741627,631885465,755758746';
	private $invite_message_1 = 'Please join this unit test campaign';
	private $invite_key_1 = NULL;
	private $target_facebook_id_1 = '637741627';

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('controller/invite_ctrl');
		$this->unit->reset_dbs();
		$this->unit->mock_login();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
		$this->unit->report_with_counter();
	}

	function prepare_data_before_test(){
		$this->campaign_1 = array(
			'campaign_id' => $this->campaign_id_1,
			'app_install_id' => $this->app_install_id_1,
			'campaign_name' => 'Test campaign',
			'campaign_detail' => 'Campaign for unit test',
			'campaign_status_id' => 1,
			'campaign_start_timestamp' => date("y-m-d H:i:s"),
			'campaign_end_timestamp' => date("y-m-d H:i:s", strtotime('+5 days')),
			'campaign_end_message' => 'Test Campaign Ended'
		);
		$this->load->model('campaign_model');
		$result = $this->campaign_model->add_campaign($this->campaign_1);
		$this->unit->run($result, TRUE, 'add campaign for test', print_r($result, TRUE));

		$result = $this->campaign_model->remove_campaign($campaign_id_to_remove = 1);
		$this->unit->run($result, 1, 'remove campaign for test', print_r($result, TRUE));

		$this->load->library('app_component_lib');
		$app_component_data = array(
	      'campaign_id' => $this->campaign_id_1,
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
	    
	    $this->app_component_lib->add_campaign($app_component_data);

	    //Campaign 2 without invite message

		$this->campaign_2 = array(
			'campaign_id' => $this->campaign_id_2,
			'app_install_id' => $this->app_install_id_2,
			'campaign_name' => 'Test campaign',
			'campaign_detail' => 'Campaign for unit test',
			'campaign_status_id' => 1,
			'campaign_start_timestamp' => date("y-m-d H:i:s"),
			'campaign_end_timestamp' => date("y-m-d H:i:s", strtotime('+5 days')),
			'campaign_end_message' => 'Test Campaign Ended'
		);
		$this->load->model('campaign_model');
		$result = $this->campaign_model->add_campaign($this->campaign_2);
		$this->unit->run($result, TRUE, 'add campaign for test', print_r($result, TRUE));

	    //Campaign 3 is outdated

		$this->campaign_3 = array(
			'campaign_id' => $this->campaign_id_3,
			'app_install_id' => $this->app_install_id_3,
			'campaign_name' => 'Test campaign',
			'campaign_detail' => 'Campaign for unit test',
			'campaign_status_id' => 1,
			'campaign_start_timestamp' => date("y-m-d H:i:s", strtotime('+1 minute')),
			'campaign_end_timestamp' => date("y-m-d H:i:s", strtotime('+5 days')),
			'campaign_end_message' => 'Test Campaign Ended'
		);
		$this->load->model('campaign_model');
		$result = $this->campaign_model->add_campaign($this->campaign_3);
		$this->unit->run($result, TRUE, 'add campaign for test', print_r($result, TRUE));
	}

	function main_test(){ 
		$app_install_id = $this->app_install_id_1;
		$page_id = $this->page_id_1;
		$facebook_page_id = $this->facebook_page_id_1;
		$input = compact('app_install_id', 'page_id', 'facebook_page_id');
		
		$result = $this->invite_ctrl->main($input);
		$result1 = $result['success'] === TRUE;
		$result2 = issetor($result['campaign_id']);
		$result3 = issetor($result['app_install_id']);
		$result4 = issetor($result['invite_message']);
		$result5 = issetor($result['facebook_page_id']);
		$result6 = issetor($result['facebook_app_id']);
		$result7 = $result['facebook_channel_url'] === $this->facebook->channel_url;

		$this->unit->run($result1, TRUE, 'main test', $result1);
		$this->unit->run($result2, TRUE, 'main test', $result2);
		$this->unit->run($result3, TRUE, 'main test', $result3);
		$this->unit->run($result4, TRUE, 'main test', $result4);
		$this->unit->run($result5, TRUE, 'main test', $result5);
		$this->unit->run($result6, TRUE, 'main test', $result6);
		$this->unit->run($result7, TRUE, 'main test', $result7);
	}

	function main_fail_test(){
		//no campaign invite message
		$app_install_id = $this->app_install_id_2;
		$page_id = $this->page_id_1;
		$facebook_page_id = $this->facebook_page_id_1;
		$input = compact('app_install_id', 'page_id', 'facebook_page_id');
		
		$result = $this->invite_ctrl->main($input);
		$result1 = $result['success'] === FALSE;
		$result2 = $result['error'] === 'cannot invite : no campaign invite message';

		$this->unit->run($result1, TRUE, 'main fail test', $result1);
		$this->unit->run($result2, TRUE, 'main fail test', $result2);

		//not in campaign
		$app_install_id = $this->app_install_id_3;
		$page_id = $this->page_id_1;
		$facebook_page_id = $this->facebook_page_id_1;
		$input = compact('app_install_id', 'page_id', 'facebook_page_id');
		
		$result = $this->invite_ctrl->main($input);
		$result1 = $result['success'] === FALSE;
		$result2 = $result['error'] === 'cannot invite : not in campaign';

		$this->unit->run($result1, TRUE, 'main fail test', $result1);
		$this->unit->run($result2, TRUE, 'main fail test', $result2);

		//not in app
		$app_install_id = NULL;
		$page_id = $this->page_id_1;
		$facebook_page_id = $this->facebook_page_id_1;
		$input = compact('app_install_id', 'page_id', 'facebook_page_id');
		
		$result = $this->invite_ctrl->main($input);
		$result1 = $result['success'] === FALSE;
		$result2 = $result['error'] === 'cannot invite : not in app';

		$this->unit->run($result1, TRUE, 'main fail test', $result1);
		$this->unit->run($result2, TRUE, 'main fail test', $result2);

	}

	function create_invite_test(){
		$app_install_id = $this->app_install_id_1;
		$campaign_id = $this->campaign_id_1;
		$facebook_page_id = $this->facebook_page_id_1;
		$invite_type = $this->invite_type_private;
		$target_facebook_id = $this->target_facebook_id_commasep_1;
		$invite_message = $this->invite_message_1;
		$input = compact('app_install_id', 'campaign_id', 'facebook_page_id', 'invite_type',
			'target_facebook_id', 'invite_message');

		$result = $this->invite_ctrl->create_invite($input);

		$result1 = $result['success'] === TRUE;
		$result2 = issetor($result['invite_link']);
		$result3 = $result['invite_message'] === $invite_message;
		$result4 = $result['public_invite'] === FALSE;
		$result5 = $result['app_install_id'] === $app_install_id;

		$this->unit->run($result1, TRUE, 'create_invite test', $result['success']);
		$this->unit->run($result2, TRUE, 'create_invite test', $result['invite_link']);
		$this->unit->run($result3, TRUE, 'create_invite test', $result['invite_message']);
		$this->unit->run($result4, TRUE, 'create_invite test', $result['public_invite']);
		$this->unit->run($result5, TRUE, 'create_invite test', $result['app_install_id']);

		$this->invite_key_1 = substr(strstr($result['invite_link'], 'invite_key='), 11);
		$this->invite_link_1 = $result['invite_link'];

		//Create again
		$result = $this->invite_ctrl->create_invite($input);

		$result1 = $result['success'] === TRUE;
		$result2 = issetor($result['invite_link']);
		$result3 = $result['invite_message'] === $invite_message;
		$result4 = $result['public_invite'] === FALSE;
		$result5 = $result['app_install_id'] === $app_install_id;
		$result6 = $result['invite_link'] === $this->invite_link_1;

		$this->unit->run($result1, TRUE, 'create_invite test', $result['success']);
		$this->unit->run($result2, TRUE, 'create_invite test', $result['invite_link']);
		$this->unit->run($result3, TRUE, 'create_invite test', $result['invite_message']);
		$this->unit->run($result4, TRUE, 'create_invite test', $result['public_invite']);
		$this->unit->run($result5, TRUE, 'create_invite test', $result['app_install_id']);
	}

	function create_invite_fail_test(){
		//not in app
		$app_install_id = NULL;
		$campaign_id = $this->campaign_id_1;
		$facebook_page_id = $this->facebook_page_id_1;
		$invite_type = $this->invite_type_private;
		$target_facebook_id = $this->target_facebook_id_commasep_1;
		$invite_message = $this->invite_message_1;
		$input = compact('app_install_id', 'campaign_id', 'facebook_page_id', 'invite_type',
			'target_facebook_id', 'invite_message');

		$result = $this->invite_ctrl->create_invite($input);

		$result1 = $result['success'] === FALSE;
		$result2 = $result['error'] === 'not in app';

		$this->unit->run($result1, TRUE, 'create_invite fail test', $result['success']);
		$this->unit->run($result2, TRUE, 'create_invite fail test', $result['error']);

		//not in campaign
		$app_install_id = $this->app_install_id_3;
		$campaign_id = $this->campaign_id_3;
		$facebook_page_id = $this->facebook_page_id_1;
		$invite_type = $this->invite_type_private;
		$target_facebook_id = $this->target_facebook_id_commasep_1;
		$invite_message = $this->invite_message_1;
		$input = compact('app_install_id', 'campaign_id', 'facebook_page_id', 'invite_type',
			'target_facebook_id', 'invite_message');

		$result = $this->invite_ctrl->create_invite($input);

		$result1 = $result['success'] === FALSE;
		$result2 = $result['error'] === 'not in campaign';

		$this->unit->run($result1, TRUE, 'create_invite fail test', $result['success']);
		$this->unit->run($result2, TRUE, 'create_invite fail test', $result['error']);

		//failed creating invite
		$app_install_id = $this->app_install_id_1;
		$campaign_id = $this->campaign_id_1;
		$facebook_page_id = $this->facebook_page_id_1;
		$invite_type = NULL;
		$target_facebook_id = $this->target_facebook_id_commasep_1;
		$invite_message = $this->invite_message_1;
		$input = compact('app_install_id', 'campaign_id', 'facebook_page_id', 'invite_type',
			'target_facebook_id', 'invite_message');

		$result = $this->invite_ctrl->create_invite($input);

		$result1 = $result['success'] === FALSE;
		$result2 = $result['error'] === 'Cannot add invite';

		$this->unit->run($result1, TRUE, 'create_invite fail test', $result['success']);
		$this->unit->run($result2, TRUE, 'create_invite fail test', $result['error']);
	}

	function accept_test(){
		$invite_key = $this->invite_key_1;
		$facebook_user_id = $this->target_facebook_id_1;
		$input = compact('invite_key', 'facebook_user_id');

		$result = $this->invite_ctrl->accept($input);

		$result1 = $result['success'] === TRUE;

		$this->load->model('installed_apps_model');
		$app = $this->installed_apps_model->get_app_profile_by_app_install_id($this->app_install_id_1);
		$result2 = $result['facebook_tab_url'] === $app['facebook_tab_url'];

		$this->unit->run($result1, TRUE, 'accept test', $result['success']);
		$this->unit->run($result2, TRUE, 'accept test', $result['facebook_tab_url']);
	}

	function accept_fail_test(){
		//Invalid invite key
		$invite_key = $this->invite_key_1 . 'z';
		$facebook_user_id = $this->target_facebook_id_1;
		$input = compact('invite_key', 'facebook_user_id');

		$result = $this->invite_ctrl->accept($input);

		$result1 = $result['success'] === FALSE;
		$result2 = strpos($result['error'], 'This invite key is invalid : ') === 0;

		$this->unit->run($result1, TRUE, 'accept test', $result['success']);
		$this->unit->run($result2, TRUE, 'accept test', $result['error']);

		//No facebook url to redirect
		$invite_key = $this->invite_key_1;
		$facebook_user_id = $this->target_facebook_id_1;
		$input = compact('invite_key', 'facebook_user_id');

		//change facebook_tab_url to empty
		$this->load->model('installed_apps_model');
		$this->installed_apps_model->update(array('facebook_tab_url' => ''), array('app_install_id' => $this->app_install_id_1));

		$result = $this->invite_ctrl->accept($input);

		$result1 = $result['success'] === FALSE;
		$result2 = $result['error'] === 'No facebook url to redirect';

		$this->unit->run($result1, TRUE, 'accept test', $result['success']);
		$this->unit->run($result2, TRUE, 'accept test', $result['error']);
	}
}