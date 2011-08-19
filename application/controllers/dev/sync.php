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
			if (ENVIRONMENT == 'production')
			{
				exit('For development & testing only.');
			}
		}
		parent::__construct();
		$this->preload();
		$this->load->dbforge();
		$this->output->enable_profiler(TRUE);
	}

	function index(){
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
							    'app_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'app_facebook_api_key' => field_option('VARCHAR', 32, $default, $null, $autoinc, $unsigned)
							),
							'app_install_status' => array(
							    'app_install_status_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
							    'app_install_status_name' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
							    'app_install_status_description' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							/*'app_statistic' => array(
							    'app_install_id' => field_option('BIGINT', 20, $default, $null, $autoinc, $unsigned),
							    'job_time' => field_option('TIMESTAMP', $constraint, 'CURRENT_TIMESTAMP', $null, $autoinc, $unsigned),
							    'job_id' => field_option('BIGINT', 20, $default, $null, $autoinc, $unsigned),
							    'active_user' => field_option('BIGINT', 20, $default, $null, $autoinc, $unsigned),
							),*/
							'app_type' => array(
							    'app_type_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
							    'app_type_name' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
							    'app_type_description' => field_option('VARCHAR', 255, $default, TRUE, $autoinc, $unsigned),
							),
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
							    'campaign_status_id' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							    'campaign_active_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'campaign_all_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'campaign_start_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'campaign_end_timestamp' => field_option('TIMESTAMP', $constraint, $default , $null, $autoinc, $unsigned),
								'campaign_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'campaign_status' => array(
							    'campaign_status_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
							    'campaign_status_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
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
							    'app_install_status' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							    'app_install_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'page_id' => field_option('BIGINT', 20, 0, TRUE, $autoinc, TRUE),
							    'app_install_secret_key' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'order_in_dashboard' => field_option('INT', 5, 0, $null, $autoinc, TRUE),
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
								'page_status' => field_option('INT', 1, 1, $null, $autoinc, TRUE),
								'page_app_installed_id' => field_option('BIGINT', 20, 0, $null, $autoinc, TRUE),
								'page_installed' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'page_user_fields' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
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
								'user_gender' => field_option('INT', 1, 1, TRUE, $autoinc, TRUE),
								'user_birth_date' => field_option('DATE', $constraint, $default, TRUE, $autoinc, $unsigned),
								'user_about' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
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
							'user_gender' =>array(
								'user_gender_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
								'user_gender_name' => field_option('VARCHAR', 32, $default, $null, $autoinc, $unsigned),
							),
							'page_status' => array(
							    'page_status_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
							    'page_status_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
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
								'package_custom_badge' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned)
							),
							'package_users' => array(
								'package_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							),
							'package_apps' => array(
								'package_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'app_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							),
							'order' => array(
								'order_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
								'order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
								'order_status' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'order_net_price' => field_option('DOUBLE', $constraint, 0, $null, $autoinc, TRUE),
								'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'payment_method' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'billing_info' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned)
							),
							'order_items' => array(
								'order_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'item_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'item_type' => field_option('INT', 10, $default, $null, $autoinc, TRUE),
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
						'app_install_status' => array('app_install_status_id'),
						//'app_statistic' => array('app_install_id','job_time'),
						'app_type' => array('app_type_id'),
						'audit_action_type' => array('audit_action_id'),
						'campaign' => array('campaign_id'),
						'campaign_status' => array('campaign_status_id'),
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
						'user_gender' =>array('user_gender_id'),
						'page_status' => array('page_status_id'),
						'user_role' => array('user_role_id'),
						'user_pages' => array('user_id', 'page_id'),
						'package' => array('package_id'),
						'package_users' => array('user_id'),
						'package_apps' => array('package_id','app_id'),
						'order' => array('order_id'),
						'order_items' => array('order_id','item_id', 'item_type'),
						'page_user_data' => array('user_id','page_id')
					);
		$tables = array(
							'app',
							'app_install_status',
							//'app_statistic',
							'app_type',
							'audit_action_type',
							'campaign',
							'campaign_status',
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
							'user_gender',
							'page_status',
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
					    'app_name' => 'Feed',
					    'app_type_id' => 1,
					    'app_maintainance' => 0,
					    'app_show_in_list' => 1,
					    'app_description' => 'RSS Feed in facebook tab',
					    'app_secret_key' =>  '11111111111111111111111111111111',
					    'app_url' =>  'http://socialhappen.dyndns.org/feed?app_install_id={app_install_id}', 
					    'app_install_url' => 'http://socialhappen.dyndns.org/feed/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}',
					    'app_install_page_url' => '',
					    'app_config_url' =>  'http://socialhappen.dyndns.org/feed/sh/config?app_install_id={app_install_id}&user_facebook_id={user_facebook_id}&app_install_secret_key={app_install_secret_key}',
					    'app_support_page_tab' =>  1,
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => ''
					),
					array(
					    'app_id' => 2, 
					    'app_name' => 'Facebook Register', 
					    'app_type_id' => 2, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Campaign register using Facebook id',
					    'app_secret_key' =>  '22222222222222222222222222222222', 
					    'app_url' => 'http://socialhappen.dyndns.org/fbreg?app_install_id={app_install_id}', 
					    'app_install_url' => 'http://socialhappen.dyndns.org/fbreg/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}', 
					    'app_install_page_url' => '', 
					    'app_config_url' => 'http://socialhappen.dyndns.org/fbreg/sh/config?app_install_id={app_install_id}&user_facebook_id={user_facebook_id}&app_install_secret_key={app_install_secret_key}', 
					    'app_support_page_tab' => 0, 
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => ''
					),
					array(
					    'app_id' => 3, 
					    'app_name' => 'Share to get it', 
					    'app_type_id' => 3, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Share links by twitter / facebook to get file url', 
					    'app_secret_key' => '33333333333333333333333333333333', 
					    'app_url' => 'http://socialhappen.dyndns.org/sharetogetit?app_install_id={app_install_id}', 
					    'app_install_url' => 'http://socialhappen.dyndns.org/sharetogetit/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}', 
					    'app_install_page_url' => '', 
					    'app_config_url' => 'http://socialhappen.dyndns.org/sharetogetit/sh/config/{app_install_id}/{user_facebook_id}/{app_install_secret_key}', 
					    'app_support_page_tab' => 0, 
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => ''
					),
					array(
					    'app_id' => 4, 
					    'app_name' => 'Facebook CMS', 
					    'app_type_id' => 1, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Content Management System on Facebook', 
					    'app_secret_key' => '44444444444444444444444444444444', 
					    'app_url' => 'http://socialhappen.dyndns.org/fbcms/blog/{app_install_id}/', 
					    'app_install_url' => 'http://socialhappen.dyndns.org/fbcms/platform/install/{company_id}/{user_facebook_id}/', 
					    'app_install_page_url' => '', 
					    'app_config_url' => 'http://socialhappen.dyndns.org/fbcms/platform/config/{app_install_id}/{user_facebook_id}/{app_install_secret_key}/', 
					    'app_support_page_tab' => 1, 
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => ''
					),
					array(
					    'app_id' => 5, 
					    'app_name' => 'SocialMart', 
					    'app_type_id' => 1, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'SocialMart', 
					    'app_secret_key' => 'fb9792b2ccb40d5482f5b7cae5e55521', 
					    'app_url' => '', 
					    'app_install_url' => '', 
					    'app_install_page_url' => '', 
					    'app_config_url' => '',
					    'app_support_page_tab' => 1, 
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => ''
					),
					array(
					    'app_id' => 6, 
					    'app_name' => 'MockApp1', 
					    'app_type_id' => 2, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Mock Application', 
					    'app_secret_key' => 'cd14463efa98e6ee00fde6ccd51a9f6d', 
					    'app_url' => 'http://beta.figabyte.com/figtest/mockapp1/', 
					    'app_install_url' => 'http://beta.figabyte.com/figtest/mockapp1/port/install_unit/?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
					    'app_install_page_url' => 'http://beta.figabyte.com/figtest/mockapp1/port/install_to_page/?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
					    'app_config_url' => 'http://beta.figabyte.com/figtest/mockapp1/admin/?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
					    'app_support_page_tab' => 1, 
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => '125970200830191' 	
					),
					array(
					    'app_id' => 7, 
					    'app_name' => 'MockApp2', 
					    'app_type_id' => 2, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Mock Application', 
					    'app_secret_key' => '94738d825664ccb7f03d046af4ef595b', 
					    'app_url' => 'http://beta.figabyte.com/figtest/mockapp2/', 
					    'app_install_url' => 'http://beta.figabyte.com/figtest/mockapp2/port/install_unit/?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
					    'app_install_page_url' => 'http://beta.figabyte.com/figtest/mockapp2/port/install_to_page/?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
					    'app_config_url' => 'http://beta.figabyte.com/figtest/mockapp2/admin/?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
					    'app_support_page_tab' => 1, 
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => '240827319274038' 	
					),
					array(
					    'app_id' => 8, 
					    'app_name' => 'MockApp3', 
					    'app_type_id' => 2, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Mock Application', 
					    'app_secret_key' => 'de9364db919da2fbf3c4bcbb3ee7f391', 
					    'app_url' => 'http://beta.figabyte.com/figtest/mockapp3/', 
					    'app_install_url' => 'http://beta.figabyte.com/figtest/mockapp3/port/install_unit/?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
					    'app_install_page_url' => 'http://beta.figabyte.com/figtest/mockapp3/port/install_to_page/?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
					    'app_config_url' => 'http://beta.figabyte.com/figtest/mockapp3/admin/?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
					    'app_support_page_tab' => 1, 
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => '154511824626980' 	
					),
					array(
					    'app_id' => 9, 
					    'app_name' => 'MockApp4', 
					    'app_type_id' => 2, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Mock Application', 
					    'app_secret_key' => '8af45604f103f8c1bdca140141c19d4e', 
					    'app_url' => 'http://beta.figabyte.com/figtest/mockapp4/', 
					    'app_install_url' => 'http://beta.figabyte.com/figtest/mockapp4/port/install_unit/?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
					    'app_install_page_url' => 'http://beta.figabyte.com/figtest/mockapp4/port/install_to_page/?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
					    'app_config_url' => 'http://beta.figabyte.com/figtest/mockapp4/admin/?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
					    'app_support_page_tab' => 1, 
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => '238405896194000' 	
					),
					array(
					    'app_id' => 10, 
					    'app_name' => 'MockApp5', 
					    'app_type_id' => 2, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Mock Application', 
					    'app_secret_key' => '7b7d7cdd870d88cad5e3ec7743b00b1c', 
					    'app_url' => 'http://beta.figabyte.com/figtest/mockapp5/', 
					    'app_install_url' => 'http://beta.figabyte.com/figtest/mockapp5/port/install_unit/?company_id={company_id}&user_id={user_id}&page_id={page_id}', 
					    'app_install_page_url' => 'http://beta.figabyte.com/figtest/mockapp5/port/install_to_page/?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
					    'app_config_url' => 'http://beta.figabyte.com/figtest/mockapp5/admin/?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
					    'app_support_page_tab' => 1, 
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => '189132747815592' 	
					),
					array(
					    'app_id' => 11, 
					    'app_name' => '[beta]FGF', 
					    'app_type_id' => 2, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Friend get fans', 
					    'app_secret_key' => 'ad3d4f609ce1c21261f45d0a09effba4', 
					    'app_url' => 'http://apps.figabyte.com/freferral/sohapfgf', 
					    'app_install_url' => 'http://apps.figabyte.com/freferral/sohapfgf/platform.php?action=install&company_id={company_id}&user_id={user_id}&page_id={page_id}&force=1', 
					    'app_install_page_url' => 'http://apps.figabyte.com/freferral/sohapfgf/platform.php?action=install_to_page&app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1', 
					    'app_config_url' => 'http://apps.figabyte.com/freferral/sohapfgf/app_config.php?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
					    'app_support_page_tab' => 1, 
					    'app_image' =>  'http://socialhappen.dyndns.org/socialhappen/uploads/images/c3d08482305d185a572f967333b6a608_o.png',
						'app_facebook_api_key' => '125138914244914' 	
					)
				);
		$this->db->insert_batch('app', $app);
		
		$app_install_status = array(
									array(
									    'app_install_status_id' => 1,
									    'app_install_status_name' => 'active',
									    'app_install_status_description' => 'Active'
									),
									array(
									    'app_install_status_id' => 2,
									    'app_install_status_name' => 'inactive',
									    'app_install_status_description' => 'Inactive'
									),
									array(
									    'app_install_status_id' => 3,
									    'app_install_status_name' => 'not complete install',
									    'app_install_status_description' => 'Installed but not complete'
									),
									array(
									    'app_install_status_id' => 6,
									    'app_install_status_name' => 'not complete install',
									    'app_install_status_description' => 'Installed but not complete'
									)
								);
		$this->db->insert_batch('app_install_status', $app_install_status);
		
		$app_type = array(
							array(
							    'app_type_id' => 1,
							    'app_type_name' => 'Page Only',
							    'app_type_description' => 'Apps will be in page only',
							),
							array(
							    'app_type_id' => 2,
							    'app_type_name' => 'Support Page',
							    'app_type_description' => 'Apps can be installed into page'
							),
							array(
							    'app_type_id' => 3, 
							    'app_type_name' => 'Standalone', 
							    'app_type_description' => 'Apps cannot be installed into page'
							)
					);
		$this->db->insert_batch('app_type', $app_type);
				
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
								'campaign_image' => 'http://socialhappen.dyndns.org/socialhappen/uploads/images/e9cd374dff834f3bfbeb24d4682c6417_o.png',
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
								'campaign_image' => 'http://socialhappen.dyndns.org/socialhappen/uploads/images/e9cd374dff834f3bfbeb24d4682c6417_o.png',
							)
						);
		$this->db->insert_batch('campaign', $campaign);
		
		$campaign_status = array(
								array(
								    'campaign_status_id' => 1,
								    'campaign_status_name' => 'Inactive'
								),
								array(
								    'campaign_status_id' => 2,
								    'campaign_status_name' => 'Active'
								),
								array(
								    'campaign_status_id' => 3,
								    'campaign_status_name' => 'Expired'
								)
							);
		$this->db->insert_batch('campaign_status', $campaign_status);
		
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
							    'company_image' => 'http://socialhappen.dyndns.org/socialhappen/uploads/images/32b299d9fb8a6e61784646ac80631153_o.png'
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
								    'app_install_status' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => '457f81902f7b768c398543e473c47465'
								),
								array(
								  	'app_install_id' => 2, 
								    'company_id' => 1, 
								    'app_id' => 2, 
								    'app_install_status' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => 'b4504b54bb0c27a22fedba10cca4eb55'
								),
								array(
								    'app_install_id' => 3, 
								    'company_id' => 1, 
								    'app_id' => 3, 
								    'app_install_status' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => '1dd5a598414f201bc521348927c265c3'
								),
								array(
								  	'app_install_id' => 4, 
								    'company_id' => 1, 
								    'app_id' => 4, 
								    'app_install_status' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => '19323810aedbbc8384b383fa21904626'
								)
							);
		$this->db->insert_batch('installed_apps', $installed_apps);
		
		$page = array(
					array(
						    'page_id' => 1, 
						    'facebook_page_id' => '116586141725712', 
						    'company_id' => 1, 
						    'page_name' => 'Test name', 
						    'page_detail' => 'detail', 
						    'page_all_member' => 22, 
						    'page_new_member' => 222, 
						    'page_image' => 'http://socialhappen.dyndns.org/socialhappen/uploads/images/1e0e1797879fb03f648d6751f43a2697_o.png',
							'page_user_fields' => 'size,color'
					),
					array(
						'page_id' => 2, 
						'facebook_page_id' => '135287989899131', 
						'company_id' => 1, 
						'page_name' => 'SH Beta', 
						'page_detail' => 'detail', 
						'page_all_member' => 10, 
						'page_new_member' => 100, 
						'page_image' => 'http://socialhappen.dyndns.org/socialhappen/uploads/images/1e0e1797879fb03f648d6751f43a2697_o.png',
						'page_user_fields' => 'size,color'
					),
				);
		$this->db->insert_batch('page', $page);
		
		$user = array(
					array(
					    'user_id' => 1, 
					    'user_first_name' => 'test',
					    'user_last_name' => 'test',
					    'user_email' => 'tes@test.com',
					    'user_image' => 'http://socialhappen.dyndns.org/socialhappen/uploads/images/bd6d2267939eeec1a64b1b46bbf90e77_o.png',					    
					    'user_facebook_id' => 713558190, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
						),
					array(
					    'user_id' => 2, 
					    'user_first_name' => 'test',
					    'user_last_name' => 'test',
					    'user_email' => 'tes@test.com',
					    'user_image' => 'http://socialhappen.dyndns.org/socialhappen/uploads/images/bd6d2267939eeec1a64b1b46bbf90e77_o.png',	
					    'user_facebook_id' => 637741627, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 3, 
					    'user_first_name' => 'test',
					    'user_last_name' => 'test',
					    'user_email' => 'tes@test.com',
					    'user_image' => 'http://socialhappen.dyndns.org/socialhappen/uploads/images/bd6d2267939eeec1a64b1b46bbf90e77_o.png',		
					    'user_facebook_id' => 631885465, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 4, 
					    'user_first_name' => 'test',
					    'user_last_name' => 'test',
					    'user_email' => 'tes@test.com',
					    'user_image' => 'http://socialhappen.dyndns.org/socialhappen/uploads/images/bd6d2267939eeec1a64b1b46bbf90e77_o.png',		
					    'user_facebook_id' => 755758746, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 5, 
					    'user_first_name' => 'test',
					    'user_last_name' => 'test',
					    'user_email' => 'tes@test.com',
					    'user_image' => 'http://socialhappen.dyndns.org/socialhappen/uploads/images/bd6d2267939eeec1a64b1b46bbf90e77_o.png',		
					    'user_facebook_id' => 508840994, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 6, 
					    'user_first_name' => 'Weerapat',
					    'user_last_name' => 'Poosri',
					    'user_email' => 'tong@figabyte.com',
					    'user_image' => 'http://graph.facebook.com/688700832/picture',		
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
								    'user_role' => 0
								),
								array(
								    'user_id' => 4,
								    'company_id' => 1,
								    'user_role' => 2
								),
								array(
								    'user_id' => 5,
								    'company_id' => 1,
								    'user_role' => 3
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
		
		$user_gender = array(
			array(
				'user_gender_id' => 1,
				'user_gender_name' => "Not sure"
			),
			array(
				'user_gender_id' => 2,
				'user_gender_name' => "Female"
			),
			array(
				'user_gender_id' => 3,
				'user_gender_name' => "Male"
			)
		);
		$this->db->insert_batch('user_gender', $user_gender);
		
		$page_status = array(
								array(
								    'page_status_id' => 1,
								    'page_status_name' => 'Not installed'
								),
								array(
								    'page_status_id' => 2,
								    'page_status_name' => 'Installed'
								)
							);
		$this->db->insert_batch('page_status', $page_status);
		
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
								)
							);
		$this->db->insert_batch('user_pages', $user_pages);
		
		$package = array(
			array(
				'package_name' => 'Normal package',
				'package_detail' => 'For normal user',
				'package_image' => NULL,
				'package_max_companies' => 1,
				'package_max_pages' => 3,
				'package_max_users' => 10000,
				'package_price' => 0,
				'package_custom_badge' => 1
			),
			array(
				'package_name' => 'Enterprise package',
				'package_detail' => 'For enterprise',
				'package_image' => NULL,
				'package_max_companies' => 3,
				'package_max_pages' => 10,
				'package_max_users' => 100000,
				'package_price' => 999,
				'package_custom_badge' => 1
			)
		);
		$this->db->insert_batch('package', $package);
		
		$package_users = array(
			array(
				'package_id' => 1,
				'user_id' => 1
			),
			array(
				'package_id' => 1,
				'user_id' => 2
			),
			array(
				'package_id' => 1,
				'user_id' => 3
			)			,
			array(
				'package_id' => 1,
				'user_id' => 4
			)			,
			array(
				'package_id' => 1,
				'user_id' => 5
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
			)
		);
		$this->db->insert_batch('package_apps', $package_apps);
		
		$order = array(
			array(
				'order_id' => 1,
				'order_date' => '2011-08-18 16:33:00',
				'order_status' => 2,
				'order_net_price' => 999,
				'user_id' => 1,
				'payment_method' => 'paypal',
				'billing_info' => 'Name: Weerapat Poosri	
									 Address: 45/6 Surawong	
									  Bangkok 10010
									 Phone: +66.0814558839
									 Email: tong@figabyte.com'
			),
			array(
				'order_id' => 2,
				'order_date' => '2011-08-18 17:12:00',
				'order_status' => 1,
				'order_net_price' => 999,
				'user_id' => 1,
				'payment_method' => 'paypal',
				'billing_info' => 'Name: Weerapat Poosri	
									 Address: 45/6 Surawong	
									  Bangkok 10010
									 Phone: +66.0814558839
									 Email: tong@figabyte.com'
			)
		);
		$this->db->insert_batch('order', $order);
		
		$order_items = array(
			array(
				'order_id' => 1,
				'item_id' => 2,
				'item_type' => 1,
				'item_name' => 'Enterprise package',
				'item_description' => 'For enterprise',
				'item_price' => 999,
				'item_unit' => 1,
				'item_discount' => 0
			),
			array(
				'order_id' => 2,
				'item_id' => 2,
				'item_type' => 1,
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
				'user_data' => json_encode(array('size' => 'L', 'color' => 'red'))
			),
			array(
				'user_id' => 2,
				'page_id' => 1,
				'user_data' => json_encode(array('size' => 'S', 'color' => 'blue'))
			),
			array(
				'user_id' => 3,
				'page_id' => 1,
				'user_data' => json_encode(array('size' => 'M', 'color' => 'red'))
			),
			array(
				'user_id' => 4,
				'page_id' => 1,
				'user_data' => json_encode(array('size' => 'S', 'color' => 'blue'))
			),
			array(
				'user_id' => 5,
				'page_id' => 1,
				'user_data' => json_encode(array('size' => 'L', 'color' => 'blue'))
			),
			array(
				'user_id' => 6,
				'page_id' => 1,
				'user_data' => json_encode(array('size' => 'L', 'color' => 'red'))
			)
		);
		$this->db->insert_batch('page_user_data', $page_user_data);
		
		echo "Test data added<br />";
	}
}
/* End of file sync.php */
/* Location: ./application/controllers/dev/sync.php */