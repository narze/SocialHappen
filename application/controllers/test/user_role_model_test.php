<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_role_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('user_role_model','user_role');
		$this->unit->reset_mysql();
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

	/**
	 * Tests get_user_role_by_user_role_id()
	 * @author Manassarn M.
	 */
	function get_user_role_by_user_role_id_test(){
		$result = $this->user_role->get_user_role_by_user_role_id(1);
		$this->unit->run($result,'is_array', 'get_user_role_by_user_role_id()');
		$this->unit->run($result['user_role_name'], 'Company Admin', "\$result['user_role_name']", $result['user_role_name']);
		$this->unit->run($result['role_all'], 1, "\$result['role_all']", $result['role_all']);
		$this->unit->run($result['role_company_add'], 0, "\$result['role_company_add']", $result['role_company_add']);
		$this->unit->run($result['role_company_edit'], 0, "\$result['role_company_edit']", $result['role_company_edit']);
		$this->unit->run($result['role_company_delete'], 0, "\$result['role_company_delete']", $result['role_company_delete']);
		$this->unit->run($result['role_all_company_pages_edit'], 0, "\$result['role_all_company_pages_edit']", $result['role_all_company_pages_edit']);
		$this->unit->run($result['role_all_company_pages_delete'], 0, "\$result['role_all_company_pages_delete']", $result['role_all_company_pages_delete']);
		$this->unit->run($result['role_all_company_apps_edit'], 0, "\$result['role_all_company_apps_edit']", $result['role_all_company_apps_edit']);
		$this->unit->run($result['role_all_company_apps_delete'], 0, "\$result['role_all_company_apps_delete']", $result['role_all_company_apps_delete']);
		$this->unit->run($result['role_all_company_campaigns_edit'], 0, "\$result['role_all_company_campaigns_edit']", $result['role_all_company_campaigns_edit']);
		$this->unit->run($result['role_all_company_campaigns_delete'], 0, "\$result['role_all_company_campaigns_delete']", $result['role_all_company_campaigns_delete']);
		$this->unit->run($result['role_page_add'], 0, "\$result['role_page_add']", $result['role_page_add']);
		$this->unit->run($result['role_page_edit'], 0, "\$result['role_page_edit']", $result['role_page_edit']);
		$this->unit->run($result['role_page_delete'], 0, "\$result['role_page_delete']", $result['role_page_delete']);
		$this->unit->run($result['role_app_add'], 0, "\$result['role_app_add']", $result['role_app_add']);
		$this->unit->run($result['role_app_edit'], 0, "\$result['role_app_edit']", $result['role_app_edit']);
		$this->unit->run($result['role_app_delete'], 0, "\$result['role_app_delete']", $result['role_app_delete']);
		$this->unit->run($result['role_campaign_add'], 0, "\$result['role_campaign_add']", $result['role_campaign_add']);
		$this->unit->run($result['role_campaign_edit'], 0, "\$result['role_campaign_edit']", $result['role_campaign_edit']);
		$this->unit->run($result['role_campaign_delete'], 0, "\$result['role_campaign_delete']", $result['role_campaign_delete']);
	
		$result = $this->user_role->get_user_role_by_user_role_id(2);
		$this->unit->run($result,'is_array', 'get_user_role_by_user_role_id()');
		$this->unit->run($result['user_role_name'], 'Page Admin', "\$result['user_role_name']", $result['user_role_name']);
		$this->unit->run($result['role_all'], 0, "\$result['role_all']", $result['role_all']);
		$this->unit->run($result['role_company_add'], 0, "\$result['role_company_add']", $result['role_company_add']);
		$this->unit->run($result['role_company_edit'], 0, "\$result['role_company_edit']", $result['role_company_edit']);
		$this->unit->run($result['role_company_delete'], 0, "\$result['role_company_delete']", $result['role_company_delete']);
		$this->unit->run($result['role_all_company_pages_edit'], 0, "\$result['role_all_company_pages_edit']", $result['role_all_company_pages_edit']);
		$this->unit->run($result['role_all_company_pages_delete'], 0, "\$result['role_all_company_pages_delete']", $result['role_all_company_pages_delete']);
		$this->unit->run($result['role_all_company_apps_edit'], 0, "\$result['role_all_company_apps_edit']", $result['role_all_company_apps_edit']);
		$this->unit->run($result['role_all_company_apps_delete'], 0, "\$result['role_all_company_apps_delete']", $result['role_all_company_apps_delete']);
		$this->unit->run($result['role_all_company_campaigns_edit'], 0, "\$result['role_all_company_campaigns_edit']", $result['role_all_company_campaigns_edit']);
		$this->unit->run($result['role_all_company_campaigns_delete'], 0, "\$result['role_all_company_campaigns_delete']", $result['role_all_company_campaigns_delete']);
		$this->unit->run($result['role_page_add'], 0, "\$result['role_page_add']", $result['role_page_add']);
		$this->unit->run($result['role_page_edit'], 1, "\$result['role_page_edit']", $result['role_page_edit']);
		$this->unit->run($result['role_page_delete'], 1, "\$result['role_page_delete']", $result['role_page_delete']);
		$this->unit->run($result['role_app_add'], 0, "\$result['role_app_add']", $result['role_app_add']);
		$this->unit->run($result['role_app_edit'], 0, "\$result['role_app_edit']", $result['role_app_edit']);
		$this->unit->run($result['role_app_delete'], 0, "\$result['role_app_delete']", $result['role_app_delete']);
		$this->unit->run($result['role_campaign_add'], 0, "\$result['role_campaign_add']", $result['role_campaign_add']);
		$this->unit->run($result['role_campaign_edit'], 0, "\$result['role_campaign_edit']", $result['role_campaign_edit']);
		$this->unit->run($result['role_campaign_delete'], 0, "\$result['role_campaign_delete']", $result['role_campaign_delete']);
	
		$result = $this->user_role->get_user_role_by_user_role_id(3);
		$this->unit->run($result,'is_array', 'get_user_role_by_user_role_id()');
		$this->unit->run($result['user_role_name'], 'Test admin', "\$result['user_role_name']", $result['user_role_name']);
		$this->unit->run($result['role_all'], 0, "\$result['role_all']", $result['role_all']);
		$this->unit->run($result['role_company_add'], 1, "\$result['role_company_add']", $result['role_company_add']);
		$this->unit->run($result['role_company_edit'], 1, "\$result['role_company_edit']", $result['role_company_edit']);
		$this->unit->run($result['role_company_delete'], 1, "\$result['role_company_delete']", $result['role_company_delete']);
		$this->unit->run($result['role_all_company_pages_edit'], 1, "\$result['role_all_company_pages_edit']", $result['role_all_company_pages_edit']);
		$this->unit->run($result['role_all_company_pages_delete'], 1, "\$result['role_all_company_pages_delete']", $result['role_all_company_pages_delete']);
		$this->unit->run($result['role_all_company_apps_edit'], 1, "\$result['role_all_company_apps_edit']", $result['role_all_company_apps_edit']);
		$this->unit->run($result['role_all_company_apps_delete'], 1, "\$result['role_all_company_apps_delete']", $result['role_all_company_apps_delete']);
		$this->unit->run($result['role_all_company_campaigns_edit'], 1, "\$result['role_all_company_campaigns_edit']", $result['role_all_company_campaigns_edit']);
		$this->unit->run($result['role_all_company_campaigns_delete'], 1, "\$result['role_all_company_campaigns_delete']", $result['role_all_company_campaigns_delete']);
		$this->unit->run($result['role_page_add'], 1, "\$result['role_page_add']", $result['role_page_add']);
		$this->unit->run($result['role_page_edit'], 1, "\$result['role_page_edit']", $result['role_page_edit']);
		$this->unit->run($result['role_page_delete'], 1, "\$result['role_page_delete']", $result['role_page_delete']);
		$this->unit->run($result['role_app_add'], 1, "\$result['role_app_add']", $result['role_app_add']);
		$this->unit->run($result['role_app_edit'], 1, "\$result['role_app_edit']", $result['role_app_edit']);
		$this->unit->run($result['role_app_delete'], 1, "\$result['role_app_delete']", $result['role_app_delete']);
		$this->unit->run($result['role_campaign_add'], 1, "\$result['role_campaign_add']", $result['role_campaign_add']);
		$this->unit->run($result['role_campaign_edit'], 1, "\$result['role_campaign_edit']", $result['role_campaign_edit']);
		$this->unit->run($result['role_campaign_delete'], 1, "\$result['role_campaign_delete']", $result['role_campaign_delete']);
	}

	function get_all_user_role_test(){
		$result = $this->user_role->get_all_user_role();
		$this->unit->run(count($result), 3, "count(\$result)", count($result));
	}
	
	/**
	 * Test add_user_role() and remove_user_role()
	 * @author Manassarn M.
	 */
	function add_user_role_and_remove_user_role_test(){
		$user = array(
								    'user_role_name' => 'Company Admin Test',
									'role_all' => 1,
									'role_company_add' => 0,
									'role_company_edit' => 0,
									'role_company_delete' => 0,
									'role_all_company_pages_edit' => 0,
									'role_all_company_pages_delete' => 0,
									'role_all_company_apps_edit' => 0,
									'role_all_company_apps_delete' => 0,
									'role_all_company_campaigns_edit' => 0,
									'role_all_company_campaigns_delete' => 0,
									'role_page_add' => 0,
									'role_page_edit' => 0,
									'role_page_delete' => 0,
									'role_app_add' => 0,
									'role_app_edit' => 0,
									'role_app_delete' => 0,
									'role_campaign_add' => 0,
									'role_campaign_edit' => 0,
									'role_campaign_delete' => 0
								);
		$user_role_id = $this->user_role->add_user_role($user);
		$this->unit->run($user_role_id,'is_int','add_user_role()');
		
		$removed = $this->user_role->remove_user_role($user_role_id);
		$this->unit->run($removed,'is_true','remove_user_role()');
		
		$removed_again = $this->user_role->remove_user_role($user_role_id);
		$this->unit->run($removed_again,'is_false','remove_user_role()');
	}
	
	/**
	 * Test update_user_role()
	 * @author Manassarn M.
	 */
	function update_user_role_test(){
		$new_role_company_add = 1;
		$data = array(
			'role_company_add' => $new_role_company_add
		);
		$result = $this->user_role->update_user_role(1,$data);
		$this->unit->run($result === TRUE, 'is_true', 'Updated role_company_add without error');
		
		$result = $this->user_role->get_user_role_by_user_role_id(1);
		$this->unit->run($result['role_company_add'] == $new_role_company_add,'is_true',"Updated role_company_add to {$new_role_company_add}");
		
	}
}
/* End of file campaign_model_test.php */
/* Location: ./application/controllers/test/campaign_model_test.php */