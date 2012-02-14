<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Migrate_lib
 */
class Migrate_lib {
	private $CI;
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->library('migration');
	}
	
	function index(){
		$this->CI->config->load('migration');
		if($this->CI->db->table_exists('migrations')){
			$row = $this->CI->db->get('migrations')->row();
			$current_version = $row ? $row->version : 0;
		} else {
			$current_version = 0;
		}
		echo 'Current version : '.$current_version;
		
	}

	function latest($backup = TRUE){
		$this->CI->config->load('migration');
		if($migrations = $this->_find_migrations()){
			$last_migration = basename(end($migrations));
			$target_version = (int) substr($last_migration, 0, 3);
			return $this->version($target_version, $backup);
		} else {
			show_error($this->CI->lang->line('migration_none_found'));
		}
	}

	function current($backup = TRUE){
		$this->CI->config->load('migration');
		$target_version = $this->CI->config->item('migration_version');
		return $this->version($target_version, $backup);
	}

	function version($target_version, $backup = TRUE){
		if($backup){
			if($this->_backup_current_version($target_version)){
				echo 'Backed up version '.$target_version.'<br />';
			} else {
				log_message('debug', 'Cannot backup, migration cancelled');
				echo 'Migration failed<br />';
				return FALSE;
			}
		}

		if (!$result = $this->CI->migration->version($target_version)) {
			show_error($this->CI->migration->error_string());
		} else { 
			if($result === TRUE){
				echo 'No version to upgrade<br />';
			} else {
				echo 'Migrated successful to version '.$result.'<br />';
			}
			return $result;
		}
	}

	function _backup_current_version($target_version){
		if($this->CI->db->table_exists('migrations')){
			$row = $this->CI->db->get('migrations')->row();
			$current_version = $row ? $row->version : 0;
		} else {
			$current_version = 0;
		}
		if($current_version == $target_version){
			echo 'Same version<br />';
			return FALSE;
		}
		// Backup your entire database and assign it to a variable
		$this->CI->load->dbutil();
		$backup = $this->CI->dbutil->backup(array(
			'format' => 'txt')); 

		// Load the file helper and write the file to your server
		$this->CI->load->helper('file');
		return write_file(APPPATH.'migrations/backup_'.date('Ymd_H-i-s').
			'_v['.$current_version.'-'.$target_version.'].sql', $backup); 
	}

	/**
	 * Find migration files (copied from CI_Migration class)
	 */
	function _find_migrations()
	{
		// Load all *_*.php files in the migrations path
		$files = glob(rtrim($this->CI->config->item('migration_path'), '/').'/' . '*_*.php');
		$file_count = count($files);
		
		for ($i = 0; $i < $file_count; $i++)
		{
			// Mark wrongly formatted files as false for later filtering
			$name = basename($files[$i], '.php');
			if ( ! preg_match('/^\d{3}_(\w+)$/', $name))
			{
				$files[$i] = FALSE;
			}
		}
		
		sort($files);

		return $files;
	}
}