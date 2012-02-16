<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Sync
 * @category Controller
 */
class Sync extends CI_Controller {
	
	function __construct(){
		// if (defined('ENVIRONMENT'))
		// {
		// 	if (!(ENVIRONMENT == 'development' || ENVIRONMENT == 'testing' ))
		// 	{
		// 		exit('For development & testing only.');
		// 	}
		// }
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
		echo '<a target="_blank" href="'.BASE_URL.'dev/sync/db_reset?unit_test=1">[Unit test] Mysql reset<br />';
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
	
	function mongodb_reset(){
		//Check if mongodb is backed up manually in last x=$interval_limit seconds

		//1.Get latest backup file name
		$prefix = 'sh_mongo\-';
		$interval_limit = 120;
		$backup_dir = glob(APPPATH.'backup/sh_mongo-*_*',GLOB_ONLYDIR);
		$file_count = count($backup_dir);
		// var_dump_pre($backup_dir);
		for ($i = 0; $i < $file_count; $i++)
		{
		// Mark wrongly formatted files as false for later filtering
		$name = basename($backup_dir[$i], '.php');
			if ( ! preg_match('/^'.$prefix.'\d{8}_\d{6}$/', $name))
			{
				$backup_dir[$i] = FALSE;
			}
		}
		
		sort($backup_dir);
		$latest_backup = basename(end($backup_dir));

		//2.try convert into unit timestamp
		$latest_backup = preg_replace('/^('.$prefix.')(\w+)_(\w+)/','${2}${3}',$latest_backup);
		$y = (int) substr($latest_backup,0,4); 
		$m = (int) substr($latest_backup,4,2); 
		$d = (int) substr($latest_backup,6,2); 
		$h = (int) substr($latest_backup,8,2); 
		$i = (int) substr($latest_backup,10,2); 
		$s = (int) substr($latest_backup,12,2);
		$latest_backup_timestamp = mktime($h, $i, $s, $m, $d, $y); 

		//3.compare with time()
		$interval = time()-$latest_backup_timestamp;
		if($interval <= $interval_limit){
			$this->db_sync->mongodb_reset();
		} else {
			echo 'Please try backup using mongodump before reset';
		}
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