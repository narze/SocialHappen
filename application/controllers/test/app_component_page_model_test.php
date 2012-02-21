<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('page_id', 1);
class App_component_page_model_test extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('app_component_page_model', 'app_component_page');
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
	
	function start_test(){
		$this->app_component_page->drop_collection();
	}
	
	function create_index_test(){
		$this->app_component_page->create_index();
	}

	function add_test(){
		$app_component = array(
   		    'page_id' => page_id,
   		    'classes' => array(
   		      array(
   		        "name" => "Founding",
   		        "invite_accepted" => 3,
   		        "achievement_id" => "4ec7507b6803fac21600000f" 
   		      ),
   		      array(
   		        "name" => "VIP",
   		        "invite_accepted" => 10,
   		        "achievement_id" => "4ec7507b6803fac216000010" 
   		      ),
   		      array(
   		        "name" => "Prime",
   		        "invite_accepted" => 50,
   		        "achievement_id" => "4ec7507b6803fac216000011" 
   		      )
   		    )
   		);
   		$result = $this->app_component_page->add($app_component);
   		$this->unit->run($result, TRUE, "\$result", $result);
	}

	function set_terms_and_conditions_test(){
		$page_id = page_id;
		$this->terms_and_conditions = 'this is terms and this is conditions';
		$result = $this->app_component_page->set_terms_and_conditions($page_id, $this->terms_and_conditions);
		$this->unit->run($result, TRUE, "\$result", $result);

		$page_id = page_id;
		$this->terms_and_conditions = 'this is terms and this is conditions 2';
		$result = $this->app_component_page->set_terms_and_conditions($page_id, $this->terms_and_conditions);
		$this->unit->run($result, TRUE, "\$result", $result);
	}

	function set_item_currency_test(){
		$page_id = page_id;
		$this->item_currency = 'THB';
		$result = $this->app_component_page->set_item_currency($page_id, $this->item_currency);
		$this->unit->run($result, TRUE, "\$result", $result);

		$page_id = page_id;
		$this->item_currency = 'USD';
		$result = $this->app_component_page->set_item_currency($page_id, $this->item_currency);
		$this->unit->run($result, TRUE, "\$result", $result);
	}

	function get_by_page_id_test(){
		$page_id = page_id;
		$result = $this->app_component_page->get_by_page_id($page_id);
		$this->unit->run($result, 'is_array', "\$result", $result);	
		$this->unit->run($result['page_id'], page_id, "\$result['page_id']", $result['page_id']);
		$this->unit->run($result['reward'], 'is_array', "\$result['reward']", $result['reward']);
		$this->unit->run($result['reward']['terms_and_conditions'], $this->terms_and_conditions, "\$result['reward']['terms_and_conditions']", $result['reward']['terms_and_conditions']);
		$this->unit->run($result['reward']['item_currency'], $this->item_currency, "\$result['reward']['item_currency']", $result['reward']['terms_and_conditions']);
	}

	function update_classes_by_page_id_test(){
		
	}

	function get_classes_by_page_id_test(){
		
	}

	function delete_test(){
		
	}

	function _update_test(){

	}

}
