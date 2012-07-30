<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Sync
 * @category Controller
 */
class Sync extends CI_Controller {

	function __construct(){
		if (defined('ENVIRONMENT'))
		{
			if (!(ENVIRONMENT == 'development'))
			{
				redirect();
			}
		}
		parent::__construct();
		$this->output->enable_profiler(FALSE);

		$this->load->library('db_sync');
		if($this->input->get('unit_test')){
			$this->db_sync->use_test_db(TRUE);
		}

	}

	function index(){
		echo '<a target="_blank" href="'.BASE_URL.'dev/sync/db_reset">[Production] Mysql reset</a><br />';
		echo '<a target="_blank" href="'.BASE_URL.'dev/sync/mongodb_reset">[Production] MongoDB reset</a><br />';
		echo '<a target="_blank" href="'.BASE_URL.'dev/sync/db_reset?unit_test=1">[Unit test] Mysql reset</a><br />';
		echo '<a target="_blank" href="'.BASE_URL.'dev/sync/mongodb_reset?unit_test=1">[Unit test] MongoDB reset</a><br />';
		echo '<a target="_blank" href="'.BASE_URL.'dev/sync/remove_users">Remove users</a><br />';
		echo '<a target="_blank" href="'.BASE_URL.'dev/sync/generate_field_code">Generate field PHP code</a><br />';
	}

	function remove_users(){
		$this->db_sync->remove_users();
	}

	function db_reset(){
		$this->load->dbutil();
		$backup = $this->dbutil->backup(array(
			'format' => 'txt'));
		$this->load->helper('file');
		if(write_file(APPPATH.'backup/backup_'.date('Ymd_H-i-s').'_sync.sql', $backup)){
			$this->db_sync->mysql_reset();
		} else {
			echo 'Please make backup folder writable';
		}

	}

	function mongodb_reset($skip = FALSE){
		$timestamp = date('Ymd_His');
		$output = array();
		$ret = null;
		echo $this->config->item('mongo_user');
		exec("cd ". FCPATH. "shdumper && sh mongodump.sh sohap figyfigy 27017 {$timestamp}", $output, $ret);
		$backup_success = file_exists(FCPATH . "application/backup/sh_mongo-{$timestamp}");

		if($backup_success) {
			echo 'Backup completed<br />';
			return $this->db_sync->mongodb_reset();
		}

		echo 'Backup failed, no reset';
	}

	function generate_field_code(){
		$tables = $this->db->list_tables();
		foreach ($tables as $table){
		   $fields = $this->db->field_data($table);
			echo "'".str_replace($this->db->dbprefix,'',$table)."' => array(<br />";
			foreach ($fields as $field){
 		 		 echo "&nbsp;&nbsp;&nbsp;&nbsp;'".$field->name."' => field_option('".str_replace('STRING','VARCHAR',strtoupper($field->type))."', \$constraint, \$default, \$null, \$autoinc, \$unsigned),<br />";
			}
			echo "),<br />";
		}
	}

}
/* End of file sync.php */
/* Location: ./application/controllers/dev/sync.php */