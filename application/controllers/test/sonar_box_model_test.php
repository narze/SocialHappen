<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sonar_box_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('sonar_box_model');
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

	function setup_before_test(){
		$this->unit->reset_mongodb();
	}

	function create_index_test(){
		$this->sonar_box_model->recreateIndex();
	}

	function add_sonar_box_test() {
		$data = array(
			'name' => 'Sonar 001',
			'data' => '01013212',
			'challenge_id' => NULL,
			'info' => array()
		);
		$result = $this->sonar_box_model->add_sonar_box($data);
		$this->unit->run($result, 'is_array', "\$result", $result);
	}

	function _generate_sonar_data_test() {
		$result = $this->sonar_box_model->_generate_sonar_data();
		$this->unit->run(strlen($result) === 8, TRUE, "\$result", $result);

		$result = base_convert($result, 4, 10);
		$in_range = $result >= 0 && $result <= 65535;
		$this->unit->run($result, TRUE, "\$result", $result);
	}

	function generate_safe_sonar_data_test() {
		$result = $this->sonar_box_model->generate_safe_sonar_data();
		$this->unit->run(strlen($result) === 8, TRUE, "\$result", $result);

		$result = base_convert($result, 4, 10);
		$in_range = $result >= 0 && $result <= 65535;
		$this->unit->run($in_range, TRUE, "\$result", $in_range);
	}

	function check_sonar_data_test() {
		$result = $this->sonar_box_model->check_sonar_data('01013212');
		$this->unit->run($result, TRUE, "\$result", $result);
		$result = $this->sonar_box_model->check_sonar_data('000000001');
		$this->unit->run($result, FALSE, "\$result", $result);
		$result = $this->sonar_box_model->check_sonar_data('00000000');
		$this->unit->run($result, FALSE, "\$result", $result);
	}
}
/* End of file sonar_box_model_test.php */
/* Location: ./application/controllers/test/sonar_box_model_test.php */