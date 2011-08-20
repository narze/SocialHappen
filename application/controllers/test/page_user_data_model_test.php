<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_user_data_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('page_user_data_model','page_users');
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
	
	// function pre_test(){
		// $this->load->model('fields_model','fields');
		// $this->fields->add_fields(100,array('first_name','last_name','id'));
	// }
	
	/**
	 * Test add_page_user()
	 * @author Manassarn M.
	 */
	function add_page_user_test(){
		$user = array(
						'user_id' => '1',
						'page_id' => '2',
						'user_data' => array('size' => 'M', 'color'=> 'red')
					);
		$result = $this->page_users->add_page_user($user);
		$this->unit->run($result,'is_true',"add_page_user()");
		$result = $this->page_users->add_page_user($user);
		$this->unit->run($result,'is_false',"duplicate add_page_user()");
		
		$user = array(
						'user_id' => '2',
						'page_id' => '2',
						'user_data' => array('size' => 'M')
					);
		$result = $this->page_users->add_page_user($user);
		$this->unit->run($result,'is_true',"add_page_user() with incomplete user_data");
		
		$user = array(
						'user_id' => '3',
						'page_id' => '2',
						'user_data' => array('foo'=> 'bar')
					);
		$result = $this->page_users->add_page_user($user);
		$this->unit->run($result,'is_true',"add_page_user() w/ wrong user_data");
		
		$user = array(
						'user_id' => '4',
						'page_id' => '2',
						'user_data' => NULL
					);
		$result = $this->page_users->add_page_user($user);
		$this->unit->run($result,'is_true',"add_page_user() w/ NULL user_data");
		
		$user = array(
						//'user_id' => '4',
						'page_id' => '2',
						'user_data' => array('size' => 'M', 'color'=> 'red')
					);
		$result = $this->page_users->add_page_user($user);
		$this->unit->run($result,'is_false',"add_page_user() w/o user_id");
		
		$user = array(
						'user_id' => '5',
						//'page_id' => '2',
						'user_data' => array('size' => 'M', 'color'=> 'red')
					);
		$result = $this->page_users->add_page_user($user);
		$this->unit->run($result,'is_false',"add_page_user() w/o page_id");
		

		$result = $this->page_users->add_page_user(array());
		$this->unit->run($result,'is_false',"no data add_user()");	
		
		$user = array(
						'user_id' => '6',
						'page_id' => '100',
						'user_data' => array('size' => 'M', 'color'=> 'red')
					);
		$result = $this->page_users->add_page_user($user);
		$this->unit->run($result,'is_false',"add_page_user() no page exist");
		
		$user = array(
						'user_id' => '100',
						'page_id' => '2',
						'user_data' => array('size' => 'M', 'color'=> 'red')
					);
		$result = $this->page_users->add_page_user($user);
		$this->unit->run($result,'is_false',"add_page_user() no user exist");
		
	}
	
	/**
	 * Tests get_page_user_by_user_id_and_page_id()
	 * @author Manassarn M.
	 */
	function get_page_user_by_user_id_and_page_id_test(){
		$result = $this->page_users->get_page_user_by_user_id_and_page_id(NULL, NULL);
		$this->unit->run($result,'is_null', 'get_page_user_by_user_id_and_page_id()');
		
		$result = $this->page_users->get_page_user_by_user_id_and_page_id(NULL, 2);
		$this->unit->run($result,'is_null', 'get_page_user_by_user_id_and_page_id()');
		$this->unit->run($result,'is_null','user_data');
		
		$result = $this->page_users->get_page_user_by_user_id_and_page_id(1, NULL);
		$this->unit->run($result,'is_null', 'get_page_user_by_user_id_and_page_id()');
		$this->unit->run($result,'is_null','user_data');
		
		$result = $this->page_users->get_page_user_by_user_id_and_page_id(1, 2);
		$this->unit->run($result,'is_array', 'get_page_user_by_user_id_and_page_id()');
		$this->unit->run($result['user_facebook_id'],'is_string','first user_facebook_id');
		$this->unit->run($result['user_data'],'is_array','user_data');
		$this->unit->run($result['user_data']['size'] == 'M','is_true','size == "M"');
		$this->unit->run($result['user_data']['color'] == 'red','is_true','color == "red"');
		
		$result = $this->page_users->get_page_user_by_user_id_and_page_id(2, 2);
		$this->unit->run($result,'is_array', 'get_page_user_by_user_id_and_page_id()');
		$this->unit->run($result['user_facebook_id'],'is_string','first user_facebook_id');
		$this->unit->run($result['user_data'],'is_array','user_data');
		$this->unit->run($result['user_data']['size'] == 'M','is_true','size == "M"');
		$this->unit->run($result['user_data']['color'] == '','is_true','color == ""');
	}
	
	/**
	 * Tests get_page_users_by_page_id()
	 * @author Manassarn M.
	 */
	function get_page_users_by_page_id_test(){
		$result = $this->page_users->get_page_users_by_page_id(NULL);
		$this->unit->run($result,'is_array', 'get_page_users_by_page_id(NULL)');
		$this->unit->run(count($result) == 0,'is_true','empty array');
		
		$result = $this->page_users->get_page_users_by_page_id(1000);
		$this->unit->run($result,'is_array', 'get_page_users_by_page_id(1000) : not found');
		$this->unit->run(count($result) == 0,'is_true','empty array');
		
		$result = $this->page_users->get_page_users_by_page_id(2);
		$this->unit->run($result,'is_array', 'get_page_users_by_page_id(2)');
		$this->unit->run($result[0],'is_array','first row');
		$this->unit->run($result[0]['user_facebook_id'],'is_string','first user_facebook_id');
		$this->unit->run($result[0]['user_data'],'is_array','user_data');
		$this->unit->run($result[0]['user_data']['size'] == 'M','is_true','size == "M"');
		$this->unit->run($result[0]['user_data']['color'] == 'red','is_true','color == "red"');
	}
	
	/**
	 * Tests update_page_user_by_user_id_and_page_id()
	 * @author Manassarn M.
	 */
	function update_page_user_by_user_id_and_page_id_test(){
		$result = $this->page_users->update_page_user_by_user_id_and_page_id(1,2,array('size' => 'S', 'color'=> 'green'));
		$this->unit->run($result, 'is_true', 'modified size,color');
		
		$result = $this->page_users->get_page_user_by_user_id_and_page_id(1, 2);
		$this->unit->run($result['user_data']['size'] == 'S','is_true','size == "S"');
		$this->unit->run($result['user_data']['color'] == 'green','is_true','color == "green"');
		
		$result = $this->page_users->update_page_user_by_user_id_and_page_id(2,2,array('color'=> ''));
		$this->unit->run($result, 'is_true', 'modified color');
		
		$result = $this->page_users->get_page_user_by_user_id_and_page_id(2, 2);
		$this->unit->run($result['user_data']['size'] == 'M','is_true','size == "M"');
		$this->unit->run($result['user_data']['color'] == '','is_true','color == ""');
		
		$result = $this->page_users->update_page_user_by_user_id_and_page_id(NULL,2,array('size' => 'L', 'color'=> 'white'));
		$this->unit->run($result, 'is_false', 'modified without user_id');
		
		$result = $this->page_users->update_page_user_by_user_id_and_page_id(2,NULL,array('size' => 'L', 'color'=> 'white'));
		$this->unit->run($result, 'is_false', 'modified without page_id');
		
		$result = $this->page_users->update_page_user_by_user_id_and_page_id(2,2,array('first_name' => 'first_modified', 'last_name'=> '' , 'id' => 123456));
		$this->unit->run($result, 'is_true', 'not found element to modify');
		
		$result = $this->page_users->update_page_user_by_user_id_and_page_id(2,2,array());
		$this->unit->run($result,'is_false',"no data update :  no input");	
	}
	
	/**
	 * Tests remove_page_user_by_user_id_and_page_id()
	 * @author Manassarn M.
	 */
	function remove_page_user_by_user_id_and_page_id_test(){
		$result = $this->page_users->remove_page_user_by_user_id_and_page_id(1,2);
		$this->unit->run($result,'is_true', 'remove_page_user_by_user_id_and_page_id(1,2)');
		
		$result = $this->page_users->remove_page_user_by_user_id_and_page_id(2,2);
		$this->unit->run($result,'is_true', 'remove_page_user_by_user_id_and_page_id(2,2)');
		
		$result = $this->page_users->remove_page_user_by_user_id_and_page_id(3,2);
		$this->unit->run($result,'is_true', 'remove_page_user_by_user_id_and_page_id(3,2)');
		
		$result = $this->page_users->remove_page_user_by_user_id_and_page_id(4,2);
		$this->unit->run($result,'is_true', 'remove_page_user_by_user_id_and_page_id(4,2)');
		
		$result = $this->page_users->remove_page_user_by_user_id_and_page_id(4,2);
		$this->unit->run($result,'is_false', 'remove_page_user_by_user_id_and_page_id(4,2) again');
		
		$result = $this->page_users->remove_page_user_by_user_id_and_page_id(1,NULL);
		$this->unit->run($result,'is_false', 'remove_page_user_by_user_id_and_page_id(1,NULL)');
		
		$result = $this->page_users->remove_page_user_by_user_id_and_page_id(NULL,2);
		$this->unit->run($result,'is_false', 'remove_page_user_by_user_id_and_page_id(1,NULL)');
		
		$result = $this->page_users->remove_page_user_by_user_id_and_page_id(1,300);
		$this->unit->run($result,'is_false', 'remove_page_user_by_user_id_and_page_id(1,300) (not existed)');
	}
	
	// function post_test(){
		// $this->load->model('fields_model','fields');
		// $this->fields->remove_fields(100,array('first_name','last_name','id'));
	// }

}
/* End of file user_model_test.php */
/* Location: ./application/controllers/test/user_model_test.php */