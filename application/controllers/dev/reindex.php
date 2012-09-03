<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Reindex
 * @category Controller
 */
class Reindex extends CI_Controller {

	function __construct(){
		parent::__construct();
		if (defined('ENVIRONMENT'))
		{
			if (!(ENVIRONMENT == 'development' || ENVIRONMENT == 'testing'))
			{
				if($this->input->get('happy') !== 'everyday'){
					redirect();
				}
			}
		}
	}

	function index(){
		if($this->socialhappen->reindex()) {
			echo 'Created all indexes';
		} else {
			echo 'Error creating indexes';
		}
	}
}
/* End of file reindex.php */
/* Location: ./application/controllers/dev/reindex.php */