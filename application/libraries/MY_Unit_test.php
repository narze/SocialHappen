<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Unit test class
 * Extend CI's Unit test class
 * @author Manassarn M.
 */
class MY_Unit_test extends CI_Unit_test {
	
	function __construct(){
		parent::__construct();
	}
	
	function reset_dbs(){
		$this->db_reset_result = file_get_contents(base_url().'dev/sync/db_reset');
		$this->mongodb_reset_result = file_get_contents(base_url().'dev/sync/mongodb_reset');
		echo "Resetted DBs<br />";
	}

	function report_with_counter(){
		$fail_count = 0;
		$failed_string = '';
		foreach($result = $this->result() as $test){
			if($test["Result"] == "Failed"){
				$fail_count++;
				$failed_string .= "<div style='color:#C11B17'>{$test['Line Number']} : {$test['Test Name']}</div>\n";
			}
		}
		if($fail_count){
			echo "<body bgcolor='white'><h1><span style='color:#C11B17'>TEST FAILED: {$fail_count}</span><span style='color:#C0C0C0'>, PASS: ".(count($result) - $fail_count).", FROM TOTAL: ".count($result)."</span></h1>";
			echo $failed_string;
			echo "</body>";
		} else {
			echo "<h1 style='color:#008000'>ALL PASSED: ".count($result)."</h1>";
		}
		echo $this->report();
	}

	function __destruct(){
		if(isset($this->db_reset_result)){
			echo $this->db_reset_result;
		}
		if(isset($this->mongodb_reset_result)){
			echo $this->mongodb_reset_result;
		}
	}
}
/* End of file MY_Unit_test.php */
/* Location: ./application/libraries/MY_Unit_test.php */