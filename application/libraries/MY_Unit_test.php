<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Unit test class
 * Extend CI's Unit test class
 * @author Manassarn M.
 */
class MY_Unit_test extends CI_Unit_test {

	private $CI;

	function __construct(){
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->library('db_sync');
		$this->CI->db_sync->use_test_db(TRUE);
		echo '[mysql] Test with database : ' . $this->CI->db->database . '<br />';

	}

	function reset_dbs($silent = TRUE){
		$this->reset_mongodb($silent);
		$this->reset_mysql($silent);
	}

	function reset_mysql($silent = TRUE){
		ob_start();
		$this->CI->db_sync->mysql_reset_and_remigrate();
		if($silent){
			$this->mysql_reset_result = ob_get_contents();
		}
		ob_end_clean();
		echo "Reset mysql test db<br />";
	}

	function reset_mongodb($silent = TRUE){
		ob_start();
		$this->CI->db_sync->mongodb_reset();
		if($silent){
			$this->mongodb_reset_result = ob_get_contents();
		}
		ob_end_clean();
		echo "Reset mongo test db<br />";
	}

	function report_with_counter(){
		if(isset($this->CI->mongo_db)){
			echo '[mongodb] Test with database : ' . $this->CI->mongo_db->__toString() . '<br />';
		}
		$fail_count = 0;
		$failed_string = '';
		foreach($result = $this->result() as $test){
			if($test["Result"] == "Failed"){var_dump($test);
				$fail_count++;
				$notes = is_array($test['Notes']) ? print_r($test['Notes'], TRUE) : $test['Notes'];
				$failed_string .= "<div style='color:#C11B17'>{$test['Line Number']} : {$test['Test Name']} : {$notes}</div>\n";
			}
		}
		if($fail_count){
			echo "<body bgcolor='white'><h1><span style='color:#C11B17'>TEST FAILED: {$fail_count}</span><span style='color:#C0C0C0'>, PASS: ".(count($result) - $fail_count).", FROM TOTAL: ".count($result)."</span></h1>";
			echo $failed_string;
			echo "</body>";
			echo $this->report();
		} else {
			echo "<h1 style='color:#008000'>ALL PASSED: ".count($result)."</h1>";
		}
	}

	function __destruct(){ //Print reset result if $silent == FALSE
		if(isset($this->mysql_reset_result)){
			echo $this->mysql_reset_result;
		}
		if(isset($this->mongodb_reset_result)){
			echo $this->mongodb_reset_result;
		}
	}

	function mock_login(){

		$userdata = array(
			'user_id' => 1,
			'user_facebook_id' => '713558190',
			'logged_in' => TRUE
		);

		$this->CI->session->set_userdata($userdata);
	}
}
/* End of file MY_Unit_test.php */
/* Location: ./application/libraries/MY_Unit_test.php */