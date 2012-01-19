<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('page_model','pages');
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
	 * Tests get_page_profile_by_page_id()
	 * @author Manassarn M.
	 */
	function get_page_profile_by_page_id_test(){
		$result = $this->pages->get_page_profile_by_page_id(1);
		$this->unit->run($result,'is_array', 'get_page_profile_by_page_id()');
		$this->unit->run($result['page_id'],'is_string','page_id');
		$this->unit->run($result['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['page_name'],'is_string','page_name');
		$this->unit->run($result['page_detail'],'is_string','page_detail');
		$this->unit->run($result['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result['page_image'],'is_string','page_image');
		$this->unit->run($result['page_status_id'] == 1,'is_true','page_status_id == 1');		
		$this->unit->run($result['page_status'] == "Not Installed",'is_true','page_status_id == "Not Installed"');	
		$this->unit->run($result['enable_facebook_page_tab'], 1, "\$result['enable_facebook_page_tab']", $result['enable_facebook_page_tab']);
		$this->unit->run($result['enable_facebook_tab_bar'], 1, "\$result['enable_facebook_tab_bar']", $result['enable_facebook_tab_bar']);
		$this->unit->run($result['enable_socialhappen_features'], 1, "\$result['enable_socialhappen_features']", $result['enable_socialhappen_features']);	
		$this->unit->run($result['page_member_limit'], 0, "\$result['page_member_limit']", $result['page_member_limit']);	
	}
	
	/**
	 * Tests get_page_profile_by_facebook_page_id()
	 * @author Manassarn M.
	 */
	function get_page_profile_by_facebook_page_id_test(){
		$result = $this->pages->get_page_profile_by_facebook_page_id('116586141725712');
		$this->unit->run($result,'is_array', 'get_page_profile_by_facebook_page_id()');
		$this->unit->run($result['page_id'],'is_string','page_id');
		$this->unit->run($result['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['page_name'],'is_string','page_name');
		$this->unit->run($result['page_detail'],'is_string','page_detail');
		$this->unit->run($result['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result['page_image'],'is_string','page_image');
		$this->unit->run($result['page_status_id'] == 1,'is_true','page_status_id == 1');		
		$this->unit->run($result['page_status'] == "Not Installed",'is_true','page_status_id == "Not Installed"');	
		$this->unit->run($result['enable_facebook_page_tab'], 1, "\$result['enable_facebook_page_tab']", $result['enable_facebook_page_tab']);
		$this->unit->run($result['enable_facebook_tab_bar'], 1, "\$result['enable_facebook_tab_bar']", $result['enable_facebook_tab_bar']);
		$this->unit->run($result['enable_socialhappen_features'], 1, "\$result['enable_socialhappen_features']", $result['enable_socialhappen_features']);
		$this->unit->run($result['page_member_limit'], 0, "\$result['page_member_limit']", $result['page_member_limit']);
	}
	
	/** 
	 * Tests get_company_pages_by_company_id()
	 * @author Manassarn M.
	 */
	function get_company_pages_by_company_id_test(){
		$result = $this->pages->get_company_pages_by_company_id(1);
		$this->unit->run($result,'is_array', 'get_company_pages_by_company_id()');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['page_name'],'is_string','page_name');
		$this->unit->run($result[0]['page_detail'],'is_string','page_detail');
		$this->unit->run($result[0]['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result[0]['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result[0]['page_image'],'is_string','page_image');
		$this->unit->run($result[0]['enable_facebook_page_tab'], 1, "\$result[0]['enable_facebook_page_tab']", $result[0]['enable_facebook_page_tab']);
		$this->unit->run($result[0]['enable_facebook_tab_bar'], 1, "\$result[0]['enable_facebook_tab_bar']", $result[0]['enable_facebook_tab_bar']);
		$this->unit->run($result[0]['enable_socialhappen_features'], 1, "\$result[0]['enable_socialhappen_features']", $result[0]['enable_socialhappen_features']);
		$this->unit->run($result[0]['page_member_limit'], 0, "\$result[0]['page_member_limit']", $result[0]['page_member_limit']);
	}
	
	/**
	 * Test add_page() and remove_page()
	 * @author Manassarn M.
	 */
	function add_page_and_remove_page_test(){
		$page = array(
							'facebook_page_id' => '1',
							'company_id' => '1',
							'page_name' => 'test',
							'page_detail' => 'test',
							'page_all_member' => 'test',
							'page_new_member' => 'test',
							'page_image' => 'test'
						);
		$page_id = $this->pages->add_page($page);
		$this->unit->run($page_id,'is_int','add_page()');
		
		$removed = $this->pages->remove_page($page_id);
		$this->unit->run($removed == 1,'is_true','remove_page()');
		
		$removed_again = $this->pages->remove_page($page_id);
		$this->unit->run($removed_again == 0,'is_true','remove_page()');
	}

	/**
	 * Test get_app_pages_by_app_install_id()
	 * @author Manassarn M.
	 */
	function get_app_pages_by_app_install_id_test(){
		$result = $this->pages->get_app_pages_by_app_install_id(1,1);
		$this->unit->run($result,'is_array', 'get_app_pages_by_app_install_id()');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($result[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($result[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run($result[0]['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result[0]['page_name'],'is_string','page_name');
		$this->unit->run($result[0]['page_detail'],'is_string','page_detail');
		$this->unit->run($result[0]['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result[0]['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result[0]['page_image'],'is_string','page_image');
		$this->unit->run($result[0]['enable_facebook_page_tab'], 1, "\$result[0]['enable_facebook_page_tab']", $result[0]['enable_facebook_page_tab']);
		$this->unit->run($result[0]['enable_facebook_tab_bar'], 1, "\$result[0]['enable_facebook_tab_bar']", $result[0]['enable_facebook_tab_bar']);
		$this->unit->run($result[0]['enable_socialhappen_features'], 1, "\$result[0]['enable_socialhappen_features']", $result[0]['enable_socialhappen_features']);
		$this->unit->run($result[0]['page_member_limit'], 0, "\$result[0]['page_member_limit']", $result[0]['page_member_limit']);
	}
	
	/**
	 * Test get_page_profile_by_campaign_id()
	 * @author Manassarn M.
	 */
	function get_page_profile_by_campaign_id_test(){
		$result = $this->pages->get_page_profile_by_campaign_id(1);
		$this->unit->run($result,'is_array', 'get_page_profile_by_campaign_id()');
		$this->unit->run($result['page_id'],'is_string','page_id');
		$this->unit->run($result['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['page_name'],'is_string','page_name');
		$this->unit->run($result['page_detail'],'is_string','page_detail');
		$this->unit->run($result['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result['page_image'],'is_string','page_image');
		$this->unit->run($result['page_status_id'] == 1,'is_true','page_status_id == 1');		
		$this->unit->run($result['page_status'] == "Not Installed",'is_true','page_status_id == "Not Installed"');
		$this->unit->run($result['enable_facebook_page_tab'], 1, "\$result['enable_facebook_page_tab']", $result['enable_facebook_page_tab']);
		$this->unit->run($result['enable_facebook_tab_bar'], 1, "\$result['enable_facebook_tab_bar']", $result['enable_facebook_tab_bar']);
		$this->unit->run($result['enable_socialhappen_features'], 1, "\$result['enable_socialhappen_features']", $result['enable_socialhappen_features']);
		$this->unit->run($result['page_member_limit'], 0, "\$result['page_member_limit']", $result['page_member_limit']);
	}
	
	/**
	 * Test get_page_profile_by_app_install_id()
	 * @author Manassarn M.
	 */
	function get_page_profile_by_app_install_id_test(){
		$result = $this->pages->get_page_profile_by_app_install_id(1);
		$this->unit->run($result,'is_array', 'get_page_profile_by_app_install_id()');
		$this->unit->run($result['page_id'],'is_string','page_id');
		$this->unit->run($result['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['page_name'],'is_string','page_name');
		$this->unit->run($result['page_detail'],'is_string','page_detail');
		$this->unit->run($result['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result['page_image'],'is_string','page_image');
		$this->unit->run($result['enable_facebook_page_tab'], 1, "\$result['enable_facebook_page_tab']", $result['enable_facebook_page_tab']);
		$this->unit->run($result['enable_facebook_tab_bar'], 1, "\$result['enable_facebook_tab_bar']", $result['enable_facebook_tab_bar']);
		$this->unit->run($result['enable_socialhappen_features'], 1, "\$result['enable_socialhappen_features']", $result['enable_socialhappen_features']);
		$this->unit->run($result['page_member_limit'], 0, "\$result['page_member_limit']", $result['page_member_limit']);
	}
	
	/**
	 * Test update_page_profile_by_page_id()
	 * @author Manassarn M.
	 */
	function update_page_profile_by_page_id_test(){
		$new_page_name = rand(1,10000);
		$data = array(
			'page_name' => $new_page_name
		);
		$result = $this->pages->update_page_profile_by_page_id(1,$data);
		$this->unit->run($result === TRUE,'is_true', 'Updated new_page_name without error');
		
		$result = $this->pages->get_page_profile_by_page_id(1);
		$this->unit->run($result['page_name'] == $new_page_name,'is_true',"Updated page_name to {$new_page_name}");
	}
	
	/**
	 * Test count_pages_by_app_id()
	 * @author Manassarn M.
	 */
	function count_pages_by_app_id_test(){
		$result = $this->pages->count_pages_by_app_id(1);
		$this->unit->run($result,'is_int', 'count_pages_by_app_id()');
	}
	
	/**
	 * Test add_page_user_fields_by_page_id()
	 * @author Manasssarn M.
	 */
	function add_page_user_fields_by_page_id_test(){
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name1',
				'required' => FALSE,
				'type' => 'text',
				'label' => 'Name 1',
				'order' => 1,
				'items' => NULL,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_array', 'Added 1 field');
		$this->unit->run(count($result) == 1, 'is_true', 'Added 1 field');
		$this->unit->run(in_array(3,$result), 'is_true', 'Added 1 field');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name2',
				'required' => FALSE,
				'type' => 'text',
				'label' => 'Name 2',
				'order' => 2,
				'items' => NULL,
				'rules' => NULL,
			),
			array(
				'name' => 'gender',
				'required' => FALSE,
				'type' => 'radio',
				'label' => 'Name 3',
				'order' => 3,
				'items' => array('radio1', 'radio2'),
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_array', 'Added 2 fields');
		$this->unit->run(count($result) == 2, 'is_true', 'Added 2 fields');
		$this->unit->run(in_array(4,$result), 'is_true', 'Added 2 fields');
		$this->unit->run(in_array(5,$result), 'is_true', 'Added 2 fields');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array());
		$this->unit->run($result, 'is_false', 'no input');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1);
		$this->unit->run($result, 'is_false', 'blank input');
		
		$result = $this->pages->add_page_user_fields_by_page_id(100, array(
			array(
				'name' => 'name4',
				'required' => FALSE,
				'type' => 'text',
				'label' => 'Name 4',
				'order' => 4,
				'items' => NULL,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'page not found');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'required' => FALSE,
				'type' => 'text',
				'label' => 'Name 4',
				'order' => 4,
				'items' => NULL,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'no name');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => '',
				'required' => FALSE,
				'type' => 'text',
				'label' => 'Name 4',
				'order' => 4,
				'items' => NULL,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'blank name');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name4',
				'required' => FALSE,
				'type' => 'text',
				'order' => 4,
				'items' => NULL,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'no Label');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name4',
				'required' => FALSE,
				'type' => 'text',
				'label' => '',
				'order' => 4,
				'items' => NULL,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'blank label');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name4',
				'required' => FALSE,
				'label' => 'Name 4',
				'order' => 4,
				'items' => NULL,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'no type');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name4',
				'required' => FALSE,
				'type' => '',
				'label' => 'Name 4',
				'order' => 4,
				'items' => NULL,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'blank type');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name4',
				'required' => FALSE,
				'type' => 'wrongtype',
				'label' => 'Name 4',
				'order' => 4,
				'items' => NULL,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'wrong type');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name5',
				'required' => TRUE,
				'type' => 'checkbox',
				'label' => 'Name 5',
				'order' => 4,
				'items' => array(1,2,3),
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_array', 'Checkbox with required = TRUE');
		$this->unit->run(count($result) == 1, 'is_true', 'Added 1 field');
		$this->unit->run(in_array(6,$result), 'is_true', 'Added 1 field');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name6',
				'required' => TRUE,
				'type' => 'checkbox',
				'label' => 'Name 5',
				'order' => 4,
				'items' => array(),
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'Checkbox without items');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name6',
				'required' => TRUE,
				'type' => 'checkbox',
				'label' => 'Name 5',
				'order' => 4,
				'items' => NULL,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'Checkbox without items');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name6',
				'required' => TRUE,
				'type' => 'checkbox',
				'label' => 'Name 5',
				'order' => 4,
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'Checkbox without items');
		
		$result = $this->pages->add_page_user_fields_by_page_id(1, array(
			array(
				'name' => 'name6',
				'required' => TRUE,
				'type' => 'checkbox',
				'label' => 'Name 5',
				'order' => 4,
				'items' => array(''),
				'rules' => NULL,
			)
		));
		$this->unit->run($result, 'is_false', 'Checkbox with blank item');
	}
	
	/**
	 * Test get_page_user_fields_by_page_id()
	 * @author Manassarn M.
	 */
	function get_page_user_fields_by_page_id_test(){
		$result = $this->pages->get_page_user_fields_by_page_id(1);
		$this->unit->run($result, 'is_array', 'get_page_user_fields_by_page_id(1)');
		$this->unit->run($result[3], 'is_array', 'id 3');
		$this->unit->run($result[3]['name'], 'is_string', "result[3]['name'] = {$result[3]['name']}");
		$this->unit->run($result[3]['type'], 'is_string', "result[3]['type'] = {$result[3]['type']}");
		$this->unit->run($result[3]['label'], 'is_string', "result[3]['label'] = {$result[3]['label']}");
		$this->unit->run($result[3]['order'], 'is_int', "result[3]['order'] = {$result[3]['order']}");
		$this->unit->run($result[3]['verify_message'], 'Please enter your '.strtolower($result[3]['label']).'.', "result[3]['verify_message'] = {$result[3]['verify_message']}");
		
		$this->unit->run($result[5]['verify_message'], 'Please select your gender.', "result[5]['verify_message'] = {$result[5]['verify_message']}");
		
		$this->unit->run($result[6], 'is_array', 'id 6');
		$this->unit->run($result[6]['required'], 'is_true', "required == true");
		
		// $this->unit->run($result[7], 'is_array', 'id 7');
		// $this->unit->run($result[7]['type'], 'text', "id_card_number type = text");
		
		// $this->unit->run($result[8], 'is_array', 'id 8');
		// $this->unit->run($result[8]['type'], 'radio', "gender type = radio");
		// $this->unit->run($result[8]['required'], 'is_true', "gender with required = true");
		
		$result = $this->pages->get_page_user_fields_by_page_id(100);
		$this->unit->run($result, 'is_false', 'get_page_user_fields_by_page_id(100)');
	}
	
	/**
	 * Test update_page_user_fields_by_page_id()
	 * @author Manassarn M.
	 */
	function update_page_user_fields_by_page_id_test(){
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				3=>array(
					'name' => 'renamed',
					'required' => TRUE,
					'label' => 'Renamed label'
				)
			)
		);
		$this->unit->run($result, 'is_true', 'Edited one field');
		
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				3=>array(
					'name' => 'renamed1',
					'required' => FALSE,
					'label' => 'New label'
				),
				4=>array(
					'name' => 'renamed2',
				),
				5=>array(
					'order' => 5
				)
			)
		);
		$this->unit->run($result, 'is_true', 'Edited fields');
		
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				3=>array(
					'name' => 'renamed1',
					'required' => FALSE,
					'type' => 'checkbox',
					'items' => array(1,2,3,4)
				),
				4=>array(
					'name' => 'renamed2',
				),
				5=>array(
					'order' => 5
				)
			)
		);
		$this->unit->run($result, 'is_true', 'Change type to checkbox with items');
		
		$result = $this->pages->update_page_user_fields_by_page_id(100, 
			array(
				3=>array(
					'name' => 'renamed1',
					'required' => FALSE,
					'label' => 'New label'
				),
				4=>array(
					'name' => 'renamed2',
				),
				5=>array(
					'order' => 5
				)
			)
		);
		$this->unit->run($result, 'is_false', 'Page not found');
		
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				3=>array(
					'name' => '',
					'required' => FALSE,
					'label' => 'New label'
				),
				4=>array(
					'name' => 'renamed2',
				),
				5=>array(
					'order' => 5
				)
			)
		);
		$this->unit->run($result, 'is_false', 'Blank name');
		
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				3=>array(
					'name' => 'renamed1',
					'required' => FALSE,
					'label' => ''
				),
				4=>array(
					'name' => 'renamed2',
				),
				5=>array(
					'order' => 5
				)
			)
		);
		$this->unit->run($result, 'is_false', 'Blank label');
		
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				3=>array(
					'name' => 'renamed1',
					'required' => FALSE,
					'type' => 'radio',
					'items' => ''
				),
				4=>array(
					'name' => 'renamed2',
				)
			)
		);
		$this->unit->run($result, 'is_false', 'Change type to radio without items');
		
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				3=>array(
					'name' => 'renamed1',
					'required' => FALSE,
					'type' => 'checkbox',
					'items' => array()
				),
				4=>array(
					'name' => 'renamed2',
				),
				5=>array(
					'order' => 5
				)
			)
		);
		$this->unit->run($result, 'is_false', 'Change type to checkbox without items');
		
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				3=>array(
					'name' => 'renamed1',
					'required' => FALSE,
					'type' => 'checkbox',
					'items' => array('')
				),
				4=>array(
					'name' => 'renamed2',
				),
				5=>array(
					'order' => 5
				)
			)
		);
		$this->unit->run($result, 'is_false', 'Change type to checkbox with blank item in array');
		
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				3=>array(
					'name' => 'renamed1',
					'required' => FALSE,
					'type' => 'wrongtype'
				),
				4=>array(
					'name' => 'renamed2',
				),
				5=>array(
					'order' => 5
				)
			)
		);
		$this->unit->run($result, 'is_false', 'wrong type');
		
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				3=>array(
					'name' => 'renamed1',
					'required' => FALSE,
					'type' => ''
				),
				4=>array(
					'name' => 'renamed2',
				),
				5=>array(
					'order' => 5
				)
			)
		);
		$this->unit->run($result, 'is_false', 'Blank type');
		
		$result = $this->pages->update_page_user_fields_by_page_id(1, 
			array(
				1000=>array(
					'name' => 'renamed1',
					'required' => FALSE
				),
				1002=>array(
					'name' => 'renamed2',
				),
				1003=>array(
					'order' => 5
				)
			)
		);
		$this->unit->run($result, 'is_false', 'Id not found');
		
		
	}
		
	/**
	 * Test remove_page_user_fields_by_page_id()
	 * @author Manassarn M.
	 */
	function remove_page_user_fields_by_page_id_test(){
		$result = $this->pages->remove_page_user_fields_by_page_id(1, array());
		$this->unit->run($result, 'is_false', 'Remove nothing');
		
		$result = $this->pages->remove_page_user_fields_by_page_id(1, array('three','four'));
		$this->unit->run($result, 'is_false', 'Wrong remove');
		
		$result = $this->pages->remove_page_user_fields_by_page_id(100, array(3,4));
		$this->unit->run($result, 'is_false', 'Wrong page');
		
		$result = $this->pages->remove_page_user_fields_by_page_id(1, array(3,4));
		$this->unit->run($result, 'is_true', 'Removed');
		
		$result = $this->pages->remove_page_user_fields_by_page_id(1, array(3,4));
		$this->unit->run($result, 'is_false', 'Remove again');
		
		$result = $this->pages->remove_page_user_fields_by_page_id(1, array(5,4));
		$this->unit->run($result, 'is_false', 'Partial wrong remove (4 is removed already)');
		
		$result = $this->pages->remove_page_user_fields_by_page_id(1, array('5'));
		$this->unit->run($result, 'is_true', 'Remove using string number');
		
		$result = $this->pages->remove_page_user_fields_by_page_id(1, 6);
		$this->unit->run($result, 'is_true', 'Remove using string number without array');
	}
	
	/**
	 * Test update_facebook_tab_url_by_page_id()
	 * @author Manassarn M.
	 */
	function update_facebook_tab_url_by_page_id_test(){
		$result = $this->pages->update_facebook_tab_url_by_page_id(1, 'http://test.com/');
		$this->unit->run($result, TRUE, 'update_facebook_tab_url_by_page_id()');
	}
	
	/**
	 * Test update_facebook_tab_url_by_facebook_page_id()
	 * @author Manassarn M.
	 */
	function update_facebook_tab_url_by_facebook_page_id_test(){
		$result = $this->pages->update_facebook_tab_url_by_facebook_page_id('116586141725712', 'http://test.com/');
		$this->unit->run($result, TRUE, 'update_facebook_tab_url_by_facebook_page_id()');
	}

	function get_page_id_by_facebook_page_id_test(){
		$result = $this->pages->get_page_id_by_facebook_page_id('116586141725712');
		$this->unit->run($result == 1, TRUE, 'get_page_id_by_facebook_page_id');
	}
}
/* End of file page_model_test.php */
/* Location: ./application/controllers/test/page_model_test.php */