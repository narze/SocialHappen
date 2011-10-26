<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Text_validate_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('text_validate');
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
	 * Test text_validate_array()
	 * @author Manassarn M.
	 */
	function text_validate_array_test(){
		$data = array(
			'test1' => array('label' => 'Test 1', 'rules' => 'required', 'input' => 1),
			'test2' => array('label' => 'Test 2', 'rules' => 'required|email', 'input' => 'valid@email.com')
		);
		$result = $this->text_validate->text_validate_array($data);
		$this->unit->run($result, TRUE, 'Passed validation');
		$this->unit->run($data, 'is_array', '$data is array');
		$this->unit->run($data['test1']['passed'], TRUE, 'test1 passed');
		$this->unit->run($data, 'is_array', '$data is array');
		$this->unit->run($data['test2']['passed'], TRUE, 'test2 passed');
		
		$data = array(
			'test1' => array('label' => 'Test 1', 'rules' => 'required', 'input' => TRUE),
			'test2' => array('label' => 'Test 2', 'rules' => 'email', 'input' => 'not_email')
		);
		$result = $this->text_validate->text_validate_array($data);
		$this->unit->run($result, FALSE, 'Failed validation : '.print_r($data,TRUE));
		$this->unit->run($data, 'is_array', '$data is array');
		$this->unit->run($data['test1']['passed'], TRUE, 'test1 passed');
		$this->unit->run($data, 'is_array', '$data is array');
		$this->unit->run($data['test2']['passed'], FALSE, 'test2 failed');
		$this->unit->run(isset($data['test2']['error_message']), TRUE, 'test2 failed : '.issetor($data['test2']['error_message']));
		$this->unit->run(strpos($data['test2']['error_message'], $data['test2']['label']) === 0, TRUE, 'error message has label');
		
		$data = array(
			'test1' => array('label' => 'Test 1', 'rules' => 'required', 'input' => TRUE),
			'test2' => array('label' => 'Test 2', 'rules' => 'email', 'input' => 'not_email', 'verify_message' => 'test defined verify message')
		);
		$result = $this->text_validate->text_validate_array($data);
		$this->unit->run($result, FALSE, 'Failed validation : '.print_r($data,TRUE));
		$this->unit->run($data, 'is_array', '$data is array');
		$this->unit->run($data['test1']['passed'], TRUE, 'test1 passed');
		$this->unit->run($data, 'is_array', '$data is array');
		$this->unit->run($data['test2']['passed'], FALSE, 'test2 failed');
		$this->unit->run(isset($data['test2']['error_message']), TRUE, 'test2 failed : '.issetor($data['test2']['error_message']));
		$this->unit->run(strpos($data['test2']['error_message'], 'test defined verify message') === 0, TRUE, 'error message has defined label');
	}
	
	/**
	 * Test required()
	 * @author Manassarn M.
	 */
	function required_test(){
		$text = '123';
		$this->unit->run($this->text_validate->required($text), TRUE, 'required('.$text.')');
		$text = 'FALSE';
		$this->unit->run($this->text_validate->required($text), TRUE, 'required('.$text.')');
		$text = TRUE;
		$this->unit->run($this->text_validate->required($text), TRUE, 'required('.$text.')');
		$text = FALSE;
		$this->unit->run($this->text_validate->required($text), FALSE, 'required('.$text.')');
		$text = 0;
		$this->unit->run($this->text_validate->required($text), TRUE, 'required('.$text.')');
		$text = 1;
		$this->unit->run($this->text_validate->required($text), TRUE, 'required('.$text.')');
		$text = NULL;
		$this->unit->run($this->text_validate->required($text), FALSE, 'required('.$text.')');
		$text = '';
		$this->unit->run($this->text_validate->required($text), FALSE, 'required('.$text.')');
	}
	
	/**
	 * Test is_email()
	 * @author Manassarn M.
	 */
	function is_email_test(){
		$text = 'test@test.com';
		$this->unit->run($this->text_validate->is_email($text), TRUE, 'is_email('.$text.')');
		$text = 'test@test.';
		$this->unit->run($this->text_validate->is_email($text), FALSE, 'is_email('.$text.')');
		$text = '@test.com';
		$this->unit->run($this->text_validate->is_email($text), FALSE, 'is_email('.$text.')');
		$text = NULL;
		$this->unit->run($this->text_validate->is_email($text), FALSE, 'is_email('.$text.')');
		$text = TRUE;
		$this->unit->run($this->text_validate->is_email($text), FALSE, 'is_email('.$text.')');
		$text = 'nyan.cat@dog.com';
		$this->unit->run($this->text_validate->is_email($text), TRUE, 'is_email('.$text.')');
	}
}
/* End of file text_validate_test.php */
/* Location: ./application/controllers/test/text_validate_test.php */
