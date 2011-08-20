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
	
	function report_with_counter(){
		$fail_count = 0;
		foreach($result = $this->result() as $test){
			if($test["Result"] == "Failed"){
				$fail_count++;
			}
		}
		if($fail_count){
			echo "<body bgcolor='white'><h1><span style='color:#C11B17'>TEST FAILED: {$fail_count}</span><span style='color:#C0C0C0'>, PASS: ".(count($result) - $fail_count).", FROM TOTAL: ".count($result)."</span></h1></body>";
		} else {
			echo "<h1 style='color:#008000'>ALL PASSED: ".count($result)."</h1>";
		}
		echo $this->report();
	}
}
/* End of file MY_Unit_test.php */
/* Location: ./application/libraries/MY_Unit_test.php */