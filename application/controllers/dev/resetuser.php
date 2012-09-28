<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Resetuser
 * @category Controller
 */
class Resetuser extends CI_Controller {

	function __construct(){
		parent::__construct();
		if (defined('ENVIRONMENT'))
		{
			if ((ENVIRONMENT !== 'testing') && (ENVIRONMENT !== 'development'))
			{
				redirect();
			}
		}
	}

	function index(){
		$this->load->model('user_mongo_model');

		$unset = array(
			"challenge_completed",
		  "challenge_redeeming",
		  "daily_challenge",
		  "daily_challenge_completed",
		  "reward_items"
		);

		$set = array(
			"tokens" => array()
		);

		$user_ids = array(1,3,5);

		foreach($user_ids as $user_id) {
			$this->user_mongo_model->update(array('user_id' => $user_id), array('$unset' => $unset));
			$this->user_mongo_model->update(array('user_id' => $user_id), array('$set' => $set));
		}

		echo 'reset user';
	}
}
/* End of file resetuser.php */
/* Location: ./application/controllers/dev/resetuser.php */