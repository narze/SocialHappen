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
			"challenge_completed" => TRUE,
		  "challenge_redeeming" => TRUE,
		  "daily_challenge" => TRUE,
		  "daily_challenge_completed" => TRUE,
		  "reward_items" => TRUE
		);

		if(!$user_ids = explode(",", $this->input->post('user_ids'))) {
			echo json_encode(array('success' => FALSE));
			return;
		}

		foreach($user_ids as $user_id) {
			$user_id = (int) $user_id;
			if(!$this->user_mongo_model->update(array('user_id' => $user_id), array('$unset' => $unset))) {
				echo json_encode(array('success' => FALSE));
				return;
			}
		}

		echo json_encode(array('success' => TRUE));
	}
}
/* End of file resetuser.php */
/* Location: ./application/controllers/dev/resetuser.php */