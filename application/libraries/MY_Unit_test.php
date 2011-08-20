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
			echo "<h1 style='color:red'>TEST FAILED : {$fail_count} / ".count($result)."</h1>";
		} else {
			echo "<h1 style='color:green'>ALL PASSED ^^</h1>";
		}
		echo $this->report();
	}
}
/* End of file MY_Unit_test.php */
/* Location: ./application/libraries/MY_Unit_test.php */