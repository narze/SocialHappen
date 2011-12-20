<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Homepage_model_test extends CI_Controller {
	public $homepage_data = array(
		'app_install_id' => 1,
		'enable' => TRUE,
		'image' => 'https://localhost/assets/images/blank.png',
		'message' => 'You are not this page\'s fan, please like this page first'
	);
	public $homepage_data2 = array(
		'app_install_id' => 2,
		'enable' => FALSE,
		'image' => 'https://localhost/assets/images/blank.png',
		'message' => 'You are not this page\'s fan, please like this page first, naja'
	);
	public $homepage_update_data = array(
		'app_install_id' => 555, // should be ignored by update
		'enable' => FALSE,
		'image' => 'https://localhost/assets/images/blank.png',
		'message' => 'You are not this page\'s fan, please like this page first, again'
	);
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('homepage_model','homepage');
		$this->unit->reset_mongodb();
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
		$this->homepage->drop_collection();
	}
	
	function create_index_before_test(){
		$this->homepage->create_index();
	}

	function add_test(){
		$homepage_data = $this->homepage_data;
		$result = $this->homepage->add($homepage_data);
		$this->unit->run($result, TRUE, 'add homepage');

		$this->unit->run($this->homepage->count_all(), 1);

		$homepage_data2 = $this->homepage_data2;
		$result = $this->homepage->add($homepage_data2);
		$this->unit->run($result, TRUE, 'add homepage 2');

		$this->unit->run($this->homepage->count_all(), 2);
	}

	function add_fail_test(){
		$homepage_data = $this->homepage_data;
		$result = $this->homepage->add($homepage_data);
		$this->unit->run($result, TRUE, 'add duplicated homepage');

		$this->unit->run($this->homepage->count_all(), 2, 'count all homepage');

		$homepage_data2 = $this->homepage_data2;
		unset($homepage_data2['app_install_id']);
		$result = $this->homepage->add($homepage_data2);
		$this->unit->run($result, FALSE, 'add homepage without app_install_id');

		$this->unit->run($this->homepage->count_all(), 2, 'count all homepage');

		$homepage_data2 = $this->homepage_data2;
		unset($homepage_data2['image']);
		$result = $this->homepage->add($homepage_data2);
		$this->unit->run($result, FALSE, 'add homepage without image');

		$this->unit->run($this->homepage->count_all(), 2, 'count all homepage');

		$homepage_data2 = $this->homepage_data2;
		unset($homepage_data2['message']);
		$result = $this->homepage->add($homepage_data2);
		$this->unit->run($result, FALSE, 'add homepage without message');

		$this->unit->run($this->homepage->count_all(), 2, 'count all homepage');
	}

	function get_homepage_by_app_install_id_test(){
		$result = $this->homepage->get_homepage_by_app_install_id(1);
		unset($result['_id']);
		$this->unit->run($result, $this->homepage_data, 'get homepage by app_install_id');
		
		$result = $this->homepage->get_homepage_by_app_install_id('1');
		unset($result['_id']);
		$this->unit->run($result, $this->homepage_data, 'get homepage by string app_install_id');
		
		$result = $this->homepage->get_homepage_by_app_install_id(2);
		$this->unit->run($result, 'is_array', 'get homepage by app_install_id');
		$this->unit->run($result['enable'], FALSE, 'enable is FALSE by default');
		
		$result = $this->homepage->get_homepage_by_app_install_id(4);
		$this->unit->run($result === NULL, TRUE, 'get homepage by app_install_id');
	}
	
	function get_homepage_by_app_install_id_fail_test(){
		$result = $this->homepage->get_homepage_by_app_install_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->homepage->get_homepage_by_app_install_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->homepage->get_homepage_by_app_install_id(NULL);
		$this->unit->run($result, FALSE, 'not found');
	}
	
	function update_homepage_by_app_install_id_test(){
		$update_data = $this->homepage_update_data;
		$result = $this->homepage->update_homepage_by_app_install_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by app_install_id');
		
		$update_data['app_install_id'] = 1;
		$result = $this->homepage->get_homepage_by_app_install_id(1);
		unset($result['_id']);
		$this->unit->run($result, $update_data, 'get updated homepage');
		
		$update_data = $this->homepage_update_data;
		unset($update_data['enable']);
		$result = $this->homepage->update_homepage_by_app_install_id(1,$update_data);
		$this->unit->run($result, TRUE, 'update by app_install_id');
		
		$update_data['app_install_id'] = 1;
		$result = $this->homepage->get_homepage_by_app_install_id(1);
		unset($result['_id']);
		$this->unit->run($result['enable'], FALSE, 'get updated homepage, enable is FALSE if not set on update');
		
		$update_data = $this->homepage_update_data;
		$result = $this->homepage->update_homepage_by_app_install_id(2,$update_data);
		$this->unit->run($result, TRUE, 'update by app_install_id');
		
		$update_data['app_install_id'] = 2;
		$result = $this->homepage->get_homepage_by_app_install_id(2);
		unset($result['_id']);
		$this->unit->run($result, $update_data, 'get updated homepage, enable is FALSE if not set on update');
	}
	
	function update_homepage_by_app_install_id_fail_test(){
		$result = $this->homepage->update_homepage_by_app_install_id(0, $this->homepage_update_data);
		$this->unit->run($result, FALSE, 'No app_install_id');
		
		$result = $this->homepage->update_homepage_by_app_install_id(NULL, $this->homepage_update_data);
		$this->unit->run($result, FALSE, 'No app_install_id');
		
		$fail_update_data = $this->homepage_update_data;
		unset($fail_update_data['image']);
		$result = $this->homepage->update_homepage_by_app_install_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No image');
		
		$fail_update_data = $this->homepage_update_data;
		unset($fail_update_data['message']);
		$result = $this->homepage->update_homepage_by_app_install_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'No message');
		
		$fail_update_data = $this->homepage_update_data;
		$fail_update_data['image'] = '';
		$result = $this->homepage->update_homepage_by_app_install_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$fail_update_data = $this->homepage_update_data;
		$fail_update_data['message'] = '';
		$result = $this->homepage->update_homepage_by_app_install_id(1, $fail_update_data);
		$this->unit->run($result, FALSE, 'Empty message');
	}
	
	function homepage_data_check_test(){
		//TRUE
		$result = $this->homepage->homepage_data_check($this->homepage_data);
		$this->unit->run($result, TRUE, 'Check homepage data');
		
		$homepage2 = $this->homepage_data;
		unset($homepage2['enable']);
		$homepage2['app_install_id'] = 2;
		$result = $this->homepage->homepage_data_check($homepage2);
		$this->unit->run($result, TRUE, 'Check homepage data without setting enable');
		
		//FALSE
		
		// $result = $this->homepage->homepage_data_check($this->homepage_data);
		// $this->unit->run($result, TRUE, 'Duplicated app_install_id');
		
		$homepage_fail = $this->homepage_data;
		unset($homepage_fail['image']);
		$result = $this->homepage->homepage_data_check($homepage_fail);
		$this->unit->run($result, FALSE, 'No image');
		
		$homepage_fail = $this->homepage_data;
		unset($homepage_fail['message']);
		$result = $this->homepage->homepage_data_check($homepage_fail);
		$this->unit->run($result, FALSE, 'No message');
		
		$homepage_fail = $this->homepage_data;
		$homepage_fail['image'] = '';
		$result = $this->homepage->homepage_data_check($homepage_fail);
		$this->unit->run($result, FALSE, 'Empty image');
		
		$homepage_fail = $this->homepage_data;
		$homepage_fail['message'] = '';
		$result = $this->homepage->homepage_data_check($homepage_fail);
		$this->unit->run($result, FALSE, 'Empty message');
		
	}

	function homepage_data_process_test(){
		$homepage = $this->homepage_data;
		$homepage['enable'] = TRUE;
		$homepage = $this->homepage->homepage_data_process($homepage);
		$this->unit->run($homepage['enable'], TRUE);
		
		$homepage = $this->homepage_data;
		$homepage['enable'] = FALSE;
		$homepage = $this->homepage->homepage_data_process($homepage);
		$this->unit->run($homepage['enable'], FALSE);
		
		$homepage = $this->homepage_data;
		$homepage['enable'] = '';
		$homepage = $this->homepage->homepage_data_process($homepage);
		$this->unit->run($homepage['enable'], FALSE);
		
		$homepage = $this->homepage_data;
		$homepage['enable'] = NULL;
		$homepage = $this->homepage->homepage_data_process($homepage);
		$this->unit->run($homepage['enable'], FALSE);
		
		$homepage = $this->homepage_data;
		unset($homepage['enable']);
		$homepage = $this->homepage->homepage_data_process($homepage);
		$this->unit->run($homepage['enable'], FALSE);
	}

	function homepage_delete_test(){
		$result = $this->homepage->get_homepage_by_app_install_id(1);
		$this->unit->run($result, 'is_array', 'found');
		$this->unit->run($this->homepage->count_all(), 2, 'count all homepage');
		$result = $this->homepage->delete(1);
		$this->unit->run($result, TRUE, 'deleted (it will return true somehow)');
		$this->unit->run($this->homepage->get_homepage_by_app_install_id(1), NULL, 'not found');
		$this->unit->run($this->homepage->count_all(), 1, 'count all homepage');
	}

	function homepage_delete_fail_test(){
		$result = $this->homepage->delete(1);
		$this->unit->run($result, TRUE, 'it was deleted, not found (it will return true somehow)');
		$this->unit->run($this->homepage->count_all(), 1, 'count all homepage');

		$result = $this->homepage->delete(321);
		$this->unit->run($result, TRUE, 'not found (it will return true somehow)');
		$this->unit->run($this->homepage->count_all(), 1, 'count all homepage');


		$result = $this->homepage->delete(0);
		$this->unit->run($result, TRUE, 'not found (it will return true somehow)');
		$this->unit->run($this->homepage->count_all(), 1, 'count all homepage');
	}
}
/* End of file homepage_model_test.php */
/* Location: ./application/controllers/test/homepage_model_test.php */