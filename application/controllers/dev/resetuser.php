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
		$this->load->model('audit_model');
		$this->load->model('action_user_data_model');
		$challenge_action_ids = array(201,202,203,204,206);

		$unset = array(
			"challenge_completed" => TRUE,
		  "challenge_redeeming" => TRUE,
		  "daily_challenge" => TRUE,
		  "daily_challenge_completed" => TRUE,
		  "reward_items" => TRUE,
		  "challenge_progress" => TRUE
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

			// remove actions & action_datas
			$criteria = array('user_id' => $user_id, 'action_id' => array('$in' => $challenge_action_ids));
			if(!$this->audit_model->delete($criteria)) {
				echo json_encode(array('success' => FALSE));
				return;
			}
			if(!$this->action_user_data_model->delete($criteria)) {
				echo json_encode(array('success' => FALSE));
				return;
			}
		}

		echo json_encode(array('success' => TRUE));
	}
}
/* End of file resetuser.php */
/* Location: ./application/controllers/dev/resetuser.php */