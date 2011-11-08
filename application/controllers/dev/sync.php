<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define("SESSION_TABLE_NAME","sessions");

/**
 * @class Sync
 * @category Controller
 */
class Sync extends CI_Controller {
	
	function __construct(){
		if (defined('ENVIRONMENT'))
		{
			if (!(ENVIRONMENT == 'development' || ENVIRONMENT == 'testing' ))
			{
				exit('For development & testing only.');
			}
		}
		parent::__construct();
		$this->preload();
		$this->load->dbforge();
		$this->output->enable_profiler(TRUE);
		define('BASE_URL', base_url());
	}
	
	function index(){
		echo '<a href="'.base_url().'dev/sync/mongodb_reset">MongoDB reset</a><br />';
		echo '<a href="'.base_url().'dev/sync/remove_users">Remove users</a><br />';
		echo '<a href="'.base_url().'dev/sync/db_reset">RESET (drop -> create -> insert data)</a><br />';
		echo '<a href="'.base_url().'dev/sync/generate_field_code">Generate field PHP code</a><br />';
		echo '<a href="'.base_url().'dev/sync/create_database">Create datebase "socialhappen"</a>';
	}
	
	/**
	 * Create database and session table in pure PHP
	 * @author Manassarn M.
	 */
	function preload(){
		$host = $this->db->hostname;
		$username = $this->db->username;
		$password = $this->db->password;
		$database = $this->db->database;
		$prefix = $this->db->dbprefix;
		$session_table = SESSION_TABLE_NAME;
		if(isset($_GET['u']) && isset($_GET['p'])){
			$username = $_GET['u'];
			$password = $_GET['p'];
		}
		$con = mysql_connect($host,$username,$password);
		if (!$con)
		    {
				die('Could not connect to mysql: ' . mysql_error());
 		    }
		if (!mysql_query("CREATE DATABASE IF NOT EXISTS {$database}",$con)){
			echo "Error creating database '{$database}': " . mysql_error()."<h3>Try dev/sync?u=username&p=password</h3>";
		}
		if($this->config->item('sess_use_database')) {
			if (!mysql_query("CREATE TABLE IF NOT EXISTS {$database}.{$prefix}{$session_table} (
							session_id varchar(40) DEFAULT '0' NOT NULL,
							ip_address varchar(16) DEFAULT '0' NOT NULL,
							user_agent varchar(50) NOT NULL,
							last_activity int(10) unsigned DEFAULT 0 NOT NULL,
							session_data text default '' not null,
							PRIMARY KEY (session_id)
							); 
						",$con)){
				echo "Error creating session table: " . mysql_error();
			}
		}
	}
	
	function remove_users(){
		$tables = array('package_users', 'page_user_data', 'sessions', 'user', 'user_apps', 'user_campaigns', 'user_companies', 'user_pages', 'page');
		foreach($tables as $table){
			if($this->db->empty_table($table)) {
				echo "Emptied table : {$table}<br />";
			}
		}
		echo "Remove users successfully";
	}
	
