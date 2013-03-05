<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coupon_lib_test extends CI_Controller {

	private $coupon_id1 = '';
	private $coupon_hash1 = '';
	private $coupon_id2 = '';
	private $coupon_hash2 = '';
	private $coupon_id3 = '';
	private $coupon_hash3 = '';
	private $coupon_id4 = '';
	private $coupon_hash4 = '';
	private $coupon_id5 = '';
	private $coupon_hash5 = '';
	private $coupon_id6 = '';
	private $coupon_hash6 = '';
	private $coupon_object1 = array(
							'company_id' => 1,
							'challenge_id' => 1,
							'user_id' => 1,
							'confirmed' => NULL,
							'confirmed_by_id' => NULL,
							'reward_item_id' => 1,
						);
	private $coupon_object2 = array(
							'company_id' => 1,
							'challenge_id' => 1,
							'user_id' => 1,
							'confirmed' => NULL,
							'confirmed_by_id' => NULL,
							'reward_item_id' => 1,
						);
	private $coupon_object3 = array(
							'company_id' => 1,
							'challenge_id' => 2,
							'user_id' => 2,
							'confirmed' => NULL,
							'confirmed_by_id' => NULL,
							'reward_item_id' => 1,
						);
	private $coupon_object4 = array(
							'company_id' => 2,
							'challenge_id' => 3,
							'user_id' => 2,
							'confirmed' => NULL,
							'confirmed_by_id' => NULL,
							'reward_item_id' => 1,
						);
	private $coupon_object5 = array(
							'company_id' => 2,
							'challenge_id' => 3,
							'user_id' => 3,
							'confirmed' => NULL,
							'confirmed_by_id' => NULL,
							'reward_item_id' => 1,
						);
	private $coupon_object6 = array(
							'company_id' => 2,
							'challenge_id' => 4,
							'user_id' => 3,
							'confirmed' => NULL,
							'confirmed_by_id' => NULL,
							'reward_item_id' => 1,
						);
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
	  	$this->load->library('coupon_lib');
	  	$this->unit->reset_dbs();
	}

	function __destruct(){
		$this->unit->report_with_counter();
	}

	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)/",$method)){
    			$this->$method();
    		}
		}
	}

	function create_coupon_test() {
		
		$this->coupon_id1 = $this->coupon_lib->create_coupon($this->coupon_object1);
		$this->coupon_id2 = $this->coupon_lib->create_coupon($this->coupon_object2);
		$this->coupon_id3 = $this->coupon_lib->create_coupon($this->coupon_object3);
		$this->coupon_id4 = $this->coupon_lib->create_coupon($this->coupon_object4);
		$this->coupon_id5 = $this->coupon_lib->create_coupon($this->coupon_object5);
		$this->coupon_id6 = $this->coupon_lib->create_coupon($this->coupon_object6);

		$this->unit->run($this->coupon_id1 , 'is_string', "\$coupon_id", $this->coupon_id1 );
		$this->unit->run($this->coupon_id2 , 'is_string', "\$coupon_id", $this->coupon_id2 );
		$this->unit->run($this->coupon_id3 , 'is_string', "\$coupon_id", $this->coupon_id3 );
		$this->unit->run($this->coupon_id4 , 'is_string', "\$coupon_id", $this->coupon_id4 );
		$this->unit->run($this->coupon_id5 , 'is_string', "\$coupon_id", $this->coupon_id5 );
		$this->unit->run($this->coupon_id6 , 'is_string', "\$coupon_id", $this->coupon_id6 );
	}

	function get_by_hash_test(){

		$this->coupon_hash1 = strrev(sha1($this->coupon_id1));
		$this->coupon_hash2 = strrev(sha1($this->coupon_id2));
		$this->coupon_hash3 = strrev(sha1($this->coupon_id3));
		$this->coupon_hash4 = strrev(sha1($this->coupon_id4));
		$this->coupon_hash5 = strrev(sha1($this->coupon_id5));
		$this->coupon_hash6 = strrev(sha1($this->coupon_id6));

		$coupon1 = $this->coupon_lib->get_by_hash($this->coupon_hash1);
		$coupon2 = $this->coupon_lib->get_by_hash($this->coupon_hash2);
		$coupon3 = $this->coupon_lib->get_by_hash($this->coupon_hash3);
		$coupon4 = $this->coupon_lib->get_by_hash($this->coupon_hash4);
		$coupon5 = $this->coupon_lib->get_by_hash($this->coupon_hash5);
		$coupon6 = $this->coupon_lib->get_by_hash($this->coupon_hash6);


		unset($coupon1['_id']);
		unset($coupon1['hash']);
		unset($coupon1['timestamp']);
		unset($coupon1['confirmed_timestamp']);
		unset($coupon1['code']);
		unset($coupon2['_id']);
		unset($coupon2['hash']);
		unset($coupon2['timestamp']);
		unset($coupon2['confirmed_timestamp']);
		unset($coupon2['code']);
		unset($coupon3['_id']);
		unset($coupon3['hash']);
		unset($coupon3['timestamp']);
		unset($coupon3['confirmed_timestamp']);
		unset($coupon3['code']);
		unset($coupon4['_id']);
		unset($coupon4['hash']);
		unset($coupon4['timestamp']);
		unset($coupon4['confirmed_timestamp']);
		unset($coupon4['code']);
		unset($coupon5['_id']);
		unset($coupon5['hash']);
		unset($coupon5['timestamp']);
		unset($coupon5['confirmed_timestamp']);
		unset($coupon5['code']);
		unset($coupon6['_id']);
		unset($coupon6['hash']);
		unset($coupon6['timestamp']);
		unset($coupon6['confirmed_timestamp']);
		unset($coupon6['code']);

		$this->unit->run($coupon1 , $this->coupon_object1, "\$coupon_id", print_r($coupon1, TRUE) );
		$this->unit->run($coupon2 , $this->coupon_object2, "\$coupon_id", print_r($coupon2, TRUE) );
		$this->unit->run($coupon3 , $this->coupon_object3, "\$coupon_id", print_r($coupon3, TRUE) );
		$this->unit->run($coupon4 , $this->coupon_object4, "\$coupon_id", print_r($coupon4, TRUE) );
		$this->unit->run($coupon5 , $this->coupon_object5, "\$coupon_id", print_r($coupon5, TRUE) );
		$this->unit->run($coupon6 , $this->coupon_object6, "\$coupon_id", print_r($coupon6, TRUE) );

	}

	function get_coupon_admin_url_test(){
		$url1 = $this->coupon_lib->get_coupon_admin_url(array('coupon_id' => $this->coupon_id1));
		$url2 = $this->coupon_lib->get_coupon_admin_url(array('coupon_hash' => $this->coupon_hash2));
		$url3 = $this->coupon_lib->get_coupon_admin_url(array('coupon_id' => $this->coupon_id3));
		$url4 = $this->coupon_lib->get_coupon_admin_url(array('coupon_hash' => $this->coupon_hash4));
		$url5 = $this->coupon_lib->get_coupon_admin_url(array('coupon_id' => $this->coupon_id5));
		$url6 = $this->coupon_lib->get_coupon_admin_url(array('coupon_hash' => $this->coupon_hash6));

		$this->unit->run($url1 , base_url().'redirect/coupon/'.$this->coupon_hash1, "\$coupon_id", $url1 );
		$this->unit->run($url2 , base_url().'redirect/coupon/'.$this->coupon_hash2, "\$coupon_id", $url2 );
		$this->unit->run($url3 , base_url().'redirect/coupon/'.$this->coupon_hash3, "\$coupon_id", $url3 );
		$this->unit->run($url4 , base_url().'redirect/coupon/'.$this->coupon_hash4, "\$coupon_id", $url4 );
		$this->unit->run($url5 , base_url().'redirect/coupon/'.$this->coupon_hash5, "\$coupon_id", $url5 );
		$this->unit->run($url6 , base_url().'redirect/coupon/'.$this->coupon_hash6, "\$coupon_id", $url6 );
	}

	function list_user_coupon_test(){
		$coupon_list1 = $this->coupon_lib->list_user_coupon(1);
		$coupon_list2 = $this->coupon_lib->list_user_coupon(2);
		$coupon_list3 = $this->coupon_lib->list_user_coupon(3);
		
		$this->unit->run(sizeof($coupon_list1) , 2, "\$coupon_list1", $coupon_list1 );
		$this->unit->run(sizeof($coupon_list2) , 2, "\$coupon_list1", $coupon_list2 );
		$this->unit->run(sizeof($coupon_list3) , 2, "\$coupon_list1", $coupon_list3 );
		
	}

	function list_challenge_coupon_test(){
		$coupon_list1 = $this->coupon_lib->list_challenge_coupon(1);
		$coupon_list2 = $this->coupon_lib->list_challenge_coupon(2);
		$coupon_list3 = $this->coupon_lib->list_challenge_coupon(3);
		$coupon_list4 = $this->coupon_lib->list_challenge_coupon(4);
		
		$this->unit->run(sizeof($coupon_list1) , 2, "\$coupon_list1", $coupon_list1 );
		$this->unit->run(sizeof($coupon_list2) , 1, "\$coupon_list1", $coupon_list2 );
		$this->unit->run(sizeof($coupon_list3) , 2, "\$coupon_list1", $coupon_list3 );
		$this->unit->run(sizeof($coupon_list4) , 1, "\$coupon_list1", $coupon_list4 );

	}

	function list_user_challenge_coupon_test(){
		$coupon_list1 = $this->coupon_lib->list_user_challenge_coupon(1,1);
		$coupon_list2 = $this->coupon_lib->list_user_challenge_coupon(2,2);
		$coupon_list3 = $this->coupon_lib->list_user_challenge_coupon(2,3);
		$coupon_list4 = $this->coupon_lib->list_user_challenge_coupon(3,3);
		$coupon_list5 = $this->coupon_lib->list_user_challenge_coupon(3,4);

		$this->unit->run(sizeof($coupon_list1) , 2, "\$coupon_list1", $coupon_list1 );
		$this->unit->run(sizeof($coupon_list2) , 1, "\$coupon_list1", $coupon_list2 );
		$this->unit->run(sizeof($coupon_list3) , 1, "\$coupon_list1", $coupon_list3 );
		$this->unit->run(sizeof($coupon_list4) , 1, "\$coupon_list1", $coupon_list4 );
		$this->unit->run(sizeof($coupon_list5) , 1, "\$coupon_list1", $coupon_list5 );
		
	}

	function list_company_coupon_test(){
		$coupon_list1 = $this->coupon_lib->list_company_coupon(1);
		$coupon_list2 = $this->coupon_lib->list_company_coupon(2);
		
		$this->unit->run(sizeof($coupon_list1) , 3, "\$coupon_list1", $coupon_list1 );
		$this->unit->run(sizeof($coupon_list2) , 3, "\$coupon_list1", $coupon_list2 );

	}

	function confirm_coupon_test(){
		$this->load->library('reward_lib');
		//TO-DO
	}

}
/* End of file coupon_lib_test.php */
/* Location: ./application/controllers/test/coupon_test.php */