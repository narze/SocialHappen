<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Text validation library
 * 
 * Validation for text in form, similar to form_validation class.
 * 
 * @author Manassarn M.
 */
class Text_validate {
	function __construct() {
        $_this =& get_instance();
    }
	
	/**
	 * Validates array of texts
	 * @param array of texts
	 * 		ex. array('[input_name]' => array(
	 * 			'rules' => [rule1|rule2|rule3], 
	 * 			'input' => [inputtext]
	 * 		))
	 * @author Manassarn M.
	 */
	function text_validate_array(&$input = array()){
		if(!is_array($input) || !$input){
			return FALSE;
		}
		$passed = TRUE;
		foreach($input as $input_name => &$data){
			$rules = explode('|', $data['rules']);
			$label = $data['label'];
			$text = issetor($data['input'], '');
			if(count($rules) == 0){
			
			} else {
				$error_message = array();
				foreach($rules as $rule){
					switch($rule){
						case 'required' :
							if($this->required($text)){
								
							} else {
								$error_message[] = $label . ' is required';
							}
						break;
						case 'email' :
							if($this->is_email($text)){
								
							} else {
								$error_message[] = $label . ' is not a valid email';
							}
						break;
					}
				}
				if(count($error_message) > 0){
					$passed = FALSE;
					$data['passed'] = FALSE;
					if(isset($data['verify_message'])){
						$data['error_message'] = $data['verify_message'];
					} else {
						$data['error_message'] = implode(', ', $error_message);
					}
				} else {
					$data['passed'] = TRUE;
					$data['error_message'] = NULL;
				}
			}
		} unset($data);
		return $passed;
	}
	
	/**
	 * Check require of input
	 * @param $text
	 * @author Manassarn M.
	 */
	function required($text = NULL){
		return $text === TRUE || strlen(''.$text);
	}
	
	/** Check if input is email
	 * @param $text
	 * @author Manassarn M.
	 */
	function is_email($text = NULL){
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $text)) ? FALSE : TRUE;
	}
	
}