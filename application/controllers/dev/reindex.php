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
			if (!(ENVIRONMENT == 'development' || ENVIRONMENT == 'testing' ))
			{
				redirect();
			}
		}
	}
	
	function index(){
		$models = array(
			'achievement_info',
			'achievement_stat',
			'achievement_stat_page',
			'achievement_user',
			'app_component',
			'app_component_page',
			'audit_action',
			'audit',
			'get_started',
			'homepage',
			'invite',
			'invite_pending',
			'notification',
			'reward_item',
			'stat_app',
			'stat_campaign',
			'stat_page'
		);
		foreach($models as $model){
			$this->load->model($model."_model", $model);
			if($this->{$model}->create_index()){
				echo 'Created index for '.$model.' model.<br />';
			} else {
				echo 'Error creating index for '.$model.' model, maybe the colletion is not created yet.<br />';
			}
		}
		echo 'Created all indexes';
	}

	// function unix_mongo_dump(){ //No file permission so this cannot be used
	// 	var_dump(nl2br(shell_exec(file_get_contents(pathinfo(__FILE__, PATHINFO_DIRNAME).'/../../../shdumper/mongodump.sh'))));
	// }
}
/* End of file reindex.php */
/* Location: ./application/controllers/dev/reindex.php */