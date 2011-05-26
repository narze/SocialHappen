<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Sync
 * @category Controller
 */
class Sync extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		$this->output->enable_profiler(TRUE);
	}

	function index(){

	}
	
	function db_reset(){
		$this->drop_tables();
		$this->create_tables();
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
	
	function drop_tables(){
		$tables = $this->db->list_tables();
		foreach ($tables as $table){
			$table = str_replace($this->db->dbprefix,'',$table);
		    $this->dbforge->drop_table($table);
		}
	}
	
	function create_database(){
		$this->dbforge->create_database('socialhappen');
	}
	
	function create_tables(){
		function field_option($type, $constraint, $default, $null, $auto_increment, $unsigned){
			$options = array();
			if(isset($type)){
				$options['type'] = $type;
			}
			if(isset($constraint)){
				$options['constraint'] = $constraint;
			}
			if(isset($default)){
				$options['default'] = $default;
			}
			if(isset($null)){
				$options['null'] = $null;
			}
			if(isset($auto_increment)){
				$options['auto_increment'] = $auto_increment;
			}
			if(isset($unsigned)){
				$options['unsigned'] = $unsigned;
			}
			return $options;
		}
		
		$constraint = NULL;
		$default = NULL;
		$null = FALSE;
		$autoinc = NULL;
		$unsigned = NULL;
		
		$fields = array(
							'app' => array(
							    'app_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'app_name' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
							    'app_type_id' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							    'app_maintainance' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_show_in_list' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_description' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_secret_key' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_install_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_config_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_support_page_tab' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'app_install_status' => array(
							    'app_install_status_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
							    'app_install_status_name' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
							    'app_install_status_description' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							/*'app_statistic' => array(
							    'app_install_id' => field_option('INT', 20, $default, $null, $autoinc, $unsigned),
							    'job_time' => field_option('TIMESTAMP', $constraint, 'CURRENT_TIMESTAMP', $null, $autoinc, $unsigned),
							    'job_id' => field_option('INT', 20, $default, $null, $autoinc, $unsigned),
							    'active_user' => field_option('INT', 20, $default, $null, $autoinc, $unsigned),
							),*/
							'app_type' => array(
							    'app_type_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
							    'app_type_name' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
							    'app_type_description' => field_option('VARCHAR', 255, $default, TRUE, $autoinc, $unsigned),
							),
							'campaign' => array(
							    'campaign_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'app_install_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'campaign_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'campaign_detail' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'campaign_status_id' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							    'campaign_active_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'campaign_all_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'campaign_start_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'campaign_end_timestamp' => field_option('TIMESTAMP', $constraint, $default , $null, $autoinc, $unsigned),
							),
							'campaign_status' => array(
							    'campaign_status_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
							    'campaign_status_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'company' => array(
							    'company_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'creator_user_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'company_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_address' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'company_email' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_telephone' => field_option('VARCHAR', 20, $default, $null, $autoinc, $unsigned),
							    'company_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'company_username' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_password' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'company_apps' => array(
							    'company_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'app_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'available_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							),
							'company_pages' => array(
							    'company_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'page_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							),
							/*'config_item' => array(
							    'app_install_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'config_key' => field_option('VARCHAR', 64, $default, $null, $autoinc, $unsigned),
							    'config_value' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'config_item_template' => array(
							    'app_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'config_key' => field_option('64', $constraint, $default, $null, $autoinc, $unsigned),
							    'config_value' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'cron_job' => array(
							    'job_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'job_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'job_start' => field_option('TIMESTAMP', $constraint, 'CURRENT_TIMESTAMP', $null, $autoinc, $unsigned),
							    'job_finish' => field_option('TIMESTAMP', $constraint, $default, $null, $autoinc, $unsigned),
							    'job_status' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),*/
							'installed_apps' => array(
							    'app_install_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'company_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'app_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'app_install_status' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_install_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'page_id' => field_option('INT', 20, $default, TRUE, $autoinc, TRUE),
							    'app_install_secret_key' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'page' => array(
							    'page_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'facebook_page_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'company_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'page_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'page_detail' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'page_all_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'page_new_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'page_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'user' => array(
							    'user_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'user_facebook_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'user_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'user_last_seen' => field_option('TIMESTAMP', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'user_apps' => array(
							    'user_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'app_install_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'user_apps_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'user_apps_last_seen' => field_option('TIMESTAMP', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'user_campaigns' => array(
							    'user_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'campaign_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							),
							'user_companies' => array(
							    'user_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'company_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'user_role' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							)
						);
		$keys = array(
						'app' => array('app_id'),
						'app_install_status' => array('app_install_status_id'),
						//'app_statistic' => array('app_install_id','job_time'),
						'app_type' => array('app_type_id'),
						'campaign' => array('campaign_id'),
						'campaign_status' => array('campaign_status_id'),
						'company' => array('company_id'),
						'company_apps' => array('company_id', 'app_id'),
						'company_pages' => array('company_id', 'page_id'),
						//'config_item' => array('app_install_id','config_key'),
						//'config_item_template' => array('app_id','config_key'),
						//'VARCHAR_job' => array('job_id'),
						'installed_apps' => array('app_install_id'),
						'page' => array('page_id'),
						'user' => array('user_id'),
						'user_apps' => array('user_id', 'app_install_id'),
						'user_campaigns' => array('user_id', 'campaign_id'),
						'user_companies' => array('user_id', 'company_id')
					);
		$tables = array(
							'app',
							'app_install_status',
							//'app_statistic',
							'app_type',
							'campaign',
							'campaign_status',
							'company',
							'company_apps',
							'company_pages',
							//'config_item',
							//'config_item_template',
							//'cron_job',
							'installed_apps',
							'page',
							'user',
							'user_apps',
							'user_campaigns',
							'user_companies'
						);
		$tables = array_map(array($this->db,'dbprefix'), $tables);
		
		foreach ($tables as $table){
			$table = str_replace($this->db->dbprefix,'',$table);
			$this->dbforge->add_field($fields[$table]);
			foreach ($keys[$table] as $primary_key){
				$this->dbforge->add_key($primary_key, TRUE);
			}
			$this->dbforge->create_table($table, TRUE);
		}
		$this->special_cases_after_create();
	}

	function special_cases_after_create(){
		$this->db->query("CREATE UNIQUE INDEX user_facebook_id ON ".$this->db->dbprefix('user')." (user_facebook_id)");
	}
	
	function input_test_data(){
		
	}
	
	function test(){
		echo '<pre>';
		var_dump($this->dbforge);
		echo '</pre>';
	}
}
/* End of file sync.php */
/* Location: ./application/controllers/dev/sync.php */