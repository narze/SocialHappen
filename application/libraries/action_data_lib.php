<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Action Data Class
 * @author Manassarn M.
 */
class Action_data_lib {

	private $QR_ACTION_ID = 201;
	private $GUESTBOOK_ACTION_ID = 202;

	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->model('action_data_model', 'action_data');
	}


}