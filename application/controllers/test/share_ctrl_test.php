<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('App_install_id_1',1);
define('App_install_id_2',2);
define('App_install_id_3',3);
define('App_install_id_4',30);
define('Campaign_id_1',11);
define('Campaign_id_2',21);
define('Campaign_id_3',31);
define('Share_link','http://socialhappen.com');
define('User_id', 1);
define('App_id', 1);

class Share_ctrl_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('controller/share_ctrl');
		$this->unit->reset_dbs();
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

	function setup_before_test(){
		$this->campaign_1 = array(
			'campaign_id' => Campaign_id_1,
			'app_install_id' => App_install_id_1,
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
	      'campaign_id' => Campaign_id_1,
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

	    //Campaign 2 without share message

		$this->campaign_2 = array(
			'campaign_id' => Campaign_id_2,
			'app_install_id' => App_install_id_2,
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
			'campaign_id' => Campaign_id_3,
			'app_install_id' => App_install_id_3,
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

	function main_fail_test(){
		$app_install_id = App_install_id_1;
		$share_link = Share_link;
		$result = $this->share_ctrl->main($app_install_id, $share_link);
		$this->unit->run($result['success'], FALSE, 'main success', $result['success']);
		$this->unit->run($result['error'], 'cannot share, please login', "result['error']", $result['error']);

		$this->unit->mock_login();

		$app_install_id = NULL;
		$share_link = Share_link;
		$result = $this->share_ctrl->main($app_install_id, $share_link);
		$this->unit->run($result['success'], FALSE, 'main success', $result['success']);
		$this->unit->run($result['error'], 'cannot share, not in app', "result['error']", $result['error']);

		$app_install_id = App_install_id_4;
		$share_link = Share_link;
		$result = $this->share_ctrl->main($app_install_id, $share_link);
		$this->unit->run($result['success'], FALSE, 'main success', $result['success']);
		$this->unit->run($result['error'], 'cannot share, no campaign', "result['error']", $result['error']);

		$app_install_id = App_install_id_2;
		$share_link = Share_link;
		$result = $this->share_ctrl->main($app_install_id, $share_link);
		$this->unit->run($result['success'], FALSE, 'main success', $result['success']);
		$this->unit->run($result['error'], 'cannot share, no share message', "result['error']", $result['error']);
	}

	function main_test(){
		$app_install_id = App_install_id_1;
		$share_link = Share_link;
		$result = $this->share_ctrl->main($app_install_id, $share_link);
		$this->unit->run($result['success'], TRUE, 'main success', $result['success']);
		$data = $result['data'];
		$this->unit->run(isset($data['user']), TRUE, "data['user']", isset($data['user']));
		$this->unit->run(isset($data['twitter_checked']), TRUE, "data['twitter_checked']", $data['twitter_checked']);
		$this->unit->run($data['facebook_checked'], TRUE, "data['facebook_checked']", $data['facebook_checked']);
		$this->unit->run($data['share_message'], 'is_string', "data['share_message']", $data['share_message']);
		$this->unit->run($data['share_link'], Share_link, "data['share_link']", $data['share_link']);
		$this->unit->run($data['app_install_id'], TRUE, "data['app_install_id']", $data['app_install_id']);
	}

	function share_submit_fail_test(){

	}

	function share_submit_test(){
		$user_id = User_id;
		$app_install_id = App_install_id_1;
		$app_id = App_id;
		$result = $this->share_ctrl->share_submit($user_id, $app_install_id, $app_id);
		$this->unit->run($result['success'], TRUE, "result['success']", $result['success']);
		// $data = $result['data'];
		$this->unit->run($result['audit_success'], TRUE, "result['audit_success']", $result['audit_success']);
		$this->unit->run($result['achievement_stat_success'], TRUE, "result['achievement_stat_success']", $result['achievement_stat_success']);

		//Check latest audit
		$this->load->library('audit_lib');
		$audits = $this->audit_lib->list_recent_audit(1);
		$this->unit->run(!!$audits[0]['image'], TRUE, "\$audits[0]['image']", $audits[0]['image']);
	}
}

/* End of s app_test.php */
/* Location: ./application/controllers/test/app_test.php */
