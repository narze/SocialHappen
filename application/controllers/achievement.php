<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Achievement extends CI_Controller {
	
	var $achievement_stat;
	
	/**
	 * construct method
	 */
	function __construct(){
		parent::__construct();
		$this->load->model('achievement_stat_model','achievement_stat');
	}
	
	function index(){
		echo $this->achievement_stat->create_index();
	}
	
	function listStat(){
		print_r($this->achievement_stat->listStat());
	}
	
	function drop(){
		echo $this->achievement_stat->drop_collection();
	}
	
	function test(){
		print_r(strpos('action.6.count', 'action.'));
	}

}

/* End of file audit.php */
/* Location: ./application/controllers/achievement.php */