	function db_reset(){
		$this->drop_tables();
		$this->create_tables();
		$this->special_cases_after_create();
		$this->insert_test_data();
		echo "Database reset successfully";
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
			if(strpos($table, $this->db->dbprefix) === 0){
				$table = str_replace($this->db->dbprefix,'',$table);
				if($this->dbforge->drop_table($table)){
					echo "Dropped table : {$table}<br />";	
				}
			}
		}
	}
	
	function create_database(){
		$this->dbforge->create_database($this->db->database);
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
							    'app_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'app_name' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
							    'app_type_id' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							    'app_maintainance' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_show_in_list' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_description' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
							    'app_secret_key' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_install_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_install_page_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_config_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_support_page_tab' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_icon' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'app_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'app_facebook_api_key' => field_option('VARCHAR', 32, $default, $null, $autoinc, $unsigned)
							),
							/*'app_statistic' => array(
							    'app_install_id' => field_option('BIGINT', 20, $default, $null, $autoinc, $unsigned),
							    'job_time' => field_option('TIMESTAMP', $constraint, 'CURRENT_TIMESTAMP', $null, $autoinc, $unsigned),
							    'job_id' => field_option('BIGINT', 20, $default, $null, $autoinc, $unsigned),
							    'active_user' => field_option('BIGINT', 20, $default, $null, $autoinc, $unsigned),
							),*/
							'audit_action_type' => array(
							    'audit_action_id' => field_option('INT', 5, $default, $null, $autoinc, TRUE),
							    'audit_action_name' => field_option('VARCHAR', 100, $default, $null, $autoinc, $unsigned),
							    'audit_action_active' => field_option('INT', 1, 1, $null, $autoinc, $unsigned),
								'audit_action_update TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							),
							'campaign' => array(
							    'campaign_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'app_install_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'campaign_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'campaign_detail' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'campaign_status_id' => field_option('INT', 2, $default, $null, $autoinc, TRUE),
							    'campaign_active_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'campaign_all_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'campaign_start_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'campaign_end_timestamp' => field_option('TIMESTAMP', $constraint, $default , $null, $autoinc, $unsigned),
								'campaign_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'company' => array(
							    'company_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'creator_user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'company_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_detail' => field_option('VARCHAR', 255, $default, TRUE, $autoinc, $unsigned),
							    'company_address' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'company_email' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_telephone' => field_option('VARCHAR', 20, $default, $null, $autoinc, $unsigned),
							    'company_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'company_username' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_password' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'company_website' => field_option('VARCHAR', 255, $default, TRUE, $autoinc, $unsigned)
							),
							'company_apps' => array(
							    'company_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'app_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'available_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							),
							'installed_apps' => array(
							    'app_install_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'company_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'app_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'app_install_status_id' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							    'app_install_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'page_id' => field_option('BIGINT', 20, 0, TRUE, $autoinc, TRUE),
							    'app_install_secret_key' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'order_in_dashboard' => field_option('INT', 5, 0, $null, $autoinc, TRUE),
							    'facebook_tab_url' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'page' => array(
							    'page_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'facebook_page_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'company_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'page_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'page_detail' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
							    'page_all_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'page_new_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'page_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'order_in_dashboard' => field_option('INT', 5, 0, $null, $autoinc, TRUE),
								'page_status_id' => field_option('INT', 1, 1, $null, $autoinc, TRUE),
								'page_app_installed_id' => field_option('BIGINT', 20, 0, $null, $autoinc, TRUE),
								'page_installed' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'page_user_fields' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
							    'facebook_tab_url' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'user' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'user_first_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'user_last_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'user_email' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'user_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'user_facebook_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'user_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'user_last_seen' => field_option('TIMESTAMP', $constraint, $default, $null, $autoinc, $unsigned),
								'user_gender_id' => field_option('INT', 1, 1, TRUE, $autoinc, TRUE),
								'user_birth_date' => field_option('DATE', $constraint, $default, TRUE, $autoinc, $unsigned),
								'user_about' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
								'user_point' => field_option('BIGINT', 20, 0, $null, $autoinc, TRUE)
							),
							'user_apps' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'app_install_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'user_apps_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'user_apps_last_seen' => field_option('TIMESTAMP', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'user_campaigns' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'campaign_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							),
							'user_companies' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'company_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'user_role' => field_option('INT', 2, 0, $null, $autoinc, TRUE),
							),
							'sessions' =>array(
								'session_id' => field_option('VARCHAR', 40, '0', $null, $autoinc, $unsigned),
								'ip_address' => field_option('VARCHAR', 16, '0', $null, $autoinc, $unsigned),
								'user_agent' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
								'last_activity' => field_option('INT', 10, 0, $null, $autoinc, TRUE),
								'user_data' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
								'user_id' => field_option('BIGINT', 20, $default, TRUE, $autoinc, TRUE),
							),
							'user_role' => array(
							    'user_role_id' => field_option('INT', 2, $default, $null, TRUE, TRUE),
							    'user_role_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'role_all' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_company_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_company_add' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_company_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_company_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_all_company_pages_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_pages_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_pages_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_all_company_apps_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_apps_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_apps_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_all_company_campaigns_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_campaigns_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_campaigns_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_page_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_page_add' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_page_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_page_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_app_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_app_add' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_app_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_app_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_campaign_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_campaign_add' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_campaign_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_campaign_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
							),
							'user_pages' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'page_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'user_role' => field_option('INT', 2, 0, $null, $autoinc, TRUE),
							),
							'package' => array(
								'package_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
								'package_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'package_detail' => field_option('VARCHAR', 255, $default, TRUE, $autoinc, $unsigned),
								'package_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'package_max_companies' => field_option('INT', 10, 0, $null, $autoinc, TRUE),
								'package_max_pages' => field_option('INT', 10, 0, $null, $autoinc, TRUE),
								'package_max_users' => field_option('INT', 10, 0, $null, $autoinc, TRUE),
								'package_price' => field_option('DOUBLE', $constraint, 0, $null, $autoinc, TRUE),
								'package_custom_badge' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'package_duration' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned)
								
							),
							'package_users' => array(
								'package_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'package_expire TIMESTAMP NOT NULL DEFAULT "0000-00-00 00:00:00"',
							),
							'package_apps' => array(
								'package_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'app_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							),
							'order' => array(
								'order_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
								'order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
								'order_status_id' => field_option('INT', 10, $default, $null, $autoinc, TRUE),
								'order_net_price' => field_option('DOUBLE', $constraint, 0, $null, $autoinc, TRUE),
								'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'payment_method' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'billing_info' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned)
							),
							'order_items' => array(
								'order_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'item_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'item_type_id' => field_option('INT', 10, $default, $null, $autoinc, TRUE),
								'item_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'item_description' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
								'item_price' => field_option('DOUBLE', $constraint, 0, $null, $autoinc, TRUE),
								'item_unit' => field_option('INT', 10, 1, $null, $autoinc, TRUE),
								'item_discount' => field_option('BIGINT', 20, 0, TRUE, $autoinc, TRUE)
							),
							'page_user_data' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'page_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'user_data' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned)
							)
						);
		$keys = array(
						'app' => array('app_id'),
						//'app_statistic' => array('app_install_id','job_time'),
						'audit_action_type' => array('audit_action_id'),
						'campaign' => array('campaign_id'),
						'company' => array('company_id'),
						'company_apps' => array('company_id', 'app_id'),
						//'config_item' => array('app_install_id','config_key'),
						//'config_item_template' => array('app_id','config_key'),
						//'VARCHAR_job' => array('job_id'),
						'installed_apps' => array('app_install_id'),
						'page' => array('page_id'),
						'user' => array('user_id'),
						'user_apps' => array('user_id', 'app_install_id'),
						'user_campaigns' => array('user_id', 'campaign_id'),
						'user_companies' => array('user_id', 'company_id'),
						'sessions' => array('session_id'),
						'user_role' => array('user_role_id'),
						'user_pages' => array('user_id', 'page_id'),
						'package' => array('package_id'),
						'package_users' => array('user_id'),
						'package_apps' => array('package_id','app_id'),
						'order' => array('order_id'),
						'order_items' => array('order_id','item_id', 'item_type_id'),
						'page_user_data' => array('user_id','page_id')
					);
		$tables = array(
							'app',
							//'app_statistic',
							'audit_action_type',
							'campaign',
							'company',
							'company_apps',
							//'config_item',
							//'config_item_template',
							//'cron_job',
							'installed_apps',
							'page',
							'user',
							'user_apps',
							'user_campaigns',
							'user_companies',
							'sessions',
							'user_role',
							'user_pages',
							'package',
							'package_users',
							'package_apps',
							'order',
							'order_items',
							'page_user_data'
						);
		$tables = array_map(array($this->db,'dbprefix'), $tables);
		
		foreach ($tables as $table){
			$table = str_replace($this->db->dbprefix,'',$table);
			$this->dbforge->add_field($fields[$table]);
			foreach ($keys[$table] as $primary_key){
				$this->dbforge->add_key($primary_key, TRUE);
			}
			if($this->dbforge->create_table($table, TRUE)){
				echo "Created table : {$table}<br />";	
			}
		}
	}

	function special_cases_after_create(){
		$this->db->query("CREATE UNIQUE INDEX user_facebook_id ON ".$this->db->dbprefix('user')." (user_facebook_id)");
	}
	
	function insert_test_data(){
		$app = array(
			array(
				'app_id' => 1, 
				'app_name' => 'Friend Get Fans', 
				'app_type_id' => $this->socialhappen->get_k('app_type','Page Only'), 
				'app_maintainance' => 0, 
				'app_show_in_list' => 1, 
				'app_description' => 'Friend Get Fans', 
				'app_secret_key' => 'ad3d4f609ce1c21261f45d0a09effba4', 
				'app_url' => 'https://apps.socialhappen.com/fgf/profile.php?app_install_id={app_install_id}', 
				'app_install_url' => 'https://apps.socialhappen.com/fgf/platform.php?action=install&company_id={company_id}&user_id={user_id}&page_id={page_id}&force=1', 
				'app_install_page_url' => 'https://apps.socialhappen.com/fgf/platform.php?action=install_to_page&app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
				'app_config_url' => 'https://apps.socialhappen.com/fgf/app_config.php?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
				'app_support_page_tab' => 1, 
				'app_icon' =>  'https://apps.socialhappen.com/fgf/images/app_image_16.png',
				'app_image' =>  'https://apps.socialhappen.com/fgf/images/app_image_o.png',
				'app_facebook_api_key' => '202663143123531' 	
			),
			array(
				'app_id' => 2, 
				'app_name' => '[Local]Friend Get Fans', 
				'app_type_id' => $this->socialhappen->get_k('app_type','Page Only'), 
				'app_maintainance' => 0, 
				'app_show_in_list' => 1, 
				'app_description' => 'Friend Get Fans', 
				'app_secret_key' => 'ad3d4f609ce1c21261f45d0a09effba4', 
				'app_url' => 'https://apps.localhost.com/fgf/profile.php?app_install_id={app_install_id}', 
				'app_install_url' => 'https://apps.localhost.com/fgf/platform.php?action=install&company_id={company_id}&user_id={user_id}&page_id={page_id}&force=1', 
				'app_install_page_url' => 'https://apps.localhost.com/fgf/platform.php?action=install_to_page&app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
				'app_config_url' => 'https://apps.localhost.com/fgf/app_config.php?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
				'app_support_page_tab' => 1, 
				'app_icon' =>  'https://apps.localhost.com/fgf/images/app_image_16.png',
				'app_image' =>  'https://apps.localhost.com/fgf/images/app_image_o.png',
				'app_facebook_api_key' => '154899207922915' 	
			),
			array(
				'app_id' => 3, 
				'app_name' => 'MockApp', 
				'app_type_id' => $this->socialhappen->get_k('app_type','Standalone'), 
				'app_maintainance' => 0, 
				'app_show_in_list' => 1, 
				'app_description' => 'Mock Application', 
				'app_secret_key' => 'cd14463efa98e6ee00fde6ccd51a9f6d', 
				'app_url' => 'https://apps.socialhappen.com/mockapp?app_install_id={app_install_id}', 
				'app_install_url' => 'https://apps.socialhappen.com/mockapp/port/install_unit/?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
				'app_install_page_url' => 'https://apps.socialhappen.com/mockapp/port/install_to_page/?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
				'app_config_url' => 'https://apps.socialhappen.com/mockapp/admin/?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
				'app_support_page_tab' => 1, 
				'app_icon' =>  'https://apps.socialhappen.com/mockapp/images/app_image_16.png',
				'app_image' =>  'https://apps.socialhappen.com/mockapp/images/app_image_o.png',
				'app_facebook_api_key' => '177890852283217' 	
			),
			array(
				'app_id' => 4, 
				'app_name' => '[Local]MockApp', 
				'app_type_id' => $this->socialhappen->get_k('app_type','Standalone'), 
				'app_maintainance' => 0, 
				'app_show_in_list' => 1, 
				'app_description' => 'Mock Application', 
				'app_secret_key' => 'cd14463efa98e6ee00fde6ccd51a9f6d', 
				'app_url' => 'https://apps.localhost.com/mockapp?app_install_id={app_install_id}', 
				'app_install_url' => 'https://apps.localhost.com/mockapp/port/install_unit/?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
				'app_install_page_url' => 'https://apps.localhost.com/mockapp/port/install_to_page/?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
				'app_config_url' => 'https://apps.localhost.com/mockapp/admin/?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
				'app_support_page_tab' => 1, 
				'app_icon' =>  'https://apps.localhost.com/mockapp/images/app_image_16.png',
				'app_image' =>  'https://apps.localhost.com/mockapp/images/app_image_o.png',
				'app_facebook_api_key' => '204755022911798' 	
			),			
			array(
				'app_id' => 5, 
				'app_name' => 'FeedVideo', 
				'app_type_id' => $this->socialhappen->get_k('app_type','Support Page'), 
				'app_maintainance' => 0, 
				'app_show_in_list' => 1, 
				'app_description' => 'Video Feed', 
				'app_secret_key' => '985a868ee8aec810d3a25a3367776ea7', 
				'app_url' => 'https://apps.socialhappen.com/feedv?app_install_id={app_install_id}', 
				'app_install_url' => 'https://apps.socialhappen.com/feedv/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
				'app_install_page_url' => 'https://apps.socialhappen.com/feedv/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
				'app_config_url' => 'https://apps.socialhappen.com/feedv/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
				'app_support_page_tab' => 1, 
				'app_icon' =>  'https://apps.socialhappen.com/feedv/images/app_image_16.png',
				'app_image' =>  'https://apps.socialhappen.com/feedv/images/app_image_o.png',
				'app_facebook_api_key' => '203741749684542' 	
			),
			array(
				'app_id' => 6, 
				'app_name' => '[Local]FeedVideo', 
				'app_type_id' => $this->socialhappen->get_k('app_type','Support Page'), 
				'app_maintainance' => 0, 
				'app_show_in_list' => 1, 
				'app_description' => 'Video Feed', 
				'app_secret_key' => '430203a30d65ef835f1521d70fd4e9b5', 
				'app_url' => 'https://apps.localhost.com/feedv?app_install_id={app_install_id}', 
				'app_install_url' => 'https://apps.localhost.com/feedv/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
				'app_install_page_url' => 'https://apps.localhost.com/feedv/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
				'app_config_url' => 'https://apps.localhost.com/feedv/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
				'app_support_page_tab' => 1, 
				'app_icon' =>  'https://apps.localhost.com/feedv/images/app_image_16.png',
				'app_image' =>  'https://apps.localhost.com/feedv/images/app_image_o.png',
				'app_facebook_api_key' => '253512681338518' 	
			),			
			array(
				'app_id' => 7, 
				'app_name' => 'FeedRSS', 
				'app_type_id' => $this->socialhappen->get_k('app_type','Support Page'), 
				'app_maintainance' => 0, 
				'app_show_in_list' => 1, 
				'app_description' => 'RSS Feed', 
				'app_secret_key' => '9c867d4b57a77c46a7e8ca3830d8fb8c', 
				'app_url' => 'https://apps.socialhappen.com/feedr?app_install_id={app_install_id}', 
				'app_install_url' => 'https://apps.socialhappen.com/feedr/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
				'app_install_page_url' => 'https://apps.socialhappen.com/feedr/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
				'app_config_url' => 'https://apps.socialhappen.com/feedr/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
				'app_support_page_tab' => 1, 
				'app_icon' =>  'https://apps.socialhappen.com/feedr/images/app_image_16.png',
				'app_image' =>  'https://apps.socialhappen.com/feedr/images/app_image_o.png',
				'app_facebook_api_key' => '249927805038578' 	
			),
			array(
				'app_id' => 8, 
				'app_name' => '[Local]FeedRSS', 
				'app_type_id' => $this->socialhappen->get_k('app_type','Support Page'), 
				'app_maintainance' => 0, 
				'app_show_in_list' => 1, 
				'app_description' => 'RSS Feed', 
				'app_secret_key' => '985a868ee8aec810d3a25a3367776ea7', 
				'app_url' => 'https://apps.localhost.com/feedr?app_install_id={app_install_id}', 
				'app_install_url' => 'https://apps.localhost.com/feedr/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
				'app_install_page_url' => 'https://apps.localhost.com/feedr/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
				'app_config_url' => 'https://apps.localhost.com/feedr/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
				'app_support_page_tab' => 1, 
				'app_icon' =>  'https://apps.localhost.com/feedr/images/app_image_16.png',
				'app_image' =>  'https://apps.localhost.com/feedr/images/app_image_o.png',
				'app_facebook_api_key' => '123678481062231' 	
			),
		);
		$this->db->insert_batch('app', $app);
				
		$audit_action_type = array(
								array(
									'audit_action_id' => 1,
									'audit_action_name' =>  'install app',
								),
								array(
									'audit_action_id' => 2,
									'audit_action_name' =>  'install app to page',
								),
								array(
									'audit_action_id' => 3,
									'audit_action_name' =>  'remove app',
								),
								array(
									'audit_action_id' => 4,
									'audit_action_name' =>  'save config',
								),
								array(
									'audit_action_id' => 5,
									'audit_action_name' =>  'install page',
								),
								array(
									'audit_action_id' => 101,
									'audit_action_name' =>  'user register to platform',
								),
								array(
									'audit_action_id' => 102,
									'audit_action_name' =>  'user register to app',
								),
								array(
									'audit_action_id' => 103,
									'audit_action_name' =>  'user visit',
								),
								array(
									'audit_action_id' => 104,
									'audit_action_name' =>  'user action',
								),
								array(
									'audit_action_id' => 105,
									'audit_action_name' =>  'user join campaign',
								)
							);
		$this->db->insert_batch('audit_action_type', $audit_action_type);
		
		$campaign = array(
							array(
							    'campaign_id' => 1,
							    'app_install_id' =>  1, 
							    'campaign_name' => 'Campaign test 1',
							    'campaign_detail' => 'Campaign test detail 1',
							    'campaign_status_id' => 1, 
							    'campaign_active_member' => 2,
							    'campaign_all_member' => 10, 
							    'campaign_start_timestamp' => '2011-05-19 18:29:43',
							    'campaign_end_timestamp' => '2012-05-18 00:00:00',
								'campaign_image' => base_url().'uploads/images/e9cd374dff834f3bfbeb24d4682c6417_o.png',
							),
							array(
							    'campaign_id' => 2, 
							    'app_install_id' => 2, 
							    'campaign_name' => 'Campaign test 2',
							    'campaign_detail' => 'Campaign test detail 2', 
							    'campaign_status_id' => 2, 
							    'campaign_active_member' => 3, 
							    'campaign_all_member' => 5, 
							    'campaign_start_timestamp' => '2011-05-18 18:05:46', 
							    'campaign_end_timestamp' => '2011-06-18 00:00:00',
								'campaign_image' => base_url().'uploads/images/e9cd374dff834f3bfbeb24d4682c6417_o.png',
							)
						);
		$this->db->insert_batch('campaign', $campaign);
		
		$company = array(
						array(
							    'company_id' => 1, 
							    'creator_user_id' => 0, 
							    'company_name' => 'Company test 1', 
							    'company_detail' => 'detail test',
							    'company_address' => '', 
							    'company_email' => 'test1@figabyte.com', 
							    'company_telephone' => '022485555', 
							    'company_register_date' => '2011-05-09 17:52:17', 
							    'company_username' => '', 
							    'company_password' => '',
							    'company_image' => base_url().'uploads/images/32b299d9fb8a6e61784646ac80631153_o.png'
							)
						);
		$this->db->insert_batch('company', $company);
		
		$company_apps = array(
							array(
							    'company_id' => 1, 
							    'app_id' => 1, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 2, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 3, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 4, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 5, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 6, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 7, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 8, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 9, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 10, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 11, 
							    'available_date' => '2011-05-19 16:01:20'
							)
						);
		$this->db->insert_batch('company_apps', $company_apps);
		
		$installed_apps = array(
								array(
								    'app_install_id' => 1, 
								    'company_id' => 1, 
								    'app_id' => 1, 
								    'app_install_status_id' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => '457f81902f7b768c398543e473c47465',
									'facebook_tab_url' => 'https://www.facebook.com/pages/Shtest/116586141725712?sk=app_202663143123531'
								),
								array(
								  	'app_install_id' => 2, 
								    'company_id' => 1, 
								    'app_id' => 2, 
								    'app_install_status_id' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => 'b4504b54bb0c27a22fedba10cca4eb55',
									'facebook_tab_url' => 'https://www.facebook.com/pages/Shtest/116586141725712?sk=app_154899207922915'
								),
								array(
								    'app_install_id' => 3, 
								    'company_id' => 1, 
								    'app_id' => 3, 
								    'app_install_status_id' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => '1dd5a598414f201bc521348927c265c3',
									'facebook_tab_url' => ''
								),
								array(
								  	'app_install_id' => 4, 
								    'company_id' => 1, 
								    'app_id' => 4, 
								    'app_install_status_id' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => '19323810aedbbc8384b383fa21904626',
									'facebook_tab_url' => 'https://www.facebook.com/pages/Shtest/116586141725712?sk=app_204755022911798'
								)
							);
		$this->db->insert_batch('installed_apps', $installed_apps);
		
		$page = array(
					array(
						    'page_id' => 1, 
						    'facebook_page_id' => '116586141725712', 
						    'company_id' => 1, 
						    'page_name' => 'SH Test', 
						    'page_detail' => 'detail', 
						    'page_all_member' => 22, 
						    'page_new_member' => 222, 
						    'page_image' => base_url().'uploads/images/1e0e1797879fb03f648d6751f43a2697_o.png',
							'page_user_fields' => json_encode(array(
								1 => array(
									'name' => 'size',
									'label' => 'Shirt size',
									'type' => 'radio',
									'required' => FALSE,
									'rules' => NULL,
									'items' => array(1=>'S',2=>'M',3=>'L',4=>'XL'),
									'order' => 1,
									'options' => NULL,
									),
								2 => array(
									'name' => 'color',
									'label' => 'Shirt color',
									'type' => 'text',
									'required' => FALSE,
									'rules' => NULL,
									'items' => NULL,
									'order' => 2,
									'options' => NULL,
								)
							)),
							'facebook_tab_url' => ''
					),
					array(
						'page_id' => 2, 
						'facebook_page_id' => '135287989899131', 
						'company_id' => 1, 
						'page_name' => 'SH Beta', 
						'page_detail' => 'detail', 
						'page_all_member' => 10, 
						'page_new_member' => 100, 
						'page_image' => base_url().'uploads/images/1e0e1797879fb03f648d6751f43a2697_o.png',
						'page_user_fields' => json_encode(array(
							1 => array(
								'name' => 'size',
								'label' => 'Shirt size',
								'type' => 'radio',
								'required' => TRUE,
								'rules' => NULL,
								'items' => array(1=>'S',2=>'M',3=>'L',4=>'XL'),
								'order' => 1,
								'verify_message' => '---',
								'options' => NULL,
								),
							2 => array(
								'name' => 'color',
								'label' => 'Shirt color',
								'type' => 'text',
								'required' => FALSE,
								'rules' => NULL,
								'items' => NULL,
								'order' => 2,
								'verify_message' => '---',
								'options' => NULL,
							),
							3 => array(
								'name' => 'checkbox_name',
								'label' => 'Checkbox',
								'type' => 'checkbox',
								'required' => FALSE,
								'rules' => NULL,
								'items' => array('value1', 'value2', 'value3'),
								'order' => 3,
								'verify_message' => '---',
								'options' => NULL,
							)	
						)),
						'facebook_tab_url' => ''
					),
					array(
						'page_id' => 3, 
						'facebook_page_id' => '135287989899131', 
						'company_id' => 1, 
						'page_name' => 'SH Beta', 
						'page_detail' => 'detail', 
						'page_all_member' => 10, 
						'page_new_member' => 100, 
						'page_image' => base_url().'uploads/images/1e0e1797879fb03f648d6751f43a2697_o.png',
						'page_user_fields' => NULL,
						'facebook_tab_url' => ''
					),
				);
		$this->db->insert_batch('page', $page);
		
		$user = array(
					array(
					    'user_id' => 1, 
					    'user_first_name' => 'test',
					    'user_last_name' => 'test',
					    'user_email' => 'tes@test.com',
					    'user_image' => base_url().'uploads/images/bd6d2267939eeec1a64b1b46bbf90e77_o.png',					    
					    'user_facebook_id' => 713558190, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
						),
					array(
					    'user_id' => 2, 
					    'user_first_name' => 'test',
					    'user_last_name' => 'test',
					    'user_email' => 'tes@test.com',
					    'user_image' => base_url().'uploads/images/bd6d2267939eeec1a64b1b46bbf90e77_o.png',	
					    'user_facebook_id' => 637741627, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 3, 
					    'user_first_name' => 'test',
					    'user_last_name' => 'test',
					    'user_email' => 'tes@test.com',
					    'user_image' => base_url().'uploads/images/bd6d2267939eeec1a64b1b46bbf90e77_o.png',		
					    'user_facebook_id' => 631885465, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 4, 
					    'user_first_name' => 'test',
					    'user_last_name' => 'test',
					    'user_email' => 'tes@test.com',
					    'user_image' => base_url().'uploads/images/bd6d2267939eeec1a64b1b46bbf90e77_o.png',		
					    'user_facebook_id' => 755758746, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 5, 
					    'user_first_name' => 'test',
					    'user_last_name' => 'test',
					    'user_email' => 'tes@test.com',
					    'user_image' => base_url().'uploads/images/bd6d2267939eeec1a64b1b46bbf90e77_o.png',		
					    'user_facebook_id' => 508840994, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 6, 
					    'user_first_name' => 'Weerapat',
					    'user_last_name' => 'Poosri',
					    'user_email' => 'tong@figabyte.com',
					    'user_image' => 'https://graph.facebook.com/688700832/picture',		
					    'user_facebook_id' => 688700832, 
					    'user_register_date' => '2011-08-03 19:00:00',
					    'user_last_seen' => '2011-08-18 09:27:04'
					)
				);
		$this->db->insert_batch('user', $user);
		
		$user_apps = array(
							array(
							    'user_id' => 1, 
							    'app_install_id' => 1, 
							    'user_apps_register_date' => '2011-05-19 19:12:20', 
							    'user_apps_last_seen' => '0000-00-00 00:00:00'
							),
							array(
							    'user_id' => 2, 
							    'app_install_id' => 2, 
							    'user_apps_register_date' => '2011-05-19 19:12:20', 
							    'user_apps_last_seen' => '0000-00-00 00:00:00'
							)
						);
		$this->db->insert_batch('user_apps', $user_apps);
		
		$user_campaigns = array(
								array(
								    'user_id' => 1,
								    'campaign_id' => 1
								),
								array(
								    'user_id' => 2,
								    'campaign_id' => 2
								),
								array(
								    'user_id' => 3,
								    'campaign_id' => 1
								),
								array(
								    'user_id' => 4,
								    'campaign_id' => 2
								),
								array(
								    'user_id' => 5,
								    'campaign_id' => 2
								),
								array(
								    'user_id' => 6,
								    'campaign_id' => 1
								)
							);
		$this->db->insert_batch('user_campaigns', $user_campaigns);
								
		$user_companies = array(
								array(
								    'user_id' => 1,
								    'company_id' => 1,
								    'user_role' => 1
								),
								array(
								    'user_id' => 2,
								    'company_id' => 1,
								    'user_role' => 1
								),
								array(
								    'user_id' => 3,
								    'company_id' => 1,
								    'user_role' => 1
								),
								array(
								    'user_id' => 4,
								    'company_id' => 1,
								    'user_role' => 1
								),
								array(
								    'user_id' => 5,
								    'company_id' => 1,
								    'user_role' => 1
								),
								array(
								    'user_id' => 6,
								    'company_id' => 1,
								    'user_role' => 1
								)
							);
		$this->db->insert_batch('user_companies', $user_companies);
		
		$sessions = array(
						array(
							'session_id' => 1111,
							'ip_address' => 0,
							'user_agent' => 0,
							'last_activity' => 0,
							'user_data' => 'a:3:{s:7:"user_id";s:1:"0";s:16:"user_facebook_id";s:9:"713558190";s:9:"logged_in";b:1;}',
							'user_id' => 0
							)
						);
		$this->db->insert_batch('sessions', $sessions);
		
		$user_role = array(
								array(
								    'user_role_id' => 1,
								    'user_role_name' => 'Company Admin',
									'role_all' => 1,
									//'role_company_view' => 0,
									'role_company_add' => 0,
									'role_company_edit' => 0,
									'role_company_delete' => 0,
									//'role_all_company_pages_view' => 0,
									'role_all_company_pages_edit' => 0,
									'role_all_company_pages_delete' => 0,
									//'role_all_company_apps_view' => 0,
									'role_all_company_apps_edit' => 0,
									'role_all_company_apps_delete' => 0,
									//'role_all_company_campaigns_view' => 0,
									'role_all_company_campaigns_edit' => 0,
									'role_all_company_campaigns_delete' => 0,
									//'role_page_view' => 0,
									'role_page_add' => 0,
									'role_page_edit' => 0,
									'role_page_delete' => 0,
									//'role_app_view' => 0,
									'role_app_add' => 0,
									'role_app_edit' => 0,
									'role_app_delete' => 0,
									//'role_campaign_view' => 0,
									'role_campaign_add' => 0,
									'role_campaign_edit' => 0,
									'role_campaign_delete' => 0
								),
								array(
								    'user_role_id' => 2,
								    'user_role_name' => 'Page Admin',
									'role_all' => 0,
									//'role_company_view' => 1,
									'role_company_add' => 0,
									'role_company_edit' => 0,
									'role_company_delete' => 0,
									//'role_all_company_pages_view' => 0,
									'role_all_company_pages_edit' => 0,
									'role_all_company_pages_delete' => 0,
									//'role_all_company_apps_view' => 0,
									'role_all_company_apps_edit' => 0,
									'role_all_company_apps_delete' => 0,
									//'role_all_company_campaigns_view' => 0,
									'role_all_company_campaigns_edit' => 0,
									'role_all_company_campaigns_delete' => 0,
									//'role_page_view' => 1,
									'role_page_add' => 0,
									'role_page_edit' => 1,
									'role_page_delete' => 1,
									//'role_app_view' => 1,
									'role_app_add' => 0,
									'role_app_edit' => 0,
									'role_app_delete' => 0,
									//'role_campaign_view' => 1,
									'role_campaign_add' => 0,
									'role_campaign_edit' => 0,
									'role_campaign_delete' => 0
								),
								array(
								    'user_role_id' => 3,
								    'user_role_name' => 'Test admin',
									'role_all' => 0,
									//'role_company_view' => 1,
									'role_company_add' => 1,
									'role_company_edit' => 1,
									'role_company_delete' => 1,
									//'role_all_company_pages_view' => 0,
									'role_all_company_pages_edit' => 1,
									'role_all_company_pages_delete' => 1,
									//'role_all_company_apps_view' => 0,
									'role_all_company_apps_edit' => 1,
									'role_all_company_apps_delete' => 1,
									//'role_all_company_campaigns_view' => 0,
									'role_all_company_campaigns_edit' => 1,
									'role_all_company_campaigns_delete' => 1,
									//'role_page_view' => 1,
									'role_page_add' => 1,
									'role_page_edit' => 1,
									'role_page_delete' => 1,
									//'role_app_view' => 1,
									'role_app_add' => 1,
									'role_app_edit' => 1,
									'role_app_delete' => 1,
									//'role_campaign_view' => 1,
									'role_campaign_add' => 1,
									'role_campaign_edit' => 1,
									'role_campaign_delete' => 1
								)
							);
		$this->db->insert_batch('user_role', $user_role);
		
		$user_pages = array(
								array(
								    'user_id' => 1,
								    'page_id' => 1,
								    'user_role' => 1
								),
								array(
								    'user_id' => 2,
								    'page_id' => 1,
								    'user_role' => 1
								),
								array(
								    'user_id' => 3,
								    'page_id' => 1,
								    'user_role' => 0
								),
								array(
								    'user_id' => 4,
								    'page_id' => 1,
								    'user_role' => 2
								),
								array(
								    'user_id' => 5,
								    'page_id' => 1,
								    'user_role' => 3
								),
								array(
								    'user_id' => 6,
								    'page_id' => 1,
								    'user_role' => 3
								),
								array(
								    'user_id' => 1,
								    'page_id' => 2,
								    'user_role' => 1
								),
								array(
								    'user_id' => 2,
								    'page_id' => 2,
								    'user_role' => 1
								),
								array(
								    'user_id' => 3,
								    'page_id' => 2,
								    'user_role' => 0
								),
								array(
								    'user_id' => 4,
								    'page_id' => 2,
								    'user_role' => 2
								),
								array(
								    'user_id' => 5,
								    'page_id' => 2,
								    'user_role' => 3
								),
								array(
								    'user_id' => 6,
								    'page_id' => 2,
								    'user_role' => 3
								),
							);
		$this->db->insert_batch('user_pages', $user_pages);
		
		$package = array(
			array(
				'package_name' => 'Normal package',
				'package_detail' => 'For normal user',
				'package_image' => base_url().'images/package_icon_free.png',
				'package_max_companies' => 1,
				'package_max_pages' => 3,
				'package_max_users' => 10000,
				'package_price' => 0,
				'package_custom_badge' => 0,
				'package_duration' => 'unlimited'
			),
			array(
				'package_name' => 'Standard package',
				'package_detail' => 'For SMEs',
				'package_image' => base_url().'images/package_icon_standard.png',
				'package_max_companies' => 3,
				'package_max_pages' => 7,
				'package_max_users' => 50000,
				'package_price' => 49,
				'package_custom_badge' => 1,
				'package_duration' => '1month'
			),
			array(
				'package_name' => 'Enterprise package',
				'package_detail' => 'For Enterprise',
				'package_image' => base_url().'images/package_icon_enterprise.png',
				'package_max_companies' => 3,
				'package_max_pages' => 10,
				'package_max_users' => 100000,
				'package_price' => 99,
				'package_custom_badge' => 1,
				'package_duration' => '1month'
			),
		);
		$this->db->insert_batch('package', $package);
		
		$package_users = array(
			array(
				'package_id' => 1,
				'user_id' => 1,
				'package_expire' => '2012-06-19 19:12:20'
			),
			array(
				'package_id' => 1,
				'user_id' => 2,
				'package_expire' => '2012-07-19 20:12:20'
			),
			array(
				'package_id' => 1,
				'user_id' => 3,
				'package_expire' => '2012-08-19 23:59:59'
			),
			array(
				'package_id' => 1,
				'user_id' => 4,
				'package_expire' => '2012-09-19 19:12:20'
			),
			array(
				'package_id' => 1,
				'user_id' => 5,
				'package_expire' => '2012-01-19 23:59:59'
			),
			array(
				'package_id' => 2,
				'user_id' => 6,
				'package_expire' => '2012-02-19 23:59:59'
			)
		);
		$this->db->insert_batch('package_users', $package_users);
		
		$package_apps = array(
			array(
				'package_id' => 1,
				'app_id' => 1
			),
			array(
				'package_id' => 1,
				'app_id' => 2
			),
			array(
				'package_id' => 1,
				'app_id' => 3
			),
			array(
				'package_id' => 1,
				'app_id' => 4
			),
			array(
				'package_id' => 1,
				'app_id' => 5
			),
			array(
				'package_id' => 1,
				'app_id' => 6
			),
			array(
				'package_id' => 1,
				'app_id' => 7
			),
			array(
				'package_id' => 1,
				'app_id' => 8
			),
			array(
				'package_id' => 2,
				'app_id' => 1
			),
			array(
				'package_id' => 2,
				'app_id' => 2
			),
			array(
				'package_id' => 2,
				'app_id' => 3
			),
			array(
				'package_id' => 2,
				'app_id' => 4
			),
			array(
				'package_id' => 2,
				'app_id' => 5
			),
			array(
				'package_id' => 2,
				'app_id' => 6
			),
			array(
				'package_id' => 2,
				'app_id' => 7
			),
			array(
				'package_id' => 2,
				'app_id' => 8
			),
			array(
				'package_id' => 3,
				'app_id' => 1
			),
			array(
				'package_id' => 3,
				'app_id' => 2
			),
			array(
				'package_id' => 3,
				'app_id' => 3
			),
			array(
				'package_id' => 3,
				'app_id' => 4
			),
			array(
				'package_id' => 3,
				'app_id' => 5
			),
			array(
				'package_id' => 3,
				'app_id' => 6
			),
			array(
				'package_id' => 3,
				'app_id' => 7
			),
			array(
				'package_id' => 3,
				'app_id' => 8
			)
		);
		$this->db->insert_batch('package_apps', $package_apps);
		
		$order = array(
			array(
				'order_id' => 1,
				'order_date' => '2011-08-18 16:33:00',
				'order_status_id' => 2,
				'order_net_price' => 999,
				'user_id' => 1,
				'payment_method' => 'paypal',
				'billing_info' => 'a:7:{s:15:"user_first_name";s:8:"Weerapat";s:14:"user_last_name";s:6:"Poosri";s:10:"user_email";s:17:"tong@figabyte.com";s:18:"credit_card_number";s:0:"";s:24:"credit_card_expire_month";s:0:"";s:23:"credit_card_expire_year";s:0:"";s:15:"credit_card_csc";s:0:"";}'
			),
			array(
				'order_id' => 2,
				'order_date' => '2011-08-18 17:12:00',
				'order_status_id' => 1,
				'order_net_price' => 999,
				'user_id' => 1,
				'payment_method' => 'paypal',
				'billing_info' => 'a:12:{s:15:"user_first_name";s:8:"Weerapat";s:14:"user_last_name";s:6:"Poosri";s:10:"user_email";s:17:"tong@figabyte.com";s:18:"credit_card_number";s:0:"";s:24:"credit_card_expire_month";s:0:"";s:23:"credit_card_expire_year";s:0:"";s:15:"credit_card_csc";s:0:"";s:8:"payer_id";s:13:"GEYCL6WB86N62";s:6:"txn_id";s:17:"9CP746008S6070136";s:14:"payment_status";s:9:"Completed";s:14:"pending_reason";s:4:"None";s:11:"reason_code";s:4:"None";}'
			)
		);
		$this->db->insert_batch('order', $order);
		
		$order_items = array(
			array(
				'order_id' => 1,
				'item_id' => 2,
				'item_type_id' => 1,
				'item_name' => 'Enterprise package',
				'item_description' => 'For enterprise',
				'item_price' => 999,
				'item_unit' => 1,
				'item_discount' => 0
			),
			array(
				'order_id' => 2,
				'item_id' => 2,
				'item_type_id' => 1,
				'item_name' => 'Enterprise package',
				'item_description' => 'For enterprise',
				'item_price' => 999,
				'item_unit' => 1,
				'item_discount' => 0
			)
		);
		$this->db->insert_batch('order_items', $order_items);
		
		$page_user_data = array(
			array(
				'user_id' => 1,
				'page_id' => 1,
				'user_data' => json_encode(array(1 => 'L', 2 => 'red'))
			),
			array(
				'user_id' => 2,
				'page_id' => 1,
				'user_data' => json_encode(array(1 => 'S', 2 => 'blue'))
			),
			array(
				'user_id' => 3,
				'page_id' => 1,
				'user_data' => json_encode(array(1 => 'M', 2 => 'red'))
			),
			array(
				'user_id' => 4,
				'page_id' => 1,
				'user_data' => json_encode(array(1 => 'S', 2 => 'blue'))
			),
			array(
				'user_id' => 5,
				'page_id' => 1,
				'user_data' => json_encode(array(1 => 'L', 2 => 'blue'))
			),
			array(
				'user_id' => 6,
				'page_id' => 1,
				'user_data' => json_encode(array(1 => 'L', 2 => 'red'))
			)
		);
		$this->db->insert_batch('page_user_data', $page_user_data);
		
		echo "Test data added<br />";
	}
	
	function drop_mongo_collections(){
		foreach(func_get_args() as $collection){
			echo $this->mongo_db->drop_collection($collection) ? "Dropped {$collection}<br />" : "Cannot drop {$collection}<br />";
		}
	}
	
	function mongodb_reset(){
		
		$this->load->library('mongo_db');
		$this->load->library('audit_lib');
		$this->load->library('achievement_lib');
		$this->mongo_db->switch_db('achievement');
		$this->drop_mongo_collections('achievement_info','achievement_stat','achievement_user');
		$this->mongo_db->switch_db('audit');
		$this->drop_mongo_collections('actions','audits');
		$this->mongo_db->switch_db('stat');
		$this->drop_mongo_collections('apps','campaigns','pages');
		$this->mongo_db->switch_db('message');
		$this->drop_mongo_collections('notification');
		echo 'Dropped collections<br />';
		
		$platform_audit_actions = array(
			array(
				'app_id' => 0,
				'action_id' => 1,
				'description' => 'Install App',
				'stat_app' => FALSE,
				'stat_page' => TRUE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has installed {app:app_id}',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 2,
				'description' => 'Install App To Page',
				'stat_app' => FALSE,
				'stat_page' => TRUE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has installed {app:object} in {page:page_id}',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 3,
				'description' => 'Remove App',
				'stat_app' => FALSE,
				'stat_page' => TRUE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has removed {app:app_id}',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 4,
				'description' => 'Update Config',
				'stat_app' => FALSE,
				'stat_page' => TRUE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has updated {app:app_id} configuration',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 5,
				'description' => 'Install Page',
				'stat_app' => FALSE,
				'stat_page' => FALSE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has installed {page:page_id} in {company:company_id}',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 6,
				'description' => 'Create Company',
				'stat_app' => FALSE,
				'stat_page' => FALSE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has created company {company:company_id}',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 7,
				'description' => 'Buy Package',
				'stat_app' => FALSE,
				'stat_page' => FALSE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has bought package {package:object}',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 8,
				'description' => 'Buy Most Expensive Package',
				'stat_app' => FALSE,
				'stat_page' => FALSE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has bought the most expensive package {package:object}',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 101,
				'description' => 'User Register SocialHappen',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => TRUE,
				'format_string' => 'User {user:user_id} has registered SocialHappen',
				'score' => 50
			),
			array(
				'app_id' => 0,
				'action_id' => 102,
				'description' => 'User Register App',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => TRUE,
				'format_string' => 'User {user:user_id} has registered {app:app_id}',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 103,
				'description' => 'User Visit',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => TRUE,
				'format_string' => 'User {user:user_id} visited {app:app_id} in {page:page_id}',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 104,
				'description' => 'User Action',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => TRUE,
				'format_string' => 'User {user:user_id} has action', //@TODO What action?
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 105,
				'description' => 'User Join Campaign',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => TRUE,
				'format_string' => 'User {user:user_id} has joined {campaign:campaign_id} in {app:app_id}',
				'score' => 0
			),
			array(
				'app_id' => 0,
				'action_id' => 106,
				'description' => 'User Register Page',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => TRUE,
				'format_string' => 'User {user:user_id} registered {page:page_id}',
				'score' => 50
			),
			array(
				'app_id' => 0,
				'action_id' => 107,
				'description' => 'User Share Profile',
				'stat_app' => FALSE,
				'stat_page' => FALSE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} shared profile',
				'score' => 5
			),
			array(
				'app_id' => 0,
				'action_id' => 108,
				'description' => 'User Share For Star',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => TRUE,
				'format_string' => 'User {user:user_id} shared',
				'score' => 1
			),
			array(
				'app_id' => 0,
				'action_id' => 109,
				'description' => 'User Login',
				'stat_app' => FALSE,
				'stat_page' => FALSE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} logged in',
				'score' => 5
			),
			array(
				'app_id' => 0,
				'action_id' => 110,
				'description' => 'User Link to Twitter',
				'stat_app' => FALSE,
				'stat_page' => FALSE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} linked with Twitter account',
				'score' => 10
			),
			array(
				'app_id' => 0,
				'action_id' => 111,
				'description' => 'User Link to Facebook',
				'stat_app' => FALSE,
				'stat_page' => FALSE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} linked with Facebook account',
				'score' => 10
			),
			array(
				'app_id' => 0,
				'action_id' => 112,
				'description' => 'User Link to Foursquare',
				'stat_app' => FALSE,
				'stat_page' => FALSE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} linked with Foursquare account',
				'score' => 10
			),
			array(
				'app_id' => 0,
				'action_id' => 113,
				'description' => 'User Invite Friend',
				'stat_app' => FALSE,
				'stat_page' => FALSE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} invited a friend',
				'score' => 1
			),
		);
		
		$audit_actions = array(
			array(
				'app_id' => 5,
				'action_id' => 1001,
				'description' => 'View video',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has viewed video {string:object} in {app:app_id}',
				'score' => 1
			),
			array(
				'app_id' => 5,
				'action_id' => 1002,
				'description' => 'Share video',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has shared video {string:object} in {app:app_id}',
				'score' => 1
			),
			array(
				'app_id' => 6,
				'action_id' => 1001,
				'description' => 'View video',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has viewed video {string:object} in {app:app_id}',
				'score' => 1
			),
			array(
				'app_id' => 6,
				'action_id' => 1002,
				'description' => 'Share video',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has shared video {string:object} in {app:app_id}',
				'score' => 1
			),
			array(
				'app_id' => 7,
				'action_id' => 1001,
				'description' => 'Share feed',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has shared feed {string:object} in {app:app_id}',
				'score' => 1
			),
			array(
				'app_id' => 8,
				'action_id' => 1001,
				'description' => 'Share feed',
				'stat_app' => TRUE,
				'stat_page' => TRUE,
				'stat_campaign' => FALSE,
				'format_string' => 'User {user:user_id} has shared feed {string:object} in {app:app_id}',
				'score' => 1
			),
		);
		foreach(array_merge($platform_audit_actions,$audit_actions) as $audit_action){
			$this->audit_lib->add_audit_action($audit_action['app_id'], $audit_action['action_id'], 
				$audit_action['description'], $audit_action['stat_app'], $audit_action['stat_page'],
				$audit_action['stat_campaign'], $audit_action['format_string'], $audit_action['score']);
		}
		echo 'Added '.count(array_merge($platform_audit_actions,$audit_actions)).' audit actions<br />';
		
		$achievement_infos = array(
			// array(
				// 'app_id' => 5,
				// 'app_install_id' => NULL,
				// 'info' => array(
					// 'name' => 'First share',
					// 'description' => 'Shared video for the first time',
					// 'criteria_string' => array('Share = 1')
				// ),
				// 'criteria' => array(
					// 'action.1002.count' => 1
				// )
			// ),
			// array(
				// 'app_id' => 6,
				// 'app_install_id' => NULL,
				// 'info' => array(
					// 'name' => 'First share',
					// 'description' => 'Shared video for the first time',
					// 'criteria_string' => array('Share = 1')
				// ),
				// 'criteria' => array(
					// 'action.1002.count' => 1
				// )
			// ),
			// array(
				// 'app_id' => 7,
				// 'app_install_id' => NULL,
				// 'info' => array(
					// 'name' => 'First share',
					// 'description' => 'Shared feed for the first time',
					// 'criteria_string' => array('Share = 1')
				// ),
				// 'criteria' => array(
					// 'action.1001.count' => 1
				// )
			// ),
			// array(
				// 'app_id' => 8,
				// 'app_install_id' => NULL,
				// 'info' => array(
					// 'name' => 'First share',
					// 'description' => 'Shared feed for the first time',
					// 'criteria_string' => array('Share = 1')
				// ),
				// 'criteria' => array(
					// 'action.1001.count' => 1
				// )
			// ),
		);
		$platform_achievements = array(
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'I\'m using SocialHappen',
					'description' => 'Share profile the 1st time',
					'criteria_string' => array('Share Profile = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.107.count' => 1
				),
				'score' => 10
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Bragger',
					'description' => 'Share profile 10 times',
					'criteria_string' => array('Share Profile = 10'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.107.count' => 10
				),
				'score' => 50
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Hello World',
					'description' => 'Share the 1st time',
					'criteria_string' => array('Share = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/50-helloworld.png'
				),
				'criteria' => array(
					'action.108.count' => 1
				),
				'score' => 10
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Speaker',
					'description' => 'Share 10 times',
					'criteria_string' => array('Share = 10'),
					'badge_image' => BASE_URL.'assets/images/badges/50-speaker.png'
				),
				'criteria' => array(
					'action.108.count' => 10
				),
				'score' => 50
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Crazy Reporter',
					'description' => 'Share 50 times',
					'criteria_string' => array('Share = 50'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.108.count' => 50
				),
				'score' => 250
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'News Channel',
					'description' => 'Share 100 times',
					'criteria_string' => array('Share = 100'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.108.count' => 100
				),
				'score' => 500
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Share Troll',
					'description' => 'Share 250 times',
					'criteria_string' => array('Share = 250'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.108.count' => 250
				),
				'score' => 1000
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Just Arrived',
					'description' => 'Sign Up SocialHappen',
					'criteria_string' => array('Signup = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/50-arrived.png'
				),
				'criteria' => array(
					'action.101.count' => 1
				),
				'score' => 10
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Hello the Club',
					'description' => 'First time register to any page',
					'criteria_string' => array('Register Page = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/50-helloclub.png'
				),
				'criteria' => array(
					'action.106.count' => 1
				),
				'score' => 10
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Club Newbie',
					'description' => 'Joined 3 SocialHappen Pages',
					'criteria_string' => array('Register Page = 3'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.106.count' => 3
				),
				'score' => 50
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Club Master',
					'description' => 'Joined 10 SocialHappen Pages',
					'criteria_string' => array('Register Page = 10'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.106.count' => 10
				),
				'score' => 50
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Hello Old Friend',
					'description' => 'Login 5 times',
					'criteria_string' => array('Login Count = 5'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.109.count' => 5
				),
				'score' => 10
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Kudos For Coming Back',
					'description' => 'Login 10 times',
					'criteria_string' => array('Login Count = 10'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.109.count' => 10
				),
				'score' => 50
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Loyal Friend',
					'description' => 'Login 50 times',
					'criteria_string' => array('Login Count = 50'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.109.count' => 50
				),
				'score' => 100
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'The Tweet Bird',
					'description' => 'Connect to Twitter',
					'criteria_string' => array('Connect Twitter Count = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/50-tw.png'
				),
				'criteria' => array(
					'action.110.count' => 1
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'The Mark Zuckerburg Network Effect',
					'description' => 'Connect to Facebook',
					'criteria_string' => array('Connect Facebook Count = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/50-fb.png'
				),
				'criteria' => array(
					'action.111.count' => 1
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'I\'m a Mayor',
					'description' => 'Connect to Foursquare',
					'criteria_string' => array('Connect Foursquare Count = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.112.count' => 1
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'You\'re Not Alone',
					'description' => 'Invite your 1st friend',
					'criteria_string' => array('Invite = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.113.count' => 1
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'I Have A Team',
					'description' => 'Invite 10 friends',
					'criteria_string' => array('Invite = 10'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.113.count' => 10
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Social Animal',
					'description' => 'Invite 50 friends',
					'criteria_string' => array('Invite = 50'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.113.count' => 50
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Celebrity',
					'description' => 'Invite 100 Friends',
					'criteria_string' => array('Invite = 100'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.113.count' => 100
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'The Invitation Engine',
					'description' => 'Invite 500 Friends',
					'criteria_string' => array('Invite = 500'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.113.count' => 500
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'You Are Admin',
					'description' => 'Buy a package',
					'criteria_string' => array('Package Bought = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.7.count' => 1
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Nobel',
					'description' => 'Buy the most expensive package',
					'criteria_string' => array('Package Bought = Most Expensive'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.8.count' => 1
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Admin Newbie',
					'description' => 'Install SocialHappen to Facebook page',
					'criteria_string' => array('Install page = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.5.count' => 1
				),
				'score' => 0
			),
			array(
				'app_id' => 0,
				'app_install_id' => NULL,
				'info' => array(
					'name' => 'Page Admin Newbie', //Temp name
					'description' => 'Install apps to SocialHappen page',
					'criteria_string' => array('Install app to page = 1'),
					'badge_image' => BASE_URL.'assets/images/badges/default.png'
				),
				'criteria' => array(
					'action.2.count' => 1
				),
				'score' => 0
			),
		);
		foreach(array_merge($achievement_infos, $platform_achievements) as $achievement_info){
			$this->achievement_lib->add_achievement_info(
				$achievement_info['app_id'], $achievement_info['app_install_id'],
				$achievement_info['info'], $achievement_info['criteria']);
		}
		echo 'Added '.count(array_merge($achievement_infos, $platform_achievements)).' achievement infos<br />';
		echo 'MongoDB reset successfully';
	}
}
/* End of file sync.php */
/* Location: ./application/controllers/dev/sync.php */