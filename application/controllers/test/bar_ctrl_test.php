<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('User_id', 1);

class Bar_ctrl_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('controller/bar_ctrl');
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

	function create_company_fail_test(){
		$user_id = NULL;
		$company_name = 'companyName';
		$company_detail = 'companyDetail';
		$company_image = 'compoanyImageUrl.com/page.jpg';
		$input = compact('user_id','company_name','company_detail','company_image');
		$result = $this->bar_ctrl->create_company($input);
		$this->unit->run($result['success'], FALSE, 'create company fail', $result['success']);
		$this->unit->run($result['error'], 'No user_id specified', $result['error']);

		$user_id = User_id;
		$company_name = NULL;
		$company_detail = 'companyDetail';
		$company_image = 'compoanyImageUrl.com/page.jpg';
		$input = compact('user_id','company_name','company_detail','company_image');
		$result = $this->bar_ctrl->create_company($input);
		$this->unit->run($result['success'], FALSE, 'create company fail', $result['success']);
		$this->unit->run($result['error'], 'Insufficient data', $result['error']);

		$user_id = User_id;
		$company_name = 'companyName';
		$company_detail = NULL;
		$company_image = 'compoanyImageUrl.com/page.jpg';
		$input = compact('user_id','company_name','company_detail','company_image');
		$result = $this->bar_ctrl->create_company($input);
		$this->unit->run($result['success'], FALSE, 'create company fail', $result['success']);
		$this->unit->run($result['error'], 'Insufficient data', $result['error']);

		$user_id = User_id;
		$company_name = 'companyName';
		$company_detail = 'companyDetail';
		$company_image = NULL;
		$input = compact('user_id','company_name','company_detail','company_image');
		$result = $this->bar_ctrl->create_company($input);
		$this->unit->run($result['success'], FALSE, 'create company fail', $result['success']);
		$this->unit->run($result['error'], 'Insufficient data', $result['error']);

		// $user_id = User_id;
		// $input = compact('user_id');
		// $result = $this->bar_ctrl->create_company($input);
		// $this->unit->run($result['success'], FALSE, 'create company fail', $result['success']);
		// $this->unit->run($result['error'], 'Error adding user company', $result['error']);

		// $user_id = User_id;
		// $input = compact('user_id');
		// $result = $this->bar_ctrl->create_company($input);
		// $this->unit->run($result['success'], FALSE, 'create company fail', $result['success']);
		// $this->unit->run($result['error'], 'Error adding company', $result['error']);
	}

	function create_company_test(){
		$user_id = User_id;
		$company_name = 'companyName';
		$company_detail = 'companyDetail';
		$company_image = 'compoanyImageUrl.com/page.jpg';
		$input = compact('user_id','company_name','company_detail','company_image');
		$result = $this->bar_ctrl->create_company($input);
		$this->unit->run($result['success'], TRUE, 'create company', $result['success']);
		$data = $result['data'];
		$this->unit->run($data['company_id'], 'is_int', 'create_company success', $data['company_id']);
	}

	function select_company_fail_test(){
		$user_id = NULL;
		$result = $this->bar_ctrl->select_company($user_id);
		$this->unit->run($result['success'], FALSE, 'select_company', $result['success']);
		$this->unit->run($result['error'], 'No user_id specified', 'error message', $result['error']);
	}

	function select_company_test(){
		$user_id = User_id;
		$result = $this->bar_ctrl->select_company($user_id);
		$this->unit->run($result['success'], TRUE, 'select_company', $result['success']);
		$data = $result['data'];
		$this->unit->run(count($data['user_companies']) > 0, TRUE, 'count user_companies', count($data['user_companies']));
		$this->unit->run(isset($data['user_can_create_company']), TRUE, 'user_can_create_company', $data['user_can_create_company']);
	}
}
/* End of file campaign_ctrl_test.php */
/* Location: ./application/controllers/test/campaign_ctrl_test.php */
